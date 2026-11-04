<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\DaftarController;
use App\Http\Controllers\Auth\KeluarController;
use App\Http\Controllers\Auth\MasukController;
use App\Http\Controllers\Member;
use App\Http\Controllers\Operator;
use App\Http\Controllers\Publik;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Publik
|--------------------------------------------------------------------------
*/
Route::get('/', [Publik\BerandaController::class, 'index'])->name('beranda');
Route::get('/cari-jadwal', [Publik\CariJadwalController::class, 'index'])->name('jadwal.cari');

/*
|--------------------------------------------------------------------------
| Route Autentikasi (hanya untuk tamu)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [MasukController::class, 'create'])->name('masuk');
    Route::post('/masuk', [MasukController::class, 'store'])->name('masuk.store');

    Route::get('/daftar', [DaftarController::class, 'create'])->name('daftar');
    Route::post('/daftar', [DaftarController::class, 'store'])->name('daftar.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [KeluarController::class, 'destroy'])->name('keluar');
    Route::post('/logout', [KeluarController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Route Member (/akun)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'peran:Member'])->prefix('akun')->name('member.')->group(function () {
    // Tiket (riwayat tiket yang sudah lunas)
    Route::get('/tiket', [Member\TiketController::class, 'index'])->name('tiket.index');
    Route::get('/tiket/{kode}', [Member\TiketController::class, 'show'])->name('tiket.show');

    // Pesanan — booking baru
    Route::get('/pesanan', [Member\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{kode}', [Member\PesananController::class, 'show'])->name('pesanan.show');

    // Booking kursi dari halaman cari jadwal
    Route::get('/jadwal/{jadwal}/pesan', [Member\PesananController::class, 'create'])->name('pesanan.create');
    Route::post('/jadwal/{jadwal}/pesan', [Member\PesananController::class, 'store'])->name('pesanan.store');

    // Pembayaran
    Route::get('/pesanan/{kode}/bayar', [Member\PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pesanan/{kode}/bayar', [Member\PembayaranController::class, 'konfirmasiStore'])->name('pembayaran.store');
    Route::get('/pesanan/{kode}/konfirmasi', [Member\PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');

    // Keluhan
    Route::get('/keluhan', [Member\KeluhanController::class, 'index'])->name('keluhan.index');
    Route::get('/keluhan/baru', [Member\KeluhanController::class, 'create'])->name('keluhan.create');
    Route::post('/keluhan', [Member\KeluhanController::class, 'store'])->name('keluhan.store');
});

/*
|--------------------------------------------------------------------------
| Route Operator (/operator)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'peran:Operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/pembayaran', [Operator\VerifikasiPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{id}', [Operator\VerifikasiPembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pembayaran/{id}/verifikasi', [Operator\VerifikasiPembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::post('/pembayaran/{id}/tolak', [Operator\VerifikasiPembayaranController::class, 'tolak'])->name('pembayaran.tolak');

    Route::get('/loket', [Operator\PesananLoketController::class, 'index'])->name('loket.index');
    Route::get('/loket/buat', [Operator\PesananLoketController::class, 'create'])->name('loket.create');
    Route::post('/loket', [Operator\PesananLoketController::class, 'store'])->name('loket.store');
});

/*
|--------------------------------------------------------------------------
| Route Admin (/admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'peran:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dasbor', [Admin\DasborController::class, 'index'])->name('dasbor');

    // Jadwal
    Route::get('/jadwal', [Admin\JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/tambah', [Admin\JadwalController::class, 'create'])->name('jadwal.create');
    Route::post('/jadwal', [Admin\JadwalController::class, 'store'])->name('jadwal.store');
    Route::get('/jadwal/{id}', [Admin\JadwalController::class, 'show'])->name('jadwal.show');
    Route::get('/jadwal/{id}/edit', [Admin\JadwalController::class, 'edit'])->name('jadwal.edit');
    Route::put('/jadwal/{id}', [Admin\JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [Admin\JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Kendaraan
    Route::get('/kendaraan', [Admin\KendaraanController::class, 'index'])->name('kendaraan.index');
    Route::get('/kendaraan/tambah', [Admin\KendaraanController::class, 'create'])->name('kendaraan.create');
    Route::post('/kendaraan', [Admin\KendaraanController::class, 'store'])->name('kendaraan.store');
    Route::get('/kendaraan/{id}/edit', [Admin\KendaraanController::class, 'edit'])->name('kendaraan.edit');
    Route::put('/kendaraan/{id}', [Admin\KendaraanController::class, 'update'])->name('kendaraan.update');
    Route::delete('/kendaraan/{id}', [Admin\KendaraanController::class, 'destroy'])->name('kendaraan.destroy');

    // Kota
    Route::get('/kota', [Admin\KotaController::class, 'index'])->name('kota.index');
    Route::get('/kota/tambah', [Admin\KotaController::class, 'create'])->name('kota.create');
    Route::post('/kota', [Admin\KotaController::class, 'store'])->name('kota.store');
    Route::get('/kota/{id}/edit', [Admin\KotaController::class, 'edit'])->name('kota.edit');
    Route::put('/kota/{id}', [Admin\KotaController::class, 'update'])->name('kota.update');
    Route::delete('/kota/{id}', [Admin\KotaController::class, 'destroy'])->name('kota.destroy');

    // Pengguna
    Route::get('/pengguna', [Admin\PenggunaController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/{id}', [Admin\PenggunaController::class, 'show'])->name('pengguna.show');

    // Pesanan (Lihat Detail, Terima/Verifikasi, Tolak)
    Route::get('/pesanan', [Admin\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [Admin\PesananController::class, 'show'])->name('pesanan.show');
    Route::post('/pesanan/{id}/verifikasi', [Admin\PesananController::class, 'verifikasi'])->name('pesanan.verifikasi');
    Route::post('/pesanan/{id}/tolak', [Admin\PesananController::class, 'tolak'])->name('pesanan.tolak');

    // Keluhan (Lihat Detail & Kirim Tanggapan Balasan)
    Route::get('/keluhan', [Admin\KeluhanController::class, 'index'])->name('keluhan.index');
    Route::get('/keluhan/{id}', [Admin\KeluhanController::class, 'show'])->name('keluhan.show');
    Route::post('/keluhan/{id}/balas', [Admin\KeluhanController::class, 'balas'])->name('keluhan.balas');
});
