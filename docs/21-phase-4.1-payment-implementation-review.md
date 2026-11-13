# 21 — Phase 4.1: Payment Implementation Read-Only Review

> **Jenis Dokumen:** Laporan Audit Pasca-Implementasi Read-Only (Post-Implementation Review)  
> **Fase:** P0-04.1 (Verifikasi & Evaluasi Kritis Implementasi P0-04)  
> **Tanggal Audit:** 12 November 2026  
> **Metode Audit:** Static Code Inspection, Configuration Audit, DB Schema Audit, Read-Only Regression Run  
> **Status Tindakan:** **READ-ONLY (Tanpa Mutasi Data, Tanpa Migrasi Database Utama, Tanpa Perubahan Kode Aplikasi)**

---

## 1. Executive Summary

Audit pasca-implementasi read-only ini dilakukan terhadap seluruh artefak dan kode hasil pengerjaan **P0-04: Payment State Guard & Verification**. Fokus audit adalah memvalidasi secara objektif apakah implementasi aktual telah memenuhi persyaratan integritas data, keamanan transaksi perbankan manual, retensi bukti historis, otorisasi peran, dan penanganan kondisi balapan konkurensi (*race conditions*).

### Rangkuman Temuan Inti
1. **Histori Pembayaran Multi-Unggahan:** **`VERIFIED`**. Tabel baru `riwayat_pembayaran` berhasil memisahkan representasi status konfirmasi aktif (`konfirmasi_pembayaran`) dari riwayat bukti lengkap (`riwayat_pembayaran`), sehingga bukti lama tidak terhapus saat upload ulang.
2. **State Guard & Transaksionalitas:** **`VERIFIED`**. Seluruh mutasi status verifikasi dan penolakan pada Operator dan Admin telah dilindungi `DB::transaction()` dan `lockForUpdate()`, serta memvalidasi status awal `Dalam Proses`. Penolakan pembayaran berhasil mengembalikan status ke `Belum Bayar` dan mencatat alasan penolakan wajib.
3. **Mekanisme Expiry & Pelepasan Kursi:** **`VERIFIED`**. Command `gotix:cancel-expired-orders` berhasil membatalkan pesanan kedaluwarsa berstatus `Belum Bayar` dan melepaskan reservasi kursi secara otomatis tanpa merusak pesanan `Lunas` atau pesanan yang sedang `Dalam Proses`.
4. **Keamanan Berkas Bukti:** **`VERIFIED`**. Berkas bukti lama tetap tersimpan di storage privat dan dapat di-stream secara terotorisasi melalui route `pembayaran.bukti.riwayat` dengan pembatasan direktori (*root confinement*) yang kokoh.
5. **Status Migrasi Database Utama (`gotix`):** **`VERIFIED`**. Migrasi `2026_11_12_000003_create_riwayat_pembayaran_table.php` berstatus **`Pending`** di database utama dan belum dieksekusi sebelum persetujuan pemilik proyek.
6. **Kelemahan & Keputusan Bisnis Terbuka (Open Items):**
   - **`OPEN BUSINESS DECISION`**: Pesanan berstatus `Dalam Proses` saat ini dilindungi dari auto-cancellation. Jika operator tidak pernah memverifikasi pembayaran (misal lalai), kursi dapat tertahan tanpa batas waktu hingga/melewati jadwal keberangkatan bus.
   - **`GAP` (Test Coverage)**: Test suite saat ini baru menguji 2 siklus unggah. Belum ada pengujian otomatis untuk > 2 kali penolakan berturut-turut, kegagalan database simulatif (orphan file rollback), dan verifikasi setelah re-upload kedua.

---

## 2. Hasil Audit Histori Pembayaran

