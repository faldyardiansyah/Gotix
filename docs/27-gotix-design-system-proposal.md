# 27 — GOTIX DESIGN SYSTEM PROPOSAL
## Visual Reference Analysis & Travel-Themed Architectural Adaptation

> **Status Dokumen:** PROPOSAL DESAIN (Tahap P2 — Revisi Arah Wilayah & Karakter)  
> **Tujuan:** Menganalisis referensi visual Picnic Club dan Doodle Crew Footer HTML, mengadaptasi prinsip desainnya ke dalam konteks layanan reservasi travel antar-kota GOTIX dengan wilayah operasional awal di **Indramayu**, serta merancang Design System terpadu untuk tiga role: **Member**, **Operator Loket**, dan **Admin**.  
> **Aturan Utama:** Tidak menyalin identitas merek Picnic Club secara langsung. Tidak menambahkan dependency baru (React/Babel CDN). Mempertahankan stack Blade, Tailwind CSS v3.4.19, dan Alpine.js.

---

## 1. Analisis Screenshot Referensi (Picnic Club)

Screenshot referensi menampilkan halaman penutup (*footer area*) bergaya *sketchbook / indie comic risograph* dengan nuansa hangat, ekspresif, dan ceria.

```
+-----------------------------------------------------------------------------------+
|  (icon) picnic club          MENU          CLUB          SAY HI                   |
|  Small-batch snacks...       Baskets       Membership    Instagram                |
|                              Drinks        Gift a basket hello@picnic.club        |
|                              Sweets        FAQ                                    |
|                                                                                   |
|            [Burung]                         [Anak Topi]                           |
|      [Kucing] \___      __/\__    [Hantu]    /                                    |
|        /\    /    \    /      \    /        /    /\ [Monster]                     |
|       |  |  |  P   |  |   I    |  | C N I  | |  |  |  /                           |
|       |__|   \____/    \______/    \______/  |__|   \/                            |
|    ======================================================== (Garis Tanah/Rumput)  |
|                                                                                   |
|                         Lunch, but make it an adventure.                          |
|                     ( Build a basket )    (  Order now  )                         |
|                                                                                   |
|  (c) 2026 Picnic Club                         Privacy Terms Cookies  [Back to top]|
+-----------------------------------------------------------------------------------+
```

### Karakteristik Visual Utama yang Teridentifikasi:
1. **Atmosfer & Mood**: Sangat ramah, bersahabat (*welcoming*), memiliki jiwa kerajinan tangan (*handcrafted*), dan jauh dari kesan software enterprise yang kaku atau dingin.
2. **Keseimbangan Warna**: Kanvas didominasi warna kuning cerah nan hangat (*sunny golden yellow* `#ffd94a`), dipadukan dengan tinta hitam pekat (*deep charcoal ink* `#141414`) dan aksen merah koral hangat (*warm coral red* `#e8402a`).
3. **Tipografi Berkontras Tinggi**:
   - **Display Wordmark**: Huruf balok raksasa (*ultra-bold grotesk/sans 900*) dengan tekstur stempel tinta kering (*dry-brush stamped edge*) dan garis arsir (*cross-hatching*).
   - **Editorial Tagline**: Kalimat penutup menggunakan font bertakik (*classic serif*) yang anggun dan santai (*"Lunch, but make it an adventure."*), menciptakan dialog visual antara ketegasan huruf balok dengan kehangatan narasi editorial.
   - **Navigasi Mikro**: Teks navigasi atas menggunakan sans-serif geometris huruf kapital kecil (*uppercase tracking*) yang rapi dan teratur.
4. **Interaksi Karakter & Tipografi**:
   - Karakter doodle tidak diletakkan secara terpisah dalam kotak ilustrasi kaku, melainkan **hidup langsung di atas huruf**: bertengger di atas lengkungan, bersandar di tiang huruf, atau melongok dari balik huruf.
   - Garis tanah (*ground line*) organik dengan kerikil dan rumput mengaitkan tipografi raksasa ke lantai fisik visual.
5. **Kontrol Interaktif Bertema Taktil**:
   - Tombol berbentuk kapsul (*pill button / rounded-full*) dengan garis tepi hitam tegas (*2px solid stroke*) dan bayangan keras (*offset hard drop shadow: 3px-5px*).
   - Efek kemiringan ringan (*subtle tilt/rotation*) pada saat di-hover memberikan sensasi stiker fisik atau lencana mainan.

---

## 2. Analisis Struktur dan Perilaku HTML (Doodle Crew Footer)

Kode sumber referensi berupa komponen React mandiri (`DoodleCrewFooter`) yang menyajikan animasi dan interaktivitas tingkat lanjut tanpa bergantung pada aset gambar eksternal.

