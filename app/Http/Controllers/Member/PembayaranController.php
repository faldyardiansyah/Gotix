<?php

namespace App\Http\Controllers\Member;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\KonfirmasiPembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function show(string $kode): View
    {
        $pesanan = Auth::user()
            ?->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'konfirmasiPembayaran'])
            ->where('kode_pesanan', $kode)
            ->firstOrFail();

        return view('member.pembayaran.show', compact('pesanan'));
    }

    public function konfirmasiStore(Request $request, string $kode): RedirectResponse
    {
        $pesanan = Auth::user()
            ?->pesanan()
            ->where('kode_pesanan', $kode)
            ->where('status', StatusPesanan::BelumBayar)
            ->firstOrFail();

        $request->validate([
            'bukti_transfer' => ['required', 'image', 'max:2048'],
            'nama_pengirim'  => ['required', 'string', 'max:100'],
            'jumlah_bayar'   => ['required', 'integer', 'min:1000'],
            'catatan'        => ['nullable', 'string', 'max:500'],
        ]);

        $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');

        KonfirmasiPembayaran::updateOrCreate(
            ['pesanan_id' => $pesanan->id],
            [
                'nama_pengirim' => $request->nama_pengirim,
                'jumlah_bayar'  => $request->jumlah_bayar,
                'bukti_transfer' => $path,
                'catatan'        => $request->catatan,
            ]
        );

        $pesanan->update(['status' => StatusPesanan::DalamProses]);

        return redirect()->route('member.pesanan.show', $kode)
            ->with('sukses', 'Konfirmasi pembayaran berhasil dikirim. Menunggu verifikasi operator.');
    }

    public function konfirmasi(string $kode): View
    {
        $pesanan = Auth::user()
            ?->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'konfirmasiPembayaran'])
            ->where('kode_pesanan', $kode)
            ->firstOrFail();

        return view('member.pembayaran.konfirmasi', compact('pesanan'));
    }
}