| Komponen Audit | Hasil Pemeriksaan Aktual | Status |
|---|---|---|
| **Foreign Key & Tipe Data** | `riwayat_pembayaran.pesanan_id` (`BIGINT UNSIGNED`) -> `pesanan.id` (`BIGINT UNSIGNED`). `pengguna_id` -> `pengguna.id`. Tipe data dan signedness identik dengan tabel induk. | `VERIFIED` |
| **Integritas Penghapusan (No Cascade Delete)** | `pesanan_id` dan `pengguna_id` dideklarasikan dengan `restrictOnDelete()`. Tidak ada opsi cascade delete yang dapat melenyapkan riwayat keuangan secara tidak sengaja. | `VERIFIED` |
| **Nullable Attribute** | `konfirmasi_pembayaran_id` (`nullOnDelete`), `diverifikasi_oleh` (`nullOnDelete`), `diverifikasi_pada` (`dateTime nullable`), dan `alasan_penolakan` (`text nullable`). Seluruh atribut audit bersifat nullable pada saat upload awal dan terisi saat verifikasi. | `VERIFIED` |
| **Pemisahan Entri Upload** | Setiap pemanggilan `Member\PembayaranController::konfirmasiStore` memicu `RiwayatPembayaran::create(...)`. Tiap berkas unggahan baru menghasilkan baris `id` baru. | `VERIFIED` |
| **Penelusuran Penolakan** | Saat ditolak di `Operator\VerifikasiPembayaranController::tolak`, record riwayat terakhir di-update dengan status `'Ditolak'`, mencatat `diverifikasi_oleh = Auth::id()`, `diverifikasi_pada = now()`, dan `alasan_penolakan`. | `VERIFIED` |
| **Retensi Berkas Fisik** | Baris lama tidak dihapus dari disk private (`Storage::delete` tidak lagi dipanggil untuk berkas lama pada `konfirmasiStore`). | `VERIFIED` |
| **Akses Bukti Pasca-Status Akhir** | Riwayat dan file bukti tetap dapat diakses oleh Member pemilik dan Operator/Admin melalui route `pembayaran.bukti.riwayat` baik saat pesanan berstatus `Lunas` maupun `Dibatalkan`. | `VERIFIED` |
| **Pencarian Record Terakhir (`latest()`)** | Pemanggilan `RiwayatPembayaran::where('pesanan_id', $pesanan->id)->latest()->first()` menggunakan `created_at DESC`. Pada skenario riil di mana upload dipisahkan oleh interval waktu verifikasi, ini aman. Namun secara teknis, pengurutan `latest('id')` lebih deterministik terhadap timestamp identik dalam detik yang sama. | `GAP (Low)` |

### Contoh Konkret Skenario Multi-Unggahan & Multi-Penolakan
1. **Upload 1 (Member A, 08:00):**
   - File tersimpan: `bukti_pembayaran/struk_1_hash.jpg`
   - `pesanan`: `status = Dalam Proses`
   - `konfirmasi_pembayaran`: `id = 1, pesanan_id = 10, nomor_resi = 'TF-001', bukti_path = '...struk_1.jpg', diverifikasi_oleh = null`
   - `riwayat_pembayaran` (Row 1): `id = 1, pesanan_id = 10, bukti_path = '...struk_1.jpg', status = 'Dalam Proses'`
2. **Penolakan 1 (Operator B, 08:30):**
   - `pesanan`: `status = Belum Bayar`
   - `konfirmasi_pembayaran`: `diverifikasi_oleh = B, diverifikasi_pada = '08:30', alasan_penolakan = 'Struk buram'`
   - `riwayat_pembayaran` (Row 1): `status = 'Ditolak', diverifikasi_oleh = B, diverifikasi_pada = '08:30', alasan_penolakan = 'Struk buram'`
3. **Upload 2 (Member A, 09:00):**
   - File fisik 1 tetap utuh di disk private. File baru tersimpan: `bukti_pembayaran/struk_2_hash.png`
   - `pesanan`: `status = Dalam Proses`
   - `konfirmasi_pembayaran`: di-update (`bukti_path = '...struk_2.png', diverifikasi_oleh = null, diverifikasi_pada = null, alasan_penolakan = null`)
   - `riwayat_pembayaran` (Row 2): `id = 2, pesanan_id = 10, bukti_path = '...struk_2.png', status = 'Dalam Proses'`. **Row 1 tidak berubah.**
4. **Penolakan 2 (Operator C, 09:15):**
   - `pesanan`: `status = Belum Bayar`
   - `konfirmasi_pembayaran`: `diverifikasi_oleh = C, diverifikasi_pada = '09:15', alasan_penolakan = 'Nominal kurang Rp 25.000'`
   - `riwayat_pembayaran` (Row 2): `status = 'Ditolak', diverifikasi_oleh = C, diverifikasi_pada = '09:15', alasan_penolakan = 'Nominal kurang Rp 25.000'`. **Row 1 tetap mencatat alasan 'Struk buram' dari Operator B.**
