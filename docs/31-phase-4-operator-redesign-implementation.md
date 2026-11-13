# DOKUMENTASI IMPLEMENTASI PHASE P4: REDESIGN OPERATOR LOKET
## GOTIX Travel Platform — Retro-Sketchbook Tactile Design System

> **Tanggal:** 13 November 2026  
> **Fase:** P4 — Redesign Antarmuka Operator Loket  
> **Status:** Selesai & Terverifikasi  
> **Referensi Utama:** `docs/27-gotix-design-system-proposal.md`, `docs/28-phase-3-member-redesign-implementation.md`, `docs/30-phase-3.2-payment-page-refinement.md`

---

## 1. Ringkasan Eksekutif

Phase P4 berfokus pada perombakan total antarmuka **Operator Loket** pada platform tiket travel **GOTIX** agar selaras dengan identitas visual *Retro-Sketchbook Tactile* yang telah disetujui pada Phase P3 (Member).

Prioritas utama perancangan Operator Loket adalah:
1. **Kecepatan transaksi kasir** konter loket stasiun/terminal untuk melayani penumpang langsung (*Walk-in*) maupun pelanggan akun *Member*.
2. **Kenyamanan dan ketelitian verifikasi transfer manual**, memungkinkan pemeriksaan struk pembayaran beresolusi tinggi, pembesaran modal (*zoom-in*), penelusuran riwayat audit (*audit trail*), serta aksi persetujuan (*approve*) atau penolakan (*reject*) terproteksi.
3. **Penyatuan navigasi ke dalam Layout Khusus Operator** (`layouts/operator.blade.php`), mengeliminasi tumpang tindih navbar publik/member, mencegah kebocoran menu yang tidak relevan, dan menghadirkan identitas shift loket yang jelas.
4. **Penggunaan kembali komponen reusable Blade** hasil Phase P3 (`<x-button>`, `<x-input>`, `<x-card>`, `<x-badge-status>`, `<x-footer-mini>`).
5. **Nol perubahan pada database, migrasi, dan logika transaksi backend** (*seat locking*, integritas *anti double-booking*, dan RBAC tetap 100% utuh).

---

## 2. Komponen Design System yang Digunakan Kembali (Reuse)

| Komponen Blade | Penggunaan pada Operator Loket |
| :--- | :--- |
| `<x-card>` | Membungkus antrean verifikasi pembayaran, denah kursi loket, formulir identitas penumpang walk-in, ringkasan transaksi, serta riwayat audit bukti bayar. |
| `<x-button>` | Tombol tindakan taktil dengan varian `primary` (Gotix Amber untuk verifikasi & proses tiket loket), `outline` (pemeriksaan berkas & navigasi kembali), dan `danger` (penolakan pembayaran). |
| `<x-input>` | Formulir nama penumpang dan nomor telepon/WhatsApp walk-in dengan border tinta 2px dan drop shadow offset. |
| `<x-badge-status>` | Lencana status pesanan taktil (`Dalam Proses`, `Lunas`, `Belum Bayar`, `Dibatalkan`) pada tabel antrean dan kartu detail transaksi. |
| `<x-footer-mini>` | Ditingkatkan dengan prop `role="Operator"` untuk menyajikan footer utilitas ringkas bertuliskan *Panel Operasional Loket & Kasir* tanpa tautan member yang tidak relevan. |

---

## 3. Tata Letak & Navigasi Khusus Operator

### A. Dedicated Layout (`resources/views/layouts/operator.blade.php`)
- **Sticky Tactile Navbar:** Ketinggian `h-20` konsisten dengan portal member, berlatar `bg-white/95 backdrop-blur-md` dengan garis batas bawah tinta `border-b-2 border-slate-900`.
- **Identitas Brand:** Logo Gotix taktil dengan lencana Amber-500 dan pill badge peran:
  ```html
  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 border-2 border-slate-900 text-slate-950 text-[11px] font-heading font-black shadow-[1.5px_1.5px_0px_#18181b]">
      <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
      <span>LOKET OPERATOR</span>
  </span>
  ```
