# 02 — Technology Stack Audit

## 1. Runtime & Environment

- **VERIFIED (CLI output):**
  - **PHP:** `8.2.12` (requirement `composer.json`: `^8.2`)
  - **Laravel Framework:** `12.70.2` (requirement `composer.json`: `^12.0`)
  - **Node.js:** `v26.13.0`
  - **npm:** `11.20.0`
- **VERIFIED (`php artisan config:show` / config files):**
  - **Locale & Timezone:** `app.locale = id`, `app.fallback_locale = id`, `app.timezone = Asia/Jakarta`.
  - **Database Driver:** `database.default = mysql` (konfigurasi test `.env.testing`: `mysql`, DB `gotix_testing`, host `127.0.0.1:3306`).
  - **Session Driver:** `session.driver = database` (`.env.testing`: `array`).
  - **Auth Guard & Provider (`config/auth.php`):** Guard `web` (driver `session`), provider `users` (Eloquent model `App\Models\Pengguna::class`).

---

## 2. Backend Dependencies (`composer.json` & `composer.lock`)

### 2.1 Production (`require`)

| Paket | Constraint (`composer.json`) | Versi Terkunci (`composer.lock`) | Status Pemakaian Aktual |
|---|---|---|---|
| `php` | `^8.2` | `8.2.12` (runtime) | **VERIFIED:** Digunakan (PHP 8.2+ backed enums, typed properties, match expressions). |
| `laravel/framework` | `^12.0` | `v12.70.2` | **VERIFIED:** Inti framework, Eloquent, Blade, routing, session auth. |
| `laravel/tinker` | `^2.10.1` | `v2.11.1` | **VERIFIED:** Tool REPL bawaan `php artisan tinker`. |

### 2.2 Development (`require-dev`)

| Paket | Constraint (`composer.json`) | Versi Terkunci (`composer.lock`) | Status Pemakaian Aktual |
|---|---|---|---|
| `pestphp/pest` | `^3.8` | `v3.8.7` | **VERIFIED:** Digunakan pada `tests/Pest.php`, `AuthTest.php`, `ModelTest.php`, `ExampleTest.php`. |
| `pestphp/pest-plugin-laravel` | `^3.2` | `v3.2.0` | **VERIFIED:** Helper HTTP & database assertion di Pest. |
| `larastan/larastan` | `*` | `v3.14.0` (+ `phpstan/phpstan 2.3.5`) | **VERIFIED (terpasang, belum dikonfigurasi):** `phpstan.neon` ada tetapi kosong (0 byte). |
| `laravel/pint` | `^1.24` | `v1.30.4` | **VERIFIED:** Terpasang tanpa file `pint.json` kustom (memakai preset default `laravel`). |
| `fakerphp/faker` | `^1.23` | `v1.24.1` | **VERIFIED:** Digunakan di seluruh 7 file `database/factories/*`. |
| `mockery/mockery` | `^1.6` | `1.6.15` | **NOT IMPLEMENTED:** Terpasang sebagai bawaan Laravel, belum ada pemakaian di `tests/`. |
| `nunomaduro/collision` | `^8.6` | `v8.9.5` | **VERIFIED:** Pelaporan error CLI Artisan & Pest. |
| `laravel/pail` | `^1.2.2` | `v1.2.7` | **VERIFIED:** Terdaftar di script `composer run dev`. |
| `laravel/sail` | `^1.41` | `v1.69.0` | **NOT IMPLEMENTED:** Tidak ada `compose.yaml` / Docker workflow terpakai di repo. |

---

## 3. Frontend Dependencies & Build Tooling (`package.json` & `package-lock.json`)

