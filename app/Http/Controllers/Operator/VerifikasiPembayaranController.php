<?php

namespace App\Http\Controllers\Operator;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\RiwayatPembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            'jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna', 'konfirmasiPembayaran.verifikator', 'riwayatPembayaran.verifikator',
        ])->findOrFail($id);

        return view('operator.pembayaran.show', compact('pesanan'));
    }

    public function verifikasi(Request $request, string $id): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403, 'Silakan masuk terlebih dahulu.');
        }

        return DB::transaction(function () use ($id, $user) {
            /** @var Pesanan $pesanan */
            $pesanan = Pesanan::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($pesanan->status !== StatusPesanan::DalamProses) {
                return redirect()->route('operator.pembayaran.show', $id)
                    ->with('gagal', 'Pembayaran tidak dapat diverifikasi karena status pesanan saat ini adalah: '.$pesanan->status->value);
            }

            $pesanan->update(['status' => StatusPesanan::Lunas]);

            if ($pesanan->konfirmasiPembayaran) {
                $pesanan->konfirmasiPembayaran->update([
                    'diverifikasi_oleh' => $user->id,
                    'diverifikasi_pada' => now(),
                    'alasan_penolakan' => null,
                ]);
            }

            if (Schema::hasTable('riwayat_pembayaran')) {
                $latestRiwayat = RiwayatPembayaran::where('pesanan_id', $pesanan->id)->latest()->first();
                if ($latestRiwayat) {
                    $latestRiwayat->update([
                        'status' => 'Diterima',
                        'diverifikasi_oleh' => $user->id,
                        'diverifikasi_pada' => now(),
                        'alasan_penolakan' => null,
                    ]);
                }
            }

            return redirect()->route('operator.pembayaran.index')
                ->with('sukses', 'Pembayaran berhasil diverifikasi. Tiket telah aktif.');
        });
    }

    public function tolak(Request $request, string $id): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403, 'Silakan masuk terlebih dahulu.');
        }

        $maxAlasan = (int) config('gotix.keluhan.alasan_penolakan_max', 1000);

        $request->validate([
            'alasan_penolakan' => ['required', 'string', "max:{$maxAlasan}"],
        ]);

        return DB::transaction(function () use ($id, $user, $request) {
            /** @var Pesanan $pesanan */
            $pesanan = Pesanan::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($pesanan->status !== StatusPesanan::DalamProses) {
                return redirect()->route('operator.pembayaran.show', $id)
                    ->with('gagal', 'Pembayaran tidak dapat ditolak karena status pesanan saat ini adalah: '.$pesanan->status->value);
            }

            // P0-04: Status dikembalikan ke Belum Bayar agar penumpang dapat mengunggah ulang sebelum batas bayar
            $pesanan->update(['status' => StatusPesanan::BelumBayar]);

            if ($pesanan->konfirmasiPembayaran) {
                $pesanan->konfirmasiPembayaran->update([
                    'diverifikasi_oleh' => $user->id,
                    'diverifikasi_pada' => now(),
                    'alasan_penolakan' => $request->alasan_penolakan,
                ]);
            }

            if (Schema::hasTable('riwayat_pembayaran')) {
                $latestRiwayat = RiwayatPembayaran::where('pesanan_id', $pesanan->id)->latest()->first();
                if ($latestRiwayat) {
                    $latestRiwayat->update([
                        'status' => 'Ditolak',
                        'diverifikasi_oleh' => $user->id,
                        'diverifikasi_pada' => now(),
                        'alasan_penolakan' => $request->alasan_penolakan,
                    ]);
                }
            }

            return redirect()->route('operator.pembayaran.index')
                ->with('sukses', 'Pembayaran ditolak. Pesanan dikembalikan ke status Belum Bayar.');
        });
    }
}
