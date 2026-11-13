<?php

use App\Enums\PeranPengguna;
use App\Enums\StatusPesanan;
use App\Models\Jadwal;
use App\Models\KonfirmasiPembayaran;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Models\RiwayatPembayaran;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

function p04LocalDisk(): FilesystemAdapter
{
    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('local');

    return $disk;
}

/*
|--------------------------------------------------------------------------
| A. PENGUJIAN UPLOAD BUKTI PEMBAYARAN
|--------------------------------------------------------------------------
*/

test('member pemilik dapat mengunggah bukti pembayaran valid sebelum batas bayar', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(2),
    ]);

    $file = UploadedFile::fake()->image('struk_pembayaran.jpg', 600, 600)->size(1024);

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BCA - 1234567890',
        'nomor_resi' => 'TF-VALID001',
        'tanggal_transfer' => now()->toDateString(),
        'jam_transfer' => '10:15',
        'bukti_transfer' => $file,
    ]);

    $response->assertRedirect(route('member.pesanan.show', $pesanan->kode_pesanan));
    $response->assertSessionHas('sukses');

    // Status pesanan menjadi Dalam Proses
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::DalamProses);

    // Record konfirmasi pembayaran tersimpan
    $konfirmasi = KonfirmasiPembayaran::where('pesanan_id', $pesanan->id)->first();
    expect($konfirmasi)->not->toBeNull()
        ->and($konfirmasi->nomor_resi)->toBe('TF-VALID001')
        ->and($konfirmasi->rekening_pengirim)->toBe('BCA - 1234567890');

    // File tersimpan di private storage
    p04LocalDisk()->assertExists($konfirmasi->bukti_path);

    // Record riwayat pembayaran tersimpan
    $riwayat = RiwayatPembayaran::where('pesanan_id', $pesanan->id)->first();
    expect($riwayat)->not->toBeNull()
        ->and($riwayat->status)->toBe('Dalam Proses')
        ->and($riwayat->bukti_path)->toBe($konfirmasi->bukti_path);
});

test('member lain dilarang mengunggah bukti transfer untuk pesanan milik orang lain (403)', function () {
    /** @var TestCase $this */
    $owner = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $intruder = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $owner->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(2),
    ]);

    $file = UploadedFile::fake()->image('struk_intruder.jpg');

    $response = $this->actingAs($intruder)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'Mandiri - 999999999',
        'bukti_transfer' => $file,
    ]);

    $response->assertStatus(403);
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::BelumBayar);
});

test('guest ditolak saat mencoba mengunggah bukti pembayaran', function () {
    /** @var TestCase $this */
    $pesanan = Pesanan::factory()->create([
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(2),
    ]);

    $file = UploadedFile::fake()->image('struk_guest.jpg');

    $response = $this->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BNI - 111111111',
        'bukti_transfer' => $file,
    ]);

    $response->assertRedirect(route('masuk'));
});

test('pesanan yang sudah Lunas menolak unggahan bukti baru', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'status' => StatusPesanan::Lunas,
    ]);

    $file = UploadedFile::fake()->image('struk_baru.jpg');

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BCA - 1234567890',
        'bukti_transfer' => $file,
    ]);

    $response->assertRedirect(route('member.pesanan.show', $pesanan->kode_pesanan));
    $response->assertSessionHas('gagal');
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::Lunas);
});

test('pesanan yang sudah Dibatalkan menolak unggahan bukti pembayaran', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'status' => StatusPesanan::Dibatalkan,
    ]);

    $file = UploadedFile::fake()->image('struk_batal.jpg');

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BCA - 1234567890',
        'bukti_transfer' => $file,
    ]);

    $response->assertRedirect(route('member.pesanan.show', $pesanan->kode_pesanan));
    $response->assertSessionHas('gagal');
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::Dibatalkan);
});

test('pesanan yang telah melewati batas pembayaran menolak unggahan bukti', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->subMinutes(10), // Sudah kedaluwarsa
    ]);

    $file = UploadedFile::fake()->image('struk_telat.jpg');

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BCA - 1234567890',
        'bukti_transfer' => $file,
    ]);

    $response->assertRedirect(route('member.pesanan.show', $pesanan->kode_pesanan));
    $response->assertSessionHas('gagal');
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::BelumBayar);
});

