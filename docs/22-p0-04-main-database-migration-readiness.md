# 22 — P0-04 Main Database Migration Readiness Report

> **Tujuan Dokumen:** Verifikasi Keamanan Sebelum Penerapan Migrasi ke Database Utama `gotix`  
> **Target Migrasi:** `database/migrations/2026_11_12_000003_create_riwayat_pembayaran_table.php`  
> **Tanggal Pemeriksaan:** 12 November 2026  
> **Status Tindakan:** **PERSIAPAN SELESAI (DATABASE UTAMA BELUM DIMIGRASI / PENDING)**

---

## 1. Status Migrasi & Target Database

Pemeriksaan status koneksi dan migrasi aktif memastikan bahwa lingkungan kerja terhubung secara tepat ke database pengembangan/utama, bukan database testing:

- **Target Database Terkonfigurasi (`.env`):** `gotix`
- **Host / Port:** `127.0.0.1:3306` (MariaDB 10.4.32)
- **Koneksi Aktif Terverifikasi (PHP Runtime):** `gotix`
- **Status Migrasi pada Database Utama (`gotix`):**
  ```text
  Migration name: 2026_11_12_000003_create_riwayat_pembayaran_table
  Status: PENDING
  ```
- **Keberadaan Tabel `riwayat_pembayaran` di Database Utama:** **`TIDAK ADA (NO)`** (Tabel belum pernah dibuat di database utama).

---

## 2. Kondisi & Integritas Data Transaksi Saat Ini (`gotix`)

Dilakukan audit kuantitatif terhadap jumlah baris dan integritas data pada database utama sebelum migrasi:

| Tabel | Jumlah Baris | Keterangan Integritas |
|---|---|---|
| `pengguna` | 3 baris | Akun resmi Admin, Operator Loket, dan Member |
| `kota` | 27 baris | Data master kota/kabupaten se-Lampung (`KotaSeeder`) |
| `kendaraan` | 2 baris | Master armada bus operasional |
| `jadwal` | 6 baris | Master jadwal keberangkatan aktif dan lampau |
| `pesanan` | 1 baris | Transaksi riil (`GTX-WB9AJV`, status: `Belum Bayar`, kursi: 1, `kursi_aktif`: 1) |
| `konfirmasi_pembayaran` | 0 baris | Belum ada rekaman konfirmasi pembayaran manual |
| `keluhan` | 0 baris | Belum ada rekaman keluhan |

Seluruh data transaksi dan master data berada dalam kondisi utuh, tanpa baris yatim (*orphan records*) dan tanpa anomali relasi.

---

## 3. Hasil Pemeriksaan Skema & Constraint Migrasi

Pemeriksaan isi berkas migrasi `2026_11_12_000003_create_riwayat_pembayaran_table.php`:

### A. Definisi Kolom & Tipe Data
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `pesanan_id`: `BIGINT UNSIGNED NOT NULL` (Foreign Key ke `pesanan.id`)
- `konfirmasi_pembayaran_id`: `BIGINT UNSIGNED NULL` (Foreign Key ke `konfirmasi_pembayaran.id`)
- `pengguna_id`: `BIGINT UNSIGNED NOT NULL` (Foreign Key ke `pengguna.id`)
- `nomor_resi`: `VARCHAR(255) NOT NULL`
- `rekening_pengirim`: `VARCHAR(255) NOT NULL`
- `tanggal_transfer`: `DATE NOT NULL`
- `jam_transfer`: `TIME NOT NULL`
- `bukti_path`: `VARCHAR(255) NOT NULL`
- `status`: `VARCHAR(255) NOT NULL DEFAULT 'Dalam Proses'`
- `diverifikasi_oleh`: `BIGINT UNSIGNED NULL` (Foreign Key ke `pengguna.id`)
- `diverifikasi_pada`: `DATETIME NULL`
- `alasan_penolakan`: `TEXT NULL`
- `created_at`, `updated_at`: `TIMESTAMP NULL`

### B. Foreign Key & Sifat Integritas Data
1. `pesanan_id`: Menggunakan `restrictOnDelete()`. Mencegah hard-delete terhadap pesanan yang memiliki rekam jejak pembayaran.
2. `pengguna_id`: Menggunakan `restrictOnDelete()`. Mencegah hard-delete terhadap akun pengguna yang pernah mengunggah bukti pembayaran.
3. `konfirmasi_pembayaran_id`: Menggunakan `nullOnDelete()`.
4. `diverifikasi_oleh`: Menggunakan `nullOnDelete()`.

### C. Pengindeksan (*Indexes*)
- Indeks Primer: `PRIMARY KEY (id)`
- Indeks Foreign Key: Dibuat otomatis oleh InnoDB MariaDB pada tiap foreign key.
- Indeks Komposit: `INDEX idx_riwayat_pesanan_created (pesanan_id, created_at)` untuk mempercepat penelusuran riwayat per pesanan berdasarkan kronologi unggah.

### D. Jaminan Tanpa Overwrite
Migrasi ini bersifat **murni aditif** (`Schema::create('riwayat_pembayaran', ...)`). Tidak ada perintah `DROP TABLE`, `DROP COLUMN`, `RENAME`, atau `MODIFY` terhadap tabel lain.

