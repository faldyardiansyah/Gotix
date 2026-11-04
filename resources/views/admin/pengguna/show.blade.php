@extends('layouts.admin')

@section('title', 'Detail Pengguna — Admin Gotix')
@section('page-title', 'Detail Pengguna: ' . $pengguna->nama)
@section('page-subtitle', 'Informasi akun profil dan riwayat perjalanan pengguna terdaftar.')

@section('konten')
<div class="max-w-4xl space-y-6">
    <div>
        <a href="{{ route('admin.pengguna.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Data Pengguna</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kartu Profil -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-700 font-bold flex items-center justify-center text-lg">
                    {{ strtoupper(substr($pengguna->nama, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-heading font-bold text-slate-900 text-sm">{{ $pengguna->nama }}</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 mt-0.5">
                        {{ $pengguna->peran?->value ?? (string) $pengguna->peran }}
                    </span>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block">Username</span>
                    <span class="font-mono text-slate-800 font-semibold">{{ $pengguna->username }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Email</span>
                    <span class="text-slate-800">{{ $pengguna->email }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Nomor Telepon</span>
                    <span class="font-mono text-slate-800">{{ $pengguna->telepon ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Jenis Kelamin</span>
                    <span class="text-slate-800">{{ $pengguna->jenis_kelamin?->value === 'L' ? 'Laki-laki' : ($pengguna->jenis_kelamin?->value === 'P' ? 'Perempuan' : '-') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Alamat Domisili</span>
                    <span class="text-slate-800">{{ $pengguna->alamat ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Terdaftar Pada</span>
                    <span class="text-slate-800">{{ $pengguna->created_at ? $pengguna->created_at->format('d M Y, H:i') : '-' }} WIB</span>
                </div>
            </div>
        </div>

        <!-- Riwayat Pesanan Pengguna -->
        <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-bold text-slate-900 text-sm">Riwayat Pesanan Tiket</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi perjalanan yang pernah dibuat pengguna.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                    {{ $pengguna->pesanan->count() }} Pesanan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Rute</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Tarif</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pengguna->pesanan as $pesanan)
                            @php
                                $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
                                $badge = match ($statusVal) {
                                    'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-rose-50 text-rose-700 border-rose-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                    <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="text-sky-600 hover:underline">
                                        {{ $pesanan->kode_pesanan }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} → {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900">
                                    Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badge }}">
                                        {{ $statusVal }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada transaksi tiket dari pengguna ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
