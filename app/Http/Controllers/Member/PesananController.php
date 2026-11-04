<?php

namespace App\Http\Controllers\Member;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PesananController extends Controller
{
    public function index(): View
    {
        $daftarPesanan = Auth::user()
            ?->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan'])
            ->latest()
            ->get() ?? collect();

        return view('member.pesanan.index', compact('daftarPesanan'));
    }

    public function show(string $kode): View
    {
        $pesanan = Auth::user()
            ?->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'konfirmasiPembayaran'])
            ->where('kode_pesanan', $kode)
            ->firstOrFail();

        return view('member.pesanan.show', compact('pesanan'));
    }

    /**
     * Tampilkan form pilih kursi untuk jadwal tertentu.
     */
    public function create(Jadwal $jadwal): View
    {
        // Ambil kursi yang sudah terisi (bukan yang dibatalkan)
        $kursiTerisi = $jadwal->pesanan()
            ->whereIn('status', [
                StatusPesanan::BelumBayar,
                StatusPesanan::DalamProses,
                StatusPesanan::Lunas,
            ])
            ->pluck('nomor_kursi')
            ->toArray();

        $jadwal->load(['kotaAsal', 'kotaTujuan', 'kendaraan']);

        return view('member.pesanan.create', compact('jadwal', 'kursiTerisi'));
    }

    /**
     * Simpan pesanan baru dengan pessimistic locking.
     */
    public function store(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $request->validate([
            'nomor_kursi' => ['required', 'integer', 'min:1', 'max:' . ($jadwal->kendaraan->kapasitas_kursi ?? 5)],
        ]);

        $nomorKursi = (int) $request->nomor_kursi;

        try {
            $kode = DB::transaction(function () use ($jadwal, $nomorKursi) {
                // Pessimistic lock — cek apakah kursi masih kosong
                $sudahDipesan = $jadwal->pesanan()
                    ->lockForUpdate()
                    ->where('nomor_kursi', $nomorKursi)
                    ->whereIn('status', [
                        StatusPesanan::BelumBayar,
                        StatusPesanan::DalamProses,
                        StatusPesanan::Lunas,
                    ])
                    ->exists();

                if ($sudahDipesan) {
                    throw new \RuntimeException('Kursi sudah dipesan oleh penumpang lain. Silakan pilih kursi lain.');
                }

                // Buat kode unik GTX-XXXXXX
                do {
                    $kode = 'GTX-' . strtoupper(Str::random(6));
                } while (Pesanan::where('kode_pesanan', $kode)->exists());

                Pesanan::create([
                    'kode_pesanan'   => $kode,
                    'jadwal_id'      => $jadwal->id,
                    'pengguna_id'    => Auth::id(),
                    'nomor_kursi'    => $nomorKursi,
                    'harga_snapshot' => $jadwal->harga,
                    'status'         => StatusPesanan::BelumBayar,
                    'batas_bayar'    => now()->addHours(24),
                ]);

                return $kode;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['nomor_kursi' => $e->getMessage()])->withInput();
        } catch (Throwable) {
            return back()->withErrors(['nomor_kursi' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('member.pesanan.show', $kode)
            ->with('sukses', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran sebelum batas waktu.');
    }
}
