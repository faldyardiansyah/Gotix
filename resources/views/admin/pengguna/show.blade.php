@extends('layouts.admin')

@section('title', 'Detail Pengguna — ' . $pengguna->nama . ' — GoTix.')
@section('page-title', 'Detail Pengguna: ' . $pengguna->nama)
@section('page-subtitle', 'Informasi akun profil dan riwayat perjalanan pengguna terdaftar.')

@section('konten')
<div class="space-y-6">
    <div>
        <a href="{{ route('admin.pengguna.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-heading font-black text-slate-700 hover:text-slate-950 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Data Pengguna</span>
        </a>
    </div>

    @php
        $peranVal = $pengguna->peran instanceof \App\Enums\PeranPengguna ? $pengguna->peran->value : (string) $pengguna->peran;
        $roleBadge = match ($peranVal) {
            'Admin' => 'bg-purple-100 text-purple-950 border-slate-900',
            'Operator' => 'bg-sky-100 text-sky-950 border-slate-900',
            default => 'bg-amber-100 text-amber-950 border-slate-900',
        };
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Kartu Profil Pengguna -->
        <div class="md:col-span-1">
            <x-card title="Informasi Profil" subtitle="Data akun pengguna terdaftar" variant="white">
                <div class="flex items-center gap-4 pb-4 border-b-2 border-slate-900">
                    <div class="w-14 h-14 rounded-2xl bg-amber-400 text-slate-950 border-2 border-slate-900 font-heading font-black flex items-center justify-center text-xl shrink-0 shadow-[2px_2px_0px_#18181b]">
                        {{ strtoupper(substr($pengguna->nama, 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="font-heading font-black text-slate-950 text-base leading-tight">{{ $pengguna->nama }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-heading font-black border mt-1 {{ $roleBadge }} shadow-[1px_1px_0px_#18181b]">
                            {{ $peranVal }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 space-y-3 text-xs">
                    <div class="p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px] block">Username</span>
                        <span class="font-mono font-bold text-slate-950 text-xs">{{ $pengguna->username ? '@' . $pengguna->username : '—' }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px] block">Email</span>
                        <span class="font-bold text-slate-950 text-xs">{{ $pengguna->email ?? '-' }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px] block">Nomor Telepon</span>
                        <span class="font-mono font-bold text-slate-950 text-xs">{{ $pengguna->telepon ?? '-' }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px] block">Jenis Kelamin</span>
                        <span class="font-bold text-slate-950 text-xs">{{ $pengguna->jenis_kelamin?->value === 'L' ? 'Laki-laki' : ($pengguna->jenis_kelamin?->value === 'P' ? 'Perempuan' : '-') }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px] block">Alamat Domisili</span>
                        <span class="font-medium text-slate-950 text-xs">{{ $pengguna->alamat ?? '-' }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px] block">Terdaftar Pada</span>
                        <span class="font-semibold text-slate-700 text-xs">{{ $pengguna->created_at ? $pengguna->created_at->format('d M Y, H:i') : '-' }} WIB</span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Kolom Kanan: Riwayat Pesanan Pengguna -->
        <div class="md:col-span-2">
            <x-card title="Riwayat Pemesanan Tiket" subtitle="Daftar perjalanan yang pernah dibuat oleh akun ini" variant="white">
                <x-slot:headerAction>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-stone-100 border-2 border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[1px_1px_0px_#18181b]">
                        Total: {{ $pengguna->pesanan->count() }} Pesanan
                    </span>
                </x-slot:headerAction>

                <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-stone-100 border-b-2 border-slate-900 text-[11px] font-heading font-black text-slate-800 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Kode Booking</th>
                                <th class="px-4 py-3">Rute Perjalanan</th>
                                <th class="px-4 py-3">Tanggal Berangkat</th>
                                <th class="px-4 py-3 text-right">Tarif</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 border-slate-900 divide-stone-200 font-medium">
                            @forelse ($pengguna->pesanan as $pesanan)
                                <tr class="hover:bg-amber-50/60 transition">
                                    <td class="px-5 py-3.5 font-mono font-black text-slate-950">
                                        {{ $pesanan->kode_pesanan }}
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-slate-900">
                                        {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }}
                                        <span class="text-amber-600 px-0.5">&rarr;</span>
                                        {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-700 whitespace-nowrap">
                                        {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-heading font-black text-slate-950 whitespace-nowrap">
                                        Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <x-badge-status :status="$pesanan->status" size="sm" />
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <x-button href="{{ route('admin.pesanan.show', $pesanan->id) }}" variant="secondary" size="sm">
                                            Detail
                                        </x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-xs font-bold text-slate-500">
                                        Belum ada riwayat transaksi tiket dari pengguna ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
