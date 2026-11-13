<?php

namespace App\Models;

use Database\Factories\KendaraanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    /** @use HasFactory<KendaraanFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel terkait dengan model.
     *
     * @var string
     */
    protected $table = 'kendaraan';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'jenis_kendaraan',
        'nomor_polisi',
        'warna',
        'kapasitas_kursi',
    ];

    /**
     * Konversi tipe data atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kapasitas_kursi' => 'integer',
        ];
    }

    /**
     * Relasi ke jadwal yang menggunakan kendaraan ini.
     *
     * @return HasMany<Jadwal, $this>
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'kendaraan_id');
    }
}
