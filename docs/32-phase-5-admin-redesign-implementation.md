# Dokumentasi Implementasi — Phase P5: Redesign Admin Panel & Finalization

> **Status:** Selesai (Completed & Verified)  
> **Tanggal:** 14 November 2026  
> **Lingkup:** Redesign Total Panel Admin GoTix (Layout, Sidebar, Dasbor, dan Seluruh Modul CRUD Master & Transaksi)

---

## 1. Ringkasan Eksekutif

Phase P5 — Redesign Admin Panel merupakan fase akhir dari harmonisasi antarmuka sistem **GoTix Travel** ke dalam identitas visual **Retro-Sketchbook Tactile** yang sebelumnya telah diimplementasikan pada Portal Member (P3) dan Operasional Loket (P4).

Pada implementasi ini, seluruh 18 file view antarmuka Administrator beserta layout utamanya telah mengalami perombakan menyeluruh dari tema lama (*sky-blue/slate modern generic*) menjadi antarmuka administrasi berbasis token desain GOTIX:
- **Gotix Amber (`#F59E0B`)**: Aksen status aktif menu, tombol utama, dan sorotan operasional.
- **Midnight Ink (`#18181B`)**: Border tegas 2px (`border-slate-900`), bayangan tactile (`shadow-[2px_2px_0px_#18181b]`, `shadow-[4px_4px_0px_#18181b]`), dan latar belakang sidebar panel.
- **Warm Paper (`#FAFAF9`)**: Latar belakang konten utama (`bg-stone-50`).
- **Card White (`#FFFFFF`)**: Permukaan kartu data dan tabel.
- **Tipografi**: Bricolage Grotesque untuk heading dan angka metrik, DM Sans untuk body, tabel, dan formulir.
- **Logo Utama**: Lingkaran doodle berbingkai tinta hitam dengan nama resmi **"GoTix."** dipertahankan secara utuh pada sidebar dan navbar.

---

## 2. Rincian Modul dan Berkas yang Diubah

### A. Layout Utama & Navigasi
1. **`resources/views/layouts/admin.blade.php`**
   - **Sidebar Midnight Ink (`#18181B`)**: Menampilkan Logo Lingkaran Doodle GoTix dengan tag `ADMIN PANEL`, border tinta tebal, bayangan tactile.
   - **Grup Navigasi Rapi**:
     - *Menu Utama*: Dasbor (`admin.dasbor`)
     - *Master Data*: Master Jadwal (`admin.jadwal.*`), Master Kota (`admin.kota.*`), Master Armada (`admin.kendaraan.*`)
     - *Transaksi & Pengguna*: Daftar Pesanan (`admin.pesanan.*`), Data Pengguna (`admin.pengguna.*`), Keluhan Member (`admin.keluhan.*`)
   - **State Menu Aktif**: Menggunakan `bg-amber-500 text-slate-950 font-heading font-black border-2 border-slate-900 shadow-[3px_3px_0px_#000]`.
   - **Profil & Footer Sidebar**: Kartu avatar inisial Administrator dengan indikator aktif hijau, tombol logout tactile (`bg-stone-900 hover:bg-rose-600 border-2`), dan pintasan kembali ke Beranda Publik.
   - **Top Header Bar**: Sticky bar putih ber-border hitam dengan judul halaman dinamis (@yield('page-title')), sub-judul, dan pill status Administrator Aktif.
   - **Footer Mini**: Terintegrasi `<x-footer-mini role="Admin" />` di dasar halaman.

2. **`resources/views/admin/_nav.blade.php`**
   - Bar header ringkas terpadu dengan deskripsi modul operasional.
   - 7 Quick Tab Buttons horizontal bergaya tactile button dengan border 2px dan drop shadow untuk akses instan antar modul master.

---

