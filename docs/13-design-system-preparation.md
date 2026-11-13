# 13 — Design System Preparation

> **Catatan Fase:** Dokumen ini menetapkan **arsitektur struktural dan katalog komponen** untuk menyatukan pengalaman **Customer/Member**, **Operator Loket**, dan **Admin** di bawah satu Design System terpadu.  
> **Belum menentukan warna final, font final, ataupun referensi visual** — keputusan token visual menunggu persetujuan dan referensi desain dari pemilik proyek pada tahap implementasi desain.

---

## 1. Prinsip Arsitektur Design System Terpadu

1. **Satu Sistem Visual, Tiga Pengalaman Berbasis Peran:**
   - **Customer / Member**, **Operator Loket**, dan **Admin** berbagi token semantik yang sama (warna status pesanan, hierarki tipografi, ketebalan border, radius sudut, gaya formulir, tabel, dan pola interaksi modal), sehingga aplikasi terasa sebagai satu produk utuh.
   - Struktur navigasi, kepadatan informasi (*information density*), dan alur aksi diadaptasi sesuai konteks tugas tiap peran.
2. **Pemisahan Tegas antara Shell Publik vs. Portal Operasional:**
   - Saat ini `Member` dan `Operator` masih mewarisi `layouts/tamu.blade.php` (lengkap dengan navbar publik, footer komersial, CDN AOS, dan splash screen 850ms), sedangkan `Admin` memakai `layouts/admin.blade.php` (sidebar gelap), dan `layouts/operator.blade.php` tidak terpakai.
   - Arsitektur target mengeliminasi fragmen tersebut menjadi **dua tipe shell utama** di atas satu fondasi Design System:
     1. **Public & Auth Shell:** Ringan, fokus konversi pencarian perjalanan dan autentikasi (tanpa splash blocking berulang).
     2. **Unified Authenticated Shell (App / Workspace Shell):** Adaptif berdasarkan `PeranPengguna`.

---

## 2. Arsitektur Pengalaman per Peran

### 2.1 Public / Guest (`beranda`, `jadwal.cari`, `masuk`, `daftar`)
- **Konteks Pengguna:** Calon penumpang yang mencari rute Lampung, membandingkan jadwal/harga/sisa kursi, lalu login/daftar untuk memesan.
- **Navigasi & Header:** Top navigation bar sederhana (Logo GOTIX, Beranda, Cari Jadwal, tombol Masuk / Daftar, atau tombol cepat ke Workspace aktif bila user sudah login sesuai perannya agar Admin/Operator tidak diarahkan ke rute Member).
- **Footer:** Informasi ringkas rute/layanan Lampung, jam operasional, dan kanal resmi dari konfigurasi (`config/gotix.php`).
- **Layout:** Konten berpusat dengan lebar maksimal terukur, kartu pencarian cepat menonjol, serta daftar jadwal responsif.

### 2.2 Customer / Member (`/akun/*`)
- **Konteks Pengguna:** Konsumen akhir (*B2C*), membutuhkan alur berurutan yang tenang, jelas, dan ramah perangkat mobile (*mobile-first*).
- **Navigasi & Header:**
  - Top bar / navigasi sekunder konsisten di **seluruh halaman Member** (tidak boleh hilang di `pesanan/create`, `pesanan/show`, `pembayaran/show`, `tiket/show`, atau `keluhan/create` seperti kondisi sekarang).
  - Item navigasi: `Cari Jadwal`, `Pesanan Saya`, `E-Tiket`, `Keluhan`, `Profil / Keluar`.
- **Kebutuhan Alur Khusus:**
  - Pemilih kursi visual adaptif terhadap `kendaraan.kapasitas_kursi` dinamis (bukan `grid-cols-5` statis).
  - Stepper status pesanan yang jelas (`Belum Bayar` -> `Dalam Proses` -> `Lunas` / `Dibatalkan`) disertai countdown `batas_bayar`.
  - E-tiket yang bersih untuk tampilan layar maupun mode `@media print`.

### 2.3 Operator Loket (`/operator/*`)
- **Konteks Pengguna:** Petugas konter (*operational frontline*), mengutamakan kecepatan pemrosesan antrean verifikasi bukti transfer dan transaksi walk-in tanpa gangguan footer pemasaran.
- **Navigasi & Header:**
  - Menggunakan **Authenticated Workspace Shell** (sidebar ringkas / header kerja khusus petugas tanpa footer publik maupun splash screen).
  - Item navigasi: `Verifikasi Pembayaran` (dengan indikator jumlah antrean `Dalam Proses`), `Kasir Loket (Walk-in)`.
