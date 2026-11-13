<?php

namespace App\Http\Controllers\Member;

use App\Enums\KategoriKeluhan;
use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KeluhanController extends Controller
{
    public function index(): View
    {
        /** @var Pengguna $user */
        $user = Auth::user();

        $daftarKeluhan = $user
            ->keluhan()
            ->with(['pesanan.jadwal.kotaAsal', 'pesanan.jadwal.kotaTujuan', 'petugasBalas'])
            ->latest()
            ->get();

        return view('member.keluhan.index', compact('daftarKeluhan'));
    }

    public function create(): View
    {
        $kategori = KategoriKeluhan::cases();

        /** @var Pengguna $user */
        $user = Auth::user();

        $daftarPesanan = $user
            ->pesanan()
            ->with(['jadwal.kotaAsal', 'jadwal.kotaTujuan'])
            ->latest()
            ->get();

        return view('member.keluhan.create', compact('kategori', 'daftarPesanan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $minDeskripsi = (int) config('gotix.keluhan.deskripsi_min', 10);
        $maxDeskripsi = (int) config('gotix.keluhan.deskripsi_max', 1000);

        $request->validate([
            'kategori' => ['required', Rule::enum(KategoriKeluhan::class)],
            'pesanan_id' => [
                'nullable',
                Rule::exists('pesanan', 'id')->where('pengguna_id', Auth::id()),
            ],
            'deskripsi' => ['required', 'string', "min:{$minDeskripsi}", "max:{$maxDeskripsi}"],
        ]);

        Keluhan::create([
            'pengguna_id' => Auth::id(),
            'pesanan_id' => $request->pesanan_id ?: null,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('member.keluhan.index')
            ->with('sukses', 'Keluhan berhasil dikirim. Terima kasih atas masukan Anda.');
    }
}
