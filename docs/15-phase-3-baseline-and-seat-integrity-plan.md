# 15 — Phase 3.0 Safe Baseline & Seat Integrity Preparation

> **Status Fase:** Persiapan & Verifikasi Baseline (`PHASE 3.0`) — tidak ada perubahan kode aplikasi, controller, model, view, migrasi, atau data development `gotix`.

---

## 1. Status Git & Keselamatan Proyek

- **VERIFIED (`git status` & `git diff --stat` pada Phase 3.0):**
  - Branch aktif: `main` (`Your branch is up to date with 'origin/main'`).
  - Perubahan terdeteksi sebelum pengerjaan Phase 3.0:
    - `modified: package-lock.json` *(pre-existing unstaged change dari pemilik proyek — dipertahankan utuh)*.
    - `modified: .agent/rules/gotix.md` *(sinkronisasi aturan AI pada Phase 2)*.
    - Untracked: `AGENTS.md` *(root governance Phase 2)*, `docs/` *(dokumentasi audit Phase 1 & 2)*.
  - File baru yang dihasilkan pada Phase 3.0: `storage/app/backups/gotix_backup_20261112_151011.sql` *(diabaikan oleh `storage/app/.gitignore`)*, `docs/15-phase-3-baseline-and-seat-integrity-plan.md`, dan pembaruan `docs/12-remediation-roadmap.md`.
  - Tidak ada file yang dihapus dan tidak ada `git commit` maupun `git push` yang dijalankan.

---

## 2. Verifikasi Backup Database & Isolasi Database

### 2.1 Verifikasi Isolasi Konfigurasi Database
- **VERIFIED:**
  - `php artisan config:show database.connections.mysql.database` -> **`gotix`** (database development utama).
  - `php artisan --env=testing config:show database.connections.mysql.database` (selaras dengan `phpunit.xml` & `.env.testing`) -> **`gotix_testing`** (database pengujian terisolasi).
  - Pemeriksaan koneksi langsung via MariaDB (`10.4.32-MariaDB`) memastikan kedua skema berdiri terpisah: `HAS_GOTIX=YES`, `HAS_GOTIX_TESTING=YES` (dibuat otomatis saat eksekusi awal suite `RefreshDatabase` ke `gotix_testing`).
  - Setelah seluruh pengujian baseline selesai, pemeriksaan baca-saja ke `gotix` mengonfirmasi `PESANAN_TOTAL=1` tidak tersentuh sama sekali.

### 2.2 Backup Aman Database Development (`gotix`)
- **Prosedur:** Menggunakan `C:\xampp\mysql\bin\mysqldump.exe` dengan flag `--single-transaction --routines --triggers --hex-blob` dan menyalurkan kredensial secara aman melalui environment proses (tidak pernah dicetak ke layar, log, atau file dokumentasi). Target berkas diverifikasi belum ada sebelumnya sehingga tidak menimpa backup lain.
- **Lokasi Backup:** `storage/app/backups/gotix_backup_20261112_151011.sql`
- **Status Verifikasi:** **BERHASIL (Non-Empty & Valid)**
  - Ukuran berkas: `20,918 bytes` (`SIZE_BYTES=20918`)
  - Penanda akhir dump valid (`-- Dump completed` / `DUMP_COMPLETED_MARKER=YES`).

---

## 3. Hasil Testing Baseline Aktual (Section 4)

Seluruh pengujian PHP dieksekusi murni terhadap database terisolasi `gotix_testing` tanpa modifikasi kode aplikasi:

| Perintah | Klasifikasi | Output Aktual Ringkas | Catatan / Diagnosa |
|---|---|---|---|
| `php artisan test` | **PASS** | `Tests: 34 passed (68 assertions)` — `Duration: 20.60s` (Unit: 1, Feature AuthTest: 18, ExampleTest: 1, ModelTest: 14). | Dieksekusi penuh terhadap `gotix_testing` (`RefreshDatabase`). Sebelum Phase 3.0 hanya terblokir oleh sandbox ACL Windows; berjalan bersih saat dieksekusi di host lokal. |
| `php vendor/bin/pint --test` | **FAIL** | Exit code `1`. Format JSON Pint melaporkan **12 file** melanggar aturan style Laravel (`binary_operator_spaces`, `concat_space`, `fully_qualified_strict_types`, `ordered_imports`, dll.). | File terdampak: `Admin\{Dasbor,Jadwal,Kendaraan,Kota,Pesanan}Controller`, `Member\{Keluhan,Pembayaran,Pesanan}Controller`, `Operator\PesananLoketController`, serta `database/seeders/{Jadwal,Kendaraan,Pengguna}Seeder`. *Tidak diformat pada fase ini sesuai larangan mengubah file kode.* |
| `npm run build` | **PASS** | Exit code `0` — `vite v7.3.7` mentransformasi `62 modules` dalam `1.57s`: `public/build/manifest.json` (`0.33 kB`), `app-BtPGdPbh.css` (`60.72 kB`), `app-DDpvETUX.js` (`114.55 kB`). | Build Tailwind v3.4.19 berjalan lancar; tetap mengabaikan 49 utilitas Tailwind v4 yang ada di template (dijadwalkan diperbaiki pada `P2-01`). |
| `php vendor/bin/phpstan analyse` | **UNKNOWN** | *(Tercatat di Phase 2)* `phpstan.neon` kosong (`0 bytes`). | Belum dikonfigurasi; dijadwalkan pada `P3-04`. |

