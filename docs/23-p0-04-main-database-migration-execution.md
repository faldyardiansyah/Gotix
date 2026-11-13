# 23 — P0-04 Main Database Migration Execution Report

> **Tujuan Dokumen:** Laporan Eksekusi Resmi Migrasi Tabel `riwayat_pembayaran` ke Database Utama `gotix`  
> **Target Migrasi:** `database/migrations/2026_11_12_000003_create_riwayat_pembayaran_table.php`  
> **Waktu Eksekusi:** 12 November 2026, 21:24:28 WIB  
> **Status Akhir:** **BERHASIL PENUH & TERVERIFIKASI (MIGRATION EXECUTED & VERIFIED)**

---

## 1. Waktu Pelaksanaan Migrasi

- **Hari / Tanggal:** Kamis, 12 November 2026
- **Waktu Mulai Eksekusi:** 21:24:28 WIB
- **Waktu Selesai Verifikasi:** 21:30:51 WIB
- **Durasi Eksekusi Migrasi:** 260.52 milidetik

---

## 2. Database Target yang Diverifikasi

Pemeriksaan runtime memastikan koneksi Laravel aktif mengarah langsung ke database utama pengujian dan operasional, bukan basis data lainnya:

- **Target Database Host:** `127.0.0.1`
- **Port:** `3306`
- **DBMS:** MariaDB `10.4.32`
- **Nama Database Target:** `gotix`
- **Koneksi Terverifikasi via Runtime:**
  ```text
  DB::connection()->getDatabaseName() = "gotix"
  ```
- **Karakteristik & Engine Default:** `InnoDB`, `utf8mb4_unicode_ci`

---

## 3. Status Sebelum Migrasi

Sebelum perintah migrasi dijalankan, seluruh prasyarat verifikasi telah dipenuhi:

1. **Status Migrasi pada Database Utama:**
   ```text
   2026_11_12_000003_create_riwayat_pembayaran_table ................................................... Pending
   ```
2. **Keberadaan Tabel Sebelum Eksekusi:**
   - Kueri `information_schema.tables` untuk `TABLE_NAME = 'riwayat_pembayaran'` mengembalikan hasil: `TIDAK ADA` (0 baris).
3. **Ketersediaan Backup Database Utama:**
   - File backup: `storage/app/backups/gotix_backup_20261112_211800.sql`
   - Ukuran file: `22,881 bytes`
   - Uji pemulihan: Berhasil diuji coba pada database sementara `gotix_restore_test` dengan 100% kesesuaian data.
4. **Kondisi Data Awal Database `gotix`:**
   - `pengguna`: 3 baris
   - `kota`: 27 baris
   - `kendaraan`: 2 baris
   - `jadwal`: 6 baris
   - `pesanan`: 1 baris (`GTX-WB9AJV`, status: `Belum Bayar`, kursi: 1, `kursi_aktif`: 1)
   - `konfirmasi_pembayaran`: 0 baris
   - `keluhan`: 0 baris

---

## 4. Perintah yang Dijalankan

Berdasarkan otorisasi eksplisit pemilik proyek, perintah migrasi resmi Laravel dijalankan di direktori root `D:\SEMESTER 5\Pemeliharaan Perangkat Lunak\Gotix`:

```bash
php artisan migrate
```

*Catatan: Perintah dijalankan secara non-destruktif dan terisolasi tanpa opsi `--fresh`, `--force`, atau perintah rollback.*

---

## 5. Hasil Eksekusi Migrasi

Output dari eksekusi perintah migrasi tercatat secara presisi sebagai berikut:

```text
   INFO  Running migrations.

  2026_11_12_000003_create_riwayat_pembayaran_table .................................................... 260.52ms DONE
```

