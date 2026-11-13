<?php

use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    RateLimiter::clear('login:testmember|127.0.0.1');
    RateLimiter::clear('register:127.0.0.1');
});

/**
 * Helper: buat user untuk pengujian autentikasi.
 */
function buatAkunUji(string $username = 'testmember', string $email = 'testmember@gotix.test', string $password = 'password123'): Pengguna
{
    return Pengguna::factory()->create([
        'username' => $username,
        'email' => $email,
        'peran' => PeranPengguna::Member,
        'password' => Hash::make($password),
    ]);
}

/*
|--------------------------------------------------------------------------
| A. PENGUJIAN RATE LIMITING LOGIN
|--------------------------------------------------------------------------
*/

test('1. login berhasil dengan kredensial valid dan mengarahkan ke dashboard/tiket', function () {
    /** @var TestCase $this */
    buatAkunUji();

    $response = $this->post(route('masuk.store'), [
        'login' => 'testmember',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('member.tiket.index'));
    $this->assertAuthenticated();
});

test('2. login gagal dengan kredensial tidak valid mencatat hit limiter', function () {
    /** @var TestCase $this */
    buatAkunUji();

    $throttleKey = 'login:testmember|127.0.0.1';
    expect(RateLimiter::attempts($throttleKey))->toBe(0);

    $response = $this->post(route('masuk.store'), [
        'login' => 'testmember',
        'password' => 'passwordsalah',
    ]);

    $response->assertSessionHasErrors('login');
    $this->assertGuest();
    expect(RateLimiter::attempts($throttleKey))->toBe(1);
});

test('3. percobaan login gagal berturut-turut sampai batas maksimal yang ditentukan', function () {
    /** @var TestCase $this */
    buatAkunUji();

    $maxAttempts = (int) config('gotix.auth.login_max_attempts', 5);
    $throttleKey = 'login:testmember|127.0.0.1';

    for ($i = 1; $i <= $maxAttempts; $i++) {
        $response = $this->post(route('masuk.store'), [
            'login' => 'testmember',
            'password' => 'passwordsalah',
        ]);

        $response->assertSessionHasErrors('login');
        expect(RateLimiter::attempts($throttleKey))->toBe($i);
    }
});

test('4. request login berikutnya setelah melebihi batas ditolak dengan HTTP 429', function () {
    /** @var TestCase $this */
    buatAkunUji();

    $maxAttempts = (int) config('gotix.auth.login_max_attempts', 5);

    // Habiskan kuota percobaan gagal
    for ($i = 0; $i < $maxAttempts; $i++) {
        $this->post(route('masuk.store'), [
            'login' => 'testmember',
            'password' => 'passwordsalah',
        ]);
    }

    // Request ke-6 harus ditolak dengan status HTTP 429
    $response = $this->post(route('masuk.store'), [
        'login' => 'testmember',
        'password' => 'passwordsalah',
    ]);

    $response->assertStatus(429);
    $response->assertHeader('Retry-After');
    $response->assertSee('Terlalu banyak percobaan masuk yang gagal');
    $this->assertGuest();
});

test('5. login berhasil membersihkan hit limiter terkait', function () {
    /** @var TestCase $this */
    buatAkunUji();

    $throttleKey = 'login:testmember|127.0.0.1';

    // Buat 3 kegagalan
    for ($i = 0; $i < 3; $i++) {
        $this->post(route('masuk.store'), [
            'login' => 'testmember',
            'password' => 'passwordsalah',
        ]);
    }

    expect(RateLimiter::attempts($throttleKey))->toBe(3);

    // Login sukses dengan password benar
    $response = $this->post(route('masuk.store'), [
        'login' => 'testmember',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('member.tiket.index'));
    $this->assertAuthenticated();

    // Hit counter harus bersih kembali ke 0
    expect(RateLimiter::attempts($throttleKey))->toBe(0);
});

test('6. identitas login yang berbeda tidak tercampur kuota rate limit-nya', function () {
    /** @var TestCase $this */
    buatAkunUji('user_satu', 'user1@gotix.test');
    buatAkunUji('user_dua', 'user2@gotix.test');

    $maxAttempts = (int) config('gotix.auth.login_max_attempts', 5);

    // Kunci user_satu dengan 5 kegagalan
    for ($i = 0; $i < $maxAttempts; $i++) {
        $this->post(route('masuk.store'), [
            'login' => 'user_satu',
            'password' => 'passwordsalah',
        ]);
    }

    // user_satu harus ditolak 429
    $resp1 = $this->post(route('masuk.store'), [
        'login' => 'user_satu',
        'password' => 'passwordsalah',
    ]);
    $resp1->assertStatus(429);

    // user_dua masih bebas mencoba dan belum terkena limit
    $resp2 = $this->post(route('masuk.store'), [
        'login' => 'user_dua',
        'password' => 'password123',
    ]);
    $resp2->assertRedirect(route('member.tiket.index'));
});

test('7. alamat IP yang berbeda tidak tercampur kuota rate limit-nya', function () {
    /** @var TestCase $this */
    buatAkunUji('user_multi_ip', 'multi@gotix.test');

    $maxAttempts = (int) config('gotix.auth.login_max_attempts', 5);

    // Kunci dari IP 192.168.1.10
    for ($i = 0; $i < $maxAttempts; $i++) {
        $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.10'])->post(route('masuk.store'), [
            'login' => 'user_multi_ip',
            'password' => 'passwordsalah',
        ]);
    }

    // Dari IP 192.168.1.10 harus kena 429
    $blocked = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.10'])->post(route('masuk.store'), [
        'login' => 'user_multi_ip',
        'password' => 'passwordsalah',
    ]);
    $blocked->assertStatus(429);

    // Dari IP 10.0.0.1 masih bisa login normal
    $allowed = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])->post(route('masuk.store'), [
        'login' => 'user_multi_ip',
        'password' => 'password123',
    ]);
    $allowed->assertRedirect(route('member.tiket.index'));
});

