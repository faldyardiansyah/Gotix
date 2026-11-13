# 12 — Remediation Roadmap

> **Prinsip Eksekusi:** Setiap tugas dikerjakan secara inkremental setelah mendapat persetujuan pemilik proyek, diurutkan berdasarkan dependensi agar integritas data & keamanan selesai sebelum refactor komponen dan redesign UI/UX.  
> **Pembaruan Phase 3.0:** Seluruh 6 keputusan bisnis telah resmi disetujui pemilik proyek dan disematkan pada tugas terkait (`P0-04`, `P1-02`, `P1-03`, `P1-05`, `P2-01`, `P2-04`). Baseline aman & rencana teknis `P0-01` terdokumentasi di `docs/15-phase-3-baseline-and-seat-integrity-plan.md`.

---

## Ringkasan Fase Prioritas

| Prioritas | Fokus | Jumlah Tugas | Ketergantungan DB |
|---|---|---|---|
| **P0** | Integritas data kritis, keamanan pembayaran, proteksi transaksi & login | 5 tugas (`P0-01` s.d. `P0-05`) | Ya (migrasi baru non-destruktif) |
| **P1** | Fitur inti & placeholder/mocked (E-Tiket, Keluhan, Loket, Konfirmasi Bayar, Detail Jadwal, Kadaluwarsa) | 6 tugas (`P1-01` s.d. `P1-06`) | Ya (opsional/tertentu sesuai keputusan owner) |
| **P2** | Konsistensi build frontend (Tailwind v3) & arsitektur shell/layout 3 peran | 4 tugas (`P2-01` s.d. `P2-04`) | Tidak |
| **P3** | Maintainability, layanan bersama, komponen reusable, Policies, QA & test coverage | 4 tugas (`P3-01` s.d. `P3-04`) | Tidak |
| **P4** | Redesign UI/UX terpadu dengan Design System bersama | 4 tugas (`P4-01` s.d. `P4-04`) | Tidak |

---

## P0 — Critical Data Integrity and Security

### `P0-01` — Perbaikan Constraint Kursi Unik (`pesanan`) untuk Kursi Dibatalkan
- **Status:** **SELESAI (Phase 3.1 — 12 November 2026)**
- **Hasil Implementasi:**
  - Migrasi `database/migrations/2026_11_12_000001_update_unique_kursi_on_pesanan_table.php` berhasil dieksekusi di `gotix` & `gotix_testing`.
  - Mengganti `unique_jadwal_nomor_kursi` dengan generated column `kursi_aktif` (`IF(status = 'Dibatalkan', NULL, nomor_kursi)`) dan unique index `unique_jadwal_kursi_aktif (jadwal_id, kursi_aktif)`.
  - Indeks mandiri `idx_pesanan_jadwal_id` ditambahkan terlebih dahulu untuk melindungi FK `pesanan_jadwal_id_foreign`.
  - `Member\PesananController` & `Operator\PesananLoketController` diperbarui: serialisasi reservasi melalui `Jadwal::where('id', ...)->lockForUpdate()`, penanganan graceful `QueryException` (duplicate entry 1062/23000), validasi jadwal masa depan, kapasitas dinamis armada, dan logging `Log::error`.
  - `app/Models/Pesanan.php` menambahkan casting `kursi_aktif => integer`.
  - Regression test suite `tests/Feature/SeatReservationIntegrityTest.php` (13 skenario) dan test suite penuh (47 tests, 113 assertions) lulus 100%. Formatting Pint lulus tanpa pelanggaran.
- **Affected files:**
  - *Baru:* `database/migrations/2026_11_12_000001_update_unique_kursi_on_pesanan_table.php`
  - *Baru:* `tests/Feature/SeatReservationIntegrityTest.php`
  - `app/Http/Controllers/Member/PesananController.php`
  - `app/Http/Controllers/Operator/PesananLoketController.php`
  - `app/Models/Pesanan.php`
  - *Dokumentasi:* `docs/16-phase-3.1-seat-integrity-implementation.md`
- **Dependencies:** Tidak ada (fondasi paling awal).
- **Risk level:** **High** (perubahan constraint tabel transaksi; InnoDB tidak mengizinkan drop `unique_jadwal_nomor_kursi` sebelum ada indeks tersendiri pada `jadwal_id` untuk FK).
- **Implementation proposal:**
  1. Di migrasi baru, buat indeks biasa `idx_pesanan_jadwal_id` pada `jadwal_id`.
  2. Drop unique key lama `unique_jadwal_nomor_kursi`.
  3. Tambahkan stored generated column: `kursi_aktif TINYINT UNSIGNED GENERATED ALWAYS AS (IF(status = 'Dibatalkan', NULL, nomor_kursi)) STORED`.
  4. Tambahkan `UNIQUE KEY unique_jadwal_kursi_aktif (jadwal_id, kursi_aktif)`.
  5. Pastikan validasi di `PesananController::store()` juga menolak jadwal yang sudah lewat (`tanggal_berangkat < today()`) dan `catch (Throwable $e)` mencatat `Log::error(...)`.
- **Required tests:** Regression test Pest: buat pesanan kursi 1 -> ubah status `Dibatalkan` -> pesan ulang kursi 1 sukses -> batal lagi -> pesan ulang lagi sukses; pesan kursi 1 ketika masih `Belum Bayar` / `Dalam Proses` / `Lunas` harus melempar `QueryException` di DB dan error validasi di controller.
- **Acceptance criteria:** Kursi bekas pesanan batal dapat dipesan berulang kali; dua pesanan aktif untuk `(jadwal_id, nomor_kursi)` tetap mustahil secara database; error non-domain tercatat di log.
- **Database changes required:** **Ya** (migrasi `ALTER TABLE` non-destruktif pada `pesanan`; tanpa mengubah file migrasi lama).

