# 03 — Backend Architecture Audit

## 1. Inventaris Komponen Backend

- **VERIFIED:** Seluruh lapisan backend terdiri dari:
  - **Enums (`app/Enums/`):** 4 file (`JenisKelamin`, `KategoriKeluhan`, `PeranPengguna`, `StatusPesanan`).
  - **Models (`app/Models/`):** 7 file (`Pengguna`, `Kota`, `Kendaraan`, `Jadwal`, `Pesanan`, `KonfirmasiPembayaran`, `Keluhan`).
  - **Middleware (`app/Http/Middleware/`):** 1 file kustom (`PastikanPunyaPeran`).
  - **Form Requests (`app/Http/Requests/Auth/`):** 2 file (`DaftarRequest`, `MasukRequest`).
  - **Controllers (`app/Http/Controllers/`):** 18 file di 5 namespace (`Auth`: 3, `Publik`: 2, `Member`: 4, `Operator`: 2, `Admin`: 7) + base `Controller`.
  - **Providers (`app/Providers/`):** 1 file (`AppServiceProvider`, kosong).
  - **Services / Actions / Policies / Events / Listeners / Jobs / API Controllers:** **NOT IMPLEMENTED** (tidak ada `routes/api.php`, tidak ada `app/Services`, `app/Actions`, `app/Policies`, `app/Events`, `app/Listeners`, `app/Jobs`).

---

## 2. Evaluasi Arsitektur Backend

### 2.1 Separation of Responsibilities
- **VERIFIED:** Pemisahan tanggung jawab masih lemah (**Fat Controllers**). Seluruh logika bisnis—mulai dari perhitungan sisa kursi, generasi kode pesanan, pemrosesan upload bukti transfer, transaksi database, validasi input non-auth, hingga query statistik—berada langsung di dalam method controller.
- **VERIFIED:** Tidak terdapat Service Layer (`KetersediaanKursiService`), Action classes, maupun Policy classes untuk otorisasi resource.

### 2.2 Code Duplication
- **VERIFIED:** Terdapat beberapa duplikasi logika lintas controller:
  1. **Perhitungan kursi terpakai / sisa kursi** diulang dengan cara berbeda di `Publik\BerandaController::index()` (`withCount`), `Publik\CariJadwalController::index()`, `Member\PesananController::create()` & `store()`, dan `Operator\PesananLoketController::index()` & `store()`.
  2. **Pembangunan daftar nomor kursi tersedia & locking transaksi** diduplikasi antara `Member\PesananController::store()` dan `Operator\PesananLoketController::store()`.
  3. **Aksi verifikasi/tolak pembayaran** diduplikasi antara `Operator\VerifikasiPembayaranController` (`verifikasi`, `tolak`) dan `Admin\PesananController` (`verifikasi`, `tolak`) dengan logika minimal (keduanya sama-sama mengabaikan audit trail dan state validation).

### 2.3 Reusability & Helper
- **VERIFIED [Dikoreksi di Phase 2]:** Tidak ada helper terpusat (misal untuk format rupiah, tanggal, kalkulasi sisa kursi, atau generator kode pesanan `GTX-XXXXXX`); seluruh model saat ini hanya mendefinisikan relasi dan casting (tanpa query scope maupun helper method bisnis).

### 2.4 Validation Consistency
- **VERIFIED:** Validasi terpecah dan tidak konsisten:
  - Hanya Auth (`DaftarRequest`, `MasukRequest`) yang menggunakan `FormRequest`.
  - Seluruh controller fitur (`Member\*`, `Operator\*`, `Admin\*`) menggunakan inline `$request->validate(...)`.
  - Beberapa validasi kritis **hilang atau bertentangan dengan skema DB** (lihat temuan rinci di bawah).

### 2.5 Error Handling
- **VERIFIED:**
  - `Member\PesananController::store()` (L108-110) dan `Operator\PesananLoketController::store()` (L89-91) membungkus `DB::transaction` dalam `try ... catch (\Throwable $e)` yang menangkap seluruh exception (termasuk error SQL constraint atau logic bug) dan mengembalikannya sebagai flash error generik `'Terjadi kesalahan sistem. Silakan coba lagi.'` tanpa logging (`Log::error`), sehingga menyulitkan diagnosa kegagalan produksi.
  - `Member\TiketController::show()` (L22-30) menggunakan `->first()` alih-alih `->firstOrFail()`, sehingga kode tiket yang tidak ada tidak melempar 404.

