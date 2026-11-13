<?php

use App\Enums\KategoriKeluhan;
use App\Enums\PeranPengguna;
use App\Enums\StatusPesanan;
use App\Models\Jadwal;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| A. PENGUJIAN INTEGRITAS FILE KONFIGURASI
|--------------------------------------------------------------------------
*/

test('1. file konfigurasi gotix berhasil dimuat dengan seluruh key terstruktur', function () {
    $config = config('gotix');

    expect($config)->toBeArray();
    expect($config)->toHaveKeys([
        'batas_bayar_jam',
        'batas_bayar_loket_jam',
        'kapasitas_kursi',
        'bank',
        'pembayaran',
        'keluhan',
        'auth',
    ]);
});

test('2. key konfigurasi memiliki nilai dan tipe data yang benar', function () {
    expect(config('gotix.batas_bayar_jam'))->toBeInt()->toBeGreaterThan(0);
    expect(config('gotix.batas_bayar_loket_jam'))->toBeInt()->toBeGreaterThan(0);
    expect(config('gotix.kapasitas_kursi'))->toBeInt()->toBeGreaterThan(0);

    // Bank
    expect(config('gotix.bank.nama'))->toBeString()->not->toBeEmpty();
    expect(config('gotix.bank.nomor'))->toBeString()->not->toBeEmpty();
    expect(config('gotix.bank.atas_nama'))->toBeString()->not->toBeEmpty();

    // Pembayaran
    expect(config('gotix.pembayaran.max_file_kb'))->toBeInt()->toBe(5120);
    expect(config('gotix.pembayaran.mimes'))->toBeArray()->toContain('jpg', 'jpeg', 'png', 'webp', 'pdf');

    // Keluhan
    expect(config('gotix.keluhan.deskripsi_min'))->toBeInt()->toBe(10);
    expect(config('gotix.keluhan.deskripsi_max'))->toBeInt()->toBe(1000);
    expect(config('gotix.keluhan.balasan_min'))->toBeInt()->toBe(3);
    expect(config('gotix.keluhan.balasan_max'))->toBeInt()->toBe(1000);

    // Auth rate limit
    expect(config('gotix.auth.login_max_attempts'))->toBeInt()->toBe(5);
    expect(config('gotix.auth.login_decay_seconds'))->toBeInt()->toBe(60);
    expect(config('gotix.auth.register_max_attempts'))->toBeInt()->toBe(5);
    expect(config('gotix.auth.register_decay_seconds'))->toBeInt()->toBe(60);
});

/*
|--------------------------------------------------------------------------
| B. PENGUJIAN KONSISTENSI PENGGUNAAN KONFIGURASI DI APLIKASI
|--------------------------------------------------------------------------
*/

test('3. pemesanan tiket member membaca konfigurasi batas bayar dan kapasitas kursi', function () {
    /** @var TestCase $this */
    Carbon::setTestNow(now());

    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDays(2)->toDateString(),
    ]);

    // Override konfigurasi batas bayar menjadi 12 jam
    config(['gotix.batas_bayar_jam' => 12]);

    $response = $this->actingAs($member)->post(route('member.pesanan.store', $jadwal->id), [
        'nomor_kursi' => 1,
    ]);

    $response->assertSessionHasNoErrors();
    $pesanan = Pesanan::where('jadwal_id', $jadwal->id)->where('nomor_kursi', 1)->firstOrFail();

    // Batas bayar harus persis 12 jam dari sekarang
    expect($pesanan->batas_bayar->timestamp)->toBe(now()->addHours(12)->timestamp);

    Carbon::setTestNow();
});