- **Menu Operasional Utama:**
  1. **Verifikasi Pembayaran** (`operator.pembayaran.index`) — aktif saat mengakses `/operator/pembayaran*`.
  2. **Kasir Loket & Walk-in** (`operator.loket.index`) — aktif saat mengakses `/operator/loket*`.
- **Area Kanan:**
  - Badge identitas operator aktif dengan inisial avatar (`OP`), nama petugas yang sedang login, dan label status shift loket.
  - Tombol **Keluar** bertema taktil berbasis form `POST` ke route `keluar`.
- **Navigasi Mobile Responsif:** Menu hamburger Alpine.js yang dapat dibuka dan ditutup dengan animasi halus, menyajikan seluruh menu operasional tanpa *overflow*.

### B. Sub-Header Halaman (`resources/views/operator/_nav.blade.php`)
- Menyajikan judul halaman berbobot `font-black text-2xl sm:text-3xl` dengan *tagline* penjelasan modul.
- Pil pintasan cepat (*quick switcher tabs*) untuk beralih instan antara Verifikasi Transfer dan Kasir Walk-in.

---

## 4. Halaman yang Di-Redesign

### A. Verifikasi Pembayaran & Dashboard Operator (`operator/pembayaran/index.blade.php`)
- **Metric KPI Aktual dari Database:**
  - *Perlu Tindakan*: Total transaksi berstatus `Dalam Proses` yang struknya menunggu pemeriksaan operator.
  - *Belum Bayar*: Total transaksi yang menunggu transfer dari penumpang.
  - *Total Terverifikasi*: Total tiket yang telah berstatus `Lunas`.
  - *Pintasan Kasir*: Kartu akses langsung untuk melayani pembelian tiket walk-in.
- **Tabel Antrean & Filter Cepat (Alpine.js):**
  - Tab filter instan (*Semua*, *Perlu Ditinjau*, *Belum Bayar*) tanpa *reload* halaman.
  - Kolom tabel bergaris tinta tegas: Kode Booking & Pemesan, Rute & Armada Keberangkatan, Nominal Tagihan & Rekening, Lencana Status Taktil, dan Tombol Tindakan.
  - Kartu taktil responsif untuk layar ponsel/tablet kecil sehingga terbebas dari *horizontal scrolling*.
  - State kosong (*empty state*) taktil saat seluruh antrean verifikasi telah tuntas.

### B. Detail Pemeriksaan Bukti Pembayaran (`operator/pembayaran/show.blade.php`)
- **Navigasi Atas:** Tombol kembali ke antrean verifikasi dan lencana status pesanan terkini.
- **Split-View Desktop (12 Kolom):**
  - **Kolom Kiri (7 Kolom):**
    - Kartu Rincian Transaksi: Kode booking `#GTX-XXXXXX`, waktu pemesanan, tenggat pembayaran, dan status penumpang (Member vs Walk-in).
    - Kartu Rute Perjalanan & Armada: Kota asal &rarr; kota tujuan, tanggal, jam keberangkatan, jenis kendaraan, plat nomor, dan nomor kursi.
    - Kartu Pencocokan Nominal: Total tagihan resmi, nama bank/rekening pengirim, nomor resi referensi, dan waktu transfer.
    - Kartu Audit Trail Unggahan (`riwayatPembayaran`): Kronologi riwayat unggahan bukti transfer sebelumnya (jika ada penolakan sebelumnya) lengkap dengan nama petugas verifikator dan alasan penolakan.
  - **Kolom Kanan (5 Kolom):**
    - Kartu Pratinjau Berkas Bukti: Gambar struk berbingkai border tinta 2px dengan efek hover pembesar.
    - Modal Pembesaran (*Zoom-In Lightbox*): Menampilkan struk ukuran penuh dengan latar gelap dan tombol tutup.
    - Panel Keputusan Operator (*State Guarded*):
      - Jika `Dalam Proses`: Tombol *Terima & Terbitkan Tiket* (POST `operator.pembayaran.verifikasi`) dengan dialog konfirmasi, dan tombol *Tolak Pembayaran...*.
      - Modal Alasan Penolakan: Form penolakan wajib dengan textarea `alasan_penolakan` (POST `operator.pembayaran.tolak`), mengembalikan status pesanan ke `Belum Bayar` agar penumpang dapat memperbaiki unggahan.
      - Jika `Lunas`: Kotak konfirmasi sukses dengan info nama verifikator dan waktu validasi.
      - Jika `Belum Bayar`: Kotak status menunggu unggahan bukti dari penumpang.

