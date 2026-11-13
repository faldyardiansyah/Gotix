# GOTIX — DOKUMENTASI IMPLEMENTASI PHASE P3.1: VISUAL REFINEMENT, NAVBAR & AUTHENTICATION REDESIGN

> **Status:** FINALIZED  
> **Tanggal:** 13 November 2026  
> **Basis Acuan:** Permintaan Revisi Terbatas P3.1, `AGENTS.md`, `docs/27-gotix-design-system-proposal.md`, `docs/28-phase-3-member-redesign-implementation.md`

---

## 1. RINGKASAN EKSEKUTIF

Phase **P3.1** difokuskan secara ketat pada penyempurnaan visual terbatas pada halaman beranda, perbaikan desain dan perilaku navbar publik, pembuatan layout khusus untuk alur autentikasi (Login dan Daftar), serta audit kepatuhan terhadap data aktual database jadwal dan penghapusan testimoni fiktif.

Seluruh fondasi teknis P3, sistem komponen Blade, keamanan alur pembayaran, dan integritas database tetap dipertahankan 100%.

---

## 2. BAGIAN YANG DIHAPUS PADA BERANDA & FOOTER

Berdasarkan hasil tinjauan pemilik proyek dan penandaan visual, elemen-elemen berikut telah dihapus secara bersih:

1. **Badge Hero:** Menghapus lencana *"Armada Travel Eksekutif Antar Kota"* di atas headline utama hero.
2. **Kelompok CTA Hero:** Menghapus tombol *"Pesan Tiket Sekarang"*, tombol *"Lihat Rute Populer"*, serta tiga indikator mikro (*"Maksimal 5 Kursi"*, *"Anti Double-Booking Lock"*, *"Verifikasi Manual Aman"*). Struktur hero kini berfokus kuat pada headline, deskripsi, serta kartu foto travel eksekutif dengan floating badge kapasitas.
3. **Badge Kartu Pencarian Jadwal:** Menghapus lencana visual *"Update Kursi Real-Time"* di pojok kanan kartu pencarian. Mekanisme pencarian, filter kota asal, kota tujuan, dan tanggal perjalanan tetap bekerja utuh.
4. **Section CTA Gelap Sebelum Footer:** Menghapus satu section gelap (*"Siap Melakukan Perjalanan Antar Kota?"*, badge, deskripsi, tombol cari, dan tombol daftar) secara utuh.
5. **Kolom Navigasi & Dua Tombol CTA Footer Ilustratif (`<x-footer-crew>`):**
   - Menghapus 3 kolom navigasi footer teks (*"LAYANAN TIKET"*, *"AREA PENUMPANG"*, *"BANTUAN & LEGAL"*) di sisi atas footer Doodle Crew.
   - Menghapus tombol *"Pesan Tiket Sekarang"* dan *"Lihat Rute Perjalanan"* di bagian bawah footer.
   - Menjadikan band atas footer berfokus pada logo dan deskripsi brand Gotix di sisi kiri secara bersih (menyerupai referensi visual Picnic Club), sementara tautan navigasi dan tombol *Ke Atas* tetap tersedia secara ringkas dan rapi pada bottom bar footer.
6. **Section Testimoni:** Menghapus section testimoni fiktif karena tidak memiliki sumber data aktual di database, sesuai arahan untuk tidak menampilkan data dummy sebagai ulasan nyata.

---

## 3. PERUBAHAN NAVBAR PUBLIK

