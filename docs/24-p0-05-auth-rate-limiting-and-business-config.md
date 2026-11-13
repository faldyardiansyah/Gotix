# 24 — P0-05 Authentication Rate Limiting & Business Configuration Consistency Report

> **Tujuan Dokumen:** Laporan Resmi Implementasi Perlindungan Rate Limiting Autentikasi dan Standardisasi Pembacaan Konfigurasi Bisnis  
> **Ruang Lingkup:** `P0-05` (Fase P0 Terakhir)  
> **Tanggal Implementasi:** 12 November 2026  
> **Status Akhir:** **FINALIZED / SELESAI PENUH**

---

## 1. Kondisi Awal yang Ditemukan

Sebelum implementasi P0-05 dilakukan, audit terarah mengidentifikasi kondisi awal sistem:

1. **Endpoint Autentikasi Tanpa Perlindungan Brute-Force:**
   - `MasukController::store` memproses kredensial username/email dan kata sandi tanpa batasan frekuensi percobaan (*unthrottled login*).
   - `DaftarController::store` memproses pendaftaran member baru tanpa batas laju per alamat IP, membuka celah untuk serangan pembuatan akun massal (*automated bot spam*).
   - Tidak ada forgot/reset password endpoint di dalam aplikasi GOTIX (hanya terdapat `/masuk`, `/daftar`, dan `/keluar`).

2. **Inkonsistensi Pembacaan Konfigurasi Bisnis:**
   - Berkas `config/gotix.php` hanya mendefinisikan sebagian nilai dasar (`batas_bayar_jam`, `kapasitas_kursi`, dan `bank`), namun penamaan key-nya tidak selaras dengan penggunaan di view:
     - `resources/views/layouts/tamu.blade.php` membaca `config('gotix.bank_name')`, `config('gotix.bank_account')`, dan `config('gotix.bank_holder')`.
     - `resources/views/member/pembayaran/show.blade.php` membaca `config('gotix.bank.nama')`, `config('gotix.bank.nomor')`, dan `config('gotix.bank.atas_nama')`.
   - Sejumlah batasan aturan bisnis ditulis secara *hardcoded* di berbagai controller:
     - Batas waktu pembayaran loket ditulis `now()->addHours(1)` di `Operator\PesananLoketController`.
     - Nilai fallback kapasitas kursi armada ditulis `?? 5` di `Member\PesananController` dan `Operator\PesananLoketController` alih-alih membaca `config('gotix.kapasitas_kursi')`.
     - Batas ukuran unggah struk transfer (`max:5120`) dan tipe MIME (`mimes:jpg,jpeg,png,webp,pdf`) ditulis langsung di `Member\PembayaranController`.
     - Batas karakter pengaduan keluhan (`min:10, max:1000` untuk member dan `min:3, max:1000` untuk admin) serta batas alasan penolakan (`max:1000`) ditulis berulang di masing-masing controller.

---

## 2. Route Autentikasi yang Dilindungi

Berdasarkan inventaris rute aktual pada `routes/web.php`, endpoint yang dilindungi adalah:

| Metode | URI | Nama Route | Controller Action | Tipe Perlindungan |
|---|---|---|---|---|
| `POST` | `/masuk` | `masuk.store` | `App\Http\Controllers\Auth\MasukController@store` | Rate limiting berbasis kombinasi identitas dinormalisasi + alamat IP |
| `POST` | `/daftar` | `daftar.store` | `App\Http\Controllers\Auth\DaftarController@store` | Rate limiting berbasis alamat IP |

*Catatan: Route publik `GET /masuk` dan `GET /daftar` tetap dapat diakses tanpa hambatan.*

---

## 3. Kebijakan Rate Limiting yang Diterapkan

### A. Kebijakan Login (`MasukController`)
- **Ambang Batas Maksimal:** 5 percobaan login gagal per identitas + IP.
- **Periode Waktu (*Decay*):** 60 detik (1 menit).
- **Mekanisme Pencatatan (*Hit Counter*):**
  - Hanya bertambah (*hit*) jika autentikasi `Auth::attempt` **gagal** (kredensial salah).
  - Jika login **berhasil**, seluruh hit counter untuk kombinasi identitas dan IP tersebut **dibersihkan seketika** (`RateLimiter::clear`).
