# 14 — Phase 2 Audit Validation, Corrections, and Testing Baseline

> **Tujuan:** Mendokumentasikan hasil verifikasi ulang temuan Phase 1 terhadap kode sumber aktual, mengoreksi ketidakakuratan dokumentasi Phase 1, mencatat temuan baru, serta merekam baseline pengujian dan build secara jujur.

---

## 1. Validasi Mendalam Area Prioritas Tinggi (Section 3 A–E)

### A. Seat Reservation Integrity (`VERIFIED`)
- **File yang diperiksa ulang:** `app/Models/Pesanan.php`, `app/Enums/StatusPesanan.php`, `app/Http/Controllers/Member/PesananController.php`, `app/Http/Controllers/Operator/PesananLoketController.php`, `database/migrations/2026_03_14_000004_create_pesanan_table.php`, `tests/Feature/ModelTest.php`.
- **Status pemesanan kursi:**
  - Mengunci kursi: `Belum Bayar`, `Dalam Proses`, `Lunas`.
  - Membebaskan kursi di logika PHP: `Dibatalkan`.
- **Perilaku konkurensi & pembatalan berulang:**
  - Kedua controller memakai `DB::transaction()` + `lockForUpdate()` yang mengecualikan `StatusPesanan::Dibatalkan`.
  - **Cacat terkonfirmasi:** Indeks unik MySQL `unique(['jadwal_id', 'nomor_kursi'], 'unique_jadwal_nomor_kursi')` di migrasi `2026_03_14_000004` mencakup **seluruh baris tanpa melihat kolom `status`**. Akibatnya, pesanan yang dibatalkan atau pembayarannya ditolak (`Dibatalkan`) **tidak pernah melepas kursinya di level DB**; percobaan pemesanan ulang lolos cek PHP lalu memicu `QueryException` duplicate entry dan ditelan blok `catch (Throwable)` tanpa logging.
  - **Evaluasi opsi solusi DB di MySQL:**
    - *Opsi ditolak — Unique 3 kolom `('jadwal_id', 'nomor_kursi', 'status')`:* **GAGAL** untuk kasus pembatalan berulang (jika kursi yang sama dibatalkan 2 kali pada jadwal yang sama, baris kedua berstatus `Dibatalkan` tetap bertabrakan di indeks unik) dan gagal mencegah dua baris aktif berstatus berbeda (misal `Belum Bayar` dan `Lunas`) untuk kursi yang sama.
    - *Opsi direkomendasikan — Stored Generated Column + Unique Index:* Tambahkan kolom komputasi `kursi_aktif TINYINT UNSIGNED GENERATED ALWAYS AS (IF(status = 'Dibatalkan', NULL, nomor_kursi)) STORED` serta `UNIQUE KEY unique_jadwal_kursi_aktif (jadwal_id, kursi_aktif)`. Karena indeks `UNIQUE` di MySQL mengizinkan nilai `NULL` berulang tanpa batas, kursi yang dibatalkan berkali-kali tersimpan aman (`kursi_aktif = NULL`), sementara maksimal hanya 1 transaksi aktif (`Belum Bayar`/`Dalam Proses`/`Lunas`) yang dapat menempati `(jadwal_id, nomor_kursi)` secara atomik.
    - *Implikasi migrasi InnoDB:* Sebelum menjalankan `DROP INDEX unique_jadwal_nomor_kursi`, migrasi **wajib** menambahkan indeks terpisah pada `jadwal_id` terlebih dahulu, karena InnoDB memakai indeks unik komposit tersebut sebagai indeks foreign key `pesanan_jadwal_id_foreign`.

### B. Transaction History Protection (`VERIFIED`)
- **Tabel berisi riwayat finansial & transaksi:** `pesanan` (`kode_pesanan`, `harga_snapshot`, `status`, `pengguna_id`, `jadwal_id`), `konfirmasi_pembayaran` (`nomor_resi`, `rekening_pengirim`, `tanggal_transfer`, `jam_transfer`, `bukti_path`, `diverifikasi_oleh`, `diverifikasi_pada`, `alasan_penolakan`), dan `jadwal` (`harga`, `tanggal_berangkat`, `jam_berangkat`).
- **Relasi `cascadeOnDelete()` terkonfirmasi:**
  - `jadwal`: `kota_asal_id`, `kota_tujuan_id`, `kendaraan_id` -> `cascadeOnDelete()`.
  - `pesanan`: `jadwal_id`, `pengguna_id` -> `cascadeOnDelete()`.
  - `konfirmasi_pembayaran`: `pesanan_id` -> `cascadeOnDelete()`; `diverifikasi_oleh` -> `nullOnDelete()`.
  - `keluhan`: `pengguna_id` -> `cascadeOnDelete()`; `pesanan_id`, `dibalas_oleh` -> `nullOnDelete()`.
