# 08 — Business Logic Documentation

## 1. Peta Alur Bisnis Utama GOTIX

- **VERIFIED:** Seluruh proses bisnis inti GOTIX dirancang dalam 6 alur berikut, namun baru sebagian yang terhubung penuh ke backend dan database:

```text
[1. Cari Jadwal] ──> [2. Pilih Kursi & Pesan] ──> [3. Bayar & Unggah Bukti]
   (Publik)              (Member)                     (Member)
                             │                            │
                             │                            ▼
                             │                    [4. Verifikasi Pembayaran]
                             │                      (Operator / Admin)
                             │                            │
                             ▼                            ▼
                    [5. Walk-In Loket]            [6. E-Tiket]        [7. Keluhan]
                       (Operator)                   (Member)       (Member <-> Admin)
```

---

## 2. Dokumentasi Rinci Setiap Alur & Evaluasi Logika

### 2.1 Pencarian Jadwal (`Publik\BerandaController`, `Publik\CariJadwalController`)
- **VERIFIED:**
  - `BerandaController::index()` memuat 6 jadwal terdekat (`Jadwal::tersedia()`) dan daftar nama kota untuk dropdown pencarian cepat.
  - `CariJadwalController::index()` memfilter `Jadwal::tersedia()` berdasarkan parameter query opsional `kota_asal`, `kota_tujuan`, dan `tanggal`.
  - Scope `Jadwal::tersedia()` memfilter `tanggal_berangkat >= today()` serta kendaraan `aktif = true`.
- **POTENTIAL ISSUE:** Tidak ada pengecekan jam keberangkatan untuk jadwal di hari yang sama (`tanggal_berangkat == today()` tetapi `jam_berangkat` sudah lewat tetap ditampilkan). Selain itu di grid rekomendasi `cari-jadwal.blade.php`, tombol pesan tidak menyembunyikan jadwal yang `sisa_kursi <= 0`.

### 2.2 Pemesanan Tiket Online & Penguncian Kursi (`Member\PesananController`)
- **VERIFIED:**
  1. `create(Jadwal $jadwal)` memuat jadwal + relasi `kendaraan`, `kotaAsal`, `kotaTujuan`, mengambil daftar `nomor_kursi` yang sudah terisi (`status` in `BelumBayar`, `DalamProses`, `Lunas`), lalu meneruskan `$kapasitas = $jadwal->kendaraan->kapasitas_kursi ?? 5`.
  2. `store(Request $request, Jadwal $jadwal)` memvalidasi `nomor_kursi` (`1..$kapasitas`), membuka `DB::transaction`, melakukan **pessimistic locking (`lockForUpdate()`)** pada baris pesanan jadwal untuk kursi tersebut (mengecualikan `Dibatalkan`), menghasilkan kode unik `GTX-XXXXXX`, menyimpan `harga_snapshot = $jadwal->harga`, menetapkan `status = StatusPesanan::BelumBayar`, dan `batas_bayar = now()->addHours(24)`.
- **Cacat Logika Terverifikasi:**
  - **CRITICAL (`unique_jadwal_nomor_kursi`):** Meskipun query `lockForUpdate()` mengecualikan `StatusPesanan::Dibatalkan`, tabel `pesanan` masih memaksakan unique constraint atas semua baris `(jadwal_id, nomor_kursi)`. Kursi dari pesanan yang pernah dibatalkan selalu memicu `QueryException` saat dipesan ulang, lalu tertangkap `catch (Throwable)` dengan pesan generik `'Terjadi kesalahan sistem. Silakan coba lagi.'`.
  - **HIGH (`batas_bayar` tidak terbaca dari config & tidak pernah kedaluwarsa):** Hardcode `now()->addHours(24)` alih-alih `config('gotix.batas_bayar_jam')`. Command `BatalkanPesananKedaluwarsa` belum ada (`NOT IMPLEMENTED`), sehingga kursi yang melewati `batas_bayar` tidak pernah dilepaskan kembali.
  - **POTENTIAL ISSUE:** Tidak ada pengecekan bahwa `$jadwal` masih valid/aktif (`tanggal_berangkat >= today()` dan `kendaraan->aktif == true`) saat `POST store()` langsung ditembak ke `/akun/jadwal/{jadwal}/pesan`.