### C. Kasir Pemesanan Loket (Walk-in) (`operator/loket/index.blade.php`)
- **Split-View Kasir Konter:**
  - **Kolom Kiri (7 Kolom):**
    - Dropdown Pemilihan Trayek & Jadwal Armada Aktif: Terintegrasi dengan info rute, armada, jam berangkat, tarif, dan kapasitas kursi.
    - Denah Kursi Interaktif (*Tactile Seat Grid*):
      - Indikator area sopir / pintu masuk.
      - Tombol kursi dinamis sesuai kapasitas kendaraan:
        - **Kosong:** Putih, border tinta 2px, bayangan offset, kursor pointer.
        - **Dipilih:** Gotix Amber-500, ring-2 amber-400, terangkat -translate-y-0.5, font-black.
        - **Terisi:** Abu-abu slate-200 dengan pola arsir (*hatch pattern*), nonaktif (*disabled*).
  - **Kolom Kanan (5 Kolom):**
    - Sakelar Tipe Pelanggan: *Walk-in (Umum)* atau *Member Terdaftar*.
    - Input Identitas: Nama penumpang dan nomor WhatsApp untuk walk-in, atau pemilihan akun member terdaftar.
    - Ringkasan Transaksi: Kursi terpilih, total tagihan terformat rupiah otomatis, status transaksi (langsung lunas), dan nama operator bertugas.
    - Tombol Submit Taktil: Dinonaktifkan sebelum kursi dipilih; memicu POST `operator.loket.store` untuk penerbitan tiket langsung lunas.
- **Riwayat Transaksi Loket Terbaru:**
  - Menampilkan 8 transaksi kasir terakhir dari database.
  - Membedakan secara tegas antara pelanggan *Member* (lencana biru) dan *Walk-in* (lencana ungu).
  - Kartu adaptif untuk tampilan seluler.

---

## 5. File yang Dibuat & Dimodifikasi

| No | File | Status | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `resources/views/layouts/operator.blade.php` | Diperbarui | Layout resmi operator beridentitas *Retro-Sketchbook Tactile*. |
| 2 | `resources/views/components/footer-mini.blade.php` | Diperbarui | Penambahan dukungan prop `role="Operator"` untuk footer loket ringkas. |
| 3 | `resources/views/operator/_nav.blade.php` | Diperbarui | Header halaman taktil dan sub-tabs navigasi cepat. |
| 4 | `resources/views/operator/pembayaran/index.blade.php` | Diperbarui | Halaman antrean verifikasi transfer, KPI metric cards, dan tab filter. |
| 5 | `resources/views/operator/pembayaran/show.blade.php` | Diperbarui | Halaman pemeriksaan bukti transfer, audit trail, modal zoom, dan verifikasi/penolakan. |
| 6 | `resources/views/operator/loket/index.blade.php` | Diperbarui | Kasir loket split-view, denah kursi interaktif dinamis, form walk-in, dan riwayat pesanan. |
| 7 | `docs/31-phase-4-operator-redesign-implementation.md` | Dibuat | Dokumentasi lengkap implementasi Phase P4. |

---

## 6. Hasil Verifikasi Kualitas

- **Kompilasi Aset (Vite):**  
  `npm run build` berhasil mengompilasi 62 modul dalam **2.07 detik** tanpa peringatan (`public/build/assets/app-N2xLDXnK.css` dan `app-DDpvETUX.js`).
