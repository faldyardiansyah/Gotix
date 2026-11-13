# 10 — Technical Debt and Recommendations

Dokumen ini mengonsolidasikan seluruh temuan utang teknis dan cacat dari audit kode GOTIX, lengkap dengan klasifikasi prioritas serta pemisahan tegas antara **isu wajib diselesaikan sebelum Redesign UI/UX (Blocker)** dan **perbaikan yang dapat ditunda/dikerjakan bersamaan setelah fondasi aman**.

---

## 1. Katalog Isu Prioritas Critical & High (Blocker Pra-Redesign)

### TD-01 — Constraint Unik `unique_jadwal_nomor_kursi` Memblokir Kursi Bekas Pesanan Batal
- **Status:** `VERIFIED` | **Priority:** `Critical`
- **File path:** `database/migrations/2026_03_14_000004_create_pesanan_table.php` (L25), `app/Http/Controllers/Member/PesananController.php` (L75-102), `app/Http/Controllers/Operator/PesananLoketController.php` (L57-83)
- **Relevant class/method/component:** Indeks `unique_jadwal_nomor_kursi`, `Member\PesananController::store()`, `Operator\PesananLoketController::store()`
- **Description:** Indeks unik `(jadwal_id, nomor_kursi)` di MySQL mencakup semua status termasuk `Dibatalkan`, padahal logika locking di controller mengecualikan `Dibatalkan`. Akibatnya, memesan kursi yang pernah dibatalkan selalu gagal karena duplicate key DB dan memunculkan pesan error generik.
- **Impact:** Kursi yang sudah dibatalkan/ditolak terkunci mati secara permanen untuk jadwal tersebut.
- **Recommended solution:** Buat migrasi perbaikan untuk mengganti unique index menjadi conditional uniqueness (kolom virtual MySQL `kursi_aktif` NULL saat `status = 'Dibatalkan'` + `UNIQUE(jadwal_id, kursi_aktif)`), lalu perbaiki exception handling agar error DB tercatat ke log.

### TD-02 — Fitur E-Tiket Member 100% Mocked & Tidak Memfilter Status Lunas
- **Status:** `VERIFIED` | **Priority:** `Critical`
- **File path:** `app/Http/Controllers/Member/TiketController.php` (L11-31), `resources/views/member/tiket/index.blade.php`, `resources/views/member/tiket/show.blade.php` (L26-164)
- **Relevant class/method/component:** `TiketController::index()`, `TiketController::show()`, tampilan e-tiket
- **Description:** `index()` mengambil semua pesanan (termasuk `Belum Bayar` & `Dibatalkan`), `show()` memakai `first()` tanpa filter `Lunas`, dan `show.blade.php` mengabaikan `$pesanan` dengan menampilkan data statis Jakarta–Bandung serta SVG QR palsu.
- **Impact:** Member menerima tiket fiktif dan pesanan yang belum lunas muncul di daftar tiket aktif.
- **Recommended solution:** Filter daftar & detail tiket ke `StatusPesanan::Lunas`, gunakan `firstOrFail()`, dan ikat seluruh elemen kartu/e-tiket ke `$pesanan` asli saat redesain.

### TD-03 — Fitur Keluhan Member Terputus & Mocked di Frontend
- **Status:** `VERIFIED` | **Priority:** `Critical`
- **File path:** `app/Http/Controllers/Member/KeluhanController.php` (L32-47), `resources/views/member/keluhan/index.blade.php` (L28-57), `resources/views/member/keluhan/create.blade.php` (L39-100), `database/migrations/2026_03_14_000006_create_keluhan_table.php` (L19)
- **Relevant class/method/component:** `Member\KeluhanController::store()`, view `member/keluhan/{index,create}`
- **Description:** `index.blade.php` menimpa data `$daftarKeluhan` dengan array dummy; `create.blade.php` memakai `action="#"` + `alert()`; opsi kategori mengandung `'Armada'` (tidak ada di enum DB); `store()` mengizinkan `max:1000` untuk kolom `VARCHAR(200)` dan mengabaikan `pesanan_id`.
- **Impact:** Keluhan member tidak pernah tersimpan ke database dan akan crash jika dikirim >200 karakter.
- **Recommended solution:** Hapus array dummy, arahkan form ke `route('member.keluhan.store')`, samakan opsi enum `KategoriKeluhan::cases()`, validasi `pesanan_id` milik user, dan perlebar kolom `deskripsi` ke `TEXT` atau samakan batas validasi.