---

## 4. Lokasi & Verifikasi Backup Database Utama

Sebelum persetujuan eksekusi, backup penuh database utama telah dibuat menggunakan utilitas native `mysqldump` dengan flag transaksional:

- **Perintah Eksekusi Backup:**
  `mysqldump -u root -h 127.0.0.1 -P 3306 --single-transaction --routines --triggers --hex-blob gotix`
- **Lokasi File Backup:**
  [storage/app/backups/gotix_backup_20261112_211800.sql](file:///d:/SEMESTER%205/Pemeliharaan%20Perangkat%20Lunak/Gotix/storage/app/backups/gotix_backup_20261112_211800.sql)
- **Ukuran File Backup:** `22,881 bytes` (Valid, non-empty, berisi seluruh DDL skema dan DML rekaman data aktual).
- **Hasil Verifikasi Header File:**
  ```text
  -- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
  -- Host: 127.0.0.1    Database: gotix
  ```

---

## 5. Uji Pemulihan Backup (Restore Test) pada Basis Data Terisolasi

Untuk memastikan file backup tidak korup dan dapat dipulihkan kapan saja bila terjadi keadaan darurat, dilakukan uji pemulihan nyata pada database sementara:

1. **Pembuatan Database Sementara:** `gotix_restore_check`
2. **Restorasi File SQL:** File `gotix_backup_20261112_211800.sql` di-pipe ke perintah `mysql.exe` untuk mengimpor seluruh skema dan data.
3. **Hasil Audit Restorasi:**
   - `pengguna`: 3 baris (Cocok 100%)
   - `kota`: 27 baris (Cocok 100%)
   - `kendaraan`: 2 baris (Cocok 100%)
   - `jadwal`: 6 baris (Cocok 100%)
   - `pesanan`: 1 baris (`kode_pesanan: GTX-WB9AJV`, `kursi_aktif: 1`, `status: Belum Bayar`) (Cocok 100%)
   - `konfirmasi_pembayaran`: 0 baris (Cocok 100%)
   - `keluhan`: 0 baris (Cocok 100%)
4. **Pembersihan:** Database `gotix_restore_check` segera dihapus (*drop database*) setelah verifikasi berhasil.
5. **Kesimpulan Uji Pemulihan:** **LULUS (100% Fidelity & Restorable)**.

---

## 6. Prosedur & Risiko Rollback

### A. Prosedur Rollback Migrasi
Jika di masa mendatang migrasi perlu dibatalkan:
```bash
php artisan migrate:rollback --step=1
```
- Method `down()` pada migrasi ini hanya memanggil `Schema::dropIfExists('riwayat_pembayaran')`.
- Eksekusi rollback **tidak akan menyentuh** tabel transaksi utama `pesanan` maupun tabel master data lainnya.
- Data `pesanan` dan `konfirmasi_pembayaran` tetap utuh.

### B. Prosedur Pemulihan Darurat (Disaster Recovery)
Jika terjadi anomali tak terduga, database utama dapat dipulihkan ke titik tepat sebelum migrasi menggunakan file backup terverifikasi:
```bash
mysql -u root -h 127.0.0.1 -P 3306 gotix < "storage/app/backups/gotix_backup_20261112_211800.sql"
```

---

## 7. Penilaian Risiko Migrasi

| Faktor Risiko | Tingkat Risiko | Penanganan & Mitigasi |
|---|---|---|
| **Downtime Layanan** | **Rendah (Low)** | Operasi `CREATE TABLE` pada MariaDB InnoDB berlangsung secara instan (< 0.1 detik). |
| **Kunci Tabel / Blocking** | **Nol (None)** | Tidak ada operasi `ALTER TABLE` pada tabel `pesanan` yang sedang aktif. |
| **Kehilangan Data Transaksi** | **Nol (None)** | Migrasi murni membuat tabel baru. Tidak ada data yang dihapus atau diubah. |
| **Konflik Foreign Key** | **Nol (None)** | Foreign key merujuk ke primary key yang valid (`pesanan.id` dan `pengguna.id`). |

---

## 8. Verifikasi Imutabilitas Database Utama

Pemeriksaan ulang setelah seluruh uji coba persiapan selesai:
- Database utama `gotix` **TIDAK MENGALAMI PERUBAHAN APAPUN**.
- Jumlah baris pada seluruh tabel tetap persis sama seperti sebelum persiapan.
- Migrasi `2026_11_12_000003_create_riwayat_pembayaran_table` tetap berstatus **`Pending`**.

---

## 9. Rekomendasi Eksekusi

Berdasarkan seluruh hasil pemeriksaan teknis di atas:
1. Migrasi `2026_11_12_000003_create_riwayat_pembayaran_table.php` dinyatakan **SANGAT AMAN (READY & SAFE TO MIGRATE)** untuk diterapkan pada database utama `gotix`.
2. Perintah eksekusi yang akan dijalankan setelah persetujuan pemilik proyek:
   ```bash
   php artisan migrate
   ```
3. Pekerjaan dihentikan pada tahap ini untuk menunggu konfirmasi eksekusi dari pemilik proyek.