- **PHP Pint Style Check:**  
  `vendor/bin/pint --test app/Http/Controllers/Operator/` lolos 100% (`{"tool":"pint","result":"passed"}`).
- **Pest PHP Automated Regression Suite:**  
  Dijalankan pada database testing terisolasi `gotix_testing`:  
  ```text
  Tests:    147 passed (522 assertions)
  Duration: 19.11s
  ```
  Seluruh 147 skenario pengujian unit, alur kerja loket operator, otorisasi RBAC, integritas *anti double-booking*, dan keamanan bukti pembayaran lulus **100%**.

---

## 7. Jaminan Integritas Database & Arsitektur

- **Database & Migrasi:** Tidak ada penambahan migrasi, tidak ada alter tabel, dan tidak ada modifikasi data transaksi pada database utama `gotix`.
- **Backend & Transaksi:** Validasi backend, *seat locking* (`lockForUpdate`), penomoran kode pesanan unik `GTX-XXXXXX`, pencatatan `dibuat_oleh_operator_id`, dan otorisasi middleware `PastikanPunyaPeran` (`peran:Operator`) tetap berfungsi 100%.
- **Admin Scope Protection:** Seluruh antarmuka admin (`resources/views/admin/*`) tidak tersentuh dan dialokasikan untuk Phase P5.
- **Git State:** Tidak ada commit atau push yang dilakukan ke remote repository.

---

## 8. Catatan Penyempurnaan P4.1 (Operator Navigation & Feature Completeness)

Berdasarkan pemeriksaan dan arahan pemilik proyek pada P4.1, dilakukan penyempurnaan fitur dan aksesibilitas antarmuka operator:

### A. Struktur Menu & Named Routes Nyata
Navigasi operator kini menyediakan akses yang jelas ke seluruh domain operasional tanpa link palsu:
1. **Dasbor & Verifikasi** (`route('operator.pembayaran.index')`):
   - Berfungsi sebagai ringkasan dashboard KPI operasional (antrean verifikasi, transaksi belum bayar, total terverifikasi lunas, dan jalan pintas kasir).
   - Menampilkan antrean verifikasi transfer manual dengan tab filter cepat.
2. **Kasir Loket (Walk-in)** (`route('operator.loket.index')`):
   - Layanan pemesanan tiket langsung di konter loket stasiun/terminal.
   - Pilihan trayek armada aktif, jam berangkat, tarif, dan denah tata letak kursi dinamis.
3. **Riwayat Transaksi** (`route('operator.loket.index', ['tab' => 'riwayat']) . '#riwayat-transaksi'`):
   - Akses langsung menuju tabel pencarian dan riwayat seluruh transaksi loket yang berstatus lunas.

### B. Mekanisme Akses & Pencarian Riwayat Pesanan
- **Penghapusan Pembatasan 8 Transaksi:** Controller `PesananLoketController@index` diperbarui dari batasan statis `take(8)` menjadi pencarian dinamis berbasis Eloquent dengan pagination `->paginate(10)->withQueryString()`.
- **Pencarian Komprehensif:** Ditambahkan formulir pencarian taktil yang mendukung query pencarian berdasarkan:
  - Kode pesanan (misal: `GTX-XXXXXX`).
  - Nama penumpang walk-in maupun member terdaftar.
  - Nomor telepon / WhatsApp penumpang.
  - Nama kota asal atau kota tujuan.
- **Pembedaan Identitas:** Baris tabel dan kartu seluler membedakan secara tegas identitas *Member Terdaftar* (lencana biru) dan *Walk-in Umum* (lencana ungu).
- **Akses Detail Transaksi:** Setiap transaksi memiliki tombol **"Detail"** yang mengarahkan ke `route('operator.pembayaran.show', $rp->id)` untuk memeriksa rincian lengkap pesanan, jadwal, kursi, waktu bayar, dan bukti kasir.

