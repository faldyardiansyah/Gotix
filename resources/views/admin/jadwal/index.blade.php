@extends('layouts.admin')

@section('title', 'Master Jadwal — GoTix.')
@section('page-title', 'Master Jadwal Keberangkatan')
@section('page-subtitle', 'Kelola rute, armada, tarif, dan kuota 5 kursi perjalanan travel antar-kota.')

@section('konten')
<!-- Bar Aksi Atas -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border-2 border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[2px_2px_0px_#18181b]">
        <span>Total Jadwal Aktif:</span>
        <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 font-black">{{ $jadwalList->count() }}</span>
    </div>
    <x-button href="{{ route('admin.jadwal.create') }}" variant="primary" size="md">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Tambah Jadwal Baru</span>
    </x-button>
</div>

<!-- Tabel Jadwal (Tactile Card) -->
<x-card title="Daftar Jadwal Perjalanan" subtitle="Pilih jadwal untuk melihat manifest penumpang atau mengubah tarif dan armada" variant="white">
    <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
        <table class="w-full text-sm text-left">
            <thead class="bg-stone-100 border-b-2 border-slate-900 text-xs font-heading font-black text-slate-800 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5">Rute Perjalanan</th>
                    <th class="px-4 py-3.5">Tanggal & Jam</th>
                    <th class="px-4 py-3.5">Armada Kendaraan</th>
                    <th class="px-4 py-3.5 text-right">Tarif</th>
                    <th class="px-4 py-3.5 text-center">Ketersediaan Kursi</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 border-slate-900 divide-stone-200 font-medium">
                @forelse ($jadwalList as $jadwal)
                    @php
                        $terisi = $jadwal->pesanan->whereIn('status', [
                            \App\Enums\StatusPesanan::BelumBayar,
                            \App\Enums\StatusPesanan::DalamProses,
                            \App\Enums\StatusPesanan::Lunas,
                        ])->count();
                        $kapasitas = max((int) ($jadwal->kendaraan?->kapasitas_kursi ?? 5), 1);
                        $sisa = max(0, $kapasitas - $terisi);
                        $penuh = $sisa === 0;
                    @endphp
                    <tr class="hover:bg-amber-50/60 transition">
                        <td class="px-5 py-4">
                            <div class="font-heading font-black text-slate-950 text-sm">
                                {{ $jadwal->kotaAsal?->nama_kota ?? '-' }}
                                <span class="text-amber-600 font-black px-1">&rarr;</span>
                                {{ $jadwal->kotaTujuan?->nama_kota ?? '-' }}
                            </div>
                            <span class="text-[11px] text-slate-500 font-mono">ID: #JDW-{{ $jadwal->id }}</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-700">
                            <div class="font-bold text-slate-900">
                                {{ $jadwal->tanggal_berangkat ? date('d M Y', strtotime($jadwal->tanggal_berangkat)) : '-' }}
                            </div>
                            <div class="text-slate-500 font-semibold">
                                {{ $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-xs text-slate-700">
                            <div class="font-bold text-slate-900">{{ $jadwal->kendaraan?->jenis_kendaraan ?? '-' }}</div>
                            <div class="text-slate-500 font-mono font-semibold">{{ $jadwal->kendaraan?->nomor_polisi ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 text-right font-heading font-black text-slate-950 text-sm whitespace-nowrap">
                            Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            @if ($sisa > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-heading font-black bg-emerald-100 text-emerald-950 border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                    <span>{{ $sisa }} Kursi Tersedia</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-heading font-black bg-rose-100 text-rose-950 border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                    <span>Penuh (0 Kursi)</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <x-button href="{{ route('admin.jadwal.show', $jadwal->id) }}" variant="secondary" size="sm">
                                    Detail
                                </x-button>
                                <x-button href="{{ route('admin.jadwal.edit', $jadwal->id) }}" variant="primary" size="sm">
                                    Edit
                                </x-button>
                                <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus jadwal keberangkatan ini? Seluruh data terkait tidak dapat dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger" size="sm">
                                        Hapus
                                    </x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-sm font-bold text-slate-700">Belum ada jadwal perjalanan terdaftar.</div>
                            <div class="mt-4">
                                <x-button href="{{ route('admin.jadwal.create') }}" variant="primary" size="sm">
                                    Tambah Jadwal Pertama &rarr;
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
