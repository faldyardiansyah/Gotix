# GOTIX — DOKUMEN KESIAPAN MIGRASI DATABASE UTAMA (PHASE P1 FINAL)

> **Status Dokumen:** SIAP EKSEKUSI (Menunggu Persetujuan Pemilik Proyek)  
> **Tanggal Penyusunan:** 13 November 2026  
> **Target DBMS:** MariaDB 10.4.32 / MySQL 8.0 (Database Utama `gotix`)  
> **Host / Port:** `127.0.0.1:3306`

---

## 1. Latar Belakang & Tujuan

Dua migrasi baru telah dibuat dan diuji secara komprehensif pada database pengujian terisolasi (`gotix_testing`). Kedua migrasi ini bertujuan untuk menyelesaikan backlog inti Phase P1 yang sebelumnya tertunda:
1. **Pemesanan Loket untuk Penumpang Walk-in Tanpa Akun Member** (`2026_11_13_000001_add_walkin_passenger_to_pesanan_table.php`).
2. **Perluasan Karakter Deskripsi Keluhan & Penambahan Kategori Armada** (`2026_11_13_000002_update_keluhan_deskripsi_and_kategori_table.php`).

Dokumen ini memverifikasi integritas struktural, kompatibilitas data lama, analisis risiko foreign key, serta rencana pencadangan (backup) dan pemulihan (rollback) sebelum migrasi diterapkan ke database produksi/utama `gotix`.

---

## 2. Rincian Migration File

### Migration 1: Walk-in Passenger
- **File:** `database/migrations/2026_11_13_000001_add_walkin_passenger_to_pesanan_table.php`
- **Operasi Database:**
  1. `ALTER TABLE pesanan DROP FOREIGN KEY pesanan_pengguna_id_foreign;`
  2. `ALTER TABLE pesanan MODIFY COLUMN pengguna_id BIGINT UNSIGNED NULL;`
  3. `ALTER TABLE pesanan ADD COLUMN nama_penumpang VARCHAR(100) NULL AFTER pengguna_id;`
  4. `ALTER TABLE pesanan ADD COLUMN telepon_penumpang VARCHAR(20) NULL AFTER nama_penumpang;`
  5. `ALTER TABLE pesanan ADD COLUMN dibuat_oleh_operator_id BIGINT UNSIGNED NULL AFTER telepon_penumpang;`
  6. `ALTER TABLE pesanan ADD CONSTRAINT pesanan_pengguna_id_foreign FOREIGN KEY (pengguna_id) REFERENCES pengguna (id) ON DELETE RESTRICT;`
  7. `ALTER TABLE pesanan ADD CONSTRAINT pesanan_dibuat_oleh_operator_id_foreign FOREIGN KEY (dibuat_oleh_operator_id) REFERENCES pengguna (id) ON DELETE RESTRICT;`
- **Kompatibilitas Data Lama:**
  Seluruh baris transaksi `pesanan` yang sudah ada tetap memiliki `pengguna_id` yang valid; kolom `nama_penumpang`, `telepon_penumpang`, dan `dibuat_oleh_operator_id` akan bernilai `NULL` (tidak merusak rekaman historis).
- **Prosedur Rollback (`down`):**
  Menghapus kedua foreign key, menghapus 3 kolom baru, mengembalikan `pengguna_id` menjadi `NOT NULL`, dan memasang kembali FK `RESTRICT`.

### Migration 2: Keluhan Schema Update
- **File:** `database/migrations/2026_11_13_000002_update_keluhan_deskripsi_and_kategori_table.php`
- **Operasi Database:**
  1. `ALTER TABLE keluhan MODIFY COLUMN deskripsi TEXT NOT NULL;`
  2. `ALTER TABLE keluhan MODIFY COLUMN kategori ENUM('Akun', 'Pesanan', 'Pembayaran', 'Pelayanan', 'Armada', 'Lainnya') NOT NULL;`
- **Kompatibilitas Data Lama:**
  Kolom `deskripsi` bertambah kapasitas dari `VARCHAR(200)` menjadi `TEXT` tanpa memotong string yang ada. Nilai enum eksisting tetap valid dan urutan nilai lama dipertahankan.
- **Prosedur Rollback (`down`):**
  Mengembalikan enum ke 5 nilai awal dan mengubah kolom `deskripsi` kembali ke `VARCHAR(200)`.

---

## 3. Hasil Pengujian pada `gotix_testing`

Seluruh 145 pengujian Pest PHP (510 assertions) telah dieksekusi dan lulus 100%:
- **Pemesanan Walk-in:** Berhasil tanpa akun member (`pengguna_id = null`), nama & telepon tersimpan, operator tercatat via `dibuat_oleh_operator_id`.
- **Validasi Walk-in:** Operator wajib mengisi nama dan telepon jika `pengguna_id` tidak dipilih.
- **E-Tiket & Boarding Pass:** Mengakomodasi identitas penumpang walk-in secara akurat pada manifest dan QR Code.
- **Keluhan:** Kategori `Armada` dan deskripsi hingga 1.000 karakter tersimpan sempurna di database.
- **Regression Guard:** Integritas kursi P0-01, soft deletes P0-02, private proof P0-03, verifikasi pembayaran P0-04, dan rate limiting P0-05 tetap hijau.

---

## 4. Rencana Cadangan (Backup) & Rollback Database Utama

### A. Perintah Backup Sebelum Eksekusi Migrasi
Sebelum menjalankan `php artisan migrate` pada database `gotix`, jalankan mysqldump:
```bash
mysqldump -u root -p gotix --routines --triggers --single-transaction > storage/app/backups/gotix_backup_pre_p1_final.sql
```

### B. Prosedur Rollback Darurat (Jika Terjadi Kegagalan)
Jika terjadi kendala saat eksekusi:
```bash
php artisan migrate:rollback --step=2
```
Atau pulihkan snapshot database:
```bash
mysql -u root -p gotix < storage/app/backups/gotix_backup_pre_p1_final.sql
```

---

## 5. Status Kesiapan

- **Koneksi Target:** Database `gotix` (saat ini Batch 4, belum dimodifikasi).
- **Status Migrasi Saat Ini:** PENDING untuk kedua berkas migrasi baru.
- **Sikap Agen:** **BERHENTI**. Sesuai aturan keselamatan database `AGENTS.md`, agen menunggu konfirmasi eksplisit dari pemilik proyek sebelum mengeksekusi migrasi pada database utama `gotix`.

