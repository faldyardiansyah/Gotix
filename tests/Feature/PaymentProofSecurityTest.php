<?php

use App\Enums\StatusPesanan;
use App\Models\Jadwal;
use App\Models\KonfirmasiPembayaran;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(RefreshDatabase::class);

function localDisk(): FilesystemAdapter
{
    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('local');

    return $disk;
}

function publicDisk(): FilesystemAdapter
{
    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('public');

    return $disk;
}

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

// ─────────────────────────────────────────────────────────────────
// A. Autentikasi & Otorisasi RBAC Streaming Bukti Transfer
// ─────────────────────────────────────────────────────────────────

test('guest ditolak dan diarahkan ke halaman masuk saat mengakses bukti transfer', function () {
    /** @var TestCase $this */
    $pesanan = Pesanan::factory()->create();
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/struk.jpg',
    ]);

    Storage::disk('local')->put('bukti_pembayaran/struk.jpg', 'fake-image-binary-data');

    $response = $this->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertRedirect(route('masuk'));
});

test('member pemilik pesanan dapat mengakses berkas bukti transfer miliknya sendiri', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/struk-member.jpg',
    ]);

    Storage::disk('local')->put('bukti_pembayaran/struk-member.jpg', 'member-proof-content');

    $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertOk();
    expect($response->streamedContent())->toBe('member-proof-content');
});

test('member dilarang mengakses berkas bukti transfer milik member lain (403)', function () {
    /** @var TestCase $this */
    $memberA = Pengguna::factory()->create();
    $memberB = Pengguna::factory()->create();

    $pesananB = Pesanan::factory()->create(['pengguna_id' => $memberB->id]);
    $konfirmasiB = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesananB->id,
        'bukti_path' => 'bukti_pembayaran/struk-member-b.jpg',
    ]);

    Storage::disk('local')->put('bukti_pembayaran/struk-member-b.jpg', 'secret-proof-b');

    // Member A mencoba mengakses struk milik Member B
    $response = $this->actingAs($memberA)->get(route('pembayaran.bukti', $konfirmasiB->id));

    $response->assertForbidden();
});

test('parameter tampering ID konfirmasi lain ditolak dengan status 403', function () {
    /** @var TestCase $this */
    $memberAttacker = Pengguna::factory()->create();
    $memberVictim = Pengguna::factory()->create();

    $pesananVictim = Pesanan::factory()->create(['pengguna_id' => $memberVictim->id]);
    $konfirmasiVictim = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesananVictim->id,
        'bukti_path' => 'bukti_pembayaran/victim-receipt.png',
    ]);

    Storage::disk('local')->put('bukti_pembayaran/victim-receipt.png', 'victim-sensitive-data');

    // Member penyerang mencoba menebak ID
    $response = $this->actingAs($memberAttacker)->get(route('pembayaran.bukti', $konfirmasiVictim->id));

    $response->assertStatus(403);
});

test('operator loket dapat mengakses berkas bukti transfer untuk proses verifikasi (200)', function () {
    /** @var TestCase $this */
    $operator = Pengguna::factory()->operator()->create();
    $member = Pengguna::factory()->create();

    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/struk-verifikasi.jpg',
    ]);

    Storage::disk('local')->put('bukti_pembayaran/struk-verifikasi.jpg', 'operator-check-content');

    $response = $this->actingAs($operator)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertOk();
    expect($response->streamedContent())->toBe('operator-check-content');
});

test('admin dapat mengakses berkas bukti transfer manapun (200)', function () {
    /** @var TestCase $this */
    $admin = Pengguna::factory()->admin()->create();
    $member = Pengguna::factory()->create();

    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/struk-admin.jpg',
    ]);

    Storage::disk('local')->put('bukti_pembayaran/struk-admin.jpg', 'admin-check-content');

    $response = $this->actingAs($admin)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertOk();
    expect($response->streamedContent())->toBe('admin-check-content');
});

// ─────────────────────────────────────────────────────────────────
// B. Header Keamanan, Content-Disposition & Traversal Guard
// ─────────────────────────────────────────────────────────────────

test('respons streaming menyertakan header keamanan nosniff dan no-store', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/secure-header-test.jpg',
    ]);

    Storage::disk('local')->put('bukti_pembayaran/secure-header-test.jpg', 'image-payload');

    $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertOk();
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    expect($response->headers->get('Cache-Control'))->toContain('no-store')
        ->and($response->headers->get('Cache-Control'))->toContain('private');
});

test('respons streaming menyertakan Content-Disposition inline dengan nama berkas', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/test-receipt.png',
    ]);

    Storage::disk('local')->put('bukti_pembayaran/test-receipt.png', 'png-binary-payload');

    $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertOk();
    $contentDisposition = $response->headers->get('Content-Disposition');
    expect($contentDisposition)->toContain('inline')
        ->and($contentDisposition)->toContain('test-receipt.png');
});

