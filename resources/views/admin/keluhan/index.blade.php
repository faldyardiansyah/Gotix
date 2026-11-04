@extends('layouts.admin')

@section('title', 'Keluhan Member — Admin Gotix')
@section('page-title', 'Keluhan & Masukan Member')
@section('page-subtitle', 'Pantau dan tindak lanjuti keluhan yang dikirimkan member.')

@section('konten')
<div class="mb-5 text-sm text-slate-600">
    Total <strong class="text-slate-900">{{ $daftarKeluhan->count() }}</strong> keluhan masuk
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50/80 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">ID</th>
                    <th class="px-5 py-3.5">Member Pelapor</th>
                    <th class="px-5 py-3.5">Kategori Keluhan</th>
                    <th class="px-5 py-3.5">Deskripsi</th>
                    <th class="px-5 py-3.5">Tanggal</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($daftarKeluhan as $keluhan)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-3.5 text-xs font-mono text-slate-400">#{{ $keluhan->id }}</td>
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-slate-900 text-xs">{{ $keluhan->pengguna?->nama ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400">@{{ $keluhan->pengguna?->username ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $keluhan->kategori?->value ?? (string) $keluhan->kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-700 max-w-xs">
                            <span class="line-clamp-2">{{ $keluhan->deskripsi }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                            {{ $keluhan->created_at ? date('d M Y', strtotime($keluhan->created_at)) : '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('admin.keluhan.show', $keluhan->id) }}"
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                            Belum ada keluhan yang diterima. 🎉
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