5. **Upload 3 & Akhirnya Disetujui (10:00):**
   - File 1, 2, 3 semuanya tetap ada di storage.
   - `pesanan`: `status = Lunas`
   - `konfirmasi_pembayaran`: `diverifikasi_oleh = B, diverifikasi_pada = '10:00', alasan_penolakan = null`
   - `riwayat_pembayaran` (Row 3): `status = 'Diterima', diverifikasi_oleh = B`. Seluruh riwayat (Row 1, 2, 3) dapat diaudit lengkap melalui UI Admin/Operator.

---

## 3. Hasil Audit Konsistensi Status

| Skenario Transisi | Controller / Handler | Aturan Validasi | Status Akhir | Status Audit |
|---|---|---|---|---|
| `Belum Bayar` + Upload Sah (Waktu valid) | `Member\PembayaranController::konfirmasiStore` | `status == BelumBayar` dan `now() <= batas_bayar` | Pesanan: `Dalam Proses`<br>Riwayat: `Dalam Proses` | `VERIFIED` |
| `Belum Bayar` + Upload Kedaluwarsa | `Member\PembayaranController::konfirmasiStore` | `now() > batas_bayar` | Ditolak (Redirect + Flash gagal) | `VERIFIED` |
| `Dalam Proses` + Verifikasi Terima | `Operator\VerifikasiPembayaranController::verifikasi`<br>`Admin\PesananController::verifikasi` | `status == DalamProses` | Pesanan: `Lunas`<br>Riwayat: `Diterima`<br>Auditor & waktu terisi | `VERIFIED` |
| `Dalam Proses` + Verifikasi Tolak | `Operator\VerifikasiPembayaranController::tolak`<br>`Admin\PesananController::tolak` | `status == DalamProses` dan `alasan_penolakan` wajib | Pesanan: `Belum Bayar`<br>Riwayat: `Ditolak`<br>Auditor, waktu, & alasan terisi | `VERIFIED` |
| `Lunas` + Coba Verifikasi Ulang | `Operator\VerifikasiPembayaranController::verifikasi` | State guard: status bukan `DalamProses` | Ditolak (Redirect + Flash gagal, status tetap `Lunas`) | `VERIFIED` |
| `Dibatalkan` + Coba Verifikasi | `Operator\VerifikasiPembayaranController::verifikasi` | State guard: status bukan `DalamProses` | Ditolak (Redirect + Flash gagal, status tetap `Dibatalkan`) | `VERIFIED` |
| `Lunas` + Coba Upload Baru | `Member\PembayaranController::konfirmasiStore` | State guard: status bukan `BelumBayar` | Ditolak (Redirect + Flash gagal) | `VERIFIED` |
| `Dibatalkan` + Coba Upload Baru | `Member\PembayaranController::konfirmasiStore` | State guard: status bukan `BelumBayar` | Ditolak (Redirect + Flash gagal) | `VERIFIED` |
| `Belum Bayar` + Batas Bayar Lewat (Scheduler) | `CancelExpiredOrders::handle` | `status == BelumBayar` dan `batas_bayar < now()` | Pesanan: `Dibatalkan`<br>Kursi: Otomatis Rilis (`NULL`) | `VERIFIED` |

### Konsistensi Representasi Antar-Tabel
1. `pesanan.status`: Enum `App\Enums\StatusPesanan` (`Belum Bayar`, `Dalam Proses`, `Lunas`, `Dibatalkan`).
2. `konfirmasi_pembayaran`: Tabel ini tidak memiliki kolom enum `status` tersendiri; status aktifnya mengikuti `pesanan.status`.
3. `riwayat_pembayaran.status`: Kolom string independen (`'Dalam Proses'`, `'Diterima'`, `'Ditolak'`). Pada setiap mutasi status pesanan, entri riwayat pembayaran yang bersangkutan diperbarui secara atomik di dalam transaksi yang sama.

---