### `P0-02` — Proteksi Riwayat Transaksi dari Penghapusan Master (`SoftDeletes` & `restrictOnDelete`)
- **Status:** **SELESAI (Phase 3.2 & Koreksi Phase 3.2.1 — 12 November 2026)**
- **Hasil Implementasi:**
  - Migrasi `database/migrations/2026_11_12_000002_protect_transaction_history_and_soft_deletes.php` berhasil dieksekusi di `gotix` & `gotix_testing`.
  - Menambahkan kolom `deleted_at` (`SoftDeletes`) ke tabel master `kota`, `kendaraan`, `jadwal`, dan `pengguna`.
  - Mengubah 8 foreign key berisiko kaskade pada `jadwal`, `pesanan`, `konfirmasi_pembayaran`, dan `keluhan` menjadi `ON DELETE RESTRICT`. Foreign key audit verifikator dan admin penjawab keluhan juga diproteksi dengan `RESTRICT`.
  - Menambahkan trait `SoftDeletes` pada model `Kota`, `Kendaraan`, `Jadwal`, dan `Pengguna`.
  - Menambahkan `->withTrashed()` pada relasi pemuatan riwayat transaksi: `Pesanan::jadwal()`, `Pesanan::pengguna()`, `Jadwal::kotaAsal()`, `Jadwal::kotaTujuan()`, `Jadwal::kendaraan()`, `KonfirmasiPembayaran::verifikator()`, `Keluhan::pengguna()`, dan `Keluhan::petugasBalas()`.
  - **Koreksi Phase 3.2.1 pada Controller Admin (`destroy()`):** Memperbaiki guard penghapusan agar entitas master dengan jadwal lampau/selesai dapat di-soft-delete tanpa memblokir pemensiunan data secara keliru, sementara jadwal aktif masa depan dan transaksi pembayaran belum selesai (`Belum Bayar`/`Dalam Proses`) tetap terproteksi secara ketat dari penghapusan.
  - Regression test suite `tests/Feature/TransactionHistoryProtectionTest.php` diperkaya menjadi 17 skenario granular, dan test suite penuh (64 tests, 178 assertions) lulus 100%. Formatting Pint lulus tanpa pelanggaran.
- **Affected files:**
  - *Baru:* `database/migrations/2026_11_12_000002_protect_transaction_history_and_soft_deletes.php`
  - *Baru:* `tests/Feature/TransactionHistoryProtectionTest.php`
  - `app/Models/{Kota,Kendaraan,Jadwal,Pengguna,Pesanan,KonfirmasiPembayaran,Keluhan}.php`
  - `app/Http/Controllers/Admin/{KotaController,KendaraanController,JadwalController}.php`
  - *Dokumentasi:* `docs/17-phase-3.2-transaction-history-protection.md`
- **Dependencies:** `P0-01` selesai lebih dulu.
- **Risk level:** **High** (mengubah perilaku foreign key dan query relasi global).
- **Implementation proposal:**
  1. Tambahkan kolom `deleted_at` (`SoftDeletes`) pada tabel `kota`, `kendaraan`, `jadwal`, `pengguna` (dan pertimbangkan flag `aktif` pada `kendaraan`/`pengguna` bila disetujui).
  2. Ubah FK `jadwal.{kota_asal_id,kota_tujuan_id,kendaraan_id}`, `pesanan.{jadwal_id,pengguna_id}`, dan `konfirmasi_pembayaran.pesanan_id` dari `CASCADE` menjadi `RESTRICT`.
  3. Di controller `destroy()`, cegah hard delete jika sudah ada `pesanan` (gunakan soft delete / nonaktifkan) dan tampilkan pesan informatif ke Admin. Gunakan `withTrashed()` pada relasi pembacaan `Pesanan` -> `jadwal` -> `kota`/`kendaraan` dan `pengguna` agar riwayat e-tiket/transaksi tetap terbaca.
- **Required tests:** Test bahwa menghapus kota/kendaraan/jadwal dengan pesanan terhubung tidak menghilangkan record `pesanan` & `konfirmasi_pembayaran`, dan e-tiket historis tetap tampil utuh.
- **Acceptance criteria:** Tidak ada lagi jalur UI/controller yang menghapus data `pesanan` atau `konfirmasi_pembayaran` secara kaskade; data historis tetap terbaca.
- **Database changes required:** **Ya** (migrasi baru menambah `deleted_at` dan mengubah FK `ON DELETE`).

### `P0-03` — Pengamanan Storage Bukti Pembayaran (Private Disk + Otorisasi Streaming)
- **Status:** **SELESAI (Phase 3.3 & Hardening Phase 3.3.2 — 12 November 2026)**
- **Hasil Implementasi:**
  - File bukti pembayaran dipindahkan ke private disk (`local` dengan root `storage/app/private/bukti_pembayaran`), sehingga berkas tidak lagi tersimpan pada storage publik dan tidak dapat diakses langsung melalui web root atau URL `/storage/...`.
  - Validasi unggahan pada `Member\PembayaranController::konfirmasiStore` diperketat dengan whitelist aman (`jpg, jpeg, png, webp, pdf`), batas ukuran maksimal 5MB (5120KB), penamaan acak unik melalui hash Laravel.
  - Endpoint controller streaming terotorisasi `App\Http\Controllers\PembayaranBuktiController::show` dibuat dan didaftarkan pada route `Route::get('/pembayaran/bukti/{konfirmasi}', ...)->name('pembayaran.bukti')->middleware('auth')`.
  - Otorisasi berlapis (RBAC): Admin dan Operator dapat melihat seluruh bukti untuk verifikasi transaksi; Member hanya dapat melihat bukti transfer yang terhubung dengan pesanan miliknya sendiri (`$konfirmasi->pesanan->pengguna_id === Auth::id()`); pihak lain/member lain ditolak `403 Forbidden`; tamu dialihkan ke halaman masuk (`302`).
  - **Hardening Root Confinement (Phase 3.3.2):** Validasi kanonikal `realpath()` diperketat dengan pemisah batas penutup `$diskRootWithSeparator = rtrim($diskRoot, '\\/') . DIRECTORY_SEPARATOR;` dan memastikan file kanonikal wajib berada di dalam batas direktori root privat, mencegah akses file pada direktori bersaudara (*sibling directories* seperti `private-backup` atau `private_data`) serta symlink ke luar root disk.
  - **Hardening Command Migrasi (Phase 3.3.2):** Command `php artisan gotix:migrate-payment-proofs` ditingkatkan dengan mekanisme *destination overwrite guard*: mendeteksi file tujuan yang sudah ada dan benturan nama sesama berkas sumber, menghasilkan nama unik bersufiks hash, memverifikasi SHA-256, dan memastikan file publik asli tidak pernah dihapus jika terjadi kegagalan penulisan atau update DB.
  - Header respons aman: `X-Content-Type-Options: nosniff`, `Cache-Control: private, no-store, no-cache, must-revalidate`, `Pragma: no-cache`, `Expires: 0`, dan `Content-Disposition: inline`.
  - Tampilan Blade dimutakhirkan menggantikan URL publik `asset('storage/...')` dengan route terotorisasi `route('pembayaran.bukti', ...)` pada: `resources/views/operator/pembayaran/show.blade.php`, `resources/views/admin/pesanan/show.blade.php`, `resources/views/member/pembayaran/show.blade.php`, dan `resources/views/member/pesanan/show.blade.php`.
  - Regression test suite `tests/Feature/PaymentProofSecurityTest.php` (33 skenario) dan test suite penuh (97 tests, 271 assertions) lulus 100%. Formatting Pint lulus tanpa pelanggaran.