### B. Dasbor Operasional
3. **`resources/views/admin/dasbor.blade.php`**
   - **Alert Banner Verifikasi**: Kotak tactile Gotix Amber interaktif ketika ada pesanan menunggu verifikasi transfer manual (`$menanggguVerifikasi > 0`) dengan tombol aksi "Verifikasi Sekarang", atau kotak hijau saat antrean bersih.
   - **4 KPI Stat Cards**:
     - *Jadwal Perjalanan* (`$totalJadwal`) — Badge Amber.
     - *Pesanan Masuk* (`$totalPesanan`) — Badge Sky.
     - *Pesanan Lunas* (`$totalLunas`) — Badge Emerald.
     - *Pengguna Sistem* (`$totalPengguna`) — Badge Purple.
   - **Tabel Jadwal Terbaru**: Menampilkan rute, waktu keberangkatan, unit armada, tarif tiket, serta badge ketersediaan kursi terproteksi (5 kursi).
   - **Pusat Kendali Master (Quick Access)**: Kartu akses cepat navigasi dengan counter real-time untuk Jadwal, Armada, Kota, dan Keluhan.

---

### C. Master Data: Jadwal Keberangkatan
4. **`resources/views/admin/jadwal/index.blade.php`**
   - Bar aksi dengan counter jadwal aktif dan tombol tambah jadwal baru.
   - Tabel berbasis `x-card` dengan status ketersediaan kursi (`x Kursi Tersedia` vs `Penuh`).
   - Tombol aksi terpadu: Detail (`x-button variant="secondary"`), Edit (`x-button variant="primary"`), dan Hapus (`x-button variant="danger"`).
5. **`resources/views/admin/jadwal/create.blade.php`**
   - Form pembuatan rute baru dalam `x-card`: Pilihan Kota Asal, Kota Tujuan, Armada Kendaraan, Tanggal Berangkat, Jam Berangkat, dan Tarif Tiket per kursi dengan border tinta dan shadow tactile.
6. **`resources/views/admin/jadwal/edit.blade.php`**
   - Form pembaruan jadwal dengan prefilled data lama dan metode HTTP `PUT`.
7. **`resources/views/admin/jadwal/show.blade.php`**
   - 4 Kartu metrik: Kapasitas Armada, Keterisian Kursi, Tarif Resmi, dan Penerimaan Lunas.
   - **Denah Visual 5 Kursi**: Grid interaktif 5 kursi menampilkan nomor kursi, nama penumpang terdaftar, kode booking dengan link detail, dan status pembayaran warna terpadu (Lunas, Verifikasi, Belum Bayar, Tersedia).
   - Tabel manifest transaksi tiket terkait dengan `<x-badge-status>`.

---

### D. Master Data: Kota Layanan
8. **`resources/views/admin/kota/index.blade.php`**
   - Tabel kota terdaftar dengan statistik jadwal aktif sebagai asal dan jadwal sebagai tujuan.
9. **`resources/views/admin/kota/create.blade.php`**
   - Form penambahan kota baru dengan validasi dan tata letak tactile.
10. **`resources/views/admin/kota/edit.blade.php`**
    - Form edit nama kota dengan feedback error yang konsisten.

---

### E. Master Data: Armada Kendaraan
11. **`resources/views/admin/kendaraan/index.blade.php`**
    - Grid kartu armada 3-kolom bergaya sketchbook tactile: Ikon mobil, jenis kendaraan, nomor polisi monospaced, warna unit, kapasitas (5 kursi), dan jumlah riwayat jadwal penugasan.
12. **`resources/views/admin/kendaraan/create.blade.php`**
    - Form registrasi unit armada baru (jenis kendaraan, nopol, warna, kapasitas).
13. **`resources/views/admin/kendaraan/edit.blade.php`**
    - Form pembaruan spesifikasi unit kendaraan.

---

### F. Transaksi & Rekapitulasi Pesanan
14. **`resources/views/admin/pesanan/index.blade.php`**
    - Quick filter tabs interaktif Alpine.js: Semua, Menunggu Verifikasi (Dalam Proses), Lunas, Belum Bayar, Dibatalkan.
    - Input pencarian real-time berdasarkan kode booking dan nama pemesan.
    - Tabel pesanan dilengkapi tag asal pemesanan (*Member* vs *Loket*).
    - Status badge menggunakan komponen `<x-badge-status>`.
