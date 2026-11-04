<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kendaraan;
use App\Models\Kota;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\View\View;

class DasborController extends Controller
{
    public function index(): View
    {
        $totalJadwal = Jadwal::count();
        $totalKendaraan = Kendaraan::count();
        $totalKota = Kota::count();
        $totalPengguna = Pengguna::count();
        $totalPesanan = Pesanan::count();
        $menanggguVerifikasi = Pesanan::where('status', \App\Enums\StatusPesanan::DalamProses)->count();
        $totalLunas = Pesanan::where('status', \App\Enums\StatusPesanan::Lunas)->count();

        $jadwalTerbaru = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan', 'pesanan'])
            ->orderBy('tanggal_berangkat', 'desc')
            ->take(5)
            ->get();

        return view('admin.dasbor', compact(
            'totalJadwal',
            'totalKendaraan',
            'totalKota',
            'totalPengguna',
            'totalPesanan',
            'menanggguVerifikasi',
            'totalLunas',
            'jadwalTerbaru',
        ));
    }
}