- **Affected files:**
  - *Baru:* `app/Http/Controllers/PembayaranBuktiController.php`
  - *Baru:* `app/Console/Commands/MigratePaymentProofs.php`
  - *Baru:* `tests/Feature/PaymentProofSecurityTest.php`
  - `app/Http/Controllers/Member/PembayaranController.php`
  - `routes/web.php`
  - `resources/views/operator/pembayaran/show.blade.php`
  - `resources/views/admin/pesanan/show.blade.php`
  - `resources/views/member/pembayaran/show.blade.php`
  - `resources/views/member/pesanan/show.blade.php`
  - *Dokumentasi:* `docs/18-phase-3.3-private-payment-proof-storage.md`, `docs/19-phase-3.3.2-private-payment-proof-hardening.md`
- **Dependencies:** Tidak ada.
- **Risk level:** **Medium** (file existing di `storage/app/public/bukti_transfer` ditangani melalui fallback transparan dan artisan command migrasi).
- **Implementation proposal:**
  1. Simpan upload baru via `$file->store('bukti_pembayaran', 'local')`.
  2. Buat route terproteksi `auth` untuk menampilkan gambar bukti bayar dengan otorisasi: hanya pemilik `pesanan` (`Member`), `Operator`, atau `Admin` yang boleh mengakses.
  3. Di controller streaming gambar, cek disk `'local'` lalu fallback ke disk `'public'` untuk kompatibilitas bukti lama sebelum dipindahkan.
- **Required tests:** Guest atau Member lain mengakses URL bukti transfer mendapat `302`/`403`; pemilik pesanan, Operator, dan Admin mendapat `200` dengan `Content-Type` gambar yang sesuai; boundary root confinement menolak direktori bersaudara dan symlink luar; command migrasi mencegah overwrite.
- **Acceptance criteria:** Upload bukti baru tidak tersimpan di folder publik dan tidak dapat diakses tanpa autentikasi dan otorisasi peran/kepemilikan; boundary confinement dan migration overwrite guard terverifikasi kokoh.
- **Database changes required:** **Tidak** (kolom `bukti_path` sudah bertipe string relatif).

### `P0-04` — Transaksionalitas, State Guard, & Audit Trail Verifikasi Pembayaran
- **Status:** **FINALIZED / SELESAI PENUH (Phase 4 & Eksekusi Database Utama — 12 November 2026)**
- **Hasil Implementasi:**
  - Migrasi `database/migrations/2026_11_12_000003_create_riwayat_pembayaran_table.php` telah berhasil dieksekusi di database utama `gotix` (Batch 4) dan di database testing `gotix_testing`. Tabel `riwayat_pembayaran` aktif dengan composite index `(pesanan_id, created_at)` dan foreign keys berintegritas tinggi (`RESTRICT` untuk pesanan & pengguna, `SET NULL` untuk konfirmasi & verifikator).
  - Model `App\Models\RiwayatPembayaran` diimplementasikan dengan relasi `pesanan()`, `konfirmasiPembayaran()`, `pengunggah()`, dan `verifikator()`. Relasi `Pesanan::riwayatPembayaran()` dan `KonfirmasiPembayaran::riwayat()` ditambahkan.
  - Endpoint private streaming diperluas dengan method `PembayaranBuktiController::showRiwayat` dan route `pembayaran.bukti.riwayat` sehingga bukti transfer historis yang pernah ditolak tetap dapat diakses oleh Member pemilik dan Operator/Admin secara terotorisasi.
  - Pengamanan proses unggah pada `Member\PembayaranController::konfirmasiStore`:
    - Memeriksa kepemilikan pesanan (403), status pesanan `BelumBayar`, dan batas bayar `now()->lte($pesanan->batas_bayar)`.
    - Validasi resi duplikat ditangani dengan `Rule::unique('konfirmasi_pembayaran', 'nomor_resi')->ignore(...)`.
    - Berkas lama **dipertahankan pada private disk** (tidak dihapus) sebagai bukti historis audit.
    - Pembungkusan transaksi database `DB::transaction()` dan penguncian konkurensi `lockForUpdate()`.
    - Mekanisme orphan cleanup: jika validasi bisnis di dalam lock atau database gagal, berkas baru yang diunggah otomatis dihapus agar tidak menjadi file sampah.
  - Pengamanan proses verifikasi & penolakan pada `Operator\VerifikasiPembayaranController` dan `Admin\PesananController`:
    - Menggunakan `DB::transaction()` dan `lockForUpdate()`.
    - Memverifikasi status wajib `StatusPesanan::DalamProses`.
    - Pada verifikasi sukses (`verifikasi`): status pesanan diubah menjadi `StatusPesanan::Lunas`, mencatat `diverifikasi_oleh = Auth::id()` dan `diverifikasi_pada = now()`, serta memperbarui status riwayat menjadi `Diterima`.
    - Pada penolakan (`tolak`): mewajibkan `alasan_penolakan` (validasi server-side max:1000), mengembalikan status pesanan ke `StatusPesanan::BelumBayar` (sesuai aturan bisnis GOTIX agar penumpang dapat memperbaiki pembayaran sebelum batas bayar), mencatat auditor dan timestamp, serta memperbarui status riwayat menjadi `Ditolak`.
  - Mekanisme Expiry Pembayaran:
    - Artisan console command `app/Console/Commands/CancelExpiredOrders.php` (`gotix:cancel-expired-orders`) dibuat.
    - Membatalkan secara otomatis pesanan `Belum Bayar` yang telah melewati `batas_bayar` dengan penguncian `lockForUpdate()`.
    - Pelepasan kursi otomatis: ketika status diubah ke `Dibatalkan`, kolom tersimpan `kursi_aktif` otomatis bernilai `NULL` (berkat fondasi P0-01) sehingga kursi langsung dapat dipesan kembali oleh pengguna lain.
    - Pesanan `Lunas` dan pesanan `Dalam Proses` terproteksi penuh dari pembatalan otomatis.
    - Command didaftarkan ke Laravel Scheduler pada `routes/console.php` untuk dijalankan setiap menit.
  - Tampilan Blade dimutakhirkan:
    - Perbaikan typo nama kolom di `resources/views/operator/pembayaran/index.blade.php` L90: `nama_pengirim` diubah ke `rekening_pengirim`.
    - Halaman Operator (`operator/pembayaran/show.blade.php`) & Admin (`admin/pesanan/show.blade.php`): menambahkan modal/form wajib alasan penolakan, perlindungan tombol aksi sesuai status pesanan, tampilan audit trail verifikator, serta daftar histori unggahan sebelumnya.
    - Halaman Member (`member/pembayaran/show.blade.php` & `member/pesanan/show.blade.php`): menampilkan banner peringatan penolakan sebelumnya disertai alasan jelas dari petugas, penutupan form jika batas pembayaran telah berakhir, hitung mundur batas bayar, serta histori pengunggahan struk.
  - Test Suite & QA:
    - Test suite baru `tests/Feature/PaymentVerificationSecurityTest.php` (14 skenario komprehensif, 84 assertions) lulus 100%.
    - Test suite penuh GOTIX (111 passed, 355 assertions) lulus 100% tanpa kegagalan.
    - Pemformatan Laravel Pint lulus tanpa pelanggaran (`passed`).
