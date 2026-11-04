<?php

namespace App\Http\Controllers\Operator;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VerifikasiPembayaranController extends Controller
{
    public function index(): View
    {
        $daftarPesanan = Pesanan::with([
            'jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna', 'konfirmasiPembayaran',
        ])
            ->whereIn('status', [StatusPesanan::DalamProses, StatusPesanan::BelumBayar])
            ->latest()
            ->get();

        $selesaiVerifikasi = Pesanan::where('status', StatusPesanan::Lunas)->count();

        return view('operator.pembayaran.index', compact('daftarPesanan', 'selesaiVerifikasi'));
    }

    public function show(string $id): View
    {
        $pesanan = Pesanan::with([
            'jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna', 'konfirmasiPembayaran',
        ])->findOrFail($id);

        return view('operator.pembayaran.show', compact('pesanan'));
    }

    public function verifikasi(string $id): RedirectResponse
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->update(['status' => StatusPesanan::Lunas]);

        return redirect()->route('operator.pembayaran.index')
            ->with('sukses', 'Pembayaran berhasil diverifikasi. Tiket telah aktif.');
    }

    public function tolak(string $id): RedirectResponse
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->update(['status' => StatusPesanan::Dibatalkan]);

        return redirect()->route('operator.pembayaran.index')
            ->with('gagal', 'Pembayaran ditolak. Pesanan dibatalkan.');
    }
}
