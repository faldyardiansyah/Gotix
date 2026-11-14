<?php

use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('1. admin dapat membuka halaman daftar dan detail pengguna dengan username tampil benar', function () {
    $admin = Pengguna::factory()->create([
        'username' => 'admin_utama',
        'peran' => PeranPengguna::Admin,
    ]);

    $member = Pengguna::factory()->create([
        'nama' => 'Budi Santoso',
        'username' => 'budisantoso',
        'peran' => PeranPengguna::Member,
    ]);

    // Uji Halaman Index Pengguna
    $responseIndex = actingAs($admin)->get(route('admin.pengguna.index'));
    $responseIndex->assertOk();
    $responseIndex->assertSee('Budi Santoso');
    $responseIndex->assertSee('@budisantoso');
    $responseIndex->assertDontSee('{{ $pengguna->username }}');

    // Uji Halaman Detail (Show) Pengguna
    $responseShow = actingAs($admin)->get(route('admin.pengguna.show', $member->id));
    $responseShow->assertOk();
    $responseShow->assertSee('Budi Santoso');
    $responseShow->assertSee('@budisantoso');
    $responseShow->assertDontSee('{{ $pengguna->username }}');
});

test('2. halaman admin tidak lagi menampilkan navigasi duplikat _nav', function () {
    $admin = Pengguna::factory()->create([
        'peran' => PeranPengguna::Admin,
    ]);

    // Uji beberapa halaman utama admin
    $routes = [
        route('admin.dasbor'),
        route('admin.pengguna.index'),
        route('admin.jadwal.index'),
        route('admin.kota.index'),
        route('admin.kendaraan.index'),
        route('admin.pesanan.index'),
        route('admin.keluhan.index'),
    ];

    foreach ($routes as $route) {
        $response = actingAs($admin)->get($route);
        $response->assertOk();
        // Memastikan tidak ada duplikat header ringkas atau bar tombol quick nav _nav
        $response->assertDontSee('Panel Kontrol &amp; Master Data Lampung');
        $response->assertDontSee('Ringkasan Dasbor');
    }
});

test('3. seluruh halaman admin tidak lagi menampilkan footer', function () {
    $admin = Pengguna::factory()->create([
        'peran' => PeranPengguna::Admin,
    ]);

    $routes = [
        route('admin.dasbor'),
        route('admin.pengguna.index'),
        route('admin.jadwal.index'),
        route('admin.kota.index'),
        route('admin.kendaraan.index'),
        route('admin.pesanan.index'),
        route('admin.keluhan.index'),
    ];

    foreach ($routes as $route) {
        $response = actingAs($admin)->get($route);
        $response->assertOk();
        $response->assertDontSee('GoTix Travel Indonesia &bull; Panel Manajemen Sistem');
        $response->assertDontSee('GoTix. Hak cipta dilindungi.');
    }
});

test('4. admin dapat mengakses form tambah pengguna dan form tampil di tengah (mx-auto)', function () {
    $admin = Pengguna::factory()->create([
        'peran' => PeranPengguna::Admin,
    ]);

    $response = actingAs($admin)->get(route('admin.pengguna.create'));

    $response->assertOk();
    $response->assertSee('Tambah Pengguna Baru');
    $response->assertSee('Form Data Pengguna Baru');
    $response->assertSee('Simpan Pengguna');
    $response->assertSee('max-w-2xl mx-auto');

    // Pastikan seluruh menu aksi tambah (kendaraan, kota, jadwal) berposisi di tengah (mx-auto)
    actingAs($admin)->get(route('admin.kendaraan.create'))->assertSee('mx-auto');
    actingAs($admin)->get(route('admin.kota.create'))->assertSee('mx-auto');
    actingAs($admin)->get(route('admin.jadwal.create'))->assertSee('mx-auto');
});

