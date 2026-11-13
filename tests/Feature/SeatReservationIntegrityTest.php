<?php

use App\Enums\PeranPengguna;
use App\Enums\StatusPesanan;
use App\Models\Jadwal;
use App\Models\Kendaraan;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(RefreshDatabase::class);

// ─────────────────────────────────────────────────────────────────
// A. Normal Booking
// ─────────────────────────────────────────────────────────────────

test('user dapat memesan kursi yang tersedia dan kursi_aktif terisi di database', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $kendaraan = Kendaraan::factory()->create(['kapasitas_kursi' => 5]);
    $jadwal = Jadwal::factory()->create([
        'kendaraan_id' => $kendaraan->id,
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    $response = $this->actingAs($member)
        ->post(route('member.pesanan.store', $jadwal), [
            'nomor_kursi' => 2,
        ]);

    $pesanan = Pesanan::where('jadwal_id', $jadwal->id)
        ->where('nomor_kursi', 2)
        ->first();

    expect($pesanan)->not->toBeNull();
    expect($pesanan->status)->toBe(StatusPesanan::BelumBayar);
    expect($pesanan->pengguna_id)->toBe($member->id);

    // Cek nilai raw kursi_aktif dari database
    $rawPesanan = DB::table('pesanan')->where('id', $pesanan->id)->first();
    expect((int) $rawPesanan->kursi_aktif)->toBe(2);

    $response->assertRedirect(route('member.pesanan.show', $pesanan->kode_pesanan));
    $response->assertSessionHas('sukses');
});

// ─────────────────────────────────────────────────────────────────
// B. Cancellation
// ─────────────────────────────────────────────────────────────────

test('membatalkan pesanan mengubah kursi_aktif menjadi NULL dan membebaskan kursi', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $kendaraan = Kendaraan::factory()->create(['kapasitas_kursi' => 5]);
    $jadwal = Jadwal::factory()->create([
        'kendaraan_id' => $kendaraan->id,
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    $pesanan = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 3,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    $rawBefore = DB::table('pesanan')->where('id', $pesanan->id)->first();
    expect((int) $rawBefore->kursi_aktif)->toBe(3);

    // Batalkan pesanan
    $pesanan->update(['status' => StatusPesanan::Dibatalkan]);

    $rawAfter = DB::table('pesanan')->where('id', $pesanan->id)->first();
    expect($rawAfter->kursi_aktif)->toBeNull();

    // Verifikasi kursi terbebas di halaman create
    $response = $this->actingAs($member)->get(route('member.pesanan.create', $jadwal));
    $response->assertOk();
    $kursiTerisi = $response->viewData('kursiTerisi');
    expect($kursiTerisi)->not->toContain(3);
});

// ─────────────────────────────────────────────────────────────────
// C. Rebooking
// ─────────────────────────────────────────────────────────────────

test('user lain dapat memesan kursi yang sebelumnya dibatalkan', function () {
    /** @var TestCase $this */
    $member1 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $member2 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $kendaraan = Kendaraan::factory()->create(['kapasitas_kursi' => 5]);
    $jadwal = Jadwal::factory()->create([
        'kendaraan_id' => $kendaraan->id,
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    // Member 1 memesan kursi 1 lalu dibatalkan
    $pesanan1 = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member1->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Dibatalkan,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Member 2 memesan kursi 1 yang sama melalui controller
    $response = $this->actingAs($member2)
        ->post(route('member.pesanan.store', $jadwal), [
            'nomor_kursi' => 1,
        ]);

    $response->assertSessionHasNoErrors();
    $pesanan2 = Pesanan::where('jadwal_id', $jadwal->id)
        ->where('pengguna_id', $member2->id)
        ->where('nomor_kursi', 1)
        ->first();

    expect($pesanan2)->not->toBeNull();
    expect($pesanan2->status)->toBe(StatusPesanan::BelumBayar);

    $rawPesanan2 = DB::table('pesanan')->where('id', $pesanan2->id)->first();
    expect((int) $rawPesanan2->kursi_aktif)->toBe(1);

    // Keduanya tetap ada di database: riwayat batal tetap terjaga
    expect(Pesanan::where('jadwal_id', $jadwal->id)->where('nomor_kursi', 1)->count())->toBe(2);
});

test('kursi yang sama dapat dibatalkan dan dipesan ulang berulang kali', function () {
    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);

    // Siklus 1: batal
    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 4,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Dibatalkan,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Siklus 2: batal lagi
    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 4,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Dibatalkan,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Siklus 3: batal ke-3
    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 4,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Dibatalkan,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Siklus 4: pesan aktif baru
    $pesananAktif = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 4,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Ada 4 baris untuk (jadwal_id, nomor_kursi = 4)
    expect(Pesanan::where('jadwal_id', $jadwal->id)->where('nomor_kursi', 4)->count())->toBe(4);

    // Hanya 1 yang memiliki kursi_aktif = 4
    $rawAktif = DB::table('pesanan')->where('id', $pesananAktif->id)->first();
    expect((int) $rawAktif->kursi_aktif)->toBe(4);

    $totalKursiAktif = DB::table('pesanan')
        ->where('jadwal_id', $jadwal->id)
        ->whereNotNull('kursi_aktif')
        ->count();
    expect($totalKursiAktif)->toBe(1);
});

// ─────────────────────────────────────────────────────────────────
// D. Duplicate Protection
// ─────────────────────────────────────────────────────────────────

test('database menolak dua pesanan aktif pada kursi dan jadwal yang sama', function () {
    $jadwal = Jadwal::factory()->create();
    $member1 = Pengguna::factory()->create();
    $member2 = Pengguna::factory()->create();

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member1->id,
        'nomor_kursi' => 2,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    expect(fn () => Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member2->id,
        'nomor_kursi' => 2,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Lunas,
        'batas_bayar' => now()->addHours(24),
    ]))->toThrow(QueryException::class);
});

test('nomor kursi yang sama boleh aktif di jadwal yang berbeda', function () {
    $jadwal1 = Jadwal::factory()->create();
    $jadwal2 = Jadwal::factory()->create();
    $member = Pengguna::factory()->create();

    $p1 = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal1->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 2,
        'harga_snapshot' => $jadwal1->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    $p2 = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal2->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 2,
        'harga_snapshot' => $jadwal2->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    expect($p1->id)->not->toBeNull();
    expect($p2->id)->not->toBeNull();
    expect(Pesanan::whereIn('jadwal_id', [$jadwal1->id, $jadwal2->id])->where('nomor_kursi', 2)->count())->toBe(2);
});

// ─────────────────────────────────────────────────────────────────
// E. Concurrency & Graceful Conflict Handling
// ─────────────────────────────────────────────────────────────────

test('controller member mengembalikan pesan error yang jelas jika kursi sudah dipesan', function () {
    /** @var TestCase $this */
    $member1 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $member2 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    // Member 1 sudah memesan kursi 1
    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member1->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Member 2 mencoba memesan kursi 1
    $response = $this->actingAs($member2)
        ->from(route('member.pesanan.create', $jadwal))
        ->post(route('member.pesanan.store', $jadwal), [
            'nomor_kursi' => 1,
        ]);

    $response->assertRedirect(route('member.pesanan.create', $jadwal));
    $response->assertSessionHasErrors('nomor_kursi');
});

test('simulasi balapan konkurensi: jika database melempar duplicate key exception, member menerima pesan terkontrol', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $kendaraan = Kendaraan::factory()->create(['kapasitas_kursi' => 5]);
    $jadwal = Jadwal::factory()->create([
        'kendaraan_id' => $kendaraan->id,
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    // Simulasikan race condition: saat event creating dipicu, transaksi lain
    // menyusup meng-insert pesanan aktif pada kursi yang sama secara mendahului
    Pesanan::creating(function ($pesanan) use ($jadwal) {
        static $inserted = false;
        if (! $inserted && $pesanan->nomor_kursi === 5) {
            $inserted = true;
            DB::table('pesanan')->insert([
                'kode_pesanan' => 'GTX-RACE01',
                'jadwal_id' => $jadwal->id,
                'pengguna_id' => Pengguna::factory()->create()->id,
                'nomor_kursi' => 5,
                'harga_snapshot' => $jadwal->harga,
                'status' => 'Belum Bayar',
                'batas_bayar' => now()->addHours(24),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    });

    $response = $this->actingAs($member)
        ->from(route('member.pesanan.create', $jadwal))
        ->post(route('member.pesanan.store', $jadwal), [
            'nomor_kursi' => 5,
        ]);

    $response->assertRedirect(route('member.pesanan.create', $jadwal));
    $response->assertSessionHasErrors([
        'nomor_kursi' => 'Kursi yang Anda pilih baru saja dipesan oleh pengguna lain. Silakan pilih kursi lain.',
    ]);
});

test('operator loket menerima pesan terkontrol saat kursi sudah terisi', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->operator()->create();
    $member1 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $member2 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member1->id,
        'nomor_kursi' => 3,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Lunas,
        'batas_bayar' => now()->addHours(1),
    ]);

    $response = $this->actingAs($operator)
        ->from(route('operator.loket.create'))
        ->post(route('operator.loket.store'), [
            'jadwal_id' => $jadwal->id,
            'pengguna_id' => $member2->id,
            'nomor_kursi' => 3,
        ]);

    $response->assertRedirect(route('operator.loket.create'));
    $response->assertSessionHasErrors('nomor_kursi');
});

test('operator loket dapat memesan kursi dengan kapasitas dinamis armada', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->operator()->create();
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $kendaraan = Kendaraan::factory()->create(['kapasitas_kursi' => 12]);
    $jadwal = Jadwal::factory()->create([
        'kendaraan_id' => $kendaraan->id,
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    // Memesan kursi 10 (lebih dari hardcoded lama 5)
    $response = $this->actingAs($operator)
        ->post(route('operator.loket.store'), [
            'jadwal_id' => $jadwal->id,
            'pengguna_id' => $member->id,
            'nomor_kursi' => 10,
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('operator.loket.index'));

    $pesanan = Pesanan::where('jadwal_id', $jadwal->id)
        ->where('nomor_kursi', 10)
        ->first();

    expect($pesanan)->not->toBeNull();
    expect($pesanan->status)->toBe(StatusPesanan::Lunas);
});

// ─────────────────────────────────────────────────────────────────
// F. Regression & Authorization
// ─────────────────────────────────────────────────────────────────

test('member tidak dapat memesan jadwal yang sudah lewat', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->subDay()->toDateString(),
    ]);

    $response = $this->actingAs($member)
        ->post(route('member.pesanan.store', $jadwal), [
            'nomor_kursi' => 1,
        ]);

    $response->assertSessionHasErrors('nomor_kursi');
    expect(Pesanan::where('jadwal_id', $jadwal->id)->count())->toBe(0);
});

test('member tidak dapat melihat tiket pesanan milik member lain', function () {
    /** @var TestCase $this */
    $memberA = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $memberB = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    $pesananA = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $memberA->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Member B mencoba mengakses detail pesanan Member A
    $response = $this->actingAs($memberB)->get(route('member.pesanan.show', $pesananA->kode_pesanan));
    $response->assertNotFound();
});

test('non-operator dilarang memesan tiket loket', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create();

    $response = $this->actingAs($member)->post(route('operator.loket.store'), [
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 1,
    ]);

    // Middleware PastikanPunyaPeran menolak member ke halaman loket
    $response->assertForbidden();
});
