@extends('layouts.tamu')

@section('konten')
    <div class="max-w-md mx-auto px-4 py-16">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-8 shadow-sm">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Selamat Datang Kembali</h1>
                <p class="text-sm text-slate-500 mt-1">Masuk menggunakan Username atau Email Anda</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('masuk.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">Username /
                        Email</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">Kata
                        Sandi</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600 transition-all">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm rounded-xl transition-all shadow-sm shadow-sky-200">
                    Masuk ke Sistem
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('daftar') }}" class="font-semibold text-sky-600 hover:underline">Daftar sekarang</a>
            </p>
        </div>
    </div>
@endsection