---

## 4. Ringkasan 6 Keputusan Bisnis yang Disetujui

Keputusan berikut telah resmi disetujui pemilik proyek dan **dipetakan hanya ke tugas remediasi masing-masing** (belum diimplementasikan di Phase 3.0):

1. **Walk-in Loket (`P1-03`):** Mengizinkan penumpang tanpa akun Member; identitas Operator yang memproses tetap dicatat terpisah pada record pesanan.
2. **Pembayaran Ditolak (`P0-04`):** Mengembalikan status pesanan ke `Belum Bayar` disertai catatan penolakan dan mengizinkan unggah ulang bukti sebelum `batas_bayar`.
3. **Keluhan (`P1-02`):** Menggunakan tipe kolom `TEXT` dengan validasi maksimal `1.000` karakter; mempertahankan kategori `Armada` dan menyinkronkan DB enum, `KategoriKeluhan`, controller, serta Blade view.
4. **Konfirmasi Pembayaran (`P1-05`):** Memfungsikan `member.pembayaran.konfirmasi` (`konfirmasi.blade.php`) sebagai halaman konfirmasi/status khusus pasca-unggah bukti bayar.
5. **Manajemen Staf Admin (`P2-04`):** Mendukung CRUD Operator termasuk fitur aktivasi dan penonaktifan akun.
6. **Tailwind CSS (`P2-01`):** Mempertahankan **Tailwind CSS `3.4.19`** (`package.json`, `postcss.config.js`, `tailwind.config.js`).

---

## 5. Audit & Rencana Teknis Seat Integrity (`Phase 3.1` / `P0-01`)

### 5.1 Hasil Inspeksi Komponen Terkait
- **`StatusPesanan` (`app/Enums/StatusPesanan.php`):** Memiliki 4 kasus string: `Belum Bayar`, `Dalam Proses`, `Lunas`, `Dibatalkan`.
- **Status yang mengunci kursi:** `Belum Bayar`, `Dalam Proses`, `Lunas` (diverifikasi pada query `Member\PesananController::{create,store}` & `Operator\PesananLoketController::store`).
- **Status pembebas kursi:** `Dibatalkan`.
- **Kondisi data & indeks saat ini di `gotix.pesanan`:**
  - `PESANAN_TOTAL = 1`, `DUPS_ALL_COUNT = 0`, `DUPS_ACTIVE_COUNT = 0` (**aman untuk migrasi, nol konflik data**).
  - Indeks aktual di tabel `pesanan`:
    1. `PRIMARY (id)`
    2. `unique_jadwal_nomor_kursi (jadwal_id, nomor_kursi)`
    3. `pesanan_kode_pesanan_unique (kode_pesanan)`
    4. `pesanan_pengguna_id_foreign (pengguna_id)`
  - **Temuan krusial FK:** Tidak terdapat indeks terpisah untuk `jadwal_id`. InnoDB/MariaDB mengandalkan `unique_jadwal_nomor_kursi` (karena `jadwal_id` ada di posisi `Seq=1`) sebagai indeks foreign key `pesanan_jadwal_id_foreign`.

### 5.2 Evaluasi Solusi MariaDB `10.4.32-MariaDB`
- **Mengapa 3-kolom `UNIQUE(jadwal_id, nomor_kursi, status)` DITOLAK:**
  - Jika kursi #1 pada jadwal J dibatalkan dua kali (`Dibatalkan` #1 dan `Dibatalkan` #2), tuple `(J, 1, 'Dibatalkan')` akan bentrok pada pembatalan kedua.
  - Juga tidak mampu mencegah kondisi balapan/bug dimana satu baris berstatus `'Belum Bayar'` dan satu baris berstatus `'Lunas'` sama-sama menempati `(J, 1)`.