### TD-04 — Fitur Pemesanan Loket Operator Mocked & Validasi Kapasitas Hardcode
- **Status:** `VERIFIED` | **Priority:** `Critical`
- **File path:** `app/Http/Controllers/Operator/PesananLoketController.php` (L45-95), `resources/views/operator/loket/index.blade.php`, `resources/views/operator/loket/create.blade.php` (L1)
- **Relevant class/method/component:** `PesananLoketController::store()`, view `operator/loket/{index,create}`
- **Description:** Frontend `loket/index.blade.php` menggunakan daftar rute statis dan `@submit.prevent="submitLoket()"` yang hanya membuka modal cetak palsu tanpa mengirim request POST. Di backend, `store()` melakukan hardcode `max:5` kursi dan mewajibkan `pengguna_id` terdaftar sehingga walk-in tanpa akun tidak didukung. `loket/create.blade.php` masih placeholder.
- **Impact:** Operator loket sama sekali tidak dapat membuat pesanan nyata yang memotong stok kursi di sistem.
- **Recommended solution:** Hubungkan form loket ke `$jadwalAktif` dan route `operator.loket.store`, pakai kapasitas dinamis kendaraan, dan putuskan skema dukungan penumpang walk-in tanpa akun sebelum mendesain ulang UI loket.

### TD-05 — Verifikasi Pembayaran Tanpa Transaksi, State Guard, & Audit Trail
- **Status:** `VERIFIED` | **Priority:** `Critical`
- **File path:** `app/Http/Controllers/Operator/VerifikasiPembayaranController.php` (L36-52), `app/Http/Controllers/Admin/PesananController.php` (L31-47), `resources/views/operator/pembayaran/show.blade.php` (L151-176), `resources/views/admin/pesanan/show.blade.php`
- **Relevant class/method/component:** `verifikasi()`, `tolak()` pada Operator & Admin
- **Description:** Verifikasi dan penolakan tidak mengecek apakah `pesanan->status === StatusPesanan::DalamProses`, tidak dibungkus `DB::transaction`, tidak mengisi `diverifikasi_oleh`, `diverifikasi_pada`, maupun `alasan_penolakan`, dan tombol aksi di UI tampil untuk semua status.
- **Impact:** Transisi status ilegal dapat terjadi (`Belum Bayar` -> `Lunas`, atau `Lunas` -> `Dibatalkan`) tanpa jejak siapa yang memproses.
- **Recommended solution:** Ekstrak ke satu Action/Service ber-transaksi dengan guard state machine, wajibkan `alasan_penolakan` saat menolak, dan catat `Auth::id()` serta timestamp verifikasi.

### TD-06 — Penghapusan Data Master Admin Menghapus Riwayat Pesanan Secara Permanen (`cascadeOnDelete`)
- **Status:** `VERIFIED` | **Priority:** `Critical`
- **File path:** `app/Http/Controllers/Admin/JadwalController.php` (L86-93), `Admin/KendaraanController.php` (L66-73), `Admin/KotaController.php` (L60-67), migrasi `2026_03_14_000003..000005`
- **Relevant class/method/component:** `destroy()` pada Kota/Kendaraan/Jadwal & FK `cascadeOnDelete()`
- **Description:** Tidak ada pengecekan relasi aktif/historis sebelum `delete()`, sementara seluruh FK memakai `cascadeOnDelete()` dan tidak ada `SoftDeletes`.
- **Impact:** Menghapus kota atau kendaraan otomatis memusnahkan data jadwal, pesanan, dan konfirmasi pembayaran historis.
- **Recommended solution:** Tambahkan validasi pencegahan delete jika berelasi, tambahkan `SoftDeletes` pada master data, dan ubah FK transaksi menjadi `restrictOnDelete()`.

