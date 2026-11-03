<?php

namespace Database\Factories;

use App\Models\Jadwal;
use App\Models\Kendaraan;
use App\Models\Kota;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jadwal>
 */
class JadwalFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory.
     *
     * @var class-string<Jadwal>
     */
    protected $model = Jadwal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kota_asal_id' => Kota::factory(),
            'kota_tujuan_id' => Kota::factory(),
            'kendaraan_id' => Kendaraan::factory(),
            'harga' => fake()->randomElement([50000, 60000, 70000, 80000]),
            'tanggal_berangkat' => fake()->dateTimeBetween('+1 days', '+14 days')->format('Y-m-d'),
            'jam_berangkat' => fake()->randomElement(['08:00:00', '10:00:00', '13:00:00', '16:00:00', '19:00:00']),
        ];
    }
}
