# GOTIX — DOKUMENTASI IMPLEMENTASI PHASE P3: DESIGN SYSTEM & MEMBER REDESIGN

> **Status:** FINALIZED  
> **Tanggal:** 13 November 2026  
> **Basis Acuan:** `docs/27-gotix-design-system-proposal.md`, `AGENTS.md`, `.agent/rules/gotix.md`

---

## 1. RINGKASAN EKSEKUTIF

Phase **P3 (Implementasi Design System & Redesign Member)** telah selesai dilaksanakan secara menyeluruh pada repositori GOTIX. Implementasi ini mewujudkan arah visual *Retro-Sketchbook Tactile* yang telah disetujui, mencakup:

1. **Konfigurasi Token Desain:** Penambahan palet warna Gotix (Amber `#F59E0B`, Midnight Ink `#18181B`, Warm Paper `#FAFAF9`, Gotix Coral `#E11D48`), bayangan retro offset bertinta (`shadow-retro`), dan tipografi (`Bricolage Grotesque`, `DM Sans`, `serif`).
2. **Komponen Blade Reusable:** Pembuatan 7 komponen terpusat di `resources/views/components/` (`button`, `input`, `card`, `badge-status`, `tiket-pass`, `footer-crew`, `footer-mini`).
3. **Redesign Seluruh Portal Member (`/akun/*`):** Transformasi lengkap navigasi, daftar tiket, e-tiket boarding pass, riwayat pesanan, detail transaksi & bukti bayar, pemilihan kursi interaktif, halaman pembayaran countdown & unggah bukti, konfirmasi status bayar, serta pengajuan & daftar keluhan (1.000 karakter & kategori Armada).
4. **Integrasi Halaman Publik & Layout:** Pembagian footer adaptif pada `layouts/tamu.blade.php` (`<x-footer-crew>` pada publik, `<x-footer-mini>` pada member), redesign pencarian jadwal (`cari-jadwal.blade.php`), dan redesign beranda publik (`beranda.blade.php`) dengan penghapusan referensi kedaerahan lama dan penerapan hub awal Indramayu.
5. **Kepatuhan Kualitas:** Berhasil dikompilasi oleh Vite tanpa error dan **100% lulus seluruh pengujian automated (147 test, 522 assertions)** pada Pest PHP.

---

## 2. FONDASI DESIGN SYSTEM & TOKEN TACTILE

### A. Konfigurasi Tailwind (`tailwind.config.js`)
Token desain diperluas tanpa merusak utilitas standar Tailwind v3:
- **Colors (`colors.gotix`):**
  - `amber`: `#F59E0B` (warna aksen utama hangat)
  - `ink`: `#18181B` (tinta gelap pekat untuk outline & teks)
  - `paper`: `#FAFAF9` (kertas hangat bertekstur sebagai latar belakang)
  - `coral`: `#E11D48` (aksen stempel/badge peringatan)
  - `white`: `#FFFFFF`
- **Box Shadows (`boxShadow`):**
  - `retro-xs`: `1.5px 1.5px 0px #18181b`
  - `retro-sm`: `2px 2px 0px #18181b`
  - `retro`: `3.5px 3.5px 0px #18181b`
  - `retro-lg`: `5px 5px 0px #18181b`
  - `retro-amber`: `4px 4px 0px #f59e0b`
  - `retro-coral`: `4px 4px 0px #e11d48`
- **Typography (`fontFamily`):**
  - `heading`: `['Bricolage Grotesque', 'sans-serif']`
  - `sans`: `['DM Sans', 'sans-serif']`
  - `serif`: `['Playfair Display', 'serif']`

### B. Styling Khusus (`resources/css/app.css`)
- Utilitas border tebal bertinta: `.border-ink` (`2px solid #18181B`), `.border-ink-2`.
- Pola arsir retro: `.bg-hatch-pattern` untuk menandai kursi yang telah terisi/dipesan penumpang lain.
- Scoped CSS & animasi doodle footer: `.dcf-crew-avatar`, hover bobbing, speech bubble toggle.
- Aturan cetak (`@media print`): Mode cetak presisi untuk boarding pass tanpa elemen navigasi browser (`.no-print` / `print:hidden`).

---

## 3. KATALOG REUSABLE BLADE COMPONENTS

Seluruh komponen reusable ditempatkan di direktori `resources/views/components/`:

| Komponen | Path File | Deskripsi & Fungsionalitas |
|---|---|---|
| `<x-button>` | `resources/views/components/button.blade.php` | Tombol interaktif taktil dengan 5 varian (`primary`, `secondary`, `danger`, `dark`, `ghost`), 3 ukuran (`sm`, `md`, `lg`), dan efek fisik tekan `active:translate-x-0.5 active:translate-y-0.5`. |
| `<x-input>` | `resources/views/components/input.blade.php` | Input form dengan border 2px ink, bayangan offset, label tebal, pesan error terintegrasi, dan slot ikon awalan/akhiran. |
| `<x-card>` | `resources/views/components/card.blade.php` | Kontainer kartu fisik berarsir halus, border bertinta 2px, shadow retro 4px, header/footer slot terstruktur. |
| `<x-badge-status>` | `resources/views/components/badge-status.blade.php` | Lencana status pesanan terpadu dengan border tegas dan indikator titik (`Belum Bayar`, `Dalam Proses`, `Lunas`, `Dibatalkan`). |
| `<x-tiket-pass>` | `resources/views/components/tiket-pass.blade.php` | Tiket boarding pass fisik dengan sobekan perforasi, cutouts notch lingkaran, kontainer SVG QR Code standar BaconQrCode ISO/IEC 18004, stempel resmi miring `TIKET SAH / LUNAS`, serta informasi snapshot harga dan verifikasi. |
| `<x-footer-crew>` | `resources/views/components/footer-crew.blade.php` | Doodle Crew Footer interaktif dengan tipografi stempel besar GOTIX, 5 karakter kru travel interaktif (click to speak bubble), tombol back to top taktil, dan dukungan preferensi reduced-motion. |
| `<x-footer-mini>` | `resources/views/components/footer-mini.blade.php` | Footer ringkas, bersih, dan profesional khusus portal terotentikasi (Member) dengan tautan navigasi akun dan hak cipta. |

---

## 4. REDESIGN PORTAL MEMBER (`/akun/*`)

Seluruh tampilan pada role Member telah diperbarui:

1. **Member Navigation (`resources/views/member/_nav.blade.php`):**
   - Menampilkan salam personal pengguna dengan badge role taktil.
   - Tab navigasi (`Tiket Saya`, `Riwayat Pesanan`, `Pusat Bantuan`) dengan status aktif warna Gotix Amber dan border bertinta.
   - Akses cepat tombol *Cari Tiket Baru*.

2. **Tiket Saya (`resources/views/member/tiket/index.blade.php` & `show.blade.php`):**
   - **Index:** Kartu mini boarding pass berarsir perforasi dengan nomor kursi besar, informasi rute jelas, jam berangkat, dan tombol langsung cetak atau lihat detail. Dilengkapi empty state ramah saat belum ada tiket.
   - **Show:** Menggunakan komponen fisik `<x-tiket-pass>`, tombol cetak boarding pass yang kompatibel dengan print CSS, dan navigasi kembali yang rapi. Memenuhi verbatim verifikasi test status tiket lunas terverifikasi.

3. **Riwayat Pesanan (`resources/views/member/pesanan/index.blade.php` & `show.blade.php`):**
   - **Index:** Tabel taktil pada layar desktop dan kartu ringkas pada layar mobile, menampilkan kode pesanan, tanggal transaksi, rute, nomor kursi, badge status retro (`<x-badge-status>`), serta tombol aksi dinamis sesuai status transaksi.
   - **Show:** Kartu detail transaksi lengkap, ringkasan jadwal, rute, tarif, tenggat pembayaran dengan countdown, serta riwayat penolakan/persetujuan bukti pembayaran private storage.

4. **Pemesanan Kursi Interaktif (`resources/views/member/pesanan/create.blade.php`):**
   - Denah kursi dinamis sesuai kapasitas kendaraan (1 sampai `$kapasitas`).
   - Status visual kursi: *Tersedia* (kartu putih border 2px ink hover amber), *Dipilih* (kuning amber solid dengan tanda centang), dan *Terisi/Dipesan* (arsir garis hatch abu-abu dengan label Terisi dan disabled).
   - Validasi frontend Alpine.js sinkron dengan backend, mencegah submit tanpa memilih nomor kursi.