### 2.6 Query Efficiency (N+1 & Lazy Loading)
- **VERIFIED [Dikoreksi di Phase 2]:** `AppServiceProvider::boot()` tidak memanggil `Model::preventLazyLoading()`. Controller utama sudah menggunakan eager loading `with(...)`, namun kalkulasi kursi terpakai dan filter jadwal aktif tersebar di masing-masing controller/Blade (misal `CariJadwalController::index()` lupa memfilter `tanggal_berangkat >= today()` saat pencarian tanpa tanggal, berbeda dengan `BerandaController::index()`).

---

## 3. Daftar Temuan Kritis & Masalah Backend

### Issue 3.1 — Celah Bug `unique_jadwal_nomor_kursi` & Error Handling Transaksi Pemesanan
- **VERIFIED**
- **File path:** `app/Http/Controllers/Member/PesananController.php` L70-108, `app/Http/Controllers/Operator/PesananLoketController.php` L55-89
- **Relevant class/method/component:** `Member\PesananController::store()`, `Operator\PesananLoketController::store()`
- **Description:** Kedua controller mengecek kursi terisi dengan mengecualikan `StatusPesanan::Dibatalkan` di dalam `DB::transaction` (`lockForUpdate()`). Namun tabel `pesanan` memiliki indeks unik mutlak `(jadwal_id, nomor_kursi)` tanpa memperhitungkan status. Ketika pengguna mencoba memesan kursi yang sebelumnya berstatus `Dibatalkan`, pengecekan PHP menganggap kursi tersedia, lalu `Pesanan::create()` gagal karena MySQL `Duplicate entry`, ditangkap oleh `catch (\Throwable)` dan hanya menampilkan pesan generik tanpa informasi dan tanpa log.
- **Impact:** Kursi dari pesanan yang dibatalkan atau pembayarannya ditolak tidak dapat dipesan ulang selamanya (**kehilangan kapasitas/pendapatan**).
- **Recommended solution:** Perbaiki mekanisme uniqueness kursi pada level desain database & transaksi (lihat `05-database-audit.md`) dan tangani `DomainException` terpisah dari system exception dengan pencatatan `Log::error`.
- **Priority:** Critical

### Issue 3.2 — `Member\TiketController` Mengambil Pesanan Belum Lunas & Gagal 404
- **VERIFIED**
- **File path:** `app/Http/Controllers/Member/TiketController.php` L11-31
- **Relevant class/method/component:** `TiketController::index()`, `TiketController::show()`
- **Description:** `index()` mengambil semua pesanan milik pengguna (tidak memfilter `StatusPesanan::Lunas`), dan `show()` memakai `->first()` alih-alih `->firstOrFail()` serta tidak memvalidasi bahwa pesanan tersebut sudah berstatus `Lunas` sebelum menampilkan e-tiket.
- **Impact:** Member dapat mengakses halaman e-tiket untuk pesanan `Belum Bayar` maupun `Dibatalkan`, dan URL kode tiket sembarang tidak menghasilkan HTTP 404 (berpotensi crash saat view memakai `$pesanan`).
- **Recommended solution:** Filter `index()` untuk `status = Lunas` (atau bedakan badge tiket aktif vs belum terbit), dan di `show()` wajibkan `status = Lunas` dengan `firstOrFail()`.
- **Priority:** Critical

### Issue 3.3 — `Member\KeluhanController::store()` Tidak Memvalidasi Enum, Tidak Menyimpan `pesanan_id`, dan Melebihi Panjang Kolom DB
- **VERIFIED**
- **File path:** `app/Http/Controllers/Member/KeluhanController.php` L34-43
- **Relevant class/method/component:** `Member\KeluhanController::store()`
- **Description:**
  1. Validasi `'kategori' => ['required']` tidak memakai `Rule::enum(KategoriKeluhan::class)`.
  2. Validasi `'deskripsi' => ['required', 'string', 'max:1000']` mengizinkan hingga 1000 karakter, sedangkan kolom `keluhan.deskripsi` di migrasi `2026_03_14_000006_create_keluhan_table.php` L19 bertipe `VARCHAR(200)`.
  3. Field opsional `pesanan_id` tidak divalidasi (termasuk kepemilikan pesanan oleh `Auth::id()`) dan tidak ikut disimpan di `Keluhan::create(...)`.
