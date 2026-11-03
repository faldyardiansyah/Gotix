<?php

// Pest test untuk Custom Auth Manual
// Tahap 2 — autentikasi dan proteksi route per peran

use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * Helper: buat pengguna sesuai peran yang ditentukan.
 */
function buatPengguna(PeranPengguna $peran = PeranPengguna::Member): Pengguna
{
    return Pengguna::factory()->create([
        'username' => match ($peran) {
            PeranPengguna::Admin => 'testadmin',
            PeranPengguna::Operator => 'testoperator',
            default => 'testmember',
        },
        'email' => match ($peran) {
            PeranPengguna::Admin => 'testadmin@gotix.test',
            PeranPengguna::Operator => 'testoperator@gotix.test',
            default => 'testmember@gotix.test',
        },
        'peran' => $peran,
        'password' => Hash::make('password123'),
    ]);
}

// ─────────────────────────────────────────────────────────────────
// Test Login
// ─────────────────────────────────────────────────────────────────

test('member dapat masuk menggunakan username', function () {
    buatPengguna(PeranPengguna::Member);

    $response = $this->post(route('masuk.store'), [
        'login' => 'testmember',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('member.tiket.index'));
    $this->assertAuthenticated();
});

test('member dapat masuk menggunakan email', function () {
    buatPengguna(PeranPengguna::Member);

    $response = $this->post(route('masuk.store'), [
        'login' => 'testmember@gotix.test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('member.tiket.index'));
    $this->assertAuthenticated();
});

test('admin diarahkan ke dasbor admin setelah masuk', function () {
    buatPengguna(PeranPengguna::Admin);

    $response = $this->post(route('masuk.store'), [
        'login' => 'testadmin',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('admin.dasbor'));
});

test('operator diarahkan ke halaman pembayaran setelah masuk', function () {
    buatPengguna(PeranPengguna::Operator);

    $response = $this->post(route('masuk.store'), [
        'login' => 'testoperator',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('operator.pembayaran.index'));
});

test('login gagal jika password salah', function () {
    buatPengguna(PeranPengguna::Member);

    $response = $this->post(route('masuk.store'), [
        'login' => 'testmember',
        'password' => 'passwordsalah',
    ]);

    $response->assertSessionHasErrors('login');
    $this->assertGuest();
});

test('login gagal jika username tidak ditemukan', function () {
    $response = $this->post(route('masuk.store'), [
        'login' => 'tidakada',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('login');
    $this->assertGuest();
});

// ─────────────────────────────────────────────────────────────────
// Test Pendaftaran
// ─────────────────────────────────────────────────────────────────

test('member baru dapat mendaftar dan otomatis masuk', function () {
    $response = $this->post(route('daftar.store'), [
        'nama' => 'Pengguna Baru',
        'username' => 'penggunabaru',
        'email' => 'baru@gotix.test',
        'telepon' => '081234567899',
        'alamat' => 'Jl. Test No. 1, Bandar Lampung',
        'jenis_kelamin' => 'L',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('member.tiket.index'));
    $this->assertAuthenticated();

    $this->assertDatabaseHas('pengguna', [
        'username' => 'penggunabaru',
        'peran' => PeranPengguna::Member->value,
    ]);
});

test('pendaftaran gagal jika username sudah dipakai', function () {
    buatPengguna(); // buat testmember

    $response = $this->post(route('daftar.store'), [
        'nama' => 'Pengguna Duplikat',
        'username' => 'testmember',
        'telepon' => '081234567899',
        'alamat' => 'Jl. Test No. 2',
        'jenis_kelamin' => 'P',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

// ─────────────────────────────────────────────────────────────────
// Test Logout
// ─────────────────────────────────────────────────────────────────

test('pengguna yang masuk dapat keluar', function () {
    $pengguna = buatPengguna();

    $response = $this->actingAs($pengguna)->post(route('keluar'));

    $response->assertRedirect(route('masuk'));
    $this->assertGuest();
});

// ─────────────────────────────────────────────────────────────────
// Test Proteksi Route per Peran
// ─────────────────────────────────────────────────────────────────

test('tamu dialihkan ke halaman masuk jika mencoba akses route auth', function () {
    $this->get(route('member.tiket.index'))->assertRedirect(route('masuk'));
    $this->get(route('operator.pembayaran.index'))->assertRedirect(route('masuk'));
    $this->get(route('admin.dasbor'))->assertRedirect(route('masuk'));
});

test('member tidak bisa mengakses route operator (403)', function () {
    $member = buatPengguna(PeranPengguna::Member);

    $this->actingAs($member)
        ->get(route('operator.pembayaran.index'))
        ->assertForbidden();
});

test('member tidak bisa mengakses route admin (403)', function () {
    $member = buatPengguna(PeranPengguna::Member);

    $this->actingAs($member)
        ->get(route('admin.dasbor'))
        ->assertForbidden();
});

test('operator tidak bisa mengakses route admin (403)', function () {
    $operator = buatPengguna(PeranPengguna::Operator);

    $this->actingAs($operator)
        ->get(route('admin.dasbor'))
        ->assertForbidden();
});

test('operator tidak bisa mengakses route member (403)', function () {
    $operator = buatPengguna(PeranPengguna::Operator);

    $this->actingAs($operator)
        ->get(route('member.tiket.index'))
        ->assertForbidden();
});

test('admin tidak bisa mengakses route member (403)', function () {
    $admin = buatPengguna(PeranPengguna::Admin);

    $this->actingAs($admin)
        ->get(route('member.tiket.index'))
        ->assertForbidden();
});

test('admin dapat mengakses dasbor admin (200)', function () {
    $admin = buatPengguna(PeranPengguna::Admin);

    $this->actingAs($admin)
        ->get(route('admin.dasbor'))
        ->assertOk();
});

test('operator dapat mengakses halaman pembayaran operator (200)', function () {
    $operator = buatPengguna(PeranPengguna::Operator);

    $this->actingAs($operator)
        ->get(route('operator.pembayaran.index'))
        ->assertOk();
});

test('member dapat mengakses halaman tiket member (200)', function () {
    $member = buatPengguna(PeranPengguna::Member);

    $this->actingAs($member)
        ->get(route('member.tiket.index'))
        ->assertOk();
});