### TD-07 — Konflik Build Tailwind CSS v3 vs Tailwind CSS v4
- **Status:** `VERIFIED` | **Priority:** `High`
- **File path:** `package.json`, `vite.config.js`, `postcss.config.js`, `tailwind.config.js`, `resources/css/app.css`, 49 pemakaian di `resources/views/`
- **Relevant class/method/component:** Konfigurasi styling Vite/Tailwind
- **Description:** Terpasang dua versi (`tailwindcss 3.4.19` dan `@tailwindcss/vite 4.3.4`), tetapi pipeline hanya menjalankan v3. Akibatnya 49 utility v4 di view (`shadow-xs`, `shadow-2xs`, `w-4.5`, `h-4.5`) tidak terkompilasi.
- **Impact:** Inkonsistensi visual dan fondasi styling rusak jika Design System baru dibangun di atas konfigurasi yang ambigu.
- **Recommended solution:** Putuskan dan migrasikan ke satu versi standar (misal Tailwind v4 penuh sesuai `.agent/rules/gotix.md`, atau bersihkan `@tailwindcss/vite` dan gunakan v3 konsisten) sebelum membangun token Design System.

### TD-08 — Batas Waktu Pembayaran (`batas_bayar`) Tidak Pernah Ditegakkan
- **Status:** `VERIFIED` | **Priority:** `High`
- **File path:** `app/Http/Controllers/Member/PesananController.php` (L101), `app/Http/Controllers/Member/PembayaranController.php` (L27-34), `routes/console.php`
- **Relevant class/method/component:** Kedaluwarsa pesanan & `BatalkanPesananKedaluwarsa` (`NOT IMPLEMENTED`)
- **Description:** `batas_bayar` disimpan dengan nilai hardcoded `24 jam` (bukan `config('gotix.batas_bayar_jam')`), tetapi tidak ada scheduler command yang membatalkan pesanan expired maupun guard di `PembayaranController` yang menolak pembayaran terlambat.
- **Impact:** Kursi tertahan tanpa batas waktu oleh pesanan yang ditinggalkan pengguna.
- **Recommended solution:** Implementasikan scheduled Artisan command untuk membatalkan pesanan melewati `batas_bayar`, gunakan `config('gotix.batas_bayar_jam')`, dan tambahkan cek kedaluwarsa saat unggah bukti bayar.

### TD-09 — Bukti Pembayaran Tersimpan di Public Disk & Isu Konfirmasi Pembayaran
- **Status:** `VERIFIED` | **Priority:** `High`
- **File path:** `app/Http/Controllers/Member/PembayaranController.php` (L35-63), `resources/views/member/pembayaran/show.blade.php` (L112-114), `resources/views/member/pembayaran/konfirmasi.blade.php` (L1), `resources/views/operator/pembayaran/index.blade.php` (L90)
- **Relevant class/method/component:** `PembayaranController`, view pembayaran Member & Operator
- **Description:** Bukti transfer disimpan di disk `public` (terekspos URL); `nomor_resi` tidak divalidasi `unique`; tidak ada `DB::transaction`; rekening BCA dihardcode di view member; `member/pembayaran/konfirmasi.blade.php` placeholder; view operator pembayaran memanggil `$pesanan->konfirmasiPembayaran->nama_pengirim` yang salah nama kolom (harusnya `rekening_pengirim`).
- **Impact:** Kebocoran data transaksi, potensi error 500 pada resi duplikat, kolom pengirim kosong di tabel verifikasi.
- **Recommended solution:** Pindahkan file ke disk `local` + controller berotorisasi, perbaiki `rekening_pengirim`, gunakan `config('gotix.bank.*')`, dan hapus/implementasikan route placeholder `konfirmasi`.