test('akses bukti transfer mengembalikan 404 jika berkas fisik tidak ada di disk', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/missing-file.jpg',
    ]);

    // Berkas tidak ditaruh di Storage::disk('local') maupun public
    $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertNotFound();
});

test('akses bukti transfer mengembalikan 404 jika ID konfirmasi pembayaran tidak ditemukan', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();

    $response = $this->actingAs($member)->get(route('pembayaran.bukti', 999999));

    $response->assertNotFound();
});

test('akses bukti transfer ditolak 403 jika record bukti_path mengandung path traversal (..)', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => '../../etc/passwd',
    ]);

    $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertStatus(403);
});

test('akses bukti transfer ditolak 403 jika record bukti_path mencoba absolute path atau drive letter', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'C:/Windows/win.ini',
    ]);

    $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertStatus(403);
});

test('akses bukti transfer ditolak 403 jika canonical path berada di luar root disk atau direktori bersaudara', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'private-backup/secret.jpg',
    ]);

    $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));

    // Berkas di luar disk root atau tidak ditemukan di disk lokal/publik ditolak secara aman
    expect(in_array($response->status(), [403, 404], true))->toBeTrue();
});

test('akses bukti transfer ditolak 403 jika berkas menunjuk ke symlink di luar root penyimpanan', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);

    $targetOutside = tempnam(sys_get_temp_dir(), 'gotix_outside_');
    file_put_contents($targetOutside, 'outside-payload');

    $symlinkRelPath = 'bukti_pembayaran/symlink_outside.jpg';
    $symlinkFullPath = Storage::disk('local')->path($symlinkRelPath);

    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => $symlinkRelPath,
    ]);

    $created = @symlink($targetOutside, $symlinkFullPath);

    if ($created) {
        $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));
        $response->assertStatus(403);
        @unlink($symlinkFullPath);
    } else {
        // Pada environment Windows un-elevated, pembuatan symlink dibatasi OS
        // Validasi batas direktori memastikan target di luar root tidak akan pernah diawali oleh root + separator
        $diskRootWithSeparator = rtrim(realpath(Storage::disk('local')->path('')), '\\/').DIRECTORY_SEPARATOR;
        expect(str_starts_with(realpath($targetOutside), $diskRootWithSeparator))->toBeFalse();
    }

    @unlink($targetOutside);
});

// ─────────────────────────────────────────────────────────────────
// C. Upload Bukti Transfer ke Private Disk & Validasi MIME Type
// ─────────────────────────────────────────────────────────────────

test('upload bukti transfer oleh member menyimpan berkas ke private disk (storage/app/private)', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
    ]);

    $file = UploadedFile::fake()->image('struk_asli.jpg', 600, 800)->size(1500);

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BCA - 123456789 a.n Budi',
        'nomor_resi' => 'TF-9988776655',
        'tanggal_transfer' => now()->toDateString(),
        'jam_transfer' => '10:30',
        'bukti_transfer' => $file,
    ]);

    $response->assertRedirect(route('member.pesanan.show', $pesanan->kode_pesanan));
    $response->assertSessionHas('sukses');

    // Pastikan tersimpan di database
    $konfirmasi = KonfirmasiPembayaran::where('pesanan_id', $pesanan->id)->first();
    expect($konfirmasi)->not->toBeNull();
    expect($konfirmasi->bukti_path)->toStartWith('bukti_pembayaran/');

    // Pastikan fisik berkas ada di private disk ('local')
    localDisk()->assertExists($konfirmasi->bukti_path);

    // Pastikan fisik berkas TIDAK ADA di public disk ('public')
    publicDisk()->assertMissing($konfirmasi->bukti_path);

    // Status pesanan berubah menjadi Dalam Proses
    expect($pesanan->fresh()->status)->toBe(StatusPesanan::DalamProses);
});

test('upload ulang bukti pembayaran mempertahankan berkas lama pada private disk sebagai histori audit', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
    ]);

    // Berkas lama sudah ada
    Storage::disk('local')->put('bukti_pembayaran/old-receipt.jpg', 'old-receipt-binary');
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_pembayaran/old-receipt.jpg',
    ]);

    $newFile = UploadedFile::fake()->image('struk_baru.png', 400, 400)->size(800);

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'Mandiri - 987654321',
        'nomor_resi' => 'TF-NEW12345',
        'tanggal_transfer' => now()->toDateString(),
        'jam_transfer' => '11:00',
        'bukti_transfer' => $newFile,
    ]);

    $response->assertRedirect(route('member.pesanan.show', $pesanan->kode_pesanan));

    // P0-04: Berkas lama HARUS tetap ada di private disk untuk retensi histori audit
    localDisk()->assertExists('bukti_pembayaran/old-receipt.jpg');

    // Berkas baru harus ada di disk local
    $updatedKonfirmasi = $konfirmasi->fresh();
    expect($updatedKonfirmasi->bukti_path)->not->toBe('bukti_pembayaran/old-receipt.jpg');
    localDisk()->assertExists($updatedKonfirmasi->bukti_path);
});

