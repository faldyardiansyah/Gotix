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
});

/*
|--------------------------------------------------------------------------
| Route Member (/akun)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'peran:Member'])->prefix('akun')->name('member.')->group(function () {
    Route::get('/tiket', [Member\TiketController::class, 'index'])->name('tiket.index');
    Route::get('/tiket/{kode}', [Member\TiketController::class, 'show'])->name('tiket.show');

    Route::get('/pesanan', [Member\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{kode}', [Member\PesananController::class, 'show'])->name('pesanan.show');

    Route::get('/pesanan/{kode}/bayar', [Member\PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::get('/pesanan/{kode}/konfirmasi', [Member\PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');

    Route::get('/keluhan', [Member\KeluhanController::class, 'index'])->name('keluhan.index');
    Route::get('/keluhan/baru', [Member\KeluhanController::class, 'create'])->name('keluhan.create');
});

/*
|--------------------------------------------------------------------------
| Route Operator (/operator)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'peran:Operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/pembayaran', [Operator\VerifikasiPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{id}', [Operator\VerifikasiPembayaranController::class, 'show'])->name('pembayaran.show');

    Route::get('/loket', [Operator\PesananLoketController::class, 'create'])->name('loket.create');
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
    Route::get('/jadwal/{id}/edit', [Admin\JadwalController::class, 'edit'])->name('jadwal.edit');
    Route::get('/jadwal/{id}', [Admin\JadwalController::class, 'show'])->name('jadwal.show');

    // Kendaraan
    Route::get('/kendaraan', [Admin\KendaraanController::class, 'index'])->name('kendaraan.index');
    Route::get('/kendaraan/tambah', [Admin\KendaraanController::class, 'create'])->name('kendaraan.create');
    Route::get('/kendaraan/{id}/edit', [Admin\KendaraanController::class, 'edit'])->name('kendaraan.edit');

    // Kota
    Route::get('/kota', [Admin\KotaController::class, 'index'])->name('kota.index');
    Route::get('/kota/tambah', [Admin\KotaController::class, 'create'])->name('kota.create');
    Route::get('/kota/{id}/edit', [Admin\KotaController::class, 'edit'])->name('kota.edit');

    // Pengguna
    Route::get('/pengguna', [Admin\PenggunaController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/{id}', [Admin\PenggunaController::class, 'show'])->name('pengguna.show');

    // Pesanan
    Route::get('/pesanan', [Admin\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [Admin\PesananController::class, 'show'])->name('pesanan.show');

    // Keluhan
    Route::get('/keluhan', [Admin\KeluhanController::class, 'index'])->name('keluhan.index');
    Route::get('/keluhan/{id}', [Admin\KeluhanController::class, 'show'])->name('keluhan.show');
});