test('5. admin dapat membuat akun Member dan Operator baru dengan password ter-hash', function () {
    $admin = Pengguna::factory()->create([
        'peran' => PeranPengguna::Admin,
    ]);

    // Buat akun Member
    $resMember = actingAs($admin)->post(route('admin.pengguna.store'), [
        'nama' => 'Member Baru Admin',
        'username' => 'memberadmin1',
        'email' => 'memberadmin1@gotix.test',
        'telepon' => '081234567801',
        'alamat' => 'Jl. Kartini No. 12, Bandar Lampung',
        'jenis_kelamin' => 'L',
        'peran' => 'Member',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $resMember->assertRedirect(route('admin.pengguna.index'));
    $resMember->assertSessionHas('sukses');

    $createdMember = Pengguna::where('username', 'memberadmin1')->first();
    expect($createdMember)->not->toBeNull()
        ->and($createdMember->nama)->toBe('Member Baru Admin')
        ->and($createdMember->peran)->toBe(PeranPengguna::Member)
        ->and(Hash::check('password123', $createdMember->password))->toBeTrue();

    // Buat akun Operator Loket
    $resOperator = actingAs($admin)->post(route('admin.pengguna.store'), [
        'nama' => 'Petugas Loket Baru',
        'username' => 'operatorloket1',
        'email' => 'operatorloket1@gotix.test',
        'telepon' => '081234567802',
        'alamat' => 'Jl. Teuku Umar No. 88, Bandar Lampung',
        'jenis_kelamin' => 'P',
        'peran' => 'Operator',
        'password' => 'operatorpass123',
        'password_confirmation' => 'operatorpass123',
    ]);

    $resOperator->assertRedirect(route('admin.pengguna.index'));
    $resOperator->assertSessionHas('sukses');

    $createdOperator = Pengguna::where('username', 'operatorloket1')->first();
    expect($createdOperator)->not->toBeNull()
        ->and($createdOperator->nama)->toBe('Petugas Loket Baru')
        ->and($createdOperator->peran)->toBe(PeranPengguna::Operator)
        ->and(Hash::check('operatorpass123', $createdOperator->password))->toBeTrue();
});

test('6. pembuatan akun dengan peran di luar Member dan Operator ditolak (misal peran Admin)', function () {
    $admin = Pengguna::factory()->create([
        'peran' => PeranPengguna::Admin,
    ]);

    $response = actingAs($admin)->post(route('admin.pengguna.store'), [
        'nama' => 'Injected Admin',
        'username' => 'injectedadmin',
        'email' => 'injected@gotix.test',
        'telepon' => '081234567803',
        'alamat' => 'Jl. Ilegal No. 1',
        'jenis_kelamin' => 'L',
        'peran' => 'Admin', // Tidak diperkenankan
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('peran');
    expect(Pengguna::where('username', 'injectedadmin')->exists())->toBeFalse();
});

test('7. pembuatan pengguna gagal jika username atau email duplikat', function () {
    $admin = Pengguna::factory()->create([
        'username' => 'admin_super',
        'email' => 'super@gotix.test',
        'peran' => PeranPengguna::Admin,
    ]);

    Pengguna::factory()->create([
        'username' => 'existinguser',
        'email' => 'existing@gotix.test',
    ]);

    $response = actingAs($admin)->post(route('admin.pengguna.store'), [
        'nama' => 'Duplikat User',
        'username' => 'existinguser',
        'email' => 'existing@gotix.test',
        'telepon' => '081234567804',
        'alamat' => 'Jl. Uji No. 9',
        'jenis_kelamin' => 'L',
        'peran' => 'Member',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['username', 'email']);
});

test('8. pembuatan pengguna gagal jika password confirmation tidak cocok', function () {
    $admin = Pengguna::factory()->create([
        'peran' => PeranPengguna::Admin,
    ]);

    $response = actingAs($admin)->post(route('admin.pengguna.store'), [
        'nama' => 'Mismatch Pass',
        'username' => 'mismatchpass',
        'telepon' => '081234567805',
        'alamat' => 'Jl. Uji No. 10',
        'jenis_kelamin' => 'L',
        'peran' => 'Member',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ]);

    $response->assertSessionHasErrors('password');
});

test('9. non-admin (tamu, member, operator) dilarang mengakses fitur tambah pengguna', function () {
    $member = Pengguna::factory()->create(['peran' => PeranPengguna::Member]);
    $operator = Pengguna::factory()->create(['peran' => PeranPengguna::Operator]);

    // Tamu (Guest)
    get(route('admin.pengguna.create'))->assertRedirect(route('masuk'));
    post(route('admin.pengguna.store'), [])->assertRedirect(route('masuk'));

    // Member (403)
    actingAs($member)->get(route('admin.pengguna.create'))->assertForbidden();
    actingAs($member)->post(route('admin.pengguna.store'), [])->assertForbidden();

    // Operator (403)
    actingAs($operator)->get(route('admin.pengguna.create'))->assertForbidden();
    actingAs($operator)->post(route('admin.pengguna.store'), [])->assertForbidden();
});

test('10. password tidak pernah ditampilkan pada daftar dan detail pengguna', function () {
    $admin = Pengguna::factory()->create([
        'peran' => PeranPengguna::Admin,
    ]);

    $user = Pengguna::factory()->create([
        'nama' => 'Rahasia Pengguna',
        'username' => 'rahasia123',
        'password' => Hash::make('supersecretpassword999'),
        'peran' => PeranPengguna::Member,
    ]);

    $indexRes = actingAs($admin)->get(route('admin.pengguna.index'));
    $indexRes->assertOk();
    $indexRes->assertDontSee('supersecretpassword999');

    $showRes = actingAs($admin)->get(route('admin.pengguna.show', $user->id));
    $showRes->assertOk();
    $showRes->assertDontSee('supersecretpassword999');
});
