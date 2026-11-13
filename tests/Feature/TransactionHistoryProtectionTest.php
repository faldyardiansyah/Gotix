<?php

use App\Enums\KategoriKeluhan;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

// ─────────────────────────────────────────────────────────────────
// A. City (Kota) Deletion & Retirement Policies
// ─────────────────────────────────────────────────────────────────

test('kota dengan riwayat jadwal lampau dapat di-soft-delete secara aman', function () {
    $admin = Pengguna::factory()->admin()->create();
    $kotaAsal = Kota::factory()->create(['nama_kota' => 'Kota Asal Lampau']);
    $kotaTujuan = Kota::factory()->create(['nama_kota' => 'Kota Tujuan Tetap']);

    // Buat jadwal masa lalu yang sudah selesai
    $jadwalLampau = Jadwal::factory()->create([
        'kota_asal_id' => $kotaAsal->id,
        'kota_tujuan_id' => $kotaTujuan->id,
        'tanggal_berangkat' => now()->subDays(5)->toDateString(),
        'jam_berangkat' => '08:00:00',
    ]);

    // Admin menghapus (mempensiunkan) kota asal yang hanya punya jadwal lampau
    $response = $this->actingAs($admin)->delete(route('admin.kota.destroy', $kotaAsal->id));
    $response->assertRedirect(route('admin.kota.index'));
    $response->assertSessionHas('sukses');

    // Kota ter-soft-delete di database
    expect(Kota::find($kotaAsal->id))->toBeNull();
    $kotaSoftDeleted = Kota::withTrashed()->find($kotaAsal->id);
    expect($kotaSoftDeleted)->not->toBeNull();
    expect($kotaSoftDeleted->deleted_at)->not->toBeNull();

    // Riwayat jadwal lampau tetap utuh dan masih dapat memuat nama kota asal
    $jadwalRefetched = Jadwal::with('kotaAsal')->find($jadwalLampau->id);
    expect($jadwalRefetched->kotaAsal->nama_kota)->toBe('Kota Asal Lampau');
});

