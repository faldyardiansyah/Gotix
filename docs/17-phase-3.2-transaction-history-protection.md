# 17 — Phase 3.2 Transaction History Protection (`P0-02`)

> **Status Fase:** Selesai (`PHASE 3.2` / `P0-02`) — 12 November 2026.  
> **Prinsip Utama:** *Master data boleh dinonaktifkan/pensiun, namun bukti riwayat transaksi finansial, tiket, pembayaran, dan keluhan penumpang tidak boleh hilang atau rusak.*

---

## 1. Temuan Audit Awal & Analisis Risiko

Sebelum implementasi Phase 3.2, audit komprehensif terhadap basis data MariaDB 10.4.32 dan migrasi awal menemukan bahwa seluruh relasi master data ke jadwal dan transaksi pesanan dikonfigurasi dengan aturan penghapusan kaskade (`ON DELETE CASCADE`):

1. **Rantai Kaskade Penghapusan Kota (`kota`):**
   Jika Admin menghapus entitas `kota`, MySQL/MariaDB secara otomatis menghapus seluruh baris `jadwal` (`kota_asal_id` atau `kota_tujuan_id`), yang selanjutnya menghapus seluruh tiket `pesanan` terkait, dan menghapus seluruh bukti transfer `konfirmasi_pembayaran`.
2. **Rantai Kaskade Penghapusan Kendaraan (`kendaraan`):**
   Jika armada dihapus, seluruh `jadwal` armada tersebut terhapus bersama seluruh `pesanan` dan bukti bayar.
3. **Rantai Kaskade Penghapusan Jadwal (`jadwal`):**
   Jika jadwal keberangkatan dihapus, seluruh pesanan tiket dan rekam jejak pembayaran lenyap.
4. **Rantai Kaskade Penghapusan Pengguna (`pengguna`):**
   Jika akun member dihapus, riwayat pesanan tiket, bukti pembayaran, dan pengaduan keluhan terhapus kaskade.
5. **Audit Jejak Verifikator & Petugas (`konfirmasi_pembayaran` & `keluhan`):**
   Sebelumnya menggunakan `ON DELETE SET NULL`, yang dapat mengosongkan identitas petugas yang memverifikasi transaksi atau membalas keluhan jika akun staf dihapus permanen.

Seluruh data transaksi di `gotix` diaudit via query read-only: ditemukan 0 record yatim (*orphan*), 1 baris pesanan aktif utuh, dan 0 konflik integritas referensial.

---

## 2. Matriks Proteksi Relasi Basis Data (Relationship Protection Matrix)

| Tabel Induk (*Parent*) | Tabel Anak (*Child*) | Nama Foreign Key | Perilaku Hapus Lama | Risiko Riwayat Transaksi | Kebijakan Proteksi Terpilih | Alasan & Justifikasi Semantik |
|---|---|---|---|---|---|---|
| `kota` | `jadwal` | `jadwal_kota_asal_id_foreign` | `CASCADE` | **CRITICAL** (Hapus kota memicu lenyapnya seluruh tiket & bukti bayar) | **RESTRICT** + `SoftDeletes` | Kota adalah data referensi geografis perjalanan. Tidak boleh dihapus paksa jika pernah melayani jadwal. |
| `kota` | `jadwal` | `jadwal_kota_tujuan_id_foreign` | `CASCADE` | **CRITICAL** (Sama seperti kota asal) | **RESTRICT** + `SoftDeletes` | Sama seperti kota asal. |
| `kendaraan` | `jadwal` | `jadwal_kendaraan_id_foreign` | `CASCADE` | **CRITICAL** (Hapus armada memicu penghapusan jadwal dan transaksi tiket) | **RESTRICT** + `SoftDeletes` | Armada fisik dapat dipensiunkan/dinonaktifkan via soft delete tanpa memusnahkan riwayat trip. |
| `jadwal` | `pesanan` | `pesanan_jadwal_id_foreign` | `CASCADE` | **CRITICAL** (Hapus jadwal langsung menghapus pesanan finansial) | **RESTRICT** + `SoftDeletes` | Jadwal keberangkatan adalah jangkar utama tiket reservasi. Wajib dilindungi dari penghapusan keras. |
| `pengguna` | `pesanan` | `pesanan_pengguna_id_foreign` | `CASCADE` | **CRITICAL** (Hapus akun member melenyapkan riwayat e-tiket & pembayaran) | **RESTRICT** + `SoftDeletes` | Akun pemesan tiket wajib dipertahankan; penonaktifan akun menggunakan soft delete. |
| `pesanan` | `konfirmasi_pembayaran` | `konfirmasi_pembayaran_pesanan_id_foreign` | `CASCADE` | **HIGH** (Penghapusan pesanan menghapus bukti bayar) | **RESTRICT** | Pesanan tidak boleh dihapus selama bukti pembayaran valid masih tercatat. |
| `pengguna` | `konfirmasi_pembayaran` | `konfirmasi_pembayaran_diverifikasi_oleh_foreign` | `SET NULL` | **MEDIUM** (Menghilangkan jejak staf verifikator transaksi keuangan) | **RESTRICT** + `SoftDeletes` | Jejak audit siapa petugas loket/admin yang memverifikasi uang transfer wajib kekal. |
| `pengguna` | `keluhan` | `keluhan_pengguna_id_foreign` | `CASCADE` | **HIGH** (Hapus pengguna melenyapkan riwayat komplain layanan) | **RESTRICT** + `SoftDeletes` | Komplain pelanggan harus tetap tersimpan untuk keperluan evaluasi layanan. |
| `pengguna` | `keluhan` | `keluhan_dibalas_oleh_foreign` | `SET NULL` | **MEDIUM** (Menghilangkan jejak admin yang merespons komplain) | **RESTRICT** + `SoftDeletes` | Jejak staf yang membalas pengaduan tetap tersimpan. |
| `pesanan` | `keluhan` | `keluhan_pesanan_id_foreign` | `SET NULL` | **LOW** (Keluhan umum dapat berdiri tanpa tiket spesifik) | **SET NULL** (Dipertahankan) | Keluhan kategori Akun/Pelayanan dapat bernilai `NULL` sejak awal tanpa nomor pesanan. |

