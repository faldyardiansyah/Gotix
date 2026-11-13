# 04 — Frontend Architecture Audit

## 1. Inventaris Frontend (`resources/`)

- **VERIFIED:** Seluruh aset frontend terdiri dari:
  - **CSS (`resources/css/app.css`):** 17 baris berisi directive Tailwind v3 (`@tailwind base/components/utilities`), `[x-cloak]`, font `DM Sans` pada `body`, dan `Bricolage Grotesque` pada heading (`h1`–`h4`).
  - **JavaScript (`resources/js/app.js`, `resources/js/bootstrap.js`):** Mengimpor `axios` (belum dipakai di UI) dan menjalankan `Alpine.start()`. Tidak terdapat modul JS khusus fitur (seperti `pemilih-kursi.js`).
  - **Blade Views (`resources/views/`):** Total **41 file Blade**:
    - **Layouts (3):** `layouts/tamu.blade.php` (aktif untuk publik, auth, member, operator), `layouts/admin.blade.php` (aktif untuk 16 halaman admin), `layouts/operator.blade.php` (tidak terpakai).
    - **Nav Partials (3):** `member/_nav.blade.php` (dipakai 3x), `operator/_nav.blade.php` (dipakai 2x), `admin/_nav.blade.php` (tidak terpakai).
    - **Publik & Auth (4):** `publik/beranda.blade.php`, `publik/cari-jadwal.blade.php`, `auth/masuk.blade.php`, `auth/daftar.blade.php`.
    - **Member (9):** `pesanan/{index,create,show}`, `pembayaran/{show,konfirmasi}`, `tiket/{index,show}`, `keluhan/{index,create}`.
    - **Operator (4):** `loket/{index,create}`, `pembayaran/{index,show}`.
    - **Admin (17):** `dasbor`, `kota/{index,create,edit}`, `kendaraan/{index,create,edit}`, `jadwal/{index,create,edit,show}`, `pesanan/{index,show}`, `keluhan/{index,show}`, `pengguna/{index,show}`.
    - **Unused (1):** `welcome.blade.php`.
  - **Reusable Blade Components (`resources/views/components/`):** **NOT IMPLEMENTED**.

---

## 2. Pola Desain, Inkonsistensi Layout & Penggunaan Tailwind

### 2.1 Arsitektur Layout Tidak Seragam
- **VERIFIED:**
  - **Admin** memakai `layouts.admin` dengan sidebar tetap kiri (`bg-slate-900 text-slate-100`) dan konten terang (`bg-slate-50`).
  - **Operator** memiliki `layouts/operator.blade.php` sendiri di repo, tetapi seluruh view operator aktif (`loket/index`, `pembayaran/index`, `pembayaran/show`) justru me-extend `layouts.tamu` (navbar publik atas + footer publik) dan hanya halaman `index` yang me-`@include('operator._nav')`. Halaman `operator/pembayaran/show.blade.php` tidak menampilkan navigasi operator sama sekali.
  - **Member** juga me-extend `layouts.tamu` sehingga menu navigasi publik ikut tampil, lalu sebagian halaman (`pesanan/index`, `tiket/index`, `keluhan/index`) menyisipkan `@include('member._nav')`, sedangkan halaman detail/form (`pesanan/create`, `pesanan/show`, `pembayaran/show`, `tiket/show`, `keluhan/create`) tidak menyertakannya.

### 2.2 Konflik Kelas Tailwind v3 vs Tailwind v4
- **VERIFIED:** Pipeline Vite (`vite.config.js` + `postcss.config.js`) mengompilasi CSS menggunakan **Tailwind CSS v3.4.19** (`tailwind.config.js`), namun hasil scanning seluruh file Blade (di luar `welcome.blade.php`) menemukan **49 pemakaian kelas utilitas Tailwind v4**:
  - `shadow-xs` (45 kemunculan di `admin/*`, `member/*`, `operator/*`, `publik/*`, `layouts/admin`)
  - `w-4.5` (2 kemunculan), `h-4.5` (1 kemunculan)
  - `shadow-2xs` (1 kemunculan di `publik/beranda.blade.php` L21)
