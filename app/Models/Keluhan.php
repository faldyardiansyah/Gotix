<?php

namespace App\Models;

use App\Enums\KategoriKeluhan;
use Database\Factories\KeluhanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keluhan extends Model
{
    /** @use HasFactory<KeluhanFactory> */
    use HasFactory;

    /**
     * Nama tabel terkait dengan model.
     *
     * @var string
     */
    protected $table = 'keluhan';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pengguna_id',
        'pesanan_id',
        'kategori',
        'deskripsi',
        'balasan',
        'dibalas_oleh',
        'dibalas_pada',
    ];

    /**
     * Konversi tipe data atribut.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'kategori' => KategoriKeluhan::class,
            'dibalas_pada' => 'datetime',
        ];
    }

    /**
     * Relasi ke pengguna yang mengajukan keluhan.
     *
     * @return BelongsTo<Pengguna, $this>
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id')->withTrashed();
    }

    /**
     * Relasi ke pesanan terkait keluhan jika ada.
     *
     * @return BelongsTo<Pesanan, $this>
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /**
     * Relasi ke admin/petugas yang membalas keluhan.
     *
     * @return BelongsTo<Pengguna, $this>
     */
    public function petugasBalas(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'dibalas_oleh')->withTrashed();
    }
}
