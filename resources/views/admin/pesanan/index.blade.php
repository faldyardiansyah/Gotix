@extends('layouts.admin')

@section('title', 'Daftar Pesanan — Admin Gotix')
@section('page-title', 'Rekapitulasi Seluruh Pesanan')
@section('page-subtitle', 'Pantau semua transaksi tiket travel masuk dari member dan loket.')

@section('konten')
<div class="mb-5 text-sm text-slate-600">
    Total <strong class="text-slate-900">{{ $daftarPesanan->count() }}</strong> pesanan tercatat
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50/80 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">Kode Booking</th>
                    <th class="px-5 py-3.5">Pemesan</th>
                    <th class="px-5 py-3.5">Rute Perjalanan</th>
                    <th class="px-5 py-3.5">Tanggal & Kursi</th>
                    <th class="px-5 py-3.5">Total</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($daftarPesanan as $pesanan)
                    @php
                        $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
                        $badgeClass = match ($statusVal) {
                            'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-rose-50 text-rose-700 border-rose-200',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-3.5 font-mono text-xs font-bold text-slate-900 whitespace-nowrap">
                            {{ $pesanan->kode_pesanan }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-slate-900 text-xs">{{ $pesanan->pengguna?->nama ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400">{{ $pesanan->pengguna?->telepon ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-700 font-medium">
                            {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} → {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-700">
                            <div>{{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}</div>
                            <div class="text-slate-400">Kursi #{{ $pesanan->nomor_kursi }}</div>
                        </td>
                        <td class="px-5 py-3.5 font-bold text-slate-900 text-xs whitespace-nowrap">
                            Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $badgeClass }}">
                                {{ $statusVal }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('admin.pesanan.show', $pesanan->id) }}"
                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                            Belum ada pesanan masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