- **Pencegahan Double Counting:** Rate limiter dikelola langsung secara presisi di controller, menghindari middleware throttle ganda yang dapat memblokir request valid.
- **Respons HTTP saat Terblokir:**
  - Kode Status: **HTTP 429 Too Many Requests**.
  - Header: `Retry-After: <detik_tersisa>`.
  - Pesan Pengguna: `"Terlalu banyak percobaan masuk yang gagal. Silakan coba lagi dalam X detik."`
  - Tampilan Web: Menampilkan kartu peringatan merah terpadu pada view `auth.masuk` tanpa mengunci akun secara permanen.
  - JSON / API: Menghasilkan struktur error standar dengan kode 429.

### B. Kebijakan Registrasi (`DaftarController`)
- **Ambang Batas Maksimal:** 5 percobaan registrasi per alamat IP.
- **Periode Waktu (*Decay*):** 60 detik (1 menit).
- **Respons HTTP saat Terblokir:**
  - Kode Status: **HTTP 429 Too Many Requests**.
  - Header: `Retry-After: <detik_tersisa>`.
  - Pesan Pengguna: `"Terlalu banyak percobaan pendaftaran. Silakan coba lagi dalam X detik."`
  - Tampilan Web: Menampilkan kartu peringatan merah terpadu pada view `auth.daftar`.

---

## 4. Mekanisme Normalisasi Identitas

Untuk mencegah penyerang melewati pembatasan kuota melalui variasi penulisan email atau username, diterapkan normalisasi konsisten:

```php
protected function throttleKey(string $login, MasukRequest $request): string
{
    $normalized = Str::lower(trim($login));

    return 'login:'.$normalized.'|'.$request->ip();
}
```

- **Pembersihan Whitespace:** Menggunakan `trim()` untuk membuang spasi di awal/akhir input.
- **Case-Insensitive:** Menggunakan `Str::lower()` sehingga `USER@GOTIX.TEST`, `User@Gotix.Test`, dan `user@gotix.test` dihitung ke dalam kuota identitas yang sama.
- **Pemisahan Konteks IP:** Dua alamat IP berbeda yang mencoba login ke akun yang sama memiliki *bucket* kuota terpisah, sehingga penyerang di luar jaringan tidak dapat mengunci akun korban secara sengaja (*Denial-of-Service prevention*).
- **Anti-Account Enumeration:** Pesan kesalahan untuk username yang ada dan yang tidak ada tetap sama persis (`'Kombinasi username/email dan kata sandi tidak cocok.'`), tidak membocorkan keberadaan akun di sistem.
- **Keamanan Data Log:** Tidak ada pencatatan kata sandi, token, atau informasi sensitif ke dalam file log aplikasi.

---

## 5. Sumber Konfigurasi Bisnis yang Digunakan

Seluruh aturan bisnis dipusatkan pada satu berkas konfigurasi tunggal:
`config/gotix.php`

Nilai dibaca menggunakan fungsi helper resmi Laravel `config('gotix.*')` dengan nilai fallback aman bertipe data terdefinisi. Tidak ada pemanggilan langsung `env()` pada lapisan controller, model, atau view.

---

## 6. Daftar Key Konfigurasi dan Penggunaannya

