<?php

namespace App\Http\Controllers\Auth;

use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MasukRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MasukController extends Controller
{
    /**
     * Tampilkan halaman formulir masuk.
     */
    public function create(): View
    {
        return view('auth.masuk');
    }

    /**
     * Proses autentikasi pengguna (username atau email).
     */
    public function store(MasukRequest $request): RedirectResponse
    {
        $login = $request->string('login')->toString();
        $password = $request->string('password')->toString();

        // Tentukan apakah input adalah email atau username
        $kolom = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $kredensial = [$kolom => $login, 'password' => $password];

        if (! Auth::attempt($kredensial, $request->boolean('ingat_saya'))) {
            return back()->withErrors([
                'login' => 'Kombinasi username/email dan kata sandi tidak cocok.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

        return $this->redirectBerdasarkanPeran();
    }

    /**
     * Arahkan pengguna sesuai perannya setelah berhasil masuk.
     */
    private function redirectBerdasarkanPeran(): RedirectResponse
    {
        $peran = Auth::user()->peran; // @phpstan-ignore-line

        return match ($peran) {
            PeranPengguna::Admin => redirect()->route('admin.dasbor'),
            PeranPengguna::Operator => redirect()->route('operator.pembayaran.index'),
            default => redirect()->route('member.tiket.index'),
        };
    }
}