- **Affected files:**
  - *Baru:* `database/migrations/2026_11_12_000003_create_riwayat_pembayaran_table.php`
  - *Baru:* `app/Models/RiwayatPembayaran.php`
  - *Baru:* `app/Console/Commands/CancelExpiredOrders.php`
  - *Baru:* `tests/Feature/PaymentVerificationSecurityTest.php`
  - `app/Http/Controllers/Member/PembayaranController.php`
  - `app/Http/Controllers/Member/PesananController.php`
  - `app/Http/Controllers/Operator/VerifikasiPembayaranController.php`
  - `app/Http/Controllers/Admin/PesananController.php`
  - `app/Http/Controllers/PembayaranBuktiController.php`
  - `app/Models/Pesanan.php`
  - `app/Models/KonfirmasiPembayaran.php`
  - `routes/web.php`
  - `routes/console.php`
  - `resources/views/operator/pembayaran/{index,show}.blade.php`
  - `resources/views/admin/pesanan/show.blade.php`
  - `resources/views/member/pembayaran/show.blade.php`
  - `resources/views/member/pesanan/show.blade.php`
  - `tests/Feature/PaymentProofSecurityTest.php`
  - `tests/Feature/SeatReservationIntegrityTest.php`
  - *Dokumentasi:* `docs/20-phase-4-payment-state-guard-and-verification.md`, `docs/21-phase-4.1-payment-implementation-review.md`, `docs/22-p0-04-main-database-migration-readiness.md`, `docs/23-p0-04-main-database-migration-execution.md`

### `P0-05` — Rate Limiting Autentikasi & Pembacaan Konfigurasi Bisnis Konsisten
- **Status:** **FINALIZED / SELESAI PENUH (Phase 5 — 12 November 2026)**
- **Hasil Implementasi:**
  - Perlindungan rate limiting autentikasi login diterapkan pada `MasukController::store` dengan ambang batas 5 percobaan gagal per 60 detik berbasis kombinasi identitas dinormalisasi (lowercase & trim) dan alamat IP (`login:<identitas>|<ip>`).
  - Gagal login menambah hit counter; login berhasil membersihkan hit counter; request ke-6 yang melampaui batas mengembalikan respons HTTP 429 Too Many Requests dengan header `Retry-After` dan pesan aman ramah pengguna tanpa membocorkan keberadaan akun (*enumeration safe*).
  - Perlindungan rate limiting registrasi diterapkan pada `DaftarController::store` (5 percobaan per 60 detik berbasis IP) untuk memitigasi serangan pembuatan akun massal / bot spam.
  - Nilai aturan bisnis yang tersebar dipusatkan pada `config/gotix.php`:
    - Batas bayar online (`gotix.batas_bayar_jam = 24`) & batas bayar loket (`gotix.batas_bayar_loket_jam = 1`).
    - Fallback kapasitas armada bus (`gotix.kapasitas_kursi = 5`).
    - Informasi rekening bank resmi terpadu (`gotix.bank.nama`, `gotix.bank.nomor`/`rekening`, `gotix.bank.atas_nama`/`pemilik`).
    - Batas berkas bukti transfer (`gotix.pembayaran.max_file_kb = 5120`, `gotix.pembayaran.mimes = ['jpg','jpeg','png','webp','pdf']`).
    - Batas karakter pengaduan keluhan & balasan (`gotix.keluhan.deskripsi_min = 10`, `deskripsi_max = 1000`, `balasan_min = 3`, `balasan_max = 1000`, `alasan_penolakan_max = 1000`).
    - Konfigurasi batas laju autentikasi (`gotix.auth.login_max_attempts = 5`, `gotix.auth.login_decay_seconds = 60`, `gotix.auth.register_max_attempts = 5`, `gotix.auth.register_decay_seconds = 60`).
  - Test Suite & QA:
    - Test suite baru `tests/Feature/AuthRateLimitingTest.php` (10 skenario komprehensif, 46 assertions) lulus 100%.
    - Test suite baru `tests/Feature/BusinessConfigurationTest.php` (7 skenario komprehensif, 57 assertions) lulus 100%.
    - Full regression test suite GOTIX (128 passed, 458 assertions) lulus 100% tanpa kegagalan.
    - Standar kode Laravel Pint lulus (`passed`) dan kompilasi aset Vite berhasil.
- **Affected files:**
  - *Baru:* `tests/Feature/AuthRateLimitingTest.php`
  - *Baru:* `tests/Feature/BusinessConfigurationTest.php`
  - `config/gotix.php`
  - `app/Http/Controllers/Auth/MasukController.php`
  - `app/Http/Controllers/Auth/DaftarController.php`
  - `app/Http/Controllers/Member/PembayaranController.php`
  - `app/Http/Controllers/Member/PesananController.php`
  - `app/Http/Controllers/Member/KeluhanController.php`
  - `app/Http/Controllers/Operator/PesananLoketController.php`
  - `app/Http/Controllers/Operator/VerifikasiPembayaranController.php`
  - `app/Http/Controllers/Admin/PesananController.php`
  - `app/Http/Controllers/Admin/KeluhanController.php`
  - `resources/views/auth/masuk.blade.php`
  - `resources/views/auth/daftar.blade.php`
  - `resources/views/layouts/tamu.blade.php`
  - *Dokumentasi:* `docs/24-p0-05-auth-rate-limiting-and-business-config.md`