- **Solusi Terpilih — Generated Column `kursi_aktif` + Unique Index:**
  - Kolom komputasi pada tabel `pesanan`:
    `kursi_aktif TINYINT UNSIGNED GENERATED ALWAYS AS (IF(status = 'Dibatalkan', NULL, nomor_kursi)) STORED NULL` (atau via Laravel Schema Builder: `$table->unsignedTinyInteger('kursi_aktif')->storedAs("IF(status = 'Dibatalkan', NULL, nomor_kursi)")->nullable()->after('nomor_kursi');`).
  - Unique Index: `UNIQUE KEY unique_jadwal_kursi_aktif (jadwal_id, kursi_aktif)`.
  - **Kompatibilitas MariaDB `10.4.32` & Laravel `MariaDbGrammar` (`VERIFIED`):**
    - `MySqlGrammar` (diwarisi `MariaDbGrammar`) menghasilkan urutan modifier: `Unsigned` -> `StoredAs` (`as (...) stored`) -> `Nullable` (`null`), yang valid pada MariaDB 10.2+ hingga 10.4.32.
    - Ekspresi `IF(status = 'Dibatalkan', NULL, nomor_kursi)` bersifat deterministik pada level baris.
    - Di InnoDB/MariaDB, kolom `UNIQUE` mengizinkan banyak nilai `NULL` tanpa batas sehingga pembatalan dan pemesanan ulang berkali-kali pada `(jadwal_id, nomor_kursi)` yang sama tidak pernah bertabrakan, sedangkan keberadaan lebih dari 1 baris non-`Dibatalkan` pada `(jadwal_id, nomor_kursi)` ditolak secara atomik oleh mesin database.

### 5.3 Proposal Migrasi & Urutan Aman (`up()` / `down()`)
1. **`up()` (pada file migrasi baru `add_kursi_aktif_unique_index_to_pesanan_table`):**
   - Langkah 1: Tambahkan indeks reguler `idx_pesanan_jadwal_id` pada kolom `jadwal_id` terlebih dahulu agar FK `pesanan_jadwal_id_foreign` tetap memiliki indeks pendukung.
   - Langkah 2: Drop indeks unik lama `unique_jadwal_nomor_kursi`.
   - Langkah 3: Tambah kolom `kursi_aktif` (`storedAs("IF(status = 'Dibatalkan', NULL, nomor_kursi)")->nullable()->after('nomor_kursi')`).
   - Langkah 4: Tambah indeks unik baru `unique(['jadwal_id', 'kursi_aktif'], 'unique_jadwal_kursi_aktif')`.
2. **`down()` (Rollback Plan):**
   - Langkah 1: Drop `unique_jadwal_kursi_aktif`.
   - Langkah 2: Drop kolom `kursi_aktif`.
   - Langkah 3: Tambahkan kembali `unique(['jadwal_id', 'nomor_kursi'], 'unique_jadwal_nomor_kursi')`.
   - Langkah 4: Drop indeks `idx_pesanan_jadwal_id`.
   - **Risiko & Prasyarat Rollback:** Rollback `down()` **hanya dapat berhasil** apabila tidak ada pasangan `(jadwal_id, nomor_kursi)` yang memiliki >1 baris historis (`Dibatalkan` + aktif atau beberapa `Dibatalkan`). Jika sudah ada riwayat pembatalan & pemesanan ulang di produksi/dev, rollback memerlukan pemulihan dari backup SQL (`storage/app/backups/gotix_backup_20261112_151011.sql`) atau deduplikasi data batal lebih dulu.

### 5.4 Perbaikan Pendamping di Aplikasi pada `Phase 3.1`
- Memastikan `Member\PesananController::store()` menggunakan `config('gotix.batas_bayar_jam')` (bukan hardcode `24`), memvalidasi jadwal belum lewat (`tanggal_berangkat >= today()`), serta mencatat error non-domain dengan `Log::error(...)` di blok `catch (Throwable $e)`.
- Memastikan `Operator\PesananLoketController::store()` memakai kapasitas dinamis `$jadwal->kendaraan->kapasitas_kursi` (menghilangkan `max:5` hardcoded) dan logging `Log::error(...)` *(catatan: dukungan walk-in non-Member tetap masuk di tugas khusus `P1-03` sesuai peta jalan agar perubahan tetap terisolasi dan fokus)*.
- Memastikan query konkurensi `DB::transaction()` + `lockForUpdate()` tetap dipertahankan.

---

## 6. Rencana Pengujian Regresi (10 Skenario Wajib)

Pada `Phase 3.1` (dan bertahap hingga `P0-04`/`P1-03`/`P1-04`), skenario berikut disiapkan di test suite Pest (`gotix_testing`):