- **Impact:** Input deskripsi >200 karakter menyebabkan SQL truncation error/crash; nilai kategori tidak valid memicu DB enum error; keluhan tidak pernah terhubung ke transaksi pesanan terkait.
- **Recommended solution:** Gunakan `Rule::enum(KategoriKeluhan::class)`, samakan batas maksimal karakter dengan skema DB (atau ubah kolom menjadi `text` pada migrasi perbaikan), dan validasi `pesanan_id` dimiliki oleh user yang login.
- **Priority:** High

### Issue 3.4 — Verifikasi Pembayaran Tanpa Transaksi, Tanpa State Check, & Tanpa Audit Trail
- **VERIFIED**
- **File path:** `app/Http/Controllers/Operator/VerifikasiPembayaranController.php` L36-52, `app/Http/Controllers/Admin/PesananController.php` L31-47
- **Relevant class/method/component:** `VerifikasiPembayaranController::verifikasi()`, `::tolak()`, `Admin\PesananController::verifikasi()`, `::tolak()`
- **Description:**
  1. Tidak ada pengecekan apakah `$pesanan->status === StatusPesanan::DalamProses` dan apakah `konfirmasiPembayaran` ada sebelum mengubah status ke `Lunas` atau `Dibatalkan`.
  2. Tidak ada `DB::transaction()`. Jika update `Pesanan` berhasil tetapi update `KonfirmasiPembayaran` gagal (atau sebaliknya), data menjadi tidak konsisten.
  3. Kolom audit pada `konfirmasi_pembayaran` (`diverifikasi_oleh`, `diverifikasi_pada`, `alasan_penolakan`) **tidak pernah diisi** oleh Operator maupun Admin.
  4. `tolak()` langsung mengubah status `Pesanan` menjadi `Dibatalkan` tanpa alasan penolakan yang divalidasi (`required|string`) dan tanpa mekanisme member untuk mengunggah ulang bukti bayar.
- **Impact:** Pesanan `Belum Bayar` tanpa bukti transfer bisa langsung disetujui menjadi `Lunas`; pesanan `Lunas` bisa diubah mundur ke `Dibatalkan`; jejak audit siapa yang memverifikasi pembayaran hilang sepenuhnya.
- **Recommended solution:** Ekstrak ke satu Service/Action transaksi dengan guard state machine (`DalamProses` -> `Lunas`/`Dibatalkan`), isi `diverifikasi_oleh`, `diverifikasi_pada`, dan wajibkan `alasan_penolakan` saat penolakan.
- **Priority:** Critical

### Issue 3.5 — `Member\PembayaranController::konfirmasiStore()` Rawan Duplicate Resi, Abaikan `batas_bayar`, & Simpan Bukti Secara Publik
- **VERIFIED**
- **File path:** `app/Http/Controllers/Member/PembayaranController.php` L27-64
- **Relevant class/method/component:** `Member\PembayaranController::konfirmasiStore()`
- **Description:**
  1. Validasi `'nomor_resi' => ['nullable', 'string', 'max:100']` tidak memiliki aturan `unique:konfirmasi_pembayaran,nomor_resi` (padahal kolom DB `nomor_resi` berindeks `unique`).
  2. Walaupun query memfilter `->where('status', StatusPesanan::BelumBayar)->firstOrFail()`, controller tidak mengecek apakah pesanan sudah melewati `$pesanan->batas_bayar` (pesanan kedaluwarsa tetap bisa dibayar).
  3. Tidak menggunakan `DB::transaction()` antara penyimpanan file bukti transfer ke disk, `KonfirmasiPembayaran::updateOrCreate()`, dan update status `Pesanan` menjadi `DalamProses`.
  4. Menyimpan file bukti transfer via `$request->file('bukti_transfer')->store('bukti_transfer', 'public')`. Karena `public/storage` aktif, file sensitif berisi data rekening dan transaksi member dapat diakses siapa saja tanpa autentikasi jika URL ditebak.
- **Impact:** Duplikasi nomor resi menghasilkan unhandled `QueryException` 500; pesanan kedaluwarsa tetap bisa dibayar; bukti transfer berpotensi bocor secara publik.
- **Recommended solution:** Tambahkan validasi `unique` pada `nomor_resi`, tambahkan guard `batas_bayar`, bungkus dalam `DB::transaction`, dan pindahkan penyimpanan bukti ke disk privat (`local`) yang disajikan melalui controller terotorisasi.
- **Priority:** High