- **Rekomendasi:** Terapkan `SoftDeletes` (`deleted_at`) pada `kota`, `kendaraan`, `jadwal`, dan `pengguna`; ubah FK transaksi dari `CASCADE` menjadi `RESTRICT` (`restrictOnDelete()`) lewat migrasi baru; cegah penghapusan master yang masih memiliki pesanan, dan gunakan `withTrashed()` saat menampilkan tiket/riwayat lama. Data eksisting tetap terjaga melalui migrasi `ALTER TABLE` tanpa mereset tabel.

### C. Payment Security (`VERIFIED`)
- **Alur saat ini:**
  - *Upload (`Member\PembayaranController::konfirmasiStore`):* Sudah memfilter `status = BelumBayar`, namun **tidak mengecek `batas_bayar`**, **tidak memvalidasi `unique:konfirmasi_pembayaran,nomor_resi`**, memvalidasi `rekening_pengirim` `max:150`, tidak memakai `DB::transaction`, dan menyimpan foto bukti ke disk `'public'` (`storage/app/public/bukti_transfer`, terekspos langsung lewat `/storage/...` tanpa autentikasi).
  - *Verifikasi (`Operator\VerifikasiPembayaranController` & `Admin\PesananController`):* `verifikasi()` dan `tolak()` sama sekali **tidak memeriksa `status === DalamProses`**, **tidak memeriksa keberadaan bukti bayar**, **tidak memakai `DB::transaction`**, serta **tidak mengisi `diverifikasi_oleh`, `diverifikasi_pada`, maupun `alasan_penolakan`**. View `operator/pembayaran/show.blade.php` & `admin/pesanan/show.blade.php` merender tombol Verifikasi/Tolak untuk semua status, sedangkan `operator/pembayaran/index.blade.php` L90 membaca kolom salah `nama_pengirim` (harusnya `rekening_pengirim`).
  - *Kedaluwarsa:* `batas_bayar` diisi literal `now()->addHours(24)` (bukan `config('gotix.batas_bayar_jam')`), dan tidak ada scheduled command pembatalan otomatis (`BatalkanPesananKedaluwarsa` belum ada).
- **Usulan arsitektur aman:** Simpan file bukti pada disk `'local'` (privat), sajikan lewat route/controller berotorisasi (`Pemilik || Operator || Admin`), bungkus submit & verify/reject dalam `DB::transaction() + lockForUpdate()`, enforce state guard + audit fields + `alasan_penolakan` wajib, dan validasi `unique:nomor_resi`.

### D. Placeholder & Mocked Feature Validation (`VERIFIED`)

