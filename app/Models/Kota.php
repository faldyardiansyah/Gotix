<?php

namespace App\Models;

use Database\Factories\KotaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kota extends Model
{
    /** @use HasFactory<KotaFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel terkait dengan model.
     *
     * @var string
     */
    protected $table = 'kota';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kota',
    ];

    /**
     * Relasi ke jadwal dengan kota ini sebagai kota asal.
     *
     * @return HasMany<Jadwal, $this>
     */
    public function jadwalAsal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'kota_asal_id');
    }

    /**
     * Relasi ke jadwal dengan kota ini sebagai kota tujuan.
     *
     * @return HasMany<Jadwal, $this>
     */
    public function jadwalTujuan(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'kota_tujuan_id');
    }
}
