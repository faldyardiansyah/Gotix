# 05 — Database Audit

> **Catatan Kepatuhan:** Seluruh temuan pada dokumen ini diperoleh melalui inspeksi langsung file migrasi (`database/migrations/`), model (`app/Models/`), factory (`database/factories/`), dan seeder (`database/seeders/`). Tidak ada migrasi, seeder, ataupun perubahan data yang dijalankan.

---

## 1. Inventaris Skema Tabel (`database/migrations/`)

- **VERIFIED:** Terdapat 9 file migrasi:
  - 3 migrasi awal Laravel (`0001_01_01_000000_create_pengguna_table.php` yang langsung membuat `pengguna`, `password_reset_tokens`, & `sessions`, serta `0001_01_01_000001_create_cache_table.php`, `0001_01_01_000002_create_jobs_table.php`).
  - 6 migrasi domain GOTIX (`2026_03_14_000001` s.d. `000006`).

> **[Dikoreksi di Phase 2]:** Tidak terdapat tabel `users` bawaan yang yatim; migrasi awal sudah langsung mendefinisikan tabel `pengguna`.

### Ringkasan Tabel Domain GOTIX

| Tabel | Primary Key | Kolom Kunci & Tipe Penting | Foreign Key & OnDelete | Indeks / Unik | Soft Delete |
|---|---|---|---|---|---|
| `pengguna` | `id` (bigint) | `nama`, `username` (unique), `email` (nullable, unique), `password`, `telepon`, `alamat`, `jenis_kelamin` (enum `L`,`P`), `peran` (enum `Admin`,`Operator`,`Member`) | — | `username` (unique), `email` (unique) | Tidak |
| `kota` | `id` (bigint) | `nama_kota` (unique) | — | `nama_kota` (unique) | Tidak |
| `kendaraan` | `id` (bigint) | `jenis_kendaraan`, `nomor_polisi` (unique), `warna`, `kapasitas_kursi` (unsignedInteger, default 5) | — | `nomor_polisi` (unique) | Tidak |
| `jadwal` | `id` (bigint) | `kota_asal_id`, `kota_tujuan_id`, `kendaraan_id`, `harga` (unsignedInteger), `tanggal_berangkat` (date), `jam_berangkat` (time) | `kota_asal_id` -> `kota.id` (**cascadeOnDelete**); `kota_tujuan_id` -> `kota.id` (**cascadeOnDelete**); `kendaraan_id` -> `kendaraan.id` (**cascadeOnDelete**) | `idx_jadwal_rute_tanggal` (`kota_asal_id`, `kota_tujuan_id`, `tanggal_berangkat`) | Tidak |
| `pesanan` | `id` (bigint) | `kode_pesanan` (255, unique), `jadwal_id`, `pengguna_id`, `nomor_kursi` (unsignedTinyInteger), `harga_snapshot` (unsignedInteger), `status` (enum 4 nilai), `batas_bayar` (dateTime) | `jadwal_id` -> `jadwal.id` (**cascadeOnDelete**); `pengguna_id` -> `pengguna.id` (**cascadeOnDelete**) | `kode_pesanan` (unique); **`unique(['jadwal_id', 'nomor_kursi'], 'unique_jadwal_nomor_kursi')`** | Tidak |
| `konfirmasi_pembayaran` | `id` (bigint) | `pesanan_id` (unique), `nomor_resi` (255, unique), `rekening_pengirim` (255), `tanggal_transfer` (date), `jam_transfer` (time), `bukti_path` (varchar nullable), `alasan_penolakan` (text nullable), `diverifikasi_oleh` (nullable), `diverifikasi_pada` (dateTime nullable) | `pesanan_id` -> `pesanan.id` (**cascadeOnDelete**); `diverifikasi_oleh` -> `pengguna.id` (**nullOnDelete**) | `pesanan_id` (unique), `nomor_resi` (unique) | Tidak |
| `keluhan` | `id` (bigint) | `pengguna_id`, `pesanan_id` (nullable), `kategori` (enum 5 nilai), `deskripsi` (**string(200)**), `balasan` (text nullable), `dibalas_oleh` (nullable), `dibalas_pada` (dateTime nullable) | `pengguna_id` -> `pengguna.id` (**cascadeOnDelete**); `pesanan_id` -> `pesanan.id` (**nullOnDelete**); `dibalas_oleh` -> `pengguna.id` (**nullOnDelete**) | Index default FK | Tidak |

---

## 2. Pemetaan Relasi Eloquent (`app/Models/`)

