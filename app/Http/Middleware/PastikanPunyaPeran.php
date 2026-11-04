<?php

namespace App\Http\Middleware;

use App\Enums\PeranPengguna;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PastikanPunyaPeran
{
    /**
     * Handle an incoming request.
     *
     * Penggunaan: ->middleware('peran:Admin') atau ->middleware('peran:Admin,Operator')
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        if (! $request->user()) {
            return redirect()->route('masuk');
        }

        $peranPengguna = $request->user()->peran; // @phpstan-ignore-line

        foreach ($peran as $p) {
            try {
                $enum = PeranPengguna::from($p);
            } catch (\ValueError) {
                abort(403, 'Peran tidak dikenal.');
            }

            if ($peranPengguna === $enum) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
