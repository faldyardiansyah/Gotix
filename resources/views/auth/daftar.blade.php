@extends('layouts.tamu')

@section('konten')
    <div class="max-w-lg mx-auto px-4 py-12">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-8 shadow-sm">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pendaftaran Penumpang Baru</h1>
                <p class="text-sm text-slate-500 mt-1">Buat akun member untuk memesan tiket perjalanan Anda</p>
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

            <form action="{{ route('daftar.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Email (Opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="telepon" value="{{ old('telepon') }}" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Jenis Kelamin</label>
                        <select name="jenis_kelamin" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">{{ old('alamat') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Kata Sandi</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Konfirmasi Sandi</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-3 bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm rounded-xl transition-all shadow-sm shadow-sky-200">
                    Daftar Akun Member
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Sudah punya akun? 
                <a href="{{ route('masuk') }}" class="font-semibold text-sky-600 hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
@endsection