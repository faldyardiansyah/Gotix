<?php

namespace App\Http\Controllers\Member;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PesananController extends Controller
{
    public function index(): View
    {
        /** @var Pengguna $user */
        $user = Auth::user();

        $daftarPesanan = $user
            ->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan'])
            ->latest()
            ->get();

        return view('member.pesanan.index', compact('daftarPesanan'));
    }

    public function show(string $kode): View
    {
        /** @var Pengguna $user */
        $user = Auth::user();

        $pesanan = $user
            ->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'konfirmasiPembayaran', 'riwayatPembayaran.verifikator'])
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
     * Simpan pesanan baru dengan pessimistic locking dan penanganan konkurensi.
     */
    public function store(Request $request, Jadwal $jadwal): RedirectResponse
    {
        // Pastikan jadwal keberangkatan belum lewat
        if ($jadwal->tanggal_berangkat && Carbon::parse($jadwal->tanggal_berangkat)->lt(today())) {
            return back()->withErrors(['nomor_kursi' => 'Jadwal perjalanan ini sudah lewat dan tidak dapat dipesan.'])->withInput();
        }

        $kapasitas = $jadwal->kendaraan?->kapasitas_kursi ?? (int) config('gotix.kapasitas_kursi', 5);

        $request->validate([
            'nomor_kursi' => ['required', 'integer', 'min:1', 'max:'.$kapasitas],
        ]);

        $nomorKursi = (int) $request->nomor_kursi;

        try {
            $kode = DB::transaction(function () use ($jadwal, $nomorKursi) {
                // Serialisasi reservasi pada jadwal ini dengan mengunci row Jadwal induk
                Jadwal::where('id', $jadwal->id)->lockForUpdate()->first();

                // Cek apakah kursi masih kosong (hanya status aktif: Belum Bayar, Dalam Proses, Lunas)
                $sudahDipesan = $jadwal->pesanan()
                    ->where('nomor_kursi', $nomorKursi)
                    ->whereIn('status', [
                        StatusPesanan::BelumBayar,
                        StatusPesanan::DalamProses,
                        StatusPesanan::Lunas,
                    ])
                    ->exists();

                if ($sudahDipesan) {
                    throw new \RuntimeException('Kursi nomor '.$nomorKursi.' sudah dipesan oleh penumpang lain. Silakan pilih kursi lain.');
                }

                // Buat kode unik GTX-XXXXXX
                do {
                    $kode = 'GTX-'.strtoupper(Str::random(6));
                } while (Pesanan::where('kode_pesanan', $kode)->exists());

                $durasiBatasBayar = (int) config('gotix.batas_bayar_jam', 24);

                Pesanan::create([
                    'kode_pesanan' => $kode,
                    'jadwal_id' => $jadwal->id,
                    'pengguna_id' => Auth::id(),
                    'nomor_kursi' => $nomorKursi,
                    'harga_snapshot' => $jadwal->harga,
                    'status' => StatusPesanan::BelumBayar,
                    'batas_bayar' => now()->addHours($durasiBatasBayar),
                ]);

                return $kode;
            });
        } catch (QueryException $e) {
            // Tangani bentrok unique index database saat race condition
            if ($e->getCode() == 23000 || (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062)) {
                return back()->withErrors(['nomor_kursi' => 'Kursi yang Anda pilih baru saja dipesan oleh pengguna lain. Silakan pilih kursi lain.'])->withInput();
            }

            Log::error('Database error saat membuat pesanan member: '.$e->getMessage(), [
                'jadwal_id' => $jadwal->id,
                'nomor_kursi' => $nomorKursi,
                'user_id' => Auth::id(),
            ]);

            return back()->withErrors(['nomor_kursi' => 'Terjadi kesalahan sistem saat memproses pemesanan. Silakan coba lagi.'])->withInput();
        } catch (\RuntimeException $e) {
            return back()->withErrors(['nomor_kursi' => $e->getMessage()])->withInput();
        } catch (Throwable $e) {
            Log::error('Unexpected error saat membuat pesanan member: '.$e->getMessage(), [
                'jadwal_id' => $jadwal->id,
                'nomor_kursi' => $nomorKursi,
                'user_id' => Auth::id(),
            ]);

            return back()->withErrors(['nomor_kursi' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('member.pesanan.show', $kode)
            ->with('sukses', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran sebelum batas waktu.');
    }
}
