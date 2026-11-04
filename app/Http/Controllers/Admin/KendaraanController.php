<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    public function index(): View
    {
        $daftarKendaraan = Kendaraan::withCount('jadwal')
            ->orderBy('jenis_kendaraan')
            ->get();

        return view('admin.kendaraan.index', compact('daftarKendaraan'));
    }

    public function create(): View
    {
        return view('admin.kendaraan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_kendaraan' => ['required', 'string', 'max:100'],
            'nomor_polisi'    => ['required', 'string', 'max:20', 'unique:kendaraan,nomor_polisi'],
            'warna'           => ['required', 'string', 'max:50'],
            'kapasitas_kursi' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        Kendaraan::create($validated);

        return redirect()->route('admin.kendaraan.index')
            ->with('sukses', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        $kendaraan = Kendaraan::findOrFail($id);

        return view('admin.kendaraan.edit', compact('kendaraan'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $validated = $request->validate([
            'jenis_kendaraan' => ['required', 'string', 'max:100'],
            'nomor_polisi'    => ['required', 'string', 'max:20', 'unique:kendaraan,nomor_polisi,' . $id],
            'warna'           => ['required', 'string', 'max:50'],
            'kapasitas_kursi' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $kendaraan->update($validated);

        return redirect()->route('admin.kendaraan.index')
            ->with('sukses', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $kendaraan->delete();

        return redirect()->route('admin.kendaraan.index')
            ->with('sukses', 'Kendaraan berhasil dihapus.');
    }
}
