@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $pesanan->kode_pesanan . ' — Admin Gotix')
@section('page-title', 'Detail Transaksi #' . $pesanan->kode_pesanan)
@section('page-subtitle', 'Informasi lengkap pemesanan tiket perjalanan dan status pembayaran.')

@section('konten')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.pesanan.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Rekapitulasi Pesanan</span>
        </a>

        @php
            $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
            $badge = match ($statusVal) {
                'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                default => 'bg-rose-50 text-rose-700 border-rose-200',
            };
        @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badge }}">
            Status: {{ $statusVal }}
        </span>
    </div>

    @if (session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
            {{ session('sukses') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Rincian Pesanan & Pemesan -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h2 class="font-heading font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">
                Data Perjalanan & Penumpang
            </h2>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Kode Booking</span>
                    <span class="font-mono font-bold text-slate-900">{{ $pesanan->kode_pesanan }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nama Pemesan</span>
                    <a href="{{ route('admin.pengguna.show', $pesanan->pengguna_id) }}" class="font-semibold text-sky-600 hover:underline">
                        {{ $pesanan->pengguna?->nama ?? '-' }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">No. Telepon</span>
                    <span class="font-mono text-slate-800">{{ $pesanan->pengguna?->telepon ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Rute Perjalanan</span>
                    <span class="font-semibold text-slate-900">
                        {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} → {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Armada</span>
                    <span class="text-slate-800">{{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? '-' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Waktu Berangkat</span>
                    <span class="text-slate-800">
                        {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                        pukul {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nomor Kursi</span>
                    <span class="font-bold text-sky-600">Kursi #{{ $pesanan->nomor_kursi }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t border-slate-100 text-sm">
                    <span class="font-bold text-slate-700">Total Tarif</span>
                    <span class="font-extrabold text-sky-600">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Bukti Pembayaran & Aksi Verifikasi / Tolak -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between space-y-4">
            <div>
                <h2 class="font-heading font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">
                    Bukti Pembayaran & Status
                </h2>

                @if ($pesanan->konfirmasiPembayaran)
                    <div class="mt-4 space-y-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nama Pengirim</span>
                            <span class="font-semibold text-slate-900">{{ $pesanan->konfirmasiPembayaran->nama_pengirim }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nominal Transfer</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($pesanan->konfirmasiPembayaran->jumlah_bayar, 0, ',', '.') }}</span>
                        </div>
                        @if ($pesanan->konfirmasiPembayaran->catatan)
                            <div class="pt-1">
                                <span class="text-slate-400">Catatan Pengirim:</span>
                                <p class="text-slate-700 italic mt-0.5">{{ $pesanan->konfirmasiPembayaran->catatan }}</p>
                            </div>
                        @endif

                        <div class="pt-2">
                            <span class="font-semibold text-slate-700 block mb-2">Foto Struk Bukti Transfer:</span>
                            @if ($pesanan->konfirmasiPembayaran->bukti_transfer)
                                <a href="{{ asset('storage/' . $pesanan->konfirmasiPembayaran->bukti_transfer) }}" target="_blank"
                                   class="block border border-slate-200 rounded-xl overflow-hidden hover:opacity-90 transition">
                                    <img src="{{ asset('storage/' . $pesanan->konfirmasiPembayaran->bukti_transfer) }}"
                                         alt="Bukti Transfer" class="w-full max-h-52 object-cover bg-slate-50">
                                    <span class="block text-center text-[11px] text-sky-600 py-1.5 bg-slate-50 border-t border-slate-100 font-medium">
                                        Perbesar bukti transfer ↗
                                    </span>
                                </a>
                            @else
                                <div class="p-4 bg-slate-50 rounded-xl text-center text-xs text-slate-400">File bukti tidak ditemukan.</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-5 bg-slate-50 border border-slate-200 rounded-xl text-center text-xs text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700">Belum ada bukti pembayaran diunggah.</p>
                        <p class="text-slate-400">Member belum melakukan konfirmasi transfer manual.</p>
                    </div>
                @endif
            </div>

            <!-- Tombol Aksi Admin: Verifikasi / Tolak -->
            <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                <form action="{{ route('admin.pesanan.verifikasi', $pesanan->id) }}" method="POST" class="flex-1"
                      onsubmit="return confirm('Verifikasi pesanan ini menjadi Lunas?')">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs text-center">
                        Terima (Lunas)
                    </button>
                </form>

                <form action="{{ route('admin.pesanan.tolak', $pesanan->id) }}" method="POST" class="flex-1"
                      onsubmit="return confirm('Tolak dan batalkan pesanan ini?')">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-xl transition text-center">
                        Tolak Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