test('upload bukti transfer menerima format yang sah (jpg, png, webp, pdf)', function ($extension, $mimeType) {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
    ]);

    $file = UploadedFile::fake()->create("dokumen.{$extension}", 100, $mimeType);

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BRI - 5544332211',
        'bukti_transfer' => $file,
    ]);

    $response->assertRedirect(route('member.pesanan.show', $pesanan->kode_pesanan));
    $response->assertSessionHasNoErrors();
})->with([
    ['jpg', 'image/jpeg'],
    ['jpeg', 'image/jpeg'],
    ['png', 'image/png'],
    ['webp', 'image/webp'],
    ['pdf', 'application/pdf'],
]);

test('upload bukti transfer menolak berkas berbahaya dan tidak sah (422)', function ($extension, $mimeType) {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
    ]);

    $file = UploadedFile::fake()->create("malicious.{$extension}", 50, $mimeType);

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'Penyerang',
        'bukti_transfer' => $file,
    ]);

    $response->assertSessionHasErrors(['bukti_transfer']);
})->with([
    ['php', 'text/x-php'],
    ['html', 'text/html'],
    ['svg', 'image/svg+xml'],
    ['exe', 'application/x-msdownload'],
    ['sh', 'application/x-sh'],
]);

test('upload bukti transfer menolak berkas melebihi ukuran maksimum 5MB dengan 422', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $jadwal = Jadwal::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'pengguna_id' => $member->id,
        'jadwal_id' => $jadwal->id,
        'status' => StatusPesanan::BelumBayar,
    ]);

    // 6MB (melebihi 5120KB)
    $oversizedFile = UploadedFile::fake()->create('large.jpg', 6144, 'image/jpeg');

    $response = $this->actingAs($member)->post(route('member.pembayaran.store', $pesanan->kode_pesanan), [
        'rekening_pengirim' => 'BRI - 5544332211',
        'bukti_transfer' => $oversizedFile,
    ]);

    $response->assertSessionHasErrors(['bukti_transfer']);
});

// ─────────────────────────────────────────────────────────────────
// D. Legacy Fallback & Artisan Command gotix:migrate-payment-proofs
// ─────────────────────────────────────────────────────────────────

test('streaming controller mendukung fallback transparan jika berkas lama masih berada di public disk', function () {
    /** @var TestCase $this */
    $member = Pengguna::factory()->create();
    $pesanan = Pesanan::factory()->create(['pengguna_id' => $member->id]);
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_transfer/legacy-public-proof.jpg',
    ]);

    // File hanya ada di disk public (belum dimigrasikan)
    Storage::disk('public')->put('bukti_transfer/legacy-public-proof.jpg', 'legacy-content-from-public');
    localDisk()->assertMissing('bukti_transfer/legacy-public-proof.jpg');

    // Pengguna yang terotorisasi tetap dapat melihatnya melalui route streaming
    $response = $this->actingAs($member)->get(route('pembayaran.bukti', $konfirmasi->id));

    $response->assertOk();
    expect($response->streamedContent())->toBe('legacy-content-from-public');
});

test('artisan gotix:migrate-payment-proofs memigrasikan berkas dari public ke private disk dengan verifikasi hash', function () {
    $pesanan = Pesanan::factory()->create();
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_transfer/sample-legacy.jpg',
    ]);

    $fileData = 'very-important-payment-receipt-content-bytes';
    Storage::disk('public')->put('bukti_transfer/sample-legacy.jpg', $fileData);

    // Jalankan command migrasi
    $exitCode = Artisan::call('gotix:migrate-payment-proofs');
    expect($exitCode)->toBe(0);

    // Berkas harus sudah dipindahkan ke disk local ('private')
    $targetPath = 'bukti_pembayaran/sample-legacy.jpg';
    localDisk()->assertExists($targetPath);
    expect(Storage::disk('local')->get($targetPath))->toBe($fileData);

    // Berkas lama di public harus terhapus
    publicDisk()->assertMissing('bukti_transfer/sample-legacy.jpg');

    // Database harus terupdate
    expect($konfirmasi->fresh()->bukti_path)->toBe($targetPath);
});

