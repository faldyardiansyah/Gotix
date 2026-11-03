<?php

namespace App\Models;

use Database\Factories\JadwalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jadwal extends Model
{
    /** @use HasFactory<JadwalFactory> */
    use HasFactory;

    /**
     * Nama tabel terkait dengan model.
     *
     * @var string
     */
    protected $table = 'jadwal';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kota_asal_id',
        'kota_tujuan_id',
        'kendaraan_id',
        'harga',
        'tanggal_berangkat',
        'jam_berangkat',
    ];

    /**
     * Konversi tipe data atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'tanggal_berangkat' => 'date',
        ];
    }

    /**
     * Relasi ke kota asal jadwal.
     *
     * @return BelongsTo<Kota, $this>
     */
    public function kotaAsal(): BelongsTo
    {
        return $this->belongsTo(Kota::class, 'kota_asal_id');
    }

    /**
     * Relasi ke kota tujuan jadwal.
     *
     * @return BelongsTo<Kota, $this>
     */
    public function kotaTujuan(): BelongsTo
    {
        return $this->belongsTo(Kota::class, 'kota_tujuan_id');
    }

    /**
     * Relasi ke armada kendaraan jadwal.
     *
     * @return BelongsTo<Kendaraan, $this>
     */
    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    /**
     * Relasi ke semua pesanan pada jadwal ini.
     *
     * @return HasMany<Pesanan, $this>
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'jadwal_id');
    }
}
