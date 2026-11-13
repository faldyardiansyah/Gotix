@extends('layouts.admin')

@section('title', 'Detail Jadwal — ' . ($jadwal->kotaAsal->nama_kota ?? '') . ' ke ' . ($jadwal->kotaTujuan->nama_kota ?? ''))
@section('page-title', 'Detail Jadwal Keberangkatan')
@section('page-subtitle', 'Informasi rute, kapasitas armada, ketersediaan kursi, dan daftar transaksi penumpang.')

@section('konten')
@php
    $asal = $jadwal->kotaAsal->nama_kota ?? '-';
    $tujuan = $jadwal->kotaTujuan->nama_kota ?? '-';
    $tgl = $jadwal->tanggal_berangkat ? $jadwal->tanggal_berangkat->format('d M Y') : '-';
    $jam = $jadwal->jam_berangkat ? substr((string) $jadwal->jam_berangkat, 0, 5) . ' WIB' : '-';
    $kapasitas = $jadwal->kendaraan->kapasitas_kursi ?? (int) config('gotix.kapasitas_kursi', 5);
    $nopol = $jadwal->kendaraan->nomor_polisi ?? '-';
    $armada = $jadwal->kendaraan->jenis_kendaraan ?? '-';
    $tarifFormatted = 'Rp ' . number_format($jadwal->harga, 0, ',', '.');

    // Pesanan aktif (mengisi kursi)
    $pesananAktif = $jadwal->pesanan->filter(function ($p) {
        return in_array($p->status->value, ['Belum Bayar', 'Dalam Proses', 'Lunas']);
    });

    $kursiTerisiMap = [];
    foreach ($pesananAktif as $p) {
        $kursiTerisiMap[$p->nomor_kursi] = $p;
    }

    $terisiCount = count($kursiTerisiMap);
    $sisaKursi = max(0, $kapasitas - $terisiCount);

    // Total penerimaan lunas
    $pesananLunas = $jadwal->pesanan->where('status', \App\Enums\StatusPesanan::Lunas);
    $totalPendapatan = $pesananLunas->sum('harga_snapshot');
@endphp

<div class="space-y-6">
    <!-- Header Aksi & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.jadwal.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="font-heading font-bold text-xl sm:text-2xl text-slate-900">
                    {{ $asal }} &rarr; {{ $tujuan }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Keberangkatan: <span class="font-semibold text-slate-700">{{ $tgl }}</span> &bull; {{ $jam }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.jadwal.edit', $jadwal->id) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                <span>Edit Jadwal</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik Jadwal -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Kapasitas Armada</div>
            <div class="font-heading text-2xl font-bold text-slate-900">{{ $kapasitas }} <span class="text-xs font-normal text-slate-500">Kursi</span></div>
            <div class="text-[11px] text-slate-500 mt-1">{{ $armada }} ({{ $nopol }})</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Keterisian Kursi</div>
            <div class="font-heading text-2xl font-bold text-sky-600">{{ $terisiCount }} <span class="text-xs font-normal text-slate-500">Terpesan</span></div>
            <div class="text-[11px] text-slate-500 mt-1">{{ $sisaKursi }} Kursi Masih Kosong</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Tarif Per Kursi</div>
            <div class="font-heading text-2xl font-bold text-slate-900">{{ $tarifFormatted }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Snapshot Harga Resmi</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Penerimaan Lunas</div>
            <div class="font-heading text-2xl font-bold text-emerald-600">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 mt-1">{{ $pesananLunas->count() }} Tiket Terverifikasi Lunas</div>
        </div>
    </div>

    <!-- Denah Keterisian Kursi (Visual Seat Grid) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="font-heading font-bold text-slate-900 text-sm">Denah Visual Keterisian Kursi</h2>
                <div class="text-[11px] text-slate-500">Status pesanan per nomor kursi pada kendaraan ini</div>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-emerald-500"></span> Lunas</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-amber-500"></span> Belum Bayar</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-sky-500"></span> Verifikasi</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-slate-100 border border-slate-300"></span> Tersedia</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2">
            @for ($kursiNo = 1; $kursiNo <= $kapasitas; $kursiNo++)
                @php
                    $pesananKursi = $kursiTerisiMap[$kursiNo] ?? null;
                    if ($pesananKursi) {
                        $st = $pesananKursi->status->value;
                        $cardBg = match ($st) {
                            'Lunas' => 'bg-emerald-50 border-emerald-300 text-emerald-800',
                            'Dalam Proses' => 'bg-sky-50 border-sky-300 text-sky-800',
                            default => 'bg-amber-50 border-amber-300 text-amber-800',
                        };
                        $penumpangNama = $pesananKursi->pengguna->nama ?? 'Penumpang';
                        $kodePesan = $pesananKursi->kode_pesanan;
                    } else {
                        $cardBg = 'bg-slate-50 border-slate-200 text-slate-600';
                        $penumpangNama = null;
                        $kodePesan = null;
                    }
                @endphp

                <div class="border rounded-xl p-3 text-center {{ $cardBg }} transition">
                    <div class="text-[10px] font-semibold uppercase tracking-wider opacity-75">Kursi</div>
                    <div class="font-heading text-xl font-extrabold my-0.5">#{{ $kursiNo }}</div>
                    @if ($pesananKursi)
                        <div class="text-[11px] font-bold truncate">{{ $penumpangNama }}</div>
                        <a href="{{ route('admin.pesanan.show', $pesananKursi->id) }}"
                           class="text-[10px] font-mono text-sky-600 hover:underline block mt-0.5">
                            {{ $kodePesan }}
                        </a>
                    @else
                        <div class="text-[11px] font-medium text-slate-400 italic">Tersedia</div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Tabel Riwayat Semua Pesanan pada Jadwal Ini -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="font-heading font-bold text-slate-900 text-sm">Daftar Transaksi Tiket Terkait</h2>
                <div class="text-xs text-slate-500">Semua pesanan yang tercatat pada jadwal ini (termasuk dibatalkan)</div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                Total: {{ $jadwal->pesanan->count() }} Transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50/80 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Kode Pesanan</th>
                        <th class="px-5 py-3.5">Penumpang</th>
                        <th class="px-5 py-3.5 text-center">Kursi</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Waktu Transaksi</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($jadwal->pesanan as $pesanan)
                        @php
                            $stVal = $pesanan->status->value;
                            $badgeClass = match ($stVal) {
                                'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                                'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-xs text-slate-900">
                                {{ $pesanan->kode_pesanan }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-900 text-xs">{{ $pesanan->pengguna->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $pesanan->pengguna->telepon ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-xs">
                                Kursi #{{ $pesanan->nomor_kursi }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                    {{ $stVal }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y H:i') : '-' }} WIB
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.pesanan.show', $pesanan->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-sky-600 hover:text-sky-700">
                                    <span>Detail Transaksi</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-xs text-slate-400 italic">
                                Belum ada pemesanan tiket untuk jadwal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
