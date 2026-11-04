@extends('layouts.tamu')

@section('title', 'Masuk Akun — Gotix')

@section('konten')
<div class="min-h-[calc(100vh-14rem)] flex items-center justify-center px-4 py-12 sm:py-16">
    <div class="w-full max-w-md" data-aos="fade-up" data-aos-duration="500">
        <!-- Card Masuk -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm">
            <!-- Header Brand -->
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-heading font-bold text-xl mx-auto mb-4 shadow-sm">
                    G
                </div>
                <h1 class="font-heading text-2xl font-extrabold text-slate-900 tracking-tight">Selamat Datang</h1>
                <p class="text-xs text-slate-500 mt-1">Masuk ke akun Gotix untuk memesan & mengelola tiket Anda</p>
            </div>

            <!-- Error Notification -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-xs text-rose-800 space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        Terjadi Kendala
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('masuk.store') }}" method="POST" class="space-y-4"
                  x-data="{ showPass: false }">
                @csrf

                <!-- Input Username / Email -->
                <div>
                    <label for="login" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Username atau Email
                    </label>
                    <div class="relative">
                        <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                               placeholder="nama_pengguna atau email@domain.com"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                    </div>
                </div>

                <!-- Input Kata Sandi -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Kata Sandi
                        </label>
                    </div>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" id="password" required
                               placeholder="Masukkan kata sandi akun"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition pr-10">
                        <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ingat_saya"
                               class="rounded border-slate-300 text-sky-600 focus:ring-sky-500 w-4 h-4">
                        <span class="text-xs text-slate-600">Ingat sesi saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full mt-2 py-3 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 active:bg-slate-950 rounded-xl transition shadow-sm cursor-pointer">
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Link Daftar -->
            <div class="mt-8 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
                Belum memiliki akun Gotix?
                <a href="{{ route('daftar') }}" class="font-bold text-sky-600 hover:text-sky-700 transition">
                    Daftar akun sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
