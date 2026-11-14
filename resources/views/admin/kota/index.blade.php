@extends('layouts.admin')

@section('title', 'Master Kota — GoTix.')
@section('page-title', 'Master Data Kota & Wilayah')
@section('page-subtitle', 'Daftar kota asal dan kota tujuan layanan travel Gotix di Lampung.')

@section('konten')
<!-- Bar Aksi Atas -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border-2 border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[2px_2px_0px_#18181b]">
        <span>Total Kota Terdaftar:</span>
        <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 font-black">{{ $daftarKota->count() }}</span>
    </div>
    <x-button href="{{ route('admin.kota.create') }}" variant="primary" size="md">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Tambah Kota Baru</span>
    </x-button>
</div>

<!-- Tabel Kota (Tactile Card) -->
<x-card title="Daftar Kota & Terminal" subtitle="Kota-kota yang terhubung dalam rute perjalanan travel" variant="white">
    <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
        <table class="w-full text-sm text-left">
            <thead class="bg-stone-100 border-b-2 border-slate-900 text-xs font-heading font-black text-slate-800 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5">ID</th>
                    <th class="px-5 py-3.5">Nama Kota / Wilayah</th>
                    <th class="px-4 py-3.5 text-center">Jadwal Keberangkatan (Asal)</th>
                    <th class="px-4 py-3.5 text-center">Jadwal Kedatangan (Tujuan)</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 border-slate-900 divide-stone-200 font-medium">
                @forelse ($daftarKota as $kota)
                    <tr class="hover:bg-amber-50/60 transition">
                        <td class="px-5 py-4 text-xs font-mono font-bold text-slate-500">
                            #KTA-{{ $kota->id }}
                        </td>
                        <td class="px-5 py-4 font-heading font-black text-slate-950 text-base">
                            {{ $kota->nama_kota }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 border border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[1px_1px_0px_#18181b]">
                                {{ $kota->jadwal_asal_count ?? 0 }} Jadwal
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-stone-100 border border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[1px_1px_0px_#18181b]">
                                {{ $kota->jadwal_tujuan_count ?? 0 }} Jadwal
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <x-button href="{{ route('admin.kota.edit', $kota->id) }}" variant="primary" size="sm">
                                    Edit
                                </x-button>
                                <form action="{{ route('admin.kota.destroy', $kota->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus kota {{ $kota->nama_kota }}? Seluruh rute terkait mungkin terdampak.')">
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
                        <td colspan="5" class="px-6 py-12 text-center text-sm font-bold text-slate-600">
                            Belum ada data kota yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
