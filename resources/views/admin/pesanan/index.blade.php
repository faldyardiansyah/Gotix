@extends('layouts.admin')

@section('title', 'Daftar Pesanan — GoTix.')
@section('page-title', 'Rekapitulasi Seluruh Pesanan')
@section('page-subtitle', 'Pantau semua transaksi tiket travel masuk dari Member dan loket operasional.')

@section('konten')
<div x-data="{ tab: 'semua', search: '' }">
    <!-- Bar Ringkasan & Filter Status -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <!-- Quick Filter Tabs (Tactile) -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="tab = 'semua'"
                    :class="tab === 'semua' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer">
                Semua Pesanan ({{ $daftarPesanan->count() }})
            </button>
            <button type="button" @click="tab = 'Dalam Proses'"
                    :class="tab === 'Dalam Proses' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Menunggu Verifikasi ({{ $daftarPesanan->where('status.value', 'Dalam Proses')->count() }})</span>
            </button>
            <button type="button" @click="tab = 'Lunas'"
                    :class="tab === 'Lunas' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Lunas ({{ $daftarPesanan->where('status.value', 'Lunas')->count() }})</span>
            </button>
            <button type="button" @click="tab = 'Belum Bayar'"
                    :class="tab === 'Belum Bayar' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer">
                Belum Bayar ({{ $daftarPesanan->where('status.value', 'Belum Bayar')->count() }})
            </button>
            <button type="button" @click="tab = 'Dibatalkan'"
                    :class="tab === 'Dibatalkan' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer">
                Dibatalkan ({{ $daftarPesanan->where('status.value', 'Dibatalkan')->count() }})
            </button>
        </div>

        <!-- Input Pencarian Cepat -->
        <div class="relative w-full md:w-64">
            <input type="text" x-model="search" placeholder="Cari kode booking / nama..."
                   class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-1.5 pl-9 text-xs font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
    </div>

    <!-- Tabel Pesanan (Tactile Card) -->
    <x-card title="Daftar Seluruh Pesanan Tiket" subtitle="Kelola dan verifikasi transaksi tiket travel penumpang" variant="white">
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-stone-100 border-b-2 border-slate-900 text-xs font-heading font-black text-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Kode Booking</th>
                        <th class="px-4 py-3.5">Pemesan / Penumpang</th>
                        <th class="px-4 py-3.5">Rute Perjalanan</th>
                        <th class="px-4 py-3.5">Jadwal & Kursi</th>
                        <th class="px-4 py-3.5 text-right">Total Tarif</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 border-slate-900 divide-stone-200 font-medium">
                    @forelse ($daftarPesanan as $pesanan)
                        @php
                            $statusVal = $pesanan->status instanceof \App\Enums\StatusPesanan ? $pesanan->status->value : (string) $pesanan->status;
                            $isWalkIn = ($pesanan->pengguna?->peran?->value ?? '') === 'Operator';
                        @endphp
                        <tr x-show="(tab === 'semua' || tab === '{{ $statusVal }}') && ('{{ strtolower($pesanan->kode_pesanan) }} {{ strtolower($pesanan->pengguna?->nama ?? '') }}'.includes(search.toLowerCase()))"
                            class="hover:bg-amber-50/60 transition">
                            <td class="px-5 py-4 font-mono font-black text-xs text-slate-950 whitespace-nowrap">
                                {{ $pesanan->kode_pesanan }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-950 text-xs">{{ $pesanan->pengguna?->nama ?? '-' }}</span>
                                    @if ($isWalkIn)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-heading font-black bg-stone-200 border border-slate-900 text-slate-900">
                                            Loket
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-heading font-black bg-amber-100 border border-slate-900 text-slate-950">
                                            Member
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $pesanan->pengguna?->telepon ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-xs font-bold text-slate-900">
                                {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }}
                                <span class="text-amber-600 font-black px-1">&rarr;</span>
                                {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-700">
                                <div class="font-bold text-slate-900">
                                    {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                                </div>
                                <div class="text-amber-800 font-heading font-black text-[11px] mt-0.5">
                                    Kursi #{{ $pesanan->nomor_kursi }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right font-heading font-black text-slate-950 text-sm whitespace-nowrap">
                                Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <x-badge-status :status="$pesanan->status" size="sm" />
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <x-button href="{{ route('admin.pesanan.show', $pesanan->id) }}" variant="secondary" size="sm">
                                    Detail
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm font-bold text-slate-600">
                                Belum ada transaksi pesanan masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