---

## 3. Detail Implementasi Migrasi

- **File Migrasi:** `database/migrations/2026_11_12_000002_protect_transaction_history_and_soft_deletes.php`
- **Tindakan `up()`:**
  1. Menambahkan kolom `deleted_at` (`$table->softDeletes()`) ke 4 tabel master data: `kota`, `kendaraan`, `jadwal`, dan `pengguna`.
  2. Melepaskan constraint kaskade lama pada tabel `jadwal` (`jadwal_kota_asal_id_foreign`, `jadwal_kota_tujuan_id_foreign`, `jadwal_kendaraan_id_foreign`) dan membangun ulang sebagai `RESTRICT`.
  3. Melepaskan constraint kaskade lama pada tabel `pesanan` (`pesanan_jadwal_id_foreign`, `pesanan_pengguna_id_foreign`) dan membangun ulang sebagai `RESTRICT`.
  4. Melepaskan constraint lama pada tabel `konfirmasi_pembayaran` (`konfirmasi_pembayaran_pesanan_id_foreign`, `konfirmasi_pembayaran_diverifikasi_oleh_foreign`) dan membangun ulang sebagai `RESTRICT`.
  5. Melepaskan constraint lama pada tabel `keluhan` (`keluhan_pengguna_id_foreign`, `keluhan_dibalas_oleh_foreign`) dan membangun ulang sebagai `RESTRICT`.
- **Pengaman `down()` (Rollback Guard):**
  - Mengembalikan seluruh definisi foreign key ke konfigurasi awal.
  - Memeriksa apakah terdapat data master dalam kondisi `deleted_at IS NOT NULL`.
  - Jika terdapat data soft-deleted, rollback membatalkan diri (`throw new \RuntimeException(...)`) untuk mencegah pemulihan sepihak entitas nonaktif.

---

## 4. Perubahan Kode Model & Controller

### 4.1 Eloquent Models
- **Trait `SoftDeletes` Ditambahkan:**
  - `App\Models\Kota`
  - `App\Models\Kendaraan`
  - `App\Models\Jadwal`
  - `App\Models\Pengguna`
- **Relasi Riwayat Transaksi Diperkaya dengan `->withTrashed()`:**
  - `Pesanan::jadwal()` -> `belongsTo(Jadwal::class, 'jadwal_id')->withTrashed()`
  - `Pesanan::pengguna()` -> `belongsTo(Pengguna::class, 'pengguna_id')->withTrashed()`
  - `Jadwal::kotaAsal()` -> `belongsTo(Kota::class, 'kota_asal_id')->withTrashed()`
  - `Jadwal::kotaTujuan()` -> `belongsTo(Kota::class, 'kota_tujuan_id')->withTrashed()`
  - `Jadwal::kendaraan()` -> `belongsTo(Kendaraan::class, 'kendaraan_id')->withTrashed()`
  - `KonfirmasiPembayaran::verifikator()` -> `belongsTo(Pengguna::class, 'diverifikasi_oleh')->withTrashed()`
  - `Keluhan::pengguna()` -> `belongsTo(Pengguna::class, 'pengguna_id')->withTrashed()`
  - `Keluhan::petugasBalas()` -> `belongsTo(Pengguna::class, 'dibalas_oleh')->withTrashed()`

