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
            'nomor_polisi' => ['required', 'string', 'max:20', 'unique:kendaraan,nomor_polisi'],
            'warna' => ['required', 'string', 'max:50'],
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
            'nomor_polisi' => ['required', 'string', 'max:20', 'unique:kendaraan,nomor_polisi,'.$id],
            'warna' => ['required', 'string', 'max:50'],
            'kapasitas_kursi' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $kendaraan->update($validated);

        return redirect()->route('admin.kendaraan.index')
            ->with('sukses', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        // Cek apakah armada masih ditugaskan pada jadwal operasional masa depan
        $hasOperationalSchedule = $kendaraan->jadwal()
            ->where(function ($q) use ($today, $currentTime) {
                $q->whereDate('tanggal_berangkat', '>', $today)
                    ->orWhere(function ($sub) use ($today, $currentTime) {
                        $sub->whereDate('tanggal_berangkat', $today)
                            ->where('jam_berangkat', '>=', $currentTime);
                    });
            })
            ->exists();

        if ($hasOperationalSchedule) {
            return back()->withErrors([
                'error' => 'Kendaraan dengan nomor polisi '.$kendaraan->nomor_polisi.' tidak dapat dinonaktifkan karena masih ditugaskan pada jadwal keberangkatan aktif masa depan.',
            ]);
        }

        $kendaraan->delete();

        return redirect()->route('admin.kendaraan.index')
            ->with('sukses', 'Kendaraan '.$kendaraan->nomor_polisi.' berhasil dihapus.');
    }
}