| Modul | Backend Saat Ini | Frontend Saat Ini | Celah Bisnis & Relasi DB | Otorisasi & Input/Output yang Diharapkan | Kriteria Penerimaan |
|---|---|---|---|---|---|
| **1. Member E-Tiket** | `TiketController::{index,show}` memuat semua status pesanan milik user; `show()` memakai `first()` (tanpa filter `Lunas`). | `tiket/index.blade.php` tampilkan semua pesanan; `tiket/show.blade.php` 100% hardcoded dummy Jakarta–Bandung & QR statis. | Harus memfilter `StatusPesanan::Lunas`; relasi ke `jadwal.{kotaAsal,kotaTujuan,kendaraan}` & `pengguna`. | Hanya Member pemilik pesanan `Lunas`. Input: `kode_pesanan`. Output: E-tiket real (404 jika tidak ditemukan/belum lunas). | Hanya pesanan `Lunas` tampil sebagai e-tiket valid dengan data DB asli. |
| **2. Member Keluhan** | `KeluhanController::{index,create,store}` ada, namun `store()` pakai `max:1000` (DB `VARCHAR(200)`), tanpa validasi enum `KategoriKeluhan`, dan abaikan `pesanan_id`. | `keluhan/index.blade.php` menimpa `$daftarKeluhan` dengan array dummy; `keluhan/create.blade.php` memakai `action="#"` + `alert()`, sertakan `'Armada'` (tidak ada di enum DB), & opsi pesanan dummy. | Sinkronisasi panjang `deskripsi` & enum `kategori`; kirim `$daftarPesanan` user ke form `create()`. | Hanya Member terautentikasi; `pesanan_id` (opsional) harus milik `Auth::id()`. Input: `kategori`, `deskripsi`, `pesanan_id?`. | Form tersubmit ke DB, muncul di daftar member & inbox admin tanpa truncation error. |
| **3. Operator Walk-in Loket** | `PesananLoketController::{index,create,store}` mengirim `$jadwalAktif` & `$daftarMember`; `store()` hardcode `max:5` kursi, wajib `pengguna_id`, set `status = Lunas`, `batas_bayar = +1h`. | `loket/index.blade.php` abaikan `$jadwalAktif`, pakai rute Jawa statis & Alpine `submitLoket()` modal struk palsu tanpa POST. `loket/create.blade.php` hanya teks `[PLACEHOLDER]`. | Tidak mendukung penumpang walk-in tanpa akun `pengguna`; kapasitas `max:5` tidak dinamis; form frontend terputus. | Hanya Operator (`peran:Operator`). Input: `jadwal_id`, `nomor_kursi` (`1..kapasitas`), data pelanggan / `pengguna_id`. Output: Record `Pesanan` `Lunas` nyata. | Pemesanan di loket tersimpan ke DB, memotong stok kursi real, & cetak struk memakai `kode_pesanan` nyata. |
| **4. Member Konfirmasi Bayar** | `PembayaranController::konfirmasi($kode)` mengambil `$pesanan` dan mengembalikan view `member.pembayaran.konfirmasi`. Upload sebenarnya dilakukan via `show` (`POST konfirmasiStore`). | `member/pembayaran/konfirmasi.blade.php` hanya berisi `[PLACEHOLDER] Form Konfirmasi Pembayaran`; `pembayaran/show.blade.php` hardcode BCA `8820 1928 33`. | Duplikasi peran antara `pembayaran.show` (sudah punya form upload) dan `pembayaran.konfirmasi` (placeholder). | Hanya Member pemilik pesanan. Input: `kode_pesanan`. Output: Halaman konfirmasi/status pasca upload atau redirect ke `pesanan.show`. | Tidak ada endpoint aktif yang mengembalikan view placeholder. |
| **5. Admin Detail Jadwal** | `Admin\JadwalController::show($id)` sudah eager load `kotaAsal`, `kotaTujuan`, `kendaraan`, `pesanan.pengguna`. | `admin/jadwal/show.blade.php` hanya berisi `[PLACEHOLDER] Admin — Detail Jadwal`. | Backend sudah siap, hanya view manifest penumpang & keterisian kursi belum diimplementasikan. | Hanya Admin (`peran:Admin`). Input: `id` jadwal. Output: Detail jadwal, daftar kursi, dan daftar pesanan terkait. | Halaman detail jadwal menampilkan data rute, armada, okupansi kursi, & manifest penumpang. |

### E. Tailwind Compatibility (`VERIFIED`)
- **Pipeline aktif terverifikasi:** `vite.config.js` tidak memakai `@tailwindcss/vite`; `postcss.config.js` menjalankan `tailwindcss` v3 (`3.4.19`) + `autoprefixer` memakai `tailwind.config.js` (`@tailwindcss/forms 0.5.11`) dan `@tailwind` di `resources/css/app.css`.
- **Bukti hasil build aktual (`npm run build`):** Berhasil dalam `1.88s` (`public/build/assets/app-bgCLE4YB.css` `59.45 kB`), dan verifikasi isi CSS hasil kompilasi membuktikan **0% utility Tailwind v4 masuk ke output** (`shadow-xs: False`, `shadow-2xs: False`, `w-4.5: False`, `h-4.5: False`, sedangkan `shadow-sm: True`).
- **Rekomendasi paling minim disrupsi:** Pertahankan **Tailwind CSS v3 (`3.4.19`)** sebagai baseline hingga atau selama Design System (karena konfigurasi `tailwind.config.js`, PostCSS, dan `@tailwindcss/forms` sudah 100% aktif dan stabil). Perubahan yang dibutuhkan nanti: hapus `@tailwindcss/vite` yang tak terpakai dari `package.json`, dan ganti 49 pemakaian kelas v4 di Blade dengan utilitas v3 setara.

---

## 2. Koreksi Atas Ketidakakuratan Audit Phase 1

Pemeriksaan ulang terhadap file aktual pada Phase 2 menemukan **5 koreksi penting** terhadap laporan Phase 1 (`docs/00`, `03`, `05`, `08`, `09`):