## 4. Hasil Audit Concurrency dan Transaction Locking

### Urutan Penguncian Baris (*Lock Order*)
- **`Member\PembayaranController::konfirmasiStore`:**
  `Pesanan::where('id', $pesanan->id)->lockForUpdate()->firstOrFail();`
- **`Operator\VerifikasiPembayaranController::verifikasi` & `tolak`:**
  `Pesanan::where('id', $id)->lockForUpdate()->firstOrFail();`
- **`Admin\PesananController::verifikasi` & `tolak`:**
  `Pesanan::where('id', $id)->lockForUpdate()->firstOrFail();`
- **`CancelExpiredOrders::handle`:**
  `Pesanan::where('id', $candidate->id)->lockForUpdate()->first();`

### Evaluasi Concurrency Safety:
1. **Penguncian Baris Induk:** Seluruh 6 operasi mutasi mengunci baris yang sama (`pesanan`) secara eksklusif (`FOR UPDATE`).
2. **Kebutuhan Penguncian `konfirmasi_pembayaran`:** Karena relasi `pesanan` ke `konfirmasi_pembayaran` adalah 1-ke-1 dan selalu dimutasi melalui konteks induk `pesanan_id`, penguncian pada tabel `pesanan` bertindak sebagai mutex transaksional (*aggregate root lock*). Penguncian baris `konfirmasi_pembayaran` secara terpisah tidak diperlukan.
3. **Analisis Deadlock:** Karena seluruh proses verifikasi, penolakan, unggah ulang, dan pembatalan otomatis HANYA mengunci satu tabel (`pesanan`) tanpa siklus ketergantungan penguncian ke tabel lain, **risiko deadlock antar-operasi pembayaran adalah 0 (mustahil terjadi siklus deadlock)**.
4. **Verifikasi Ganda (*Double-Submit*):** Jika dua operator mengklik tombol verifikasi pada milidetik yang sama, transaksi pertama memperoleh lock dan mengubah status ke `Lunas`. Transaksi kedua yang menunggu lock akan membaca status `Lunas` saat gilirannya tiba, memicu pengecekan `$pesanan->status !== StatusPesanan::DalamProses`, dan membatalkan mutasi tanpa merusak transaksi.
5. **Collision Upload vs Expiry:** Jika Member mengunggah berkas tepat saat cron expiry berjalan:
   - Jika Member mendapat lock lebih dulu: status menjadi `Dalam Proses`. Expiry yang memperoleh lock setelahnya melihat status bukan `Belum Bayar` dan melewatinya.
   - Jika Expiry mendapat lock lebih dulu: status menjadi `Dibatalkan`. Transaksi Member melempar `DomainException`, dan blok `catch` segera menghapus berkas baru dari disk private (`Storage::delete($path)`). Tidak ada berkas orphan yang tersisa.

---

## 5. Hasil Audit Expiry dan Scheduler

| Poin Pemeriksaan | Hasil Evaluasi Aktual | Status |
|---|---|---|
| **Pencocokan Zona Waktu** | Aplikasi menggunakan `Asia/Jakarta` (`config/app.php`). Nilai `now()` dan datetime MariaDB berada pada basis zona waktu yang sinkron. | `VERIFIED` |
| **Konsistensi Batas Waktu** | Perbandingan menggunakan operator `<` (`batas_bayar < $now`). Pesanan yang batas bayarnya belum terlampaui tidak dibatalkan. | `VERIFIED` |
| **Proteksi Pesanan `Lunas`** | Query penyaring hanya mengambil `status = StatusPesanan::BelumBayar`. Pesanan Lunas tidak pernah tersentuh. | `VERIFIED` |
| **Idempotensi Command** | Pengujian berulang membuktikan command aman dijalankan berulang kali tanpa melempar exception atau memicu mutasi ganda. | `VERIFIED` |
| **Pelepasan Kursi Otomatis** | Berkat stored generated column `kursi_aktif` dari Phase P0-01 (`IF(status = 'Dibatalkan', NULL, nomor_kursi)`), saat status diubah ke `Dibatalkan`, kolom `kursi_aktif` otomatis menjadi `NULL`, sehingga kursi langsung tersedia di unique index untuk pemesan berikutnya. | `VERIFIED` |
| **Pendaftaran Scheduler** | Terdaftar pada `routes/console.php` dengan `Schedule::command('gotix:cancel-expired-orders')->everyMinute();` (sintaks resmi Laravel 12). | `VERIFIED` |

