<?php

namespace App\Http\Controllers\Auth;

use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MasukRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
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
    public function store(MasukRequest $request): Response|RedirectResponse|JsonResponse
    {
        $rawLogin = $request->string('login')->toString();
        $password = $request->string('password')->toString();
        $throttleKey = $this->throttleKey($rawLogin, $request);

        $maxAttempts = (int) config('gotix.auth.login_max_attempts', 5);
        $decaySeconds = (int) config('gotix.auth.login_decay_seconds', 60);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $pesan = "Terlalu banyak percobaan masuk yang gagal. Silakan coba lagi dalam {$seconds} detik.";

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $pesan,
                    'errors' => [
                        'login' => [$pesan],
                    ],
                ], 429)->header('Retry-After', (string) $seconds);
            }

            return response()->view('auth.masuk', [
                'rateLimitMessage' => $pesan,
                'errors' => new ViewErrorBag,
            ], 429)->header('Retry-After', (string) $seconds);
        }

        $login = trim($rawLogin);
        // Tentukan apakah input adalah email atau username
        $kolom = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $kredensial = [$kolom => $login, 'password' => $password];

        if (! Auth::attempt($kredensial, $request->boolean('ingat_saya'))) {
            RateLimiter::hit($throttleKey, $decaySeconds);

            return back()->withErrors([
                'login' => 'Kombinasi username/email dan kata sandi tidak cocok.',
            ])->onlyInput('login');
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        return $this->redirectBerdasarkanPeran();
    }

    /**
     * Dapatkan throttle key untuk rate limiting autentikasi.
     * Kombinasi identitas login yang dinormalisasi (lowercase & trim) dan alamat IP.
     */
    protected function throttleKey(string $login, MasukRequest $request): string
    {
        $normalized = Str::lower(trim($login));

        return 'login:'.$normalized.'|'.$request->ip();
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
