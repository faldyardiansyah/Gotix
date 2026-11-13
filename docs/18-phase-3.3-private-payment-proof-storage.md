# 18 — Implementasi Pengamanan Storage Bukti Pembayaran (Phase 3.3 / P0-03)

> **Status:** Selesai Diimplementasikan & Tervalidasi  
> **Tanggal Pelaksanaan:** 12 November 2026  
> **Target Remediasi:** P0-03 (Private Disk + Authorized File Streaming)  
> **Kepatuhan Tata Kelola:** Mematuhi seluruh aturan `AGENTS.md` (tanpa modifikasi migrasi destruktif, tanpa hard delete, lolos Pest 100%, lolos Pint 100%).

---

## 1. Ringkasan Eksekutif

Pada implementasi awal GOTIX, foto struk bukti pembayaran diunggah langsung ke disk `public` (`$request->file('bukti_transfer')->store('bukti_transfer', 'public')`) yang ditautkan ke direktori web publik via symlink (`/storage/bukti_transfer/...`). Struktur ini memiliki kerentanan serius:
1. **Paparan Data Finansial Terbuka (Public Exposure):** Siapa pun di internet tanpa autentikasi dapat melihat struk transfer jika mengetahui atau menebak nama berkasnya.
2. **Ketiadaan Kontrol Akses (No RBAC/Authorization):** Tautan file statis tidak memeriksa apakah pengakses adalah pemilik pesanan, operator yang berwenang, atau pihak luar.
3. **Risiko IDOR (Insecure Direct Object Reference):** Pengguna dapat mengubah parameter untuk mengakses bukti transfer milik penumpang lain.
4. **Validasi File Terlalu Longgar:** Aturan validasi `image` menerima format SVG yang berpotensi mengandung vektor XSS (Cross-Site Scripting).

Pada **Phase 3.3 (P0-03)**, celah keamanan ini telah ditutup secara menyeluruh:
- **Private Storage:** Berkas baru diunggah dan disimpan di direktori privat (`local` disk, root `storage_path('app/private')`) di luar jangkauan web root.
- **Authorized Streaming Endpoint:** Dibuat route dan controller streaming `App\Http\Controllers\PembayaranBuktiController` yang dilindungi autentikasi (`auth`) dan otorisasi berbasis peran (RBAC).
- **Hardening Validasi Unggahan:** Validasi diperketat menggunakan whitelist ekstensi aman (`jpg, jpeg, png, webp, pdf`) dengan ukuran maksimum 5MB (5120KB).
- **Proteksi Path Traversal & Header Keamanan:** Proteksi ketat terhadap manipulasi path (`..`, `:`, `/`, `\\`) serta penyematan header `X-Content-Type-Options: nosniff` dan `Cache-Control: private, no-store`.
- **Dukungan Legacy & Command Migrasi Konsol:** Endpoint streaming mendukung pembacaan fallback transparan untuk berkas warisan di public storage, dilengkapi command `php artisan gotix:migrate-payment-proofs` dengan verifikasi integritas hash SHA-256.

---

## 2. Arsitektur & Kebijakan Keamanan

### 2.1 Matriks Otorisasi Akses (RBAC)

| Peran / Status | Hak Akses Bukti Transfer | Perilaku Sistem |
|---|---|---|
| **Tamu / Guest** | Ditolak | HTTP 302 Redirect ke `/masuk` |
| **Member (Pemilik Pesanan)** | Diizinkan | HTTP 200 Streaming Berkas |
| **Member (Bukan Pemilik)** | Ditolak Keras | HTTP 403 Forbidden |
| **Operator Loket** | Diizinkan | HTTP 200 Streaming Berkas (keperluan verifikasi) |
| **Admin** | Diizinkan | HTTP 200 Streaming Berkas (keperluan audit/rekonsiliasi) |

### 2.2 Header Respons Keamanan

