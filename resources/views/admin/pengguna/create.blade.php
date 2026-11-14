@extends('layouts.admin')

@section('title', 'Tambah Pengguna — GoTix.')
@section('page-title', 'Tambah Pengguna Baru')
@section('page-subtitle', 'Daftarkan akun Member atau Operator Loket baru dalam sistem GoTix Travel.')

@section('konten')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.pengguna.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-heading font-black text-slate-700 hover:text-slate-950 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Data Pengguna</span>
        </a>
    </div>

    <x-card title="Form Data Pengguna Baru" subtitle="Lengkapi identitas pengguna dan tentukan peran akun dengan benar" variant="white">
        <form action="{{ route('admin.pengguna.store') }}" method="POST">
            @csrf
            <div class="space-y-5">

                @if ($errors->any())
                    <div class="rounded-xl border-2 border-slate-900 bg-rose-50 text-rose-950 p-4 text-xs font-bold space-y-1 shadow-[2px_2px_0px_#18181b]">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Nama Lengkap -->
                <div>
                    <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Nama Lengkap <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                           placeholder="Contoh: Budi Pratama" required
                           class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                </div>

                <!-- Username & Telepon -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Username <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="username" value="{{ old('username') }}"
                               placeholder="Contoh: budipratama" required
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                        <p class="text-[11px] text-slate-500 font-semibold mt-1">Minimal 3 karakter, alfanumerik, tanpa spasi.</p>
                    </div>

                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Nomor Telepon / WhatsApp <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="telepon" value="{{ old('telepon') }}"
                               placeholder="Contoh: 081234567890" required
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    </div>
                </div>

                <!-- Email & Peran -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Alamat Email <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="Contoh: budi@gmail.com"
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    </div>

                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Peran Akun <span class="text-rose-600">*</span>
                        </label>
                        <select name="peran" required
                                class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                            <option value="">Pilih Peran Akun...</option>
                            <option value="Member" {{ old('peran', 'Member') === 'Member' ? 'selected' : '' }}>
                                Member (Penumpang Umum)
                            </option>
                            <option value="Operator" {{ old('peran') === 'Operator' ? 'selected' : '' }}>
                                Operator Loket (Petugas Kasir & Verifikasi)
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Jenis Kelamin <span class="text-rose-600">*</span>
                    </label>
                    <div class="flex items-center gap-6 pt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-900">
                            <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', 'L') === 'L' ? 'checked' : '' }}
                                   class="w-4 h-4 text-amber-500 border-2 border-slate-900 focus:ring-amber-400">
                            <span>Laki-laki</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-900">
                            <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') === 'P' ? 'checked' : '' }}
                                   class="w-4 h-4 text-amber-500 border-2 border-slate-900 focus:ring-amber-400">
                            <span>Perempuan</span>
                        </label>
                    </div>
                </div>

                <!-- Alamat Domisili -->
                <div>
                    <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Alamat Domisili <span class="text-rose-600">*</span>
                    </label>
                    <textarea name="alamat" rows="3" required
                              placeholder="Contoh: Jl. Raden Intan No. 45, Enggal, Bandar Lampung"
                              class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">{{ old('alamat') }}</textarea>
                </div>

                <!-- Password & Konfirmasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t-2 border-slate-900">
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Kata Sandi <span class="text-rose-600">*</span>
                        </label>
                        <input type="password" name="password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    </div>

                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Konfirmasi Kata Sandi <span class="text-rose-600">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required
                               placeholder="Ulangi kata sandi"
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-4 border-t-2 border-slate-900 flex items-center gap-3">
                    <x-button type="submit" variant="primary" size="md" class="flex-1">
                        Simpan Pengguna
                    </x-button>
                    <x-button href="{{ route('admin.pengguna.index') }}" variant="secondary" size="md" class="flex-1">
                        Batal
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>
</div>
@endsection
