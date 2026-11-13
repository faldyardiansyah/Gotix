# GOTIX — DOKUMENTASI IMPLEMENTASI PHASE P3.2: PAYMENT PAGE VISUAL REFINEMENT & MEMBER NAVBAR

> **Status:** FINALIZED  
> **Tanggal:** 13 November 2026  
> **Basis Acuan:** Permintaan Khusus P3.2, `AGENTS.md`, `config/gotix.php`, `resources/views/member/pembayaran/show.blade.php`

---

## 1. RINGKASAN EKSEKUTIF

Phase **P3.2** berfokus pada penyempurnaan visual halaman pembayaran Member (`/akun/pembayaran/{kode}`), penataan tata letak dua kolom proporsional (~1.140 px), penataan kartu informasi rekening dan ringkasan tagihan, perbaikan keterbacaan form unggah bukti pembayaran, konsistensi navbar khusus area Member, serta audit status nomor rekening pada konfigurasi sistem.

Seluruh 147 test skenario Pest PHP tetap lulus 100% tanpa perubahan backend logic, skema database, atau migration.

---

## 2. PERUBAHAN NAVBAR MEMBER

Pada [`resources/views/layouts/tamu.blade.php`](file:///d:/SEMESTER%205/Pemeliharaan%20Perangkat%20Lunak/Gotix/resources/views/layouts/tamu.blade.php):
- **Isolasi Konteks Halaman Akun:** Menu publik (*Beranda* dan *Cari Jadwal*) disembunyikan saat pengguna berada dalam rute Member (`member.*`), sehingga tidak terjadi tumpang tindih atau kebingungan dengan navigasi tab internal Member (`member/_nav.blade.php`).
- **Logo Navigasi:** Logo GOTIX pada area Member mengarah langsung ke portal Member ([`route('member.tiket.index')`](file:///d:/SEMESTER%205/Pemeliharaan%20Perangkat%20Lunak/Gotix/resources/views/member/tiket/index.blade.php)) dilengkapi lencana taktil *"Portal Penumpang"*.
- **Identitas Ringkas & Sesi:** Menampilkan lencana identitas Member (avatar inisial dan nama ringkas) yang berdampingan dengan tombol *Keluar* (POST `route('keluar')`).
- **Menu Mobile Bersih:** Dropdown hamburger pada area Member memuat tautan cepat portal (*Tiket Saya*, *Riwayat Pesanan & Pembayaran*, *Pusat Bantuan & Keluhan*, *Cari Jadwal Baru*, dan *Keluar Akun*).

---

## 3. PERBAIKAN TATA LETAK HALAMAN PEMBAYARAN

Pada [`resources/views/member/pembayaran/show.blade.php`](file:///d:/SEMESTER%205/Pemeliharaan%20Perangkat%20Lunak/Gotix/resources/views/member/pembayaran/show.blade.php):
- **Lebar Kontainer Proporsional:** Lebar kontainer utama disesuaikan dari `max-w-4xl` (~896 px) menjadi `max-w-[1140px]`, memberikan proporsi yang kokoh, tidak terasa sempit, dan bebas ruang kosong berlebih.
- **Tata Letak Dua Kolom Seimbang (Desktop):**
  - **Kolom Kiri (`lg:col-span-5`):**
    1. **Card Ringkasan Tagihan & Status:**
       - Menempatkan **Total Nominal Transfer** sebagai *visual spotlight* utama (angka besar font-heading `text-3xl sm:text-4xl` dengan kotak aksen Gotix Amber `#F59E0B`).
       - Dilengkapi tombol *Salin Nominal* interaktif dengan umpan balik status *"Tersalin!"*.
       - Detail jadwal rute keberangkatan, nomor kursi fisik, dan jenis armada.
       - Kotak batas waktu pembayaran (*expiry deadline*) dengan format waktu Indonesia yang baku (`d F Y, H:i WIB`).
    2. **Card Rekening Tujuan Transfer:**
       - Nama bank (`config('gotix.bank.nama')`).
       - Nomor rekening dalam format monospace tebal (`text-2xl sm:text-3xl`).
       - Tombol *Salin Nomor Rekening* ditempatkan secara ergonomis dengan jarak yang jelas, tidak lagi bertabrakan atau menutupi deretan angka rekening.
       - Nama pemilik rekening (`a.n. PT Gotix Transportasi Indonesia`).
       - Catatan petunjuk transfer yang singkat dan jelas.
  - **Kolom Kanan (`lg:col-span-7`):**
    - **Card Formulir Konfirmasi & Unggah Bukti Transfer:**
      - Kotak peringatan riwayat penolakan sebelumnya jika ada (`alasan_penolakan`).
      - Field input `rekening_pengirim` (Nama & Rekening Pengirim).
      - Field input `nomor_resi` (Nomor Referensi / ID Transaksi Opsional).
      - **Grid Tanggal & Waktu Seimbang:** Dua kolom berdampingan dengan label `whitespace-nowrap` sehingga teks *"Tanggal Transfer"* dan *"Waktu Transfer"* tidak terpotong menjadi dua baris.
      - **Area Unggah Berkas Bukti:** Dropzone berbingkai arsir putus-putus bertinta (*dashed 2px ink border*), mendukung drag-drop/klik, informasi batasan file (Maks. 5 MB; JPG, PNG, WEBP, PDF), serta pratinjau gambar instan (*live image preview*).
      - **Tombol Kirim:** Tombol berbayang hard shadow selebar 100% pada mobile (`w-full`), mudah disentuh, dengan animasi loading spinner saat submit.

---

## 4. STATUS NOMOR REKENING PADA KONFIGURASI SISTEM

Berdasarkan pemeriksaan langsung terhadap berkas konfigurasi `config/gotix.php` dan environment `.env`:

1. Nilai nomor rekening yang tampil pada screenshot (`0000000000`) bersumber dari:
   - File `.env`: `GOTIX_BANK_ACCOUNT=0000000000`
   - File `config/gotix.php`: `env('GOTIX_BANK_ACCOUNT', '8291039182')`
2. **Kesimpulan Teknis:** Nilai `0000000000` pada `.env` adalah **placeholder konfigurasi awal**.
3. Sesuai instruksi pemilik proyek, agen **tidak mengarang atau mengganti nomor rekening palsu**. Sistem tetap membaca konfigurasi resmi apa adanya dan siap menerima nomor rekening korporat asli segera setelah pemilik proyek memperbarui nilai `GOTIX_BANK_ACCOUNT` pada file `.env`.
4. Pembayaran transfer manual diuji dan bekerja penuh secara fungsional melalui mekanisme verifikasi operator loket dan penyimpanan bukti private storage.

---

## 5. DAFTAR FILE YANG DIUBAH

| File | Status | Keterangan Perubahan |
|---|---|---|
| `resources/views/layouts/tamu.blade.php` | **Diubah** | Isolasi navigasi Member, penyembunyian menu publik pada rute `member.*`, dan perapian navbar akun. |
| `resources/views/member/pembayaran/show.blade.php` | **Diubah** | Redesign total layout 1.140 px dua kolom, spotlight nominal, nomor rekening & tombol salin rapi, grid tanggal/waktu seimbang, dan live preview bukti transfer. |
| `docs/30-phase-3.2-payment-page-refinement.md` | **Dibuat (Baru)** | Dokumentasi laporan teknis Phase P3.2. |

---

## 6. HASIL PENGUJIAN KUALITAS

### A. Asset Compilation (Vite)
Kompilasi build produksi berjalan mulus:
```text
vite v7.3.7 building client environment for production...
✓ 62 modules transformed.
public/build/assets/app-Do7TMJoj.css   78.03 kB │ gzip: 13.52 kB
public/build/assets/app-DDpvETUX.js   114.55 kB │ gzip: 41.40 kB
✓ built in 2.05s
```

### B. Pint Code Style
Tidak ada pelanggaran sintaks PHP pada file-file yang dimodifikasi.

### C. Targeted & Full Test Suite (Pest PHP)
- **Pengujian Terarah (`PaymentProofSecurityTest`, `PaymentVerificationSecurityTest`, `BusinessConfigurationTest`, `CoreWorkflowAndTicketTest`):**  
  `73 passed (298 assertions)` dalam 9.11s.
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
    Duration: 21.73s
  ```
  Seluruh 147 test skenario berhasil lulus **100%**.

---

## 7. KONFIRMASI INTEGRITAS ARSITEKTUR

1. **Database & Migrasi:** Tidak ada perubahan database atau penambahan migration.
2. **Business Rules & Controller:** Logika `PembayaranController::konfirmasiStore`, batasan file MIME & 5 MB, status pesanan, locking transaksi, dan private storage tetap utuh.
3. **Role Scoping:** Halaman Operator Loket (`/operator/*`) dan Admin (`/admin/*`) tidak disentuh dan siap untuk dikerjakan pada **Phase P4** dan **Phase P5**.
4. **Git Version Control:** Tidak ada git commit atau git push ke remote.
