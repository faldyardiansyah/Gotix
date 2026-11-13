<?php

namespace App\Http\Controllers\Auth;

use App\Enums\JenisKelamin;
use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DaftarRequest;
use App\Models\Pengguna;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ViewErrorBag;
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
    public function store(DaftarRequest $request): Response|RedirectResponse|JsonResponse
    {
        $throttleKey = 'register:'.$request->ip();
        $maxAttempts = (int) config('gotix.auth.register_max_attempts', 5);
        $decaySeconds = (int) config('gotix.auth.register_decay_seconds', 60);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $pesan = "Terlalu banyak percobaan pendaftaran. Silakan coba lagi dalam {$seconds} detik.";

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $pesan,
                    'errors' => [
                        'nama' => [$pesan],
                    ],
                ], 429)->header('Retry-After', (string) $seconds);
            }

            return response()->view('auth.daftar', [
                'rateLimitMessage' => $pesan,
                'errors' => new ViewErrorBag,
            ], 429)->header('Retry-After', (string) $seconds);
        }

        RateLimiter::hit($throttleKey, $decaySeconds);

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
