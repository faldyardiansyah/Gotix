@extends('layouts.admin')

@section('title', 'Detail Jadwal — ' . ($jadwal->kotaAsal->nama_kota ?? '') . ' ke ' . ($jadwal->kotaTujuan->nama_kota ?? ''))
@section('page-title', 'Detail Jadwal Keberangkatan')
@section('page-subtitle', 'Informasi rute, kapasitas armada, ketersediaan kursi, dan daftar transaksi penumpang.')

@section('konten')
@php
    $asal = $jadwal->kotaAsal->nama_kota ?? '-';
    $tujuan = $jadwal->kotaTujuan->nama_kota ?? '-';
    $tgl = $jadwal->tanggal_berangkat ? date('d M Y', strtotime($jadwal->tanggal_berangkat)) : '-';
    $jam = $jadwal->jam_berangkat ? substr((string) $jadwal->jam_berangkat, 0, 5) . ' WIB' : '-';
    $kapasitas = max((int) ($jadwal->kendaraan->kapasitas_kursi ?? config('gotix.kapasitas_kursi', 5)), 1);
    $nopol = $jadwal->kendaraan->nomor_polisi ?? '-';
    $armada = $jadwal->kendaraan->jenis_kendaraan ?? '-';
    $tarifFormatted = 'Rp ' . number_format($jadwal->harga, 0, ',', '.');

    // Pesanan aktif (mengisi kursi)
    $pesananAktif = $jadwal->pesanan->filter(function ($p) {
        $val = $p->status instanceof \App\Enums\StatusPesanan ? $p->status->value : (string) $p->status;
        return in_array($val, ['Belum Bayar', 'Dalam Proses', 'Lunas']);
    });

    $kursiTerisiMap = [];
    foreach ($pesananAktif as $p) {
        $kursiTerisiMap[$p->nomor_kursi] = $p;
    }

    $terisiCount = count($kursiTerisiMap);
    $sisaKursi = max(0, $kapasitas - $terisiCount);

    // Total penerimaan lunas
    $pesananLunas = $jadwal->pesanan->filter(function ($p) {
        $val = $p->status instanceof \App\Enums\StatusPesanan ? $p->status->value : (string) $p->status;
        return $val === 'Lunas';
    });
    $totalPendapatan = $pesananLunas->sum('harga_snapshot');
@endphp

