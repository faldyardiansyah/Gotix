@extends('layouts.auth')

@section('title', 'Masuk ke Akun — Gotix')

@section('konten')
<div class="w-full max-w-md my-auto">
    <!-- Card Masuk Taktil Retro -->
    <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 sm:p-8 shadow-[5px_5px_0px_#18181b]">
        <!-- Header Card -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-400 border-2 border-slate-900 text-slate-950 font-heading font-black text-xl mb-3 shadow-[2px_2px_0px_#18181b]">
                <svg class="w-6 h-6 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            <h1 class="font-heading text-2xl font-black text-slate-950 tracking-tight">Masuk ke Akun</h1>
            <p class="text-xs text-slate-600 mt-1 font-medium">Masuk untuk memesan kursi dan mengelola tiket travel Anda</p>
        </div>

        <!-- Notifikasi Error / Rate Limit Warning -->
        @if ($errors->any() || isset($rateLimitMessage))
            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border-2 border-rose-600 text-xs text-rose-950 shadow-[2px_2px_0px_#e11d48] space-y-1">
                <div class="font-heading font-black flex items-center gap-1.5 text-rose-900">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Kendala Autentikasi:</span>
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

        <form action="{{ route('masuk.store') }}" method="POST" class="space-y-4"
              x-data="{ showPass: false, submitting: false }"
              @submit="submitting = true">
            @csrf

            <!-- Input Username / Email -->
            <div>
                <label for="login" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                    Username atau Email
                </label>
                <div class="relative">
                    <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                           placeholder="username atau email@domain.com"
                           class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                </div>
            </div>

            <!-- Input Kata Sandi -->
            <div>
                <label for="password" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                    Kata Sandi
                </label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" id="password" required
                           placeholder="Masukkan kata sandi"
                           class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition pr-10">
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

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="ingat_saya"
                           class="rounded-md border-2 border-slate-900 text-amber-500 focus:ring-amber-400 w-4 h-4 shadow-[1px_1px_0px_#18181b]">
                    <span class="text-xs font-bold text-slate-700">Ingat sesi saya</span>
                </label>
            </div>

            <!-- Submit Button Taktil Gotix -->
            <button type="submit"
                    :disabled="submitting"
                    class="w-full mt-2 py-3 px-4 bg-amber-500 hover:bg-amber-400 active:translate-x-0.5 active:translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed text-slate-950 font-heading font-black text-sm rounded-xl border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] transition flex items-center justify-center gap-2 cursor-pointer">
                <span x-show="!submitting">Masuk ke Akun</span>
                <span x-show="submitting" x-cloak class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Memproses...</span>
                </span>
            </button>
        </form>

        <!-- Link ke Halaman Daftar -->
        <div class="mt-6 pt-5 border-t-2 border-slate-100 text-center text-xs text-slate-600 font-medium">
            Belum memiliki akun Gotix?
            <a href="{{ route('daftar') }}" class="font-heading font-extrabold text-slate-950 hover:text-amber-600 underline decoration-2 transition ml-1">
                Daftar akun sekarang &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