15. **`resources/views/admin/pesanan/show.blade.php`**
    - Detail lengkap data perjalanan dan penumpang.
    - Timeline audit trail verifikasi pembayaran (`riwayatPembayaran`) dengan nama verifikator dan waktu verifikasi.
    - Pratinjau struk bukti transfer manual dengan tautan pembesar file.
    - Tombol aksi verifikasi aman (Terima Lunas vs Tolak Pembayaran) dengan modal penolakan alasan wajib.

---

### G. Manajemen Data Pengguna
16. **`resources/views/admin/pengguna/index.blade.php`**
    - Tab filter peran akun (Semua, Member, Operator, Admin) dan kolom pencarian.
    - Avatar inisial dengan badge peran tactile dan counter jumlah transaksi.
17. **`resources/views/admin/pengguna/show.blade.php`**
    - Kartu profil lengkap pengguna (username, email, telepon, jenis kelamin, alamat, tanggal registrasi).
    - Tabel riwayat perjalanan dan pemesanan tiket pengguna terkait.

---

### H. Layanan Keluhan Member
18. **`resources/views/admin/keluhan/index.blade.php`**
    - Rekapitulasi keluhan penumpang dengan pill kategori (Pelayanan, Jadwal, Armada, Fasilitas) dan indikator status tanggapan (*Sudah Dibalas* vs *Menunggu Tanggapan*).
19. **`resources/views/admin/keluhan/show.blade.php`**
    - Informasi pelapor dan deskripsi lengkap keluhan.
    - Tanggapan resmi yang tersimpan beserta stempel waktu.
    - Form pengiriman/pembaruan balasan resmi Admin ke akun Member.

---

## 3. Komponen Reusable yang Digunakan

| Komponen | Kegunaan dalam Admin Panel |
| :--- | :--- |
| `<x-card>` | Pembungkus utama tabel data, kartu denah kursi, dan formulir input |
| `<x-button>` | Tombol aksi utama (primary/amber, secondary/white, danger/rose, dark) |
| `<x-badge-status>` | Penanda status transaksi pesanan (Lunas, Dalam Proses, Belum Bayar, Dibatalkan) |
| `<x-footer-mini>` | Footer standar di bagian bawah konten dengan konfigurasi `role="Admin"` |

---

## 4. Hasil Pengujian & Quality Assurance

### A. Kompilasi Aset Frontend (Vite)
- **Perintah:** `npm run build`
- **Hasil:**
  ```text
  vite v7.3.7 building client environment for production...
  transforming...
  ✓ 62 modules transformed.
  public/build/manifest.json              0.33 kB │ gzip:  0.17 kB
  public/build/assets/app-CPn4NTKg.css   87.83 kB │ gzip: 15.19 kB
  public/build/assets/app-DDpvETUX.js   114.55 kB │ gzip: 41.40 kB
  ✓ built in 1.60s
  ```
- **Status:** **PASS** (Zero warnings, clean bundle).

### B. Standardisasi Gaya Kode (Laravel Pint)
- **Perintah:** `vendor/bin/pint app/Http/Controllers/Admin/DasborController.php`
- **Hasil:** **PASS** (Semua controller Admin terformat sesuai standar PSR-12/Laravel).

### C. Pengujian Regresi Lengkap (Pest PHP)
- **Perintah:** `php artisan test`
- **Hasil:**
  ```text
  Tests:    150 passed (528 assertions)
  Duration: 24.61s
  ```
- **Cakupan Pengujian Termasuk:**
  - `Tests\Feature\AuthTest`: Akses dasbor admin, otorisasi RBAC (Member/Operator/Admin).
  - `Tests\Feature\PaymentVerificationSecurityTest`: Alur verifikasi pembayaran dan penolakan admin.
  - `Tests\Feature\SeatReservationIntegrityTest`: Integritas reservasi kuota 5 kursi terproteksi.
  - `Tests\Feature\TransactionHistoryProtectionTest`: Perlindungan master data kota, kendaraan, dan jadwal.
