<?php

namespace App\Http\Controllers\Member;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Services\TiketService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TiketController extends Controller
{
    /**
     * Tampilkan seluruh tiket dan riwayat pemesanan milik member aktif.
     */
    public function index(): View
    {
        /** @var Pengguna $user */
        $user = Auth::user();

        $tiketList = $user
            ->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan'])
            ->latest()
            ->get();

        return view('member.tiket.index', compact('tiketList'));
    }

    /**
     * Tampilkan e-tiket resmi (boarding pass) dengan QR Code verifikasi.
     * Hanya pesanan berstatus LUNAS dan milik pengguna aktif yang dapat diakses.
     */
    public function show(string $kode, TiketService $tiketService): View
    {
        /** @var Pengguna $user */
        $user = Auth::user();

        /** @var Pesanan $pesanan */
        $pesanan = $user
            ->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna'])
            ->where('kode_pesanan', $kode)
            ->where('status', StatusPesanan::Lunas)
            ->firstOrFail();

        $qrSvg = $tiketService->generateQrSvg($pesanan);
        $payload = $tiketService->generatePayload($pesanan);

        return view('member.tiket.show', compact('pesanan', 'qrSvg', 'payload'));
    }
}