Setiap berkas yang dialirkan (streamed) melalui endpoint menyertakan header keamanan HTTP berikut:
```http
Content-Type: <resolved-mime-type>
Content-Disposition: inline; filename="<sanitized-filename>"
X-Content-Type-Options: nosniff
Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Pragma: no-cache
Expires: 0
```
- `X-Content-Type-Options: nosniff`: Mencegah browser melakukan MIME-sniffing yang dapat mengeksekusi payload berbahaya terselubung.
- `Cache-Control: private, no-store`: Menjamin berkas bukti transfer tidak disimpan di cache publik (proxy, CDN, ISP) maupun browser cache bersama.

---

## 3. Rincian Perubahan Kode Sumber

### 3.1 Controller Streaming: `app/Http/Controllers/PembayaranBuktiController.php`
Dibuat pengontrol terdedikasi dengan tanggung jawab tunggal (*Single Responsibility Principle*):
- Memeriksa hak akses pengguna terhadap pesanan yang terhubung ke rekaman `KonfirmasiPembayaran`.
- Menolak upaya path traversal jika rekaman `bukti_path` mengandung `..`, `:`, atau dimulai dengan slash/backslash.
- Memeriksa keberadaan fisik file pada disk `local` (prioritas utama) atau fallback ke disk `public` (berkas warisan).
- Memastikan `realpath` berkas fisik berada secara ketat di dalam `diskRoot`.
- Mengembalikan `BinaryFileResponse` dengan header keamanan lengkap.

### 3.2 Registrasi Route: `routes/web.php`
Route didaftarkan di dalam grup middleware `auth`:
```php
Route::middleware('auth')->group(function () {
    Route::post('/keluar', [KeluarController::class, 'destroy'])->name('keluar');
    Route::post('/logout', [KeluarController::class, 'destroy'])->name('logout');

    // Berkas Bukti Pembayaran (Akses Terotorisasi & Streaming Aman)
    Route::get('/pembayaran/bukti/{konfirmasi}', [PembayaranBuktiController::class, 'show'])->name('pembayaran.bukti');
});
```

### 3.3 Hardening Upload: `app/Http/Controllers/Member/PembayaranController.php`
Method `konfirmasiStore` dimutakhirkan:
1. Validasi MIME diperketat:
   ```php
   'bukti_transfer' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
   ```
2. Pembersihan berkas lama saat upload ulang: Jika penumpang mengunggah ulang bukti pada pesanan yang sama, berkas lama di disk fisik otomatis dihapus guna mencegah penumpukan *orphan files*.
3. Penyimpanan langsung ke disk privat:
   ```php
   $path = $request->file('bukti_transfer')->store('bukti_pembayaran', 'local');
   ```

### 3.4 Pembaruan Tampilan Frontend (Blade Views)
Seluruh sintaks lama `asset('storage/' . $pesanan->konfirmasiPembayaran->bukti_path)` digantikan secara konsisten dengan `route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id)`:
1. `resources/views/operator/pembayaran/show.blade.php`: Thumbnail dan modal zoom-in struk bayar.
2. `resources/views/admin/pesanan/show.blade.php`: Tautan perbesar dan thumbnail bukti transfer.
3. `resources/views/member/pembayaran/show.blade.php`: Pratinjau struk yang telah diunggah oleh member.
4. `resources/views/member/pesanan/show.blade.php`: Tampilan detail bukti pembayaran pada rincian reservasi member.

### 3.5 Command Migrasi Konsol: `app/Console/Commands/MigratePaymentProofs.php`
Dibuat perintah Artisan `gotix:migrate-payment-proofs`:
- Opsi `--dry-run`: Memeriksa dan melaporkan status berkas tanpa melakukan perubahan fisik maupun database.
- Eksekusi normal:
  1. Membaca berkas lama dari disk `public`.
  2. Menghitung checksum SHA-256 berkas asli.
  3. Menulis berkas ke disk `local` (`bukti_pembayaran/<filename>`).
  4. Menghitung checksum berkas yang baru ditulis dan membandingkannya. Jika tidak cocok, proses dibatalkan dan file target dihapus (*rollback*).
  5. Memperbarui `bukti_path` pada rekaman database.
  6. Menghapus berkas lama dari disk `public`.