Status migrasi setelah eksekusi (`php artisan migrate:status`):
```text
  Migration name ...................................................................................... Batch / Status  
  0001_01_01_000000_create_pengguna_table .................................................................... [1] Ran  
  0001_01_01_000001_create_cache_table ....................................................................... [1] Ran  
  0001_01_01_000002_create_jobs_table ........................................................................ [1] Ran  
  2026_03_14_000001_create_kota_table ........................................................................ [1] Ran  
  2026_03_14_000002_create_kendaraan_table ................................................................... [1] Ran  
  2026_03_14_000003_create_jadwal_table ...................................................................... [1] Ran  
  2026_03_14_000004_create_pesanan_table ..................................................................... [1] Ran  
  2026_03_14_000005_create_konfirmasi_pembayaran_table ....................................................... [1] Ran  
  2026_03_14_000006_create_keluhan_table ..................................................................... [1] Ran  
  2026_11_12_000001_update_unique_kursi_on_pesanan_table ..................................................... [2] Ran  
  2026_11_12_000002_protect_transaction_history_and_soft_deletes ............................................. [3] Ran  
  2026_11_12_000003_create_riwayat_pembayaran_table .......................................................... [4] Ran  
```

---

## 6. Verifikasi Struktur Tabel dan Foreign Key

Pemeriksaan mendalam terhadap tabel fisik baru `riwayat_pembayaran` di database `gotix` melalui kueri katalog sistem MariaDB (`information_schema`):

### A. Definisi Kolom & Tipe Data Fisik
| Kolom | Tipe Data | Nullable | Default | Extra |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | NULL | `auto_increment` |
| `pesanan_id` | `bigint(20) unsigned` | NO | NULL | |
| `konfirmasi_pembayaran_id` | `bigint(20) unsigned` | YES | NULL | |
| `pengguna_id` | `bigint(20) unsigned` | NO | NULL | |
| `nomor_resi` | `varchar(255)` | NO | NULL | |
| `rekening_pengirim` | `varchar(255)` | NO | NULL | |
| `tanggal_transfer` | `date` | NO | NULL | |
| `jam_transfer` | `time` | NO | NULL | |
| `bukti_path` | `varchar(255)` | NO | NULL | |
| `status` | `varchar(255)` | NO | `'Dalam Proses'` | |
| `diverifikasi_oleh` | `bigint(20) unsigned` | YES | NULL | |
| `diverifikasi_pada` | `datetime` | YES | NULL | |
| `alasan_penolakan` | `text` | YES | NULL | |
| `created_at` | `timestamp` | YES | NULL | |
| `updated_at` | `timestamp` | YES | NULL | |

### B. Foreign Key Constraints
| Nama Constraint | Kolom Lokal | Tabel Referensi | Kolom Referensi | ON DELETE | ON UPDATE |
|---|---|---|---|---|---|
| `riwayat_pembayaran_pesanan_id_foreign` | `pesanan_id` | `pesanan` | `id` | `RESTRICT` | `CASCADE` |
| `riwayat_pembayaran_pengguna_id_foreign` | `pengguna_id` | `pengguna` | `id` | `RESTRICT` | `CASCADE` |
| `riwayat_pembayaran_konfirmasi_pembayaran_id_foreign` | `konfirmasi_pembayaran_id` | `konfirmasi_pembayaran` | `id` | `SET NULL` | `CASCADE` |
| `riwayat_pembayaran_diverifikasi_oleh_foreign` | `diverifikasi_oleh` | `pengguna` | `id` | `SET NULL` | `CASCADE` |

### C. Pengindeksan (*Indexes*)
1. `PRIMARY`: `PRIMARY KEY (id)`
2. `riwayat_pembayaran_pesanan_id_foreign`: `INDEX (pesanan_id)`
3. `riwayat_pembayaran_konfirmasi_pembayaran_id_foreign`: `INDEX (konfirmasi_pembayaran_id)`
4. `riwayat_pembayaran_pengguna_id_foreign`: `INDEX (pengguna_id)`
5. `riwayat_pembayaran_diverifikasi_oleh_foreign`: `INDEX (diverifikasi_oleh)`
6. `riwayat_pembayaran_pesanan_id_created_at_index`: `INDEX (pesanan_id, created_at)` *(Composite Index)*

Semua constraint telah dikonfirmasi aktif pada mesin penyimpanan `InnoDB`.

---

## 7. Hasil Pemeriksaan Jumlah Data dan Integritas Transaksi

Audit perbandingan baris data sebelum dan sesudah migrasi pada database `gotix`:

