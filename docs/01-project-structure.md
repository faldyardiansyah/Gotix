# 01 — Project Structure Audit

## 1. Struktur Direktori Utama

- **VERIFIED:** Repositori mengikuti struktur standar aplikasi **Laravel 12** tanpa struktur modular `Actions/` atau `Services/`.

```text
Gotix/
├── .agent/
│   └── rules/
│       └── gotix.md                  # Aturan AI proyek (banyak menyimpang dari implementasi aktual)
├── app/
│   ├── Enums/                        # 4 PHP backed enums (JenisKelamin, KategoriKeluhan, PeranPengguna, StatusPesanan)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php        # Base abstract controller kosong
│   │   │   ├── Admin/                # 7 controller (Dasbor, Jadwal, Keluhan, Kendaraan, Kota, Pengguna, Pesanan)
│   │   │   ├── Auth/                 # 3 controller (Daftar, Keluar, Masuk)
│   │   │   ├── Member/               # 4 controller (Keluhan, Pembayaran, Pesanan, Tiket)
│   │   │   ├── Operator/             # 2 controller (PesananLoket, VerifikasiPembayaran)
│   │   │   └── Publik/               # 2 controller (Beranda, CariJadwal)
│   │   ├── Middleware/
│   │   │   └── PastikanPunyaPeran.php# Middleware RBAC role check (`peran:...`)
│   │   └── Requests/
│   │       └── Auth/                 # Hanya 2 FormRequest (DaftarRequest, MasukRequest)
│   ├── Models/                       # 7 Eloquent models (Pengguna, Kota, Kendaraan, Jadwal, Pesanan, KonfirmasiPembayaran, Keluhan)
│   └── Providers/
│       └── AppServiceProvider.php    # register() & boot() kosong
├── bootstrap/
│   ├── app.php                       # Konfigurasi routing, alias middleware `peran`, guest redirect ke `masuk`
│   └── providers.php                 # Registrasi AppServiceProvider
├── config/                           # Konfigurasi Laravel standar + `config/gotix.php`
├── database/
│   ├── factories/                    # 7 factory (seluruh model domain + UserFactory bawaan? — cek detail di Doc 05)
│   ├── migrations/                   # 9 migrasi (3 sistem Laravel + 6 tabel GOTIX)
│   └── seeders/                      # 5 seeder (Database, Pengguna, Kota, Kendaraan, Jadwal)
├── lang/
│   └── id/                           # Bahasa Indonesia (auth.php, pagination.php, passwords.php, validation.php)
├── public/
│   ├── hot                           # Berisi `http://[::1]:5173` (indikasi dev server Vite pernah aktif)
│   ├── storage/                      # Symlink ke `storage/app/public`
│   └── ...                           # index.php, assets gambar/favicon jika ada
├── resources/
│   ├── css/app.css                   # Entry Tailwind v3 (`@tailwind base/components/utilities`)
│   ├── js/
│   │   ├── app.js                    # Import bootstrap & inisialisasi Alpine.js
│   │   └── bootstrap.js              # Konfigurasi Axios
│   └── views/                        # 41 file Blade (layouts, auth, publik, member, operator, admin, welcome.blade.php)
├── routes/
│   ├── web.php                       # Seluruh 38 route aplikasi
│   └── console.php                   # Hanya command default `inspire`
├── tests/
│   ├── Pest.php, TestCase.php
│   ├── Feature/                      # AuthTest.php, ModelTest.php, ExampleTest.php
│   └── Unit/                         # ExampleTest.php
├── .env.example, .env.testing, .gitignore, composer.json, composer.lock
├── package.json, package-lock.json, phpstan.neon (0 bytes), phpunit.xml
├── postcss.config.js, tailwind.config.js, vite.config.js, README.md
```

---

## 2. Tanggung Jawab Direktori Penting

- **VERIFIED (`app/Enums/`):** Menyimpan nilai status/kategori domain berbasis string-backed PHP enum (`PeranPengguna`, `StatusPesanan`, `KategoriKeluhan`, `JenisKelamin`).
- **VERIFIED (`app/Http/Controllers/{Admin,Auth,Member,Operator,Publik}/`):** Mengandung seluruh logika HTTP, query database, validasi (selain Auth), dan penanganan error secara langsung (*fat controllers*).
- **VERIFIED (`app/Http/Middleware/PastikanPunyaPeran.php`):** Satu-satunya middleware kustom untuk otorisasi berbasis peran.
- **VERIFIED (`app/Http/Requests/Auth/`):** Hanya dipakai untuk `MasukController` dan `DaftarController`. Controller fitur (Admin, Member, Operator) tidak menggunakan FormRequest.
- **VERIFIED (`app/Models/`):** Menggunakan skema bahasa Indonesia dengan relasi eksplisit (misal `kotaAsal`, `kotaTujuan`, `kendaraan`, `pesanan`, `konfirmasiPembayaran`).
- **VERIFIED (`config/gotix.php`):** Menyimpan parameter bisnis (`batas_bayar_jam`, `kapasitas_kursi`, `bank`), namun sebagian tidak dipakai atau salah dipanggil oleh controller/view.
- **VERIFIED (`resources/views/`):** Terorganisasi per peran/konteks (`publik/`, `auth/`, `member/`, `operator/`, `admin/`, `layouts/`), namun tanpa direktori `components/` untuk UI reusable.

---

## 3. Audit Instruksi AI (`AGENTS.md` dan `.agent/rules/`)

- **NOT IMPLEMENTED (`AGENTS.md`):** Tidak ditemukan file `AGENTS.md` di root repositori.
- **VERIFIED (`.agent/rules/gotix.md`):** File ini berisi 67 baris instruksi arsitektur dan konvensi proyek, namun **terjadi deviasi besar antara dokumen aturan dan implementasi nyata**:

| Aturan di `.agent/rules/gotix.md` | Status Aktual di Codebase |
|---|---|
| `app/Actions/` untuk logika bisnis yang dipanggil dari beberapa controller | **NOT IMPLEMENTED** — direktori tidak ada; logika ditulis langsung di controller dan sebagian diduplikasi. |
| `app/Services/KetersediaanKursiService.php` | **NOT IMPLEMENTED** — perhitungan sisa kursi tersebar di `CariJadwalController`, `Member/PesananController`, `Operator/PesananLoketController`. |
| `app/Policies/` (`PesananPolicy`, `KeluhanPolicy`, dll.) | **NOT IMPLEMENTED** — otorisasi hanya mengecek `pengguna_id === Auth::id()` di controller atau tidak ada sama sekali. |
| `app/Console/Commands/BatalkanPesananKedaluwarsa.php` & schedule di `routes/console.php` | **NOT IMPLEMENTED** — pesanan kedaluwarsa tidak pernah dibatalkan otomatis. |
| `app/Support/Format.php` (helper `rupiah()`, `tanggal()`, `kursi()`) | **NOT IMPLEMENTED** — formatting memakai `number_format()` / Carbon inline di berbagai Blade. |
| `Model::preventLazyLoading()` di `AppServiceProvider` | **NOT IMPLEMENTED** — `AppServiceProvider::boot()` kosong. |
| Tailwind CSS v4 dengan `@theme` di `resources/css/app.css` | **NOT IMPLEMENTED** — Vite/PostCSS menjalankan Tailwind v3 (`@tailwind base/components/utilities`). |
| `resources/views/components/` (komponen Blade reusable) & `resources/js/komponen/pemilih-kursi.js` | **NOT IMPLEMENTED** — seluruh UI masih inline di masing-masing file view. |
| `layouts/member.blade.php` & `layouts/dasbor.blade.php` | **NOT IMPLEMENTED** — aplikasi menggunakan `layouts/tamu.blade.php` (untuk publik/auth/member/operator), `layouts/admin.blade.php`, dan `layouts/operator.blade.php` (tidak terpakai). |
| `_form.blade.php` partial untuk form admin create/edit | **NOT IMPLEMENTED** — form create dan edit diduplikasi di file terpisah pada `admin/kota`, `admin/kendaraan`, `admin/jadwal`. |
| `Admin/MemberController` & `Admin/OperatorController` terpisah | **NOT IMPLEMENTED** — digabung ke `Admin/PenggunaController` yang bersifat read-only. |

---

## 4. Temuan File Duplikat, Tidak Terpakai, Placeholder, atau Salah Tempat

### 4.1 File Tidak Terpakai (*Unused / Orphaned*)

1. **VERIFIED — `resources/views/layouts/operator.blade.php`**
   - **File path:** `resources/views/layouts/operator.blade.php`
   - **Relevant class/method/component:** Layout Operator
   - **Description:** Layout khusus operator tersedia di repositori, tetapi seluruh 4 view di `resources/views/operator/` justru melakukan `@extends('layouts.tamu')` dan tidak ada satu pun file yang meng-extend `layouts.operator`.
   - **Impact:** Kebingungan saat pemeliharaan dan redesign UI/UX karena pengembang dapat salah mengedit layout yang tidak aktif.
   - **Recommended solution:** Konsolidasikan struktur layout sesuai keputusan Design System sebelum Phase 2 dan hapus/migrasi layout yang yatim.
   - **Priority:** Medium

2. **VERIFIED — `resources/views/admin/_nav.blade.php`**
   - **File path:** `resources/views/admin/_nav.blade.php`
   - **Relevant class/method/component:** Partial navigasi admin
   - **Description:** Partial ini menyediakan tab navigasi horizontal admin, padahal seluruh 16 view aktif admin memakai `@extends('layouts.admin')` yang sudah memiliki sidebar gelap sendiri dan tidak pernah me-`@include('admin._nav')`.
   - **Impact:** Kode mati (*dead template*) yang berisiko membingungkan alur navigasi admin.
   - **Recommended solution:** Hapus setelah memastikan konsolidasi layout admin pada fase redesign.
   - **Priority:** Low

3. **VERIFIED — `resources/views/welcome.blade.php`**
   - **File path:** `resources/views/welcome.blade.php`
   - **Relevant class/method/component:** View bawaan installer Laravel
   - **Description:** File `welcome.blade.php` bawaan Laravel (berisi inline CSS Tailwind v4.0.7 terkompilasi) tidak dirujuk oleh route atau controller mana pun (`/` diarahkan ke `BerandaController` -> `publik.beranda`).
   - **Impact:** Menambah noise saat pencarian/scanning kelas Tailwind pada `resources/views`.
   - **Recommended solution:** Hapus saat tahap pembersihan codebase.
   - **Priority:** Low

4. **VERIFIED — `tests/Feature/ExampleTest.php` & `tests/Unit/ExampleTest.php`**
   - **File path:** `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`
   - **Relevant class/method/component:** Skeleton test bawaan Laravel
   - **Description:** Hanya berisi test contoh (`GET /` 200 dan `expect(true)->toBeTrue()`).
   - **Impact:** Memberi ilusi coverage test yang tidak menguji domain GOTIX.
   - **Recommended solution:** Pertahankan atau ganti dengan unit/feature test domain yang bermakna.
   - **Priority:** Low

### 4.2 File Placeholder (Belum Diimplementasikan)

5. **VERIFIED — 4 View Masih Berisi Placeholder Satu Baris**
   - **File path:**
     - `resources/views/admin/jadwal/show.blade.php` (`[PLACEHOLDER] Admin — Detail Jadwal`)
     - `resources/views/member/pembayaran/konfirmasi.blade.php` (`[PLACEHOLDER] Member — Konfirmasi Pembayaran`)
     - `resources/views/operator/loket/create.blade.php` (`[PLACEHOLDER] Operator — Pesanan Loket`)
   - **Relevant class/method/component:** `Admin\JadwalController::show` (route `admin.jadwal.show`), `Member\PembayaranController::konfirmasi` (route `member.pembayaran.konfirmasi`), `Operator\PesananLoketController::create` (route `operator.loket.create`).
   - **Description:** Route dan method controller mengembalikan view Blade yang hanya berisi teks `[PLACEHOLDER] ...` tanpa struktur HTML, form, maupun data.
   - **Impact:** Pengguna yang mengakses route tersebut mendapatkan halaman teks mentah dan fitur bisnis tidak berjalan.
   - **Recommended solution:** Putuskan apakah endpoint tersebut akan diimplementasikan penuh atau dihapus/digabung dengan halaman `index`/`show` yang relevan sebelum redesign UI.
   - **Priority:** High

### 4.3 File Konfigurasi / Artefak Build Bermasalah

6. **VERIFIED — `phpstan.neon` (0 bytes)**
   - **File path:** `phpstan.neon`
   - **Relevant class/method/component:** Konfigurasi PHPStan / Larastan
   - **Description:** File konfigurasi ada di root repositori tetapi berukuran 0 byte (kosong sepenuhnya).
   - **Impact:** Analisis statis Larastan tidak terkonfigurasi (`paths`, `level`, `includes` belum didefinisikan).
   - **Recommended solution:** Lengkapi isi `phpstan.neon` dengan `includes: [vendor/larastan/larastan/extension.neon]` serta `paths: [app]` dan level bertahap.
   - **Priority:** Medium

7. **VERIFIED — `public/hot` ada tanpa `public/build/`**
   - **File path:** `public/hot`
   - **Relevant class/method/component:** Vite asset resolver (`@vite(...)`)
   - **Description:** File `public/hot` (`http://[::1]:5173`) tertinggal di direktori `public/`, sementara folder `public/build/` tidak ada.
   - **Impact:** Ketika dev server Vite tidak dijalankan, seluruh halaman yang memakai `@vite(['resources/css/app.css', 'resources/js/app.js'])` akan mencoba memuat aset dari `http://[::1]:5173` dan gagal memuat CSS/JS.
   - **Recommended solution:** Pastikan `.gitignore` mengecualikan `/public/hot` (saat ini sudah tercantum di `.gitignore`, namun file terlanjur ada di local workspace) dan jalankan build/dev server dengan benar saat pengembangan.
   - **Priority:** Medium

---

## 5. Pola Arsitektur yang Teridentifikasi

- **VERIFIED — Fat Controller / Transaction Script Pattern:** Tidak ada lapisan Service, Action, ataupun Repository. Validasi input bisnis (selain Auth), kalkulasi sisa kursi, penomoran kode pesanan, dan query database dilakukan langsung di dalam controller.
- **VERIFIED — Route Prefix + Role Middleware Isolation:** Fitur dibagi berdasarkan namespace controller dan grup route berdasarkan peran (`Member`, `Operator`, `Admin`), namun lapisan view belum konsisten memisahkan layout-nya.
- **VERIFIED — Partial Reuse Minim:** Form CRUD Admin (create/edit) diduplikasi penuh di masing-masing file view alih-alih diekstrak ke partial/komponen. Navigasi sekunder pada Member & Operator menggunakan `@include('member._nav')` dan `@include('operator._nav')` yang ditanam secara manual di tiap halaman indeks (tetapi tidak di halaman `show`/`create`).