### 2.3 Konfirmasi Pembayaran Member (`Member\PembayaranController`)
- **VERIFIED:**
  1. `show(string $kode)` menampilkan instruksi transfer & form unggah bukti untuk pesanan milik `Auth::user()`.
  2. `konfirmasiStore(Request $request, string $kode)` mengambil pesanan milik user dengan `status = StatusPesanan::BelumBayar` (`firstOrFail()`), memvalidasi `bukti_transfer` (`image`, `max:5120`), `rekening_pengirim` (`max:150`), `nomor_resi` (`nullable|max:100`), menyimpan berkas ke disk `public` (`bukti_transfer/`), menjalankan `KonfirmasiPembayaran::updateOrCreate(['pesanan_id' => $pesanan->id], ...)`, dan mengubah `pesanan.status` menjadi `StatusPesanan::DalamProses`.
  3. `konfirmasi(string $kode)` merender view placeholder `[PLACEHOLDER] Member — Konfirmasi Pembayaran`.
- **Cacat Logika Terverifikasi:**
  - **HIGH:** Tidak menolak pesanan yang melewati `batas_bayar`; tidak memvalidasi `unique:konfirmasi_pembayaran,nomor_resi`; tidak memakai `DB::transaction()`; menyimpan bukti sensitif di disk `public`.
  - **HIGH (UI):** Halaman `member/pembayaran/show.blade.php` L112-114 menampilkan nomor rekening BCA statis (`8820 1928 33`), tidak sinkron dengan `config/gotix.php`.

### 2.4 Verifikasi Pembayaran (`Operator\VerifikasiPembayaranController` & `Admin\PesananController`)
- **VERIFIED:**
  - Operator dapat melihat antrean `DalamProses` + `BelumBayar` di `operator.pembayaran.index`.
  - Baik Operator (`VerifikasiPembayaranController`) maupun Admin (`Admin\PesananController`) memiliki method `verifikasi($id)` (mengubah `pesanan.status` -> `Lunas`) dan `tolak($id)` (mengubah `pesanan.status` -> `Dibatalkan`).
- **Cacat Logika Terverifikasi:**
  - **CRITICAL:** Tidak ada validasi state awal (`DalamProses` + punya `konfirmasiPembayaran`), tidak ada `DB::transaction()`, dan **kolom `diverifikasi_oleh`, `diverifikasi_pada`, serta `alasan_penolakan` pada `konfirmasi_pembayaran` tidak pernah ditulis**.
  - **HIGH:** View `operator/pembayaran/index.blade.php` L90 mengakses `$pesanan->konfirmasiPembayaran->nama_pengirim` (kolom salah; seharusnya `rekening_pengirim`), dan halaman detail `show.blade.php` Operator & Admin merender tombol Verifikasi/Tolak pada seluruh status tanpa guard.

### 2.5 Penerbitan & Tampilan E-Tiket (`Member\TiketController`)
- **VERIFIED (Status: RUSAK / MOCKED):**
  - `TiketController::index()` mengambil semua pesanan user (termasuk `BelumBayar`, `DalamProses`, `Dibatalkan`), tidak memfilter `StatusPesanan::Lunas`.
  - `TiketController::show($kode)` menggunakan `->first()` (bukan `firstOrFail()`) tanpa mengecek status `Lunas`.
  - View `resources/views/member/tiket/show.blade.php` mengabaikan `$pesanan` sepenuhnya dan menampilkan tiket statis rute Jakarta–Bandung dengan SVG QR palsu.

### 2.6 Pemesanan Loket Walk-in (`Operator\PesananLoketController`)
- **VERIFIED (Status: MOCKED di Frontend, Cacat Validasi di Backend):**
  - Backend `PesananLoketController::store()` langsung menetapkan `status = StatusPesanan::Lunas` dengan `lockForUpdate()`, namun:
    - Hardcode validasi kursi `max:5` alih-alih kapasitas kendaraan.
    - Wajib memilih `pengguna_id` yang sudah ada di tabel `pengguna` (tidak mendukung penumpang walk-in tanpa akun).
  - Frontend `operator/loket/index.blade.php` tidak pernah me-POST form ke `operator.loket.store`; hanya memakai data rute statis dan modal struk print palsu Alpine.js. `operator/loket/create.blade.php` masih placeholder.

### 2.7 Manajemen Keluhan (`Member\KeluhanController` & `Admin\KeluhanController`)
- **VERIFIED (Status: Putus di Sisi Member, Aktif di Sisi Admin):**
  - Sisi Admin (`Admin\KeluhanController`) dapat melihat dan membalas keluhan (`balasan` + `dibalas_pada`).
  - Sisi Member (`member/keluhan/index.blade.php`) menimpa data DB dengan array dummy; `member/keluhan/create.blade.php` memakai `action="#"` + `alert()`; dan `Member\KeluhanController::store()` memvalidasi deskripsi `max:1000` terhadap kolom DB `VARCHAR(200)`, tidak memvalidasi enum `KategoriKeluhan`, serta tidak menyimpan `pesanan_id`.

