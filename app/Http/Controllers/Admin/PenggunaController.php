<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(): View
    {
        $daftarPengguna = Pengguna::withCount('pesanan')
            ->orderBy('nama')
            ->get();

        return view('admin.pengguna.index', compact('daftarPengguna'));
    }

    public function show(string $id): View
    {
        $pengguna = Pengguna::with(['pesanan.jadwal.kotaAsal', 'pesanan.jadwal.kotaTujuan'])->findOrFail($id);

        return view('admin.pengguna.show', compact('pengguna'));
    }
}