*Dampak Positif:* E-tiket historis, detail pesanan di dasbor member/admin, dan bukti transfer pembayaran tetap menampilkan nama kota rute, plat nomor armada, serta nama penumpang secara utuh meskipun armada/rute tersebut telah dipensiunkan dari sistem jadwal aktif.

### 4.2 Admin Controllers (Diperbarui pada Phase 3.2.1)
- **`Admin\KotaController::destroy($id)`:**
  Memeriksa jadwal operasional aktif masa depan (`tanggal_berangkat > today` atau `tanggal_berangkat == today && jam_berangkat >= now`). Jika kota masih terdaftar pada rute aktif masa depan, penghapusan ditolak. Namun jika kota hanya memiliki jadwal historis lampau, kota berhasil di-soft delete (`deleted_at` terisi) sehingga tidak muncul pada dropdown pilihan pembuatan jadwal baru, sementara nama kota pada tiket/jadwal historis tetap terbaca utuh via `withTrashed()`.
- **`Admin\KendaraanController::destroy($id)`:**
  Memeriksa apakah armada masih ditugaskan pada jadwal operasional aktif masa depan. Jika armada hanya memiliki riwayat trip masa lampau, armada dapat dipensiunkan via soft-delete secara aman, mengecualikannya dari formulir jadwal baru tanpa merusak nomor polisi pada riwayat tiket.
- **`Admin\JadwalController::destroy($id)`:**
  Membedakan status transaksi aktif vs historis:
  1. *Unresolved Payment Guard:* Menolak penghapusan jika ada pesanan berstatus `Belum Bayar` atau `Dalam Proses` (meskipun tanggal trip telah lewat), demi melindungi hak verifikasi dana/tiket.
  2. *Active Future Passenger Guard:* Menolak penghapusan jika jadwal merupakan keberangkatan masa depan dengan tiket penumpang lunas (`Lunas`), demi mencegah penumpang terlantar.
  3. *Completed / Cancelled Trip Soft-Delete:* Mengizinkan soft-delete jika seluruh pesanan telah `Dibatalkan` atau trip masa lampau telah tuntas (`Lunas`), membersihkan tabel operasional admin tanpa merusak riwayat transaksi.

---

## 5. Verifikasi Backup & Uji Pemulihan (Restore Test)

- **File Backup Terverifikasi:** `storage/app/backups/gotix_backup_20261112_151011.sql` (20,918 bytes).
- **Prosedur Uji Pemulihan:**
  - Dijalankan script scratch `restore_test.php` yang membuat basis data sementara `gotix_restore_test`.
  - Seluruh skrip SQL dump diimpor ke dalam database sementara via multi-statements PDO.
  - Verifikasi tabel berhasil: 15 tabel terpulihkan lengkap (`pesanan`, `jadwal`, `pengguna`, `kota`, `kendaraan`, dll.).
  - Verifikasi data berhasil: `pesanan` = 1 baris, `pengguna` = 3 baris, `jadwal` = 6 baris.
  - Database sementara dihapus bersih (`DROP DATABASE gotix_restore_test`).
  - Hasil: `RESTORE_TEST_SUCCESS=YES`.

---

## 6. Hasil Eksekusi Pengujian Otomatis

