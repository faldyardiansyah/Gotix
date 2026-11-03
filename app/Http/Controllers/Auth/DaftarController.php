<?php

namespace App\Http\Controllers\Auth;

use App\Enums\JenisKelamin;
use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DaftarRequest;
use App\Models\Pengguna;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DaftarController extends Controller
{
    /**
     * Tampilkan halaman formulir pendaftaran.
     */
    public function create(): View
    {
        return view('auth.daftar');
    }

    /**
     * Proses pendaftaran member baru.
     */
    public function store(DaftarRequest $request): RedirectResponse
    {
        $pengguna = Pengguna::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email ?: null,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'jenis_kelamin' => JenisKelamin::from($request->jenis_kelamin),
            'peran' => PeranPengguna::Member,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($pengguna));

        Auth::login($pengguna);

        $request->session()->regenerate();

        return redirect()->route('member.tiket.index');
    }
}

