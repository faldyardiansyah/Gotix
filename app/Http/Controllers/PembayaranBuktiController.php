<?php

namespace App\Http\Controllers;

use App\Enums\PeranPengguna;
use App\Models\KonfirmasiPembayaran;
use App\Models\RiwayatPembayaran;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PembayaranBuktiController extends Controller
{
    /**
     * Stream payment proof file securely with RBAC and security headers.
     */
    public function show(Request $request, KonfirmasiPembayaran $konfirmasi): Response
    {
        $user = $request->user();

        // RBAC Authorization check
        $isAuthorized = false;

        if ($user->peran === PeranPengguna::Admin || $user->peran === PeranPengguna::Operator) {
            $isAuthorized = true;
        } elseif ($user->peran === PeranPengguna::Member) {
            // Member may only view proofs for their own bookings
            $konfirmasi->loadMissing('pesanan');
            if ($konfirmasi->pesanan && $konfirmasi->pesanan->pengguna_id === $user->id) {
                $isAuthorized = true;
            }
        }

        if (! $isAuthorized) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti transfer ini.');
        }

        $path = $konfirmasi->bukti_path;

        if (! $path) {
            abort(404, 'Bukti transfer belum diunggah.');
        }

        return $this->streamFile($path);
    }

    /**
     * Stream historical payment proof file securely with RBAC and security headers.
     */
    public function showRiwayat(Request $request, RiwayatPembayaran $riwayat): Response
    {
        $user = $request->user();

        // RBAC Authorization check
        $isAuthorized = false;

        if ($user->peran === PeranPengguna::Admin || $user->peran === PeranPengguna::Operator) {
            $isAuthorized = true;
        } elseif ($user->peran === PeranPengguna::Member) {
            $riwayat->loadMissing('pesanan');
            if ($riwayat->pesanan && $riwayat->pesanan->pengguna_id === $user->id) {
                $isAuthorized = true;
            }
        }

        if (! $isAuthorized) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti transfer ini.');
        }

        $path = $riwayat->bukti_path;

        if (! $path) {
            abort(404, 'Bukti transfer tidak ditemukan.');
        }

        return $this->streamFile($path);
    }

    /**
     * Internal method to securely stream file from local/public disk with defenses.
     */
    private function streamFile(string $path): Response
    {
        // Path traversal defense: disallow directory traversal sequences, drive letters, and root prefixes
        if (str_contains($path, '..') || str_contains($path, ':') || str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            abort(403, 'Akses file tidak valid.');
        }

        // Check local (private) disk first, then fallback to public disk for legacy files
        $disk = null;
        if (Storage::disk('local')->exists($path)) {
            $disk = 'local';
        } elseif (Storage::disk('public')->exists($path)) {
            $disk = 'public';
        }

        if (! $disk) {
            abort(404, 'File bukti transfer tidak ditemukan di penyimpanan.');
        }

        $fullPath = Storage::disk($disk)->path($path);
        $realFullPath = realpath($fullPath);
        $diskRoot = realpath(Storage::disk($disk)->path(''));

        // Ensure real path and disk root are valid strings
        if (! $realFullPath || ! $diskRoot) {
            abort(404, 'File bukti transfer tidak dapat ditemukan.');
        }

        $diskRootWithSeparator = rtrim($diskRoot, '\\/').DIRECTORY_SEPARATOR;

        // Ensure real path stays strictly inside disk root boundary
        if (! str_starts_with($realFullPath, $diskRootWithSeparator)) {
            abort(403, 'Akses file di luar direktori yang diizinkan.');
        }

        /** @var FilesystemAdapter $storageDisk */
        $storageDisk = Storage::disk($disk);
        $mimeType = $storageDisk->mimeType($path) ?: 'application/octet-stream';
        $filename = basename($path);

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $response = response()->file($realFullPath, $headers);
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-store', true);
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