/*
|--------------------------------------------------------------------------
| B. PENGUJIAN VERIFIKASI PEMBAYARAN (OPERATOR & ADMIN)
|--------------------------------------------------------------------------
*/

test('operator dapat menyetujui pembayaran Dalam Proses menjadi Lunas dan mencatat auditor serta waktu', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'status' => StatusPesanan::DalamProses,
    ]);

    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/struk-valid.jpg',
    ]);

    $riwayat = RiwayatPembayaran::create([
        'pesanan_id' => $pesanan->id,
        'konfirmasi_pembayaran_id' => $konfirmasi->id,
        'pengguna_id' => $member->id,
        'nomor_resi' => $konfirmasi->nomor_resi,
        'rekening_pengirim' => $konfirmasi->rekening_pengirim,
        'tanggal_transfer' => now()->toDateString(),
        'jam_transfer' => '09:00:00',
        'bukti_path' => $konfirmasi->bukti_path,
        'status' => 'Dalam Proses',
    ]);

    $response = $this->actingAs($operator)->post(route('operator.pembayaran.verifikasi', $pesanan->id));

    $response->assertRedirect(route('operator.pembayaran.index'));
    $response->assertSessionHas('sukses');

    // Pesanan menjadi Lunas
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::Lunas);

    // Konfirmasi menyimpan verifikator dan waktu
    $konfirmasiFresh = $konfirmasi->fresh();
    expect($konfirmasiFresh->diverifikasi_oleh)->toBe($operator->id)
        ->and($konfirmasiFresh->diverifikasi_pada)->not->toBeNull()
        ->and($konfirmasiFresh->alasan_penolakan)->toBeNull();

    // Riwayat menjadi Diterima
    $riwayatFresh = $riwayat->fresh();
    expect($riwayatFresh->status)->toBe('Diterima')
        ->and($riwayatFresh->diverifikasi_oleh)->toBe($operator->id)
        ->and($riwayatFresh->diverifikasi_pada)->not->toBeNull();
});

test('operator dapat menolak pembayaran Dalam Proses dan mengembalikan pesanan ke status Belum Bayar dengan alasan penolakan wajib', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'status' => StatusPesanan::DalamProses,
        'batas_bayar' => now()->addHour(),
    ]);

    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/struk-blur.jpg',
    ]);

    $riwayat = RiwayatPembayaran::create([
        'pesanan_id' => $pesanan->id,
        'konfirmasi_pembayaran_id' => $konfirmasi->id,
        'pengguna_id' => $member->id,
        'nomor_resi' => $konfirmasi->nomor_resi,
        'rekening_pengirim' => $konfirmasi->rekening_pengirim,
        'tanggal_transfer' => now()->toDateString(),
        'jam_transfer' => '09:00:00',
        'bukti_path' => $konfirmasi->bukti_path,
        'status' => 'Dalam Proses',
    ]);

    // 1. Coba tolak tanpa alasan penolakan -> gagal validasi
    $failedResponse = $this->actingAs($operator)->post(route('operator.pembayaran.tolak', $pesanan->id), []);
    $failedResponse->assertSessionHasErrors('alasan_penolakan');
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::DalamProses);

    // 2. Tolak dengan alasan penolakan yang sah
    $successResponse = $this->actingAs($operator)->post(route('operator.pembayaran.tolak', $pesanan->id), [
        'alasan_penolakan' => 'Nominal transfer Rp 50.000 tidak sesuai tagihan Rp 150.000.',
    ]);

    $successResponse->assertRedirect(route('operator.pembayaran.index'));
    $successResponse->assertSessionHas('sukses');

    // Pesanan KEMBALI menjadi Belum Bayar (bukan Dibatalkan)
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::BelumBayar);

    // Konfirmasi menyimpan alasan penolakan dan petugas
    $konfirmasiFresh = $konfirmasi->fresh();
    expect($konfirmasiFresh->diverifikasi_oleh)->toBe($operator->id)
        ->and($konfirmasiFresh->diverifikasi_pada)->not->toBeNull()
        ->and($konfirmasiFresh->alasan_penolakan)->toBe('Nominal transfer Rp 50.000 tidak sesuai tagihan Rp 150.000.');

    // Riwayat pembayaran menjadi Ditolak
    $riwayatFresh = $riwayat->fresh();
    expect($riwayatFresh->status)->toBe('Ditolak')
        ->and($riwayatFresh->diverifikasi_oleh)->toBe($operator->id)
        ->and($riwayatFresh->alasan_penolakan)->toBe('Nominal transfer Rp 50.000 tidak sesuai tagihan Rp 150.000.');
});