---

## 4. Hasil Pengujian & Verifikasi

### 4.1 Test Suite Terdedikasi: `tests/Feature/PaymentProofSecurityTest.php`
Sebanyak **28 skenario pengujian** dibuat mencakup seluruh vektor pengujian keamanan:
- **Autentikasi & RBAC (6 skenario):** Guest diarahkan ke login, member pemilik mendapat 200, member lain ditolak 403, parameter tampering ditolak 403, operator mendapat 200, admin mendapat 200.
- **Header Keamanan & Traversal (6 skenario):** Validasi header nosniff dan no-store, Content-Disposition inline, file fisik hilang 404, ID konfirmasi tidak ada 404, path traversal `..` ditolak 403, path traversal drive letter `C:` ditolak 403.
- **Upload Hardening & MIME Validation (13 skenario):** Berkas tersimpan di disk `local` bukan `public`, berkas lama dihapus saat re-upload, format valid diterima (`jpg, jpeg, png, webp, pdf`), berkas berbahaya ditolak 422 (`php, html, svg, exe, sh`), batas 5MB divalidasi.
- **Legacy Fallback & Artisan Command (3 skenario):** Fallback transparan berkas di disk public, command migrasi memindahkan berkas dengan verifikasi hash, command `--dry-run` tidak mengubah disk/database.

Hasil eksekusi:
```text
PASS  Tests\Feature\PaymentProofSecurityTest
✓ guest ditolak dan diarahkan ke halaman masuk saat mengakses bukti transfer
✓ member pemilik pesanan dapat mengakses berkas bukti transfer miliknya sendiri
✓ member dilarang mengakses berkas bukti transfer milik member lain (403)
✓ parameter tampering ID konfirmasi lain ditolak dengan status 403
✓ operator loket dapat mengakses berkas bukti transfer untuk proses verifikasi (200)
✓ admin dapat mengakses berkas bukti transfer manapun (200)
✓ respons streaming menyertakan header keamanan nosniff dan no-store
✓ respons streaming menyertakan Content-Disposition inline dengan nama berkas
✓ akses bukti transfer mengembalikan 404 jika berkas fisik tidak ada di disk
✓ akses bukti transfer mengembalikan 404 jika ID konfirmasi pembayaran tidak ditemukan
✓ akses bukti transfer ditolak 403 jika record bukti_path mengandung path traversal (..)
✓ akses bukti transfer ditolak 403 jika record bukti_path mencoba absolute path atau drive letter
✓ upload bukti transfer oleh member menyimpan berkas ke private disk (storage/app/private)
✓ upload ulang bukti pembayaran menghapus berkas lama pada private disk
✓ upload bukti transfer menerima format yang sah (jpg, png, webp, pdf) with ('jpg', 'image/jpeg')
✓ upload bukti transfer menerima format yang sah (jpg, png, webp, pdf) with ('jpeg', 'image/jpeg')
✓ upload bukti transfer menerima format yang sah (jpg, png, webp, pdf) with ('png', 'image/png')
✓ upload bukti transfer menerima format yang sah (jpg, png, webp, pdf) with ('webp', 'image/webp')
✓ upload bukti transfer menerima format yang sah (jpg, png, webp, pdf) with ('pdf', 'application/pdf')
✓ upload bukti transfer menolak berkas berbahaya dan tidak sah (422) with ('php', 'text/x-php')
✓ upload bukti transfer menolak berkas berbahaya dan tidak sah (422) with ('html', 'text/html')
✓ upload bukti transfer menolak berkas berbahaya dan tidak sah (422) with ('svg', 'image/svg+xml')
✓ upload bukti transfer menolak berkas berbahaya dan tidak sah (422) with ('exe', 'application/x-msdownload')
✓ upload bukti transfer menolak berkas berbahaya dan tidak sah (422) with ('sh', 'application/x-sh')
✓ upload bukti transfer menolak berkas melebihi ukuran maksimum 5MB dengan 422
✓ streaming controller mendukung fallback transparan jika berkas lama masih berada di public disk
✓ artisan gotix:migrate-payment-proofs memigrasikan berkas dari public ke private disk dengan verifikasi hash
✓ artisan gotix:migrate-payment-proofs --dry-run tidak mengubah file fisik maupun database

Tests:  28 passed (74 assertions)
```