| Tabel | Sebelum Migrasi | Sesudah Migrasi | Delta | Keterangan |
|---|---|---|---|---|
| `pengguna` | 3 | 3 | 0 | Master pengguna utuh |
| `kota` | 27 | 27 | 0 | Master kota Lampung utuh |
| `kendaraan` | 2 | 2 | 0 | Master armada bus utuh |
| `jadwal` | 6 | 6 | 0 | Master jadwal utuh |
| `pesanan` | 1 | 1 | 0 | Transaksi riil `GTX-WB9AJV` utuh |
| `konfirmasi_pembayaran` | 0 | 0 | 0 | Bersih |
| `riwayat_pembayaran` | 0 (tabel belum ada) | 0 (tabel baru aktif) | 0 | Siap menampung histori pembayaran |
| `keluhan` | 0 | 0 | 0 | Bersih |

Detail transaksi `pesanan` tunggal pada database `gotix`:
- **Kode Pesanan:** `GTX-WB9AJV`
- **Status:** `Belum Bayar`
- **Nomor Kursi:** `1`
- **Kursi Aktif:** `1` (Generated column integritas P0-01 tetap terjaga sempurna)

Tidak ada data yang termutasi, terhapus, terkorupsi, atau terputus relasinya.

---

## 8. Hasil Pemeriksaan Aplikasi dan Test Suite

1. **Uji Khusus Pembayaran (`PaymentVerificationSecurityTest`):**
   ```text
   PASS  Tests\Feature\PaymentVerificationSecurityTest
   Tests:    14 passed (84 assertions)
   Duration: 3.36s
   ```
2. **Uji Regresi Penuh (`php artisan test`):**
   ```text
   PASS  Tests\Unit\ExampleTest (1 test)
   PASS  Tests\Feature\AuthTest (18 tests)
   PASS  Tests\Feature\ExampleTest (1 test)
   PASS  Tests\Feature\ModelTest (14 tests)
   PASS  Tests\Feature\PaymentProofSecurityTest (30 tests)
   PASS  Tests\Feature\PaymentVerificationSecurityTest (14 tests)
   PASS  Tests\Feature\SeatReservationIntegrityTest (13 tests)
   PASS  Tests\Feature\TransactionHistoryProtectionTest (17 tests)

   Tests:    111 passed (355 assertions)
   Duration: 6.91s
   ```
3. **Frontend Asset Build (`npm run build`):**
   ```text
   ✓ 62 modules transformed.
   public/build/manifest.json              0.33 kB │ gzip:  0.17 kB
   public/build/assets/app-ZkTp0JxQ.css   62.37 kB │ gzip: 10.96 kB
   public/build/assets/app-DDpvETUX.js   114.55 kB │ gzip: 41.40 kB
   ✓ built in 1.74s
   ```

---

## 9. Kondisi Akhir Database (`gotix`)

- **Status Skema:** Terkini (12 dari 12 berkas migrasi berhasil dijalankan / `Batch 4`).
- **Integritas Konkurensi & Kursi (P0-01):** Terlindungi (`unique_jadwal_kursi_aktif`).
- **Proteksi Histori & Soft Delete (P0-02 / P0-02.1):** Terlindungi (`restrictOnDelete` pada tabel master).
- **Keamanan Penyimpanan Bukti Pembayaran (P0-03):** Terlindungi (Private disk `local` + streaming controller RBAC).
- **Status Guard & Histori Pembayaran (P0-04):** Terlindungi (`riwayat_pembayaran` aktif dengan composite index dan audit logging).

---

## 10. Status P0-04: SELESAI PENUH

Tahap **P0-04: Payment State Guard & Verification** kini dinyatakan **SELESAI PENUH (COMPLETED IN FULL)** di seluruh lapisan:
1. Skema Database (`riwayat_pembayaran`) aktif pada database utama `gotix`.
2. Model Eloquent & Relasi Histori telah terpasang.
3. Controller Member, Operator, dan Admin telah terintegrasi dengan validasi dan audit trail.
4. Scheduled Task / Artisan Command Expiry (`gotix:cancel-expired-orders`) siap dijalankan.
5. Seluruh 111 pengujian otomatis lulus (100% PASS).
6. Aset antarmuka telah dikompilasi ulang dengan sukses.