| Key Konfigurasi | Tipe Data | Nilai Default | Penggunaan di Aplikasi |
|---|---|---|---|
| `gotix.batas_bayar_jam` | `int` | `24` | Durasi batas pembayaran online member (`Member\PesananController`) |
| `gotix.batas_bayar_loket_jam` | `int` | `1` | Durasi batas pembayaran tiket walk-in loket (`Operator\PesananLoketController`) |
| `gotix.kapasitas_kursi` | `int` | `5` | Fallback kapasitas kursi kendaraan (`PesananController`, `PesananLoketController`) |
| `gotix.bank.nama` | `string` | `'Bank Central Asia (BCA)'` | Nama bank tujuan transfer resmi Gotix (`tamu.blade.php`, `pembayaran/show.blade.php`) |
| `gotix.bank.rekening` / `.nomor` | `string` | `'8291039182'` | Nomor rekening resmi Gotix |
| `gotix.bank.pemilik` / `.atas_nama` | `string` | `'PT Gotix Transportasi Indonesia'` | Nama pemilik rekening resmi Gotix |
| `gotix.pembayaran.max_file_kb` | `int` | `5120` | Batas maksimum ukuran unggahan berkas struk transfer (`Member\PembayaranController`) |
| `gotix.pembayaran.mimes` | `array` | `['jpg','jpeg','png','webp','pdf']` | Ekstensi berkas struk pembayaran yang diizinkan (`Member\PembayaranController`) |
| `gotix.keluhan.deskripsi_min` | `int` | `10` | Batas minimum karakter keluhan penumpang (`Member\KeluhanController`) |
| `gotix.keluhan.deskripsi_max` | `int` | `1000` | Batas maksimum karakter keluhan penumpang (`Member\KeluhanController`) |
| `gotix.keluhan.balasan_min` | `int` | `3` | Batas minimum karakter balasan admin (`Admin\KeluhanController`) |
| `gotix.keluhan.balasan_max` | `int` | `1000` | Batas maksimum karakter balasan admin (`Admin\KeluhanController`) |
| `gotix.keluhan.alasan_penolakan_max` | `int` | `1000` | Batas panjang teks alasan penolakan (`VerifikasiPembayaranController`, `Admin\PesananController`) |
| `gotix.auth.login_max_attempts` | `int` | `5` | Batas maksimal percobaan login gagal (`MasukController`) |
| `gotix.auth.login_decay_seconds` | `int` | `60` | Durasi penguncian sementara login gagal (`MasukController`) |
| `gotix.auth.register_max_attempts` | `int` | `5` | Batas maksimal percobaan pendaftaran per IP (`DaftarController`) |
| `gotix.auth.register_decay_seconds` | `int` | `60` | Durasi pembatasan registrasi per IP (`DaftarController`) |

---

## 7. Daftar File yang Dibuat atau Dimodifikasi

### File Baru:
1. `tests/Feature/AuthRateLimitingTest.php` (10 skenario uji rate limiting autentikasi)
2. `tests/Feature/BusinessConfigurationTest.php` (7 skenario uji konfigurasi bisnis terpadu)
3. `docs/24-p0-05-auth-rate-limiting-and-business-config.md` (Dokumen laporan resmi ini)

### File Dimodifikasi:
1. `config/gotix.php` — Penambahan konfigurasi terpadu (auth, pembayaran, keluhan, bank aliases).
2. `app/Http/Controllers/Auth/MasukController.php` — Implementasi rate limiting login, normalisasi identitas, HTTP 429 response.
3. `app/Http/Controllers/Auth/DaftarController.php` — Implementasi rate limiting registrasi per IP.
4. `app/Http/Controllers/Member/PembayaranController.php` — Pembacaan `config('gotix.pembayaran.*')`.
5. `app/Http/Controllers/Member/PesananController.php` — Pembacaan `config('gotix.kapasitas_kursi')`.
6. `app/Http/Controllers/Member/KeluhanController.php` — Pembacaan `config('gotix.keluhan.*')`.
7. `app/Http/Controllers/Operator/PesananLoketController.php` — Pembacaan `config('gotix.batas_bayar_loket_jam')` dan `config('gotix.kapasitas_kursi')`.
8. `app/Http/Controllers/Operator/VerifikasiPembayaranController.php` — Pembacaan `config('gotix.keluhan.alasan_penolakan_max')`.
9. `app/Http/Controllers/Admin/PesananController.php` — Pembacaan `config('gotix.keluhan.alasan_penolakan_max')`.
10. `app/Http/Controllers/Admin/KeluhanController.php` — Pembacaan `config('gotix.keluhan.*')`.
11. `resources/views/auth/masuk.blade.php` — Penanganan `$rateLimitMessage` pada banner alert.
12. `resources/views/auth/daftar.blade.php` — Penanganan `$rateLimitMessage` pada banner alert.
13. `resources/views/layouts/tamu.blade.php` — Sinkronisasi pembacaan rekening bank resmi `config('gotix.bank.*')`.
14. `docs/12-remediation-roadmap.md` — Pemutakhiran status P0-05 menjadi FINALIZED.

---

## 8. Test yang Ditambahkan

### A. Test Suite `AuthRateLimitingTest.php` (10 Tests / 46 Assertions):
1. Login berhasil dengan kredensial valid dan mengarahkan ke dashboard/tiket.
2. Login gagal dengan kredensial tidak valid mencatat hit limiter.
3. Percobaan login gagal berturut-turut sampai batas maksimal yang ditentukan.
4. Request login berikutnya setelah melebihi batas ditolak dengan HTTP 429 dan Retry-After header.
5. Login berhasil membersihkan hit limiter terkait (counter reset ke 0).
6. Identitas login yang berbeda tidak saling mengunci kuota rate limit.
7. Alamat IP yang berbeda tidak saling mengunci kuota rate limit.
8. Normalisasi identitas bekerja konsisten (lowercase & trim).
9. Respons kesalahan tidak membocorkan keberadaan akun (*timing & enumeration safe*).
10. Endpoint registrasi dibatasi per IP untuk mencegah serangan spam/bot.

