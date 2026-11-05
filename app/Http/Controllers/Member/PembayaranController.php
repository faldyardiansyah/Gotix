<?php

namespace App\Http\Controllers\Member;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\KonfirmasiPembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
            'bukti_transfer'    => ['required', 'image', 'max:5120'], // maks 5MB
            'rekening_pengirim' => ['required', 'string', 'max:150'],
            'nomor_resi'        => ['nullable', 'string', 'max:100'],
            'tanggal_transfer'  => ['nullable', 'date'],
            'jam_transfer'      => ['nullable'],
        ]);

        $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');

        $nomorResi = $request->filled('nomor_resi')
            ? $request->nomor_resi
            : 'TF-' . strtoupper(Str::random(10));

        KonfirmasiPembayaran::updateOrCreate(
            ['pesanan_id' => $pesanan->id],
            [
                'nomor_resi'        => $nomorResi,
                'rekening_pengirim' => $request->rekening_pengirim,
                'tanggal_transfer'  => $request->tanggal_transfer ?? now()->toDateString(),
                'jam_transfer'      => $request->jam_transfer ?? now()->format('H:i:s'),
                'bukti_path'        => $path,
            ]
        );

        $pesanan->update(['status' => StatusPesanan::DalamProses]);

        return redirect()->route('member.pesanan.show', $kode)
            ->with('sukses', 'Foto bukti transfer berhasil diunggah! Menunggu verifikasi dari operator.');
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