Pembaruan navbar pada [`resources/views/layouts/tamu.blade.php`](file:///d:/SEMESTER%205/Pemeliharaan%20Perangkat%20Lunak/Gotix/resources/views/layouts/tamu.blade.php):

- **Proporsi & Skala:** Tinggi navbar disesuaikan menjadi `h-20` proporsional dengan padding yang lega dan tidak tampak kerdil.
- **Identitas Logo (Kiri):** Logo GOTIX dengan kotak Gotix Amber `#F59E0B`, border bertinta 2px (`#18181B`), bayangan offset retro `2.5px`, tipografi Bricolage Grotesque font-black, dan tautan langsung ke `route('beranda')`.
- **Menu Navigasi (Tengah):** Menu `Beranda` (`route('beranda')`) dan `Cari Jadwal` (`route('jadwal.cari')`) dengan *active state* tegas (warna amber lembut berarsir dan border 2px ink).
- **Aksi Autentikasi / Sesi (Kanan):**
  - **Status Tamu (Guest):** Tombol `Masuk` bergaya *secondary/outline* (bg putih, border 2px ink, shadow retro) dan tombol `Daftar` bergaya *primary* Gotix Amber `#F59E0B` dengan border bertinta dan teks hitam tebal.
  - **Status Terautentikasi (Member):** Menampilkan badge profil ringkas pengguna yang mengarah ke portal Member dan tombol `Keluar` (POST `route('keluar')`), tanpa menduplikasi tab navigasi yang sudah ada di halaman internal Member (`member/_nav.blade.php`).
  - **Status Operator / Admin:** Menampilkan tombol panel kontrol khusus role dan tombol keluar.
- **Responsivitas & Menu Mobile:** Hamburger toggle interaktif berbasis Alpine.js (`mobileMenu`), transisi slide-down halus, serta pemisahan menu dan tombol aksi yang jelas pada layar ponsel. Tidak ada `href="#"` atau link placeholder.

---

## 4. PERUBAHAN LAYOUT & DESAIN LOGIN DAN DAFTAR

### A. Layout Khusus Autentikasi (`resources/views/layouts/auth.blade.php`)
Dibuat layout terisolasi khusus halaman Login dan Register dengan aturan ketat:
- **Bebas Navbar Publik & Member:** Tidak merender navbar publik, tidak merender menu tab dashboard, dan tidak menampilkan tombol Masuk/Daftar yang redundant di navbar.
- **Header Minimalis:** Hanya memuat logo GOTIX di sisi kiri dan tombol taktil `Kembali ke Beranda` di sisi kanan.
- **Latar Belakang Taktil:** Menggunakan palet Warm Paper `#FAFAF9` dengan pola dot radial halus.
- **Bebas Doodle Crew Footer:** Footer publik ilustratif dihilangkan dari halaman autentikasi dan digantikan dengan footer legal ringkas satu baris.

### B. Halaman Masuk (`resources/views/auth/masuk.blade.php`)
- Menggunakan `layouts.auth`.
- Kartu retro taktil (`border-2 border-slate-900 shadow-[5px_5px_0px_#18181b] bg-white rounded-2xl`).
- Mendukung input `login` (fleksibel untuk username atau email sesuai `MasukRequest`).
- Input kata sandi dilengkapi tombol toggle intip sandi (*show/hide password*) dengan SVG icon.
- Checkbox `ingat_saya` dengan styling retro.
- Penanganan visual untuk `rateLimitMessage` (HTTP 429) dan error validasi Laravel.
- Indikator status tombol submit (*loading spinner / submitting state*).
- Tautan menuju halaman pendaftaran member baru.

### C. Halaman Pendaftaran (`resources/views/auth/daftar.blade.php`)
- Menggunakan `layouts.auth`.
- Kartu retro taktil proporsional (`border-2 border-slate-900 shadow-[6px_6px_0px_#18181b]`).
- Seluruh kolom sesuai kontrak `DaftarRequest`:
  - `nama`: Nama Lengkap Sesuai KTP
  - `username`: Username unik (min. 3 karakter, alfanumerik)
  - `email`: Alamat email (opsional)
  - `telepon`: Nomor kontak WhatsApp aktif
  - `jenis_kelamin`: Dropdown pilihan `L` (Laki-laki) / `P` (Perempuan)
  - `alamat`: Alamat domisili
  - `password` & `password_confirmation`: Kata sandi minimal 8 karakter dengan toggle intip sandi
- Dukungan penanganan rate limiting registrasi per IP dan error validasi komprehensif.
- Tautan menuju halaman masuk bagi pengguna yang sudah terdaftar.

---

## 5. AUDIT SUMBER DATA JADWAL & TESTIMONI

1. **Jadwal Keberangkatan Beranda:**
   - Dikontrol oleh `App\Http\Controllers\Publik\BerandaController::index()` yang mengambil data aktual:
     `Jadwal::whereDate('tanggal_berangkat', '>=', now()->toDateString())->take(6)->get()`.
   - Data yang ditampilkan di kartu beranda bersumber langsung dari database nyata (`gotix`). Tidak ada jadwal atau rute yang di-hardcode seolah-olah aktif.
   - Jika jadwal di database kosong, sistem merender *empty state* taktil yang mengarahkan pengunjung ke halaman pencarian jadwal secara ramah.
2. **Audit Testimoni:**
   - Tidak ada model atau tabel testimoni di database.
   - Sesuai instruksi pemilik proyek, seluruh kartu testimoni fiktif telah dihapus dari beranda.

---

## 6. DAFTAR FILE YANG DIBUAT DAN DIUBAH

| File | Status | Keterangan Perubahan |
|---|---|---|
| `resources/views/layouts/auth.blade.php` | **Dibuat (Baru)** | Master layout minimalis untuk Login dan Register tanpa navbar publik / Doodle Footer. |
| `resources/views/auth/masuk.blade.php` | **Diubah** | Redesign formulir masuk menggunakan `layouts.auth`, input taktil, show/hide password, error & rate limit alert. |
| `resources/views/auth/daftar.blade.php` | **Diubah** | Redesign formulir pendaftaran menggunakan `layouts.auth`, grid input taktil, validasi lengkap, error & rate limit alert. |
| `resources/views/layouts/tamu.blade.php` | **Diubah** | Redesign navbar publik (skala proporsional h-20, active state jelas, outline Masuk, Gotix Amber Daftar, mobile menu responsif). |
| `resources/views/publik/beranda.blade.php` | **Diubah** | Penghapusan badge hero, CTA hero, mikro pilar, badge pencarian, dark CTA section, dan testimoni fiktif. |
| `resources/views/components/footer-crew.blade.php` | **Diubah** | Penghapusan dua tombol CTA di bawah tagline footer sesuai permintaan revisi visual. |
| `docs/29-phase-3.1-visual-auth-refinement.md` | **Dibuat (Baru)** | Dokumentasi laporan teknis Phase P3.1. |

---

## 7. HASIL PENGUJIAN & VERIFIKASI KUALITAS

### A. Asset Compilation (Vite)
Kompilasi build produksi berhasil tanpa error:
```text
vite v7.3.7 building client environment for production...
✓ 62 modules transformed.
public/build/assets/app-CPGKDlOM.css   77.69 kB │ gzip: 13.47 kB
public/build/assets/app-DDpvETUX.js   114.55 kB │ gzip: 41.40 kB
✓ built in 1.79s
```

### B. Pint Code Style
Tidak ada pelanggaran sintaks PHP pada file-file yang dimodifikasi.

### C. Targeted & Full Test Suite (Pest PHP)
Pengujian dijalankan pada konfigurasi pengujian terisolasi `gotix_testing`:
- **Pengujian Terarah (`AuthTest`, `AuthRateLimitingTest`, `CoreWorkflowAndTicketTest`):**  
  `47 passed (150 assertions)` dalam 17.01s.
- **Full Test Suite (`php artisan test`):**  
  ```text
     PASS  Tests\Unit\ExampleTest
     PASS  Tests\Feature\AuthRateLimitingTest (10 tests)
     PASS  Tests\Feature\AuthTest (18 tests)
     PASS  Tests\Feature\BusinessConfigurationTest (7 tests)
     PASS  Tests\Feature\CoreWorkflowAndTicketTest (19 tests)
     PASS  Tests\Feature\ExampleTest (1 test)
     PASS  Tests\Feature\ModelTest (16 tests)
     PASS  Tests\Feature\PaymentProofSecurityTest (16 tests)
     PASS  Tests\Feature\PaymentVerificationSecurityTest (27 tests)
     PASS  Tests\Feature\SeatReservationIntegrityTest (13 tests)
     PASS  Tests\Feature\TransactionHistoryProtectionTest (19 tests)

    Tests:    147 passed (522 assertions)
    Duration: 23.24s
  ```
  Seluruh 147 test skenario berhasil lulus **100%**.

---

## 8. KONFIRMASI INTEGRITAS ARSITEKTUR

1. **Database & Migrasi:** Tidak ada file migration yang ditambah atau diubah. Struktur tabel dan data database utama `gotix` tetap utuh.
2. **Backend Authentication & Rate Limiting:** Controller autentikasi (`MasukController`, `DaftarController`), `MasukRequest`, `DaftarRequest`, mekanisme `Auth::attempt`, hashing bcrypt, serta kuota rate limiter login/register tidak dimodifikasi.
3. **Role Scoping:** Halaman Operator Loket (`/operator/*`) dan Admin (`/admin/*`) tidak disentuh dan siap untuk dikerjakan pada fase berikutnya (P4 dan P5).
4. **Git Version Control:** Tidak dilakukan commit maupun push ke remote repository.