- **Status:** **100% PASS** (150/150 pengujian berhasil).

---

## 5. Phase P5.1: Admin UI Cleanup & Create User Feature

Pada iterasi P5.1, dilakukan empat penyempurnaan langsung sesuai instruksi pemilik proyek:

### A. Penghapusan Navigasi Duplikat Antarhalaman
- **Akar Masalah:** Komponen `resources/views/admin/_nav.blade.php` dipanggil di 6 halaman admin (`jadwal/index`, `kota/index`, `kendaraan/index`, `pesanan/index`, `pengguna/index`, `keluhan/index`), menciptakan deretan tombol duplikat horizontal tepat di bawah judul halaman yang membingungkan karena seluruh menu telah tersedia di sidebar.
- **Penyelesaian:** Seluruh panggilan `@include('admin._nav', ...)` telah dihapus dari seluruh halaman admin. Sidebar Midnight Ink kini menjadi satu-satunya navigasi resmi, sementara toolbar fungsional (seperti filter tab, kolom pencarian, dan tombol aksi tambah) tetap dipertahankan secara utuh.

### B. Perbaikan Tampilan Nama & Username pada Tabel Pengguna
- **Akar Masalah:** Penggunaan sintaks `@{{ $pengguna->username }}` menyebabkan Blade menganggapnya sebagai escape syntax untuk frontend framework (Vue/Alpine), sehingga merender teks literal `{{ $pengguna->username }}` alih-alih data aktual.
- **Penyelesaian:** Diperbaiki menjadi `{{ $pengguna->username ? '@' . $pengguna->username : '—' }}` menggunakan Blade escaping standar, dengan fallback tanda strip (`—`) jika username kosong.

### C. Penonaktifan Footer Khusus Dasbor Admin
- **Penyelesaian:** Komponen `<x-footer-mini role="Admin" />` pada `resources/views/layouts/admin.blade.php` dibungkus dengan klausul kondisional `@unless (request()->routeIs('admin.dasbor'))`. Dasbor Admin kini memiliki area konten bersih tanpa footer bawah, sedangkan halaman Admin lainnya tetap memiliki footer mini.

### D. Fitur Tambah Pengguna Baru
- **Antarmuka (`admin/pengguna/index.blade.php`):** Ditambahkan tombol utama `+ Tambah Pengguna` bergaya Gotix Amber dengan hard shadow pada header toolbar.
- **Formulir (`admin/pengguna/create.blade.php`):** Halaman form baru berbasis `<x-card>` yang menampung input Nama Lengkap, Username, Email, Nomor Telepon, Peran Akun (`Member` atau `Operator`), Jenis Kelamin (`L` atau `P`), Alamat Domisili, serta Password & Konfirmasi Password.
- **Backend & Keamanan (`app/Http/Controllers/Admin/PenggunaController.php`):**
  - Route `admin.pengguna.create` (GET `/admin/pengguna/tambah`) dan `admin.pengguna.store` (POST `/admin/pengguna`).
  - Validasi ketat: Uniqueness username & email, format `alpha_dash`, konfirmasi password minimum 8 karakter.
  - Otorisasi Peran: Hanya mengizinkan pembuatan akun `Member` dan `Operator` (`Rule::in(['Operator', 'Member'])`). Pembuatan peran Admin baru diblokir oleh validasi server-side.
  - Password tersimpan dalam bentuk hash (`Hash::make()`), tidak pernah terekspos di tabel atau detail pengguna.

### E. Hasil Pengujian Terarah P5.1 (`AdminPenggunaManagementTest.php`)
- **Hasil:** 10 passed (69 assertions) — 100% PASS.

---

## 6. Kesimpulan

Seluruh ruang lingkup Phase P5 dan P5.1 telah selesai diimplementasikan secara komprehensif pada level kode sumber dan antarmuka Blade, selaras dengan design system Member dan Operator tanpa mengubah struktur database maupun logika bisnis backend.