- **Impact:** Kelas-kelas tersebut tidak dikenali Tailwind v3 dan menghasilkan elemen tanpa bayangan atau ukuran ikon `0`/default di produksi.

### 2.3 Elemen UI Duplikat & Tidak Reusable
- **VERIFIED:**
  - **Badge Status Pesanan & Keluhan:** Pewarnaan badge (`Belum Bayar`, `Dalam Proses`, `Lunas`, `Dibatalkan`, serta status keluhan) ditulis ulang dengan blok `@if/@switch/@match` berbeda-beda di minimal 8 view (`admin/dasbor`, `admin/pesanan/*`, `admin/keluhan/*`, `member/pesanan/*`, `member/tiket/index`, `operator/pembayaran/*`), bahkan sebagian masih menggunakan warna berbeda untuk status yang sama.
  - **Form CRUD Admin Create/Edit:** Form untuk `kota`, `kendaraan`, dan `jadwal` masing-masing diduplikasi penuh antara `create.blade.php` dan `edit.blade.php` tanpa partial `_form.blade.php` atau `<x-input>` component.
  - **Card Jadwal & Tombol Aksi:** Duplikasi markup di `publik/beranda.blade.php` dan `publik/cari-jadwal.blade.php`.

### 2.4 Ketergantungan AOS & Splash Screen Eksternal
- **VERIFIED (`resources/views/layouts/tamu.blade.php` L16, L28-40, L284-292):**
  - Halaman publik/member/operator memuat library animasi AOS dari CDN `unpkg.com` dan menampilkan `#gotix-splash` overlay penuh selama minimal 850ms (`setTimeout(..., 850)` pada event `load`).
  - Lebih dari 30 elemen menggunakan `data-aos="..."`. Jika CDN `unpkg.com` lambat/gagal atau JS terblokir, elemen berpotensi tidak tampil (*opacity: 0*). Splash 850ms pada setiap perpindahan halaman member/operator sangat menghambat UX operasional.

---

## 3. Audit View Mocked, Rusak, & Placeholder (Peran per Peran)

### Issue 4.1 — Halaman Publik (`beranda.blade.php` & `cari-jadwal.blade.php`) Bermasalah Konten, Encoding, & CTA Role
- **VERIFIED**
- **File path:** `resources/views/publik/beranda.blade.php` (L1-460+), `resources/views/publik/cari-jadwal.blade.php` (L180-195, L278-283), `resources/views/layouts/tamu.blade.php` (L263-265)
- **Relevant class/method/component:** Halaman publik & footer tamu
- **Description:**
  1. **Footer Config Key Salah (`layouts/tamu.blade.php` L263-265):** Memanggil `config('gotix.bank_name')`, `config('gotix.bank_account')`, `config('gotix.bank_holder')` padahal struktur di `config/gotix.php` adalah `gotix.bank.{nama,rekening,pemilik}`.
  2. **Konten Domain Campur Aduk & Karakter Encoding Rusak (`publik/beranda.blade.php`):** Mengandung karakter pengganti UTF-8 rusak (``) pada pemisah teks, mencampur rute Jawa (Jakarta–Bandung, Jogja–Surabaya) dan testimoni Jakarta/Bandung dengan seeder Lampung.
  3. **CTA Pesan Mengarah ke Route Member untuk Admin/Operator (`cari-jadwal.blade.php` L180-190 & L278-283):** Menggunakan `@auth` generik untuk menampilkan tombol ke `route('member.pesanan.create')` sehingga Admin/Operator yang mengklik tombol tersebut terkena `403 Forbidden`. Pada grid rekomendasi, tidak ada pengecekan `$sisaKursi > 0` sebelum tombol Pesan aktif.