- **Kebutuhan Alur Khusus:**
  - Antrean verifikasi menampilkan pratinjau bukti bayar besar/zoomable, detail resi & `rekening_pengirim`, dan modal konfirmasi penolakan yang mewajibkan input `alasan_penolakan`.
  - Layar loket split-view (pilihan jadwal aktif & denah kursi real di kiri, form penumpang & ringkasan tunai/cetak di kanan).

### 2.4 Admin (`/admin/*`)
- **Konteks Pengguna:** Pengelola sistem (*backoffice*), membutuhkan kepadatan data tinggi, tabel sortable/filterable, dan kontrol CRUD aman.
- **Navigasi & Header:**
  - Menggunakan **Authenticated Workspace Shell** yang sama secara komponen dengan Operator (sidebar kiri kolapsibel di mobile + top header breadcrumb & info pengguna aktif).
  - Kelompok menu terstruktur:
    - *Ringkasan:* `Dasbor`
    - *Operasional:* `Jadwal Keberangkatan`, `Monitoring Pesanan`, `Keluhan Penumpang`
    - *Data Master & Akses:* `Kota Layanan`, `Armada Kendaraan`, `Pengguna & Staf`
- **Kebutuhan Alur Khusus:**
  - Form `create` & `edit` memakai satu partial/komponen seragam (`_form.blade.php` + komponen input).
  - Modal konfirmasi destruktif yang menjelaskan dampak/relasi sebelum soft delete/penonaktifan data master.

---

## 3. Katalog Kategori Komponen Reusable (`resources/views/components/`)

Seluruh komponen berikut dirancang sebagai Blade Components (`<x-...>`) yang memakai token desain tunggal (nantinya didefinisikan via CSS custom properties / konfigurasi Tailwind terpilih) dan menggantikan markup inline yang saat ini terduplikasi.

| Kategori | Nama Komponen yang Diusulkan | Cakupan Varian & Kebutuhan Teknis | Menggantikan Duplikasi Saat Ini |
|---|---|---|---|
| **1. Buttons** | `<x-button>`, `<x-icon-button>` | Varian semantik: `primary`, `secondary`, `ghost`, `danger`, `success`. Ukuran: `sm`, `md`, `lg`. Mendukung tag `<a>` vs `<button>`, state `disabled`, dan `aria-label` untuk tombol ikon. | Tombol submit/link beragam kelas di 35+ file Blade. |
| **2. Inputs** | `<x-input>`, `<x-textarea>`, `<x-file-input>`, `<x-form-field>` | Pembungkus standar: `<label for="...">` terikat otomatis ke `id`, teks bantuan (`hint`), slot pesan error `@error`, penghitung karakter opsional (untuk deskripsi keluhan), aksesibilitas `aria-invalid` & `aria-describedby`. | Input mentah di auth, pembayaran, keluhan, & form CRUD admin yang sering tanpa `for`/`id`. |
| **3. Selects** | `<x-select>` | Select standar dengan chevron konsisten, placeholder option, binding `old()`, error state, dan dukungan dropdown dinamis Alpine.js bila diperlukan. | `<select>` dengan wrapper panah SVG duplikat di `beranda`, `cari-jadwal`, `keluhan/create`, `loket`, `jadwal/*`. |
| **4. Tables** | `<x-table>`, `<x-th>`, `<x-td>` | Wrapper `overflow-x-auto`, header seragam, alignment kolom (`left`/`right`/`numeric`), hover row, serta slot kosong terintegrasi. | Tabel HTML panjang berulang di `admin/*` dan `operator/pembayaran/index`. |
| **5. Cards** | `<x-card>`, `<x-stat-card>` | Kartu konten dasar (header, body, footer opsional) serta kartu KPI metrik dasbor (ikon, label, angka, sub-info) yang konsisten di Admin Dasbor, Operator Verifikasi, dan Kartu Jadwal. | Card metrik dan box putih `rounded-2xl`/`rounded-3xl` yang inkonsisten antar halaman. |
| **6. Status Badges** | `<x-status-badge>` | Pemetaan tunggal terpusat terhadap `StatusPesanan` enum (`BelumBayar`, `DalamProses`, `Lunas`, `Dibatalkan`) dan status keluhan (`Menunggu Balasan`, `Dibalas`), menjamin warna, label, dan indikator dot identik di semua peran. | Blok `@php @match/@if` badge yang diduplikasi secara inkonsisten di 8+ view. |
| **7. Modals** | `<x-modal>`, `<x-confirm-modal>` | Dialog aksesibel berbasis Alpine (`x-cloak`, trap/escape close, backdrop klik, tombol aksi utama & batal). Digunakan untuk pratinjau struk bukti transfer, konfirmasi hapus data master, penolakan pembayaran (dengan textarea alasan), serta struk cetak loket. | `confirm()` & `alert()` native browser serta modal zoom manual di `operator/pembayaran/show`. |
| **8. Notifications** | `<x-alert>`, `<x-toast>` | Banner/toast flash session (`sukses`, `gagal`, `warning`, `info`, dan `$errors->any()`) dengan tombol tutup (`dismissible`) yang muncul konsisten di header konten setiap shell. | Blok flash message berbeda-beda di setiap halaman. |
| **9. Navigation** | `<x-nav-link>`, `<x-breadcrumb>`, `<x-tabs>` | Link navigasi aktif (`request()->routeIs(...)`), breadcrumb halaman detail (`show`/`create`/`edit`), dan sub-navigasi horizontal/tab responsif. | `member/_nav`, `operator/_nav`, `admin/_nav`, dan breadcrumb manual. |
| **10. Sidebar** | `<x-workspace-sidebar>` | Sidebar responsif dengan drawer mobile (`Alpine.js`) untuk shell terautentikasi (Admin dan/atau Operator), mendukung pengelompokan menu berdasarkan peran. | Sidebar hardcoded di `layouts/admin.blade.php` & layout tak terpakai `layouts/operator.blade.php`. |
| **11. Header** | `<x-public-header>`, `<x-workspace-header>`, `<x-page-header>` | Header top-bar publik (responsive mobile toggle), top-bar workspace (judul halaman, role badge, user menu/logout), dan header seksi halaman (judul, subjudul, slot tombol aksi kanan). | Navbar di `layouts/tamu.blade.php` dan header judul di setiap halaman CRUD/daftar. |
| **12. Empty States** | `<x-empty-state>` | Pola konsisten ketika daftar jadwal, pesanan, e-tiket, keluhan, atau tabel admin kosong: ikon terpusat, judul ringkas, deskripsi tindakan selanjutnya, dan slot tombol CTA opsional. | Blok empty state berbeda-beda di `cari-jadwal`, `tiket/index`, `pesanan/index`, dan tabel admin. |
| **13. Loading States** | `<x-skeleton>`, `<x-spinner>` | Indikator submit formulir (mencegah double-click pada booking kursi & upload bukti bayar) dan skeleton/spinner untuk operasi asinkron Alpine.js, menggantikan splash screen penuh 850ms `#gotix-splash`. | `#gotix-splash` blocking overlay pada `layouts/tamu.blade.php`. |

