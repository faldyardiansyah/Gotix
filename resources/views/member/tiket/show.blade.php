@extends('layouts.tamu')

@section('title', 'E-Tiket #' . $pesanan->kode_pesanan . ' — Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb & Tombol Cetak -->
    <div class="mb-6 flex items-center justify-between gap-4 no-print">
        <a href="{{ route('member.tiket.index') }}"
           class="inline-flex items-center gap-2 text-xs font-heading font-bold text-slate-700 hover:text-slate-950 px-3 py-1.5 rounded-full border border-slate-300 hover:border-slate-900 bg-white transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Tiket</span>
        </a>

        <x-button onclick="window.print()" variant="primary" size="sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.641-2.062-1.17-2.915a11.96 11.96 0 0 1 7.45-7.45c.853.529 1.839.93 2.915 1.17a11.96 11.96 0 0 1-7.45 7.45c-.853-.529-1.839-.93-2.915-1.17ZM6 18v3h12v-3m0-6V4H6v8m12 0H6" />
            </svg>
            <span>Cetak Boarding Pass</span>
        </x-button>
    </div>

    <!-- E-Tiket Boarding Pass Component -->
    <x-tiket-pass :pesanan="$pesanan" :qrSvg="$qrSvg" />
</div>
@endsection
