@extends('layouts.tamu')

@section('title', 'Pendaftaran Member — Gotix')

@section('konten')
<div class="max-w-xl mx-auto px-4 py-12 sm:py-16">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 md:p-10 shadow-sm" data-aos="fade-up" data-aos-duration="500">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-sky-600 text-white flex items-center justify-center font-heading font-bold text-xl mx-auto mb-4 shadow-sm">
                G
            </div>
            <h1 class="font-heading text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Member</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi data diri Anda untuk pemesanan tiket travel antar kota cepat & transparan</p>
        </div>

        <!-- Notifikasi Error -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-xs text-rose-800 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    Mohon Periksa Kolom Input:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('daftar.store') }}" method="POST" class="space-y-4"
              x-data="{ showPass: false }">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="nama" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Lengkap Sesuai KTP
                </label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required autofocus
                       placeholder="Contoh: Budi Santoso"
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
            </div>

            <!-- Username & Email Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Username Unik
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required
                           placeholder="budisantoso"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition font-mono">
                </div>
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email (Opsional)
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           placeholder="budi@example.com"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                </div>
            </div>

            <!-- Telepon & Jenis Kelamin -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="telepon" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor WhatsApp / Telepon
                    </label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}" required
                           placeholder="081234567890"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition font-mono">
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Jenis Kelamin
                    </label>
                    <div class="relative">
                        <select name="jenis_kelamin" id="jenis_kelamin" required
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition appearance-none cursor-pointer">
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label for="alamat" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Domisili
                </label>
                <textarea name="alamat" id="alamat" rows="2" required
                          placeholder="Masukkan alamat domisili atau titik jemput langganan Anda"
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition leading-relaxed">{{ old('alamat') }}</textarea>
            </div>

            <!-- Kata Sandi & Konfirmasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kata Sandi (Min. 8 Karakter)
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" id="password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition pr-9">
                        <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                            <svg x-show="!showPass" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showPass" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Ulangi Kata Sandi
                    </label>
                    <input :type="showPass ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                           placeholder="Ketik ulang kata sandi"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full mt-3 py-3 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm shadow-sky-600/20 cursor-pointer">
                Daftar Sebagai Member Resmi Gotix
            </button>
        </form>

        <!-- Link Masuk -->
        <div class="mt-8 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun Gotix sebelumnya?
            <a href="{{ route('masuk') }}" class="font-bold text-sky-600 hover:text-sky-700 transition">
                Masuk ke akun Anda
            </a>
        </div>
    </div>
</div>
@endsection
