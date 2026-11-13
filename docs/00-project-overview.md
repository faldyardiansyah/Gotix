# 00 — Project Overview (Audit GOTIX)

> **Status Dokumen:** Phase 1 — Comprehensive Technical & Functional Audit  
> **Lingkup:** Investigasi read-only atas codebase GOTIX tanpa perubahan kode, skema database, ataupun dependensi.

---

## 1. Ringkasan Eksekutif

- **VERIFIED:** **GOTIX** adalah aplikasi web pemesanan tiket perjalanan/travel berbasis **Laravel 12** (`laravel/framework v12.70.2`, PHP `^8.2`), menggunakan Blade + Tailwind CSS + Alpine.js + Vite di sisi frontend, serta MySQL sebagai basis data utama (`database.default = mysql`).
- **VERIFIED:** Aplikasi ini dirancang untuk melayani **tiga peran pengguna terautentikasi** (`PeranPengguna`: `Member`, `Operator`, `Admin`) ditambah pengunjung publik (*guest*).
- **VERIFIED:** Berdasarkan hasil inspeksi kode sumber, sistem **belum siap masuk ke fase produksi maupun langsung ke redesign UI/UX tanpa perbaikan prasyarat** karena adanya sejumlah cacat fungsional kritis pada alur inti (*core workflows*), inkonsistensi konfigurasi build Tailwind CSS (v3 vs v4), serta beberapa modul yang masih berupa *placeholder* atau masih menggunakan data statis (*mocked/hardcoded*).

---

## 2. Konteks Bisnis & Domain

- **VERIFIED [Dikoreksi di Phase 2]:** Domain yang ditemukan pada `database/seeders/*` (`KotaSeeder` dengan 27 kota/kabupaten Lampung dan `KendaraanSeeder` dengan armada Avanza & Xenia 5 kursi) serta aturan `.agent/rules/gotix.md` adalah layanan **travel antar-kota di wilayah Provinsi Lampung**. Sementara itu teks pemasaran/dummy pada beberapa Blade view masih mencampur istilah armada dan rute Pulau Jawa.
- **POTENTIAL ISSUE:** Konten UI masih tidak konsisten secara domain. Pada `resources/views/publik/beranda.blade.php`, teks footer/rute/testimoni serta data mock di `resources/views/member/keluhan/index.blade.php`, `resources/views/member/tiket/show.blade.php`, dan `resources/views/operator/loket/index.blade.php` masih mencampur rute Pulau Jawa (Jakarta–Bandung, Yogyakarta–Surabaya, Semarang–Solo) dengan data Lampung dari database.
  - **File path:** `resources/views/publik/beranda.blade.php`, `resources/views/member/tiket/show.blade.php`, `resources/views/member/keluhan/index.blade.php`, `resources/views/operator/loket/index.blade.php`
  - **Relevant class/method/component:** Halaman landing publik, tampilan e-tiket, daftar keluhan member, halaman loket operator.
  - **Description:** Copywriting statis dan data mock masih merujuk ke kota di Pulau Jawa, berlawanan dengan data seeder Lampung (`KotaSeeder`).
  - **Impact:** Membingungkan pengguna dan merusak kredibilitas produk saat UI/UX di-redesign.
  - **Recommended solution:** Hapus seluruh data mock dan standarisasi copywriting dari konfigurasi atau data dinamis database sebelum/selama redesign.
  - **Priority:** Medium

---

## 3. Kondisi Proyek Secara Menyeluruh