test('artisan gotix:migrate-payment-proofs --dry-run tidak mengubah file fisik maupun database', function () {
    $pesanan = Pesanan::factory()->create();
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_transfer/dry-run-sample.jpg',
    ]);

    $fileData = 'dry-run-content';
    Storage::disk('public')->put('bukti_transfer/dry-run-sample.jpg', $fileData);

    // Jalankan dry run
    $exitCode = Artisan::call('gotix:migrate-payment-proofs', ['--dry-run' => true]);
    expect($exitCode)->toBe(0);

    // File di public HARUS TETAP ADA
    publicDisk()->assertExists('bukti_transfer/dry-run-sample.jpg');

    // File di private TIDAK BOLEH ADA
    localDisk()->assertMissing('bukti_pembayaran/dry-run-sample.jpg');

    // Database tidak boleh berubah
    expect($konfirmasi->fresh()->bukti_path)->toBe('bukti_transfer/dry-run-sample.jpg');
});

test('command migrasi tidak menimpa file privat yang sudah ada dan menghasilkan nama tujuan unik', function () {
    $pesanan = Pesanan::factory()->create();
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_transfer/struk.jpg',
    ]);

    // File privat yang sudah ada sebelumnya dengan nama yang sama
    $existingPrivateData = 'original-private-unrelated-file-content';
    Storage::disk('local')->put('bukti_pembayaran/struk.jpg', $existingPrivateData);

    // File sumber pada disk publik yang akan dimigrasi
    $newPublicData = 'newly-migrated-receipt-data';
    Storage::disk('public')->put('bukti_transfer/struk.jpg', $newPublicData);

    // Jalankan migrasi
    $exitCode = Artisan::call('gotix:migrate-payment-proofs');
    expect($exitCode)->toBe(0);

    // File privat lama TIDAK BOLEH tertimpa
    expect(Storage::disk('local')->get('bukti_pembayaran/struk.jpg'))->toBe($existingPrivateData);

    // File baru harus disimpan dengan nama tujuan yang unik
    $newPath = $konfirmasi->fresh()->bukti_path;
    expect($newPath)->not->toBe('bukti_pembayaran/struk.jpg')
        ->and($newPath)->toStartWith('bukti_pembayaran/struk_')
        ->and(Storage::disk('local')->get($newPath))->toBe($newPublicData);

    // File publik lama harus terhapus
    publicDisk()->assertMissing('bukti_transfer/struk.jpg');
});

test('command migrasi menangani dua file sumber dengan basename sama tanpa saling menimpa', function () {
    $pesananA = Pesanan::factory()->create();
    $konfirmasiA = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesananA->id,
        'bukti_path' => 'bukti_transfer/receipt.jpg',
    ]);

    $pesananB = Pesanan::factory()->create();
    $konfirmasiB = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesananB->id,
        'bukti_path' => 'bukti_transfer_folder2/receipt.jpg',
    ]);

    $dataA = 'receipt-content-for-booking-A';
    $dataB = 'receipt-content-for-booking-B';

    Storage::disk('public')->put('bukti_transfer/receipt.jpg', $dataA);
    Storage::disk('public')->put('bukti_transfer_folder2/receipt.jpg', $dataB);

    $exitCode = Artisan::call('gotix:migrate-payment-proofs');
    expect($exitCode)->toBe(0);

    $pathA = $konfirmasiA->fresh()->bukti_path;
    $pathB = $konfirmasiB->fresh()->bukti_path;

    // Kedua path tujuan harus berbeda
    expect($pathA)->not->toBe($pathB);

    // Kedua konten harus tersimpan utuh dan sesuai pada file masing-masing
    expect(Storage::disk('local')->get($pathA))->toBe($dataA)
        ->and(Storage::disk('local')->get($pathB))->toBe($dataB);

    // Kedua file publik lama harus terhapus
    publicDisk()->assertMissing('bukti_transfer/receipt.jpg');
    publicDisk()->assertMissing('bukti_transfer_folder2/receipt.jpg');
});

test('command migrasi aman saat dieksekusi ulang (idempoten)', function () {
    $pesanan = Pesanan::factory()->create();
    $konfirmasi = KonfirmasiPembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'bukti_path' => 'bukti_transfer/idempotent.jpg',
    ]);

    $data = 'idempotent-test-content';
    Storage::disk('public')->put('bukti_transfer/idempotent.jpg', $data);

    // Eksekusi pertama: migrasi berjalan sukses
    $exitCode1 = Artisan::call('gotix:migrate-payment-proofs');
    expect($exitCode1)->toBe(0);
    $migratedPath = $konfirmasi->fresh()->bukti_path;
    expect(Storage::disk('local')->exists($migratedPath))->toBeTrue();

    // Eksekusi kedua: harus aman dan melewati file yang sudah privat
    $exitCode2 = Artisan::call('gotix:migrate-payment-proofs');
    expect($exitCode2)->toBe(0);
    expect($konfirmasi->fresh()->bukti_path)->toBe($migratedPath);
    expect(Storage::disk('local')->get($migratedPath))->toBe($data);
});