### TD-10 — Tidak Ada Rate Limiting pada Login & Placeholder Halaman Detail Jadwal Admin
- **Status:** `VERIFIED` | **Priority:** `High`
- **File path:** `app/Http/Controllers/Auth/MasukController.php` (L25-44), `resources/views/admin/jadwal/show.blade.php` (L1)
- **Relevant class/method/component:** `MasukController::store()`, `Admin\JadwalController::show()`
- **Description:** Endpoint login tidak menerapkan `RateLimiter` terhadap brute-force; halaman detail jadwal admin hanya berisi teks `[PLACEHOLDER] Admin — Detail Jadwal`.
- **Impact:** Rentan brute-force login; menu detail jadwal admin rusak.
- **Recommended solution:** Tambahkan `RateLimiter` standar Laravel pada `MasukController::store()`, dan implementasikan tampilan manifest penumpang/kursi di `admin/jadwal/show.blade.php` (atau hapus route jika tidak dipakai).

---

## 2. Katalog Isu Medium & Low (Dapat Dikerjakan Saat / Setelah Redesign)

| ID | Prioritas | File / Komponen | Deskripsi Ringkas & Solusi |
|---|---|---|---|
| **TD-11** | Medium | `resources/views/layouts/tamu.blade.php` L263-265 | Memanggil `config('gotix.bank_name/account/holder')` yang salah struktur (seharusnya `config('gotix.bank.{nama,rekening,pemilik}')`). |
| **TD-12** | Medium | `resources/views/publik/beranda.blade.php`, `cari-jadwal.blade.php` | Copywriting campur Jawa/Lampung, karakter UTF-8 rusak (``), tombol CTA `@auth` memicu 403 bagi Admin/Operator, rekomendasi jadwal tidak cek `$sisaKursi > 0`. |
| **TD-13** | Medium | `resources/views/layouts/{tamu,admin,operator}.blade.php`, `admin/_nav.blade.php` | Operator tidak memakai `layouts/operator.blade.php` (orphaned) melainkan `layouts.tamu`; `admin/_nav.blade.php` & `welcome.blade.php` orphaned; splash screen 850ms + AOS CDN di setiap halaman tamu/member/operator. |
| **TD-14** | Medium | `app/Http/Controllers/Admin/PenggunaController.php` | Hanya read-only (`index`, `show`). Admin belum bisa membuat akun Operator atau mengubah status pengguna dari UI. |
| **TD-15** | Medium | `app/Http/Controllers/Admin/JadwalController.php` L33-42, `Publik\CariJadwalController.php` L15-33 | `[Dikoreksi Phase 2]`: Validasi `different:kota_asal_id` sudah ada, namun belum ada `after_or_equal:today` pada `store()` dan `CariJadwalController` belum memfilter `tanggal_berangkat >= today()` saat tanpa filter tanggal. |
| **TD-16** | Medium | `app/Http/Controllers/*` & `app/Policies/` | Tidak ada Laravel Policies, Services/Actions, maupun FormRequest di luar Auth; perhitungan sisa kursi dan state pesanan tersebar di banyak controller. |
| **TD-17** | Medium | `phpstan.neon` & `tests/Feature/*` | `phpstan.neon` kosong 0 byte; belum ada feature test untuk booking, pembayaran, verifikasi, e-tiket, loket, dan keluhan. |
| **TD-18** | Medium | Seluruh Blade forms & components | Form CRUD Admin duplikat (`create`/`edit`); label form sering tanpa atribut `for`/`id`; konfirmasi hapus memakai `confirm()` native; badge status duplikat di 8+ file. |
| **TD-19** | Low | `app/Http/Controllers/Admin/DasborController.php` L22 & `admin/dasbor.blade.php` L10 | Typo nama variabel `$menanggguVerifikasi` (triple `g`). |
| **TD-20** | Low | `app/Providers/AppServiceProvider.php`, `public/hot`, `resources/js/bootstrap.js` | `Model::preventLazyLoading()` belum diaktifkan; `public/hot` tertinggal tanpa `public/build/`; `axios` ter-import tanpa pemakaian. |
| **TD-21** | Low | `.agent/rules/gotix.md` | Dokumen aturan AI sangat menyimpang dari codebase aktual — harus disinkronkan setelah keputusan arsitektur Phase 2 disepakati. |

