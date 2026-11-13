# 06 — Authentication and RBAC Audit

## 1. Mekanisme Autentikasi

- **VERIFIED (`config/auth.php`, `app/Http/Controllers/Auth/*`):**
  - **Guard & Provider:** Menggunakan session guard default `web` dengan Eloquent provider `users` yang dipetakan ke model `App\Models\Pengguna` (tabel `pengguna`).
  - **Login (`Auth\MasukController::store()`):** Menerima input `login` (diuji via `filter_var($login, FILTER_VALIDATE_EMAIL)` untuk memilih kolom `email` atau `username`) dan `password`, serta opsi `ingat_saya` (*remember me*). Sesi diregenerasi (`$request->session()->regenerate()`) setelah login sukses.
  - **Registrasi (`Auth\DaftarController::store()`):** Menerima `nama`, `username`, `email` (opsional/nullable), `telepon`, `alamat`, `jenis_kelamin`, `password`. Peran **selalu dikunci** ke `PeranPengguna::Member` di backend sehingga tidak dapat dimanipulasi menjadi `Admin` atau `Operator` melalui form daftar. Password di-hash via `Hash::make()`, memicu event `Registered`, otomatis login, dan regenerasi sesi.
  - **Logout (`Auth\KeluarController::destroy()`):** Melakukan `Auth::logout()`, `invalidate()` sesi, dan `regenerateToken()` CSRF, lalu redirect ke route `beranda` (`/`). Tersedia pada `/keluar` dan `/logout` (`POST`, dilindungi `auth`).
  - **Redirect Guest (`bootstrap/app.php` L16):** `$middleware->redirectGuestsTo(fn () => route('masuk'))`.

---

## 2. Penyimpanan Peran & Proteksi Middleware

- **VERIFIED (`database/migrations/2026_03_14_000001_create_pengguna_table.php`, `app/Enums/PeranPengguna.php`, `app/Http/Middleware/PastikanPunyaPeran.php`):**
  - Peran disimpan dalam kolom enum MySQL `pengguna.peran` dengan nilai `Admin`, `Operator`, `Member` (default `Member`) dan di-cast ke backed enum `App\Enums\PeranPengguna` pada model `Pengguna`.
  - Middleware alias `'peran'` (`PastikanPunyaPeran::class`) didaftarkan di `bootstrap/app.php` L15.
  - Logika `PastikanPunyaPeran::handle()`:
    1. Jika `! $request->user()`, redirect ke route `masuk`.
    2. Iterasi parameter variadik `...$peran`, konversi ke `PeranPengguna::from($p)` (melempar `403` jika nama peran invalid), lalu membandingkan secara strict (`===`) dengan `$request->user()->peran`.
    3. Jika tidak cocok, mengembalikan `abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.')`.
- **VERIFIED (Role Redirect Pasca Login):**
  - `MasukController::redirectBerdasarkanPeran()` mengarahkan:
    - `Admin` -> `admin.dasbor` (`/admin/dasbor`)
    - `Operator` -> `operator.pembayaran.index` (`/operator/pembayaran`)
    - `Member` (default) -> `member.tiket.index` (`/akun/tiket`)
  - `DaftarController::store()` mengarahkan Member baru ke `member.tiket.index`.
  - Middleware `guest` bawaan Laravel (`RedirectIfAuthenticated`) mengatur redirect jika user sudah login saat mengakses `/masuk` atau `/daftar`. **POTENTIAL ISSUE:** Belum ada custom `redirectUsersTo` untuk peran di `bootstrap/app.php`; `AuthTest.php` menguji bahwa halaman `/masuk` & `/daftar` me-redirect user terautentikasi, namun tujuan default Laravel (`/home` atau `/dashboard` jika tersedia, atau `/` jika tidak) mungkin tidak selaras dengan dasbor tiap peran — perlu diverifikasi di test suite.

---

## 3. Matriks Peran dan Izin (Verified Implementation)

> **Catatan:** Tidak terdapat Laravel Policies atau Gates di aplikasi ini (`NOT IMPLEMENTED`). Seluruh izin berbasis proteksi route middleware `peran:<Role>` ditambah pengecekan kepemilikan `Auth::user()->pesanan()` di controller Member.

