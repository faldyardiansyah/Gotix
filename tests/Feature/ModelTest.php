<?php

// Pest test untuk relasi Model dan constraint kursi ganda
// Tahap 1 — relasi Eloquent & unique index

use App\Enums\PeranPengguna;
use App\Enums\StatusPesanan;
use App\Models\Jadwal;
use App\Models\Keluhan;
use App\Models\Kendaraan;
use App\Models\KonfirmasiPembayaran;
use App\Models\Kota;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

// ─────────────────────────────────────────────────────────────────
// Test Relasi Model
// ─────────────────────────────────────────────────────────────────

test('jadwal memiliki relasi kotaAsal, kotaTujuan, kendaraan', function () {
    $jadwal = Jadwal::factory()->create();

    expect($jadwal->kotaAsal)->toBeInstanceOf(Kota::class);
    expect($jadwal->kotaTujuan)->toBeInstanceOf(Kota::class);
    expect($jadwal->kendaraan)->toBeInstanceOf(Kendaraan::class);
});

test('kota memiliki relasi jadwalAsal dan jadwalTujuan', function () {
    $jadwal = Jadwal::factory()->create();

    $kotaAsal = $jadwal->kotaAsal;
    $kotaTujuan = $jadwal->kotaTujuan;

    expect($kotaAsal->jadwalAsal)->toHaveCount(1);
    expect($kotaTujuan->jadwalTujuan)->toHaveCount(1);
});

test('kendaraan memiliki relasi jadwal', function () {
    $kendaraan = Kendaraan::factory()->create();
    Jadwal::factory()->count(3)->create(['kendaraan_id' => $kendaraan->id]);

    expect($kendaraan->refresh()->jadwal)->toHaveCount(3);
});

test('pesanan memiliki relasi jadwal dan pengguna', function () {
    $pesanan = Pesanan::factory()->create();

    expect($pesanan->jadwal)->toBeInstanceOf(Jadwal::class);
    expect($pesanan->pengguna)->toBeInstanceOf(Pengguna::class);
});

test('pengguna memiliki relasi pesanan dan keluhan', function () {
    $pengguna = Pengguna::factory()->create();
    Pesanan::factory()->count(2)->create(['pengguna_id' => $pengguna->id]);
    Keluhan::factory()->count(1)->create(['pengguna_id' => $pengguna->id]);

    expect($pengguna->refresh()->pesanan)->toHaveCount(2);
    expect($pengguna->refresh()->keluhan)->toHaveCount(1);
});

test('konfirmasi pembayaran berelasi ke pesanan dan verifikator', function () {
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'diverifikasi_oleh' => $operator->id,
    ]);

    expect($konfirmasi->pesanan)->toBeInstanceOf(Pesanan::class);
    expect($konfirmasi->verifikator)->toBeInstanceOf(Pengguna::class);
    expect($konfirmasi->verifikator->id)->toBe($operator->id);
});

test('pesanan memiliki relasi konfirmasiPembayaran (hasOne)', function () {
    $pesanan = Pesanan::factory()->create();
    KonfirmasiPembayaran::factory()->create(['pesanan_id' => $pesanan->id]);

    expect($pesanan->refresh()->konfirmasiPembayaran)->toBeInstanceOf(KonfirmasiPembayaran::class);
});

// ─────────────────────────────────────────────────────────────────
// Test Constraint Kursi Tidak Boleh Ganda
// ─────────────────────────────────────────────────────────────────

test('tidak bisa memesan kursi yang sama pada jadwal yang sama', function () {
    $jadwal = Jadwal::factory()->create();
    $member1 = Pengguna::factory()->create();
    $member2 = Pengguna::factory()->create();

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member1->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => 60000,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Kursi 1 di jadwal yang sama — harus melempar exception
    expect(fn () => Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member2->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => 60000,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]))->toThrow(QueryException::class);
});

test('bisa memesan kursi berbeda pada jadwal yang sama', function () {
    $jadwal = Jadwal::factory()->create();
    $member1 = Pengguna::factory()->create();
    $member2 = Pengguna::factory()->create();

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member1->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => 60000,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member2->id,
        'nomor_kursi' => 2,
        'harga_snapshot' => 60000,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    expect(Pesanan::where('jadwal_id', $jadwal->id)->count())->toBe(2);
});

test('kursi yang sama boleh dipesan di jadwal berbeda', function () {
    $jadwal1 = Jadwal::factory()->create();
    $jadwal2 = Jadwal::factory()->create();
    $member = Pengguna::factory()->create();

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal1->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 3,
        'harga_snapshot' => 60000,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal2->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 3,
        'harga_snapshot' => 60000,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    expect(Pesanan::where('nomor_kursi', 3)->count())->toBe(2);
});

// ─────────────────────────────────────────────────────────────────
// Test Enum Cast
// ─────────────────────────────────────────────────────────────────

test('peran pengguna di-cast sebagai PeranPengguna enum', function () {
    $pengguna = Pengguna::factory()->create(['peran' => PeranPengguna::Admin]);
    expect($pengguna->fresh()->peran)->toBe(PeranPengguna::Admin);
});

test('status pesanan di-cast sebagai StatusPesanan enum', function () {
    $pesanan = Pesanan::factory()->create(['status' => StatusPesanan::Lunas]);
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::Lunas);
});

test('password pengguna tersimpan sebagai hash', function () {
    $pengguna = Pengguna::factory()->create(['password' => Hash::make('rahasia123')]);
    expect($pengguna->password)->not->toBe('rahasia123');
    expect(Hash::check('rahasia123', $pengguna->password))->toBeTrue();
});

test('pengguna memiliki helper isAdmin, isOperator, isMember', function () {
    $admin = Pengguna::factory()->create(['peran' => PeranPengguna::Admin]);
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);

    expect($admin->isAdmin())->toBeTrue();
    expect($admin->isOperator())->toBeFalse();
    expect($operator->isOperator())->toBeTrue();
    expect($member->isMember())->toBeTrue();
    expect($member->isAdmin())->toBeFalse();
});
