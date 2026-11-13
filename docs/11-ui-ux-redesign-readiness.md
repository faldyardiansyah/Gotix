# 11 — UI/UX Redesign Readiness Evaluation

## 1. Kesimpulan Kesiapan Menuju Phase 2

- **Status Kesiapan:** **BELUM SIAP LANGSUNG REDESIGN PENUH TANPA PERBAIKAN PRASYARAT.**
- **VERIFIED:** Secara visual dan struktural, halaman-halaman existing sudah memiliki fondasi Tailwind + Alpine.js dan tata letak dasar, **namun** terdapat **4 fitur inti yang masih mocked/placeholder** (E-Tiket Member, Keluhan Member, Walk-in Loket Operator, Detail Jadwal Admin & Konfirmasi Pembayaran), **1 bug database pemblokir kursi** (`unique_jadwal_nomor_kursi`), dan **konflik konfigurasi Tailwind v3 vs v4**.
- Jika tim langsung melakukan redesign UI/UX di atas kondisi saat ini, desain baru hanya akan membungkus form palsu (`action="#"`), tabel tanpa data riil, atau alur transaksi yang error di database.

---

## 2. Evaluasi Kesiapan per Peran (Shared Design System)

| Peran / Area | Kondisi UI Saat Ini | Kendala Sebelum Masuk Design System Terpadu |
|---|---|---|
| **Publik & Auth** | `layouts.tamu` (navbar atas + footer, splash 850ms, AOS CDN). Copywriting campuran Lampung & Jawa, karakter rusak di `beranda.blade.php`. | Harus memisahkan splash/animasi berat dari halaman aplikasi terautentikasi, memperbaiki key `config('gotix.bank.*')`, serta membatasi CTA booking agar tidak melempar 403 ke staf. |
| **1. Customer / Member** | Memakai `layouts.tamu` + `@include('member._nav')` hanya pada 3 halaman `index`. Halaman `pesanan/create`, `show`, `pembayaran/show`, `tiket/show`, `keluhan/create` tidak punya sub-nav konsisten. | Alur **E-Tiket (`tiket/show`)** dan **Keluhan (`keluhan/*`)** masih 100% mocked dan harus dihubungkan ke data/controller nyata; diagram kursi `grid-cols-5` harus mendukung kapasitas kendaraan dinamis (>5 kursi). |
| **2. Operator Loket** | Inkonsisten: `layouts/operator.blade.php` tersedia di repo tapi **tidak dipakai**; halaman operator memakai `layouts.tamu` + footer publik + `@include('operator._nav')` (kecuali `pembayaran/show`). | Harus dipindahkan ke shell dashboard operasional (tanpa footer & splash publik); `operator/loket/index` harus disambungkan ke backend real dan mendukung skema penumpang walk-in + kapasitas kursi dinamis; `pembayaran/index` harus memperbaiki kolom `rekening_pengirim`. |
| **3. Admin** | Memakai `layouts.admin` (sidebar gelap kiri `slate-900`, header terang) yang paling terstruktur; `admin/_nav.blade.php` yatim. | Form `create`/`edit` masih duplikat penuh tanpa komponen reusable; `jadwal/show` masih placeholder; halaman kelola akun Operator/Member (`pengguna`) masih read-only. |

---

## 3. Daftar Risiko yang WAJIB Diselesaikan Sebelum / Awal Phase 2 (Must-Fix)

1. **Perbaikan Konflik Tailwind CSS (v3 vs v4):** Tetapkan satu sistem token desain tunggal (disarankan migrasi bersih ke **Tailwind v4 `@theme` + `@tailwindcss/vite`** sesuai standar modern proyek, atau bersihkan paket v4 dan kunci di v3) serta kompilasi ulang aset (`npm run build`). Tanpa ini, komponen Design System baru akan mengalami kelas utility yang hilang seperti 49 kasus `shadow-xs`/`w-4.5` saat ini.
2. **Perbaikan Constraint `unique_jadwal_nomor_kursi` + Cascade Delete:** Kursi yang dibatalkan harus bisa dipesan ulang tanpa menabrak `QueryException`, dan penghapusan master kota/kendaraan/jadwal tidak boleh memusnahkan data pesanan historis.
3. **Penyambungan Kontrak Data pada Fitur Mocked/Placeholder:**
   - Hubungkan `member/tiket/{index,show}` ke `$pesanan` status `Lunas` (hapus dummy Jakarta–Bandung).
   - Hubungkan `member/keluhan/{index,create}` ke `KeluhanController` & selaraskan panjang kolom `deskripsi`.
   - Rancang & hubungkan kontrak `operator/loket` ke `PesananLoketController` (kapasitas kursi dinamis + data penumpang non-akun).
   - Selesaikan status route placeholder `admin.jadwal.show` dan `member.pembayaran.konfirmasi`.
