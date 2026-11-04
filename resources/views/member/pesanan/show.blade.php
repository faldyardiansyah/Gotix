@extends('layouts.tamu')

@section('title', 'Detail Pesanan #' . ($kode ?? 'GTX-908211') . ' — Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Tombol Kembali -->
    <div class="mb-6 flex items-center justify-between" data-aos="fade-down" data-aos-duration="400">
        <a href="{{ route('member.pesanan.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Riwayat Pesanan</span>
        </a>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Pembayaran Lunas
        </span>
    </div>

    @php
        $bookingCode = $kode ?? 'GTX-908211';
    @endphp

    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-8" data-aos="fade-up" data-aos-duration="500">
        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rincian Reservasi Tiket</span>
                <h1 class="font-heading text-2xl font-extrabold text-slate-900 mt-0.5">Pesanan #{{ $bookingCode }}</h1>
                <p class="text-xs text-slate-500 mt-1">Dipesan pada {{ date('d M Y, H:i') }} WIB</p>
            </div>

            <a href="{{ route('member.tiket.show', $bookingCode) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-sm self-start sm:self-auto">
                <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
                <span>Buka E-Tiket Perjalanan</span>
            </a>
        </div>

        <!-- Rute & Armada -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-slate-50 rounded-2xl border border-slate-200/80">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Rute Perjalanan</span>
                <div class="text-base font-bold text-slate-900 mt-1">Jakarta (Gambir) → Bandung (Dipatiukur)</div>
                <div class="text-xs text-slate-600 mt-1">Jadwal: {{ date('d M Y', strtotime('+2 days')) }} pukul 08:30 WIB</div>
            </div>

            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Armada & Fasilitas</span>
                <div class="text-base font-bold text-slate-900 mt-1">Toyota HiAce Premio Executive</div>
                <div class="text-xs text-slate-600 mt-1">Plat BE 1212 AT &bull; AC &bull; Reclining Seat &bull; USB Port</div>
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
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ Auth::user()->nama ?? 'Budi Santoso' }}</td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono text-xs">{{ Auth::user()->telepon ?? '0812-3456-7890' }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-sky-600">Kursi #02</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rincian Pembayaran -->
        <div>
            <h2 class="font-heading font-bold text-slate-900 text-base mb-4">Rincian Pembayaran</h2>
            <div class="border border-slate-200 rounded-2xl p-5 space-y-3 bg-white">
                <div class="flex items-center justify-between text-sm text-slate-600">
                    <span>Tarif Tiket Armada Executive (1 Kursi)</span>
                    <span class="font-medium text-slate-900">Rp 120.000</span>
                </div>
                <div class="flex items-center justify-between text-sm text-slate-600">
                    <span>Biaya Layanan & Administrasi Transfer</span>
                    <span class="font-medium text-emerald-600">GRATIS</span>
                </div>
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-base font-extrabold text-slate-900">
                    <span>Total Pembayaran</span>
                    <span class="text-xl text-sky-600">Rp 120.000</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
