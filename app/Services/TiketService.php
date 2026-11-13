<?php

namespace App\Services;

use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TiketService
{
    /**
     * Hitung tanda tangan digital (HMAC-SHA256) untuk verifikasi keaslian e-tiket.
     */
    public function generateSignature(Pesanan $pesanan): string
    {
        $payload = sprintf(
            '%s|%d|%d|%s',
            $pesanan->kode_pesanan,
            (int) $pesanan->jadwal_id,
            (int) $pesanan->nomor_kursi,
            $pesanan->status->value
        );

        return hash_hmac('sha256', $payload, (string) config('app.key'));
    }

    /**
     * Susun payload data resmi untuk boarding e-tiket tanpa mengekspos data sensitif.
     *
     * @return array<string, mixed>
     */
    public function generatePayload(Pesanan $pesanan): array
    {
        $namaPenumpang = $pesanan->nama_penumpang ?: ($pesanan->pengguna?->nama ?? 'Penumpang Walk-in');

        return [
            'kode' => $pesanan->kode_pesanan,
            'kursi' => (int) $pesanan->nomor_kursi,
            'jadwal_id' => (int) $pesanan->jadwal_id,
            'status' => $pesanan->status->value,
            'penumpang' => $namaPenumpang,
            'rute' => sprintf(
                '%s -> %s',
                $pesanan->jadwal?->kotaAsal?->nama_kota ?? 'Asal',
                $pesanan->jadwal?->kotaTujuan?->nama_kota ?? 'Tujuan'
            ),
            'waktu' => $pesanan->jadwal?->tanggal_berangkat
                ? sprintf(
                    '%s %s WIB',
                    $pesanan->jadwal->tanggal_berangkat->format('Y-m-d'),
                    substr((string) $pesanan->jadwal->jam_berangkat, 0, 5)
                )
                : null,
            'signature' => $this->generateSignature($pesanan),
        ];
    }

    /**
     * Verifikasi integritas payload e-tiket dari sisi server.
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifyPayload(array $payload): bool
    {
        if (empty($payload['kode']) || empty($payload['signature'])) {
            return false;
        }

        /** @var Pesanan|null $pesanan */
        $pesanan = Pesanan::where('kode_pesanan', $payload['kode'])->first();

        if (! $pesanan || ! $this->isTiketValid($pesanan)) {
            return false;
        }

        // Pastikan atribut payload kursi dan jadwal sesuai dengan record database
        if (isset($payload['kursi']) && (int) $payload['kursi'] !== (int) $pesanan->nomor_kursi) {
            return false;
        }

        if (isset($payload['jadwal_id']) && (int) $payload['jadwal_id'] !== (int) $pesanan->jadwal_id) {
            return false;
        }

        $expectedSignature = $this->generateSignature($pesanan);

        return hash_equals($expectedSignature, (string) $payload['signature']);
    }

    /**
     * Evaluasi apakah pesanan memenuhi kriteria kelayakan sebagai e-tiket resmi yang valid.
     * Tiket HANYA valid jika status pesanan adalah Lunas.
     */
    public function isTiketValid(Pesanan $pesanan): bool
    {
        return $pesanan->status === StatusPesanan::Lunas;
    }

    /**
     * Hasilkan SVG QR Code standar ISO/IEC 18004 menggunakan BaconQrCode.
     * QR Code memuat payload digital resmi dan tanda tangan kriptografis HMAC-SHA256.
     */
    public function generateQrSvg(Pesanan $pesanan, int $size = 200): string
    {
        $payload = $this->generatePayload($pesanan);
        $encodedData = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($encodedData);

        // Bersihkan XML declaration jika ada agar aman di-render inline di Blade
        $svg = preg_replace('/^<\?xml[^>]*\?>\s*/i', '', $svg);

        // Tambahkan styling responsif & aksesibilitas pada tag root SVG
        $ariaLabel = htmlspecialchars("QR Code Tiket {$pesanan->kode_pesanan}", ENT_QUOTES, 'UTF-8');

        return preg_replace(
            '/<svg\b([^>]*)>/i',
            '<svg$1 class="w-full h-full text-slate-900" role="img" aria-label="'.$ariaLabel.'">',
            $svg,
            1
        ) ?? $svg;
    }
}