### A. Arsitektur Grafis Tanpa Dependensi Aset Gambar
- **100% Inline SVG**: Seluruh karakter (Yeti, Kid, Bird, Cat, Ghost, Mushroom), garis tanah, rumput, kerikil, dan doodle mengambang digambar murni menggunakan path SVG. Tidak ada file `.png`, `.jpg`, atau `.webp` yang perlu di-fetch melalui jaringan.
- **SVG Filter Pipeline untuk Tekstur Tinta (Dry-brush Ink)**:
  - Menggunakan kombinasi `<feTurbulence>`, `<feDisplacementMap>`, `<feColorMatrix>`, dan `<feComposite>` untuk mendistorsi tepi huruf secara prosedural sehingga menghasilkan efek tepian kasar seperti stempel tinta kayu atau sablon manual.
  - Memanfaatkan pola `<pattern id="hatch">` bersudut 38 derajat untuk efek bayangan arsir komik (*comic hatching*).
- **Line Boil Animation**: Tiga variasi filter turbulensi (`boil0`, `boil1`, `boil2`) ditukar secara bergantian pada 8 FPS untuk mensimulasikan getaran alami gambar animasi tradisional (*cel animation line boil*).

### B. Storyboard & Timing Masuk Elemen
Saat komponen memasuki *viewport*, animasi dijalankan secara bertahap dan teratur menggunakan variabel CSS `--d` (*staggered delay*):
1. **0ms - 60ms**: Garis tanah mulai tergores dari kiri ke kanan (`dcf-draw`).
2. **160ms - 480ms**: Huruf wordmark jatuh bergantian seperti stempel karet dengan efek hempasan debu tinta (`dcf-stamp` & `dcf-dust`).
3. **500ms - 900ms**: Karakter bermunculan sesuai posisinya: melompat dari bawah (`dcf-rise-up`), mendarat dari atas (`dcf-plop`), atau mengintip dari belakang huruf (`dcf-peek`).
4. **900ms - 1200ms**: Doodle mengambang mulai bertebaran, kalimat tagline muncul kata per kata, dan tombol aksi meletup muncul (`dcf-pop`).

### C. Interaktivitas Pointer & Perilaku Mikro
- **Eye Gaze Tracking**: Pupil mata karakter menghitung sudut vektor menuju koordinat kursor pengguna (`lookAt`), berkedip secara acak, dan saling melirik satu sama lain saat posisi kursor diam (*idle*).
- **Fisika Floater**: Objek doodle melayang perlahan dengan gerakan osilasi sinus, terdorong menjauh saat didekati kursor, dan dapat dipecahkan (*pop animation*) saat diklik.
- **Poking Interaction**: Mengklik salah satu karakter memicu animasi loncat (`dcf-hop`) dan menampilkan balon percakapan komik berisi pesan singkat ramah.

### D. Aksesibilitas dan Performa
- **IntersectionObserver**: Seluruh perhitungan loop render `requestAnimationFrame` otomatis diistirahatkan (*sleep*) ketika footer berada di luar layar.
- **Dukungan `prefers-reduced-motion` Penuh**:
  - Semua animasi dan transisi dihentikan total (`animation: none !important`).
  - Efek line boil dan fisika doodle dimatikan.
  - Seluruh elemen langsung ditampilkan dalam kondisi final (opacity 1, transform normal) tanpa efek pusing bagi pengguna yang memiliki sensitivitas gerak (*vestibular motion disorders*).
- **Semantik ARIA**: Menggunakan `role="group"`, `aria-label`, hidden `h2.dcf-sr`, navigasi tab keyboard lengkap (`onKeyDown` Enter/Space), dan outline fokus yang jelas (`:focus-visible`).

---

## 3. Karakter Visual yang Dipertahankan

Prinsip-prinsip desain berikut sangat berharga dan akan dipertahankan untuk membangun keunikan identitas GOTIX:

| Karakter Referensi | Alasan Mempertahankan untuk GOTIX |
| :--- | :--- |
| **Kontras Tinta Kuat & Garis Tepi 2px** | Menciptakan kejelasan hierarki visual (*high legibility*), kesan percaya diri, dan tampilan taktil retro-modern yang sangat cocok untuk sistem tiket. |
| **Display Typography Ekspresif Bertekstur Stempel** | Menghidupkan nuansa stempel fisik tiket perjalanan (*ink-stamped boarding pass*) dan papan rute terminal bus. |
| **Karakter Doodle Bertengger pada Tipografi** | Memberikan sentuhan ramah manusiawi (*human touch*) pada layanan transportasi yang sering kali dipersepsikan kaku dan mekanis. |
| **Tombol Pill dengan Hard Offset Shadow** | Bentuk rounded kapsul dengan bayangan keras (`3px-5px`) memberikan affordance klik yang sangat jelas dan memuaskan (*tactile click feel*). |
| **Underline Wavy/Squiggle pada Hover Link** | Menggantikan garis bawah browser standar dengan goresan gelombang tangan dinamis yang menyenangkan saat tautan disorot. |
| **Dialog Balon Komik untuk Pesan/Tips** | Format ideal untuk menampilkan bantuan, status pesanan, atau konfirmasi operasional secara tidak membosankan. |
| **Dukungan Aksesibilitas Reduced-Motion** | Standar kepatuhan aksesibilitas modern yang menjamin kenyamanan seluruh pengguna tanpa merusak esensi desain. |

