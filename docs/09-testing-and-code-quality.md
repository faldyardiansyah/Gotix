# 09 — Testing and Code Quality Audit

## 1. Inventaris Suite Pengujian (`tests/`)

- **VERIFIED:** Proyek dikonfigurasi memakai **Pest PHP** (`pestphp/pest v3.8.7` + `pestphp/pest-plugin-laravel v3.2.0`) dengan total **34 test cases** di 4 file test:

| File | Jumlah Test Case | Cakupan Terverifikasi dari Kode | Ketergantungan DB / Trait |
|---|---|---|---|
| `tests/Feature/AuthTest.php` | 18 | Registrasi, validasi register, login email/username, logout, middleware role (`Admin`, `Operator`, `Member`), redirect guest & autentikasi | `RefreshDatabase` (MySQL `gotix_testing`) |
| `tests/Feature/ModelTest.php` | 14 | Relasi Eloquent (7 test), unique constraint `(jadwal_id, nomor_kursi)` (3 test), Enum cast `PeranPengguna`/`StatusPesanan`, hash password, helper `isAdmin`/`isOperator`/`isMember` | `RefreshDatabase` (MySQL `gotix_testing`) |
| `tests/Feature/ExampleTest.php` | 1 | `GET /` mengembalikan 200 | Memuat view & DB (`BerandaController` mengakses `jadwal` & `kota`) |
| `tests/Unit/ExampleTest.php` | 1 | `expect(true)->toBeTrue()` | Tanpa DB |

- **NOT IMPLEMENTED (Kesenjangan Cakupan Uji):**
  - Tidak ada feature/integration test untuk alur pemesanan kursi member (`Member\PesananController`, skenario kursi ganda/konkurensi, atau kursi bekas batal).
  - Tidak ada test untuk upload & konfirmasi pembayaran (`Member\PembayaranController`), verifikasi/tolak pembayaran (`Operator` & `Admin`), loket operator (`PesananLoketController`), e-tiket (`TiketController`), CRUD master Admin (`Kota`, `Kendaraan`, `Jadwal`), maupun modul Keluhan.

---

## 2. Konfigurasi Pengujian (`phpunit.xml` & `.env.testing`)

- **VERIFIED (`phpunit.xml` L21-38, `.env.testing` L1-9):**
  - Suite `Unit` -> `tests/Unit`; Suite `Feature` -> `tests/Feature`.
  - Konfigurasi DB testing mengarah ke **MySQL** (`DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=gotix_testing`, `DB_USERNAME=root`, `DB_PASSWORD=""`).
  - `SESSION_DRIVER=array`, `CACHE_STORE=array`, `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`.
- **PERINGATAN KONFIGURASI:**
  - `tests/Pest.php` menerapkan `RefreshDatabase` di `tests/Feature`. Karena `phpunit.xml` dan `.env.testing` menargetkan `DB_DATABASE=gotix_testing` (terpisah dari database aplikasi utama), eksekusi `php artisan test` secara teori mengosongkan/memigrasi database `gotix_testing`, **namun tetap tidak dijalankan secara destruktif atau di-bypass** selama audit.

---

## 3. Konfigurasi Code Quality (PHPStan/Larastan & Laravel Pint)

- **VERIFIED (`phpstan.neon`):**
  - File `phpstan.neon` di root repositori **kosong (0 byte)** (`NOT IMPLEMENTED` / belum dikonfigurasi). Larastan `v3.14.0` dan PHPStan `2.3.5` sudah terpasang di `vendor/`, beserta beberapa anotasi `// @phpstan-ignore-line` pada controller/middleware (`MasukController.php` L51, `PastikanPunyaPeran.php` L25), yang menandakan Larastan pernah dipakai secara manual atau disiapkan tetapi konfigurasinya hilang/dikosongkan.
- **VERIFIED (Laravel Pint `v1.30.4`):**
  - Tidak terdapat `pint.json` kustom; Pint berjalan dengan preset standar `laravel`.

---

## 4. Hasil Verifikasi Eksekusi Aktual Saat Audit

> **Kejujuran Pelaporan:** Sesuai kondisi lingkungan sandbox Windows saat audit dijalankan, perintah pengujian dan static analysis menunjukkan hasil aktual sebagai berikut — **tidak ada klaim lulus test/lint yang dibuat di luar output yang benar-benar teramati**:

1. **Pest / PHPUnit (`php artisan test`):**
   - **Output aktual:** `INFO Cannot open bootstrap script "D:\SEMESTER 5\Pemeliharaan Perangkat Lunak\Gotix\vendor/autoload.php".` (exit code `2`).
   - **Penyebab terverifikasi:** Pada environment sandbox Windows, fungsi PHP `is_readable()` mengembalikan `false` untuk file di direktori kerja (meskipun `is_file()` bernilai `true` dan `require` berhasil). Akibatnya PHPUnit/Pest gagal membuka `bootstrap="vendor/autoload.php"`.
   - **Status eksekusi suite (34 tests):** `UNKNOWN` — tidak dapat dieksekusi di sandbox tanpa akses filesystem standar; perlu dijalankan ulang langsung di terminal lokal pengembang terhadap database `gotix_testing`.

2. **PHPStan / Larastan (`php vendor/bin/phpstan analyse`):**
   - **Output aktual:** `Note: Using configuration file D:\SEMESTER 5\Pemeliharaan Perangkat Lunak\Gotix\phpstan.neon.` -> `File 'D:\SEMESTER 5\Pemeliharaan Perangkat Lunak\Gotix\phpstan.neon' is missing or is not readable.` (exit code `1`).
   - **Penyebab terverifikasi:** Kombinasi dua hal: `phpstan.neon` memang kosong (0 byte) dan kendala `is_readable()` sandbox Windows.
   - **Status analisis statis:** `UNKNOWN` (belum dapat dijalankan; konfigurasi kosong).

3. **Laravel Pint (`php vendor/bin/pint --test`):**
   - **Output aktual:** Mencetak error `In FileHandler.php line 153: Cannot write to file "C:\Users\LOQ\AppData\Local\Temp\054aa4855be4e89f17f65599c97a9350" as it is not writable.` (2 kali), lalu keluar dengan exit code `0`.
   - **Status formatting:** `UNKNOWN` — Pint gagal menulis file temp cache di `AppData\Local\Temp` (di luar workspace sandbox) sehingga hasil pengecekan gaya kode tidak dapat dianggap terverifikasi penuh.

---

## 5. Rekomendasi Peningkatan Kualitas & Testing

1. **Lengkapi `phpstan.neon` (Medium):** Konfigurasi minimal `level: 5`, `includes: [vendor/larastan/larastan/extension.neon]`, `paths: [app]`. Ini akan langsung menangkap properti salah seperti `nama_pengirim` vs `rekening_pengirim` atau return type mismatch.
2. **Tambah Feature Test Alur Bisnis Inti (High):** Tambahkan test Pest terisolasi (pada `gotix_testing`) untuk: booking kursi, penolakan kursi ganda, booking ulang kursi `Dibatalkan`, upload konfirmasi bayar & batas kadaluarsa, verifikasi/tolak oleh Operator & Admin, akses e-tiket hanya saat `Lunas`, serta pembuatan & balasan keluhan.
3. **Tambahkan `pint.json` & CI Script (Low):** Dokumentasikan aturan gaya kode dan jalankan `pint --test`, `phpstan`, dan `pest` secara lokal di luar batasan sandbox sebelum memulai redesign Phase 2.

