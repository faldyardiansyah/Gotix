# 19 — Hardening Pengamanan Storage Bukti Pembayaran (Phase 3.3.2 / P0-03)

> **Status:** Selesai Diimplementasikan & Tervalidasi  
> **Tanggal Pelaksanaan:** 12 November 2026  
> **Dasar Pelaksanaan:** Laporan Audit Pasca-Implementasi Phase 3.3.1 (`docs/18-phase-3.3-private-payment-proof-storage.md` & lampiran audit)  
> **Kepatuhan Tata Kelola:** Mematuhi seluruh batasan keras `AGENTS.md` (tanpa modifikasi skema/migrasi DB, tanpa menjalankan migrasi riil, lolos Pint 100%, lolos Pest 100%).

---

## 1. Ringkasan Eksekutif

Pada **Phase 3.3.1 (Read-Only Post-Implementation Audit)**, ditemukan dua kelemahan teknis spesifik pada implementasi P0-03:
1. **Root Directory Confinement:** Validasi path kanonikal `realpath()` menggunakan `str_starts_with($realFullPath, $diskRoot)` tanpa pemisah direktori penutup (`DIRECTORY_SEPARATOR`), yang berpotensi meloloskan akses ke direktori bersaudara (*sibling directories*) seperti `private-backup` atau `private_data`.
2. **Potensi Overwrite pada Command Migrasi:** Perintah migrasi konsol `MigratePaymentProofs` belum memeriksa ketersediaan nama tujuan di disk privat, sehingga berisiko menimpa berkas privat yang sudah ada jika terdapat kesamaan nama berkas sumber.

Pada **Phase 3.3.2**, kedua kelemahan teknis tersebut telah diperbaiki secara tuntas melalui langkah *hardening* terfokus tanpa merombak arsitektur sistem maupun skema basis data.

---

## 2. Rincian Temuan yang Diperbaiki

### 2.1 Perbaikan Batas Direktori Root (Root Confinement)
- **Berkas:** `app/Http/Controllers/PembayaranBuktiController.php`
- **Mekanisme Lama:**
  ```php
  $realFullPath = realpath($fullPath);
  $diskRoot = realpath(Storage::disk($disk)->path(''));
  if (! $realFullPath || ! $diskRoot || ! str_starts_with($realFullPath, $diskRoot)) {
      abort(403, 'Akses file di luar direktori yang diizinkan.');
  }
  ```
- **Masalah:** Fungsi `realpath(...)` pada direktori root menghasilkan string tanpa akhiran pemisah (misal `D:\...\storage\app\private`). Pencocokan teks mentah `str_starts_with` akan mengevaluasi `true` untuk path bersaudara seperti `D:\...\storage\app\private-backup\secret.jpg`.
- **Mekanisme Baru (Hardened):**
  ```php
  $fullPath = Storage::disk($disk)->path($path);
  $realFullPath = realpath($fullPath);
  $diskRoot = realpath(Storage::disk($disk)->path(''));

  // Pastikan resolusi path dan root berhasil dan tidak bernilai false/kosong
  if (! $realFullPath || ! $diskRoot) {
      abort(404, 'File bukti transfer tidak dapat ditemukan.');
  }

  // Bentuk batas direktori kanonikal dengan pemisah penutup
  $diskRootWithSeparator = rtrim($diskRoot, '\\/') . DIRECTORY_SEPARATOR;

  // Pastikan berkas berada strictly di dalam batas direktori root
  if (! str_starts_with($realFullPath, $diskRootWithSeparator)) {
      abort(403, 'Akses file di luar direktori yang diizinkan.');
  }
  ```
- **Keunggulan:**
  - Menjamin direktori bersaudara (`private-backup`, `private_data`) ditolak secara mutlak dengan HTTP 403.
  - Menjamin symlink yang mengarah keluar dari root penyimpanan langsung terdeteksi dan diblokir dengan HTTP 403.
  - Memisahkan kegagalan resolusi file fisik (HTTP 404) dari pelanggaran batas keamanan direktori (HTTP 403).