- **VERIFIED [Dikoreksi di Phase 2]:**
  - **`Pengguna`** (`pengguna`): `hasMany(Pesanan::class, 'pengguna_id')`, `hasMany(Keluhan::class, 'pengguna_id')`, `hasMany(KonfirmasiPembayaran::class, 'diverifikasi_oleh')` (`konfirmasiVerifikasi`), `hasMany(Keluhan::class, 'dibalas_oleh')` (`balasanKeluhan`), serta helper `isAdmin()`, `isOperator()`, `isMember()`. Casting: `password => hashed`, `peran => PeranPengguna::class`, `jenis_kelamin => JenisKelamin::class`.
  - **`Kota`** (`kota`): `hasMany(Jadwal::class, 'kota_asal_id')` (`jadwalAsal`), `hasMany(Jadwal::class, 'kota_tujuan_id')` (`jadwalTujuan`).
  - **`Kendaraan`** (`kendaraan`): `hasMany(Jadwal::class, 'kendaraan_id')`. Casting: `kapasitas_kursi => integer`. *(Tidak terdapat kolom `aktif` maupun `scopeAktif()`.)*
  - **`Jadwal`** (`jadwal`): `belongsTo(Kota::class, 'kota_asal_id')` (`kotaAsal`), `belongsTo(Kota::class, 'kota_tujuan_id')` (`kotaTujuan`), `belongsTo(Kendaraan::class, 'kendaraan_id')`, `hasMany(Pesanan::class, 'jadwal_id')`. *(Tidak terdapat `scopeTersedia()` maupun accessor `sisa_kursi` di model.)*
  - **`Pesanan`** (`pesanan`): `belongsTo(Jadwal::class, 'jadwal_id')`, `belongsTo(Pengguna::class, 'pengguna_id')`, `hasOne(KonfirmasiPembayaran::class, 'pesanan_id')`, `hasMany(Keluhan::class, 'pesanan_id')`. Casting: `nomor_kursi => integer`, `harga_snapshot => integer`, `status => StatusPesanan::class`, `batas_bayar => datetime`. *(Generasi kode `GTX-XXXXXX` dilakukan langsung di controller, bukan melalui method model `buatKode()`.)*
  - **`KonfirmasiPembayaran`** (`konfirmasi_pembayaran`): `belongsTo(Pesanan::class, 'pesanan_id')`, `belongsTo(Pengguna::class, 'diverifikasi_oleh')` (`verifikator`). Casting: `tanggal_transfer => date`, `diverifikasi_pada => datetime`.
  - **`Keluhan`** (`keluhan`): `belongsTo(Pengguna::class, 'pengguna_id')`, `belongsTo(Pesanan::class, 'pesanan_id')`, `belongsTo(Pengguna::class, 'dibalas_oleh')` (`petugasBalas`). Casting: `kategori => KategoriKeluhan::class`, `dibalas_pada => datetime`.

---

## 3. Audit Seeders & Factories

- **VERIFIED (`database/seeders/`) [Dikoreksi di Phase 2]:**
  - `DatabaseSeeder`: Memanggil `PenggunaSeeder`, `KotaSeeder`, `KendaraanSeeder`, `JadwalSeeder`.
  - `PenggunaSeeder`: Membuat 3 akun uji (`admin`, `operator`, `member`).
  - `KotaSeeder`: Mengisi **27 kota/kabupaten di Provinsi Lampung** (Bandar Lampung, Metro, Pringsewu, Kotabumi, Kalianda, Liwa, dll.).
  - `KendaraanSeeder`: Mengisi **2 armada** (`Avanza` BE 1212 AT, kapasitas 5 kursi; dan `Xenia` BE 4677 DT, kapasitas 5 kursi).
  - `JadwalSeeder`: Membuat 6 jadwal perjalanan ke depan dari Bandar Lampung.
- **VERIFIED (`database/factories/`):** Seluruh 7 model domain memiliki factory masing-masing.

---

## 4. Inkonsistensi & Cacat Database Kritis

### Issue 5.1 — Constraint `unique_jadwal_nomor_kursi` Memblokir Pemesanan Ulang Kursi yang Dibatalkan
- **VERIFIED**
- **File path:** `database/migrations/2026_03_14_000004_create_pesanan_table.php` L25
- **Relevant class/method/component:** `$table->unique(['jadwal_id', 'nomor_kursi'], 'unique_jadwal_nomor_kursi');`
- **Description:** Indeks unik membatasi pasangan `(jadwal_id, nomor_kursi)` secara mutlak untuk semua status. Sementara itu, seluruh aplikasi (`Jadwal::getSisaKursiAttribute()`, `Member\PesananController::store()`, `Operator\PesananLoketController::store()`) menganggap kursi dengan status `Dibatalkan` bebas dipesan kembali.
- **Impact:** Ketika pesanan dibatalkan atau pembayaran ditolak (`status = Dibatalkan`), kursi tersebut masih tercatat di tabel `pesanan`. Setiap percobaan memesan kursi tersebut kembali oleh user lain akan gagal pada tingkat database (`Duplicate entry 'X-Y' for key 'unique_jadwal_nomor_kursi'`).
- **Recommended solution:** Ganti pola constraint dengan *generated/nullable column* untuk kursi aktif di MySQL 8 (misal kolom virtual `kursi_aktif` bernilai `nomor_kursi` saat `status != 'Dibatalkan'` dan `NULL` saat `'Dibatalkan'`, lalu beri `UNIQUE(jadwal_id, kursi_aktif)`), atau pindahkan ke tabel alokasi kursi / arsip. Karena aturan Phase 1 melarang mengubah migrasi lama, buat migrasi baru pada fase perbaikan setelah persetujuan.
- **Priority:** Critical

