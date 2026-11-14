@extends('layouts.admin')

@section('title', 'Tambah Armada — GoTix.')
@section('page-title', 'Tambah Armada Kendaraan')
@section('page-subtitle', 'Daftarkan unit kendaraan travel baru berkapasitas 5 kursi eksekutif.')

@section('konten')
<div class="max-w-xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.kendaraan.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-heading font-black text-slate-700 hover:text-slate-950 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Master Armada</span>
        </a>
    </div>

    <x-card title="Form Data Armada" subtitle="Lengkapi spesifikasi unit kendaraan travel" variant="white">
        <form action="{{ route('admin.kendaraan.store') }}" method="POST">
            @csrf
            <div class="space-y-5">

                @if ($errors->any())
                    <div class="rounded-xl border-2 border-slate-900 bg-rose-50 text-rose-950 p-4 text-xs font-bold space-y-1 shadow-[2px_2px_0px_#18181b]">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Jenis Kendaraan <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="jenis_kendaraan" value="{{ old('jenis_kendaraan') }}"
                           placeholder="Contoh: Avanza Premio / Xenia Executive" required
                           class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                </div>

                <div>
                    <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Nomor Polisi (Plat Nomor) <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="nomor_polisi" value="{{ old('nomor_polisi') }}"
                           placeholder="Contoh: BE 1212 AT" required
                           class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-mono font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Warna Kendaraan <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="warna" value="{{ old('warna') }}"
                               placeholder="Contoh: Hitam Metalik" required
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    </div>
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Kapasitas Kursi <span class="text-rose-600">*</span>
                        </label>
                        <input type="number" name="kapasitas_kursi" value="{{ old('kapasitas_kursi', 5) }}"
                               min="1" max="50" required
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    </div>
                </div>

                <div class="pt-4 border-t-2 border-slate-900 flex items-center gap-3">
                    <x-button type="submit" variant="primary" size="md" class="flex-1">
                        Simpan Armada
                    </x-button>
                    <x-button href="{{ route('admin.kendaraan.index') }}" variant="secondary" size="md" class="flex-1">
                        Batal
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>
</div>
@endsection
