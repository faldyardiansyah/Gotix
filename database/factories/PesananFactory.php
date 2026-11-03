<?php

namespace Database\Factories;

use App\Enums\StatusPesanan;
use App\Models\Jadwal;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pesanan>
 */
class PesananFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory.
     *
     * @var class-string<Pesanan>
     */
    protected $model = Pesanan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
            'jadwal_id' => Jadwal::factory(),
            'pengguna_id' => Pengguna::factory(),
            'nomor_kursi' => fake()->numberBetween(1, 5),
            'harga_snapshot' => 60000,
            'status' => StatusPesanan::BelumBayar,
            'batas_bayar' => now()->addHours(24),
        ];
    }
}
