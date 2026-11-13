<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kendaraan;
use App\Models\Kota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(): View
    {
        $jadwalList = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan', 'pesanan'])
            ->orderBy('tanggal_berangkat', 'desc')
            ->orderBy('jam_berangkat', 'desc')
            ->get();

        return view('admin.jadwal.index', compact('jadwalList'));
    }

    public function create(): View
    {
        $daftarKota = Kota::orderBy('nama_kota')->get();
        $daftarKendaraan = Kendaraan::orderBy('jenis_kendaraan')->get();

        return view('admin.jadwal.create', compact('daftarKota', 'daftarKendaraan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kota_asal_id' => ['required', 'exists:kota,id'],
            'kota_tujuan_id' => ['required', 'exists:kota,id', 'different:kota_asal_id'],
            'kendaraan_id' => ['required', 'exists:kendaraan,id'],
            'harga' => ['required', 'integer', 'min:1000'],
            'tanggal_berangkat' => ['required', 'date'],
            'jam_berangkat' => ['required'],
        ]);

        Jadwal::create($validated);

        return redirect()->route('admin.jadwal.index')
            ->with('sukses', 'Jadwal berhasil ditambahkan.');
    }

    public function show(string $id): View
    {
        $jadwal = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan', 'pesanan.pengguna'])
            ->findOrFail($id);

        return view('admin.jadwal.show', compact('jadwal'));
    }

    public function edit(string $id): View
    {
        $jadwal = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan'])->findOrFail($id);
        $daftarKota = Kota::orderBy('nama_kota')->get();
        $daftarKendaraan = Kendaraan::orderBy('jenis_kendaraan')->get();

        return view('admin.jadwal.edit', compact('jadwal', 'daftarKota', 'daftarKendaraan'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $jadwal = Jadwal::findOrFail($id);

        $validated = $request->validate([
            'kota_asal_id' => ['required', 'exists:kota,id'],
            'kota_tujuan_id' => ['required', 'exists:kota,id', 'different:kota_asal_id'],
            'kendaraan_id' => ['required', 'exists:kendaraan,id'],
            'harga' => ['required', 'integer', 'min:1000'],
            'tanggal_berangkat' => ['required', 'date'],
            'jam_berangkat' => ['required'],
        ]);

        $jadwal->update($validated);

        return redirect()->route('admin.jadwal.index')
            ->with('sukses', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $jadwal = Jadwal::with('pesanan')->findOrFail($id);

        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        // 1. Cek apakah ada transaksi pembayaran yang belum terselesaikan (Belum Bayar atau Dalam Proses)
        $hasUnresolvedTransactions = $jadwal->pesanan()
            ->whereIn('status', [
                StatusPesanan::BelumBayar,
                StatusPesanan::DalamProses,
            ])
            ->exists();

        if ($hasUnresolvedTransactions) {
            return back()->withErrors([
                'error' => 'Jadwal perjalanan ini tidak dapat dihapus karena masih memiliki transaksi pemesanan yang belum terselesaikan (Belum Bayar atau Dalam Proses verifikasi).',
            ]);
        }

        // 2. Cek apakah jadwal merupakan keberangkatan aktif masa depan dengan tiket penumpang lunas
        $isFutureOrCurrent = ($jadwal->tanggal_berangkat->toDateString() > $today) ||
            ($jadwal->tanggal_berangkat->toDateString() === $today && $jadwal->jam_berangkat >= $currentTime);

        if ($isFutureOrCurrent) {
            $hasActiveTickets = $jadwal->pesanan()
                ->where('status', StatusPesanan::Lunas)
                ->exists();

            if ($hasActiveTickets) {
                return back()->withErrors([
                    'error' => 'Jadwal keberangkatan masa depan ini memiliki tiket penumpang aktif yang sudah lunas dan tidak dapat dihapus.',
                ]);
            }
        }

        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('sukses', 'Jadwal perjalanan berhasil dihapus.');
    }
}
