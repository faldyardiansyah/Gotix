<?php

namespace App\Http\Controllers\Admin;

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
            'jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna', 'konfirmasiPembayaran.verifikator', 'riwayatPembayaran.verifikator',
        ])->findOrFail($id);

        return view('admin.pesanan.show', compact('pesanan'));
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
                return redirect()->route('admin.pesanan.show', $id)
                    ->with('gagal', 'Pesanan tidak dapat diverifikasi karena status saat ini adalah: '.$pesanan->status->value);
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

            return redirect()->route('admin.pesanan.show', $id)
                ->with('sukses', 'Pesanan #'.$pesanan->kode_pesanan.' berhasil diverifikasi menjadi LUNAS.');
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
                return redirect()->route('admin.pesanan.show', $id)
                    ->with('gagal', 'Pesanan tidak dapat ditolak karena status saat ini adalah: '.$pesanan->status->value);
            }

            // P0-04: Status dikembalikan ke Belum Bayar
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

            return redirect()->route('admin.pesanan.show', $id)
                ->with('sukses', 'Pesanan #'.$pesanan->kode_pesanan.' telah DITOLAK dan dikembalikan ke status Belum Bayar.');
        });
    }
}
