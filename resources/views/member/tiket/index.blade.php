@extends('layouts.tamu')

@section('title', 'Tiket Saya — Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('member._nav', [
        'judul' => 'E-Tiket Perjalanan Saya',
        'deskripsi' => 'Daftar tiket aktif yang siap digunakan dan riwayat perjalanan Anda di seluruh rute Indonesia.'
    ])

    @if ($tiketList->isEmpty())
        <div class="text-center py-16 bg-white border-2 border-slate-900 rounded-2xl p-8 shadow-[5px_5px_0px_#18181b]">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-slate-900 text-amber-600 flex items-center justify-center mx-auto mb-4 shadow-[3px_3px_0px_#18181b]">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
            </div>
            <h3 class="font-heading font-black text-slate-900 text-xl">Belum Ada Tiket Perjalanan</h3>
            <p class="text-sm text-slate-600 mt-2 max-w-md mx-auto font-medium">
                Anda belum memiliki tiket perjalanan aktif. Silakan pilih rute keberangkatan travel eksekutif dan amankan kursi Anda sekarang.
            </p>
            <div class="mt-6">
                <x-button href="{{ route('jadwal.cari') }}" variant="primary" size="md">
                    <span>Cari Jadwal Travel</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </x-button>
            </div>
        </div>
    @else
        <!-- Grid Tiket Perjalanan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($tiketList as $item)
                @php
                    $kode = $item->kode_pesanan;
                    $asal = $item->jadwal?->kotaAsal?->nama_kota ?? 'Kota Asal';
                    $tujuan = $item->jadwal?->kotaTujuan?->nama_kota ?? 'Kota Tujuan';
                    $waktuBerangkat = $item->jadwal?->waktu_berangkat ? \Carbon\Carbon::parse($item->jadwal->waktu_berangkat) : null;
                    $kursi = $item->nomor_kursi;
                    $armada = ($item->jadwal?->kendaraan?->nama_kendaraan ?? 'Minibus Eksekutif') . ' (' . ($item->jadwal?->kendaraan?->nomor_polisi ?? '-') . ')';
                @endphp

                <div class="bg-white border-2 border-slate-900 rounded-2xl shadow-[4px_4px_0px_#18181b] hover:shadow-[6px_6px_0px_#18181b] hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between overflow-hidden">
                    <!-- Top Strip -->
                    <div class="px-5 py-3.5 bg-amber-50 border-b-2 border-slate-900 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-xs px-2.5 py-1 rounded-md bg-white border-1.5 border-slate-900 text-slate-900 shadow-[1.5px_1.5px_0px_#18181b]">
                                {{ $kode }}
                            </span>
                            <x-badge-status :status="$item->status" size="sm" />
                        </div>
                        <div class="font-heading font-black text-xs text-slate-900 px-2.5 py-1 bg-amber-400 border-1.5 border-slate-900 rounded-full shadow-[1px_1px_0px_#18181b]">
                            Kursi #{{ $kursi }}
                        </div>
                    </div>

                    <!-- Detail Rute & Waktu -->
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex-1">
                                <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-400">Dari</span>
                                <h4 class="font-heading font-black text-xl text-slate-900 truncate">{{ $asal }}</h4>
                            </div>

                            <div class="px-2 py-1 bg-amber-100 border border-slate-900 rounded-full text-slate-950 text-xs font-bold shrink-0">
                                &rarr;
                            </div>

                            <div class="flex-1 text-right">
                                <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-400">Tujuan</span>
                                <h4 class="font-heading font-black text-xl text-slate-900 truncate">{{ $tujuan }}</h4>
                            </div>
                        </div>

                        <!-- Jam & Info Armada -->
                        <div class="p-3 bg-stone-50 rounded-xl border-1.5 border-slate-900/40 flex items-center justify-between gap-3 text-xs">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900">
                                    {{ $waktuBerangkat ? $waktuBerangkat->translatedFormat('d M Y') : '-' }}
                                </span>
                                <p class="text-slate-500 font-medium">
                                    Pukul {{ $waktuBerangkat ? $waktuBerangkat->format('H:i') . ' WIB' : '-' }}
                                </p>
                            </div>

                            <div class="text-right text-[11px] text-slate-600 font-medium">
                                <span class="font-bold text-slate-900">Armada:</span>
                                <div class="truncate max-w-[160px]">{{ $armada }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="px-5 py-3.5 border-t-2 border-slate-900 bg-white flex items-center justify-between gap-3">
                        <div class="text-xs font-heading font-black text-slate-900">
                            Rp {{ number_format($item->harga_snapshot, 0, ',', '.') }}
                        </div>

                        @if ($item->status === \App\Enums\StatusPesanan::Lunas)
                            <x-button href="{{ route('member.tiket.show', $kode) }}" variant="dark" size="sm">
                                <span>Buka E-Tiket</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </x-button>
                        @elseif ($item->status === \App\Enums\StatusPesanan::BelumBayar)
                            <x-button href="{{ route('member.pembayaran.show', $kode) }}" variant="primary" size="sm">
                                <span>Bayar Sekarang</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </x-button>
                        @else
                            <x-button href="{{ route('member.pesanan.show', $kode) }}" variant="secondary" size="sm">
                                <span>Detail Pesanan</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </x-button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