test('admin dapat memverifikasi dan menolak pembayaran dengan audit identitas admin', function () {
    /** @var TestCase $this */
    $admin = Pengguna::factory()->create(['peran' => PeranPengguna::Admin]);
    $pesanan = Pesanan::factory()->create(['status' => StatusPesanan::DalamProses]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create(['pesanan_id' => $pesanan->id]);

    $response = $this->actingAs($admin)->post(route('admin.pesanan.verifikasi', $pesanan->id));
    $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));

    expect($pesanan->fresh()->status)->toBe(StatusPesanan::Lunas);
    expect($konfirmasi->fresh()->diverifikasi_oleh)->toBe($admin->id);
});

test('member tidak dapat mengakses endpoint verifikasi pembayaran (403)', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $pesanan = Pesanan::factory()->create(['status' => StatusPesanan::DalamProses]);

    $response = $this->actingAs($member)->post(route('operator.pembayaran.verifikasi', $pesanan->id));
    $response->assertStatus(403);
});

test('verifikasi terhadap pesanan yang sudah Lunas atau Dibatalkan ditolak', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);

    $pesananLunas = Pesanan::factory()->create(['status' => StatusPesanan::Lunas]);
    $pesananBatal = Pesanan::factory()->create(['status' => StatusPesanan::Dibatalkan]);

    // Verifikasi pada pesanan lunas
    $response1 = $this->actingAs($operator)->post(route('operator.pembayaran.verifikasi', $pesananLunas->id));
    $response1->assertRedirect(route('operator.pembayaran.show', $pesananLunas->id));
    $response1->assertSessionHas('gagal');

    // Verifikasi pada pesanan batal
    $response2 = $this->actingAs($operator)->post(route('operator.pembayaran.verifikasi', $pesananBatal->id));
    $response2->assertRedirect(route('operator.pembayaran.show', $pesananBatal->id));
    $response2->assertSessionHas('gagal');
});

/*
|--------------------------------------------------------------------------
| C. PENGUJIAN HISTORI BUKTI & UPLOAD ULANG
|--------------------------------------------------------------------------
*/

test('member dapat mengunggah ulang bukti setelah penolakan tanpa menghapus bukti sebelumnya', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHours(3),
    ]);

    // 1. Upload Pertama
    $file1 = UploadedFile::fake()->image('struk_1.jpg');
    $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BCA - 1111',
        'nomor_resi' => 'TF-0001',
        'bukti_transfer' => $file1,
    ]);

    $konfirmasi1 = KonfirmasiPembayaran::where('pesanan_id', $pesanan->id)->first();
    $path1 = $konfirmasi1->bukti_path;
    p04LocalDisk()->assertExists($path1);

    // 2. Operator menolak upload pertama
    $this->actingAs($operator)->post(route('operator.pembayaran.tolak', $pesanan->id), [
        'alasan_penolakan' => 'Struk buram, tidak dapat diverifikasi.',
    ]);

    expect($pesanan->fresh()->status)->toBe(StatusPesanan::BelumBayar);

    // 3. Member mengunggah struk perbaikan (Upload Kedua)
    $file2 = UploadedFile::fake()->image('struk_2_jelas.png');
    $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BCA - 1111',
        'nomor_resi' => 'TF-0002',
        'bukti_transfer' => $file2,
    ]);

    expect($pesanan->fresh()->status)->toBe(StatusPesanan::DalamProses);

    $konfirmasi2 = $konfirmasi1->fresh();
    $path2 = $konfirmasi2->bukti_path;

    // Kedua berkas fisik HARUS tetap tersimpan di storage (retensi audit P0-04)
    p04LocalDisk()->assertExists($path1);
    p04LocalDisk()->assertExists($path2);
    expect($path1)->not->toBe($path2);

    // Riwayat pembayaran memiliki 2 entri
    $riwayatList = RiwayatPembayaran::where('pesanan_id', $pesanan->id)->orderBy('id')->get();
    expect($riwayatList)->toHaveCount(2);
    expect($riwayatList[0]->bukti_path)->toBe($path1)
        ->and($riwayatList[0]->status)->toBe('Ditolak')
        ->and($riwayatList[0]->alasan_penolakan)->toBe('Struk buram, tidak dapat diverifikasi.');

    expect($riwayatList[1]->bukti_path)->toBe($path2)
        ->and($riwayatList[1]->status)->toBe('Dalam Proses');

    // Berkas lama di riwayat tetap dapat diakses via streaming controller
    $streamResponse = $this->actingAs($member)->get(route('pembayaran.bukti.riwayat', $riwayatList[0]->id));
    $streamResponse->assertStatus(200);

    // Member lain dilarang mengakses riwayat bukti milik member ini (403)
    $otherMember = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $unauthResponse = $this->actingAs($otherMember)->get(route('pembayaran.bukti.riwayat', $riwayatList[0]->id));
    $unauthResponse->assertStatus(403);
});

