@extends('layouts.auth')

@section('title', 'Daftar Akun Member — Gotix')

@section('konten')
<div class="w-full max-w-xl my-auto">
    <!-- Card Daftar Taktil Retro -->
    <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 sm:p-8 md:p-10 shadow-[6px_6px_0px_#18181b]">
        <!-- Header Card -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-400 border-2 border-slate-900 text-slate-950 font-heading font-black text-xl mb-3 shadow-[2px_2px_0px_#18181b]">
                <svg class="w-6 h-6 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.765Z" />
                </svg>
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Daftar Akun Member</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 font-medium">Lengkapi data diri Anda untuk pemesanan tiket travel antar kota cepat & transparan</p>
        </div>

        <!-- Notifikasi Error / Rate Limit Warning -->
        @if ($errors->any() || isset($rateLimitMessage))
            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border-2 border-rose-600 text-xs text-rose-950 shadow-[2px_2px_0px_#e11d48] space-y-1">
                <div class="font-heading font-black flex items-center gap-1.5 text-rose-900">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Mohon Periksa Kolom Input:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-900 font-medium">
                    @if (isset($rateLimitMessage))
                        <li>{{ $rateLimitMessage }}</li>
                    @endif
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('daftar.store') }}" method="POST" class="space-y-4"
              x-data="{ showPass: false, submitting: false }"
              @submit="submitting = true">
            @csrf

            <!-- Nama Lengkap Sesuai KTP -->
            <div>
                <label for="nama" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                    Nama Lengkap Sesuai KTP
                </label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required autofocus
                       placeholder="Contoh: Budi Santoso"
                       class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
            </div>

            <!-- Username & Email Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                        Username Unik
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required
                           placeholder="budisantoso"
                           class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition font-mono">
                </div>
                <div>
                    <label for="email" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                        Alamat Email (Opsional)
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           placeholder="budi@example.com"
                           class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                </div>
            </div>

            <!-- Telepon & Jenis Kelamin Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="telepon" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                        Nomor WhatsApp / Telepon
                    </label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}" required
                           placeholder="081234567890"
                           class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition font-mono">
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                        Jenis Kelamin
                    </label>
                    <div class="relative">
                        <select name="jenis_kelamin" id="jenis_kelamin" required
                                class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition appearance-none">
                            <option value="">Pilih Jenis Kelamin...</option>
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-900 font-bold">
                            ▼
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat Domisili -->
            <div>
                <label for="alamat" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                    Alamat Domisili
                </label>
                <textarea name="alamat" id="alamat" rows="2" required
                          placeholder="Masukkan alamat domisili atau titik penjemputan langganan Anda"
                          class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition leading-relaxed">{{ old('alamat') }}</textarea>
            </div>

            <!-- Kata Sandi & Konfirmasi Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                        Kata Sandi (Min. 8 Karakter)
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" id="password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition pr-9">
                        <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-600 hover:text-slate-950 transition cursor-pointer"
                                :title="showPass ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                            <svg x-show="!showPass" class="w-4 h-4 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                        Ulangi Kata Sandi
                    </label>
                    <input :type="showPass ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                           placeholder="Ketik ulang kata sandi"
                           class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                </div>
            </div>

            <!-- Submit Button Taktil Gotix -->
            <button type="submit"
                    :disabled="submitting"
                    class="w-full mt-3 py-3.5 px-4 bg-amber-500 hover:bg-amber-400 active:translate-x-0.5 active:translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed text-slate-950 font-heading font-black text-sm rounded-xl border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] transition flex items-center justify-center gap-2 cursor-pointer">
                <span x-show="!submitting">Daftar Sebagai Member Resmi Gotix</span>
                <span x-show="submitting" x-cloak class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Mendaftarkan Akun...</span>
                </span>
            </button>
        </form>

        <!-- Link ke Halaman Masuk -->
        <div class="mt-6 pt-5 border-t-2 border-slate-100 text-center text-xs text-slate-600 font-medium">
            Sudah memiliki akun Gotix sebelumnya?
            <a href="{{ route('masuk') }}" class="font-heading font-extrabold text-slate-950 hover:text-amber-600 underline decoration-2 transition ml-1">
                Masuk ke akun Anda &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
