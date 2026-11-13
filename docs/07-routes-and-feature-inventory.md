# 07 — Routes and Feature Inventory

## 1. Inventaris Lengkap Route (`routes/web.php`)

- **VERIFIED:** Terdapat **38 route aplikasi** yang didefinisikan di `routes/web.php` (ditambah `/up` healthcheck bawaan Laravel dari `bootstrap/app.php` L13). Tidak terdapat `routes/api.php` (`NOT IMPLEMENTED`).

### 1.1 Route Publik & Autentikasi

| Method | URI | Name | Middleware | Controller@Method | Fungsi & Status Aktual |
|---|---|---|---|---|---|
| `GET` | `/` | `beranda` | `web` | `Publik\BerandaController@index` | **VERIFIED (Aktif):** Landing page + pencarian cepat + jadwal populer. Campuran copywriting Jawa/Lampung. |
| `GET` | `/cari-jadwal` | `jadwal.cari` | `web` | `Publik\CariJadwalController@index` | **VERIFIED (Aktif):** Pencarian jadwal berdasarkan kota asal, tujuan, dan tanggal + rekomendasi rute. |
| `GET` | `/masuk` | `masuk` | `web`, `guest` | `Auth\MasukController@create` | **VERIFIED (Aktif):** Form login username/email. |
| `POST` | `/masuk` | `masuk.store` | `web`, `guest` | `Auth\MasukController@store` | **VERIFIED (Aktif):** Proses login + redirect sesuai peran (tanpa rate limiter). |
| `GET` | `/daftar` | `daftar` | `web`, `guest` | `Auth\DaftarController@create` | **VERIFIED (Aktif):** Form registrasi member baru. |
| `POST` | `/daftar` | `daftar.store` | `web`, `guest` | `Auth\DaftarController@store` | **VERIFIED (Aktif):** Registrasi member (`peran = Member`), login otomatis. |
| `POST` | `/keluar` | `keluar` | `web`, `auth` | `Auth\KeluarController@destroy` | **VERIFIED (Aktif):** Logout dan invalidate sesi. |
| `POST` | `/logout` | `logout` | `web`, `auth` | `Auth\KeluarController@destroy` | **VERIFIED (Aktif):** Alias route `/keluar`. |

### 1.2 Route Member (`/akun/*`, Middleware: `web`, `auth`, `peran:Member`)

| Method | URI | Name | Controller@Method | Fungsi & Status Aktual |
|---|---|---|---|---|
| `GET` | `/akun/tiket` | `member.tiket.index` | `Member\TiketController@index` | **VERIFIED (Bermasalah):** Menampilkan *semua* pesanan (termasuk belum lunas/batal), bukan hanya tiket `Lunas`. |
| `GET` | `/akun/tiket/{kode}` | `member.tiket.show` | `Member\TiketController@show` | **VERIFIED (Mocked/Rusak):** Query memakai `first()` (tanpa filter `Lunas`); view 100% hardcoded dummy Jakarta–Bandung. |
| `GET` | `/akun/pesanan` | `member.pesanan.index` | `Member\PesananController@index` | **VERIFIED (Aktif):** Riwayat seluruh pesanan milik member. |
| `GET` | `/akun/pesanan/{kode}` | `member.pesanan.show` | `Member\PesananController@show` | **VERIFIED (Aktif):** Detail pesanan, status pembayaran, dan tautan ke bayar/tiket. |
| `GET` | `/akun/jadwal/{jadwal}/pesan` | `member.pesanan.create` | `Member\PesananController@create` | **VERIFIED (Aktif):** Form pemilihan kursi interaktif berbasis Alpine.js. |
| `POST` | `/akun/jadwal/{jadwal}/pesan` | `member.pesanan.store` | `Member\PesananController@store` | **VERIFIED (Cacat DB):** Buat pesanan `Belum Bayar`; gagal jika memilih kursi bekas pesanan `Dibatalkan` akibat unique index DB. |
| `GET` | `/akun/pesanan/{kode}/bayar` | `member.pembayaran.show` | `Member\PembayaranController@show` | **VERIFIED (Aktif, isu rekening statis):** Halaman instruksi & unggah bukti transfer (rekening BCA hardcoded). |
| `POST` | `/akun/pesanan/{kode}/bayar` | `member.pembayaran.store` | `Member\PembayaranController@konfirmasiStore` | **VERIFIED (Parsial):** Simpan bukti transfer ke disk `public` & ubah status ke `DalamProses`; tidak cek kadaluarsa & duplicate resi. |
| `GET` | `/akun/pesanan/{kode}/konfirmasi` | `member.pembayaran.konfirmasi` | `Member\PembayaranController@konfirmasi` | **VERIFIED (Placeholder):** Mengembalikan `[PLACEHOLDER] Member — Konfirmasi Pembayaran`. |
| `GET` | `/akun/keluhan` | `member.keluhan.index` | `Member\KeluhanController@index` | **VERIFIED (Mocked):** View menimpa data DB dari controller dengan array dummy statis. |
| `GET` | `/akun/keluhan/baru` | `member.keluhan.create` | `Member\KeluhanController@create` | **VERIFIED (Mocked):** Form pakai `action="#"` dan `alert()`, tidak pernah submit ke server. |
| `POST` | `/akun/keluhan` | `member.keluhan.store` | `Member\KeluhanController@store` | **VERIFIED (Tidak Tersambung & Isu Validasi):** Backend ada tetapi tidak dipakai oleh form; validasi `max:1000` bertentangan dengan kolom `VARCHAR(200)`. |

