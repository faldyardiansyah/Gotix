@extends('layouts.admin')

@section('title', 'Edit Jadwal — Admin Gotix')
@section('page-title', 'Edit Jadwal Keberangkatan')
@section('page-subtitle', 'Perbarui data rute, armada, tarif, atau waktu berangkat.')

@section('konten')
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-heading font-bold text-slate-900 text-sm">
                Edit Jadwal:
                {{ $jadwal->kotaAsal?->nama_kota ?? '-' }} → {{ $jadwal->kotaTujuan?->nama_kota ?? '-' }}
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $jadwal->tanggal_berangkat ? $jadwal->tanggal_berangkat->format('d M Y') : '-' }} pukul {{ $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) : '-' }} WIB
            </p>
        </div>
        <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kota Asal</label>
                        <select name="kota_asal_id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                            @foreach ($daftarKota as $kota)
                                <option value="{{ $kota->id }}" {{ (old('kota_asal_id', $jadwal->kota_asal_id) == $kota->id) ? 'selected' : '' }}>
                                    {{ $kota->nama_kota }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kota Tujuan</label>
                        <select name="kota_tujuan_id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                            @foreach ($daftarKota as $kota)
                                <option value="{{ $kota->id }}" {{ (old('kota_tujuan_id', $jadwal->kota_tujuan_id) == $kota->id) ? 'selected' : '' }}>
                                    {{ $kota->nama_kota }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Armada Kendaraan</label>
                    <select name="kendaraan_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                        @foreach ($daftarKendaraan as $kendaraan)
                            <option value="{{ $kendaraan->id }}" {{ (old('kendaraan_id', $jadwal->kendaraan_id) == $kendaraan->id) ? 'selected' : '' }}>
                                {{ $kendaraan->jenis_kendaraan }} — {{ $kendaraan->nomor_polisi }} ({{ $kendaraan->warna }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Berangkat</label>
                        <input type="date" name="tanggal_berangkat"
                               value="{{ old('tanggal_berangkat', $jadwal->tanggal_berangkat?->format('Y-m-d')) }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jam Berangkat</label>
                        <input type="time" name="jam_berangkat"
                               value="{{ old('jam_berangkat', $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) : '') }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Harga Tiket (Rp)</label>
                    <input type="number" name="harga" value="{{ old('harga', $jadwal->harga) }}" min="0"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl transition">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.jadwal.index') }}"
                       class="flex-1 inline-flex items-center justify-center py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition text-center">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