| Paket | Constraint (`package.json`) | Versi Terkunci (`package-lock.json`) | Status Pemakaian Aktual |
|---|---|---|---|
| `vite` | `^7.0.7` | `7.3.7` | **VERIFIED:** Bundler aktif via `vite.config.js`. |
| `laravel-vite-plugin` | `^2.0.0` | `2.1.0` | **VERIFIED:** Plugin entry point `resources/css/app.css` & `resources/js/app.js`. |
| `tailwindcss` | `^3.1.0` | `3.4.19` | **VERIFIED (AKTIF):** Dipakai oleh `postcss.config.js` & `tailwind.config.js` serta directive `@tailwind` di `resources/css/app.css`. |
| `@tailwindcss/vite` | `^4.0.0` | `4.3.4` | **VERIFIED (TIDAK AKTIF / KONFLIK):** Terpasang di `package.json`, tetapi **tidak di-import** di `vite.config.js`. |
| `@tailwindcss/forms` | `^0.5.2` | `0.5.11` | **VERIFIED:** Dimuat dalam array `plugins` di `tailwind.config.js`. |
| `autoprefixer` | `^10.4.2` | `10.6.2` | **VERIFIED:** Terdaftar di `postcss.config.js`. |
| `postcss` | — (dev/transitive) | `8.5.32` | **VERIFIED:** Pipeline PostCSS aktif bersama Tailwind v3. |
| `alpinejs` | `^3.4.2` | `3.17.5` | **VERIFIED:** Di-import & dijalankan (`Alpine.start()`) di `resources/js/app.js`, digunakan pada komponen UI interaktif (`x-data`, `x-show`, `x-model`, modal). |
| `axios` | `^1.11.0` | `1.21.0` | **VERIFIED (diimpor):** Diimpor di `resources/js/bootstrap.js`, namun belum ada pemanggilan `axios` di `resources/js/` maupun view Blade. |
| `concurrently` | `^9.0.1` | `9.2.5` | **VERIFIED:** Digunakan di script `composer run dev`. |

### Dependensi Eksternal via CDN (Di luar `package.json`)

- **VERIFIED:**
  - **Google Fonts:** `DM Sans` (body) dan `Bricolage Grotesque` (heading) dimuat dari `fonts.googleapis.com` di `layouts/tamu.blade.php` dan `layouts/admin.blade.php` (dan `layouts/operator.blade.php` yang tidak terpakai).
  - **AOS (Animate On Scroll) v2.3.4:** Dimuat via `unpkg.com` (`aos@2.3.4/dist/aos.css` dan `aos.js`) di `resources/views/layouts/tamu.blade.php` L16 & L284, dipakai melalui 30+ atribut `data-aos` di halaman publik, auth, member, dan operator.

---

## 4. Temuan Konfigurasi & Konflik Stack

### Issue 4.1 — Konflik Instalasi Tailwind CSS v3 vs Tailwind CSS v4
- **VERIFIED**
- **File path:** `package.json` L11 & L19, `vite.config.js` L1-11, `postcss.config.js` L1-6, `tailwind.config.js` L1-22, `resources/css/app.css` L1-17
- **Relevant class/method/component:** Pipeline build CSS (Vite + PostCSS + Tailwind)
- **Description:** `package.json` menginstal dua versi Tailwind sekaligus (`tailwindcss: ^3.1.0` yang terkunci di `3.4.19`, dan `@tailwindcss/vite: ^4.0.0` yang terkunci di `4.3.4`). Sementara itu `vite.config.js` tidak memakai plugin `@tailwindcss/vite`, sehingga Vite memproses CSS melalui `postcss.config.js` menggunakan **Tailwind v3.4.19**. Namun di sisi Blade templates (`publik/`, `member/`, `operator/`, `admin/`, `layouts/`), ditemukan **49 pemakaian utility khas Tailwind v4** (`shadow-xs` sebanyak 45x, `w-4.5` 2x, `h-4.5` 1x, `shadow-2xs` 1x) dan instruksi `.agent/rules/gotix.md` menyebut Tailwind v4 `@theme`.
- **Impact:** Seluruh utility Tailwind v4 (`shadow-xs`, `shadow-2xs`, `w-4.5`, `h-4.5`) **tidak terkompilasi** oleh Tailwind v3 dan diabaikan secara diam-diam oleh browser. Selain itu keberadaan `@tailwindcss/vite` menciptakan ambiguitas stack saat Phase 2 redesign.
- **Recommended solution:** Tetapkan satu versi resmi Tailwind sebelum memulai Design System (rekomendasi: selaraskan sepenuhnya ke Tailwind v4 `@tailwindcss/vite` + `@import "tailwindcss"` + `@theme` atau bersihkan `@tailwindcss/vite` dan gunakan standar Tailwind v3 murni). Jangan mencampur utility v3 dan v4.
- **Priority:** High