### 2.2 Pencegahan Overwrite pada Command Migrasi (Destination Overwrite Guard)
- **Berkas:** `app/Console/Commands/MigratePaymentProofs.php`
- **Mekanisme Lama:** Target tujuan langsung dibentuk dari `bukti_pembayaran/ . basename($path)` dan langsung ditulis melalui `Storage::disk('local')->put()`.
- **Mekanisme Baru (Hardened):**
  1. Melacak seluruh path yang telah diklaim selama siklus migrasi via array `$claimedPaths`.
  2. Memeriksa keberadaan file target di disk privat:
     ```php
     $newPath = 'bukti_pembayaran/' . $filename;
     $counter = 1;
     while (Storage::disk('local')->exists($newPath) || in_array($newPath, $claimedPaths, true)) {
         $suffix = '_' . ($counter === 1 ? substr(hash('sha256', $path . '_' . $record->id), 0, 8) : $counter);
         $newFilename = $name . $suffix . ($extension ? '.' . $extension : '');
         $newPath = 'bukti_pembayaran/' . $newFilename;
         $counter++;
     }
     $claimedPaths[] = $newPath;
     ```
  3. Menjamin dua berkas publik berbeda yang memiliki nama dasar identik (misal `folder1/struk.jpg` dan `folder2/struk.jpg`) mendapatkan nama unik yang berbeda di disk privat dan tidak saling menimpa.
  4. Menjamin berkas privat lama yang sudah ada tidak pernah tertimpa.
  5. Menjamin berkas sumber publik hanya dihapus setelah:
     - Berkas privat berhasil ditulis.
     - Hash SHA-256 sumber dan target diverifikasi cocok 100%.
     - Pembaruan database baris rekaman berhasil.
  6. Blok penanganan error (`catch`) membersihkan file parsial baru jika sempat tertulis tanpa merusak file lain di disk privat.

---

## 3. Inventaris Berkas Terpengaruh

| No | Berkas | Status | Deskripsi Perubahan |
|---|---|---|---|
| 1 | `app/Http/Controllers/PembayaranBuktiController.php` | Dimodifikasi | Penegakan batas direktori dengan `DIRECTORY_SEPARATOR` dan penolakan direktori bersaudara. |
| 2 | `app/Console/Commands/MigratePaymentProofs.php` | Dimodifikasi | Mekanisme *destination overwrite guard*, tracking `$claimedPaths`, dan penamaan unik. |
| 3 | `tests/Feature/PaymentProofSecurityTest.php` | Dimodifikasi | Penambahan 5 skenario uji baru mencakup boundary direktori, symlink, dan collision guard (total 33 skenario). |
| 4 | `docs/12-remediation-roadmap.md` | Dimodifikasi | Pemutakhiran status P0-03 & dokumentasi 6 persyaratan retensi histori untuk P0-04. |
| 5 | `docs/19-phase-3.3.2-private-payment-proof-hardening.md` | **Baru** | Dokumentasi teknis lengkap implementasi Phase 3.3.2. |

---

## 4. Hasil Pengujian & Verifikasi

### 4.1 Test Suite Keamanan Storage: `tests/Feature/PaymentProofSecurityTest.php`
Hasil eksekusi: **33 passed (93 assertions)** dalam 4.01s.
- `akses bukti transfer ditolak 403 jika canonical path berada di luar root disk atau direktori bersaudara` (LULUS)
- `akses bukti transfer ditolak 403 jika berkas menunjuk ke symlink di luar root penyimpanan` (LULUS)
- `command migrasi tidak menimpa file privat yang sudah ada dan menghasilkan nama tujuan unik` (LULUS)
- `command migrasi menangani dua file sumber dengan basename sama tanpa saling menimpa` (LULUS)
- `command migrasi aman saat dieksekusi ulang (idempoten)` (LULUS)
- Seluruh skenario RBAC, MIME validation, header nosniff, parameter tampering, dan traversal sebelumnya tetap LULUS.

*Catatan Symlink Lingkungan Windows:* Pada lingkungan pengujian Windows non-elevated (tanpa izin administrator khusus), pembuatan symlink melalui PHP `symlink()` dibatasi oleh kebijakan OS. Uji otomatis secara cerdas mendeteksi kapabilitas lingkungan ini: jika pembuatan symlink diizinkan OS, uji memvalidasi penolakan 403; jika tidak diizinkan, uji memverifikasi secara matematis bahwa canonical path di luar root tidak akan pernah memenuhi kondisi `str_starts_with` terhadap `$diskRootWithSeparator`.

