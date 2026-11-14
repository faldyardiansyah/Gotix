@extends('layouts.admin')

@section('title', 'Keluhan Member — GoTix.')
@section('page-title', 'Keluhan & Masukan Member')
@section('page-subtitle', 'Pantau dan tindak lanjuti masukan serta kendala yang dilaporkan penumpang.')

@section('konten')
<!-- Bar Ringkasan -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border-2 border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[2px_2px_0px_#18181b]">
        <span>Total Keluhan Masuk:</span>
        <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 font-black">{{ $daftarKeluhan->count() }}</span>
    </div>
</div>

<!-- Tabel Keluhan (Tactile Card) -->
<x-card title="Daftar Keluhan & Masukan Penumpang" subtitle="Klik detail untuk membaca isi lengkap dan mengirimkan balasan resmi" variant="white">
    <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
        <table class="w-full text-sm text-left">
            <thead class="bg-stone-100 border-b-2 border-slate-900 text-xs font-heading font-black text-slate-800 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5">ID</th>
                    <th class="px-4 py-3.5">Member Pelapor</th>
                    <th class="px-4 py-3.5">Kategori</th>
                    <th class="px-4 py-3.5">Isi Keluhan</th>
                    <th class="px-4 py-3.5 text-center">Status Balasan</th>
                    <th class="px-4 py-3.5">Tanggal</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 border-slate-900 divide-stone-200 font-medium">
                @forelse ($daftarKeluhan as $keluhan)
                    @php
                        $kategoriVal = $keluhan->kategori instanceof \App\Enums\KategoriKeluhan ? $keluhan->kategori->value : (string) ($keluhan->kategori ?? '-');
                        $sudahDibalas = !empty($keluhan->balasan);
                    @endphp
                    <tr class="hover:bg-amber-50/60 transition">
                        <td class="px-5 py-4 text-xs font-mono font-bold text-slate-500">
                            #KLH-{{ $keluhan->id }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-heading font-black text-slate-950 text-xs">{{ $keluhan->pengguna?->nama ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $keluhan->pengguna?->username ? '@' . $keluhan->pengguna->username : '—' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-heading font-black bg-amber-100 text-amber-950 border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                                {{ $kategoriVal }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-xs text-slate-700 max-w-xs">
                            <p class="line-clamp-2 font-medium">{{ $keluhan->deskripsi }}</p>
                        </td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            @if ($sudahDibalas)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-heading font-black bg-emerald-100 text-emerald-950 border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    <span>Sudah Dibalas</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-heading font-black bg-rose-100 text-rose-950 border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                    <span>Menunggu Tanggapan</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-xs text-slate-600 whitespace-nowrap font-medium">
                            {{ $keluhan->created_at ? date('d M Y', strtotime($keluhan->created_at)) : '-' }}
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <x-button href="{{ route('admin.keluhan.show', $keluhan->id) }}" variant="secondary" size="sm">
                                Tanggapi
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm font-bold text-slate-500">
                            Belum ada keluhan masuk dari penumpang. 🎉
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
