@extends('layouts.admin')

@section('title', 'Edit Kendaraan — Admin Gotix')
@section('page-title', 'Edit Armada Kendaraan')
@section('page-subtitle', 'Perbarui data armada kendaraan yang terdaftar.')

@section('konten')
<div class="max-w-lg">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-heading font-bold text-slate-900 text-sm">
                Edit: {{ $kendaraan->jenis_kendaraan }} — {{ $kendaraan->nomor_polisi }}
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui data armada di bawah ini.</p>
        </div>
        <form action="{{ route('admin.kendaraan.update', $kendaraan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-5">

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Kendaraan</label>
                    <input type="text" name="jenis_kendaraan" value="{{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) }}"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Polisi</label>
                    <input type="text" name="nomor_polisi" value="{{ old('nomor_polisi', $kendaraan->nomor_polisi) }}"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Warna</label>
                        <input type="text" name="warna" value="{{ old('warna', $kendaraan->warna) }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kapasitas Kursi</label>
                        <input type="number" name="kapasitas_kursi" value="{{ old('kapasitas_kursi', $kendaraan->kapasitas_kursi) }}"
                               min="1" max="50"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl transition">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.kendaraan.index') }}"
                       class="flex-1 inline-flex items-center justify-center py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition text-center">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
