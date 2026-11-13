@extends('layouts.tamu')

@section('title', 'Detail Reservasi #' . $pesanan->kode_pesanan . ' — Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @php
        $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
        $isExpired = $pesanan->batas_bayar && now()->gt($pesanan->batas_bayar);
    @endphp

    <!-- Breadcrumb & Status -->
    <div class="mb-6 flex items-center justify-between gap-4">
        <a href="{{ route('member.pesanan.index') }}"
           class="inline-flex items-center gap-2 text-xs font-heading font-bold text-slate-700 hover:text-slate-950 px-3.5 py-1.5 rounded-full border-2 border-slate-900 bg-white shadow-[2px_2px_0px_#18181b] transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Riwayat Pesanan</span>
        </a>

        <x-badge-status :status="$pesanan->status" size="md" />
    </div>

    @if (session('sukses'))
        <div class="mb-6 bg-emerald-100 border-2 border-slate-900 text-emerald-950 rounded-2xl p-4 text-xs font-bold shadow-[3px_3px_0px_#18181b]">
            {{ session('sukses') }}
        </div>
    @endif

    @if (session('gagal'))
        <div class="mb-6 bg-rose-100 border-2 border-slate-900 text-rose-950 rounded-2xl p-4 text-xs font-bold shadow-[3px_3px_0px_#18181b]">
            {{ session('gagal') }}
        </div>
    @endif

    @if ($statusVal === 'Belum Bayar' && $pesanan->konfirmasiPembayaran?->alasan_penolakan)
        <div class="mb-6 bg-rose-100 border-2 border-slate-900 rounded-2xl p-4 sm:p-5 text-xs text-rose-950 space-y-1.5 shadow-[4px_4px_0px_#e11d48]">
            <div class="font-heading font-black text-rose-950 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>Perhatian: Bukti Pembayaran Sebelumnya Ditolak</span>
            </div>
            <p class="font-medium"><strong>Alasan Penolakan:</strong> "{{ $pesanan->konfirmasiPembayaran->alasan_penolakan }}"</p>
            @if (! $isExpired)
                <p class="text-rose-900 font-bold">Silakan unggah ulang bukti transfer yang valid sebelum batas waktu pembayaran berakhir.</p>
            @endif
        </div>
    @endif

    <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 sm:p-8 shadow-[5px_5px_0px_#18181b] space-y-8">
        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b-2 border-slate-900 gap-4">
            <div>
                <span class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-slate-500">Rincian Reservasi Tiket</span>
                <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">Pesanan #{{ $pesanan->kode_pesanan }}</h1>
                <p class="text-xs text-slate-500 mt-1 font-medium">Dipesan pada {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-' }} WIB</p>
            </div>

            @if ($statusVal === 'Lunas')
                <x-button href="{{ route('member.tiket.show', $pesanan->kode_pesanan) }}" variant="dark" size="md">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    <span>Buka E-Tiket Perjalanan</span>
                </x-button>
            @elseif ($statusVal === 'Belum Bayar' && ! $isExpired)
                <x-button href="{{ route('member.pembayaran.show', $pesanan->kode_pesanan) }}" variant="primary" size="md">
                    <span>Bayar & Upload Bukti Sekarang</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </x-button>
            @endif
        </div>

        <!-- Rute & Armada Database -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-amber-50 rounded-2xl border-2 border-slate-900">
            <div>
                <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-500">Rute Perjalanan</span>
                <div class="text-lg font-heading font-black text-slate-900 mt-1">
                    {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} &rarr; {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                </div>
                <div class="text-xs text-slate-700 mt-1 font-medium">
                    Jadwal: {{ $pesanan->jadwal?->waktu_berangkat ? \Carbon\Carbon::parse($pesanan->jadwal->waktu_berangkat)->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                </div>
            </div>

            <div>
                <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-500">Armada Kendaraan</span>
                <div class="text-lg font-heading font-black text-slate-900 mt-1">
                    {{ $pesanan->jadwal?->kendaraan?->nama_kendaraan ?? '-' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})
                </div>
                <div class="text-xs text-slate-700 mt-1 font-medium">
                    Warna {{ $pesanan->jadwal?->kendaraan?->warna ?? '-' }} &bull; Kapasitas {{ $pesanan->jadwal?->kendaraan?->kapasitas_kursi ?? 5 }} Kursi
                </div>
            </div>
        </div>

        <!-- Detail Penumpang & Kursi -->
        <div>
            <h2 class="font-heading font-black text-slate-900 text-lg mb-3">Data Penumpang</h2>
            <div class="border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[3px_3px_0px_#18181b]">
                <table class="w-full text-left text-sm">
                    <thead class="bg-stone-100 border-b-2 border-slate-900 text-xs font-heading font-black text-slate-900 uppercase">
                        <tr>
                            <th class="px-5 py-3.5">No</th>
                            <th class="px-5 py-3.5">Nama Penumpang</th>
                            <th class="px-5 py-3.5">Nomor Telepon</th>
                            <th class="px-5 py-3.5 text-right">No. Kursi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <tr>
                            <td class="px-5 py-3.5 text-slate-400">1</td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ Auth::user()->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono text-xs">{{ Auth::user()->telepon ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="font-heading font-black px-3 py-1 rounded-full bg-amber-500 border border-slate-900 text-slate-950 text-xs shadow-[1px_1px_0px_#18181b]">
                                    Kursi #{{ $pesanan->nomor_kursi }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rincian Pembayaran & Bukti -->
        <div>
            <h2 class="font-heading font-black text-slate-900 text-lg mb-3">Rincian Pembayaran</h2>
            <div class="border-2 border-slate-900 rounded-2xl p-5 space-y-3 bg-white shadow-[3px_3px_0px_#18181b]">
                <div class="flex items-center justify-between text-sm text-slate-600 font-medium">
                    <span>Tarif Tiket Perjalanan (1 Kursi)</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm text-slate-600 font-medium">
                    <span>Biaya Layanan & Administrasi</span>
                    <span class="font-black text-emerald-700">GRATIS</span>
                </div>
                <div class="pt-3 border-t-2 border-slate-900 flex items-center justify-between text-base font-black text-slate-900">
                    <span>Total Pembayaran</span>
                    <span class="text-2xl text-amber-600 font-heading">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                </div>
                <div class="text-xs text-slate-500 pt-1 font-medium">
                    Batas Waktu Pembayaran:
                    <span class="font-mono font-bold {{ $isExpired ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ $pesanan->batas_bayar ? $pesanan->batas_bayar->format('d M Y, H:i') : '-' }} WIB
                    </span>
                </div>
            </div>

            @if ($pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path)
                <div class="mt-6 border-2 border-slate-900 rounded-2xl p-5 bg-stone-50 space-y-3 shadow-[3px_3px_0px_#18181b]">
                    <span class="text-xs font-heading font-black text-slate-900 uppercase tracking-wider block">Foto Bukti Transfer Aktif:</span>
                    <div class="max-w-xs border-2 border-slate-900 rounded-xl overflow-hidden bg-white shadow-[2px_2px_0px_#18181b]">
                        <img src="{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}"
                             alt="Bukti Transfer" class="w-full h-auto object-cover">
                    </div>
                    <div class="text-xs text-slate-700 space-y-1 font-medium">
                        <p>&bull; Rekening Pengirim: <strong>{{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}</strong></p>
                        <p>&bull; Resi Transfer: <strong class="font-mono">{{ $pesanan->konfirmasiPembayaran->nomor_resi }}</strong></p>
                        <p>&bull; Waktu: <strong>{{ $pesanan->konfirmasiPembayaran->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }}</strong></p>
                    </div>
                </div>
            @endif

            @if ($pesanan->riwayatPembayaran && $pesanan->riwayatPembayaran->count() > 0)
                <div class="mt-6 border-2 border-slate-900 rounded-2xl p-5 bg-white space-y-3 shadow-[3px_3px_0px_#18181b]">
                    <span class="text-xs font-heading font-black text-slate-900 uppercase tracking-wider block">Histori Pengunggahan Bukti:</span>
                    <div class="space-y-2.5">
                        @foreach ($pesanan->riwayatPembayaran as $index => $riwayat)
                            <div class="p-3.5 border-2 rounded-xl text-xs space-y-1.5 {{ $riwayat->status === 'Ditolak' ? 'bg-rose-50 border-rose-600' : ($riwayat->status === 'Diterima' ? 'bg-emerald-50 border-emerald-600' : 'bg-stone-50 border-slate-900') }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-heading font-bold text-slate-900">Unggahan #{{ $pesanan->riwayatPembayaran->count() - $index }} ({{ $riwayat->nomor_resi }})</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-heading font-black uppercase border border-slate-900 {{ $riwayat->status === 'Diterima' ? 'bg-emerald-200 text-emerald-950' : ($riwayat->status === 'Ditolak' ? 'bg-rose-200 text-rose-950' : 'bg-amber-200 text-amber-950') }}">
                                        {{ $riwayat->status }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium">
                                    {{ $riwayat->created_at ? $riwayat->created_at->format('d M Y, H:i') : '-' }} WIB
                                </div>
                                @if ($riwayat->alasan_penolakan)
                                    <div class="text-[11px] text-rose-950 bg-rose-200/60 p-2.5 rounded-lg border border-rose-300 font-medium">
                                        <strong>Alasan Ditolak:</strong> {{ $riwayat->alasan_penolakan }}
                                    </div>
                                @endif
                                <div class="pt-1">
                                    <a href="{{ route('pembayaran.bukti.riwayat', $riwayat->id) }}" target="_blank"
                                       class="text-[11px] font-heading font-bold text-slate-900 underline hover:text-amber-600">
                                        Lihat File Struk ↗
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