- **Impact:** Pengalaman pengguna terganggu (teks rusak, 403 error untuk staf, info rekening footer hilang).
- **Recommended solution:** Perbaiki key `config('gotix.bank.*')`, bersihkan encoding UTF-8 & copywriting Lampung, serta sesuaikan CTA `@auth` hanya untuk role `Member` (atau tampilkan label sesuai role).
- **Priority:** High

### Issue 4.2 — E-Tiket Member (`member/tiket/show.blade.php` & `index.blade.php`) 100% Mocked / Hardcoded
- **VERIFIED**
- **File path:** `resources/views/member/tiket/show.blade.php` L26-164, `resources/views/member/tiket/index.blade.php` L28-117
- **Relevant class/method/component:** Tampilan daftar tiket & detail e-tiket member
- **Description:** `member/tiket/show.blade.php` sama sekali tidak merender objek `$pesanan`. Seluruh informasi rute (`Jakarta (Gambir) -> Bandung (Dipatiukur)`), armada (`HiAce Premio • BE 1212 AT`), kursi (`02`), harga (`Rp 120.000`), status (`TERVERIFIKASI & LUNAS`), dan SVG QR code semuanya hardcoded statis. Sementara `tiket/index.blade.php` me-render semua pesanan (termasuk yang belum lunas) dan menghubungkannya ke halaman e-tiket palsu tersebut.
- **Impact:** Member tidak pernah menerima e-tiket asli dari pesanannya dan pesanan yang belum lunas tampil seolah bisa dicetak menjadi tiket sah.
- **Recommended solution:** Binding penuh data `$pesanan` (dengan guard `status === Lunas`), hasilkan kode/QR verifikasi unik dari `kode_pesanan`, dan sembunyikan aksi cetak e-tiket untuk pesanan non-Lunas.
- **Priority:** Critical

### Issue 4.3 — Halaman Keluhan Member (`member/keluhan/index.blade.php` & `create.blade.php`) 100% Mocked & Tidak Tersambung ke Backend
- **VERIFIED**
- **File path:** `resources/views/member/keluhan/index.blade.php` L28-57, `resources/views/member/keluhan/create.blade.php` L39-100
- **Relevant class/method/component:** Modul Keluhan Member
- **Description:**
  1. `index.blade.php` L28-57 menimpa variabel `$daftarKeluhan` dari controller dengan array hardcoded dummy (`GTX-JKTBDG`, `GTX-321550`). Data keluhan asli di database tidak pernah tampil.
  2. `create.blade.php` L39 menulis `<form action="#" method="POST" onsubmit="event.preventDefault(); alert(...);">`, sehingga form **tidak pernah mengirim POST** ke `route('member.keluhan.store')`. Opsi kategori juga menyertakan `'Armada'` (tidak valid di enum `KategoriKeluhan`) dan opsi pesanan hardcoded.
- **Impact:** Fitur keluhan member lumpuh total di frontend meskipun tabel dan controller admin sudah disiapkan.
- **Recommended solution:** Hapus array dummy di `index.blade.php`, hubungkan `form` di `create.blade.php` ke `route('member.keluhan.store')` dengan `@csrf`, gunakan `KategoriKeluhan::cases()`, dan kirim daftar pesanan real dari `KeluhanController::create()`.
- **Priority:** Critical

### Issue 4.4 — Halaman Loket Operator (`operator/loket/index.blade.php` & `create.blade.php`) Mocked & Tidak Mengirim Transaksi
- **VERIFIED**
- **File path:** `resources/views/operator/loket/index.blade.php` L7-67+, `resources/views/operator/loket/create.blade.php` L1
- **Relevant class/method/component:** Modul Walk-in Loket Operator
- **Description:** `operator/loket/index.blade.php` tidak memakai koleksi `$jadwalAktif` yang dikirim controller, melainkan dropdown rute hardcoded (`jkt-bdg`, `yog-sub`, dll.) dan diagram 5 kursi statis. Tombol submit terikat pada `@submit.prevent="submitLoket()"` di Alpine.js yang hanya membuka modal struk thermal palsu tanpa mengirim `POST` ke `route('operator.loket.store')`. Sementara `operator/loket/create.blade.php` hanya berisi `[PLACEHOLDER] Operator — Pesanan Loket`.
- **Impact:** Pemesanan walk-in oleh Operator Loket tidak pernah tersimpan ke database maupun mengurangi sisa kursi jadwal real.
- **Recommended solution:** Integrasikan data `$jadwalAktif` dan status kursi real ke antarmuka loket, hubungkan form ke backend `operator.loket.store`, dan dukung kapasitas kendaraan dinamis serta data penumpang walk-in.
- **Priority:** Critical

