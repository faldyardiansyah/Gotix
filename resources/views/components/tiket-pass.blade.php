@props([
    'pesanan',
    'qrSvg' => null,
    'mode' => 'full',
])

@php
    $jadwal = $pesanan->jadwal;
    $namaPenumpang = $pesanan->nama_penumpang ?? $pesanan->pengguna->nama ?? 'Penumpang';
    $teleponPenumpang = $pesanan->telepon_penumpang ?? $pesanan->pengguna->telepon ?? '-';
    $waktuBerangkat = $jadwal ? \Carbon\Carbon::parse($jadwal->waktu_berangkat) : null;
    $waktuTiba = $jadwal && $jadwal->waktu_tiba ? \Carbon\Carbon::parse($jadwal->waktu_tiba) : null;

    if (!$qrSvg && $pesanan) {
        try {
            $qrSvg = app(\App\Services\TiketService::class)->generateQrSvg($pesanan);
        } catch (\Throwable $e) {
            $qrSvg = null;
        }
    }
@endphp

<div {{ $attributes->merge(['class' => 'relative bg-white border-2 border-slate-900 rounded-2xl shadow-[5px_5px_0px_#18181b] overflow-hidden select-none']) }}>
    <!-- Header Top Strip -->
    <div class="px-5 py-3 bg-amber-500 border-b-2 border-slate-900 flex items-center justify-between gap-3 text-slate-950">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-slate-950 text-amber-400 flex items-center justify-center font-heading font-black text-xs">
                G
            </div>
            <span class="font-heading font-black text-sm tracking-wider uppercase">Gotix Boarding Pass</span>
            <span class="hidden sm:inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-950/10 border border-slate-950/20">Eksekutif</span>
        </div>

        <div class="font-mono font-black text-sm tracking-widest bg-white border-2 border-slate-900 px-3 py-0.5 rounded-full shadow-[1.5px_1.5px_0px_#18181b]">
            {{ $pesanan->kode_pesanan }}
        </div>
    </div>

    <!-- Main Ticket Body: Split View (Detail & Stub) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 relative">
        <!-- Main Travel Section (Kolom 1-8) -->
        <div class="lg:col-span-8 p-6 sm:p-7 space-y-6">
            <!-- Rute Perjalanan -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b-2 border-slate-100">
                <div>
                    <span class="text-[11px] font-heading font-bold uppercase tracking-wider text-slate-500">Kota Asal</span>
                    <h3 class="font-heading font-black text-2xl text-slate-900 mt-0.5">
                        {{ $jadwal->kotaAsal->nama_kota ?? 'Kota Asal' }}
                    </h3>
                    <p class="text-xs text-slate-600 font-medium">Terminal / Pool Keberangkatan</p>
                </div>

                <div class="flex items-center justify-center sm:px-4 py-2 text-amber-600">
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-[11px] font-heading font-bold uppercase tracking-wider text-slate-400">Perjalanan Langsung</span>
                        <div class="flex items-center gap-2">
                            <span class="w-8 sm:w-16 h-0.5 bg-slate-300 border-t-2 border-dashed border-slate-400"></span>
                            <div class="w-8 h-8 rounded-full bg-amber-500 border-2 border-slate-900 flex items-center justify-center text-slate-950 shadow-[2px_2px_0px_#18181b]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </div>
                            <span class="w-8 sm:w-16 h-0.5 bg-slate-300 border-t-2 border-dashed border-slate-400"></span>
                        </div>
                    </div>
                </div>

                <div class="sm:text-right">
                    <span class="text-[11px] font-heading font-bold uppercase tracking-wider text-slate-500">Kota Tujuan</span>
                    <h3 class="font-heading font-black text-2xl text-slate-900 mt-0.5">
                        {{ $jadwal->kotaTujuan->nama_kota ?? 'Kota Tujuan' }}
                    </h3>
                    <p class="text-xs text-slate-600 font-medium">Terminal / Pool Kedatangan</p>
                </div>
            </div>

            <!-- Detail Penumpang, Waktu & Armada Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="font-heading font-bold text-slate-500 uppercase tracking-wider text-[10px]">Nama Penumpang</span>
                    <p class="font-bold text-slate-900 text-sm mt-0.5 truncate">{{ $namaPenumpang }}</p>
                    <p class="text-[11px] text-slate-500 font-mono">{{ $teleponPenumpang }}</p>
                </div>

                <div>
                    <span class="font-heading font-bold text-slate-500 uppercase tracking-wider text-[10px]">Waktu Berangkat</span>
                    <p class="font-bold text-slate-900 text-sm mt-0.5">
                        {{ $waktuBerangkat ? $waktuBerangkat->format('H:i') . ' WIB' : '-' }}
                    </p>
                    <p class="text-[11px] text-slate-500">
                        {{ $waktuBerangkat ? $waktuBerangkat->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>

                <div>
                    <span class="font-heading font-bold text-slate-500 uppercase tracking-wider text-[10px]">Armada Travel</span>
                    <p class="font-bold text-slate-900 text-sm mt-0.5 truncate">
                        {{ $jadwal->kendaraan->nama_kendaraan ?? 'Minibus Eksekutif' }}
                    </p>
                    <p class="text-[11px] text-slate-500 font-mono">
                        {{ $jadwal->kendaraan->nomor_polisi ?? '-' }}
                    </p>
                </div>

                <div>
                    <span class="font-heading font-bold text-slate-500 uppercase tracking-wider text-[10px]">Tarif Tiket</span>
                    <p class="font-heading font-black text-slate-900 text-sm mt-0.5">
                        Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Status Tiket: TERVERIFIKASI
                    </span>
                </div>
            </div>

            <!-- Instruksi Boarding Penumpang -->
            <div class="p-3.5 bg-amber-50 rounded-xl border-1.5 border-amber-600/60 flex items-start gap-2.5 text-xs text-amber-950 font-medium">
                <svg class="w-4 h-4 text-amber-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <span>Tunjukkan e-tiket fisik ini atau layar smartphone kepada kondektur/sopir sebelum menaiki armada. Harap tiba di titik kumpul minimal 15 menit sebelum keberangkatan.</span>
            </div>
        </div>

        <!-- Perforated Divider Line with Notches (Desktop Border-L, Mobile Border-T) -->
        <div class="lg:col-span-4 bg-stone-50 p-6 sm:p-7 border-t-2 lg:border-t-0 lg:border-l-2 border-dashed border-slate-900 flex flex-col items-center justify-between gap-5 relative">
            <!-- Top Half-Circle Notch (Desktop Left, Mobile Top) -->
            <div class="hidden lg:block absolute -left-3 -top-3 w-6 h-6 bg-stone-100 border-2 border-slate-900 rounded-full"></div>
            <!-- Bottom Half-Circle Notch (Desktop Left, Mobile Bottom) -->
            <div class="hidden lg:block absolute -left-3 -bottom-3 w-6 h-6 bg-stone-100 border-2 border-slate-900 rounded-full"></div>

            <div class="text-center w-full">
                <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-slate-500">Nomor Kursi</span>
                <div class="w-20 h-20 rounded-2xl bg-amber-500 border-2 border-slate-900 mx-auto mt-1 flex items-center justify-center font-heading font-black text-4xl text-slate-950 shadow-[3px_3px_0px_#18181b]">
                    {{ str_pad($pesanan->nomor_kursi, 2, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <!-- Standard BaconQrCode Container -->
            <div class="bg-white p-3 border-2 border-slate-900 rounded-xl shadow-[3px_3px_0px_#18181b] flex flex-col items-center">
                @if ($qrSvg)
                    <div class="w-32 h-32 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                        {!! $qrSvg !!}
                    </div>
                @else
                    <div class="w-32 h-32 flex items-center justify-center bg-slate-100 text-slate-400 text-xs font-mono">
                        [QR Tidak Tersedia]
                    </div>
                @endif
                <span class="text-[10px] font-mono font-bold text-slate-600 mt-2 tracking-wider">PINDAI DI TERMINAL</span>
            </div>

            <!-- Stempel Lunas / Resmi -->
            <div class="w-full text-center">
                <div class="inline-block border-2 border-rose-600 text-rose-600 px-3.5 py-1 rounded-lg font-heading font-black text-xs uppercase tracking-widest rotate-[-3deg] shadow-[2px_2px_0px_#e11d48]">
                    ★ TIKET SAH / LUNAS ★
                </div>
            </div>
        </div>
    </div>
</div>