### Issue 5.2 — `cascadeOnDelete()` Berlebihan Tanpa Soft Deletes Merusak Integritas Data Transaksi
- **VERIFIED**
- **File path:** `database/migrations/2026_03_14_000003_create_jadwal_table.php` L16-18, `2026_03_14_000004_create_pesanan_table.php` L16-17, `2026_03_14_000005_create_konfirmasi_pembayaran_table.php` L16
- **Relevant class/method/component:** Foreign key constraints pada `jadwal`, `pesanan`, `konfirmasi_pembayaran`
- **Description:** Tidak ada tabel yang memiliki `softDeletes()`, namun semua relasi dari `kota`, `kendaraan`, `pengguna`, dan `jadwal` memakai `cascadeOnDelete()`.
- **Impact:** Penghapusan 1 data `Kota`, `Kendaraan`, `Jadwal`, atau `Pengguna` otomatis menghapus seluruh riwayat transaksi `Pesanan` dan bukti bayar `KonfirmasiPembayaran` secara permanen tanpa jejak audit keuangan.
- **Recommended solution:** Gunakan `restrictOnDelete()` pada FK transaksi historis (migrasi baru), terapkan `SoftDeletes` pada master data, dan tambahkan guard pengecekan relasi pada controller `destroy()`.
- **Priority:** Critical

### Issue 5.3 — Ketidaksesuaian Panjang Kolom `keluhan.deskripsi` vs Validasi Controller
- **VERIFIED**
- **File path:** `database/migrations/2026_03_14_000006_create_keluhan_table.php` L19 vs `app/Http/Controllers/Member/KeluhanController.php` L36
- **Relevant class/method/component:** Kolom `deskripsi` pada tabel `keluhan`
- **Description:** Kolom DB didefinisikan `VARCHAR(200)` (`$table->string('deskripsi', 200)`), sementara controller memvalidasi `max:1000`.
- **Impact:** SQL `Data too long for column 'deskripsi'` / truncation error jika member mengetik >200 karakter.
- **Recommended solution:** Ubah tipe kolom `keluhan.deskripsi` menjadi `TEXT` (lewat migrasi alter table baru) atau turunkan aturan validasi agar konsisten.
- **Priority:** High

### Issue 5.4 — Ketidaksesuaian Nama Kolom pada View vs Skema `konfirmasi_pembayaran` & Nilai Enum
- **VERIFIED**
- **File path:** `resources/views/operator/pembayaran/index.blade.php` L90, `resources/views/member/keluhan/create.blade.php`
- **Relevant class/method/component:** `konfirmasi_pembayaran.rekening_pengirim`, `keluhan.kategori`
- **Description:** View operator pembayaran memanggil `$pesanan->konfirmasiPembayaran->nama_pengirim` padahal kolom tabelnya bernama `rekening_pengirim`. View buat keluhan member memuat opsi kategori `'Armada'` yang tidak ada di enum MySQL `('Akun','Pesanan','Pembayaran','Pelayanan','Lainnya')`.
- **Impact:** Informasi pengirim selalu kosong di dashboard verifikasi; nilai kategori `'Armada'` gagal disimpan ke DB jika form dihubungkan tanpa perbaikan opsi.
- **Recommended solution:** Selaraskan penamaan properti dan daftar opsi enum dengan skema/Enum PHP.
- **Priority:** High

### Issue 5.5 — Generasi Kode Pesanan (`GTX-XXXXXX`) di Controller Memakai Loop `exists()`
- **VERIFIED / POTENTIAL ISSUE [Dikoreksi di Phase 2]**
- **File path:** `app/Http/Controllers/Member/PesananController.php` L90-92, `app/Http/Controllers/Operator/PesananLoketController.php` L71-73
- **Relevant class/method/component:** Generasi kode acak `GTX-XXXXXX`
- **Description:** Generasi kode acak `GTX-XXXXXX` diduplikasi di dua controller dan dicek memakai `Pesanan::where('kode_pesanan', $kode)->exists()` sebelum insert. Pada konkurensi sangat tinggi ada potensi rendah tabrakan `unique` constraint.
- **Impact:** Duplikasi logika pembuatan kode dan potensi rendah duplicate key error saat tabrakan acak terjadi.
- **Recommended solution:** Ekstrak ke helper terpusat di model/service dan pertahankan unique index `kode_pesanan` sebagai pengaman akhir.
- **Priority:** Low

### Issue 5.6 — Ketidakseragaman Penggunaan `DB::transaction`
- **VERIFIED:**
  - `Member\PesananController::store()` & `Operator\PesananLoketController::store()`: Menggunakan `DB::transaction` + `lockForUpdate()`. (**Baik**, meskipun terbentur Issue 5.1).
  - `Member\PembayaranController::konfirmasiStore()`, `Operator\VerifikasiPembayaranController::{verifikasi,tolak}`, `Admin\PesananController::{verifikasi,tolak}`: **Tidak menggunakan `DB::transaction`** meskipun memperbarui 2 tabel (`pesanan` & `konfirmasi_pembayaran`) atau mengunggah file bersamaan dengan insert/update DB. *(Priority: High)*