- **Dependencies:** Tidak ada.
- **Risk level:** **Low**.
- **Database changes required:** **Tidak**.

---

## P1 — Broken Core Functionality and Payment Workflow

### `P1-01` — Perbaikan Modul E-Tiket Member (`Member\TiketController`)
- **Status:** **SELESAI (Phase P1 — 12 November 2026)**
- **Hasil Implementasi:**
  - E-Tiket resmi dibatasi secara ketat hanya untuk pesanan berstatus `StatusPesanan::Lunas`. Pesanan selain lunas (`Belum Bayar`, `Dalam Proses`, `Dibatalkan`) mengembalikan respons `404 Not Found`.
  - Akses e-tiket dilindungi otorisasi kepemilikan member (`pengguna_id === Auth::id()`).
  - Dibuat layanan server terpadu `app/Services/TiketService.php` yang menghasilkan payload digital bertanda tangan kriptografis HMAC-SHA256 (`app.key`) dan merender SVG QR Code dinamis 25x25 (lengkap dengan finder & alignment pattern).
  - Tampilan `resources/views/member/tiket/show.blade.php` dibersihkan 100% dari seluruh data dummy Jakarta–Bandung, diganti dengan data Eloquent nyata (`$pesanan->jadwal`, `$pesanan->pengguna`, barcode SVG & QR Code SVG).
  - Tampilan `resources/views/member/tiket/index.blade.php` diperbarui dengan tombol aksi adaptif: "Lihat E-Tiket" untuk `Lunas`, "Bayar Sekarang" untuk `Belum Bayar`, dan "Detail Pesanan" untuk status lainnya.
- **Affected files:**
  - *Baru:* `app/Services/TiketService.php`
  - `app/Http/Controllers/Member/TiketController.php`
  - `resources/views/member/tiket/index.blade.php`
  - `resources/views/member/tiket/show.blade.php`
  - *Dokumentasi:* `docs/25-p1-core-functionality-and-payment-workflow.md`
- **Dependencies:** `P0-04`.
- **Risk level:** **Medium**.
- **Database changes required:** **Tidak**.

### `P1-02` — Penyambungan Modul Keluhan Member (Database CRUD Riil)
- **Status:** **SELESAI TAHAP P1 (Menunggu Migrasi DB untuk `TEXT` & `Armada`)**
- **Hasil Implementasi:**
  - Menghapus total mock PHP array pada `resources/views/member/keluhan/index.blade.php` dan menggantinya dengan iterasi Eloquent `$daftarKeluhan` nyata (lengkap dengan status, balasan admin, dan nama admin yang merespons).
  - Menghapus event inline `onsubmit="alert()"` pada `resources/views/member/keluhan/create.blade.php`, mengarahkan form POST nyata ke `route('member.keluhan.store')`.
  - Dropdown tiket keluhan memuat pesanan nyata milik member yang sedang login; dropdown kategori memuat enum `KategoriKeluhan`.
  - `Member\KeluhanController` memvalidasi kategori, kepemilikan pesanan, dan panjang teks deskripsi (`min:10, max:200`) sesuai kapasitas kolom database aktif.
  - Skenario pengujian terotomasi Pest memvalidasi pengiriman keluhan member dan balasan admin.
- **Affected files:**
  - `app/Http/Controllers/Member/KeluhanController.php`
  - `resources/views/member/keluhan/index.blade.php`
  - `resources/views/member/keluhan/create.blade.php`
  - *Dokumentasi:* `docs/25-p1-core-functionality-and-payment-workflow.md`
- **Dependencies:** `P0`.
- **Database changes required:** **Pending Persetujuan Migrasi** (`keluhan.deskripsi` ke `TEXT` dan penambahan enum `Armada`).

### `P1-03` — Implementasi Nyata Pemesanan Walk-in Loket (`Operator\PesananLoketController`)
- **Status:** **SELESAI TAHAP P1 (Menunggu Migrasi DB untuk Walk-in Tanpa Akun)**
- **Hasil Implementasi:**
  - Frontend loket `resources/views/operator/loket/index.blade.php` dibersihkan dari rute/kursi Alpine dummy, digantikan dengan data jadwal aktif masa depan dari database dan relasi kendaraan/kursi riil.
  - Grid pemilihan kursi bersifat dinamis sesuai kapasitas kendaraan (`kapasitas_kursi`), menampilkan status kursi terisi (merah) dan kursi tersedia (hijau).
  - Pemesanan tiket loket dilindungi pessimistic locking (`lockForUpdate()`), transaksi database, penanganan konkurensi duplicate entry, dan validasi kapasitas dinamis armada.
  - Pesanan loket langsung diterbitkan berstatus `StatusPesanan::Lunas` tanpa bukti transfer manual.
  - `operator.loket.create` dialihkan secara mulus ke antarmuka utama loket `operator.loket.index`.
- **Affected files:**
  - `app/Http/Controllers/Operator/PesananLoketController.php`
  - `resources/views/operator/loket/index.blade.php`
  - `resources/views/operator/loket/create.blade.php`
  - *Dokumentasi:* `docs/25-p1-core-functionality-and-payment-workflow.md`
- **Dependencies:** `P0-01`.
- **Database changes required:** **Pending Persetujuan Migrasi** (`pesanan.pengguna_id` nullable, `nama_penumpang`, `telepon_penumpang`, `dibuat_oleh_operator_id`).

### `P1-04` — Penegakan Kedaluwarsa Pesanan & Filter Pencarian Jadwal Aktif
- **Status:** **SELESAI (Phase P0-04 & P1 — 12 November 2026)**
- **Hasil Implementasi:**
  - Command Artisan `php artisan gotix:cancel-expired-orders` berjalan terjadwal tiap jam, membatalkan pesanan `Belum Bayar` kedaluwarsa, dan melepaskan kursi aktif secara idempoten.
  - `Publik\CariJadwalController::index` diperbarui untuk otomatis memfilter jadwal masa lalu (`whereDate('tanggal_berangkat', '>=', now()->toDateString())`) saat pengguna tidak memasukkan tanggal pencarian spesifik.
  - Skenario pengujian Pest memverifikasi jadwal kemarin tidak pernah muncul pada pencarian publik tanpa filter tanggal.
