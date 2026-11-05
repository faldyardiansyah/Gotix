@extends('layouts.tamu')

@section('title', 'Detail Pesanan #' . $pesanan->kode_pesanan . ' — Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @php
        $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
        $badgeClass = match ($statusVal) {
            'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
            'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-rose-50 text-rose-700 border-rose-200',
        };
        $dotClass = match ($statusVal) {
            'Lunas' => 'bg-emerald-500',
            'Dalam Proses' => 'bg-sky-500 animate-pulse',
            'Belum Bayar' => 'bg-amber-500 animate-pulse',
            default => 'bg-rose-500',
        };
    @endphp

    <!-- Tombol Kembali & Status Badge -->
    <div class="mb-6 flex items-center justify-between" data-aos="fade-down" data-aos-duration="400">
        <a href="{{ route('member.pesanan.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Riwayat Pesanan</span>
        </a>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
            <span class="w-2 h-2 rounded-full {{ $dotClass }}"></span>
            Status: {{ $statusVal }}
        </span>
    </div>

    @if (session('sukses'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-medium">
            {{ session('sukses') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-8" data-aos="fade-up" data-aos-duration="500">
        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rincian Reservasi Tiket</span>
                <h1 class="font-heading text-2xl font-extrabold text-slate-900 mt-0.5">Pesanan #{{ $pesanan->kode_pesanan }}</h1>
                <p class="text-xs text-slate-500 mt-1">Dipesan pada {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-' }} WIB</p>
            </div>

            @if ($statusVal === 'Lunas')
                <a href="{{ route('member.tiket.show', $pesanan->kode_pesanan) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-sm self-start sm:self-auto">
                    <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    <span>Buka E-Tiket Perjalanan</span>
                </a>
            @elseif ($statusVal === 'Belum Bayar')
                <a href="{{ route('member.pembayaran.show', $pesanan->kode_pesanan) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition shadow-sm self-start sm:self-auto">
                    <span>Bayar & Upload Bukti Sekarang →</span>
                </a>
            @endif
        </div>

        <!-- Rute & Armada Real Database -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-slate-50 rounded-2xl border border-slate-200/80">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Rute Perjalanan</span>
                <div class="text-base font-bold text-slate-900 mt-1">
                    {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} → {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                </div>
                <div class="text-xs text-slate-600 mt-1">
                    Jadwal: {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                    pukul {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                </div>
            </div>

            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Armada Kendaraan</span>
                <div class="text-base font-bold text-slate-900 mt-1">
                    {{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? '-' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})
                </div>
                <div class="text-xs text-slate-600 mt-1">
                    Warna {{ $pesanan->jadwal?->kendaraan?->warna ?? '-' }} • Kapasitas {{ $pesanan->jadwal?->kendaraan?->kapasitas_kursi ?? 5 }} Kursi
                </div>
            </div>
        </div>

        <!-- Detail Penumpang & Kursi -->
        <div>
            <h2 class="font-heading font-bold text-slate-900 text-base mb-4">Data Penumpang</h2>
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Nama Penumpang</th>
                            <th class="px-5 py-3">Nomor Telepon</th>
                            <th class="px-5 py-3 text-right">No. Kursi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-5 py-3.5 text-slate-400">1</td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ Auth::user()->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono text-xs">{{ Auth::user()->telepon ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-sky-600">Kursi #{{ $pesanan->nomor_kursi }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rincian Pembayaran & Bukti -->
        <div>
            <h2 class="font-heading font-bold text-slate-900 text-base mb-4">Rincian Pembayaran</h2>
            <div class="border border-slate-200 rounded-2xl p-5 space-y-3 bg-white">
                <div class="flex items-center justify-between text-sm text-slate-600">
                    <span>Tarif Tiket Perjalanan (1 Kursi)</span>
                    <span class="font-medium text-slate-900">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm text-slate-600">
                    <span>Biaya Layanan & Administrasi</span>
                    <span class="font-medium text-emerald-600">GRATIS</span>
                </div>
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-base font-extrabold text-slate-900">
                    <span>Total Pembayaran</span>
                    <span class="text-xl text-sky-600">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path)
                <div class="mt-6 border border-slate-200 rounded-2xl p-5 bg-slate-50 space-y-3">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Foto Bukti Transfer yang Telah Anda Unggah:</span>
                    <div class="max-w-xs border border-slate-200 rounded-xl overflow-hidden bg-white shadow-xs">
                        <img src="{{ asset('storage/' . $pesanan->konfirmasiPembayaran->bukti_path) }}"
                             alt="Bukti Transfer" class="w-full h-auto object-cover">
                    </div>
                    <div class="text-xs text-slate-600 space-y-0.5">
                        <p>• Rekening Pengirim: <strong>{{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}</strong></p>
                        <p>• Resi: <strong class="font-mono">{{ $pesanan->konfirmasiPembayaran->nomor_resi }}</strong></p>
                        <p>• Waktu: <strong>{{ $pesanan->konfirmasiPembayaran->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }}</strong></p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
