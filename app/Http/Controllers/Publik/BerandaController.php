<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kota;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        $daftarKota = Kota::orderBy('nama_kota')->get();

        // Jadwal populer — ambil jadwal ke depan dari DB, maks 6
        $jadwalPopuler = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan', 'pesanan'])
            ->whereDate('tanggal_berangkat', '>=', now()->toDateString())
            ->orderBy('tanggal_berangkat')
            ->orderBy('jam_berangkat')
            ->take(6)
            ->get();

        return view('publik.beranda', compact('daftarKota', 'jadwalPopuler'));
    }
}
