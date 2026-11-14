<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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

    public function create(): View
    {
        return view('admin.pengguna.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:pengguna,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:pengguna,email'],
            'telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:500'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'peran' => ['required', Rule::in([PeranPengguna::Operator->value, PeranPengguna::Member->value])],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        $pengguna = Pengguna::create([
            'nama' => $validated['nama'],
            'username' => strtolower(trim($validated['username'])),
            'email' => ! empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
            'telepon' => trim($validated['telepon']),
            'alamat' => trim($validated['alamat']),
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'peran' => $validated['peran'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.pengguna.index')
            ->with('sukses', "Pengguna baru '{$pengguna->nama}' ({$pengguna->peran->value}) berhasil ditambahkan.");
    }

    public function show(string $id): View
    {
        $pengguna = Pengguna::with(['pesanan.jadwal.kotaAsal', 'pesanan.jadwal.kotaTujuan'])->findOrFail($id);

        return view('admin.pengguna.show', compact('pengguna'));
    }
}
