@extends('layouts.tamu')

@section('title', 'Verifikasi Struk #' . $pesanan->kode_pesanan . ' — Operator Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        modalBukti: false,
     }">

    @php
        $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
        $badgeClass = match ($statusVal) {
            'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
            'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    @endphp

    <!-- Breadcrumb & Navigasi Kembali -->
    <div class="mb-6 flex items-center justify-between gap-4" data-aos="fade-down" data-aos-duration="400">
        <a href="{{ route('operator.pembayaran.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Verifikasi</span>
        </a>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
            <span class="w-2 h-2 rounded-full {{ $statusVal === 'Lunas' ? 'bg-emerald-500' : 'bg-sky-500 animate-pulse' }}"></span>
            Status: {{ $statusVal }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: Rincian Pesanan & Pembayaran -->
        <div class="lg:col-span-7 space-y-6" data-aos="fade-right" data-aos-duration="500">
            <!-- Header Kartu -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Transaksi</span>
                        <h1 class="font-heading text-2xl font-extrabold text-slate-900 mt-0.5">#{{ $pesanan->kode_pesanan }}</h1>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400">Waktu Pemesanan</div>
                        <div class="text-xs font-bold text-slate-800 font-mono">{{ $pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-' }} WIB</div>
                    </div>
                </div>

                <!-- Info Pemesan -->
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nama Penumpang</div>
                        <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $pesanan->pengguna?->nama ?? '-' }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $pesanan->pengguna?->telepon ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nomor Kursi</div>
                        <div class="text-sm font-bold text-sky-600 mt-0.5">Kursi #{{ $pesanan->nomor_kursi }}</div>
                        <div class="text-xs text-slate-500">{{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? '-' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})</div>
                    </div>
                </div>
            </div>

            <!-- Rute & Jadwal -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-heading font-bold text-slate-900 text-sm">Informasi Rute Perjalanan</h2>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kota Asal</span>
                        <span class="font-bold text-slate-900">{{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kota Tujuan</span>
                        <span class="font-bold text-slate-900">{{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200">
                        <span class="text-slate-500">Tanggal Keberangkatan</span>
                        <span class="font-bold text-slate-900">
                            {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                            &bull; {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Perbandingan Tagihan vs Data Transfer -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-heading font-bold text-slate-900 text-sm">Pencocokan Nominal & Data Transfer</h2>
                <div class="border border-slate-200 rounded-2xl p-4 space-y-2.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Tarif Tagihan Resmi</span>
                        <span class="font-mono font-bold text-slate-900 text-sm">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Rekening / Pengirim</span>
                        <span class="font-medium text-slate-800">{{ $pesanan->konfirmasiPembayaran?->rekening_pengirim ?? 'Belum ada konfirmasi' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Nomor Resi / Referensi</span>
                        <span class="font-mono font-bold text-slate-800">{{ $pesanan->konfirmasiPembayaran?->nomor_resi ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Waktu Transfer</span>
                        <span class="text-slate-800 font-medium">
                            {{ $pesanan->konfirmasiPembayaran?->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }}
                            {{ $pesanan->konfirmasiPembayaran?->jam_transfer ? substr($pesanan->konfirmasiPembayaran->jam_transfer, 0, 5) . ' WIB' : '' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pratinjau Struk & Tombol Aksi -->
        <div class="lg:col-span-5 space-y-6" data-aos="fade-left" data-aos-duration="500">
            <!-- Box Pratinjau Bukti Transfer -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-heading font-bold text-slate-900 text-sm">Berkas Bukti Transfer</h2>
                    <span class="text-[11px] text-slate-400">Klik untuk perbesar</span>
                </div>

                @if ($pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path)
                    <div @click="modalBukti = true"
                         class="relative border border-slate-200 rounded-2xl overflow-hidden bg-slate-100 group cursor-pointer shadow-xs aspect-[3/4] flex items-center justify-center">
                        <img src="{{ asset('storage/' . $pesanan->konfirmasiPembayaran->bukti_path) }}"
                             alt="Bukti Transfer Penumpang" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/80 backdrop-blur-sm text-xs font-semibold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                </svg>
                                <span>Perbesar Struk</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-8 border border-slate-200 rounded-2xl bg-slate-50 text-center space-y-2">
                        <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <p class="text-xs font-semibold text-slate-600">Belum ada struk transfer yang diunggah</p>
                        <p class="text-[11px] text-slate-400">Penumpang belum menyelesaikan proses upload bukti bayar.</p>
                    </div>
                @endif
            </div>

            <!-- Tombol Aksi Verifikasi / Tolak -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-3">
                <form action="{{ route('operator.pembayaran.verifikasi', $pesanan->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin memvalidasi pembayaran ini dan menerbitkan E-Tiket resmi?')">
                    @csrf
                    <button type="submit"
                            class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm shadow-emerald-600/20 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Terima & Terbitkan Tiket</span>
                    </button>
                </form>

                <form action="{{ route('operator.pembayaran.tolak', $pesanan->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin menolak pembayaran ini?')">
                    @csrf
                    <button type="submit"
                            class="w-full py-3 px-4 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 border border-rose-200 font-bold text-xs uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                        <span>Tolak Pembayaran</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pratinjau Gambar Struk (Zoom In) -->
    @if ($pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path)
        <div x-show="modalBukti"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
             x-cloak>
            <div @click.away="modalBukti = false"
                 class="bg-white border border-slate-200 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-heading font-bold text-slate-900 text-sm">Pratinjau Bukti Transfer Penuh</h3>
                    <button @click="modalBukti = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div>
                    <img src="{{ asset('storage/' . $pesanan->konfirmasiPembayaran->bukti_path) }}"
                         alt="Bukti Transfer Penuh" class="w-full max-h-[70vh] object-contain rounded-2xl bg-slate-50">
                </div>
                <button @click="modalBukti = false"
                        class="w-full py-2.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    @endif
</div>
@endsection
