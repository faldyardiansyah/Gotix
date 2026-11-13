# Aturan Proyek Gotix

> Dokumen ini disinkronkan dengan aturan tata kelola root [`AGENTS.md`](../../AGENTS.md) dan hasil validasi Phase 2.  
> Jika terdapat perbedaan detail, ikuti prioritas Source of Truth pada `AGENTS.md` (kode sumber & migrasi aktual lebih tinggi daripada dokumen aturan lama).

## Konteks & Stack Aktual
- **Domain:** Gotix = sistem pemesanan tiket travel antar kota Provinsi Lampung (`KotaSeeder`).
- **Stack Terverifikasi:** Laravel 12 (`^12.0`), PHP `^8.2`, Blade, Alpine.js, MySQL, Pest, dan Vite.
- **Build CSS Aktual:** Pipeline aktif saat ini berjalan dengan **Tailwind CSS v3** (`tailwindcss`, `postcss.config.js`, `tailwind.config.js`, `@tailwind` di `resources/css/app.css`). Paket `@tailwindcss/vite` juga terpasang di `package.json`, namun belum diaktifkan; jangan mencampur utilitas Tailwind v4 ke build v3 sebelum versi resmi Design System diputuskan bersama pemilik proyek.
- **Autentikasi & Pembayaran:** Custom authentication manual tanpa Breeze/Fortify/Jetstream (`app/Http/Controllers/Auth/`, model `Pengguna`, tabel `pengguna`). Pembayaran menggunakan transfer bank manual (`config/gotix.php`) tanpa Midtrans/payment gateway.

## Bahasa & Konvensi Penamaan
- Nama class, folder, tabel, kolom, route, variabel, komentar, dan teks UI menggunakan **Bahasa Indonesia**.
- Pengecualian tetap memakai Bahasa Inggris standar Laravel: nama method CRUD (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`), suffix arsitektural (`Controller`, `Request`, `Policy`, `Service`, `Action`), serta kata kunci framework.

## Autentikasi & Otorisasi
- Autentikasi menggunakan `Auth::attempt()` di `app/Http/Controllers/Auth/` dengan dukungan input `username` atau `email` dan hashing `Hash::make()` / cast `'hashed'`.
- Route dibagi di `routes/web.php` menjadi grup publik, `guest`/`auth`, Member (`/akun`, `peran:Member`), Operator (`/operator`, `peran:Operator`), dan Admin (`/admin`, `peran:Admin`) menggunakan middleware `PastikanPunyaPeran` (`peran`).
- Setiap endpoint yang mengakses resource milik pengguna atau mengubah status transaksi wajib memvalidasi kepemilikan dan state pesanan di backend (jangan bergantung pada penyembunyian tombol UI).

## Arsitektur Aktual vs Target Bertahap
- **Kondisi Aktual:** Logika bisnis dan validasi non-auth saat ini masih berada di dalam controller (`Admin/*`, `Member/*`, `Operator/*`), tanpa direktori `Actions/`, `Services/`, `Policies/`, `components/`, dan form CRUD admin belum memakai partial `_form.blade.php`.
- **Aturan Pengembangan:**
  - Periksa kode aktual sebelum mengedit; jangan mengasumsikan file atau helper (seperti `Actions/`, `Services/`, `Format.php`, atau `BatalkanPesananKedaluwarsa`) sudah ada sebelum dibuat pada tahap perbaikan yang disetujui.
  - Saat melakukan perbaikan yang disetujui, hindari duplikasi logika (misalnya perhitungan ketersediaan kursi dan transisi verifikasi pembayaran) serta jaga controller tetap fokus dan tervalidasi konsisten.
  - Model dengan tabel berbahasa Indonesia mendeklarasikan `protected $table` dan memanfaatkan Enum (`PeranPengguna`, `StatusPesanan`, `JenisKelamin`, `KategoriKeluhan`).
  - Perlindungan kursi ganda wajib menjaga konsistensi antara transaksi (`DB::transaction()` + `lockForUpdate()`) dan constraint database tanpa mengunci kursi dari pesanan berstatus `Dibatalkan`.
  - Hindari query N+1 dengan eager loading `with()`.

## Struktur Direktori Aktual
```text
gotix/
├── app/
│   ├── Enums/ (PeranPengguna, StatusPesanan, JenisKelamin, KategoriKeluhan)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/ (MasukController, DaftarController, KeluarController)
│   │   │   ├── Publik/ (BerandaController, CariJadwalController)
│   │   │   ├── Member/ (PesananController, PembayaranController, TiketController, KeluhanController)
│   │   │   ├── Operator/ (VerifikasiPembayaranController, PesananLoketController)
│   │   │   └── Admin/ (DasborController, JadwalController, KendaraanController, KotaController, PenggunaController, PesananController, KeluhanController)
│   │   ├── Middleware/ (PastikanPunyaPeran.php)
│   │   └── Requests/Auth/ (DaftarRequest, MasukRequest)
│   ├── Models/ (Pengguna, Kota, Kendaraan, Jadwal, Pesanan, KonfirmasiPembayaran, Keluhan)
│   └── Providers/ (AppServiceProvider.php)
├── config/ (auth.php, gotix.php, ...)
├── database/ (migrations/, seeders/, factories/)
├── docs/ (dokumentasi audit Phase 1, validasi, roadmap remediasi, & persiapan design system)
├── lang/id/ (auth.php, pagination.php, passwords.php, validation.php)
├── resources/
│   ├── css/app.css
│   ├── js/ (app.js, bootstrap.js)
│   └── views/ (layouts/, publik/, auth/, member/, operator/, admin/)
└── routes/ (web.php, console.php)
```

## Perlindungan Database
- Database utama pengembangan bernama `gotix` dan database pengujian terisolasi bernama `gotix_testing` (`.env.testing` / `phpunit.xml`).
- **Dilarang keras** mereset database, menjalankan `migrate:fresh`, `db:wipe`, `db:seed` destruktif, atau mengubah file migrasi yang sudah ada tanpa persetujuan eksplisit pemilik proyek.
- Perubahan skema yang disetujui wajib melalui file migrasi baru yang aman terhadap data historis transaksi.

## Desain & Tampilan
- Gunakan **satu Design System bersama** untuk seluruh peran (Member, Operator Loket, Admin) dengan navigasi dan layout yang disesuaikan per peran.
- Token visual final (warna, tipografi, komponen UI bersama) menunggu persetujuan dan referensi desain pada fase Redesign UI/UX berikutnya (`docs/13-design-system-preparation.md`).
- Hindari markup Blade yang duplikat, pertahankan responsivitas dan aksesibilitas (`label for/id`, `aria-label`), serta pisahkan logika bisnis/data dummy dari view.

## Kualitas Kode, Pengujian & Git
- Jalankan pengujian non-destruktif terhadap `gotix_testing` (`php artisan test`), pemeriksaan format (`vendor/bin/pint --test`), dan pemeriksaan build (`npm run build`) sesuai perubahan, serta laporkan output aktual secara jujur.
- Jangan melakukan `git commit` atau `git push` tanpa instruksi eksplisit dari pemilik proyek, dan jangan mengubah file di luar direktori proyek.
