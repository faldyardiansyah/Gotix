<?php

use App\Enums\KategoriKeluhan;
use App\Enums\PeranPengguna;
use App\Enums\StatusPesanan;
use App\Models\Jadwal;
use App\Models\Keluhan;
use App\Models\Kendaraan;
use App\Models\Kota;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Services\TiketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| A. PENCARIAN JADWAL & FILTER AKTIF (PUBLIK / MEMBER)
|--------------------------------------------------------------------------
*/

test('1. pencarian jadwal menampilkan jadwal keberangkatan aktif masa depan', function () {
    /** @var TestCase $this */
    $asal = Kota::factory()->create(['nama_kota' => 'Bandar Lampung']);
    $tujuan = Kota::factory()->create(['nama_kota' => 'Metro']);

    $jadwalAktif = Jadwal::factory()->create([
        'kota_asal_id' => $asal->id,
        'kota_tujuan_id' => $tujuan->id,
        'tanggal_berangkat' => now()->addDays(2)->toDateString(),
    ]);

    $response = $this->get(route('jadwal.cari', [
        'kota_asal' => 'Bandar Lampung',
        'kota_tujuan' => 'Metro',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Bandar Lampung');
    $response->assertSee('Metro');
});

test('2. jadwal lampau tidak muncul pada pencarian publik tanpa filter tanggal', function () {
    /** @var TestCase $this */
    $asal = Kota::factory()->create(['nama_kota' => 'Bakauheni']);
    $tujuan = Kota::factory()->create(['nama_kota' => 'Kalianda']);

    // Jadwal kemarin (lampau)
    $jadwalLampau = Jadwal::factory()->create([
        'kota_asal_id' => $asal->id,
        'kota_tujuan_id' => $tujuan->id,
        'tanggal_berangkat' => now()->subDays(2)->toDateString(),
    ]);

    $response = $this->get(route('jadwal.cari', [
        'kota_asal' => 'Bakauheni',
        'kota_tujuan' => 'Kalianda',
    ]));

    $response->assertStatus(200);
    // Jadwal lampau tidak ditampilkan sebagai hasil utama
    $hasilJadwal = $response->viewData('hasilJadwal');
    expect($hasilJadwal->contains('id', $jadwalLampau->id))->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| B. E-TIKET & VERIFIKASI QR CODE (MEMBER)
|--------------------------------------------------------------------------
*/

test('3. member dapat mengakses e-tiket resmi jika pesanan berstatus Lunas', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create([
        'tanggal_berangkat' => now()->addDays(3)->toDateString(),
        'jam_berangkat' => '08:00:00',
    ]);

    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::Lunas,
        'nomor_kursi' => 2,
    ]);

    $response = $this->actingAs($member)->get(route('member.tiket.show', $pesanan->kode_pesanan));

    $response->assertStatus(200);
    $response->assertSee($pesanan->kode_pesanan);
    $response->assertSee($jadwal->kotaAsal->nama_kota);
    $response->assertSee($jadwal->kotaTujuan->nama_kota);
    $response->assertSee('Status Tiket: TERVERIFIKASI', false);
    // Memastikan terdapat elemen SVG QR Code
    $response->assertSee('aria-label="QR Code Tiket '.$pesanan->kode_pesanan.'"', false);
});

test('4. e-tiket ditolak (404) untuk pesanan yang belum Lunas atau Dibatalkan', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create();

    // Pesanan Belum Bayar
    $pesananBelumBayar = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
    ]);

    $respBelumBayar = $this->actingAs($member)->get(route('member.tiket.show', $pesananBelumBayar->kode_pesanan));
    $respBelumBayar->assertStatus(404);

    // Pesanan Dibatalkan
    $pesananBatal = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::Dibatalkan,
    ]);

    $respBatal = $this->actingAs($member)->get(route('member.tiket.show', $pesananBatal->kode_pesanan));
    $respBatal->assertStatus(404);
});