---

## 4. Karakter Visual yang Perlu Diubah

Untuk mencegah salah sasaran identitas merek, beberapa elemen dari referensi Picnic Club **wajib diubah dan dialihkan ke domain transportasi GOTIX**:

```
+---------------------------------------------------------------------------------+
|                       TRANSFORMASI DOMAIN & IDENTITAS                           |
|                                                                                 |
|  REFERENSI PICNIC CLUB                      ADAPTASI GOTIX TRAVEL               |
|  ---------------------------------          ---------------------------------   |
|  - Makanan, roti lapis (sandwich)    ===>   - Armada minibus eksekutif & bus    |
|  - Piknik taman, keranjang belanja   ===>   - Tiket perjalanan, boarding pass   |
|  - Kucing makan tuna, keju hantu     ===>   - Figur pengemudi, koper & tas      |
|  - Sepeda santai akhir pekan         ===>   - Rute jalan, halte, & terminal     |
|                                             (wilayah operasional awal: Indramayu)|
|  - Kanvas 100% kuning pekat          ===>   - Sistem kanvas seimbang (Warm Sand |
|    (menyebabkan lelah pada tabel)             + Amber Aksen + Midnight Ink)     |
+---------------------------------------------------------------------------------+
```

1. **Eliminasi Total Motif Kuliner**: Seluruh motif makanan, sandwich, minuman piknik, dan roti dihilangkan mutlak.
2. **Penggantian Wordmark**: Kata "PICNIC" / "MISCHIEF" digantikan oleh wordmark **"GOTIX"** (5 huruf simetris yang sangat seimbang untuk komposisi bertengger).
3. **Konsep Karakter Awal (Eksploratif & Belum Ditetapkan sebagai Maskot Resmi)**:
   - Figur karakter dalam proposal ini berstatus sebagai **konsep awal yang masih terbuka untuk dievaluasi dan belum dikunci sebagai maskot resmi**.
   - Nama, desain visual detail, dan penempatan posisi karakter pada tipografi GOTIX tidak dikunci sebelum visualnya disetujui oleh pemilik proyek.
   - Contoh gagasan karakter bertema perjalanan universal yang dapat dieksplorasi:
     - *Figur Pengemudi / Sopir Armada*: Karakter ramah bertopi pengemudi.
     - *Figur Penumpang / Petualang*: Membawa tas ransel, botol minum, atau koper perjalanan.
     - *Figur Kucing Terminal*: Kucing santai pengamat hilir mudik stasiun atau terminal.
     - *Figur Burung Perjalanan*: Burung kecil yang hinggap di rambu jalan atau spion kendaraan.
     - *Figur Petugas Loket / Kondektur*: Memegang tiket perjalanan.
4. **Ikonografi & Doodle Universal**: Menggunakan elemen perjalanan universal:
   - Tiket fisik berperforasi (*stub ticket*).
   - Penanda kilometer dan rambu petunjuk arah (*route milestone / directional sign*).
   - Minibus travel eksekutif dan bus antar-kota.
   - Koper, tas ransel, dan perlengkapan perjalanan (*luggage / travel gear*).
   - Bintang panduan (*navigation star*) dan garis lintasan rute meliuk.
   *(Tidak menggunakan simbol daerah lokal yang tidak relevan; fokus penuh pada elemen transportasi universal).*
5. **Mitigasi Visual Fatigue pada Layar Dashboard Operasional**:
   - Warna latar belakang kuning cerah hanya digunakan sebagai daya tarik utama pada **Hero Banner** dan **Footer Halaman Publik**.
   - Pada halaman **Member**, **Operator Loket**, dan **Admin**, latar belakang menggunakan warna kertas hangat bersih (*Clean Warm Stone / Paper White*), menjaga fokus mata petugas saat membaca tabel puluhan baris.

---

## 5. Usulan Palet Warna GOTIX (Color System)

Sistem warna dirancang mengikuti pendekatan *semantic token* dengan fondasi nuansa perjalanan yang hangat, optimis, dan terpercaya:

