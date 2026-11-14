@extends('layouts.admin')

@section('title', 'Tambah Jadwal — GoTix.')
@section('page-title', 'Tambah Jadwal Baru')
@section('page-subtitle', 'Buat rute dan jadwal keberangkatan travel baru.')

@section('konten')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.jadwal.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-heading font-black text-slate-700 hover:text-slate-950 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Master Jadwal</span>
        </a>
    </div>

    <x-card title="Form Jadwal Keberangkatan" subtitle="Lengkapi rute perjalanan, penugasan armada, dan tarif resmi" variant="white">
        <form action="{{ route('admin.jadwal.store') }}" method="POST">
            @csrf
            <div class="space-y-5">

                @if ($errors->any())
                    <div class="rounded-xl border-2 border-slate-900 bg-rose-50 text-rose-950 p-4 text-xs font-bold space-y-1 shadow-[2px_2px_0px_#18181b]">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Kota Asal <span class="text-rose-600">*</span>
                        </label>
                        <select name="kota_asal_id" required
                                class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                            <option value="">Pilih Kota Asal...</option>
                            @foreach ($daftarKota as $kota)
                                <option value="{{ $kota->id }}" {{ old('kota_asal_id') == $kota->id ? 'selected' : '' }}>
                                    {{ $kota->nama_kota }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Kota Tujuan <span class="text-rose-600">*</span>
                        </label>
                        <select name="kota_tujuan_id" required
                                class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                            <option value="">Pilih Kota Tujuan...</option>
                            @foreach ($daftarKota as $kota)
                                <option value="{{ $kota->id }}" {{ old('kota_tujuan_id') == $kota->id ? 'selected' : '' }}>
                                    {{ $kota->nama_kota }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Armada Kendaraan <span class="text-rose-600">*</span>
                    </label>
                    <select name="kendaraan_id" required
                            class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                        <option value="">Pilih Armada...</option>
                        @foreach ($daftarKendaraan as $kendaraan)
                            <option value="{{ $kendaraan->id }}" {{ old('kendaraan_id') == $kendaraan->id ? 'selected' : '' }}>
                                {{ $kendaraan->jenis_kendaraan }} — {{ $kendaraan->nomor_polisi }} (Warna: {{ $kendaraan->warna }}, Kapasitas: {{ $kendaraan->kapasitas_kursi ?? 5 }} Kursi)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Tanggal Berangkat <span class="text-rose-600">*</span>
                        </label>
                        <input type="date" name="tanggal_berangkat" value="{{ old('tanggal_berangkat') }}" min="{{ date('Y-m-d') }}" required
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    </div>
                    <div>
                        <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                            Jam Berangkat <span class="text-rose-600">*</span>
                        </label>
                        <input type="time" name="jam_berangkat" value="{{ old('jam_berangkat') }}" required
                               class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    </div>
                </div>

                <div>
                    <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Tarif Tiket Per Kursi (Rp) <span class="text-rose-600">*</span>
                    </label>
                    <input type="number" name="harga" value="{{ old('harga') }}" placeholder="Contoh: 60000" min="0" required
                           class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
                    <p class="text-[11px] text-slate-500 font-semibold mt-1">Tarif berlaku untuk setiap 1 kursi penumpang.</p>
                </div>

                <div class="pt-4 border-t-2 border-slate-900 flex items-center gap-3">
                    <x-button type="submit" variant="primary" size="md" class="flex-1">
                        Simpan Jadwal Keberangkatan
                    </x-button>
                    <x-button href="{{ route('admin.jadwal.index') }}" variant="secondary" size="md" class="flex-1">
                        Batal
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>
</div>
@endsection
