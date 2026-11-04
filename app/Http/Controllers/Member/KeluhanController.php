<?php

namespace App\Http\Controllers\Member;

use App\Enums\KategoriKeluhan;
use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KeluhanController extends Controller
{
    public function index(): View
    {
        $daftarKeluhan = Auth::user()
            ?->keluhan()
            ->latest()
            ->get() ?? collect();

        return view('member.keluhan.index', compact('daftarKeluhan'));
    }

    public function create(): View
    {
        $kategori = KategoriKeluhan::cases();

        return view('member.keluhan.create', compact('kategori'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kategori'    => ['required'],
            'deskripsi'   => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        Keluhan::create([
            'pengguna_id' => Auth::id(),
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
        ]);

        return redirect()->route('member.keluhan.index')
            ->with('sukses', 'Keluhan berhasil dikirim. Terima kasih atas masukan Anda.');
    }
}