### C. Mekanisme Akses Jadwal & Ketersediaan Kursi
- Akses jadwal keberangkatan dan ketersediaan kursi secara operasional telah terpenuhi langsung pada modul **Kasir Loket** (`operator.loket.index`). Operator dapat melihat daftar jadwal masa mendatang, jam keberangkatan, tipe armada & plat nomor, tarif resmi, serta denah kursi interaktif (kursi kosong vs terisi).
- Ditambahkan pula tautan pintasan taktil menuju katalog pencarian jadwal publik (`route('jadwal.cari')` target `_blank`) pada kartu jadwal kasir untuk referensi lintas pencarian.

### D. File yang Dimodifikasi pada P4.1
1. `app/Http/Controllers/Operator/PesananLoketController.php`: Dukungan parameter `Request $request`, filter query pencarian transaksi `cari`, dan pagination 10 item per halaman.
2. `resources/views/layouts/operator.blade.php`: Pembaruan navbar desktop dan drawer mobile (Dasbor & Verifikasi, Kasir Loket, Riwayat Transaksi).
3. `resources/views/operator/_nav.blade.php`: Penyesuaian sub-tabs taktil untuk 3 modul operasional.
4. `resources/views/operator/loket/index.blade.php`: Penambahan form pencarian transaksi, tautan katalog publik, pagination taktil, serta tombol detail transaksi.
5. `resources/views/operator/pembayaran/show.blade.php`: Dukungan tampilan ramah transaksi tunai loket dan tombol navigasi kembali ke Antrean Verifikasi maupun Riwayat Loket.
6. `tests/Feature/AuthTest.php`: Penambahan pengujian akses kasir loket dan pencarian riwayat transaksi.

### E. Hasil Pengujian P4.1
- **Vite Build:** `npm run build` sukses mengompilasi 62 modul dalam **1.89s** (`public/build/assets/app-Bee1D-Wl.css` dan `app-DDpvETUX.js`).
- **Pint Style Check:** Lolos 100% pada seluruh file controller dan test terkait.
- **Targeted Test Suite:** 20 pengujian `AuthTest` dan 15 pengujian operator lolos **100%** tanpa kegagalan.

---

## 9. Penyempurnaan Halaman Mandiri Riwayat Transaksi (Pemisahan Menu Kasir & Riwayat)

Sesuai instruksi tindak lanjut pemilik proyek (*"untuk menu riwayat transaksi buatkan halaman baru saja jangan gabung sama menu kasir loket"*), dilakukan restrukturisasi halaman agar modul kasir loket dan riwayat transaksi terpisah secara mandiri dan bersih:

### A. Arsitektur Route & Controller Baru
1. **Route Baru Dedicated:**
   - Didaftarkan route GET `/operator/riwayat` dengan nama `operator.riwayat.index` di dalam grup middleware `['auth', 'peran:Operator']` pada `routes/web.php`.
2. **Controller Method Dedicated:**
   - Menambahkan method `riwayat(Request $request)` pada `app/Http/Controllers/Operator/PesananLoketController.php`.
   - Mengambil data pesanan berstatus `Lunas` dengan eager loading `jadwal.kotaAsal`, `jadwal.kotaTujuan`, `jadwal.kendaraan`, `pengguna`, `operator`, dan `pembayaran`.
   - Dilengkapi filter pencarian teks `cari` (mencari kode pesanan, nama penumpang, no WA/telepon, rute kota asal dan tujuan).
   - Dilengkapi filter tab `tipe` (*Semua*, *Walk-in*, *Member*).
   - Menghitung metrik ringkasan KPI: `$totalLunas`, `$totalWalkin`, dan `$totalMember`.
   - Pagination 12 item per halaman dengan `withQueryString()`.
3. **Penyederhanaan Method Kasir:**
   - Method `index()` pada `PesananLoketController` dikembalikan fokus secara murni untuk menyajikan halaman kasir loket pos (`$jadwalAktif` dan `$daftarMember`) tanpa query riwayat yang membebani alur kerja reservasi.