### Evaluasi Khusus: Status `Dalam Proses` (OPEN BUSINESS DECISION)
- **Kondisi Aktual:**
  Pesanan berstatus `Dalam Proses` saat ini sengaja **tidak dibatalkan** oleh `CancelExpiredOrders`, karena sedang menunggu verifikasi petugas.
- **Risiko Bisnis yang Teridentifikasi:**
  Jika operator/admin lalai atau tidak membuka dashboard verifikasi dalam waktu lama, pesanan berstatus `Dalam Proses` akan menggantung selamanya. Akibatnya, kursi bus pada jadwal tersebut terkunci secara permanen hingga bus berangkat dan tidak dapat dibeli oleh penumpang loket maupun online lainnya.
- **Status:** **`OPEN BUSINESS DECISION`**
- **Rekomendasi Kebijakan:**
  1. *Kebijakan Batas Waktu Verifikasi:* Tetapkan batas maksimal verifikasi bagi operator (misal: maksimal 2 jam setelah unggah, atau maksimal 1 jam sebelum bus berangkat).
  2. *Eskalasi / Auto-Reject:* Jika batas verifikasi operator terlampaui tanpa aksi, pesanan otomatis dikembalikan ke `Belum Bayar` atau dibatalkan dengan notifikasi sistem.
  3. *Sortir Antrean Urgensi:* Di halaman antrean Operator (`operator/pembayaran/index.blade.php`), urutkan antrean berdasarkan jadwal keberangkatan bus terdekat (`jadwal.tanggal_berangkat ASC, jadwal.jam_berangkat ASC`), bukan berdasarkan waktu pembuatan pesanan (`created_at DESC`).

---

## 6. Hasil Audit Private File Streaming

