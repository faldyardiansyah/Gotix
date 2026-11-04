<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KotaController extends Controller
{
    public function index(): View
    {
        $daftarKota = Kota::withCount(['jadwalAsal', 'jadwalTujuan'])
            ->orderBy('nama_kota')
            ->get();

        return view('admin.kota.index', compact('daftarKota'));
    }

    public function create(): View
    {
        return view('admin.kota.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kota' => ['required', 'string', 'max:100', 'unique:kota,nama_kota'],
        ]);

        Kota::create($validated);

        return redirect()->route('admin.kota.index')
            ->with('sukses', 'Kota berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        $kota = Kota::findOrFail($id);

        return view('admin.kota.edit', compact('kota'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $kota = Kota::findOrFail($id);

        $validated = $request->validate([
            'nama_kota' => ['required', 'string', 'max:100', 'unique:kota,nama_kota,' . $id],
        ]);

        $kota->update($validated);

        return redirect()->route('admin.kota.index')
            ->with('sukses', 'Kota berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $kota = Kota::findOrFail($id);
        $kota->delete();

        return redirect()->route('admin.kota.index')
            ->with('sukses', 'Kota berhasil dihapus.');
    }
}