### B. Halaman Baru: `resources/views/operator/riwayat/index.blade.php`
- Menghadirkan antarmuka taktil *Retro-Sketchbook* mandiri yang lengkap:
  1. **4 Kartu Metrik KPI:** Total Transaksi Lunas, Total Walk-in (Non-Member), Total Member Terdaftar, dan Banner pintasan ke kasir loket.
  2. **Bar Pencarian & Filter Tabs:** Input pencarian responsif dan tab kategori filter (*Semua Transaksi*, *Khusus Walk-in Loket*, *Khusus Member Terdaftar*).
  3. **Tabel Data Taktil (Desktop):** Kolom Kode Booking, Waktu & Loket, Penumpang (badge pembeda Walk-in vs Member), Trayek & Armada, Kursi & Total Bayar, serta tombol aksi **Detail Transaksi** (`route('operator.pembayaran.show', $rp->id)`).
  4. **Kartu Taktil (Mobile):** Tampilan adaptif dengan border tinta, shadow taktil, dan badge status lunas.
  5. **Empty State Interaktif:** Menampilkan ilustrasi pencarian kosong ketika kata kunci tidak ditemukan dengan tombol reset filter.
  6. **Pagination Taktil:** Menampilkan navigasi halaman yang mempertahankan parameter filter pencarian.

### C. Refactor Halaman Kasir Loket: `resources/views/operator/loket/index.blade.php`
- Menghapus tabel riwayat transaksi yang sebelumnya digabung di bawah form kasir.
- Menjadikan halaman kasir 100% fokus pada *Split-View Point of Sale (POS)*:
  - Sisi Kiri: Pilihan jadwal aktif & denah kursi interaktif real-time.
  - Sisi Kanan: Formulir data penumpang walk-in / member, kalkulasi tagihan tunai otomatis, dan tombol penerbitan tiket langsung lunas.
- Menggantikan tabel lama dengan banner tautan taktil elegan di bagian bawah yang mengarahkan ke halaman riwayat mandiri (`route('operator.riwayat.index')`).

### D. Penyelarasan Navigasi di Seluruh Level
1. **Layout Operator (`resources/views/layouts/operator.blade.php`):**
   - Desktop & Mobile Drawer kini secara seragam memiliki 3 item navigasi utama dengan *active state* presisi:
     - `operator.pembayaran.*` &rarr; **Dasbor & Verifikasi**
     - `operator.loket.*` &rarr; **Kasir Loket (Walk-in)**
     - `operator.riwayat.*` &rarr; **Riwayat Transaksi**
2. **Sub-Tabs Taktil (`resources/views/operator/_nav.blade.php`):**
   - 3 sub-tab navigasi mandiri dengan deteksi rute aktif `request()->routeIs(...)`.
3. **Mini Footer (`resources/views/components/footer-mini.blade.php`):**
   - Tautan navigasi mini footer untuk peran `Operator` memuat 3 modul: *Dasbor & Verifikasi*, *Kasir Loket*, dan *Riwayat Transaksi*.

### E. Verifikasi Kualitas & Hasil Uji
- **PHP Pint (PSR-12 Style):** Lolos 100% (`vendor/bin/pint --test`).
- **Vite Asset Compilation:** `npm run build` sukses mengompilasi 62 modul dalam **1.98 detik** (`public/build/assets/app-CScK3i0G.css` dan `app-DDpvETUX.js`).
- **Feature Tests (`tests/Feature/AuthTest.php`):** 21 pengujian lolos 100%, termasuk skenario pengujian akses halaman kasir loket, pengujian akses halaman riwayat transaksi loket (HTTP 200), dan pengujian pencarian riwayat berbasis parameter kata kunci.
- **Full Automated Regression Suite (Pest PHP):**
  ```text
  Tests:    150 passed (528 assertions)
  Duration: 19.35s
  ```
  Seluruh 150 pengujian unit dan fitur pada database `gotix_testing` lulus **100%** tanpa satupun error maupun regresi.

