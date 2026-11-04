@extends('layouts.tamu')

@section('title', 'E-Tiket #' . ($kode ?? 'GTX-JKTBDG') . ' — Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb / Tombol Kembali -->
    <div class="mb-6 flex items-center justify-between gap-4 print:hidden" data-aos="fade-down" data-aos-duration="400">
        <a href="{{ route('member.tiket.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Tiket</span>
        </a>

        <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition duration-150 shadow-sm cursor-pointer">
            <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.641-2.062-1.17-2.915a11.96 11.96 0 0 1 7.45-7.45c.853.529 1.839.93 2.915 1.17a11.96 11.96 0 0 1-7.45 7.45c-.853-.529-1.839-.93-2.915-1.17ZM6 18v3h12v-3m0-6V4H6v8m12 0H6" />
            </svg>
            <span>Cetak E-Tiket</span>
        </button>
    </div>

    @php
        $bookingCode = $kode ?? 'GTX-JKTBDG';
    @endphp

    <!-- E-Tiket Boarding Pass Card -->
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm" data-aos="zoom-in" data-aos-duration="500">
        <!-- Header Boarding Pass -->
        <div class="bg-slate-900 text-white p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center font-heading font-bold text-xl text-sky-400">
                    G
                </div>
                <div>
                    <div class="font-heading font-extrabold text-xl tracking-tight">Gotix Executive Travel</div>
                    <div class="text-xs text-slate-400">Boarding Pass Resmi Penumpang Antar Kota</div>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <div class="text-xs uppercase tracking-wider text-slate-400 font-medium">Kode Pemesanan</div>
                <div class="font-mono text-xl font-bold tracking-wider text-sky-400">{{ $bookingCode }}</div>
            </div>
        </div>

        <!-- Rute Utama & Jadwal -->
        <div class="p-6 sm:p-8 border-b border-slate-200 bg-slate-50/50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <!-- Kota Asal -->
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Keberangkatan</div>
                    <div class="font-heading text-2xl font-bold text-slate-900">Jakarta (Gambir)</div>
                    <p class="text-xs text-slate-500 mt-1">Pool Gotix Gambir, Jl. Medan Merdeka Timur No. 12</p>
                </div>

                <!-- Indikator Rute / Panah -->
                <div class="flex flex-col items-center justify-center text-center py-2 md:py-0">
                    <div class="text-xs font-semibold text-sky-700 bg-sky-50 px-3 py-1 rounded-full border border-sky-200 mb-2">
                        Perjalanan Langsung
                    </div>
                    <div class="w-full flex items-center gap-2 max-w-[160px]">
                        <div class="h-0.5 bg-slate-300 flex-grow"></div>
                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                        <div class="h-0.5 bg-slate-300 flex-grow"></div>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">Est. 2 Jam 30 Menit via Tol Cipularang</div>
                </div>

                <!-- Kota Tujuan -->
                <div class="md:text-right">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Destinasi Tiba</div>
                    <div class="font-heading text-2xl font-bold text-slate-900">Bandung (Dipatiukur)</div>
                    <p class="text-xs text-slate-500 mt-1">Pool Gotix Dipatiukur No. 45, Coblong</p>
                </div>
            </div>
        </div>

        <!-- Detail Penumpang & Kursi -->
        <div class="p-6 sm:p-8">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 mb-8">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nama Penumpang</div>
                    <div class="text-sm font-bold text-slate-900">{{ Auth::user()->nama ?? 'Budi Santoso' }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->telepon ?? '0812-3456-7890' }}</div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Tanggal & Waktu</div>
                    <div class="text-sm font-bold text-slate-900">{{ date('d M Y', strtotime('+2 days')) }}</div>
                    <div class="text-xs text-slate-500">Pukul 08:30 WIB</div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Armada Kendaraan</div>
                    <div class="text-sm font-bold text-slate-900">HiAce Premio</div>
                    <div class="text-xs text-slate-500 font-mono">BE 1212 AT (Hitam)</div>
                </div>

                <div class="bg-sky-50 border border-sky-200/80 rounded-2xl p-3.5 text-center">
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-sky-800">Nomor Kursi</div>
                    <div class="font-heading text-3xl font-extrabold text-sky-600">02</div>
                    <div class="text-[10px] text-sky-700">Kapasitas 5 Seat</div>
                </div>
            </div>

            <!-- Tiket Perforasi Divider -->
            <div class="relative py-4 my-2">
                <div class="border-t-2 border-dashed border-slate-200"></div>
            </div>

            <!-- Bagian Bawah: QR Code & Petunjuk Validasi -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-2">
                <div class="flex items-center gap-5">
                    <!-- SVG QR Code Dummy Minimalist -->
                    <div class="w-24 h-24 p-2 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-full h-full text-slate-900" viewBox="0 0 100 100" fill="currentColor">
                            <!-- Outer Corners -->
                            <rect x="5" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="6"/>
                            <rect x="13" y="13" width="10" height="10" fill="currentColor"/>
                            <rect x="69" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="6"/>
                            <rect x="77" y="13" width="10" height="10" fill="currentColor"/>
                            <rect x="5" y="69" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="6"/>
                            <rect x="13" y="77" width="10" height="10" fill="currentColor"/>
                            <!-- Random Pattern dots -->
                            <rect x="40" y="10" width="8" height="8"/>
                            <rect x="52" y="18" width="8" height="8"/>
                            <rect x="40" y="30" width="8" height="8"/>
                            <rect x="10" y="45" width="8" height="8"/>
                            <rect x="25" y="45" width="8" height="8"/>
                            <rect x="45" y="45" width="12" height="12"/>
                            <rect x="65" y="40" width="8" height="8"/>
                            <rect x="80" y="45" width="10" height="8"/>
                            <rect x="40" y="65" width="8" height="8"/>
                            <rect x="55" y="75" width="8" height="8"/>
                            <rect x="75" y="65" width="14" height="8"/>
                            <rect x="70" y="80" width="18" height="10"/>
                        </svg>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-slate-900 mb-1">Verifikasi Boarding Penumpang</div>
                        <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                            Tunjukkan barcode ini kepada petugas loket atau sopir armada saat sebelum keberangkatan. Harap tiba di titik kumpul minimal 15 menit lebih awal.
                        </p>
                        <div class="mt-2 text-[11px] font-mono text-emerald-600 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Status Tiket: TERVERIFIKASI & LUNAS
                        </div>
                    </div>
                </div>

                <div class="text-right sm:self-end">
                    <div class="text-xs text-slate-400">Total Tarif Resmi</div>
                    <div class="font-heading text-xl font-extrabold text-slate-900">Rp 120.000</div>
                    <div class="text-[10px] text-slate-400">Bebas Biaya Bagasi s/d 10 Kg</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