```
[ Primary: Gotix Amber #F59E0B ]   [ Secondary: Midnight Ink #18181B ]
[ Accent: Travel Coral #E11D48 ]     [ Canvas: Warm Paper #FAFAF9 ]
```

### A. Palet Inti (Core Palette Tokens)
- **Primary — Gotix Amber / Sun Gold (`brand-primary`)**:
  - `50`: `#FFFBEB` (Latar badge / highlight lembut)
  - `100`: `#FEF3C7` (Badge peringatan / batas bayar)
  - `400`: `#FBBF24` (Hover aksen cerah)
  - `500`: `#F59E0B` (Warna identitas utama Gotix, tombol primer, stempel bintang)
  - `600`: `#D97706` (Border fokus, teks berpenekanan pada latar terang)
  - `700`: `#B45309` (Active state tombol primer)
- **Secondary — Midnight Charcoal / Deep Ink (`brand-ink`)**:
  - `950`: `#09090B` (Deep ink border, bayangan keras hitam pekat)
  - `900`: `#18181B` (Tinta utama: garis stroke 2px, teks heading, wordmark stamp)
  - `800`: `#27272A` (Sidebar operator & admin, tombol sekunder solid)
  - `700`: `#3F3F46` (Ikon navigasi tidak aktif)
- **Accent — Travel Coral / Sunset Crimson (`brand-accent`)**:
  - `500`: `#F43F5E` / `#E11D48` (Stempel "LUNAS", hati/bintang doodle, peringatan tenggat waktu pembayaran)
- **Neutrals — Paper Canvas & Slate (`surface` & `background`)**:
  - `canvas-warm`: `#FAFAF9` (Latar belakang utama aplikasi — nuansa kertas gambar lembut)
  - `canvas-sunny`: `#FEF08A` / `#FDE047` (Latar belakang khusus Footer & Hero publik)
  - `surface-card`: `#FFFFFF` (Latar kartu putih bersih dengan kontras tinggi terhadap border ink)
  - `surface-subtle`: `#F4F4F5` (Header tabel, kolom pencarian pasif)
  - `text-body`: `#27272A` (Teks paragraf utama — kontras 12:1 di atas kertas putih)
  - `text-muted`: `#71717A` (Keterangan sekunder, sub-judul, jam keberangkatan)

### B. Warna Status Bisnis (Semantic Status Tokens)
Sesuai enum `App\Enums\StatusPesanan` dan kategori keluhan:
- **Belum Bayar (Menunggu Pembayaran)**:
  - Background: `#F1F5F9` (Slate 100) | Border: `#334155` (Slate 700) | Text: `#0F172A` (Slate 900)
- **Dalam Proses (Verifikasi Pembayaran)**:
  - Background: `#FEF3C7` (Amber 100) | Border: `#B45309` (Amber 700) | Text: `#78350F` (Amber 900)
- **Lunas (Tiket Terbit & Terverifikasi)**:
  - Background: `#D1FAE5` (Emerald 100) | Border: `#047857` (Emerald 700) | Text: `#064E3B` (Emerald 900)
- **Dibatalkan / Ditolak**:
  - Background: `#FFE4E6` (Rose 100) | Border: `#BE123C` (Rose 700) | Text: `#881337` (Rose 900)

---

## 6. Usulan Tipografi (Typography System)

Gotix telah memiliki fondasi tipografi Google Fonts yang sangat kuat dan relevan di `tailwind.config.js`: **Bricolage Grotesque** dan **DM Sans**. Keduanya disempurnakan dengan tambahan *editorial serif stack* untuk melengkapi karakter Picnic Club.

```
+------------------------------------------------------------------------------------+
|  DISPLAY & HERO:   Bricolage Grotesque 800/900 (Ekspresif, Tinta Kental, Berbobot)  |
|  EDITORIAL SERIF:  Iowan Old Style / Palatino / Georgia (Hangat, Storytelling)     |
|  BODY & UI:        DM Sans 400/500/700 (Jernih, Presisi, Nyaman di Layar Digital)  |
|  NUMERIK / KODE:   JetBrains Mono / Consolas (Kompak untuk Kode GTX & Rekening)   |
+------------------------------------------------------------------------------------+
```

### A. Hirarki Tipografi
1. **Display Mega Wordmark (Footer & Hero Stamp)**:
   - Font: `Bricolage Grotesque` ExtraBold / Black (Weight 800 - 900)
   - Karakter: Huruf kapital penuh, tracking ketat (`tracking-[-0.03em]`), teks diberi filter SVG tekstur tinta kasar.
2. **Heading 1 & Heading 2 (Judul Halaman & Section)**:
   - Font: `Bricolage Grotesque` Bold (Weight 700)
   - Tampilan berkarakter dengan *ink traps* alami yang memperkuat citra cetak fisik.