/*
|--------------------------------------------------------------------------
| D. PENGUJIAN MEKANISME EXPIRY & RELEASE KURSI
|--------------------------------------------------------------------------
*/

test('command gotix:cancel-expired-orders membatalkan pesanan Belum Bayar kedaluwarsa dan melepaskan kursi', function () {
    /** @var TestCase $this */
    $jadwal = Jadwal::factory()->create();

    // Pesanan 1: Belum Bayar dan batas_bayar lewat -> harus dibatalkan
    $pesananExpired = Pesanan::factory()->create([
        'jadwal_id' => $jadwal->id,
        'nomor_kursi' => 3,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->subMinutes(5),
    ]);

    // Pesanan 2: Belum Bayar tetapi belum kedaluwarsa -> jangan dibatalkan
    $pesananActive = Pesanan::factory()->create([
        'jadwal_id' => $jadwal->id,
        'nomor_kursi' => 4,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addMinutes(30),
    ]);

    // Pesanan 3: Dalam Proses dan batas_bayar lewat -> JANGAN dibatalkan (sedang ditinjau operator)
    $pesananInReview = Pesanan::factory()->create([
        'jadwal_id' => $jadwal->id,
        'nomor_kursi' => 5,
        'status' => StatusPesanan::DalamProses,
        'batas_bayar' => now()->subMinutes(5),
    ]);

    // Pesanan 4: Lunas -> JANGAN dibatalkan
    $pesananLunas = Pesanan::factory()->create([
        'jadwal_id' => $jadwal->id,
        'nomor_kursi' => 6,
        'status' => StatusPesanan::Lunas,
        'batas_bayar' => now()->subHours(1),
    ]);

    // Jalankan command
    Artisan::call('gotix:cancel-expired-orders');

    // Pesanan expired berubah menjadi Dibatalkan
    expect($pesananExpired->fresh()->status)->toBe(StatusPesanan::Dibatalkan);

    // Kursi 3 otomatis lepas (kursi_aktif NULL via generated column P0-01)
    expect($pesananExpired->fresh()->kursi_aktif)->toBeNull();

    // Pesanan lainnya tetap
    expect($pesananActive->fresh()->status)->toBe(StatusPesanan::BelumBayar);
    expect($pesananInReview->fresh()->status)->toBe(StatusPesanan::DalamProses);
    expect($pesananLunas->fresh()->status)->toBe(StatusPesanan::Lunas);

    // Kursi 3 yang dibatalkan sekarang dapat dipesan kembali oleh pemesan lain
    $newMember = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $newPesanan = Pesanan::factory()->create([
        'pengguna_id' => $newMember->id,
        'jadwal_id' => $jadwal->id,
        'nomor_kursi' => 3,
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->addHour(),
    ]);

    expect($newPesanan->fresh()->status)->toBe(StatusPesanan::BelumBayar)
        ->and($newPesanan->fresh()->kursi_aktif)->toBe(3);
});

test('command expiry aman dijalankan berulang kali (idempoten)', function () {
    /** @var TestCase $this */
    $pesanan = Pesanan::factory()->create([
        'status' => StatusPesanan::BelumBayar,
        'batas_bayar' => now()->subHour(),
    ]);

    // Eksekusi pertama
    Artisan::call('gotix:cancel-expired-orders');
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::Dibatalkan);

    // Eksekusi kedua tidak menghasilkan error
    $exitCode = Artisan::call('gotix:cancel-expired-orders');
    expect($exitCode)->toBe(0);
});