---

## 4. Kontrak Token Desain (Placeholder Struktural Sebelum Pemilihan Visual)

Sebelum implementasi visual (Phase 4 / Fase Redesign), file CSS utama & konfigurasi Tailwind hanya akan menyiapkan kategori token abstrak berikut untuk diisi setelah referensi desain dan palet warna disetujui pemilik proyek:

- **Color Roles (Semantic Tokens):** `--color-surface-*`, `--color-text-*`, `--color-border-*`, `--color-brand-primary/*`, `--color-accent/*`, `--color-status-{pending,processing,success,danger,neutral}/*`.
- **Typography Scale:** Keluarga huruf display/heading dan body, skala ukuran, line-height, serta gaya tabular-nums/monospace untuk kode pesanan (`GTX-XXXXXX`), tarif (`Rp`), dan jadwal jam.
- **Spacing & Radius Scale:** Skala konsisten untuk card padding, form gap, border radius kontrol vs container, dan ketebalan border/elevation.
- **Breakpoints:** Standar responsif Tailwind (`sm`, `md`, `lg`, `xl`) untuk menjamin keterpakaian di layar HP (Member) maupun desktop (Operator Loket & Admin).

---

## 5. Prasyarat Sebelum Memulai Implementasi Design System

1. Penetapan versi Tailwind CSS (`P2-01` — rekomendasi teknis: pertahankan **Tailwind v3.4.19** yang sudah terpasang dan berjalan bersih di `vite build`, atau putuskan migrasi ke **Tailwind v4** beserta penyesuaian `@tailwindcss/forms` & `app.css`).
2. Penyelesaian tugas kritis **P0** dan **P1** sehingga setiap komponen Design System terhubung ke data, validasi, dan status database yang asli.
3. Persetujuan pemilik proyek atas **referensi visual, palet warna, tipografi, dan pola navigasi** (misal: apakah Member tetap memakai top-nav modern atau sidebar portal, dan apakah Operator memakai sidebar kerja seperti Admin).