### Issue 4.5 — Bug UI Pembayaran Member & Operator (`member/pembayaran/show.blade.php`, `operator/pembayaran/*`)
- **VERIFIED**
- **File path:**
  - `resources/views/member/pembayaran/show.blade.php` L112-114
  - `resources/views/member/pembayaran/konfirmasi.blade.php` L1
  - `resources/views/operator/pembayaran/index.blade.php` L90
  - `resources/views/operator/pembayaran/show.blade.php` L151-176
  - `resources/views/admin/pesanan/show.blade.php` (aksi verifikasi)
- **Relevant class/method/component:** Tampilan Pembayaran & Verifikasi
- **Description:**
  1. `operator/pembayaran/index.blade.php` L90 membaca `$pesanan->konfirmasiPembayaran->nama_pengirim` yang **tidak ada di tabel/model** (nama kolom aktual adalah `rekening_pengirim`), sehingga selalu tampil fallback/kosong.
  2. `member/pembayaran/show.blade.php` L112-114 melakukan hardcode rekening BCA `8820 1928 33` alih-alih `config('gotix.bank.*')`.
  3. `member/pembayaran/konfirmasi.blade.php` hanya berisi teks `[PLACEHOLDER] Member — Konfirmasi Pembayaran`.
  4. `operator/pembayaran/show.blade.php` L151-176 (serta `admin/pesanan/show.blade.php`) tetap merender tombol Setujui & Tolak walaupun status pesanan bukan `Dalam Proses` (misal sudah `Lunas`, `Dibatalkan`, atau belum ada bukti bayar).
- **Impact:** Informasi pengirim di tabel verifikasi tidak tampil, tombol verifikasi dapat dipicu pada state yang salah, dan rekening tujuan tidak terpusat.
- **Recommended solution:** Perbaiki nama atribut ke `rekening_pengirim`, gunakan `config('gotix.bank.*')`, batasi tombol verifikasi hanya ketika status `DalamProses`, dan putuskan nasib route/view placeholder konfirmasi.
- **Priority:** High

### Issue 4.6 — Halaman Placeholder Admin & Inkonsistensi Aksesibilitas / Responsivitas
- **VERIFIED:**
  1. `resources/views/admin/jadwal/show.blade.php` hanya berisi `[PLACEHOLDER] Admin — Detail Jadwal`. *(Priority: High)*
  2. **Aksesibilitas (WCAG):** Banyak `<label>` pada form `admin/*/create`, `admin/*/edit`, `member/pesanan/create`, dan `member/keluhan/create` belum memiliki atribut `for` yang berpasangan dengan `id` pada input, serta beberapa tombol ikon tidak memiliki `aria-label`. Dialog konfirmasi hapus di admin masih menggunakan browser native `confirm()` / `alert()`. *(Priority: Medium)*
  3. **Responsivitas:** Diagram kursi pada `member/pesanan/create.blade.php` menggunakan `grid-cols-5` tetap (bisa berdesakan di layar mobile sempit atau tidak realistis untuk armada >5 kursi seperti Elf 12 kursi / HiAce 8-14 kursi). Tabel admin & operator sudah dibungkus `overflow-x-auto`, namun beberapa header dan kartu dasbor perlu konsistensi breakpoint untuk desain ulang 3 peran. *(Priority: Medium)*

