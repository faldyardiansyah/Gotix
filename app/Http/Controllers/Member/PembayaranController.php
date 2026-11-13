<?php

namespace App\Http\Controllers\Member;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\KonfirmasiPembayaran;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Models\RiwayatPembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function show(string $kode): View
    {
        /** @var Pengguna $user */
        $user = Auth::user();

        $pesanan = $user
            ->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'konfirmasiPembayaran', 'riwayatPembayaran.verifikator'])
            ->where('kode_pesanan', $kode)
            ->firstOrFail();

        return view('member.pembayaran.show', compact('pesanan'));
    }

    public function konfirmasiStore(Request $request, string $kode): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403, 'Silakan masuk terlebih dahulu.');
        }

        $pesanan = Pesanan::where('kode_pesanan', $kode)->firstOrFail();

        // Pemeriksaan kepemilikan
        if ($pesanan->pengguna_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk pesanan ini.');
        }

        // Pemeriksaan status dan batas bayar sebelum upload file
        if ($pesanan->status === StatusPesanan::Lunas) {
            return redirect()->route('member.pesanan.show', $kode)
                ->with('gagal', 'Pesanan ini sudah berstatus LUNAS. Tidak dapat mengunggah bukti pembayaran baru.');
        }

        if ($pesanan->status === StatusPesanan::Dibatalkan) {
            return redirect()->route('member.pesanan.show', $kode)
                ->with('gagal', 'Pesanan ini telah DIBATALKAN. Pembayaran tidak dapat diproses.');
        }

        if ($pesanan->status !== StatusPesanan::BelumBayar) {
            return redirect()->route('member.pesanan.show', $kode)
                ->with('gagal', 'Pesanan sedang dalam proses verifikasi atau tidak menunggu pembayaran.');
        }

        if ($pesanan->batas_bayar && now()->gt($pesanan->batas_bayar)) {
            return redirect()->route('member.pesanan.show', $kode)
                ->with('gagal', 'Batas waktu pembayaran pesanan telah terlewati.');
        }

        $existingKonfirmasi = KonfirmasiPembayaran::where('pesanan_id', $pesanan->id)->first();

        $maxKb = (int) config('gotix.pembayaran.max_file_kb', 5120);
        $mimes = implode(',', (array) config('gotix.pembayaran.mimes', ['jpg', 'jpeg', 'png', 'webp', 'pdf']));

        $request->validate([
            'bukti_transfer' => ['required', 'file', "mimes:{$mimes}", "max:{$maxKb}"],
            'rekening_pengirim' => ['required', 'string', 'max:150'],
            'nomor_resi' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('konfirmasi_pembayaran', 'nomor_resi')->ignore($existingKonfirmasi?->id),
            ],
            'tanggal_transfer' => ['nullable', 'date'],
            'jam_transfer' => ['nullable'],
        ]);

        // Simpan ke private disk ('local' root: storage/app/private)
        $path = $request->file('bukti_transfer')->store('bukti_pembayaran', 'local');

        try {
            DB::transaction(function () use ($pesanan, $user, $request, $path) {
                /** @var Pesanan $lockedPesanan */
                $lockedPesanan = Pesanan::where('id', $pesanan->id)->lockForUpdate()->firstOrFail();

                if ($lockedPesanan->status !== StatusPesanan::BelumBayar) {
                    throw new \DomainException('Pesanan tidak dalam status Belum Bayar.');
                }

                if ($lockedPesanan->batas_bayar && now()->gt($lockedPesanan->batas_bayar)) {
                    throw new \DomainException('Batas waktu pembayaran pesanan telah terlewati.');
                }

                $nomorResi = $request->filled('nomor_resi')
                    ? $request->nomor_resi
                    : 'TF-'.strtoupper(Str::random(10));

                // P0-04: Berkas lama dipertahankan dalam private storage sebagai bukti historis
                $konfirmasi = KonfirmasiPembayaran::updateOrCreate(
                    ['pesanan_id' => $lockedPesanan->id],
                    [
                        'nomor_resi' => $nomorResi,
                        'rekening_pengirim' => $request->rekening_pengirim,
                        'tanggal_transfer' => $request->tanggal_transfer ?? now()->toDateString(),
                        'jam_transfer' => $request->jam_transfer ?? now()->format('H:i:s'),
                        'bukti_path' => $path,
                        'diverifikasi_oleh' => null,
                        'diverifikasi_pada' => null,
                        'alasan_penolakan' => null,
                    ]
                );

                if (Schema::hasTable('riwayat_pembayaran')) {
                    RiwayatPembayaran::create([
                        'pesanan_id' => $lockedPesanan->id,
                        'konfirmasi_pembayaran_id' => $konfirmasi->id,
                        'pengguna_id' => $user->id,
                        'nomor_resi' => $nomorResi,
                        'rekening_pengirim' => $request->rekening_pengirim,
                        'tanggal_transfer' => $request->tanggal_transfer ?? now()->toDateString(),
                        'jam_transfer' => $request->jam_transfer ?? now()->format('H:i:s'),
                        'bukti_path' => $path,
                        'status' => 'Dalam Proses',
                    ]);
                }

                $lockedPesanan->update(['status' => StatusPesanan::DalamProses]);
            });
        } catch (\DomainException $e) {
            // Hapus berkas baru jika validasi bisnis dalam transaction lock gagal
            Storage::disk('local')->delete($path);

            return redirect()->route('member.pesanan.show', $kode)->with('gagal', $e->getMessage());
        } catch (\Throwable $e) {
            // Hapus berkas baru jika terjadi kegagalan database sistemik
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return redirect()->route('member.pesanan.show', $kode)
            ->with('sukses', 'Foto bukti transfer berhasil diunggah! Menunggu verifikasi dari operator.');
    }

    public function konfirmasi(string $kode): View
    {
        /** @var Pengguna $user */
        $user = Auth::user();

        $pesanan = $user
            ->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'konfirmasiPembayaran'])
            ->where('kode_pesanan', $kode)
            ->firstOrFail();

        return view('member.pembayaran.konfirmasi', compact('pesanan'));
    }
}