1. **Tabel & Seeder `kota` (`05-database-audit.md`):** Phase 1 sempat mencatat kolom `kode_kota` dan hanya 8 kota. **Kenyataan di source code (`2026_03_14_000001_create_kota_table.php` & `KotaSeeder.php`):** tabel `kota` hanya memiliki `id`, `nama_kota`, `timestamps` (tanpa `kode_kota`), dan `KotaSeeder` mengisi **27 kota/kabupaten di Lampung**.
2. **Tabel & Seeder `kendaraan` (`05-database-audit.md`):** Phase 1 mencatat kolom `model`, `aktif`, `scopeAktif()`, serta armada HiAce/Elf/Luxio (kapasitas 5–12). **Kenyataan (`2026_03_14_000002_create_kendaraan_table.php`, `Kendaraan.php`, `KendaraanSeeder.php`):** kolomnya bernama `jenis_kendaraan`, **tidak ada kolom `aktif` maupun `scopeAktif()`**, dan `KendaraanSeeder` hanya mengisi **2 armada** (`Avanza` 5 kursi dan `Xenia` 5 kursi). (Nama HiAce/Elf hanya muncul di copywriting/dummy Blade view.)
3. **Method/Scope Model `Jadwal` & `Pesanan` (`03/05/08-audit`):** Phase 1 sempat mengutip `Jadwal::scopeTersedia()`, `Jadwal::getSisaKursiAttribute()`, `Pesanan::buatKode()`, dan `Pesanan::scopePerluVerifikasi()`. **Kenyataan (`Jadwal.php` L1-89 & `Pesanan.php` L1-94):** metode dan scope tersebut **tidak ada di model**; penyaringan tanggal dan generasi kode `GTX-XXXXXX` ditulis langsung di dalam controller dan kalkulasi sisa kursi ditulis inline di Blade/controller.
4. **Migrasi Awal Pengguna (`05-database-audit.md`):** Phase 1 sempat menyebut adanya tabel `users` yatim dari `create_users_table.php` di samping migrasi terpisah `pengguna`. **Kenyataan:** File migrasi awal telah bernama `0001_01_01_000000_create_pengguna_table.php` dan langsung membuat `pengguna`, `password_reset_tokens`, dan `sessions` (tidak ada tabel `users` yatim).
5. **Validasi `different:kota_asal_id` (`03-backend-audit.md`, `TD-15`):** Phase 1 mengklaim `Admin\JadwalController` tidak mengecek `kota_asal_id !== kota_tujuan_id`. **Kenyataan (`Admin\JadwalController.php` L37 & L73):** Validasi `'different:kota_asal_id'` **sudah ada** di `store()` maupun `update()`; yang belum ada hanyalah pencegahan `tanggal_berangkat < today()` pada pembuatan jadwal baru (`store()`).

### Temuan Baru yang Ditemukan di Phase 2
1. **`Publik\CariJadwalController::index()` (L15-33):** Pencarian jadwal tanpa filter tanggal menampilkan seluruh jadwal termasuk jadwal tanggal lampau (berbeda dengan `BerandaController` yang memfilter `tanggal_berangkat >= today()`).
2. **`member/pesanan/create.blade.php` (L100):** Terdapat atribut Alpine mati `x-bind:class="kursiDipilih === {{ $i }} ? '' : ''"` (visual kursi terpilih sebenarnya bekerja melalui Tailwind `peer-checked:`).
3. **Konflik Berbahaya di `.agent/rules/gotix.md` L55 (Sebelum Disinkronkan):** Aturan lama secara eksplisit mengizinkan AI menjalankan `migrate:fresh --seed` pada database development `gotix`, bertolak belakang dengan aturan keamanan database yang ditetapkan di root `AGENTS.md`. (Sudah dikoreksi pada Phase 2).
4. **Ketergantungan Indeks FK InnoDB (`pesanan`):** Membuktikan bahwa migrasi perbaikan `unique_jadwal_nomor_kursi` wajib menambah indeks `jadwal_id` terlebih dahulu sebelum men-drop indeks komposit lama.

---

## 3. Hasil Baseline Pengujian & Build (`Section 6`)

Pemeriksaan environment pada `.env.testing` dan `phpunit.xml` mengonfirmasi bahwa konfigurasi testing diarahkan ke database terisolasi `gotix_testing` (MySQL `127.0.0.1:3306`, `SESSION_DRIVER=array`, `CACHE_STORE=array`, `QUEUE_CONNECTION=sync`). Berikut hasil eksekusi aktual pada workspace:

