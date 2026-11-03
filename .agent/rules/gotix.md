# Aturan Proyek Gotix

Konteks: Gotix = pemesanan tiket travel antar kota Lampung. Menggunakan Laravel 12, Blade, Tailwind v4 (Vite), Alpine.js, MySQL, dan Pest. TANPA Breeze/Fortify/Jetstream (Menggunakan Custom Authentication Manual). TANPA Midtrans (Pembayaran transfer manual).

Bahasa: Nama class, folder, tabel, kolom, route, variabel, komentar, dan teks UI wajib menggunakan Bahasa Indonesia. Pengecualian: nama method CRUD standar Laravel (index, create, store, show, edit, update, destroy), suffix Controller/Request/Policy, serta kata kunci framework tetap menggunakan Bahasa Inggris.

Autentikasi:
- Murni menggunakan Auth::attempt() secara manual di dalam Controller otentikasi (`app/Http/Controllers/Auth/`).
- Login mendukung input berupa Username ATAU Email.
- Password wajib selalu di-hash menggunakan Hash::make() atau bcrypt().

Arsitektur & Aturan Kode:
- Controller dibuat tipis (maksimal ±20 baris per method). Validasi melalui Form Request, dan logika bisnis diletakkan di `app/Actions` serta `app/Services`.
- Semua route didefinisikan di `routes/web.php`, dikelompokkan menjadi: publik, auth, member (`/akun`), operator (`/operator`), dan admin (`/admin`). Menggunakan middleware alias 'peran'.
- View per resource wajib menyediakan: index, create, edit, show, serta partial `_form.blade.php` yang digunakan bersama untuk form create dan edit.
- Model dengan nama tabel berbahasa Indonesia wajib mendeklarasikan properti `protected $table`. Gunakan Enum (`PeranPengguna`, `StatusPesanan`, `JenisKelamin`, `KategoriKeluhan`).
- Kursi tidak boleh dobel: dilindungi dengan `DB::transaction() + lockForUpdate() + unique index` pada kombinasi (`jadwal_id`, `nomor_kursi`).
- Hindari masalah N+1: selalu gunakan eager loading `with()`, serta aktifkan `Model::preventLazyLoading()` pada environment non-produksi.

Peta Struktur Folder Proyek:
gotix/
├── app/
│   ├── Actions/                        # Logika Bisnis (Pesanan, Jadwal)
│   │   ├── Pesanan/ (BuatPesananAction, BatalkanPesananAction, KirimKonfirmasiPembayaranAction, SetujuiPembayaranAction, TolakPembayaranAction)
│   │   └── Jadwal/ (CariJadwalAction)
│   ├── Console/Commands/ (BatalkanPesananKedaluwarsa.php)
│   ├── Enums/ (PeranPengguna, StatusPesanan, JenisKelamin, KategoriKeluhan)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/ (MasukController, DaftarController, KeluarController)
│   │   │   ├── Publik/ (BerandaController, CariJadwalController)
│   │   │   ├── Member/ (PesananController, PembayaranController, TiketController, KeluhanController)
│   │   │   ├── Operator/ (VerifikasiPembayaranController, PesananLoketController)
│   │   │   └── Admin/ (DasborController, JadwalController, KendaraanController, KotaController, PesananController, MemberController, OperatorController, KeluhanController)
│   │   ├── Middleware/ (PastikanPunyaPeran.php)
│   │   └── Requests/ (Auth/, Member/, Admin/)
│   ├── Models/ (Pengguna, Kota, Kendaraan, Jadwal, Pesanan, KonfirmasiPembayaran, Keluhan)
│   ├── Policies/ (PesananPolicy, KeluhanPolicy)
│   ├── Services/ (KetersediaanKursiService.php)
│   └── Support/ (Format.php)
├── config/ (auth.php, gotix.php)
├── database/ (migrations/, seeders/)
├── lang/id/ (validation.php, auth.php, dll)
├── resources/
│   ├── css/app.css
│   ├── js/ (app.js, komponen/pemilih-kursi.js)
│   └── views/
│       ├── components/ (ui/, pesanan/, tata-letak/)
│       ├── layouts/ (tamu.blade.php, member.blade.php, dasbor.blade.php)
│       ├── publik/, auth/, member/, operator/, admin/, errors/
└── routes/ (web.php, console.php)

Database: 
- Nama database menggunakan `gotix` (development) dan `gotix_testing` (testing). 
- Agen diizinkan membuat database, menjalankan migrasi (`migrate`, `migrate:fresh --seed`), serta `db:seed` pada kedua database tersebut. Dilarang keras menyentuh database lain.

Desain & Tampilan:
- Token warna dan font didefinisikan secara terpusat di dalam directive `@theme` pada `resources/css/app.css`. 
- Dilarang keras melakukan hardcode kode warna hex langsung di dalam file view (.blade.php). 
- Gaya desain: tenang, tepercaya, menggunakan border tipis 1px, tanpa gradasi berlebihan, menerapkan gaya glassmorphism, menggunakan emoji sebagai ikon, serta kartu fitur yang konsisten.

Kualitas Kode:
- Jalankan `vendor/bin/pint` untuk formatting code dan `php artisan test` setelah menyelesaikan setiap tahap.
- Lakukan satu commit per tahap dengan format pesan Conventional Commits berbahasa Indonesia (contoh: `feat:`, `fix:`, `refactor:`).

Batasan Keamanan:
- Jangan pernah menjalankan perintah destruktif di luar proyek, melakukan `git push --force`, ataupun mengubah file di luar direktori proyek.