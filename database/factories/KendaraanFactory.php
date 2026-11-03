<?php

namespace Database\Factories;

use App\Models\Kendaraan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kendaraan>
 */
class KendaraanFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory.
     *
     * @var class-string<Kendaraan>
     */
    protected $model = Kendaraan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenis_kendaraan' => fake()->randomElement(['Toyota Avanza', 'Daihatsu Xenia', 'Toyota Innova']),
            'nomor_polisi' => 'BE '.fake()->unique()->numberBetween(1000, 9999).' '.fake()->lexify('??'),
            'warna' => fake()->randomElement(['Hitam', 'Putih', 'Silver', 'Abu-abu', 'Biru']),
            'kapasitas_kursi' => 5,
        ];
    }
}