3. **Editorial Tagline & Kutipan Perjalanan**:
   - Font: Serif Stack (`"Iowan Old Style", "Palatino Linotype", Palatino, Georgia, serif`)
   - Style: Normal atau sedikit miring (*italic*), ukuran 16px - 20px, line-height lega (1.5).
4. **Body Text, Formulir, & Tabel**:
   - Font: `DM Sans` (Weight 400 Regular, 500 Medium, 700 Bold)
   - Menghasilkan keterbacaan (*readability*) tinggi untuk instruksi transfer bank, daftar rute, dan nama penumpang.
5. **Data Monospace (Kode Pesanan, Nomor Kursi, Jam)**:
   - Font: `font-mono` (`ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace`)
   - Ditempatkan dalam kotak berbingkai border tipis bergaya stempel tiket: `GTX-WB9AJV`.

---

## 7. Komponen dan Aturan Desain (Component Design Rules)

Seluruh komponen memadukan presisi fungsional dengan sentuhan taktil *retro-sketchbook*.

```
+------------------+    +------------------+    +-------------------+
|  [>] Pesan Tiket |    |  ( ) Verifikasi  |    |  [!] BATAS WAKTU  |
|  bg-amber-500    |    |  bg-white        |    |  bg-rose-100      |
|  border-2-ink    |    |  border-2-ink    |    |  border-2-ink     |
|  shadow-[3px_3px]|    |  shadow-[3px_3px]|    |  rounded-xl       |
+------------------+    +------------------+    +-------------------+
  (Tombol Primer)         (Tombol Sekunder)       (Alert Peringatan)
```

### A. Tombol (Buttons)
- **Primary Action (Amber Punch)**:
  - Kelas: `bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-bold border-2 border-slate-950 rounded-full px-5 py-2.5 shadow-[3px_3px_0px_#18181b] hover:shadow-[4px_4px_0px_#18181b] hover:-translate-y-0.5 active:translate-y-0.5 active:shadow-[1px_1px_0px_#18181b] transition-all`
- **Secondary / Outline**:
  - Kelas: `bg-white hover:bg-slate-50 text-slate-900 font-bold border-2 border-slate-950 rounded-full px-5 py-2.5 shadow-[3px_3px_0px_#18181b] transition-all`
- **Danger Action (Batalkan / Tolak)**:
  - Kelas: `bg-rose-100 hover:bg-rose-200 text-rose-900 font-bold border-2 border-slate-950 rounded-full px-5 py-2.5 shadow-[3px_3px_0px_#18181b]`

### B. Input & Select
- **Field Masukan**:
  - Latar belakang putih bersih, border tebal `border-2 border-slate-950`, sudut melengkung `rounded-xl`, shadow halus `shadow-[2px_2px_0px_#18181b]`.
  - State fokus: `focus:ring-0 focus:border-amber-600 focus:shadow-[3px_3px_0px_#d97706] focus:-translate-y-0.5 transition-all`.
  - Label: `font-bold text-xs uppercase tracking-wider text-slate-800 mb-1.5 flex items-center gap-1`.

### C. Kartu (Cards)
- **Retro Paper Card**:
  - `bg-white border-2 border-slate-950 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] relative overflow-hidden`.
  - Varian Header Berarsir: Bagian atas kartu memiliki strip aksen warna atau pola arsir halus untuk membedakan kategori (misal: Rute Perjalanan, Ringkasan Tarif, Data Penumpang).

### D. Badge Status
- Berbentuk kapsul tebal: `inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black border-1.5 border-slate-950 shadow-[1.5px_1.5px_0px_#18181b]`.
- Dilengkapi indikator titik bulat solid beranimasi halus atau ikon doodle mini.

### E. Tabel Data (Admin & Operator)
- Header tabel: `bg-slate-100 border-b-2 border-slate-950 text-slate-900 font-extrabold text-xs uppercase tracking-wider px-4 py-3`.
- Baris tabel: `border-b border-slate-200 hover:bg-amber-50/50 transition-colors`.
- Kolom aksi: Tombol ringkas berbentuk kapsul mini dengan label yang sangat lugas.

### F. Balon Komik & Empty State
- **Tooltip / Speech Bubble**: Kotak pesan bergaya balon komik dengan ekor segitiga SVG, border hitam 2px, dan shadow `3px 3px 0px #18181b`.
- **Empty State**: Ilustrasi doodle bus kosong atau koper santai buatan tangan, disertai teks jenaka yang informatif (*"Belum ada tiket aktif nih. Yuk cari jadwal jalan-jalan!"*).

---

## 8. Strategi Desain: Portal Member (Passenger-Centric)