test('kota yang masih memiliki jadwal aktif masa depan dilarang di-soft-delete', function () {
    $admin = Pengguna::factory()->admin()->create();
    $kota = Kota::factory()->create(['nama_kota' => 'Kota Rute Aktif']);

    Jadwal::factory()->create([
        'kota_asal_id' => $kota->id,
        'tanggal_berangkat' => now()->addDays(3)->toDateString(),
        'jam_berangkat' => '10:00:00',
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.kota.destroy', $kota->id));
    $response->assertSessionHasErrors('error');

    // Kota tetap aktif (tidak ter-soft-delete)
    expect(Kota::find($kota->id))->not->toBeNull();
    expect(Kota::find($kota->id)->deleted_at)->toBeNull();
});

test('kota yang di-soft-delete tidak muncul lagi pada pilihan pembuatan jadwal baru', function () {
    $admin = Pengguna::factory()->admin()->create();
    $kotaPensiun = Kota::factory()->create(['nama_kota' => 'Kota Rute Pensiun']);
    $kotaAktif = Kota::factory()->create(['nama_kota' => 'Kota Rute Baru']);

    $kotaPensiun->delete();

    $response = $this->actingAs($admin)->get(route('admin.jadwal.create'));
    $response->assertOk();

    $daftarKota = $response->viewData('daftarKota');
    expect($daftarKota->pluck('nama_kota'))->toContain('Kota Rute Baru');
    expect($daftarKota->pluck('nama_kota'))->not->toContain('Kota Rute Pensiun');
});

// ─────────────────────────────────────────────────────────────────
// B. Vehicle (Kendaraan) Deletion & Retirement Policies
// ─────────────────────────────────────────────────────────────────

test('kendaraan dengan riwayat jadwal lampau dapat di-soft-delete secara aman', function () {
    $admin = Pengguna::factory()->admin()->create();
    $kendaraan = Kendaraan::factory()->create(['nomor_polisi' => 'BE 8888 OLD']);

    // Jadwal masa lampau
    $jadwalLampau = Jadwal::factory()->create([
        'kendaraan_id' => $kendaraan->id,
        'tanggal_berangkat' => now()->subDays(3)->toDateString(),
        'jam_berangkat' => '07:00:00',
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.kendaraan.destroy', $kendaraan->id));
    $response->assertRedirect(route('admin.kendaraan.index'));
    $response->assertSessionHas('sukses');

    expect(Kendaraan::find($kendaraan->id))->toBeNull();
    $kendaraanSoftDeleted = Kendaraan::withTrashed()->find($kendaraan->id);
    expect($kendaraanSoftDeleted)->not->toBeNull();
    expect($kendaraanSoftDeleted->deleted_at)->not->toBeNull();

    // Relasi jadwal lampau tetap memuat plat kendaraan pensiun
    $jadwalRefetched = Jadwal::with('kendaraan')->find($jadwalLampau->id);
    expect($jadwalRefetched->kendaraan->nomor_polisi)->toBe('BE 8888 OLD');
});

test('kendaraan yang masih ditugaskan pada jadwal aktif masa depan dilarang di-soft-delete', function () {
    $admin = Pengguna::factory()->admin()->create();
    $kendaraan = Kendaraan::factory()->create(['nomor_polisi' => 'BE 1111 ACT']);

    Jadwal::factory()->create([
        'kendaraan_id' => $kendaraan->id,
        'tanggal_berangkat' => now()->addDays(2)->toDateString(),
        'jam_berangkat' => '14:00:00',
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.kendaraan.destroy', $kendaraan->id));
    $response->assertSessionHasErrors('error');

    expect(Kendaraan::find($kendaraan->id))->not->toBeNull();
    expect(Kendaraan::find($kendaraan->id)->deleted_at)->toBeNull();
});

test('kendaraan yang di-soft-delete tidak muncul lagi pada pilihan pembuatan jadwal baru', function () {
    $admin = Pengguna::factory()->admin()->create();
    $kendaraanPensiun = Kendaraan::factory()->create(['nomor_polisi' => 'BE 5555 PEN']);
    $kendaraanAktif = Kendaraan::factory()->create(['nomor_polisi' => 'BE 6666 NEW']);

    $kendaraanPensiun->delete();

    $response = $this->actingAs($admin)->get(route('admin.jadwal.create'));
    $response->assertOk();

    $daftarKendaraan = $response->viewData('daftarKendaraan');
    expect($daftarKendaraan->pluck('nomor_polisi'))->toContain('BE 6666 NEW');
    expect($daftarKendaraan->pluck('nomor_polisi'))->not->toContain('BE 5555 PEN');
});

// ─────────────────────────────────────────────────────────────────
// C. Schedule (Jadwal) Deletion & Transaction State Distinction
// ─────────────────────────────────────────────────────────────────

test('jadwal masa depan dengan tiket penumpang aktif lunas dilarang di-soft-delete', function () {
    $admin = Pengguna::factory()->admin()->create();
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwalMasaDepan = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDays(2)->toDateString(),
        'jam_berangkat' => '09:00:00',
    ]);

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwalMasaDepan->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwalMasaDepan->harga,
        'status' => StatusPesanan::Lunas,
        'batas_bayar' => now()->addHours(24),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.jadwal.destroy', $jadwalMasaDepan->id));
    $response->assertSessionHasErrors('error');

    expect(Jadwal::find($jadwalMasaDepan->id))->not->toBeNull();
    expect(Jadwal::find($jadwalMasaDepan->id)->deleted_at)->toBeNull();
});

test('jadwal dengan transaksi pembayaran yang belum selesai dilarang di-soft-delete meski tanggal telah lewat', function () {
    $admin = Pengguna::factory()->admin()->create();
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);

    // Jadwal kemarin tetapi status pembayaran masih Dalam Proses (ada bukti bayar perlu verifikasi)
    $jadwalKemarin = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->subDay()->toDateString(),
        'jam_berangkat' => '10:00:00',
    ]);

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwalKemarin->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwalKemarin->harga,
        'status' => StatusPesanan::DalamProses,
        'batas_bayar' => now()->addHours(24),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.jadwal.destroy', $jadwalKemarin->id));
    $response->assertSessionHasErrors('error');

    expect(Jadwal::find($jadwalKemarin->id))->not->toBeNull();
    expect(Jadwal::find($jadwalKemarin->id)->deleted_at)->toBeNull();
});

test('jadwal masa lampau dengan transaksi yang telah tuntas lunas dapat di-soft-delete', function () {
    $admin = Pengguna::factory()->admin()->create();
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);

    // Jadwal 3 hari lalu dengan pesanan yang telah selesai (Lunas)
    $jadwalLampau = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->subDays(3)->toDateString(),
        'jam_berangkat' => '08:00:00',
    ]);

    $pesanan = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwalLampau->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 2,
        'harga_snapshot' => $jadwalLampau->harga,
        'status' => StatusPesanan::Lunas,
        'batas_bayar' => now()->subDays(3),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.jadwal.destroy', $jadwalLampau->id));
    $response->assertRedirect(route('admin.jadwal.index'));
    $response->assertSessionHas('sukses');

    // Jadwal ter-soft-delete
    expect(Jadwal::find($jadwalLampau->id))->toBeNull();
    expect(Jadwal::withTrashed()->find($jadwalLampau->id)->deleted_at)->not->toBeNull();

    // Tiket pesanan historis tetap utuh dan relasi ke jadwal tetap terbaca
    $pesananRefetched = Pesanan::with('jadwal')->find($pesanan->id);
    expect($pesananRefetched)->not->toBeNull();
    expect($pesananRefetched->jadwal)->not->toBeNull();
    expect($pesananRefetched->jadwal->id)->toBe($jadwalLampau->id);
});

