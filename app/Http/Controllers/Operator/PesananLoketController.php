<?php

namespace App\Http\Controllers\Operator;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PesananLoketController extends Controller
{
    public function index(): View
    {
        $jadwalAktif = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan', 'pesanan'])
            ->whereDate('tanggal_berangkat', '>=', now()->toDateString())
            ->orderBy('tanggal_berangkat')
            ->orderBy('jam_berangkat')
            ->get();

        return view('operator.loket.index', compact('jadwalAktif'));
    }

    public function create(): View
    {
        $jadwalAktif = Jadwal::with(['kotaAsal', 'kotaTujuan', 'kendaraan', 'pesanan'])
            ->whereDate('tanggal_berangkat', '>=', now()->toDateString())
            ->orderBy('tanggal_berangkat')
            ->get();

        $daftarMember = Pengguna::where('peran', 'Member')->orderBy('nama')->get();

        return view('operator.loket.create', compact('jadwalAktif', 'daftarMember'));
    }

    /**
     * Buat pesanan dari loket (walk-in) dengan pessimistic lock.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'jadwal_id'   => ['required', 'exists:jadwal,id'],
            'pengguna_id' => ['required', 'exists:pengguna,id'],
            'nomor_kursi' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $jadwal = Jadwal::with('kendaraan')->findOrFail($request->jadwal_id);

        try {
            $kode = DB::transaction(function () use ($request, $jadwal) {
                $sudahDipesan = $jadwal->pesanan()
                    ->lockForUpdate()
                    ->where('nomor_kursi', $request->nomor_kursi)
                    ->whereIn('status', [
                        StatusPesanan::BelumBayar,
                        StatusPesanan::DalamProses,
                        StatusPesanan::Lunas,
                    ])
                    ->exists();

                if ($sudahDipesan) {
                    throw new \RuntimeException('Kursi nomor ' . $request->nomor_kursi . ' sudah dipesan.');
                }

                do {
                    $kode = 'GTX-' . strtoupper(Str::random(6));
                } while (Pesanan::where('kode_pesanan', $kode)->exists());

                Pesanan::create([
                    'kode_pesanan'   => $kode,
                    'jadwal_id'      => $jadwal->id,
                    'pengguna_id'    => $request->pengguna_id,
                    'nomor_kursi'    => $request->nomor_kursi,
                    'harga_snapshot' => $jadwal->harga,
                    'status'         => StatusPesanan::Lunas, // Loket = langsung lunas
                    'batas_bayar'    => now()->addHours(1),
                ]);

                return $kode;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['nomor_kursi' => $e->getMessage()])->withInput();
        } catch (Throwable) {
            return back()->withErrors(['nomor_kursi' => 'Terjadi kesalahan. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('operator.loket.index')
            ->with('sukses', "Pesanan loket {$kode} berhasil dibuat dan langsung lunas.");
    }
}
