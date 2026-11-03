<?php

namespace Database\Factories;

use App\Models\KonfirmasiPembayaran;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<KonfirmasiPembayaran>
 */
class KonfirmasiPembayaranFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory.
     *
     * @var class-string<KonfirmasiPembayaran>
     */
    protected $model = KonfirmasiPembayaran::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pesanan_id' => Pesanan::factory(),
            'nomor_resi' => 'TF-'.strtoupper(Str::random(10)),
            'rekening_pengirim' => fake()->bankAccountNumber().' a.n '.fake('id_ID')->name(),
            'tanggal_transfer' => now()->format('Y-m-d'),
            'jam_transfer' => now()->format('H:i:s'),
            'bukti_path' => 'bukti_transfer/contoh.jpg',
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
            'alasan_penolakan' => null,
        ];
    }
}