### Issue 4.2 — Konfigurasi Aplikasi (`config/gotix.php`) Tidak Sinkron dengan View & Controller
- **VERIFIED**
- **File path:** `config/gotix.php` L1-23, `resources/views/layouts/tamu.blade.php` L263-265, `resources/views/member/pembayaran/show.blade.php` L112-114, `app/Http/Controllers/Member/PesananController.php` L88, `app/Http/Controllers/Operator/PesananLoketController.php` L72
- **Relevant class/method/component:** `config('gotix.*')`
- **Description:**
  1. `config/gotix.php` mendefinisikan nested array `'bank' => ['nama' => ..., 'rekening' => ..., 'pemilik' => ...]`, namun `layouts/tamu.blade.php` L263-265 memanggil key yang tidak ada (`config('gotix.bank_name')`, `config('gotix.bank_account')`, `config('gotix.bank_holder')`), sehingga menampilkan nilai fallback atau kosong.
  2. `member/pembayaran/show.blade.php` L112-114 melakukan hardcode rekening BCA `8820 1928 33` alih-alih membaca `config('gotix.bank.*')`.
  3. `Member\PesananController::store()` L88 menuliskan `now()->addHours(24)` secara literal alih-alih `config('gotix.batas_bayar_jam')`.
  4. `Operator\PesananLoketController::store()` L47-51 melakukan hardcode `'max:5'` alih-alih menggunakan kapasitas kendaraan atau `config('gotix.kapasitas_kursi')`.
- **Impact:** Perubahan rekening bank atau batas waktu bayar melalui `.env` / `config/gotix.php` tidak berdampak pada tampilan maupun logika pembayaran.
- **Recommended solution:** Seragamkan seluruh pembacaan konfigurasi bisnis melalui `config('gotix.bank.*')` dan `config('gotix.batas_bayar_jam')`.
- **Priority:** High

### Issue 4.3 — Ketergantungan Aset Lokal pada Vite Dev Server (`public/hot`) & CDN Eksternal
- **VERIFIED**
- **File path:** `public/hot`, `resources/views/layouts/tamu.blade.php` L12-16 & L284
- **Relevant class/method/component:** Layout & asset loading
- **Description:** `public/hot` aktif (`http://[::1]:5173`) dan `public/build/` tidak tersedia. Selain itu AOS CSS/JS dimuat dari `unpkg.com` dan font dimuat dari Google Fonts tanpa fallback lokal.
- **Impact:** Aplikasi gagal merender CSS/JS jika server Vite dev tidak aktif, dan animasi AOS bergantung pada koneksi internet ke CDN eksternal (jika CDN lambat/terblokir, elemen dengan `data-aos` bisa tetap tersembunyi).
- **Recommended solution:** Jalankan `npm run build` untuk produksi/testing tanpa dev server dan hapus `public/hot` dari workflow deployment; pertimbangkan memaketkan dependensi UI/animasi lewat Vite atau CSS transisi lokal pada Design System baru.
- **Priority:** Medium

### Issue 4.4 — `phpstan.neon` Kosong (0 Byte) & Tidak Ada `pint.json`
- **VERIFIED**
- **File path:** `phpstan.neon`
- **Relevant class/method/component:** Konfigurasi static analysis
- **Description:** `phpstan.neon` berukuran 0 byte sehingga Larastan tidak memiliki definisi `includes` maupun `paths`.
- **Impact:** Tidak ada gerbang kualitas statis otomatis untuk mendeteksi pemanggilan properti model yang salah (seperti `nama_pengirim` yang seharusnya `rekening_pengirim`).
- **Recommended solution:** Konfigurasi `phpstan.neon` (level 5+ untuk `app/`) sebelum/bersamaan refactor Phase 2.
- **Priority:** Medium

