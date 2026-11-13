<?php

namespace App\Http\Controllers\Operator;

use App\Enums\PeranPengguna;
use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $daftarMember = Pengguna::where('peran', PeranPengguna::Member)->orderBy('nama')->get();

        return view('operator.loket.index', compact('jadwalAktif', 'daftarMember'));
    }

    public function riwayat(Request $request): View
    {
        $riwayatQuery = Pesanan::with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna'])
            ->where('status', StatusPesanan::Lunas)
            ->latest();

        if ($request->filled('cari')) {
            $cari = trim($request->cari);
            $riwayatQuery->where(function ($q) use ($cari) {
                $q->where('kode_pesanan', 'like', "%{$cari}%")
                    ->orWhere('nama_penumpang', 'like', "%{$cari}%")
                    ->orWhere('telepon_penumpang', 'like', "%{$cari}%")
                    ->orWhereHas('pengguna', function ($qp) use ($cari) {
                        $qp->where('nama', 'like', "%{$cari}%")
                            ->orWhere('telepon', 'like', "%{$cari}%")
                            ->orWhere('username', 'like', "%{$cari}%");
                    })
                    ->orWhereHas('jadwal.kotaAsal', function ($qka) use ($cari) {
                        $qka->where('nama_kota', 'like', "%{$cari}%");
                    })
                    ->orWhereHas('jadwal.kotaTujuan', function ($qkt) use ($cari) {
                        $qkt->where('nama_kota', 'like', "%{$cari}%");
                    });
            });
        }

        if ($request->filled('tipe')) {
            if ($request->tipe === 'member') {
                $riwayatQuery->whereNotNull('pengguna_id');
            } elseif ($request->tipe === 'walkin') {
                $riwayatQuery->whereNull('pengguna_id');
            }
        }

        $riwayatPesanan = $riwayatQuery->paginate(12)->withQueryString();

        $totalLunas = Pesanan::where('status', StatusPesanan::Lunas)->count();
        $totalWalkin = Pesanan::where('status', StatusPesanan::Lunas)->whereNull('pengguna_id')->count();
        $totalMember = Pesanan::where('status', StatusPesanan::Lunas)->whereNotNull('pengguna_id')->count();

        return view('operator.riwayat.index', compact('riwayatPesanan', 'totalLunas', 'totalWalkin', 'totalMember'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('operator.loket.index');
    }

    /**
     * Buat pesanan dari loket (walk-in) dengan pessimistic lock dan penanganan konkurensi.
     */
    public function store(Request $request): RedirectResponse
    {
        $jadwal = Jadwal::with('kendaraan')->findOrFail($request->jadwal_id);
        $kapasitas = $jadwal->kendaraan?->kapasitas_kursi ?? (int) config('gotix.kapasitas_kursi', 5);

        $request->validate([
            'jadwal_id' => ['required', 'exists:jadwal,id'],
            'nomor_kursi' => ['required', 'integer', 'min:1', 'max:'.$kapasitas],
            'pengguna_id' => ['nullable', 'exists:pengguna,id'],
            'nama_penumpang' => ['required_without:pengguna_id', 'nullable', 'string', 'max:100'],
            'telepon_penumpang' => ['required_without:pengguna_id', 'nullable', 'string', 'max:20'],
        ]);

        $nomorKursi = (int) $request->nomor_kursi;
        $operatorId = Auth::id();

        try {
            $kode = DB::transaction(function () use ($request, $jadwal, $nomorKursi, $operatorId) {
                // Serialisasi reservasi pada jadwal ini dengan mengunci row Jadwal induk
                Jadwal::where('id', $jadwal->id)->lockForUpdate()->first();

                $sudahDipesan = $jadwal->pesanan()
                    ->where('nomor_kursi', $nomorKursi)
                    ->whereIn('status', [
                        StatusPesanan::BelumBayar,
                        StatusPesanan::DalamProses,
                        StatusPesanan::Lunas,
                    ])
                    ->exists();

                if ($sudahDipesan) {
                    throw new \RuntimeException('Kursi nomor '.$nomorKursi.' sudah dipesan.');
                }

                do {
                    $kode = 'GTX-'.strtoupper(Str::random(6));
                } while (Pesanan::where('kode_pesanan', $kode)->exists());

                $batasBayarLoketJam = (int) config('gotix.batas_bayar_loket_jam', 1);

                $penggunaId = $request->filled('pengguna_id') ? $request->pengguna_id : null;
                $namaPenumpang = $request->nama_penumpang;
                $teleponPenumpang = $request->telepon_penumpang;

                // Jika memilih akun member terdaftar, lengkapi nama & telepon dari profil member jika input kosong
                if ($penggunaId) {
                    $member = Pengguna::find($penggunaId);
                    if ($member) {
                        $namaPenumpang = $namaPenumpang ?: $member->nama;
                        $teleponPenumpang = $teleponPenumpang ?: $member->telepon;
                    }
                }

                Pesanan::create([
                    'kode_pesanan' => $kode,
                    'jadwal_id' => $jadwal->id,
                    'pengguna_id' => $penggunaId,
                    'nama_penumpang' => $namaPenumpang,
                    'telepon_penumpang' => $teleponPenumpang,
                    'dibuat_oleh_operator_id' => $operatorId,
                    'nomor_kursi' => $nomorKursi,
                    'harga_snapshot' => $jadwal->harga,
                    'status' => StatusPesanan::Lunas, // Loket = langsung lunas
                    'batas_bayar' => now()->addHours($batasBayarLoketJam),
                ]);

                return $kode;
            });
        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062)) {
                return back()->withErrors(['nomor_kursi' => 'Kursi nomor '.$nomorKursi.' baru saja dipesan oleh pengguna lain. Silakan pilih kursi lain.'])->withInput();
            }

            Log::error('Database error saat membuat pesanan loket: '.$e->getMessage(), [
                'jadwal_id' => $jadwal->id,
                'nomor_kursi' => $nomorKursi,
            ]);

            return back()->withErrors(['nomor_kursi' => 'Terjadi kesalahan sistem saat memproses pemesanan loket. Silakan coba lagi.'])->withInput();
        } catch (\RuntimeException $e) {
            return back()->withErrors(['nomor_kursi' => $e->getMessage()])->withInput();
        } catch (Throwable $e) {
            Log::error('Unexpected error saat membuat pesanan loket: '.$e->getMessage(), [
                'jadwal_id' => $jadwal->id,
                'nomor_kursi' => $nomorKursi,
            ]);

            return back()->withErrors(['nomor_kursi' => 'Terjadi kesalahan. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('operator.loket.index')
            ->with('sukses', "Pesanan loket {$kode} berhasil dibuat dan langsung lunas.");
    }
}