### 1.3 Route Operator Loket (`/operator/*`, Middleware: `web`, `auth`, `peran:Operator`)

| Method | URI | Name | Controller@Method | Fungsi & Status Aktual |
|---|---|---|---|---|
| `GET` | `/operator/pembayaran` | `operator.pembayaran.index` | `Operator\VerifikasiPembayaranController@index` | **VERIFIED (Aktif, bug tampilan kolom):** Antrean pesanan `DalamProses` & `BelumBayar`; view salah mengakses `nama_pengirim`. |
| `GET` | `/operator/pembayaran/{id}` | `operator.pembayaran.show` | `Operator\VerifikasiPembayaranController@show` | **VERIFIED (Aktif, guard UI lemah):** Detail bukti bayar + tombol Verifikasi/Tolak muncul di semua status. |
| `POST` | `/operator/pembayaran/{id}/verifikasi` | `operator.pembayaran.verifikasi` | `Operator\VerifikasiPembayaranController@verifikasi` | **VERIFIED (Parsial):** Mengubah status menjadi `Lunas`, tetapi tidak mencatat `diverifikasi_oleh`/`diverifikasi_pada` dan tanpa cek state/transaksi. |
| `POST` | `/operator/pembayaran/{id}/tolak` | `operator.pembayaran.tolak` | `Operator\VerifikasiPembayaranController@tolak` | **VERIFIED (Parsial):** Mengubah status menjadi `Dibatalkan`, tanpa `alasan_penolakan` maupun audit trail. |
| `GET` | `/operator/loket` | `operator.loket.index` | `Operator\PesananLoketController@index` | **VERIFIED (Mocked):** View mengabaikan `$jadwalAktif`, memakai rute/kursi statis dan modal struk palsu tanpa POST. |
| `GET` | `/operator/loket/buat` | `operator.loket.create` | `Operator\PesananLoketController@create` | **VERIFIED (Placeholder):** Mengembalikan `[PLACEHOLDER] Operator — Pesanan Loket`. |
| `POST` | `/operator/loket` | `operator.loket.store` | `Operator\PesananLoketController@store` | **VERIFIED (Tidak Tersambung & Isu Logika):** Hardcode `max:5` kursi & wajib `pengguna_id` terdaftar. |

### 1.4 Route Admin (`/admin/*`, Middleware: `web`, `auth`, `peran:Admin`)