test('jadwal yang seluruh pemesanannya dibatalkan dapat di-soft-delete secara aman', function () {
    $admin = Pengguna::factory()->admin()->create();
    $member1 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $member2 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);

    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDays(2)->toDateString(),
        'jam_berangkat' => '15:00:00',
    ]);

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member1->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Dibatalkan,
        'batas_bayar' => now()->addHours(24),
    ]);

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member2->id,
        'nomor_kursi' => 2,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Dibatalkan,
        'batas_bayar' => now()->addHours(24),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.jadwal.destroy', $jadwal->id));
    $response->assertRedirect(route('admin.jadwal.index'));
    $response->assertSessionHas('sukses');

    expect(Jadwal::find($jadwal->id))->toBeNull();
    expect(Jadwal::withTrashed()->find($jadwal->id)->deleted_at)->not->toBeNull();
    expect(Pesanan::where('jadwal_id', $jadwal->id)->count())->toBe(2);
});

// ─────────────────────────────────────────────────────────────────
// D. Historical Resolution & Database Constraint Verification
// ─────────────────────────────────────────────────────────────────

test('relasi riwayat transaksi tetap dapat mengakses master data yang di-soft-delete', function () {
    $member = Pengguna::factory()->create([
        'nama' => 'Pelanggan Setia Gotix',
        'peran' => PeranPengguna::Member,
    ]);
    $kotaAsal = Kota::factory()->create(['nama_kota' => 'Tanjung Karang']);
    $kotaTujuan = Kota::factory()->create(['nama_kota' => 'Bakauheni']);
    $kendaraan = Kendaraan::factory()->create(['nomor_polisi' => 'BE 7777 HIST']);
    $jadwal = Jadwal::factory()->create([
        'kota_asal_id' => $kotaAsal->id,
        'kota_tujuan_id' => $kotaTujuan->id,
        'kendaraan_id' => $kendaraan->id,
    ]);

    $pesanan = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 3,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Lunas,
        'batas_bayar' => now()->addHours(24),
    ]);

    // Soft-delete seluruh entitas master
    $jadwal->delete();
    $kotaAsal->delete();
    $kotaTujuan->delete();
    $kendaraan->delete();
    $member->delete();

    // Muat ulang pesanan dengan seluruh relasi withTrashed
    $pesananFresh = Pesanan::with(['jadwal.kotaAsal', 'jadwal.kotaTujuan', 'jadwal.kendaraan', 'pengguna'])
        ->find($pesanan->id);

    expect($pesananFresh->jadwal)->not->toBeNull();
    expect($pesananFresh->jadwal->kotaAsal->nama_kota)->toBe('Tanjung Karang');
    expect($pesananFresh->jadwal->kotaTujuan->nama_kota)->toBe('Bakauheni');
    expect($pesananFresh->jadwal->kendaraan->nomor_polisi)->toBe('BE 7777 HIST');
    expect($pesananFresh->pengguna->nama)->toBe('Pelanggan Setia Gotix');
});

