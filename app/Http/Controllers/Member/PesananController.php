<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(): View
    {
        $daftarPesanan = Auth::user()
            ?->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan'])
            ->latest()
            ->get() ?? collect();

        return view('member.pesanan.index', compact('daftarPesanan'));
    }

    public function show(string $kode): View
    {
        $pesanan = Auth::user()
            ?->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan'])
            ->where('kode_pesanan', $kode)
            ->first();

        return view('member.pesanan.show', ['kode' => $kode, 'pesanan' => $pesanan]);
    }
}
