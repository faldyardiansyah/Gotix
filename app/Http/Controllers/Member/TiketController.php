<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TiketController extends Controller
{
    public function index(): View
    {
        $tiketList = Auth::user()
            ?->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan'])
            ->latest()
            ->get() ?? collect();

        return view('member.tiket.index', compact('tiketList'));
    }

    public function show(string $kode): View
    {
        $pesanan = Auth::user()
            ?->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan'])
            ->where('kode_pesanan', $kode)
            ->first();

        return view('member.tiket.show', ['kode' => $kode, 'pesanan' => $pesanan]);
    }
}