test('database foreign key RESTRICT menolak hard delete kota yang memiliki jadwal', function () {
    $kota = Kota::factory()->create();
    $jadwal = Jadwal::factory()->create(['kota_asal_id' => $kota->id]);

    expect(fn () => DB::table('kota')->where('id', $kota->id)->delete())
        ->toThrow(QueryException::class);
});

test('database foreign key RESTRICT menolak hard delete kendaraan yang memiliki jadwal', function () {
    $kendaraan = Kendaraan::factory()->create();
    $jadwal = Jadwal::factory()->create(['kendaraan_id' => $kendaraan->id]);

    expect(fn () => DB::table('kendaraan')->where('id', $kendaraan->id)->delete())
        ->toThrow(QueryException::class);
});

test('database foreign key RESTRICT menolak hard delete jadwal yang memiliki pesanan', function () {
    $jadwal = Jadwal::factory()->create();
    $member = Pengguna::factory()->create();

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    expect(fn () => DB::table('jadwal')->where('id', $jadwal->id)->delete())
        ->toThrow(QueryException::class);
});

test('database foreign key RESTRICT menolak hard delete pengguna yang memiliki pesanan', function () {
    $jadwal = Jadwal::factory()->create();
    $member = Pengguna::factory()->create();

    Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(24),
    ]);

    expect(fn () => DB::table('pengguna')->where('id', $member->id)->delete())
        ->toThrow(QueryException::class);
});

test('database foreign key RESTRICT melindungi konfirmasi_pembayaran dan verifikator', function () {
    $jadwal = Jadwal::factory()->create();
    $member = Pengguna::factory()->create();
    $operator = Pengguna::factory()->operator()->create();

    $pesanan = Pesanan::create([
        'kode_pesanan' => 'GTX-'.strtoupper(Str::random(6)),
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $member->id,
        'nomor_kursi' => 1,
        'harga_snapshot' => $jadwal->harga,
        'status' => StatusPesanan::Lunas,
        'batas_bayar' => now()->addHours(24),
    ]);

    $konfirmasi = KonfirmasiPembayaran::create([
        'pesanan_id' => $pesanan->id,
        'nomor_resi' => 'TF-888888',
        'rekening_pengirim' => 'BCA 9876543210',
        'tanggal_transfer' => now()->toDateString(),
        'jam_transfer' => '12:00:00',
        'diverifikasi_oleh' => $operator->id,
        'diverifikasi_pada' => now(),
    ]);

    expect(fn () => DB::table('pesanan')->where('id', $pesanan->id)->delete())
        ->toThrow(QueryException::class);

    expect(fn () => DB::table('pengguna')->where('id', $operator->id)->delete())
        ->toThrow(QueryException::class);

    $operator->delete();
    expect($konfirmasi->fresh()->verifikator)->not->toBeNull();
    expect($konfirmasi->fresh()->verifikator->id)->toBe($operator->id);
});

test('keluhan dengan pesanan_id yang bernilai null tetap valid sesuai rancangan', function () {
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);

    $keluhan = Keluhan::create([
        'pengguna_id' => $member->id,
        'pesanan_id' => null,
        'kategori' => KategoriKeluhan::Pelayanan,
        'deskripsi' => 'Pelayanan loket sangat ramah dan cepat.',
    ]);

    expect($keluhan->pesanan_id)->toBeNull();
    expect($keluhan->pengguna->id)->toBe($member->id);
});