- **Affected files:**
  - `app/Console/Commands/BatalkanPesananKedaluwarsa.php`
  - `routes/console.php`
  - `app/Http/Controllers/Publik/CariJadwalController.php`
  - *Dokumentasi:* `docs/20-phase-4-payment-state-guard-and-verification.md`, `docs/25-p1-core-functionality-and-payment-workflow.md`
- **Dependencies:** `P0-01`, `P0-04`.
- **Database changes required:** **Tidak**.

### `P1-05` — Detail Jadwal Admin & Halaman Konfirmasi Pembayaran Member
- **Status:** **SELESAI (Phase P0-04 & P1 — 12 November 2026)**
- **Hasil Implementasi:**
  - Placeholder mentah `[PLACEHOLDER] Admin — Detail Jadwal` pada `resources/views/admin/jadwal/show.blade.php` diganti 100% dengan halaman detail jadwal admin terpadu: kartu ringkasan rute, 5 metrik cards operasional (Kapasitas, Terisi, Tersedia, Okupansi %, Estimasi Pendapatan), visual denah keterisian kursi (seat occupancy grid), serta tabel riwayat transaksi tiket terkait dengan tautan langsung ke detail pesanan admin.
  - Halaman konfirmasi pembayaran pasca-unggah bukti transfer telah diimplementasikan penuh pada Phase P0-04 (`resources/views/member/pembayaran/konfirmasi.blade.php`) dengan kartu status peninjauan dan panduan verifikasi.
- **Affected files:**
  - `resources/views/admin/jadwal/show.blade.php`
  - `resources/views/member/pembayaran/konfirmasi.blade.php`
  - *Dokumentasi:* `docs/25-p1-core-functionality-and-payment-workflow.md`
- **Dependencies:** `P0-04`.
- **Database changes required:** **Tidak**.

### `P1-06` — Sinkronisasi Copywriting Domain Lampung, Perbaikan CTA Role, & Typo Admin Dasbor
- **Status:** **IN REVIEW / NEXT SUB-TASK**
- **Description:** Membersihkan karakter rusak UTF-8 serta teks rute Pulau Jawa pada view publik, memperbaiki CTA `@auth` di pencarian jadwal agar tidak memicu `403` untuk Admin/Operator, dan memperbaiki typo `$menanggguVerifikasi`.
- **Affected files:**
  - `resources/views/publik/beranda.blade.php`
  - `resources/views/publik/cari-jadwal.blade.php`
  - `app/Http/Controllers/Admin/DasborController.php` & `resources/views/admin/dasbor.blade.php`
- **Dependencies:** Tidak ada.
- **Risk level:** **Low**.
- **Database changes required:** **Tidak**.
  - *Baru:* `app/Console/Commands/BatalkanPesananKedaluwarsa.php`
  - `routes/console.php`
  - `app/Http/Controllers/Publik/CariJadwalController.php`
  - `app/Http/Controllers/Member/PesananController.php`
  - `app/Http/Controllers/Admin/JadwalController.php` (validasi `after_or_equal:today` pada `store()`)
  - `resources/views/publik/cari-jadwal.blade.php`
- **Dependencies:** `P0-01` (agar pesanan `Dibatalkan` otomatis membebaskan kursi di DB).
- **Risk level:** **Medium**.
- **Implementation proposal:** Command Artisan mengubah pesanan `Belum Bayar` dengan `batas_bayar < now()` menjadi `Dibatalkan`. Filter `CariJadwalController::index()` agar hanya menampilkan jadwal `tanggal_berangkat >= today()` dan jadwal belum penuh pada rekomendasi, serta cek `$jadwal->tanggal_berangkat >= today()` di `PesananController::store()`.
- **Required tests:** Running command membatalkan hanya pesanan `Belum Bayar` yang expired (tidak menyentuh `Dalam Proses`/`Lunas`); kursi menjadi tersedia kembali; pencarian publik tidak mengembalikan jadwal lampau.
- **Acceptance criteria:** Kursi pesanan kedaluwarsa dilepas otomatis dan jadwal masa lalu tidak lagi dapat dipesan.
- **Database changes required:** **Tidak**.

### `P1-05` — Penyelesaian Placeholder Detail Jadwal Admin & Halaman Konfirmasi Pembayaran Member
- **Description:** Mengimplementasikan `admin/jadwal/show.blade.php` (manifest penumpang & keterisian kursi per jadwal) serta menjadikan `member/pembayaran/konfirmasi.blade.php` halaman status/konfirmasi khusus pasca-unggah bukti transfer.
- **Affected files:**
  - `resources/views/admin/jadwal/show.blade.php`
  - `app/Http/Controllers/Member/PembayaranController.php` & `resources/views/member/pembayaran/konfirmasi.blade.php`
- **Dependencies:** `P0-04`. **[Keputusan Disetujui]:** Gunakan halaman konfirmasi/status khusus pasca-unggah bukti pembayaran (`member.pembayaran.konfirmasi`).
- **Risk level:** **Low**.
- **Implementation proposal:** Tampilkan detail rute, kendaraan, daftar kursi terisi, dan daftar penumpang pada `admin/jadwal/show.blade.php`. Ubah redirect akhir `PembayaranController::konfirmasiStore()` menuju `route('member.pembayaran.konfirmasi', $kode)` dan implementasikan `member/pembayaran/konfirmasi.blade.php` dengan status verifikasi (`Dalam Proses`), detail resi/rekening pengirim, dan tautan ke `member.pesanan.show`.
- **Required tests:** Akses `admin.jadwal.show` merender manifest jadwal real; unggah bukti bayar mengarahkan ke `member.pembayaran.konfirmasi` dengan data nyata; tidak ada route aktif yang mengembalikan `[PLACEHOLDER]`.
- **Acceptance criteria:** Zero placeholder Blade pages di seluruh route terdaftar.
- **Database changes required:** **Tidak**.

### `P1-06` — Sinkronisasi Copywriting Domain Lampung, Perbaikan CTA Role, & Typo Admin Dasbor
- **Description:** Membersihkan karakter rusak UTF-8 serta teks rute Pulau Jawa pada view publik, memperbaiki CTA `@auth` di pencarian jadwal agar tidak memicu `403` untuk Admin/Operator, dan memperbaiki typo `$menanggguVerifikasi`.
- **Affected files:**
  - `resources/views/publik/beranda.blade.php`
  - `resources/views/publik/cari-jadwal.blade.php`
  - `app/Http/Controllers/Admin/DasborController.php` & `resources/views/admin/dasbor.blade.php`