test('4. pemesanan tiket loket membaca konfigurasi batas bayar loket dan kapasitas kursi', function () {
    /** @var TestCase $this */
    Carbon::setTestNow(now());

    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $customer = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDays(2)->toDateString(),
    ]);

    // Override konfigurasi batas bayar loket menjadi 3 jam
    config(['gotix.batas_bayar_loket_jam' => 3]);

    $response = $this->actingAs($operator)->post(route('operator.loket.store'), [
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $customer->id,
        'nomor_kursi' => 2,
    ]);

    $response->assertSessionHasNoErrors();
    $pesanan = Pesanan::where('jadwal_id', $jadwal->id)->where('nomor_kursi', 2)->firstOrFail();

    // Batas bayar loket harus persis 3 jam dari sekarang
    expect($pesanan->batas_bayar->timestamp)->toBe(now()->addHours(3)->timestamp);

    Carbon::setTestNow();
});

test('5. unggah bukti transfer mematuhi batasan max_file_kb dan mimes terkonfigurasi', function () {
    /** @var TestCase $this */
    Storage::fake('local');

    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(2),
    ]);

    // Ubah batasan upload via config: max 500 KB
    config(['gotix.pembayaran.max_file_kb' => 500]);

    // File 600 KB harus ditolak validasi
    $fileBesar = UploadedFile::fake()->create('bukti_besar.jpg', 600);

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'bukti_transfer' => $fileBesar,
        'rekening_pengirim' => 'BCA - 1234567890',
        'nomor_resi' => 'TF-CONFIG001',
    ]);

    $response->assertSessionHasErrors('bukti_transfer');

    // File 300 KB harus diterima
    $fileSesuai = UploadedFile::fake()->create('bukti_pas.jpg', 300);

    $responseSukses = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'bukti_transfer' => $fileSesuai,
        'rekening_pengirim' => 'BCA - 1234567890',
        'nomor_resi' => 'TF-CONFIG002',
    ]);

    $responseSukses->assertSessionHasNoErrors();
});

test('6. validasi keluhan member mematuhi batas karakter min dan max terkonfigurasi', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);

    // Konfigurasi min 15 dan max 50 karakter
    config([
        'gotix.keluhan.deskripsi_min' => 15,
        'gotix.keluhan.deskripsi_max' => 50,
    ]);

    // Terlalu pendek (< 15 karakter)
    $respPendek = $this->actingAs($member)->post(route('member.keluhan.store'), [
        'kategori' => KategoriKeluhan::Pelayanan->value,
        'deskripsi' => 'Pendek',
    ]);
    $respPendek->assertSessionHasErrors('deskripsi');

    // Terlalu panjang (> 50 karakter)
    $respPanjang = $this->actingAs($member)->post(route('member.keluhan.store'), [
        'kategori' => KategoriKeluhan::Pelayanan->value,
        'deskripsi' => str_repeat('A', 55),
    ]);
    $respPanjang->assertSessionHasErrors('deskripsi');

    // Sesuai batas (antara 15 dan 50 karakter)
    $respValid = $this->actingAs($member)->post(route('member.keluhan.store'), [
        'kategori' => KategoriKeluhan::Pelayanan->value,
        'deskripsi' => 'Pelayanan di loket cukup ramah dan cepat.',
    ]);
    $respValid->assertSessionHasNoErrors();
});

test('7. perubahan dinamis pada batas rate limit autentikasi memengaruhi threshold controller', function () {
    /** @var TestCase $this */
    RateLimiter::clear('login:usertestdinamis|127.0.0.1');

    $user = Pengguna::factory()->create([
        'username' => 'usertestdinamis',
        'password' => bcrypt('password123'),
        'peran' => PeranPengguna::Member,
    ]);

    // Ubah konfigurasi menjadi hanya 2 percobaan gagal
    config(['gotix.auth.login_max_attempts' => 2]);

    // Percobaan 1 gagal
    $this->post(route('masuk.store'), [
        'login' => 'usertestdinamis',
        'password' => 'passwordsalah',
    ]);

    // Percobaan 2 gagal
    $this->post(route('masuk.store'), [
        'login' => 'usertestdinamis',
        'password' => 'passwordsalah',
    ]);

    // Percobaan 3 harus langsung ditolak dengan HTTP 429
    $resp = $this->post(route('masuk.store'), [
        'login' => 'usertestdinamis',
        'password' => 'passwordsalah',
    ]);

    $resp->assertStatus(429);
});