### 4.2 Uji Regresi Menyeluruh (Full Test Suite)
Seluruh 92 skenario pengujian pada aplikasi GOTIX (`AuthTest`, `ModelTest`, `SeatReservationIntegrityTest`, `TransactionHistoryProtectionTest`, dan `PaymentProofSecurityTest`) lulus 100%:
```text
Tests:  92 passed (252 assertions)
Duration: 6.45s
```

### 4.3 Uji Gaya Kode (Laravel Pint) & Build Asset
- **Laravel Pint:** Lulus tanpa pelanggaran (`{"tool":"pint","result":"passed"}`).
- **Vite Build (`npm run build`):** Sukses membangun asset produksi tanpa hambatan.

---

## 5. Inventaris Berkas Terpengaruh

| No | Berkas | Status | Deskripsi |
|---|---|---|---|
| 1 | `app/Http/Controllers/PembayaranBuktiController.php` | **Baru** | Controller streaming berkas dengan otorisasi RBAC, header keamanan, dan anti path traversal. |
| 2 | `app/Console/Commands/MigratePaymentProofs.php` | **Baru** | Command Artisan untuk memigrasikan berkas lama ke private disk dengan verifikasi hash SHA-256. |
| 3 | `tests/Feature/PaymentProofSecurityTest.php` | **Baru** | 28 skenario pengujian komprehensif keamanan storage dan otorisasi. |
| 4 | `routes/web.php` | Dimodifikasi | Mendaftarkan route terproteksi `pembayaran.bukti` di bawah middleware `auth`. |
| 5 | `app/Http/Controllers/Member/PembayaranController.php` | Dimodifikasi | Menyimpan upload ke disk `local`, memperketat validasi MIME, menghapus berkas lama saat re-upload. |
| 6 | `resources/views/operator/pembayaran/show.blade.php` | Dimodifikasi | Menggunakan `route('pembayaran.bukti', ...)` pada thumbnail dan modal. |
| 7 | `resources/views/admin/pesanan/show.blade.php` | Dimodifikasi | Menggunakan `route('pembayaran.bukti', ...)` pada tampilan admin. |
| 8 | `resources/views/member/pembayaran/show.blade.php` | Dimodifikasi | Menggunakan `route('pembayaran.bukti', ...)` pada tampilan instruksi bayar member. |
| 9 | `resources/views/member/pesanan/show.blade.php` | Dimodifikasi | Menggunakan `route('pembayaran.bukti', ...)` pada tampilan detail reservasi member. |
| 10 | `docs/12-remediation-roadmap.md` | Dimodifikasi | Pemutakhiran status tugas `P0-03` menjadi Selesai. |
| 11 | `docs/18-phase-3.3-private-payment-proof-storage.md` | **Baru** | Dokumentasi teknis lengkap implementasi Phase 3.3. |

---

## 6. Kesimpulan & Status

Phase 3.3 (`P0-03: Private Disk + Authorized File Streaming`) telah diselesaikan secara tuntas dan aman. Seluruh data bukti pembayaran penumpang GOTIX kini terisolasi secara privat dari akses publik, terproteksi oleh otorisasi berbasis peran, dan siap melanjutkan ke fase remediasi berikutnya (`P0-04`) sesuai persetujuan pemilik proyek.