<div class="space-y-6">
    <!-- Header Aksi & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.jadwal.index') }}"
               class="p-2 bg-white text-slate-900 border-2 border-slate-900 rounded-xl hover:bg-amber-100 shadow-[2px_2px_0px_#18181b] transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="font-heading font-black text-xl sm:text-2xl text-slate-950">
                    {{ $asal }} <span class="text-amber-500 font-black">&rarr;</span> {{ $tujuan }}
                </h1>
                <p class="text-xs text-slate-600 font-semibold mt-0.5">
                    Keberangkatan: <span class="font-bold text-slate-900">{{ $tgl }}</span> &bull; {{ $jam }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-button href="{{ route('admin.jadwal.edit', $jadwal->id) }}" variant="primary" size="md">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                <span>Edit Jadwal</span>
            </x-button>
        </div>
    </div>

    <!-- Ringkasan Statistik Jadwal (4 Kartu Tactile) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b]">
            <div class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-500 mb-1">Kapasitas Armada</div>
            <div class="font-heading text-2xl sm:text-3xl font-black text-slate-950">{{ $kapasitas }} <span class="text-xs font-bold text-slate-500">Kursi</span></div>
            <div class="text-xs text-slate-600 font-semibold mt-1">{{ $armada }} ({{ $nopol }})</div>
        </div>

        <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b]">
            <div class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-500 mb-1">Keterisian Kursi</div>
            <div class="font-heading text-2xl sm:text-3xl font-black text-amber-600">{{ $terisiCount }} <span class="text-xs font-bold text-slate-500">Terisi</span></div>
            <div class="text-xs text-slate-600 font-semibold mt-1">{{ $sisaKursi }} Kursi Masih Kosong</div>
        </div>

        <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b]">
            <div class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-500 mb-1">Tarif Per Kursi</div>
            <div class="font-heading text-2xl sm:text-3xl font-black text-slate-950">{{ $tarifFormatted }}</div>
            <div class="text-xs text-slate-600 font-semibold mt-1">Snapshot Harga Resmi</div>
        </div>

        <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b]">
            <div class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-500 mb-1">Penerimaan Lunas</div>
            <div class="font-heading text-2xl sm:text-3xl font-black text-emerald-600">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-600 font-semibold mt-1">{{ $pesananLunas->count() }} Tiket Terverifikasi</div>
        </div>
    </div>

    <!-- Denah Keterisian Kursi (Visual Seat Grid) -->
    <x-card title="Denah Visual Keterisian Kursi ({{ $kapasitas }} Kursi)" subtitle="Status pemesanan per nomor kursi pada armada travel ini" variant="white">
        <x-slot:headerAction>
            <div class="flex flex-wrap items-center gap-3 text-xs font-heading font-bold">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border border-slate-900"></span> Lunas</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 border border-slate-900"></span> Verifikasi</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-stone-300 border border-slate-900"></span> Belum Bayar</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-white border border-slate-900"></span> Tersedia</span>
            </div>
        </x-slot:headerAction>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 pt-1">
            @for ($kursiNo = 1; $kursiNo <= $kapasitas; $kursiNo++)
                @php
                    $pesananKursi = $kursiTerisiMap[$kursiNo] ?? null;
                    if ($pesananKursi) {
                        $st = $pesananKursi->status instanceof \App\Enums\StatusPesanan ? $pesananKursi->status->value : (string) $pesananKursi->status;
                        $cardBg = match ($st) {
                            'Lunas' => 'bg-emerald-100 border-slate-900 text-emerald-950',
                            'Dalam Proses' => 'bg-amber-100 border-slate-900 text-amber-950',
                            default => 'bg-stone-200 border-slate-900 text-slate-900',
                        };
                        $penumpangNama = $pesananKursi->pengguna->nama ?? 'Penumpang';
                        $kodePesan = $pesananKursi->kode_pesanan;
                    } else {
                        $cardBg = 'bg-stone-50 border-slate-400 text-slate-500 border-dashed';
                        $penumpangNama = null;
                        $kodePesan = null;
                    }
                @endphp

                <div class="border-2 rounded-2xl p-4 text-center {{ $cardBg }} shadow-[2px_2px_0px_#18181b] transition">
                    <div class="text-[10px] font-heading font-black uppercase tracking-wider opacity-75">Nomor Kursi</div>
                    <div class="font-heading text-2xl font-black my-1">#{{ $kursiNo }}</div>
                    @if ($pesananKursi)
                        <div class="text-xs font-black truncate text-slate-950">{{ $penumpangNama }}</div>
                        <a href="{{ route('admin.pesanan.show', $pesananKursi->id) }}"
                           class="text-[11px] font-mono font-bold text-amber-700 hover:text-amber-900 underline block mt-1">
                            {{ $kodePesan }}
                        </a>
                    @else
                        <div class="text-xs font-bold text-slate-400 italic">Kosong / Tersedia</div>
                    @endif
                </div>
            @endfor
        </div>
    </x-card>

    <!-- Tabel Riwayat Semua Pesanan pada Jadwal Ini -->
    <x-card title="Daftar Transaksi Tiket Terkait" subtitle="Semua tiket penumpang tercatat pada rute keberangkatan ini" variant="white">
        <x-slot:headerAction>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-stone-100 border-2 border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[1px_1px_0px_#18181b]">
                Total: {{ $jadwal->pesanan->count() }} Transaksi
            </span>
        </x-slot:headerAction>

        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-stone-100 border-b-2 border-slate-900 text-xs font-heading font-black text-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Kode Booking</th>
                        <th class="px-4 py-3.5">Penumpang</th>
                        <th class="px-4 py-3.5 text-center">Kursi</th>
                        <th class="px-4 py-3.5">Status Tiket</th>
                        <th class="px-4 py-3.5">Waktu Pesan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 border-slate-900 divide-stone-200 font-medium">
                    @forelse ($jadwal->pesanan as $pesanan)
                        <tr class="hover:bg-amber-50/60 transition">
                            <td class="px-5 py-4 font-mono font-black text-xs text-slate-950">
                                {{ $pesanan->kode_pesanan }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-950 text-xs">{{ $pesanan->pengguna->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $pesanan->pengguna->telepon ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-center font-heading font-black text-xs text-slate-950">
                                Kursi #{{ $pesanan->nomor_kursi }}
                            </td>
                            <td class="px-4 py-4">
                                <x-badge-status :status="$pesanan->status" size="sm" />
                            </td>
                            <td class="px-4 py-4 text-xs text-slate-600 font-medium">
                                {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y H:i') : '-' }} WIB
                            </td>
                            <td class="px-5 py-4 text-right">
                                <x-button href="{{ route('admin.pesanan.show', $pesanan->id) }}" variant="secondary" size="sm">
                                    Detail
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-xs text-slate-500 font-bold italic">
                                Belum ada pemesanan tiket untuk jadwal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