```
[ Beranda Member ] ---> [ Cari Jadwal ] ---> [ Pilih Kursi 5-Slot ] ---> [ E-Tiket & Boarding Pass ]
(Kenyamanan &         (Pilihan Rute Cepat    (Denah Taktil Retro-Stempel   (Gaya Tiket Fisik dengan
 Kejelasan Status)     Populer Indramayu)     Bebas Double Booking)        Perforasi & QR Standar)
```

1. **Prioritas Pengguna**:
   - Menghilangkan kecemasan kehabisan tiket (*anxiety-free travel booking*).
   - Memastikan pengguna tahu persis status pembayaran dan sisa waktu bayar (*countdown timer* jelas).
2. **Denah Pemilihan Kursi Taktil (Interactive Seat Grid)**:
   - Menghilangkan kotak abu-abu hambar.
   - Kursi Kosong: Kursi bernuansa putih-krem dengan border tinta 2px dan nomor kursi besar bergaya stempel.
   - Kursi Dipilih: Berubah warna menjadi kuning amber hangat dengan ikon bintang mini di sudut.
   - Kursi Terisi: Berarsir diagonal halus (*hatched pattern*) dengan label ramah "Terisi".
3. **E-Tiket & Boarding Pass**:
   - Didesain menyerupai tiket perjalanan bus retro: sisi kiri memuat detail jadwal dan QR Code standar, dipisahkan oleh garis putus-putus berlubang perforasi (*ticket punch stub*), sisi kanan memuat ringkasan penumpang dan stempel status **LUNAS**.
   - Dilengkapi fungsi cetak bersih via `@media print`.

---

## 9. Strategi Desain: Portal Operator Loket (Speed & Efficiency)

```
+-----------------------------------------------------------------------------------+
| TOP BAR OPERATOR: [Kasir Loket Walk-in]  [Verifikasi Transfer (Antrean: 3)]       |
+-----------------------------------------------------------------------------------+
| PANEL KIRI: RUTE & DENAH KURSI             | PANEL KANAN: FORM CEPAT KASIR LOKET  |
| - Pilih Jadwal: Indramayu -> JKT (14:00)   | - Nama Penumpang: [Budi Santoso    ] |
| - Mini Bus HiAce [Kapasitas: 5]            | - No. WhatsApp:   [08123456789     ] |
|   [ (1) ] [ (2) ] [ (3) ] [ (4) ] [ (5) ]  | - Pilihan Kursi:  Kursi #03          |
|   (Hijau = Siap Jual, Merah = Terisi)      | - Total Bayar:    Rp 65.000 (TUNAI)  |
|                                            | [ > CETAK TIKET LOKET (Enter) ]      |
+-----------------------------------------------------------------------------------+
```

1. **Prioritas Pengguna**:
   - Kecepatan pemrosesan transaksi penumpang datang langsung (*walk-in passenger*).
   - Verifikasi bukti transfer tanpa keraguan dan tanpa klik berulang yang sia-sia.
2. **Karakter Visual Khusus Operator**:
   - **Bebas Distraksi**: Tidak ada doodle mengambang, animasi boil dinonaktifkan untuk menjaga stabilitas visual saat bekerja 8 jam sehari.
   - **High-Contrast Input Fields**: Form masukan memiliki ukuran teks lebih besar (16px), batas kolom tebal, dan auto-focus ke kolom nama penumpang.
   - **Split-Screen Layout**: Layar loket terbagi dua: sisi kiri denah ketersediaan armada, sisi kanan form eksekusi instan.
3. **Modal Verifikasi Pembayaran**:
   - Menampilkan gambar bukti transfer dalam ukuran besar dengan tombol zoom instan.
   - Tombol Terima (Hijau Emerald) dan Tolak (Merah Coral) diletakkan berjauhan dengan konfirmasi alasan wajib untuk mencegah salah klik operator.

---

## 10. Strategi Desain: Portal Admin (Operational Governance)

```
+-----------------------------------------------------------------------------------+
| SIDEBAR GOTIX        | METRIK OPERASIONAL HARI INI                                |
| [G] Gotix Admin      | [ Armada Jalan: 12 ] [ Tiket Terjual: 48 ] [ Omzet: Rp 3.2M ]
|                      +------------------------------------------------------------+
| - Dasbor Utama       | TABEL MANAJEMEN JADWAL & KELUHAN                           |
| - Jadwal & Armada    | Filter: [ Semua Kota v ] [ Status v ]  [ Cari Data...    ] |
| - Pesanan & Tiket    | ---------------------------------------------------------- |
| - Layanan Keluhan    | ID    Rute             Waktu     Armada   Kursi   Aksi     |
| - Data Pengguna      | #102  Indramayu        08:00 WIB E-1234   3/5     [Edit]   |
+-----------------------------------------------------------------------------------+
```