### B. Test Suite `BusinessConfigurationTest.php` (7 Tests / 57 Assertions):
1. File konfigurasi `gotix.php` berhasil dimuat dengan seluruh key terstruktur.
2. Seluruh key konfigurasi memiliki nilai dan tipe data yang benar.
3. Pemesanan tiket member membaca konfigurasi batas bayar dan kapasitas kursi.
4. Pemesanan tiket loket membaca konfigurasi batas bayar loket dan kapasitas kursi.
5. Unggah bukti transfer mematuhi batasan `max_file_kb` dan MIME terkonfigurasi.
6. Validasi keluhan member mematuhi batas karakter min dan max terkonfigurasi.
7. Perubahan dinamis pada konfigurasi rate limit autentikasi memengaruhi threshold controller secara langsung.

---

## 9. Hasil Full Test Suite

Pengujian menyeluruh dijalankan pada database testing terisolasi `gotix_testing`:

```text
   PASS  Tests\Unit\ExampleTest (1 test)
   PASS  Tests\Feature\AuthRateLimitingTest (10 tests)
   PASS  Tests\Feature\AuthTest (18 tests)
   PASS  Tests\Feature\BusinessConfigurationTest (7 tests)
   PASS  Tests\Feature\ExampleTest (1 test)
   PASS  Tests\Feature\ModelTest (14 tests)
   PASS  Tests\Feature\PaymentProofSecurityTest (30 tests)
   PASS  Tests\Feature\PaymentVerificationSecurityTest (14 tests)
   PASS  Tests\Feature\SeatReservationIntegrityTest (13 tests)
   PASS  Tests\Feature\TransactionHistoryProtectionTest (17 tests)

   Tests:    128 passed (458 assertions)
   Duration: 16.34s
```

Seluruh 128 pengujian lulus 100% tanpa ada regresi pada modul sebelumnya (P0-01 s.d. P0-04).

---

## 10. Hasil Pemeriksaan Kualitas

1. **Pemeriksaan Sintaks PHP (`php -l`):**
   - 12 berkas PHP yang dimodifikasi/dibuat dinyatakan bebas kesalahan sintaks (`No syntax errors detected`).
2. **Pemeriksaan Standar Gaya Kode (`vendor/bin/pint --test`):**
   - Seluruh berkas P0-05 berstatus **`passed`** sesuai standar Laravel Pint.
3. **Pemeriksaan Kompilasi Aset Frontend (`npm run build`):**
   - Vite 7.3.7 berhasil mengompilasi seluruh modul (`✓ 62 modules transformed`, `built in 3.21s`).
4. **Pemeriksaan Skema Database:**
   - Tidak ada migrasi database yang dibuat atau dijalankan. Database utama `gotix` tetap pada status Batch 4 tanpa mutasi struktur.

---

## 11. Temuan yang Sengaja Tidak Ditangani (Di Luar Lingkup)

Sesuai batasan mutlak P0-05:
1. **Fitur Lupa / Reset Password:** Aplikasi GOTIX saat ini memang belum menyediakan alur reset password melalui email. Fitur baru tidak dibuat agar tidak melanggar batasan lingkup.
2. **Pengaturan Konfigurasi Dinamis via UI Admin:** Konfigurasi bisnis tetap dikelola melalui berkas konfigurasi Laravel standar (`config/gotix.php`) dan `.env`, tanpa menambahkan tabel database pengaturan baru atau modul UI admin.
3. **File Lama di Luar Lingkup P0-05:** Masalah format lama pada file seeders atau controller lama yang tidak tersentuh P0-05 tidak diformat massal demi menjaga riwayat diff yang bersih.

---

## 12. Kesimpulan Status P0-05

Tahap **P0-05 — Authentication Rate Limiting & Business Configuration Consistency** resmi dinyatakan:

> **STATUS: FINALIZED / SELESAI PENUH**  
> Seluruh fase fondasi kritis **P0 (P0-01 sampai P0-05)** pada roadmap perbaikan GOTIX kini telah selesai 100%.