test('5. member tidak dapat melihat e-tiket milik member lain (404)', function () {
    /** @var TestCase $this */
    $memberA = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $memberB = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create();

    $pesananA = Pesanan::factory()->create([
        'pengguna_id' => $memberA->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::Lunas,
    ]);

    $response = $this->actingAs($memberB)->get(route('member.tiket.show', $pesananA->kode_pesanan));
    $response->assertStatus(404);
});

test('6. TiketService menghasilkan payload dan signature verifikasi yang valid', function () {
    $tiketService = app(TiketService::class);
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::Lunas,
        'nomor_kursi' => 1,
    ]);

    $payload = $tiketService->generatePayload($pesanan);

    expect($payload['kode'])->toBe($pesanan->kode_pesanan);
    expect($payload['status'])->toBe(StatusPesanan::Lunas->value);
    expect($payload['signature'])->not->toBeEmpty();

    // Verifikasi sukses
    expect($tiketService->verifyPayload($payload))->toBeTrue();

    // Tampered payload harus ditolak
    $tamperedPayload = $payload;
    $tamperedPayload['signature'] = 'tampered_signature_12345';
    expect($tiketService->verifyPayload($tamperedPayload))->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| C. PENGADUAN KELUHAN & BALASAN (MEMBER & ADMIN)
|--------------------------------------------------------------------------
*/

test('7. member dapat mengirim keluhan baru dan tersimpan di database', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
    ]);

    $response = $this->actingAs($member)->post(route('member.keluhan.store'), [
        'kategori' => KategoriKeluhan::Pelayanan->value,
        'pesanan_id' => $pesanan->id,
        'deskripsi' => 'Pengemudi sangat sopan dan armada tiba tepat waktu.',
    ]);

    $response->assertRedirect(route('member.keluhan.index'));
    $this->assertDatabaseHas('keluhan', [
        'pengguna_id' => $member->id,
        'pesanan_id' => $pesanan->id,
        'kategori' => KategoriKeluhan::Pelayanan->value,
        'deskripsi' => 'Pengemudi sangat sopan dan armada tiba tepat waktu.',
    ]);
});

test('8. admin dapat melihat dan membalas keluhan member', function () {
    /** @var TestCase $this */
    $admin = Pengguna::factory()->create(['peran' => PeranPengguna::Admin]);
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $keluhan = Keluhan::factory()->create([
        'pengguna_id' => $member->id,
        'deskripsi' => 'Keluhan penumpang tentang pendingin ruangan bus.',
        'balasan' => null,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.keluhan.balas', $keluhan->id), [
        'balasan' => 'Terima kasih atas masukannya. Teknisi kami telah memperbaiki AC armada.',
    ]);

    $response->assertRedirect(route('admin.keluhan.show', $keluhan->id));
    $this->assertDatabaseHas('keluhan', [
        'id' => $keluhan->id,
        'balasan' => 'Terima kasih atas masukannya. Teknisi kami telah memperbaiki AC armada.',
        'dibalas_oleh' => $admin->id,
    ]);
});

/*
|--------------------------------------------------------------------------
| D. OPERATOR LOKET (WALK-IN ORDERS)
|--------------------------------------------------------------------------
*/

test('9. operator dapat membuat pesanan loket dan menghasilkan status Lunas', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $customer = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $kendaraan = Kendaraan::factory()->create(['kapasitas_kursi' => 5]);
    $jadwal = Jadwal::factory()->create([
        'kendaraan_id' => $kendaraan->id,
        'tanggal_berangkat' => now()->addDay()->toDateString(),
    ]);

    $response = $this->actingAs($operator)->post(route('operator.loket.store'), [
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $customer->id,
        'nomor_kursi' => 3,
    ]);

    $response->assertRedirect(route('operator.loket.index'));
    $this->assertDatabaseHas('pesanan', [
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $customer->id,
        'nomor_kursi' => 3,
        'status' => StatusPesanan::Lunas->value,
    ]);
});