1. **First-time seat booking:** Member memesan kursi #1 pada jadwal aktif -> berhasil membuat `Pesanan` berstatus `Belum Bayar` dengan `kursi_aktif = 1` dan `batas_bayar` sesuai config.
2. **Two users attempting to book the same seat:** Member A memesan kursi #1 (`Belum Bayar`), Member B mencoba memesan kursi #1 di jadwal yang sama -> ditolak di level controller (error validasi kursi terisi) maupun di level model/DB (`QueryException`).
3. **Cancellation releasing the seat:** Pesanan kursi #1 milik Member A berstatus `Dibatalkan` -> nilai `kursi_aktif` menjadi `NULL` dan kursi #1 kembali tampil tersedia di halaman pilih kursi.
4. **Rebooking a cancelled seat:** Setelah pesanan Member A menjadi `Dibatalkan`, Member B memesan kursi #1 pada jadwal yang sama -> berhasil tersimpan (`Belum Bayar`, `kursi_aktif = 1`).
5. **Repeated cancellation and rebooking:** Kursi #1 pada jadwal J dipesan -> dibatalkan -> dipesan ulang -> dibatalkan lagi -> dipesan ulang ke-3 kalinya -> seluruh siklus berhasil tanpa tabrakan unique index.
6. **Same seat number on different schedules:** Member memesan kursi #2 di Jadwal J1 dan kursi #2 di Jadwal J2 -> keduanya berhasil tanpa konflik.
7. **Payment rejection and re-upload *(untuk `P0-04`)*:** Setelah pembayaran ditolak Operator/Admin, status kembali `Belum Bayar` dengan `alasan_penolakan`, kursi tetap terkunci untuk pemesan tersebut, dan Member dapat mengunggah ulang bukti transfer sebelum `batas_bayar`.
8. **Expired booking releasing the seat *(untuk `P1-04`)*:** Pesanan `Belum Bayar` yang melewati `batas_bayar` dibatalkan oleh command kedaluwarsa -> `status = Dibatalkan`, `kursi_aktif = NULL`, sehingga kursi dapat langsung dipesan oleh Member lain.
9. **Operator walk-in booking *(uji kapasitas dinamis pada `P0-01` & penumpang non-Member pada `P1-03`)*:** Operator memesan kursi pada jadwal (termasuk kursi bekas batal dan sesuai kapasitas kendaraan) -> tersimpan sebagai `Lunas` dan mengunci kursi agar Member tidak bisa memesannya lagi.
10. **Prevention of duplicate active seats:** Percobaan insert langsung di database untuk dua baris aktif (`Belum Bayar` + `Dalam Proses`, `Belum Bayar` + `Lunas`, atau `Lunas` + `Lunas`) pada `(jadwal_id, nomor_kursi)` yang sama wajib melempar `QueryException`.

---

## 7. Daftar File yang Diperkirakan Terdampak pada Implementasi Berikutnya (`Phase 3.1` / `P0-01`)

- **Baru:**
  - `database/migrations/2026_11_12_XXXXXX_update_unique_kursi_on_pesanan_table.php`
  - `tests/Feature/SeatReservationIntegrityTest.php` (menguji skenario 1–6, 9–10 yang relevan dengan `P0-01`)
- **Diubah:**
  - `app/Http/Controllers/Member/PesananController.php` (`Log::error`, cek tanggal jadwal aktif, `config('gotix.batas_bayar_jam')`)
  - `app/Http/Controllers/Operator/PesananLoketController.php` (`Log::error`, kapasitas kursi dinamis kendaraan; pengisian `pengguna_id` belum diubah sebelum `P1-03`)

---

## 8. Risiko Terbuka Sebelum Implementasi `Phase 3.1`

1. **InnoDB Foreign-Key Index Order:** Jika migrasi men-drop `unique_jadwal_nomor_kursi` sebelum menambahkan indeks pada `jadwal_id`, MariaDB akan menggagalkan migrasi (`Cannot drop index needed in a foreign key constraint`).
2. **Rollback Destruktif Tidak Otomatis:** Setelah ada data pembatalan & pemesanan ulang di tabel `pesanan`, rollback `down()` migrasi `P0-01` tidak bisa mengembalikan `UNIQUE(jadwal_id, nomor_kursi)` lama tanpa menghapus/memulihkan data dari backup.
3. **Pint Style Baseline Gagal (`12 files`):** Repositori memiliki 12 file controller/seeder yang gagal `pint --test` sebelum Phase 3; perbaikan gaya kode sebaiknya dilakukan hanya pada file yang disentuh atau dalam commit bersih terpisah sesuai persetujuan pemilik.
4. **Ruang Lingkup Terpisah antara `P0-01`, `P0-04`, `P1-03`, `P1-04`:** Skenario regresi #7 (reject & re-upload) dan #8 (auto-expiry) serta #9 penuh (walk-in tanpa akun) bergantung pada implementasi tugas masing-masing setelah `P0-01` selesai.

