<?php

namespace App\Http\Controllers\Admin;

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
        $daftarKeluhan = Keluhan::with('pengguna')->latest()->get();

        return view('admin.keluhan.index', compact('daftarKeluhan'));
    }

    public function show(string $id): View
    {
        $keluhan = Keluhan::with('pengguna')->findOrFail($id);

        return view('admin.keluhan.show', compact('keluhan'));
    }

    public function balas(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'balasan' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $keluhan = Keluhan::findOrFail($id);
        $keluhan->update([
            'balasan' => $request->balasan,
            'dibalas_oleh' => Auth::id(),
            'dibalas_pada' => now(),
        ]);

        return redirect()->route('admin.keluhan.show', $id)
            ->with('sukses', 'Tanggapan/balasan keluhan berhasil dikirim ke member.');
    }
}