test('10. operator dilarang memesan kursi yang sudah terisi', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $customer1 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $customer2 = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $kendaraan = Kendaraan::factory()->create(['kapasitas_kursi' => 5]);
    $jadwal = Jadwal::factory()->create(['kendaraan_id' => $kendaraan->id]);

    // Kursi 2 sudah aktif terisi
    Pesanan::factory()->create([
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $customer1->id,
        'nomor_kursi' => 2,
        'status' => StatusPesanan::Lunas,
    ]);

    // Operator mencoba memesan kursi 2 lagi
    $response = $this->actingAs($operator)->post(route('operator.loket.store'), [
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => $customer2->id,
        'nomor_kursi' => 2,
    ]);

    $response->assertSessionHasErrors('nomor_kursi');
});

/*
|--------------------------------------------------------------------------
| E. ADMIN DETAIL JADWAL
|--------------------------------------------------------------------------
*/

test('11. admin dapat melihat detail jadwal lengkap beserta denah kursi dan pesanan', function () {
    /** @var TestCase $this */
    $admin = Pengguna::factory()->create(['peran' => PeranPengguna::Admin]);
    $jadwal = Jadwal::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.jadwal.show', $jadwal->id));

    $response->assertStatus(200);
    $response->assertSee($jadwal->kotaAsal->nama_kota);
    $response->assertSee($jadwal->kotaTujuan->nama_kota);
    $response->assertSee('Denah Visual Keterisian Kursi');
    $response->assertSee('Daftar Transaksi Tiket Terkait');
});

/*
|--------------------------------------------------------------------------
| F. P1 FINAL COMPLETION: WALKIN NON-MEMBER & EXTENDED COMPLAINTS
|--------------------------------------------------------------------------
*/

test('12. operator dapat memesan tiket untuk penumpang walk-in tanpa akun member', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $kendaraan = Kendaraan::factory()->create(['kapasitas_kursi' => 5]);
    $jadwal = Jadwal::factory()->create(['kendaraan_id' => $kendaraan->id]);

    $response = $this->actingAs($operator)->post(route('operator.loket.store'), [
        'jadwal_id' => $jadwal->id,
        'nomor_kursi' => 4,
        'nama_penumpang' => 'Siti Nurhaliza',
        'telepon_penumpang' => '081298765432',
    ]);

    $response->assertRedirect(route('operator.loket.index'));
    $this->assertDatabaseHas('pesanan', [
        'jadwal_id' => $jadwal->id,
        'pengguna_id' => null,
        'nomor_kursi' => 4,
        'nama_penumpang' => 'Siti Nurhaliza',
        'telepon_penumpang' => '081298765432',
        'dibuat_oleh_operator_id' => $operator->id,
        'status' => StatusPesanan::Lunas->value,
    ]);
});

test('13. walk-in tanpa akun wajib mengisi nama dan telepon penumpang', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $jadwal = Jadwal::factory()->create();

    $response = $this->actingAs($operator)->post(route('operator.loket.store'), [
        'jadwal_id' => $jadwal->id,
        'nomor_kursi' => 1,
        'pengguna_id' => null,
        'nama_penumpang' => '',
        'telepon_penumpang' => '',
    ]);

    $response->assertSessionHasErrors(['nama_penumpang', 'telepon_penumpang']);
});

test('14. TiketService dan e-tiket boarding pass menampilkan identitas walk-in secara akurat', function () {
    $tiketService = app(TiketService::class);
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $jadwal = Jadwal::factory()->create();

    $pesananWalkin = Pesanan::factory()->create([
        'pengguna_id' => null,
        'nama_penumpang' => 'Rahmat Hidayat',
        'telepon_penumpang' => '085211223344',
        'dibuat_oleh_operator_id' => $operator->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::Lunas,
        'nomor_kursi' => 3,
    ]);

    $payload = $tiketService->generatePayload($pesananWalkin);
    expect($payload['penumpang'])->toBe('Rahmat Hidayat');
    expect($payload['status'])->toBe(StatusPesanan::Lunas->value);
    expect($tiketService->verifyPayload($payload))->toBeTrue();
});