- **Dependencies:** Tidak ada.
- **Risk level:** **Low**.
- **Implementation proposal:** Pastikan CTA booking hanya diarahkan ke `member.pesanan.create` bila `$user?->isMember()` (tampilkan link dasbor/loket untuk Admin/Operator); perbaiki teks ke domain Lampung; rapikan `$menungguVerifikasi`.
- **Required tests:** Render halaman publik tanpa error; login Admin/Operator melihat CTA sesuai peran dan tidak diarahkan ke route Member `403`.
- **Acceptance criteria:** Seluruh konten publik konsisten dengan domain Lampung dan bebas broken encoding.
- **Database changes required:** **Tidak**.

---

## P2 — Frontend Build Consistency and Shared Architecture

### `P2-01` — Standardisasi Build CSS Tailwind v3.4.19 (Versi Tunggal Tanpa Konflik)
- **Description:** Mempertahankan **Tailwind CSS `3.4.19`** sesuai keputusan pemilik proyek, membersihkan paket `@tailwindcss/vite` yang tidak dipakai, dan mengganti 49 utilitas Tailwind v4 yang terabaikan di Blade agar 100% kelas terkompilasi ke `public/build`.
- **Affected files:**
  - `package.json`, `package-lock.json`
  - Seluruh view yang masih memakai utilitas v4 tak terkompilasi (`shadow-xs`, `shadow-2xs`, `w-4.5`, `h-4.5`)
- **Dependencies:** **[Keputusan Disetujui]:** Pertahankan Tailwind CSS `3.4.19`.
- **Risk level:** **Low**.
- **Implementation proposal:** Hapus dependensi `@tailwindcss/vite` saat tahap `P2-01`, pertahankan `vite.config.js` + `postcss.config.js` + `tailwind.config.js` (`@tailwindcss/forms`), lalu konversi 49 kelas v4 (`shadow-xs`/`shadow-2xs` -> `shadow-sm`, `w-4.5`/`h-4.5` -> `w-[18px]`/`h-[18px]` atau `w-4`/`h-4`).
- **Required tests:** `npm run build` sukses dan inspeksi CSS output memastikan kelas pengganti terbentuk penuh.
- **Acceptance criteria:** Satu versi Tailwind (`3.4.19`) aktif tanpa paket konflik; nol utility class mati.
- **Database changes required:** **Tidak**.

### `P2-02` — Konsolidasi Arsitektur Layout 3 Peran & Penghapusan File Mati
- **Description:** Memisahkan shell publik (`layouts.tamu` / `layouts.publik`) dari shell aplikasi terautentikasi, dan memigrasikan Operator & Member dari layout tamu menuju arsitektur shell yang konsisten (termasuk menghapus splash screen 850ms pada halaman operasional dan file yatim seperti `welcome.blade.php`, `admin/_nav.blade.php`, serta `layouts/operator.blade.php` yang tidak aktif setelah diganti shell baru).
- **Affected files:**
  - `resources/views/layouts/*`, `resources/views/member/_nav.blade.php`, `resources/views/operator/_nav.blade.php`, `resources/views/admin/_nav.blade.php`, `resources/views/welcome.blade.php`
- **Dependencies:** `P2-01`, persetujuan arsitektur Design System (`docs/13-design-system-preparation.md`).
- **Risk level:** **Medium**.
- **Implementation proposal:** Terapkan pola 2 shell dasar: shell Publik/Auth dan shell Portal Terautentikasi berbasis peran (`Member`, `Operator`, `Admin`), pastikan sub-halaman detail (`show`/`create`) tetap memiliki navigasi konsisten.
- **Required tests:** Cek seluruh halaman 3 peran merender layout tanpa error Blade dan navigasi antar-menu tiap peran tetap utuh.
- **Acceptance criteria:** Setiap peran memiliki navigasi persisten di seluruh halaman tanpa file layout yatim.
- **Database changes required:** **Tidak**.

### `P2-03` — Aksesibilitas Form, Label, Modal Konfirmasi & Eliminasi CDN Berat
- **Description:** Menambahkan `for`/`id` pada seluruh form input, `aria-label` pada tombol ikon, mengganti `alert()`/`confirm()` native dengan modal Alpine konsisten, dan mengevaluasi penghapusan CDN eksternal `unpkg.com` AOS agar halaman tidak bergantung koneksi pihak ketiga.
- **Affected files:** Seluruh form `admin/*`, `member/*`, `operator/*`, `layouts/tamu.blade.php`.
- **Dependencies:** `P2-01`, `P2-02`.
- **Risk level:** **Low**.
- **Implementation proposal:** Standarisasi struktur form dan komponen modal konfirmasi hapus/tolak.
- **Required tests:** Audit keyboard/tab focus & form submission pada seluruh form.
- **Acceptance criteria:** Semua input memiliki label terikat, tidak ada dialog browser native `confirm()`/`alert()`.
- **Database changes required:** **Tidak**.

### `P2-04` — Manajemen Operator oleh Admin (CRUD + Aktivasi/Nonaktivasi)
- **Description:** Memperluas `Admin\PenggunaController` (atau controller Operator khusus) untuk mendukung **CRUD akun Operator beserta fitur aktivasi & penonaktifan**, sesuai keputusan pemilik proyek.
- **Affected files:**
  - *Baru:* `database/migrations/YYYY_MM_DD_XXXXXX_add_aktif_to_pengguna_table.php`
  - `app/Models/Pengguna.php`, `app/Http/Controllers/Auth/MasukController.php` (tolak login akun nonaktif)
  - `app/Http/Controllers/Admin/PenggunaController.php`, `routes/web.php`, `resources/views/admin/pengguna/*`
- **Dependencies:** `P0-02`. **[Keputusan Disetujui]:** Dukung CRUD Operator termasuk aktivasi dan penonaktifan.
- **Risk level:** **Medium**.
- **Implementation proposal:** Tambah kolom boolean `aktif` (default `true`) pada `pengguna`; tambahkan endpoint `create`/`store`/`edit`/`update`/toggle-aktif untuk Operator; pastikan login (`MasukController`) menolak pengguna dengan `aktif = false`; cegah Admin menonaktifkan dirinya sendiri.
- **Required tests:** Admin dapat membuat, mengubah, menonaktifkan, dan mengaktifkan kembali akun Operator; Operator nonaktif ditolak login; Member tetap menerima `403`.
- **Acceptance criteria:** Pengelolaan staf Operator (termasuk aktivasi/nonaktivasi) berjalan penuh di UI Admin tanpa bergantung seeder.
- **Database changes required:** **Ya** (kolom boolean `aktif` pada `pengguna`).