| Aspek | Status | Ringkasan Kondisi |
|---|---|---|
| **Struktur & Routing** | **VERIFIED** | 38 route terdaftar di `routes/web.php` (`publik`, `auth`, `member`, `operator`, `admin`). Middleware peran (`PastikanPunyaPeran`) melindungi grup route masing-masing peran. |
| **Autentikasi & RBAC** | **VERIFIED** | Login manual (email atau username), registrasi member, logout. Role disimpan pada kolom enum `pengguna.peran` dan dicast ke `App\Enums\PeranPengguna`. |
| **Database & Schema** | **VERIFIED** | 9 file migrasi (3 bawaan Laravel + 6 domain GOTIX: `pengguna`, `kota`, `kendaraan`, `jadwal`, `pesanan`, `konfirmasi_pembayaran`, `keluhan`). Terdapat bug kritis pada `unique(['jadwal_id', 'nomor_kursi'])` yang memblokir kursi bekas pesanan batal. |
| **Alur Pemesanan Member** | **VERIFIED** | Pencarian jadwal, pilih kursi (`lockForUpdate`), buat pesanan, unggah bukti bayar berfungsi secara parsial; konfirmasi pembayaran terpisah & e-tiket & keluhan **rusak/mocked**. |
| **Alur Operator Loket** | **VERIFIED** | Verifikasi pembayaran berfungsi secara parsial (tanpa mencatat verifier/waktu & tanpa transaksi); halaman walk-in loket (`operator/loket/index.blade.php`) **mocked/tidak mengirim request ke server**, dan `create.blade.php` masih **placeholder**. |
| **Alur Admin** | **VERIFIED** | CRUD Kota, Kendaraan, Jadwal, dan manajemen Pesanan/Keluhan tersedia; `admin/jadwal/show.blade.php` masih **placeholder**, manajemen Pengguna hanya read-only, dan typo variabel `$menanggguVerifikasi` ditemukan pada dasbor. |
| **Frontend & Build** | **VERIFIED** | Dual instalasi Tailwind v3 & v4 di `package.json`, dengan pipeline Vite/PostCSS masih berjalan di **Tailwind v3** sementara view memakai beberapa kelas v4 (`shadow-xs`, `shadow-2xs`, `w-4.5`, `h-4.5`). `public/hot` aktif, `public/build/` tidak ada. |
| **Testing & QA** | **VERIFIED / UNKNOWN (eksekusi terblokir sandbox)** | Terdapat 32 test case di `tests/` (`AuthTest`, `ModelTest`, 2 `ExampleTest`), namun `phpstan.neon` kosong (0 byte). Di lingkungan sandbox Windows saat audit, PHP melaporkan `is_readable()` = `false` pada file workspace sehingga `pest` dan `phpstan` tidak dapat dieksekusi sampai selesai. |

---

## 4. Ringkasan Kesenjangan Terhadap `.agent/rules/gotix.md`

- **VERIFIED:** File `.agent/rules/gotix.md` mendefinisikan arsitektur target yang **jauh berbeda dan sebagian besar belum diimplementasikan (`NOT IMPLEMENTED`)** di codebase saat ini:
  - Tidak ada `app/Actions/`, `app/Services/`, `app/Policies/`, `app/Console/Commands/BatalkanPesananKedaluwarsa.php`, `app/Support/Format.php`.
  - Tidak ada komponen Blade di `resources/views/components/` maupun JS modular di `resources/js/komponen/pemilih-kursi.js`.
  - Tidak ada `layouts/member.blade.php` atau `layouts/dasbor.blade.php` (kode aktual memakai `layouts/tamu.blade.php`, `layouts/admin.blade.php`, dan `layouts/operator.blade.php` yang tidak dipakai).
  - Tidak ada `Admin/MemberController` dan `Admin/OperatorController` terpisah (diganti `Admin/PenggunaController` read-only).
  - `Model::preventLazyLoading()` tidak diaktifkan di `AppServiceProvider`.
  - `@theme` Tailwind v4 tidak digunakan di `resources/css/app.css`.

---

## 5. Peta Dokumen Audit (`docs/`)

| File | Fokus Audit |
|---|---|
| `00-project-overview.md` | Ringkasan eksekutif, konteks domain, dan kondisi menyeluruh proyek. |
| `01-project-structure.md` | Struktur direktori, tanggung jawab folder, tinjauan `.agent/rules`, file yatim/duplikat/salah tempat. |
| `02-technology-stack.md` | Versi aktual Composer, NPM, Vite, Tailwind, konfigurasi Laravel, serta konflik dependensi. |
| `03-backend-audit.md` | Evaluasi controller, model, middleware, validation, security, query efficiency, dan arsitektur backend. |
| `04-frontend-audit.md` | Evaluasi layout, partials, Blade views, Alpine.js, CSS, konsistensi UI, responsivitas, dan aksesibilitas. |
| `05-database-audit.md` | Audit migrasi, relasi model, foreign key, indeks, seeders, factories, transaksi, dan ketidaksesuaian skema. |
| `06-authentication-and-rbac.md` | Mekanisme auth, penyimpanan peran, proteksi middleware, redirect peran, matriks izin, dan celah otorisasi. |
| `07-routes-and-feature-inventory.md` | Inventaris lengkap 38 route (`web.php`), parameter, middleware, controller, serta status fitur tiap peran. |
| `08-business-logic-documentation.md` | Dokumentasi alur pemesanan, pembayaran, verifikasi, e-tiket, loket, dan keluhan beserta cacat logikanya. |
| `09-testing-and-code-quality.md` | Inventaris test (`AuthTest`, `ModelTest`), konfigurasi PHPUnit/Pest/PHPStan/Pint, serta kendala eksekusi aktual. |
| `10-technical-debt-and-recommendations.md` | Katalog terstruktur utang teknis & isu lengkap dengan file path, dampak, solusi, dan prioritas. |
| `11-ui-ux-redesign-readiness.md` | Evaluasi kesiapan menuju Phase 2 (Design System terpadu 3 peran), daftar blocker wajib, dan rekomendasi fase transisi. |