### Issue 3.6 — `Operator\PesananLoketController::store()` Hardcode Kapasitas & Tidak Dukung Penumpang Non-Akun
- **VERIFIED**
- **File path:** `app/Http/Controllers/Operator/PesananLoketController.php` L47-89
- **Relevant class/method/component:** `Operator\PesananLoketController::store()`
- **Description:**
  1. Validasi `'nomor_kursi' => ['required', 'integer', 'min:1', 'max:5']` memakai angka `5` hardcode, mengabaikan `$jadwal->kendaraan->kapasitas_kursi` (misal Elf/HiAce Commuter yang berkapasitas >5 kursi).
  2. Membutuhkan `'pengguna_id' => ['required', 'exists:pengguna,id']` tanpa memverifikasi `peran === Member`, dan belum memiliki skema untuk mencatat nama/telepon penumpang walk-in tanpa akun (bertentangan dengan label UI di `operator/loket/index.blade.php`).
  3. Mengeset `status => StatusPesanan::Lunas` (langsung lunas tunai di loket), namun tetap mengisi `batas_bayar => now()->addHours(1)` yang tidak relevan untuk pesanan berstatus lunas. Selain itu view `operator/loket/index.blade.php` bahkan tidak mengirim `POST` ke endpoint ini (hanya membuka modal cetak palsu di Alpine.js).
- **Impact:** Kursi nomor >5 tidak dapat dipesan di loket; walk-in penumpang tanpa akun tidak bisa dilayani oleh backend.
- **Recommended solution:** Gunakan kapasitas dinamis dari `$jadwal->kendaraan->kapasitas_kursi` dan desain dukungan penumpang walk-in (kolom guest di `pesanan` atau auto-register member) serta opsi langsung lunas tunai untuk loket.
- **Priority:** High

### Issue 3.7 — Penghapusan Master Data Admin (`Kota`, `Kendaraan`, `Jadwal`) Berpotensi Menghapus Histori Transaksi
- **VERIFIED**
- **File path:** `app/Http/Controllers/Admin/JadwalController.php` L86-93, `Admin/KendaraanController.php` L66-73, `Admin/KotaController.php` L60-67
- **Relevant class/method/component:** Method `destroy()` pada ketiga controller Admin
- **Description:** Ketiga controller memanggil `->delete()` secara langsung tanpa mengecek apakah terdapat relasi `jadwal` atau `pesanan` aktif/historis. Mengingat migrasi memakai `cascadeOnDelete()`, menghapus kota atau kendaraan akan menghapus jadwal beserta seluruh data `pesanan` dan `konfirmasi_pembayaran` terkait secara permanen.
- **Impact:** Hilangnya data keuangan dan riwayat pesanan pelanggan akibat penghapusan data master.
- **Recommended solution:** Tambahkan guard pencegahan penghapusan jika masih berelasi dengan `jadwal`/`pesanan`, atau terapkan soft delete / flag status nonaktif.
- **Priority:** Critical

### Issue 3.8 — Bug Variabel & Kebocoran Keamanan Autentikasi
- **VERIFIED:**
  1. **Typo variabel di `Admin\DasborController::index()` (L22 & L39):** Menggunakan `$menanggguVerifikasi` (tiga huruf `g`), berisiko menyebabkan bug jika diperbaiki sepihak tanpa menyinkronkan `admin/dasbor.blade.php` L10. *(Priority: Low)*
  2. **Tidak ada Rate Limiting Login (`app/Http/Controllers/Auth/MasukController.php` L29-51):** Menggunakan `Auth::attempt()` langsung tanpa `RateLimiter` / throttle percobaan gagal. *(Priority: High)*
  3. **Input `Admin\JadwalController` tidak mengecek tanggal masa lalu [Dikoreksi di Phase 2]:** `store()` dan `update()` (L37, L73) **sudah** memvalidasi `'different:kota_asal_id'`, namun `store()` (L40) belum memvalidasi `'after_or_equal:today'` untuk mencegah pembuatan jadwal di masa lalu. *(Priority: Medium)*
  4. **Validasi unik `KendaraanController::update()` / `KotaController::update()`:** Telah menggunakan `Rule::unique(...)->ignore(...)` dengan benar (**VERIFIED** — implementasi sudah baik pada aspek ini).