4. **Pengamanan Alur Verifikasi & Pembayaran:** Terapkan `DB::transaction`, guard state machine (`BelumBayar` -> `DalamProses` -> `Lunas`/`Dibatalkan`), pencatatan audit (`diverifikasi_oleh`, `diverifikasi_pada`, `alasan_penolakan`), validasi `batas_bayar`, `unique:nomor_resi`, dan penyimpanan bukti transfer di disk privat.
5. **Konsolidasi Arsitektur Layout 3 Peran:** Putuskan struktur shell UI untuk Design System bersama:
   - `layouts.publik` (landing, cari jadwal, auth — ringan tanpa splash blocking 850ms di setiap navigasi).
   - `layouts.app` / `layouts.dasbor` terpadu (dipakai bersama oleh **Member**, **Operator Loket**, dan **Admin** dengan menu sidebar/topbar dinamis berdasarkan `PeranPengguna`, berbagi token warna, tipografi, badge status, tabel, form input, dan modal konfirmasi).

---

## 4. Perbaikan yang Dapat Dikerjakan Bertahap (Non-Blocker / Selama Redesign)

- Ekstraksi **Blade Components** (`resources/views/components/`): `<x-badge-status>`, `<x-button>`, `<x-input>`, `<x-select>`, `<x-card>`, `<x-modal-konfirmasi>` (menggantikan `confirm()`/`alert()` native serta menyatukan form `_form.blade.php` admin).
- Ekstraksi helper format (`Format::rupiah()`, `Format::tanggal()`) dan `Service`/`Action` (`KetersediaanKursiService`, `VerifikasiPembayaranAction`).
- Penambahan manajemen akun staf (`Admin\PenggunaController` CRUD Operator/Admin) dan rate limiter login.
- Penambahan Laravel Policies (`PesananPolicy`, `KeluhanPolicy`), konfigurasi `phpstan.neon`, serta perluasan suite test Pest di `gotix_testing`.
- Pembersihan file mati (`welcome.blade.php`, `admin/_nav.blade.php`, `layouts/operator.blade.php` setelah diganti shell terpadu) dan sinkronisasi `.agent/rules/gotix.md`.

---

## 5. Rekomendasi Urutan Eksekusi Setelah Persetujuan Audit (Pra-Phase 2 -> Phase 2)

```text
[Tahap A: Stabilisasi Fondasi & Kontrak Data] (Wajib Sebelum Redesign UI)
  1. Kunci versi Tailwind (v4 @theme atau v3) & bersihkan package konflik.
  2. Migrasi DB perbaikan: conditional unique kursi aktif, pembesaran keluhan.deskripsi,
     soft delete / restrictOnDelete, opsi guest walk-in loket (bila disetujui).
  3. Perbaiki controller inti (Tiket Lunas, Verifikasi transaksional + audit, Batas Bayar,
     Privasi file bukti transfer, Validasi Keluhan & Loket).

[Tahap B: Fondasi Design System Bersama (3 Peran)]
  4. Definisi token desain (warna brand, netral, status semantic, tipografi, spacing).
  5. Bangun Blade Components reusable & layout shell terpadu (Publik vs Portal Member/Operator/Admin).

[Tahap C: Redesign UI/UX Per Modul & Verifikasi]
  6. Implementasi ulang UI Publik & Auth -> Member -> Operator Loket -> Admin.
  7. Lengkapi Feature Test Pest, PHPStan level 5+, dan Pint check.
```