1. **Prioritas Pengguna**:
   - Kepadatan informasi tinggi (*high information density*), pemantauan metrik armada, dan manajemen master data (Kota, Kendaraan, Jadwal).
2. **Karakter Visual Khusus Admin**:
   - Tetap membawa DNA visual Gotix (border 2px, font Bricolage Grotesque, aksen warna status), tetapi diaplikasikan secara terukur dan disiplin tanpa ornamen dekoratif berlebih.
   - Kartu metrik ringkas menggunakan ikon doodle mikro monokromatik (misal: ikon bus mini untuk total kendaraan, tiket mini untuk pesanan hari ini).
   - Tabel master data memiliki fitur filter baris cepat, badge status warna tegas, dan pagination bergaya kapsul.

---

## 11. Strategi Responsive System

Desain dijamin fleksibel dari ponsel layar sempit hingga monitor resolusi ultra-wide:

| Breakpoint | Target Layar | Penyesuaian Layout & Komponen |
| :--- | :--- | :--- |
| **Mobile (`<640px`)** | Smartphone | - Navigasi beralih ke tab bawah (*bottom tab bar*) atau drawer ringkas.<br>- Wordmark GOTIX di footer mengecil proporsional via `viewBox` SVG.<br>- Denah kursi ditampilkan 1 baris memanjang dengan scroll horizontal halus.<br>- Tombol aksi memenuhi lebar layar (*w-full*). |
| **Tablet (`640px - 1023px`)** | iPad / Tablet Kasir | - Layout grid 2 kolom untuk kartu jadwal.<br>- Sidebar operator dapat dibuka-tutup (*slide overlay*).<br>- Footer crew menampilkan 3 karakter utama untuk menjaga kerapian. |
| **Desktop (`>=1024px`)** | Laptop & PC Loket | - Layar kerja penuh split-screen untuk Operator Loket.<br>- Sidebar Admin menetap di sisi kiri.<br>- Wordmark GOTIX di footer menampilkan seluruh kru lengkap dengan storyboard entrance. |

---

## 12. Strategi Animasi dan Aksesibilitas

### A. Pedoman Animasi Gotix
1. **Fungsional, Bukan Beban**: Animasi hanya dipakai untuk memberikan umpan balik aksi (*micro-feedback*) seperti tombol membal saat diklik (`active:translate-y-0.5`), balon pesan muncul lembut, atau progress bar stepper.
2. **Durasi Terukur**: Transisi UI berada pada kisaran `150ms - 250ms` dengan kurva `cubic-bezier(0.3, 1.4, 0.5, 1)` untuk memberi rasa pantulan elastis yang wajar.
3. **Pemberhentian Siklus Render**: Efek interaktif kursor hanya aktif saat mouse berada di dalam area footer.

### B. Kepatuhan Aksesibilitas (WCAG 2.1 AA)
- **Rasio Kontras Minimum 4.5:1**:
  - Teks tinta gelap `#18181B` di atas latar putih `#FFFFFF` (Rasio: 16.1:1 — Sangat Lulus).
  - Teks tinta gelap `#18181B` di atas latar amber `#F59E0B` (Rasio: 8.2:1 — Sangat Lulus).
  - Teks putih `#FFFFFF` di atas tombol coral `#E11D48` (Rasio: 5.1:1 — Sangat Lulus).
- **Indikator Fokus Eksplisit**:
  - Seluruh elemen yang dapat menerima fokus keyboard (`<a>`, `<button>`, `<input>`, `<select>`) memiliki styling `:focus-visible` berupa garis putus-putus kontras: `outline: 2px dashed #18181B; outline-offset: 4px;`.
- **Dukungan `prefers-reduced-motion`**:
  ```css
  @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after {
          animation-duration: 0.01ms !important;
          animation-iteration-count: 1 !important;
          transition-duration: 0.01ms !important;
      }
  }
  ```

---

## 13. Rekomendasi Struktur Implementasi pada Laravel Blade

Untuk mengadopsi prinsip desain tanpa mengotori proyek dengan Babel CDN atau dependensi React, implementasi teknis dilakukan menggunakan kapabilitas bawaan Laravel:

```
resources/
├── css/
│   └── app.css                      <-- Utility layer kustom (border-ink, shadow-retro, hatch)
├── views/
│   ├── components/
│   │   ├── button.blade.php         <-- Reusable pill button (primer, sekunder, danger)
│   │   ├── input.blade.php          <-- Reusable input form dengan border-ink & shadow
│   │   ├── card.blade.php           <-- Retro card container
│   │   ├── badge-status.blade.php   <-- Badge status pesanan terpadu
│   │   ├── tiket-pass.blade.php     <-- Komponen boarding pass fisik + QR SVG
│   │   ├── footer-crew.blade.php    <-- Full interactive Gotix Crew Footer (Halaman Publik)
│   │   └── footer-mini.blade.php    <-- Clean minimal utility footer (Operator & Admin)
│   └── layouts/
│       ├── tamu.blade.php           <-- Public shell (memuat footer-crew)
│       ├── member.blade.php         <-- Dedicated Member shell
│       ├── operator.blade.php       <-- Dedicated Operator workspace shell
│       └── admin.blade.php          <-- Dedicated Admin workspace shell
```

