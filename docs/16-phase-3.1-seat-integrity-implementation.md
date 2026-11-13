# 16 — Phase 3.1 Seat Reservation Integrity Implementation (`P0-01`)

> **Status Fase:** Selesai (`PHASE 3.1` / `P0-01`) — 12 November 2026.  
> **Prinsip Utama:** *Database enforces integrity. Application handles conflicts. Tests prove both.*

---

## 1. Ringkasan Eksekutif

Pada fase ini, integritas reservasi kursi GOTIX telah berhasil diperbaiki secara menyeluruh pada tingkat basis data MariaDB 10.4.32, framework Laravel 12, dan test suite Pest PHP.

Sebelumnya, constraint `unique_jadwal_nomor_kursi (jadwal_id, nomor_kursi)` secara permanen memblokir pemesanan ulang kursi yang telah berstatus `Dibatalkan`. Solusi baru mengimplementasikan kolom komputasi (*STORED generated column*) `kursi_aktif` dan unique index `unique_jadwal_kursi_aktif (jadwal_id, kursi_aktif)`, sehingga kursi yang dibatalkan dapat dipesan kembali berulang kali tanpa batasan, sementara integritas pencegahan kursi ganda pada status aktif (`Belum Bayar`, `Dalam Proses`, `Lunas`) tetap terjamin secara atomik di level mesin database.

---

## 2. Detail Migrasi Basis Data

### 2.1 File Migrasi Baru
- **Path:** `database/migrations/2026_11_12_000001_update_unique_kursi_on_pesanan_table.php`
- **Tabel:** `pesanan`
- **Sifat:** Non-destruktif, additive & altering tanpa mengubah migrasi historis sebelumnya.

### 2.2 Urutan Operasi `up()`
1. **Penambahan Indeks Mandiri:**
   `$table->index('jadwal_id', 'idx_pesanan_jadwal_id');`
   *Tujuan:* Menjamin foreign key `pesanan_jadwal_id_foreign` tetap memiliki indeks pendukung pada MariaDB/InnoDB saat `unique_jadwal_nomor_kursi` dihapus.
2. **Pelepasan Constraint Lama:**
   `$table->dropUnique('unique_jadwal_nomor_kursi');`
3. **Penambahan Stored Generated Column:**
   `$table->unsignedTinyInteger('kursi_aktif')->storedAs("IF(status = 'Dibatalkan', NULL, nomor_kursi)")->nullable()->after('nomor_kursi');`
4. **Penambahan Constraint Unik Baru:**
   `$table->unique(['jadwal_id', 'kursi_aktif'], 'unique_jadwal_kursi_aktif');`

### 2.3 Perilaku Generated Column `kursi_aktif`
- Jika `status` adalah `Belum Bayar`, `Dalam Proses`, atau `Lunas`: `kursi_aktif` bernilai integer sama dengan `nomor_kursi` (misal: `1`).
- Jika `status` adalah `Dibatalkan`: `kursi_aktif` bernilai `NULL`.
- Karena MariaDB / InnoDB memperlakukan nilai `NULL` sebagai non-distinct pada unique index, banyak baris dengan `kursi_aktif = NULL` diizinkan ada pada `(jadwal_id, NULL)`.
- Sebaliknya, jika ada dua baris dengan `status` aktif mencoba menempati nomor kursi yang sama pada `jadwal_id` yang sama, mesin database melempar error duplikasi (`SQLSTATE 23000` / error code `1062`).

### 2.4 Perubahan Indeks pada Tabel `pesanan` (Sebelum vs Sesudah)

| Nama Indeks | Kolom | Tipe | Sebelum | Sesudah |
|---|---|---|---|---|
| `PRIMARY` | `id` | Unique | Ada | Ada |
| `pesanan_kode_pesanan_unique` | `kode_pesanan` | Unique | Ada | Ada |
| `unique_jadwal_nomor_kursi` | `jadwal_id, nomor_kursi` | Unique | **Ada** | **Dihapus** |
| `idx_pesanan_jadwal_id` | `jadwal_id` | Index biasa | *Tidak ada* | **Dibuat (Mendukung FK)** |
| `unique_jadwal_kursi_aktif` | `jadwal_id, kursi_aktif` | Unique | *Tidak ada* | **Dibuat (Integritas Kursi)** |
| `pesanan_pengguna_id_foreign` | `pengguna_id` | Index biasa | Ada | Ada |

### 2.5 Keterbatasan Rollback & Prosedur Aman `down()`
- Method `down()` memeriksa terlebih dahulu apakah terdapat data pemesanan historis ganda:
  ```php
  $hasDuplicates = DB::table('pesanan')
      ->select('jadwal_id', 'nomor_kursi')
      ->groupBy('jadwal_id', 'nomor_kursi')
      ->havingRaw('COUNT(*) > 1')
      ->exists();
  ```
- **Jika `$hasDuplicates` bernilai true:** Rollback melempar `RuntimeException` secara aman tanpa menghapus data transaksi sedikitpun. Rollback hanya diizinkan melalui pemulihan backup database development (`storage/app/backups/gotix_backup_20261112_151011.sql`).
- **Jika `$hasDuplicates` bernilai false:** Unique index lama `unique_jadwal_nomor_kursi` dipulihkan, lalu indeks sementara `idx_pesanan_jadwal_id` dilepas.

---

## 3. Perubahan Kode Aplikasi

### 3.1 Model `app/Models/Pesanan.php`
- Menambahkan casting `'kursi_aktif' => 'integer'` pada method `casts()`.
- Kolom `kursi_aktif` tidak dimasukkan ke dalam `$fillable` karena dikelola dan dihitung otomatis oleh MariaDB.

