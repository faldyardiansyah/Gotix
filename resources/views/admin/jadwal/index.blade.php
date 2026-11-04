@extends('layouts.admin')

@section('title', 'Master Jadwal — Admin Gotix')
@section('page-title', 'Master Jadwal Keberangkatan')
@section('page-subtitle', 'Kelola rute, armada, tarif, dan jadwal perjalanan antar kota.')

@section('konten')
<!-- Bar Aksi -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="text-sm text-slate-600">
        Total <strong class="text-slate-900">{{ $jadwalList->count() }}</strong> jadwal terdaftar
    </div>
    <a href="{{ route('admin.jadwal.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Tambah Jadwal Baru</span>
    </a>
</div>

<!-- Tabel Jadwal -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50/80 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">Rute Perjalanan</th>
                    <th class="px-5 py-3.5">Tanggal & Jam</th>
                    <th class="px-5 py-3.5">Armada</th>
                    <th class="px-5 py-3.5">Tarif</th>
                    <th class="px-5 py-3.5">Sisa Kursi</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($jadwalList as $jadwal)
                    @php
                        $terisi = $jadwal->pesanan->whereIn('status', [
                            \App\Enums\StatusPesanan::BelumBayar,
                            \App\Enums\StatusPesanan::DalamProses,
                            \App\Enums\StatusPesanan::Lunas,
                        ])->count();
                        $kapasitas = $jadwal->kendaraan?->kapasitas_kursi ?? 5;
                        $sisa = max(0, $kapasitas - $terisi);
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-900 text-sm">
                                {{ $jadwal->kotaAsal?->nama_kota ?? '-' }} → {{ $jadwal->kotaTujuan?->nama_kota ?? '-' }}
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-700">
                            <div class="font-semibold">{{ $jadwal->tanggal_berangkat ? date('d M Y', strtotime($jadwal->tanggal_berangkat)) : '-' }}</div>
                            <div class="text-slate-500">{{ $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}</div>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-700">
                            <div class="font-medium">{{ $jadwal->kendaraan?->jenis_kendaraan ?? '-' }}</div>
                            <div class="text-slate-400 font-mono">{{ $jadwal->kendaraan?->nomor_polisi ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-900 text-sm whitespace-nowrap">
                            Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4">
                            @if ($sisa > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $sisa }} tersedia
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Penuh
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.jadwal.show', $jadwal->id) }}"
                                   class="px-2.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                    Detail
                                </a>
                                <a href="{{ route('admin.jadwal.edit', $jadwal->id) }}"
                                   class="px-2.5 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-lg transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus jadwal ini? Tindakan tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-sm text-slate-500">Belum ada jadwal perjalanan terdaftar.</div>
                            <a href="{{ route('admin.jadwal.create') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 hover:text-sky-700">
                                Tambah jadwal pertama →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