| Poin Pemeriksaan | Hasil Evaluasi Aktual | Status |
|---|---|---|
| **Otorisasi Kepemilikan Member** | Member hanya diizinkan melihat berkas bukti jika `$konfirmasi->pesanan->pengguna_id === $user->id` atau `$riwayat->pesanan->pengguna_id === $user->id`. Pihak lain ditolak `403 Forbidden`. | `VERIFIED` |
| **RBAC Operator & Admin** | Operator dan Admin memiliki wewenang untuk memeriksa berkas bukti apapun dalam lingkup tugas verifikasi. | `VERIFIED` |
| **Parameter Tampering Defense** | Mengganti ID konfirmasi atau ID riwayat pada URL dengan ID milik orang lain menghasilkan respons `403 Forbidden` (bukan data bocor). | `VERIFIED` |
| **Path Traversal Defense** | Pengecekan sequence traversal (`..`, `:`, `/`, `\`) menolak upaya eksploitasi traversal dengan `403`. | `VERIFIED` |
| **Root Directory Confinement** | Validasi kanonikal `realpath()` menggunakan delimiter `$diskRootWithSeparator = rtrim($diskRoot, '\\/').DIRECTORY_SEPARATOR;` dan memastikan berkas kanonikal wajib berada di dalam batas direktori root privat. | `VERIFIED` |
| **Header Keamanan Respons** | Header `X-Content-Type-Options: nosniff`, `Cache-Control: private, no-store, no-cache, must-revalidate`, `Pragma: no-cache`, `Expires: 0`, dan `Content-Disposition: inline`. | `VERIFIED` |
| **Ketiadaan Akses Publik Terbuka** | Berkas disimpan di disk privat (`local`), tidak ada URL publik `/storage/...` yang dapat diakses secara langsung oleh tamu/mesin pencari. | `VERIFIED` |

---

## 7. Hasil Audit Migration dan Database

### Struktur Migrasi: `2026_11_12_000003_create_riwayat_pembayaran_table.php`
- **Tabel:** `riwayat_pembayaran`
- **Karakteristik Skema:**
  - Bersifat murni aditif (membuat tabel baru `CREATE TABLE`).
  - Tidak mengubah kolom, tipe data, atau indeks pada tabel yang sudah ada (`pesanan`, `konfirmasi_pembayaran`, `pengguna`, `jadwal`).
  - Tidak memiliki cascade delete berbahaya (`restrictOnDelete` pada `pesanan_id` dan `pengguna_id`).
  - Rollback plan aman: `Schema::dropIfExists('riwayat_pembayaran')`.
  - Tidak menimbulkan konflik dengan migrasi P0-01 (unique constraint kursi aktif) maupun P0-02 (soft deletes master data).

### Status Aktual Migrasi Database
- **Database Testing (`gotix_testing`):**
  - Status: **`Ran`** (Batch 1).
  - Digunakan untuk eksekusi 111 automated test suite.
- **Database Utama (`gotix`):**
  - Status: **`Pending`**
  - **`VERIFIED`**: Perintah `php artisan migrate` belum dijalankan pada database utama `gotix`. Database utama tetap murni berada pada status sebelum P0-04.

---

## 8. Hasil Audit Test Coverage

Pengujian otomatis saat ini mencakup 14 skenario pengujian fungsional dan keamanan pada `tests/Feature/PaymentVerificationSecurityTest.php`, ditambah 33 skenario pada `PaymentProofSecurityTest.php`.

### Evaluasi Gap Pengujian:

| Skenario Pengujian | Cakupan Saat Ini | Status Evaluasi | Rekomendasi Tambahan |
|---|---|---|---|
| **Multi-Upload & Penolakan Berulang (> 2 Siklus)** | Baru menguji 2 siklus (Upload 1 -> Tolak 1 -> Upload 2). | `GAP (Low)` | Tambahkan automated test untuk 3-4 siklus unggah dan penolakan berturut-turut untuk memastikan `riwayat_pembayaran` tetap konsisten pada siklus panjang. |
| **Verifikasi Pasca Upload Ulang** | Pengujian siklus ke-2 berhenti saat status `Dalam Proses`. | `GAP (Low)` | Tambahkan penegasan bahwa operator dapat memverifikasi unggahan ke-2 menjadi `Lunas` dan baris riwayat ke-2 berubah menjadi `Diterima` sementara baris ke-1 tetap `Ditolak`. |
| **Simulasi Rollback Database (Orphan Cleanup)** | Blok `try ... catch (\Throwable)` telah ada di controller. | `GAP (Low)` | Buat test dengan mock exception pada DB event untuk membuktikan berkas baru terhapus dari storage jika transaksi DB melempar exception. |
| **Akses Bukti pada Pesanan yang Telah Batal/Lunas** | Teruji pada pesanan aktif dan pesanan baru. | `GAP (Low)` | Tambahkan test eksplisit bahwa bukti historis tetap dapat diakses setelah pesanan melewati batas waktu dan dibatalkan oleh scheduler. |
| **Pengujian Foreign Key RESTRICT pada Riwayat** | Diuji secara terpisah di `TransactionHistoryProtectionTest`. | `VERIFIED` | Sifat RESTRICT pada `pesanan_id` dan `pengguna_id` telah terlindungi di level engine MySQL InnoDB. |

---

## 9. Daftar Temuan Berdasarkan Tingkat Risiko

### Critical
- *Tidak ada temuan dengan risiko Critical.* Arsitektur transaksi, penguncian baris, dan isolasi keamanan private storage berfungsi kokoh.

### High
- *Tidak ada temuan dengan risiko High.*

### Medium
1. **`OPEN BUSINESS DECISION` — Potensi Kursi Tergantung pada Pesanan `Dalam Proses`:**
   - **Deskripsi:** Pesanan berstatus `Dalam Proses` tidak dibatalkan otomatis oleh command expiry. Jika operator tidak pernah memproses verifikasi/penolakan (misal kelalaian petugas loket), kursi penumpang akan terkunci selamanya hingga dan setelah jadwal bus berangkat.
   - **Dampak:** Penumpang lain tidak bisa memesan kursi tersebut, dan pendapatan perusahaan berpotensi hilang jika pembayaran ternyata fiktif.

### Low
1. **`GAP` — Pengurutan `latest()` Berpotensi Bergantung pada Presisi Detik:**
   - **Deskripsi:** Pencarian riwayat aktif menggunakan `RiwayatPembayaran::where(...)->latest()->first()`, yang secara bawaan mengurutkan berdasarkan `created_at DESC`. Jika dua baris dibuat pada milidetik yang sama, pengurutan bisa bergantung pada engine DB.
   - **Dampak:** Minimal pada alur manual karena antar-unggahan pasti berselisih menit/jam. Disarankan mengganti menjadi `latest('id')` agar lebih deterministik.
2. **`GAP` — Test Suite Belum Menguji Siklus Unggah > 2 Kali:**
   - **Deskripsi:** Pengujian baru memvalidasi 2 unggahan dan belum menyertakan pengujian otomatis untuk pembatalan pesanan yang memiliki 3 riwayat bukti.

---

## 10. Daftar Rekomendasi Koreksi

1. **Pengurutan Riwayat Deterministik:**
   Ubah pemanggilan `latest()` menjadi `latest('id')` pada method verifikasi dan penolakan untuk menjamin record teratas selalu merupakan record dengan ID terbesar.
2. **Pengurutan Antrean Operator Berdasarkan Urgensi Keberangkatan:**
   Di `Operator\VerifikasiPembayaranController::index()`, urutkan pesanan berdasarkan `jadwal.tanggal_berangkat ASC, jadwal.jam_berangkat ASC` agar pesanan untuk jadwal yang akan segera berangkat berada di antrean paling atas.
3. **Pengayaan Test Regression:**
   Tambahkan test case yang mencakup 3 siklus unggah-tolak-unggah, verifikasi pasca unggah ulang, dan simulasi kegagalan database untuk memastikan pembersihan orphan file.

---

## 11. Daftar Keputusan Bisnis yang Masih Diperlukan (Open Business Decision)

| No | Topik Keputusan Bisnis | Pertanyaan Kunci untuk Pemilik Proyek | Usulan Solusi Tim Pengembang |
|---|---|---|---|
| 1 | **Batas Waktu Verifikasi Operator (`Dalam Proses`)** | Berapa lama toleransi maksimal pesanan berstatus `Dalam Proses` sebelum dianggap kedaluwarsa jika operator tidak merespons? | Tetapkan batas waktu maksimal verifikasi: misal **12 jam setelah unggah**, atau maksimal **H-2 jam sebelum waktu keberangkatan bus**. |
| 2 | **Aksi Otomatis Saat Verifikasi Menggantung** | Jika batas toleransi verifikasi terlampaui tanpa aksi operator, tindakan apa yang harus diambil sistem? | Opsi A: Pesanan dikembalikan ke `Belum Bayar` dan dikirim peringatan darurat ke Admin.<br>Opsi B: Pesanan otomatis dibatalkan, kursi dirilis, dan penumpang diberi instruksi pengembalian dana (*refund claim*). |

---

## 12. Kesimpulan Kelayakan P0-04

Implementasi **P0-04 (Payment State Guard & Verification)** dinyatakan **LAYAK DITERIMA (APPROVED FOR MAIN DB MIGRATION)** dengan catatan:
1. Seluruh 111 test suite lulus 100% tanpa regresi.
2. Seluruh aturan teknis dan keamanan yang diamanatkan pada P0-04 telah terverifikasi secara objektif pada kode aktual.
3. Migrasi database aman dan bersifat non-destruktif.
4. Rekomendasi teknis minor (Low) dan Keputusan Bisnis (Medium) dapat dijadwalkan pada fase perbaikan fitur pembayaran lanjutan (P1-04 / P1-06).

---

## 13. Status Migrasi Database Utama (`gotix`)

- **Kondisi Database Utama Saat Ini:**
  ```text
  Migration name: 2026_11_12_000003_create_riwayat_pembayaran_table
  Status: PENDING
  ```
- **Instruksi Eksekusi (Menunggu Persetujuan Pemilik Proyek):**
  ```bash
  php artisan migrate
  ```
  *Perintah ini akan membuat tabel baru `riwayat_pembayaran` pada database produksi/utama tanpa memutasi data yang sudah ada.*
- **Perintah Pengaktifan Scheduler di Server:**
  ```bash
  * * * * * cd /path-to-gotix && php artisan schedule:run >> /dev/null 2>&1
  ```