### 3.2 Controller Member `app/Http/Controllers/Member/PesananController.php`
- **Serialisasi Konkurensi:** Menambahkan `Jadwal::where('id', $jadwal->id)->lockForUpdate()->first();` di dalam `DB::transaction()` untuk mengunci row `Jadwal` induk dan menghindari kondisi balapan pada kursi yang belum memiliki baris `pesanan` sama sekali.
- **Validasi Jadwal Kadaluwarsa:** Menolak pemesanan jika `tanggal_berangkat < today()`.
- **Konfigurasi Batas Bayar:** Menggunakan `config('gotix.batas_bayar_jam', 24)` (menghilangkan hardcode 24 jam).
- **Graceful Duplicate Handling:** Menangkap `QueryException` dengan kode `23000` / `1062` dan mengembalikan pesan validasi ramah pengguna: `"Kursi yang Anda pilih baru saja dipesan oleh pengguna lain. Silakan pilih kursi lain."`.
- **Structured Error Logging:** Mencatat error query database dan throwable tak terduga via `Log::error(...)` disertai konteks `jadwal_id`, `nomor_kursi`, dan `user_id`.

### 3.3 Controller Operator `app/Http/Controllers/Operator/PesananLoketController.php`
- **Kapasitas Armada Dinamis:** Mengganti validasi hardcode `'max:5'` menjadi `'max:' . ($jadwal->kendaraan?->kapasitas_kursi ?? 5)` sehingga mendukung armada dengan kapasitas lebih besar (misal: HiAce / Minibus).
- **Serialisasi Konkurensi:** Menambahkan `Jadwal::where('id', $jadwal->id)->lockForUpdate()->first();` di dalam `DB::transaction()`.
- **Graceful Duplicate Handling:** Menangkap `QueryException` dan mengembalikan pesan: `"Kursi nomor X baru saja dipesan oleh pengguna lain. Silakan pilih kursi lain."`.
- **Structured Error Logging:** Menambahkan `Log::error(...)` untuk kegagalan sistem.
- *Catatan Scope:* Dukungan walk-in non-Member tidak diubah di sini dan tetap dijadwalkan pada `P1-03`.

---

## 4. Hasil Eksekusi Migrasi & Pengujian

### 4.1 Eksekusi Migrasi pada Basis Data
- **Testing Database (`gotix_testing`):** Otomatis termigrasi dan divalidasi via Pest `RefreshDatabase`.
- **Development Database (`gotix`):**
  - Perintah: `php artisan migrate`
  - Output: `2026_11_12_000001_update_unique_kursi_on_pesanan_table ... 171.74ms DONE`
  - Verifikasi baris eksisting: `ROW: id=1, kode=GTX-WB9AJV, status=Belum Bayar, nomor=1, kursi_aktif=1` (terisi otomatis tanpa konflik).

### 4.2 Test Suite Regresi (`tests/Feature/SeatReservationIntegrityTest.php`)
Dibuat 13 skenario pengujian komprehensif:

1. `user dapat memesan kursi yang tersedia dan kursi_aktif terisi di database` (**PASS**)
2. `membatalkan pesanan mengubah kursi_aktif menjadi NULL dan membebaskan kursi` (**PASS**)
3. `user lain dapat memesan kursi yang sebelumnya dibatalkan` (**PASS**)
4. `kursi yang sama dapat dibatalkan dan dipesan ulang berulang kali` (**PASS**)
5. `database menolak dua pesanan aktif pada kursi dan jadwal yang sama` (**PASS**)
6. `nomor kursi yang sama boleh aktif di jadwal yang berbeda` (**PASS**)
7. `controller member mengembalikan pesan error yang jelas jika kursi sudah dipesan` (**PASS**)
8. `simulasi balapan konkurensi: jika database melempar duplicate key exception, member menerima pesan terkontrol` (**PASS**)
9. `operator loket menerima pesan terkontrol saat kursi sudah terisi` (**PASS**)
10. `operator loket dapat memesan kursi dengan kapasitas dinamis armada` (**PASS**)
11. `member tidak dapat memesan jadwal yang sudah lewat` (**PASS**)
12. `member tidak dapat melihat tiket pesanan milik member lain` (**PASS**)
13. `non-operator dilarang memesan tiket loket` (**PASS**)

### 4.3 Hasil Pengujian Penuh (`php artisan test`)
- **Total Tests:** **47 passed** (sebelumnya 34 passed, +13 test baru)
- **Total Assertions:** **113 assertions** (sebelumnya 68 assertions, +45 assertion baru)
- **Durasi:** `4.72s`
- **Semua suite lulus tanpa kegagalan:** `Unit\ExampleTest`, `Feature\AuthTest`, `Feature\ExampleTest`, `Feature\ModelTest`, `Feature\SeatReservationIntegrityTest`.

### 4.4 Kepatuhan Standar Kode (`vendor/bin/pint --test`)
- Diformat dan diperiksa khusus pada 5 berkas terdampak:
  `{"tool":"pint","result":"passed"}` (100% compliant).

---

## 5. Daftar Berkas Dibuat dan Diubah

### Berkas Baru
1. `database/migrations/2026_11_12_000001_update_unique_kursi_on_pesanan_table.php`
2. `tests/Feature/SeatReservationIntegrityTest.php`
3. `docs/16-phase-3.1-seat-integrity-implementation.md`

### Berkas Diubah
1. `app/Models/Pesanan.php`
2. `app/Http/Controllers/Member/PesananController.php`
3. `app/Http/Controllers/Operator/PesananLoketController.php`
4. `docs/12-remediation-roadmap.md`