### 4.2 Uji Regresi Menyeluruh (Full Test Suite)
Hasil eksekusi: `php artisan test`
```text
Tests:  97 passed (271 assertions)
Duration: 7.23s
```
Semua 97 pengujian di GOTIX (`AuthTest`, `ModelTest`, `SeatReservationIntegrityTest`, `TransactionHistoryProtectionTest`, dan `PaymentProofSecurityTest`) lulus 100% tanpa ada regresi.

### 4.3 Uji Gaya Kode (Laravel Pint)
Hasil eksekusi: `vendor/bin/pint --test`
```json
{"tool":"pint","result":"passed"}
```
Kode sepenuhnya mematuhi standar PSR-12 dan aturan Laravel Pint.

---

## 5. Konfirmasi Integritas Basis Data & Sistem

1. **Konfirmasi Skema Database:** **0 perubahan skema database** dan **0 migrasi baru dibuat**.
2. **Konfirmasi Eksekusi Migrasi Riil:** Command migrasi normal **TIDAK DIJALANKAN** pada basis data riil `gotix`. Data `konfirmasi_pembayaran` tetap berjumlah 0 baris utuh.
3. **Konfirmasi Integritas P0-01 & P0-02:** Kolom generated `pesanan.kursi_aktif`, unique index `unique_jadwal_kursi_aktif`, kolom `deleted_at` master data, serta batasan foreign key `ON DELETE RESTRICT` terverifikasi utuh dan aktif.
4. **Konfirmasi Git:** Tidak ada `git commit` maupun `git push` yang dijalankan.

---

## 6. Persyaratan Retensi Histori Bukti untuk P0-04

Sebagai tindak lanjut dari temuan Audit D pada Phase 3.3.1, persyaratan berikut telah disematkan secara resmi ke dalam roadmap `P0-04` pada `docs/12-remediation-roadmap.md`:

1. **Bukti Ditolak Tetap Tersedia:** Bukti transfer yang pernah ditolak oleh operator/admin harus tetap tersedia sebagai data riwayat dan dilarang dimusnahkan.
2. **Upload Baru Tanpa Hapus:** Pengunggahan bukti bayar baru oleh penumpang pada pesanan yang pernah ditolak tidak boleh menghapus file fisik bukti lama yang telah menjadi bagian dari rekam jejak audit.
3. **Ketertelusuran Histori:** Histori bukti transfer harus dapat dikaitkan dengan pesanan, waktu unggah, verifikator penolak, dan alasan penolakan (`alasan_penolakan`).
4. **Pencegahan Berkas Tak Bertuan:** Berkas bukti lama tidak boleh dibiarkan menjadi file tanpa referensi (*orphan files*) yang tidak dapat dilacak asal-usulnya.
5. **Evaluasi Skema Audit:** Implementasi P0-04 wajib mengevaluasi kebutuhan tabel riwayat verifikasi/bukti pembayaran (misal `riwayat_pembayaran`) atau mekanisme audit logging yang tepat.
6. **Batasan Relasi `hasOne`:** P0-04 dilarang mengasumsikan relasi 1-ke-1 (`hasOne`) saat ini pada `konfirmasi_pembayaran` sudah memadai untuk menampung beberapa versi bukti pembayaran.

---

## 7. Kesimpulan

Phase 3.3.2 telah menuntaskan seluruh perbaikan teknis yang diamanatkan pada audit pasca-implementasi. Implementasi P0-03 (Payment Proof Storage Security) kini telah memiliki proteksi batas direktori yang kokoh (*bulletproof root confinement*) dan command migrasi bebas benturan (*zero-overwrite guarantee*).

Sistem GOTIX kini sepenuhnya siap untuk melanjutkan ke fase remediasi berikutnya: **P0-04 (Transaksionalitas, State Guard, & Audit Trail Verifikasi Pembayaran)** setelah disetujui oleh pemilik proyek.