### A. Logika Interaktivitas via Alpine.js
Interaksi kursor dan efek klik karakter yang ada pada komponen React referensi diterjemahkan secara bersih ke dalam Alpine.js:
```html
<div x-data="gotixCrewFooter()" @pointermove="onPointerMove($event)" @pointerleave="onPointerLeave()">
    <!-- Inline SVG Gotix Crew dengan bind pupil mata dan speech bubble -->
    ...
</div>
```
Logika JavaScript disimpan di `resources/js/components/gotix-crew.js` dan di-import secara native oleh Vite di `resources/js/app.js`.

### B. Roadmap Tahapan Implementasi Redesign GOTIX
Sesuai arahan tata kelola proyek, implementasi redesign tidak dilakukan sekaligus melainkan dipisah per role secara bertahap:
- **P2: Design System Preparation**: Perumusan prinsip desain visual, token warna, tipografi, dan arsitektur komponen (tahap saat ini).
- **P3: Implementasi Design System, Reusable Blade Components, & Redesign Member**:
  - Pendaftaran token warna `Gotix Amber`, `Midnight Ink`, dan `Warm Paper` di `tailwind.config.js` serta utility classes di `app.css`.
  - Pembangunan pustaka komponen dasar Blade (`<x-button>`, `<x-input>`, `<x-card>`, `<x-badge-status>`, `<x-tiket-pass>`).
  - Perakitan shell publik dan komponen `<x-footer-crew>` pada halaman publik.
  - Redesign penuh portal **Member** (pencarian jadwal, pemilihan kursi 5-slot, e-tiket boarding pass, dan konfirmasi pembayaran).
- **P4: Redesign Operator Loket**:
  - Penerapan authenticated workspace shell khusus petugas loket.
  - Redesign antarmuka kasir loket walk-in (split-view rute & denah kursi) dan alur cepat cetak tiket fisik.
  - Redesign antarmuka verifikasi bukti transfer dengan modal preview zoomable yang ergonomis.
- **P5: Redesign Admin & Finalisasi**:
  - Penerapan workspace shell admin dengan navigasi sidebar efisien.
  - Redesign dasbor metrik operasional, tabel manajemen master data (Kota, Kendaraan, Jadwal), monitoring pesanan, dan layanan keluhan.
  - Audit konsistensi akhir, pengujian responsivitas lintas perangkat, dan finalisasi seluruh tampilan.

---

## 14. Ringkasan Rekomendasi & Keputusan Kunci

| Aspek | Rekomendasi GOTIX | Status Persetujuan |
| :--- | :--- | :---: |
| **Identitas Visual** | Mengadopsi prinsip *retro-sketchbook tactile*: garis tinta 2px tebal, hard shadow 3px, tipografi stempel, dan elemen perjalanan universal (bus, tiket, rute, terminal, koper). | Menunggu Tinjauan |
| **Palet Warna** | Menggunakan trio: **Gotix Amber** (`#F59E0B`), **Midnight Ink** (`#18181B`), dan **Warm Paper** (`#FAFAF9`). Menghindari latar kuning pekat di dashboard internal. | Menunggu Tinjauan |
| **Tipografi** | Memaksimalkan `Bricolage Grotesque` (Display/Headings) dan `DM Sans` (Body/Forms) yang sudah aktif, ditambah fallback editorial serif untuk tagline. | Menunggu Tinjauan |
| **Kru Karakter Gotix** | Konsep awal karakter perjalanan universal (pengemudi, penumpang, kucing terminal, burung perjalanan, petugas tiket) sebagai opsi eksploratif yang belum dikunci sebagai maskot resmi. | Menunggu Evaluasi Visual |
| **Dua Tipe Footer** | Footer ilustratif besar berekor animasi untuk halaman publik (`beranda`); Footer fungsional ringkas untuk portal Member, Operator, dan Admin. | Menunggu Tinjauan |
| **Teknologi Implementasi** | 100% Blade + Tailwind CSS v3 + Alpine.js native (tanpa React, tanpa Babel CDN, tanpa dependency baru). | Menunggu Tinjauan |

---
*Dokumen ini disusun sebagai cetak biru desain terpadu GOTIX Fase P2 dan siap diajukan untuk tinjauan pemilik proyek.*