| Perintah | Hasil Aktual | Status | Diagnosa Tepat |
|---|---|---|---|
| `php artisan test` | Exit code `2`: `INFO Cannot open bootstrap script "D:\SEMESTER 5\Pemeliharaan Perangkat Lunak\Gotix\vendor/autoload.php".` | **UNKNOWN** | Pada sandbox Windows saat audit, PHP `is_file('vendor/autoload.php')` bernilai `true` tetapi `is_readable('vendor/autoload.php')` mengembalikan `false` akibat batasan ACL sandbox, sehingga PHPUnit tidak dapat memuat bootstrapper maupun mengevaluasi suite (34 test case). |
| `php vendor/bin/pint --test` | Mencetak error 2×: `In FileHandler.php line 153: Cannot write to file "C:\Users\LOQ\AppData\Local\Temp\054aa4855be4e89f17f65599c97a9350" as it is not writable.` lalu exit `0`. | **UNKNOWN** | `sys_get_temp_dir()` menunjuk ke folder Temp di luar direktori kerja sandbox (`is_writable = false`), sehingga Pint tidak dapat menulis file cache sementara untuk memeriksa kepatuhan gaya kode. |
| `php vendor/bin/phpstan analyse` | Exit code `1`: `File 'D:\SEMESTER 5\Pemeliharaan Perangkat Lunak\Gotix\phpstan.neon' is missing or is not readable.` | **UNKNOWN** | Kombinasi dua faktor: file `phpstan.neon` kosong (0 byte / belum dikonfigurasi) dan kendala `is_readable()` pada sandbox Windows. |
| `npm run build` | Exit code `0`: `vite v7.3.7` mentransformasi 62 modul dalam `1.88s`, menghasilkan `public/build/manifest.json` (`0.33 kB`), `app-bgCLE4YB.css` (`59.45 kB`), `app-DDpvETUX.js` (`114.55 kB`). | **VERIFIED (PASS BUILD / LOSSY TAILWIND V4)** | Build Vite berjalan bersih dan membuktikan bahwa Tailwind v3 aktif mengompilasi CSS tetapi menjatuhkan 49 utilitas Tailwind v4 dari hasil output CSS. |

> **Tindakan yang diperlukan pada host lokal pengembang (di luar sandbox):** Jalankan `php artisan test` terhadap `gotix_testing` dan `php vendor/bin/pint --test` langsung di terminal lokal pemilik proyek untuk mengonfirmasi baseline lulus/gagal sesungguhnya sebelum eksekusi Phase 3.

---

## 4. Keputusan Bisnis yang Membutuhkan Persetujuan Pemilik Proyek

1. **Alur Penumpang Walk-in Loket (`P1-03`):** Apakah pemesanan walk-in oleh Operator akan tetap memilih akun Member terdaftar (`pengguna_id`), otomatis membuat akun Member baru, atau mengizinkan kolom penumpang guest (`nama_penumpang`, `telepon_penumpang`, `pengguna_id nullable`) pada tabel `pesanan`?
2. **Alur Pembayaran yang Ditolak (`P0-04`):** Ketika Operator/Admin menolak bukti transfer (`tolak()`), apakah pesanan langsung menjadi `Dibatalkan` (memaksa buat pesanan baru), atau kembali ke `Belum Bayar` dengan `alasan_penolakan` agar member bisa mengunggah ulang bukti bayar selama `batas_bayar` belum lewat?
3. **Batas Karakter & Kategori Keluhan (`P1-02`):** Apakah kolom `keluhan.deskripsi` diperlebar ke `TEXT` / 1000 karakter, atau form/validasi dikunci di 200 karakter? Dan apakah kategori `'Armada'` akan ditambahkan ke enum `KategoriKeluhan` atau dihapus dari UI?
4. **Route `member.pembayaran.konfirmasi` (`P1-05`):** Apakah route placeholder ini akan difungsikan sebagai halaman status/ringkasan setelah upload bukti bayar, atau dihapus karena alur saat ini me-redirect ke `member.pesanan.show`?
5. **Pengelolaan Akun Pengguna oleh Admin (`P2-04`):** Apakah `Admin\PenggunaController` perlu diperluas untuk membuat/mengedit akun Operator Loket dan menonaktifkan pengguna?
6. **Versi Tailwind CSS Resmi (`P2-01`) & Referensi Visual Design System (`P4-01`):** Konfirmasi apakah akan **mempertahankan Tailwind v3** (paling minim disrupsi) atau migrasi ke **Tailwind v4**, serta bagikan referensi visual/palet warna/preferensi layout saat siap memasuki tahap desain.

