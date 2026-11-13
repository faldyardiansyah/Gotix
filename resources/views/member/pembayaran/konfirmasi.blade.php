@extends('layouts.tamu')

@section('title', 'Status Pembayaran #' . $pesanan->kode_pesanan . ' — Gotix')

@section('konten')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @php
        $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
        $rute = ($pesanan->jadwal?->kotaAsal?->nama_kota ?? '-') . ' → ' . ($pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-');
        $waktuBerangkat = $pesanan->jadwal?->waktu_berangkat ? \Carbon\Carbon::parse($pesanan->jadwal->waktu_berangkat) : null;
    @endphp

    <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 sm:p-8 shadow-[5px_5px_0px_#18181b] text-center space-y-6">
        @if ($statusVal === 'Lunas')
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 border-2 border-slate-900 text-emerald-600 flex items-center justify-center mx-auto shadow-[3px_3px_0px_#18181b]">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-heading font-black bg-emerald-100 border border-emerald-700 text-emerald-900 mb-2">
                    Pembayaran Berhasil Diverifikasi
                </span>
                <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Tiket Perjalanan Anda Siap!
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-md mx-auto font-medium">
                    Pembayaran telah diverifikasi oleh operator. E-Tiket boarding pass Anda telah terbit dan siap digunakan saat keberangkatan.
                </p>
            </div>
            <div class="pt-2">
                <x-button href="{{ route('member.tiket.show', $pesanan->kode_pesanan) }}" variant="dark" size="lg" class="w-full sm:w-auto">
                    <span>Buka E-Tiket Boarding Pass</span>
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </x-button>
            </div>
        @elseif ($statusVal === 'Dalam Proses')
            <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-slate-900 text-amber-600 flex items-center justify-center mx-auto shadow-[3px_3px_0px_#18181b]">
                <svg class="w-8 h-8 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-heading font-black bg-amber-100 border border-amber-700 text-amber-900 mb-2">
                    Menunggu Verifikasi Operator
                </span>
                <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Bukti Transfer Berhasil Dikirim
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-md mx-auto font-medium">
                    Terima kasih! Bukti pembayaran Anda sedang diperiksa oleh petugas loket Gotix. Anda dapat memantau status secara berkala.
                </p>
            </div>
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                <x-button href="{{ route('member.pesanan.show', $pesanan->kode_pesanan) }}" variant="secondary" size="md">
                    Detail Pesanan
                </x-button>
                <x-button href="{{ route('member.pesanan.index') }}" variant="primary" size="md">
                    Riwayat Transaksi
                </x-button>
            </div>
        @else
            <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-slate-900 text-amber-600 flex items-center justify-center mx-auto shadow-[3px_3px_0px_#18181b]">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <x-badge-status :status="$pesanan->status" size="md" />
                <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight mt-3">
                    Status: {{ $statusVal }}
                </h1>
            </div>
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                <x-button href="{{ route('member.pembayaran.show', $pesanan->kode_pesanan) }}" variant="primary" size="md">
                    Instruksi Pembayaran
                </x-button>
            </div>
        @endif

        <!-- Ringkasan Tiket -->
        <div class="p-5 bg-stone-50 rounded-2xl border-2 border-slate-900 text-left space-y-3">
            <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200">
                <span class="font-bold text-slate-500 uppercase">Kode Booking</span>
                <span class="font-mono font-black text-slate-900 text-sm">{{ $pesanan->kode_pesanan }}</span>
            </div>
            <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200">
                <span class="font-bold text-slate-500 uppercase">Rute Perjalanan</span>
                <span class="font-heading font-bold text-slate-900 text-sm">{{ $rute }}</span>
            </div>
            <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200">
                <span class="font-bold text-slate-500 uppercase">Waktu Berangkat</span>
                <span class="font-bold text-slate-900">{{ $waktuBerangkat ? $waktuBerangkat->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</span>
            </div>
            <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200">
                <span class="font-bold text-slate-500 uppercase">Nomor Kursi</span>
                <span class="font-heading font-black text-slate-900">Kursi #{{ $pesanan->nomor_kursi }}</span>
            </div>
            <div class="flex items-center justify-between text-xs pt-1">
                <span class="font-bold text-slate-500 uppercase">Total Tagihan</span>
                <span class="font-heading font-black text-amber-600 text-base">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