| Method | URI | Name | Controller@Method | Fungsi & Status Aktual |
|---|---|---|---|---|
| `GET` | `/admin/dasbor` | `admin.dasbor` | `Admin\DasborController@index` | **VERIFIED (Aktif):** Ringkasan metrik, pendapatan, keluhan belum dibalas, pesanan perlu verifikasi. |
| `GET` | `/admin/jadwal` | `admin.jadwal.index` | `Admin\JadwalController@index` | **VERIFIED (Aktif):** Daftar jadwal perjalanan dengan pagination/eager loading. |
| `GET` | `/admin/jadwal/tambah` | `admin.jadwal.create` | `Admin\JadwalController@create` | **VERIFIED (Aktif):** Form tambah jadwal baru. |
| `POST` | `/admin/jadwal` | `admin.jadwal.store` | `Admin\JadwalController@store` | **VERIFIED (Aktif):** Simpan jadwal (kurang validasi `kota_tujuan != kota_asal` & tanggal >= hari ini). |
| `GET` | `/admin/jadwal/{id}` | `admin.jadwal.show` | `Admin\JadwalController@show` | **VERIFIED (Placeholder):** Mengembalikan `[PLACEHOLDER] Admin — Detail Jadwal`. |
| `GET` | `/admin/jadwal/{id}/edit` | `admin.jadwal.edit` | `Admin\JadwalController@edit` | **VERIFIED (Aktif):** Form edit jadwal. |
| `PUT` | `/admin/jadwal/{id}` | `admin.jadwal.update` | `Admin\JadwalController@update` | **VERIFIED (Aktif):** Update jadwal. |
| `DELETE` | `/admin/jadwal/{id}` | `admin.jadwal.destroy` | `Admin\JadwalController@destroy` | **VERIFIED (Risiko Data):** Hapus jadwal langsung (`cascadeOnDelete` menghapus seluruh pesanan terkait). |
| `GET` | `/admin/kendaraan` | `admin.kendaraan.index` | `Admin\KendaraanController@index` | **VERIFIED (Aktif):** Daftar armada kendaraan. |
| `GET` | `/admin/kendaraan/tambah` | `admin.kendaraan.create` | `Admin\KendaraanController@create` | **VERIFIED (Aktif):** Form tambah kendaraan. |
| `POST` | `/admin/kendaraan` | `admin.kendaraan.store` | `Admin\KendaraanController@store` | **VERIFIED (Aktif):** Simpan kendaraan. |
| `GET` | `/admin/kendaraan/{id}/edit` | `admin.kendaraan.edit` | `Admin\KendaraanController@edit` | **VERIFIED (Aktif):** Form edit kendaraan. |
| `PUT` | `/admin/kendaraan/{id}` | `admin.kendaraan.update` | `Admin\KendaraanController@update` | **VERIFIED (Aktif):** Update kendaraan (`Rule::unique` dengan ignore). |
| `DELETE` | `/admin/kendaraan/{id}` | `admin.kendaraan.destroy` | `Admin\KendaraanController@destroy` | **VERIFIED (Risiko Data):** Hapus kendaraan langsung (`cascadeOnDelete` ke jadwal & pesanan). |
| `GET` | `/admin/kota` | `admin.kota.index` | `Admin\KotaController@index` | **VERIFIED (Aktif):** Daftar kota layanan. |
| `GET` | `/admin/kota/tambah` | `admin.kota.create` | `Admin\KotaController@create` | **VERIFIED (Aktif):** Form tambah kota. |
| `POST` | `/admin/kota` | `admin.kota.store` | `Admin\KotaController@store` | **VERIFIED (Aktif):** Simpan kota. |
| `GET` | `/admin/kota/{id}/edit` | `admin.kota.edit` | `Admin\KotaController@edit` | **VERIFIED (Aktif):** Form edit kota. |
| `PUT` | `/admin/kota/{id}` | `admin.kota.update` | `Admin\KotaController@update` | **VERIFIED (Aktif):** Update kota (`Rule::unique` dengan ignore). |
| `DELETE` | `/admin/kota/{id}` | `admin.kota.destroy` | `Admin\KotaController@destroy` | **VERIFIED (Risiko Data):** Hapus kota langsung (`cascadeOnDelete` ke jadwal & pesanan). |
| `GET` | `/admin/pengguna` | `admin.pengguna.index` | `Admin\PenggunaController@index` | **VERIFIED (Aktif, Read-Only):** Daftar seluruh pengguna. Tidak ada fitur create/edit/hapus user. |
| `GET` | `/admin/pengguna/{id}` | `admin.pengguna.show` | `Admin\PenggunaController@show` | **VERIFIED (Aktif, Read-Only):** Detail profil pengguna beserta ringkasan pesanan/keluhan. |
| `GET` | `/admin/pesanan` | `admin.pesanan.index` | `Admin\PesananController@index` | **VERIFIED (Aktif):** Monitoring seluruh pesanan. |
| `GET` | `/admin/pesanan/{id}` | `admin.pesanan.show` | `Admin\PesananController@show` | **VERIFIED (Aktif):** Detail pesanan & bukti bayar. |
| `POST` | `/admin/pesanan/{id}/verifikasi` | `admin.pesanan.verifikasi` | `Admin\PesananController@verifikasi` | **VERIFIED (Parsial):** Set `Lunas` tanpa cek state/transaksi/audit trail. |
| `POST` | `/admin/pesanan/{id}/tolak` | `admin.pesanan.tolak` | `Admin\PesananController@tolak` | **VERIFIED (Parsial):** Set `Dibatalkan` tanpa `alasan_penolakan` maupun audit trail. |
| `GET` | `/admin/keluhan` | `admin.keluhan.index` | `Admin\KeluhanController@index` | **VERIFIED (Aktif):** Daftar seluruh keluhan masuk. |
| `GET` | `/admin/keluhan/{id}` | `admin.keluhan.show` | `Admin\KeluhanController@show` | **VERIFIED (Aktif):** Detail keluhan & form balasan admin. |
| `POST` | `/admin/keluhan/{id}/balas` | `admin.keluhan.balas` | `Admin\KeluhanController@balas` | **VERIFIED (Aktif):** Simpan balasan dan timestamp `dibalas_pada`. |