test('8. normalisasi identitas bekerja konsisten (lowercase & trim)', function () {
    /** @var TestCase $this */
    buatAkunUji('akun_normalisasi', 'normalisasi@gotix.test');

    // Input dengan variasi huruf besar dan spasi di awal/akhir
    $this->post(route('masuk.store'), [
        'login' => '  Akun_Normalisasi  ',
        'password' => 'passwordsalah',
    ]);

    $this->post(route('masuk.store'), [
        'login' => 'AKUN_NORMALISASI',
        'password' => 'passwordsalah',
    ]);

    $this->post(route('masuk.store'), [
        'login' => 'akun_normalisasi ',
        'password' => 'passwordsalah',
    ]);

    $throttleKey = 'login:akun_normalisasi|127.0.0.1';
    expect(RateLimiter::attempts($throttleKey))->toBe(3);
});

test('9. respons kesalahan tidak membocorkan keberadaan akun', function () {
    /** @var TestCase $this */
    buatAkunUji('user_terdaftar', 'terdaftar@gotix.test');

    // Percobaan pada akun yang ADA tetapi password salah
    $respAda = $this->post(route('masuk.store'), [
        'login' => 'user_terdaftar',
        'password' => 'passwordsalah',
    ]);

    // Percobaan pada akun yang TIDAK ADA
    $respTidakAda = $this->post(route('masuk.store'), [
        'login' => 'user_tidak_terdaftar_sama_sekali',
        'password' => 'passwordrandom',
    ]);

    // Pesan kesalahan yang diterima user harus sama persis
    $errorAda = session('errors')->get('login')[0] ?? null;
    $respAda->assertSessionHasErrors('login');

    $this->post(route('masuk.store'), [
        'login' => 'user_tidak_terdaftar_sama_sekali',
        'password' => 'passwordrandom',
    ]);
    $errorTidakAda = session('errors')->get('login')[0] ?? null;

    expect($errorAda)->toBe('Kombinasi username/email dan kata sandi tidak cocok.');
    expect($errorTidakAda)->toBe('Kombinasi username/email dan kata sandi tidak cocok.');
});

/*
|--------------------------------------------------------------------------
| B. PENGUJIAN RATE LIMITING REGISTRASI
|--------------------------------------------------------------------------
*/

test('10. endpoint registrasi dibatasi per IP untuk mencegah serangan spam/bot', function () {
    /** @var TestCase $this */
    $maxAttempts = (int) config('gotix.auth.register_max_attempts', 5);

    // Lakukan registrasi berulang kali dari IP yang sama
    for ($i = 1; $i <= $maxAttempts; $i++) {
        $this->withServerVariables(['REMOTE_ADDR' => '172.16.0.5'])->post(route('daftar.store'), [
            'nama' => 'Spam User '.$i,
            'username' => 'spamuser'.$i,
            'email' => 'spam'.$i.'@gotix.test',
            'telepon' => '0812345678'.$i,
            'alamat' => 'Alamat Spam '.$i,
            'jenis_kelamin' => 'L',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        Auth::logout();
    }

    // Percobaan ke-6 harus ditolak 429
    $response = $this->withServerVariables(['REMOTE_ADDR' => '172.16.0.5'])->post(route('daftar.store'), [
        'nama' => 'Spam User Terblokir',
        'username' => 'spamuserblocked',
        'email' => 'blocked@gotix.test',
        'telepon' => '081234567899',
        'alamat' => 'Alamat Blokir',
        'jenis_kelamin' => 'L',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(429);
    $response->assertHeader('Retry-After');
    $response->assertSee('Terlalu banyak percobaan pendaftaran');
});
