<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kota;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CariJadwalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan', 'pesanan']);

        if ($request->filled('kota_asal')) {
            $query->whereHas('kotaAsal', function ($q) use ($request) {
                $q->where('nama_kota', 'like', '%'.$request->kota_asal.'%');
            });
        }

        if ($request->filled('kota_tujuan')) {
            $query->whereHas('kotaTujuan', function ($q) use ($request) {
                $q->where('nama_kota', 'like', '%'.$request->kota_tujuan.'%');
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_berangkat', $request->tanggal);
        } else {
            $query->whereDate('tanggal_berangkat', '>=', now()->toDateString());
        }

        $hasilJadwal = $query->orderBy('tanggal_berangkat')->orderBy('jam_berangkat')->get();

        // Jika hasil kosong karena filter tanggal atau belum ada, sediakan jadwal terdekat sebagai alternatif
        $jadwalRekomendasi = collect();
        if ($hasilJadwal->isEmpty()) {
            $jadwalRekomendasi = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan', 'pesanan'])
                ->whereDate('tanggal_berangkat', '>=', now()->toDateString())
                ->orderBy('tanggal_berangkat')
                ->take(6)
                ->get();
        }

        $daftarKota = Kota::orderBy('nama_kota')->get();

        return view('publik.cari-jadwal', compact('hasilJadwal', 'jadwalRekomendasi', 'daftarKota'));
    }
}