### 6.1 Test Suite Khusus `TransactionHistoryProtectionTest.php` (17 Skenario — Phase 3.2.1)
1. `kota dengan riwayat jadwal lampau dapat di-soft-delete secara aman` (**PASS**)
2. `kota yang masih memiliki jadwal aktif masa depan dilarang di-soft-delete` (**PASS**)
3. `kota yang di-soft-delete tidak muncul lagi pada pilihan pembuatan jadwal baru` (**PASS**)
4. `kendaraan dengan riwayat jadwal lampau dapat di-soft-delete secara aman` (**PASS**)
5. `kendaraan yang masih ditugaskan pada jadwal aktif masa depan dilarang di-soft-delete` (**PASS**)
6. `kendaraan yang di-soft-delete tidak muncul lagi pada pilihan pembuatan jadwal baru` (**PASS**)
7. `jadwal masa depan dengan tiket penumpang aktif lunas dilarang di-soft-delete` (**PASS**)
8. `jadwal dengan transaksi pembayaran yang belum selesai dilarang di-soft-delete meski tanggal telah lewat` (**PASS**)
9. `jadwal masa lampau dengan transaksi yang telah tuntas lunas dapat di-soft-delete` (**PASS**)
10. `jadwal yang seluruh pemesanannya dibatalkan dapat di-soft-delete secara aman` (**PASS**)
11. `relasi riwayat transaksi tetap dapat mengakses master data yang di-soft-delete` (**PASS**)
12. `database foreign key RESTRICT menolak hard delete kota yang memiliki jadwal` (**PASS**)
13. `database foreign key RESTRICT menolak hard delete kendaraan yang memiliki jadwal` (**PASS**)
14. `database foreign key RESTRICT menolak hard delete jadwal yang memiliki pesanan` (**PASS**)
15. `database foreign key RESTRICT menolak hard delete pengguna yang memiliki pesanan` (**PASS**)
16. `database foreign key RESTRICT melindungi konfirmasi_pembayaran dan verifikator` (**PASS**)
17. `keluhan dengan pesanan_id yang bernilai null tetap valid sesuai rancangan` (**PASS**)

### 6.2 Test Suite Penuh Repositori (`php artisan test`)
- **Total Tests:** **64 passed** (baseline Phase 3.2: 58 passed, **+6 test granular baru**)
- **Total Assertions:** **178 assertions** (baseline Phase 3.2: 154 assertions, **+24 assertion baru**)
- **Durasi:** `7.32s`
- **Kondisi Suite:** Lulus 100% tanpa ada kegagalan regresi:
  - `Unit\ExampleTest`: 1 passed
  - `Feature\AuthTest`: 18 passed
  - `Feature\ExampleTest`: 1 passed
  - `Feature\ModelTest`: 14 passed
  - `Feature\SeatReservationIntegrityTest`: 13 passed (P0-01 tetap utuh)
  - `Feature\TransactionHistoryProtectionTest`: 17 passed (P0-02 & P0-02.1 tervalidasi)

### 6.3 Standar Kualitas Gaya Kode (`vendor/bin/pint --test`)
- Diperiksa terhadap seluruh berkas modifikasi:
  `{"tool":"pint","result":"passed"}` (100% patuh standar Laravel Pint).

---

## 7. Berkas Dibuat dan Dimodifikasi

### Berkas Baru
1. `database/migrations/2026_11_12_000002_protect_transaction_history_and_soft_deletes.php`
2. `tests/Feature/TransactionHistoryProtectionTest.php`
3. `docs/17-phase-3.2-transaction-history-protection.md`

### Berkas Dimodifikasi
1. `app/Models/Kota.php`
2. `app/Models/Kendaraan.php`
3. `app/Models/Jadwal.php`
4. `app/Models/Pengguna.php`
5. `app/Models/Pesanan.php`
6. `app/Models/KonfirmasiPembayaran.php`
7. `app/Models/Keluhan.php`
8. `app/Http/Controllers/Admin/KotaController.php`
9. `app/Http/Controllers/Admin/KendaraanController.php`
10. `app/Http/Controllers/Admin/JadwalController.php`
11. `docs/12-remediation-roadmap.md`

---

## 8. Risiko Terbuka & Mitigasi

1. **Foreign Key Error pada Raw DB Query Admin:** Jika ada admin atau skrip eksternal yang mengeksekusi `DELETE FROM kota/kendaraan/jadwal/pengguna` secara manual langsung di basis data saat baris tersebut masih memiliki referensi di tabel anak, MariaDB akan melempar `1451 Cannot delete or update a parent row: a foreign key constraint fails`. Hal ini merupakan perilaku yang memang disengaja (*by design*) untuk melindungi integritas transaksi. Mitigasi: Operasi penghapusan melalui aplikasi selalu menggunakan soft-deletes atau pemeriksaan controller.
2. **Rollback Non-Otomatis Jika Ada Data Soft-Deleted:** Jika tabel master telah memiliki baris dengan `deleted_at IS NOT NULL`, rollback migrasi `down()` akan menolak drop kolom `deleted_at` demi keselamatan data. Pemulihan ke status lama harus melalui pemulihan backup SQL jika diperlukan.

