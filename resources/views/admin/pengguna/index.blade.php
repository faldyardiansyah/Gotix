@extends('layouts.admin')

@section('title', 'Data Pengguna — Admin Gotix')
@section('page-title', 'Manajemen Pengguna Sistem')
@section('page-subtitle', 'Daftar seluruh akun Admin, Operator, dan Member terdaftar.')

@section('konten')
<div class="mb-5 text-sm text-slate-600">
    Total <strong class="text-slate-900">{{ $daftarPengguna->count() }}</strong> pengguna terdaftar
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50/80 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">Nama & Username</th>
                    <th class="px-5 py-3.5">Email / Kontak</th>
                    <th class="px-5 py-3.5">Peran</th>
                    <th class="px-5 py-3.5">Total Pesanan</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($daftarPengguna as $pengguna)
                    @php
                        $peranVal = $pengguna->peran?->value ?? (string) $pengguna->peran;
                        $badgeColor = match ($peranVal) {
                            'Admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'Operator' => 'bg-sky-50 text-sky-700 border-sky-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($pengguna->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900 text-sm">{{ $pengguna->nama }}</div>
                                    <div class="text-xs text-slate-400 font-mono">@{{ $pengguna->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-700">
                            <div>{{ $pengguna->email ?? '-' }}</div>
                            <div class="text-slate-400">{{ $pengguna->telepon }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeColor }}">
                                {{ $peranVal }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm font-semibold text-slate-900">
                            {{ $pengguna->pesanan_count }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.pengguna.show', $pengguna->id) }}"
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                            Belum ada pengguna terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