5. **Pembayaran & Unggah Bukti (`resources/views/member/pembayaran/show.blade.php` & `konfirmasi.blade.php`):**
   - **Show:** Informasi rekening bank transfer manual (`config/gotix.php`), kotak countdown sisa waktu pembayaran, instruksi transfer langkah demi langkah, form unggah bukti transfer dengan preview gambar langsung (file drag/click), serta indikator riwayat catatan penolakan jika pernah ditolak sebelumnya.
   - **Konfirmasi:** Menggantikan teks placeholder lama dengan kartu status pembayaran komprehensif, menampilkan indikator status verifikasi, opsi unggah ulang bukti jika ditolak, tombol pantau pesanan, atau tombol unduh tiket jika lunas.

6. **Keluhan & Bantuan (`resources/views/member/keluhan/index.blade.php` & `create.blade.php`):**
   - **Index:** Daftar keluhan dengan lencana kategori bertinta, indikator status verifikasi (`Menunggu Respon` vs `Sudah Dibalas`), kartu respon resmi admin berlatar amber lembut, serta empty state informatif.
   - **Create:** Form pengajuan keluhan lengkap dengan dropdown kategori resmi (termasuk kategori baru `Armada`), opsi pemilihan pesanan terkait, textarea deskripsi taktil dengan live character counter hingga 1.000 karakter, dan validasi minimal 10 karakter.

---

## 5. INTEGRASI HALAMAN PUBLIK & LAYOUT

1. **Master Layout Tamu (`resources/views/layouts/tamu.blade.php`):**
   - Navigasi atas dengan logo Gotix Amber retro dan tombol autentikasi taktil.
   - Integrasi footer cerdas dan adaptif:
     - Jika rute adalah Member (`member.*`): merender `<x-footer-mini />` yang bersih dan tidak mengganggu alur kerja pengguna.
     - Jika rute adalah publik (`beranda`, `jadwal.cari`): merender `<x-footer-crew />` interaktif dengan karakter doodle travel.

2. **Pencarian Jadwal (`resources/views/publik/cari-jadwal.blade.php`):**
   - Kotak pencarian taktil dengan field kota asal, kota tujuan, dan tanggal keberangkatan.
   - Kartu hasil pencarian jadwal dengan indikator sisa kursi real-time, tarif per kursi, dan tombol aksi pemesanan dinamis sesuai status autentikasi.

3. **Beranda Publik (`resources/views/publik/beranda.blade.php`):**
   - Hero section retro tactile dua kolom dengan headline Bricolage Grotesque berarsir dan tombol CTA berbayang hard shadow.
   - Pembersihan seluruh referensi identitas daerah lama (Lampung) menjadi transportasi eksekutif antar kota dengan Indramayu sebagai operasional awal yang valid.
   - Kartu 4 pilar kenyamanan (Maksimal 5 Penumpang, Anti Double-Booking Lock, Verifikasi Cepat & Terpercaya, Armada Prima).
   - Box pencarian jadwal cepat dan daftar rute populer dari database.

---

## 6. HASIL VERIFIKASI KUALITAS & PENGUJIAN

### A. Asset Compilation (Vite)
Perintah `npm run build` berhasil dikompilasi tanpa ada kegagalan sintaks:
```text
vite v7.3.7 building client environment for production...
transforming...
✓ 62 modules transformed.
rendering chunks...
computing gzip size...
public/build/manifest.json              0.33 kB │ gzip:  0.17 kB
public/build/assets/app-ZR23rIQW.css   82.62 kB │ gzip: 14.36 kB
public/build/assets/app-DDpvETUX.js   114.55 kB │ gzip: 41.40 kB
✓ built in 2.22s
```

### B. Automated Test Suite (Pest PHP)
Seluruh rangkaian pengujian (Feature & Unit) dijalankan pada environment terisolasi `gotix_testing`:
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
  Duration: 29.85s
```

---

## 7. BATASAN & JAMINAN ARSITEKTUR

1. **Database & Migrasi:** Tidak ada perubahan pada struktur tabel, migrasi database, atau data transaksi database `gotix`. Database utama tetap utuh.
2. **Logika Bisnis & Backend:** Tidak ada perubahan pada service transaksi, locking concurrency (`pessimistic lock`), algoritma HMAC-SHA256 QR Code, atau rate limiter autentikasi.
3. **Ruang Lingkup Role:** Halaman Operator Loket (`/operator/*`) dan Admin (`/admin/*`) tetap dipertahankan pada kondisi aslinya dan tidak diubah pada fase ini (dialokasikan secara terpisah untuk Phase P4 dan Phase P5).
4. **Git Repository:** Tidak dilakukan git commit atau git push, mematuhi aturan governance pengembangan GOTIX.