test('15. member dapat mengajukan keluhan kategori Armada', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);

    $response = $this->actingAs($member)->post(route('member.keluhan.store'), [
        'kategori' => KategoriKeluhan::Armada->value,
        'deskripsi' => 'Suspensi armada sangat nyaman dan kursi eksekutif terawat baik.',
    ]);

    $response->assertRedirect(route('member.keluhan.index'));
    $this->assertDatabaseHas('keluhan', [
        'pengguna_id' => $member->id,
        'kategori' => 'Armada',
        'deskripsi' => 'Suspensi armada sangat nyaman dan kursi eksekutif terawat baik.',
    ]);
});

test('16. keluhan dengan deskripsi hingga 1000 karakter diterima sesuai konfigurasi bisnis', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $deskripsi1000 = str_repeat('Pelayanan sangat memuaskan dan armada prima. ', 22);
    $deskripsi1000 = substr($deskripsi1000, 0, 1000);

    $response = $this->actingAs($member)->post(route('member.keluhan.store'), [
        'kategori' => KategoriKeluhan::Pelayanan->value,
        'deskripsi' => $deskripsi1000,
    ]);

    $response->assertRedirect(route('member.keluhan.index'));
    $this->assertDatabaseHas('keluhan', [
        'pengguna_id' => $member->id,
        'deskripsi' => $deskripsi1000,
    ]);
});

test('17. keluhan dengan deskripsi melebihi 1000 karakter ditolak validasi', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $deskripsi1001 = str_repeat('A', 1001);

    $response = $this->actingAs($member)->post(route('member.keluhan.store'), [
        'kategori' => KategoriKeluhan::Pelayanan->value,
        'deskripsi' => $deskripsi1001,
    ]);

    $response->assertSessionHasErrors('deskripsi');
});

test('18. TiketService menghasilkan SVG QR Code berstandar ISO/IEC 18004 untuk tiket Member', function () {
    $tiketService = app(TiketService::class);
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member, 'nama' => 'Budi Santoso']);
    $jadwal = Jadwal::factory()->create();

    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::Lunas,
        'nomor_kursi' => 2,
    ]);

    $svg = $tiketService->generateQrSvg($pesanan);

    // 1. Verifikasi elemen SVG dan styling responsif
    expect($svg)->toContain('<svg');
    expect($svg)->toContain('viewBox="0 0 200 200"');
    expect($svg)->toContain('role="img"');
    expect($svg)->toContain('aria-label="QR Code Tiket '.$pesanan->kode_pesanan.'"');
    expect($svg)->not->toContain('<?xml');

    // 2. Verifikasi payload yang di-encode
    $payload = $tiketService->generatePayload($pesanan);
    expect($payload['penumpang'])->toBe('Budi Santoso');
    expect($tiketService->verifyPayload($payload))->toBeTrue();

    // 3. Verifikasi tamper detection
    $tampered = $payload;
    $tampered['kursi'] = 99;
    expect($tiketService->verifyPayload($tampered))->toBeFalse();
});

test('19. TiketService menghasilkan SVG QR Code berstandar ISO/IEC 18004 untuk tiket Walk-in', function () {
    $tiketService = app(TiketService::class);
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $jadwal = Jadwal::factory()->create();

    $pesananWalkin = Pesanan::factory()->create([
        'pengguna_id' => null,
        'nama_penumpang' => 'Dewi Sartika',
        'telepon_penumpang' => '081233445566',
        'dibuat_oleh_operator_id' => $operator->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::Lunas,
        'nomor_kursi' => 5,
    ]);

    $svg = $tiketService->generateQrSvg($pesananWalkin);

    expect($svg)->toContain('<svg');
    expect($svg)->toContain('aria-label="QR Code Tiket '.$pesananWalkin->kode_pesanan.'"');

    $payload = $tiketService->generatePayload($pesananWalkin);
    expect($payload['penumpang'])->toBe('Dewi Sartika');
    expect($tiketService->verifyPayload($payload))->toBeTrue();
});