| Fitur / Endpoint / Resource | Publik (Guest) | Member | Operator Loket | Admin | Mekanisme Proteksi Terverifikasi |
|---|---|---|---|---|---|
| Beranda (`/`) & Cari Jadwal (`/cari-jadwal`) | Ya | Ya | Ya | Ya | Tanpa middleware auth |
| Login (`/masuk`), Daftar (`/daftar`) | Ya | Redirect (`guest`) | Redirect (`guest`) | Redirect (`guest`) | Middleware `guest` |
| Logout (`/keluar`, `/logout`) | Tidak (redirect `masuk`) | Ya | Ya | Ya | Middleware `auth` |
| Lihat Daftar & Detail Pesanan Saya (`/akun/pesanan*`) | Tidak | Ya (hanya milik sendiri) | Tidak (403) | Tidak (403) | `auth`, `peran:Member`, scoped `Auth::user()->pesanan()` |
| Buat Pesanan / Pilih Kursi (`/akun/jadwal/{jadwal}/pesan`) | Tidak | Ya | Tidak (403) | Tidak (403) | `auth`, `peran:Member` |
| Unggah Bukti Pembayaran (`/akun/pesanan/{kode}/bayar`) | Tidak | Ya (milik sendiri, `Belum Bayar`) | Tidak (403) | Tidak (403) | `auth`, `peran:Member`, scoped `Auth::user()->pesanan()` |
| Lihat Daftar & Detail E-Tiket (`/akun/tiket*`) | Tidak | Ya (milik sendiri — *bug: termasuk belum lunas*) | Tidak (403) | Tidak (403) | `auth`, `peran:Member`, scoped `Auth::user()->pesanan()` |
| Buat & Lihat Keluhan (`/akun/keluhan*`) | Tidak | Ya (*frontend create masih mocked*) | Tidak (403) | Tidak (403) | `auth`, `peran:Member` |
| Verifikasi / Tolak Pembayaran (`/operator/pembayaran*`) | Tidak | Tidak (403) | Ya | Tidak pada route `/operator` (punya sendiri di `/admin/pesanan`) | `auth`, `peran:Operator` |
| Pemesanan Loket Walk-in (`/operator/loket*`) | Tidak | Tidak (403) | Ya (*frontend index mocked*) | Tidak (403) | `auth`, `peran:Operator` |
| Dasbor Admin (`/admin/dasbor`) | Tidak | Tidak (403) | Tidak (403) | Ya | `auth`, `peran:Admin` |
| CRUD Kota, Kendaraan, Jadwal (`/admin/{kota,kendaraan,jadwal}*`) | Tidak | Tidak (403) | Tidak (403) | Ya | `auth`, `peran:Admin` |
| Lihat Daftar & Detail Pengguna (`/admin/pengguna*`) | Tidak | Tidak (403) | Tidak (403) | Ya (Read-Only) | `auth`, `peran:Admin` |
| Create / Edit / Delete Pengguna (Member/Operator) | Tidak | Tidak | Tidak | **NOT IMPLEMENTED** | Tidak ada route/method di `Admin\PenggunaController` |
| Lihat & Verifikasi/Tolak Pesanan (`/admin/pesanan*`) | Tidak | Tidak (403) | Tidak (403) | Ya | `auth`, `peran:Admin` |
| Lihat & Balas Keluhan (`/admin/keluhan*`) | Tidak | Tidak (403) | Tidak (403) | Ya | `auth`, `peran:Admin` |

---

## 4. Temuan Keamanan & Ketidaksesuaian Navigasi RBAC

### Issue 6.1 — Navigasi Frontend Memicu 403 bagi Admin & Operator di Halaman Pencarian Jadwal
- **VERIFIED**
- **File path:** `resources/views/publik/cari-jadwal.blade.php` L180-195 & L278-283, `resources/views/publik/beranda.blade.php`
- **Relevant class/method/component:** Tombol CTA Pemesanan Tiket (`@auth` block)
- **Description:** Di halaman publik `cari-jadwal.blade.php`, pengecekan otorisasi tombol pemesanan hanya menggunakan `@auth` (tidak memeriksa `Auth::user()->peran === PeranPengguna::Member`). Ketika Admin atau Operator sedang login dan membuka `/cari-jadwal`, tombol "Pesan Tiket" tetap diarahkan ke `route('member.pesanan.create', ...)`.
- **Impact:** Mengklik tombol pemesanan saat login sebagai Admin atau Operator langsung menghasilkan error `403 Forbidden`. Selain itu pada header `layouts/tamu.blade.php`, staf yang membuka halaman publik juga tetap melihat navigasi tamu standar.
- **Recommended solution:** Ganti `@auth` pada tombol booking publik menjadi cek peran spesifik (`$user?->peran === PeranPengguna::Member`), dan tampilkan link menuju workspace masing-masing (`operator.loket.index` untuk Operator, `admin.dasbor` untuk Admin).
- **Priority:** Medium

### Issue 6.2 — Bukti Transfer Disimpan pada Public Disk Tanpa Perlindungan Akses
- **VERIFIED**
- **File path:** `app/Http/Controllers/Member/PembayaranController.php` L43, `resources/views/operator/pembayaran/show.blade.php`, `resources/views/admin/pesanan/show.blade.php`
- **Relevant class/method/component:** Upload dan tampilan `bukti_path`
- **Description:** Foto bukti transfer member disimpan di `storage/app/public/bukti_transfer` (`disk = 'public'`) dan diakses langsung melalui URL publik `/storage/bukti_transfer/...` tanpa autentikasi ataupun pengecekan peran/kepemilikan.
- **Impact:** Kebocoran privasi data keuangan pengguna (bukti transfer, nama rekening) apabila path berkas ditemukan/ditebak.
- **Recommended solution:** Simpan berkas pada disk privat (`local`), buat route controller khusus terproteksi (`auth` + cek kepemilikan `Member` atau peran `Operator`/`Admin`) untuk men-stream gambar bukti bayar.
- **Priority:** High

### Issue 6.3 — Tidak Ada Rate Limiting pada Endpoint Login & Tidak Ada Manajemen Staf di UI Admin
- **VERIFIED:**
  1. `Auth\MasukController::store()` tidak membatasi percobaan login gagal (`RateLimiter`), membuka celah brute-force password. *(Priority: High)*
  2. `Admin\PenggunaController` hanya menyediakan `index` dan `show`. Admin **tidak dapat menambah akun Operator baru, mengedit peran, atau menonaktifkan pengguna** melalui UI (akun Operator/Admin baru saat ini hanya bisa dibuat lewat seeder/tinker/DB langsung). *(Priority: Medium)*
  3. Tidak ada Laravel Policies (`PesananPolicy`, `KeluhanPolicy`), sehingga bila nanti ada route yang menerima ID pesanan atau keluhan lintas peran, perlindungan bergantung pada kedisiplinan developer di tiap controller. *(Priority: Medium)*