---

## P3 — Code Maintainability and Reusable Components

### `P3-01` — Ekstraksi Service & Action Logika Bisnis Inti (`KetersediaanKursiService`, `Pesanan`, `Pembayaran`)
- **Description:** Menghilangkan duplikasi kalkulasi sisa kursi, pembuatan pesanan, dan verifikasi pembayaran lintas `Publik`, `Member`, `Operator`, dan `Admin`.
- **Affected files:**
  - *Baru:* `app/Services/KetersediaanKursiService.php`, `app/Actions/Pesanan/*`, `app/Support/Format.php`
  - Refactor `BerandaController`, `CariJadwalController`, `Member\{Pesanan,Pembayaran}Controller`, `Operator\{PesananLoket,VerifikasiPembayaran}Controller`, `Admin\PesananController`, `Models\{Jadwal,Pesanan}`
- **Dependencies:** `P0-01` s.d. `P1-04`.
- **Risk level:** **Medium**.
- **Implementation proposal:** Satukan query kursi terisi/tersisa, generator kode pesanan, dan state transitions dalam Service/Action teruji; aktifkan `Model::preventLazyLoading(! app()->isProduction())` di `AppServiceProvider`.
- **Required tests:** Unit test `KetersediaanKursiService` & `Format`, serta seluruh Feature test tetap hijau tanpa `LazyLoadingViolationException`.
- **Acceptance criteria:** Tidak ada query N+1 dan nol duplikasi logika booking/pembayaran di controller.
- **Database changes required:** **Tidak**.

### `P3-02` — Konsolidasi FormRequest & Laravel Policies
- **Description:** Mengganti inline `$request->validate()` di controller fitur dengan `FormRequest` khusus dan memperkenalkan `PesananPolicy` / `KeluhanPolicy` untuk otorisasi resource eksplisit.
- **Affected files:** `app/Http/Requests/{Member,Operator,Admin}/*`, `app/Policies/{PesananPolicy,KeluhanPolicy}.php`, controllers terkait.
- **Dependencies:** `P3-01`.
- **Risk level:** **Low**.
- **Implementation proposal:** Pindahkan aturan validasi ke Request classes dan registrasikan policy resource.
- **Required tests:** Test otorisasi lintas user dan validation failure responses.
- **Acceptance criteria:** Controller ramping (<30 baris per action) dan otorisasi terpusat.
- **Database changes required:** **Tidak**.

### `P3-03` — Ekstraksi Blade UI Components & Form Partials (`_form.blade.php`)
- **Description:** Membangun pustaka komponen Blade di `resources/views/components/` (tombol, input, select, badge status, kartu, modal, tabel, empty state) serta menyatukan form `create`/`edit` Admin via `_form.blade.php`.
- **Affected files:** `resources/views/components/*`, `resources/views/admin/{kota,kendaraan,jadwal}/_form.blade.php`, refactor view lain.
- **Dependencies:** `P2-01`, `P2-02`, spesifikasi token Design System (`P4-01`).
- **Risk level:** **Low-Medium**.
- **Implementation proposal:** Hilangkan 8+ duplikasi switch badge status dan duplikasi markup form CRUD.
- **Required tests:** Render test / feature test semua halaman CRUD & daftar.
- **Acceptance criteria:** Perubahan token atau style komponen cukup dilakukan di satu file komponen.
- **Database changes required:** **Tidak**.

### `P3-04` — Konfigurasi QA (`phpstan.neon`), Pint, & Perluasan Regresi Test Suite
- **Description:** Mengisi `phpstan.neon` (saat ini 0 byte) dengan ekstensi Larastan, memverifikasi gaya kode via `vendor/bin/pint --test`, dan menulis Feature Test Pest penuh untuk seluruh alur GOTIX di `gotix_testing`.
- **Affected files:** `phpstan.neon`, *opsional* `pint.json`, `tests/Feature/{Booking,Pembayaran,Verifikasi,Tiket,Keluhan,Loket,AdminCrud}Test.php`.
- **Dependencies:** `P0` s.d. `P3-03`.
- **Risk level:** **Low**.
- **Implementation proposal:** Targetkan PHPStan minimal Level 5 pada `app/` dan 100% happy-path + failure-path test coverage pada alur transaksi utama.
- **Required tests:** `php artisan test`, `vendor/bin/phpstan analyse`, `vendor/bin/pint --test` semuanya lulus bersih di lingkungan standar/CI.
- **Acceptance criteria:** Seluruh suite QA hijau sebagai pengaman sebelum dan sesudah redesign UI.
- **Database changes required:** **Tidak**.

---

## P4 — UI/UX Redesign (Unified 3-Role Design System)

### `P4-01` — Definisi Design Tokens & Panduan Visual (Setelah Approval & Referensi Desain)
- **Description:** Menetapkan palet warna final, skala tipografi, radius, elevasi, spacing, dan aturan responsivitas sesuai referensi visual yang disetujui pemilik proyek (`docs/13-design-system-preparation.md`).
- **Dependencies:** `P2-01`, `P3-03`, referensi desain & keputusan owner.
- **Risk level:** **Low**.

### `P4-02` — Redesign Alur Publik, Auth & Customer / Member
- **Description:** Mengimplementasikan desain baru pada Beranda, Cari Jadwal, Masuk/Daftar, Pesanan, Pilih Kursi dinamis, Pembayaran, E-Tiket cetak/digital, dan Keluhan menggunakan komponen Design System bersama.
- **Dependencies:** `P1-01`, `P1-02`, `P1-06`, `P4-01`.
- **Risk level:** **Medium**.

### `P4-03` — Redesign Alur Operator Loket
- **Description:** Mengimplementasikan shell operasional Operator yang fokus kecepatan transaksi: antrean verifikasi bukti bayar & kasir loket walk-in dengan denah kursi dinamis dan struk cetak asli.
- **Dependencies:** `P0-04`, `P1-03`, `P4-01`.
- **Risk level:** **Medium**.

### `P4-04` — Redesign Alur Admin Backoffice & Verifikasi Akhir
- **Description:** Menyelaraskan Dasbor Admin, CRUD Kota/Kendaraan/Jadwal (termasuk detail manifest), Pengguna, Monitoring Pesanan, dan Keluhan ke Design System terpadu, lalu menjalankan regresi penuh (`pest`, `phpstan`, `pint`, `npm run build`).
- **Dependencies:** `P1-05`, `P2-04`, `P4-01`.
- **Risk level:** **Low-Medium**.

