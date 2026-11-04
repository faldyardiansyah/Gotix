<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(): View
    {
        $daftarPesanan = Pesanan::with([
            'jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna',
        ])->latest()->get();

        return view('admin.pesanan.index', compact('daftarPesanan'));
    }

    public function show(string $id): View
    {
        $pesanan = Pesanan::with([
            'jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna', 'konfirmasiPembayaran',
        ])->findOrFail($id);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function verifikasi(string $id): RedirectResponse
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->update(['status' => StatusPesanan::Lunas]);

        return redirect()->route('admin.pesanan.show', $id)
            ->with('sukses', 'Pesanan #' . $pesanan->kode_pesanan . ' berhasil diverifikasi menjadi LUNAS.');
    }

    public function tolak(string $id): RedirectResponse
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->update(['status' => StatusPesanan::Dibatalkan]);

        return redirect()->route('admin.pesanan.show', $id)
            ->with('sukses', 'Pesanan #' . $pesanan->kode_pesanan . ' telah DITOLAK / DIBATALKAN.');
    }
}
