@extends('layouts.tamu')

@section('title', 'Pilih Kursi — ' . $jadwal->kotaAsal->nama_kota . ' → ' . $jadwal->kotaTujuan->nama_kota . ' — Gotix')

@section('konten')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-heading font-bold text-slate-600 mb-6">
        <a href="{{ route('beranda') }}" class="hover:text-amber-600 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('jadwal.cari') }}" class="hover:text-amber-600 transition">Cari Jadwal</a>
        <span>/</span>
        <span class="text-slate-900">Pilih Kursi</span>
    </nav>

    {{-- Header --}}
    <div class="mb-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 border-2 border-slate-900 text-slate-950 font-heading text-xs font-black shadow-[2px_2px_0px_#18181b] mb-2.5">
            <span>Langkah 1 dari 2</span>
            <span class="text-slate-400">&bull;</span>
            <span>Reservasi Kursi</span>
        </div>
        <h1 class="font-heading text-2xl md:text-3xl font-black text-slate-900 tracking-tight">
            Pilih Nomor Kursi
        </h1>
        <p class="text-sm text-slate-600 mt-1 font-medium">Pilih kursi yang Anda inginkan. Kuota kursi eksklusif terproteksi anti bentrok.</p>
    </div>

    {{-- Flash Error --}}
    @if ($errors->any())
        <div class="mb-5 bg-rose-100 border-2 border-slate-900 text-rose-950 rounded-2xl p-4 text-xs font-bold shadow-[3px_3px_0px_#18181b] flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Info Jadwal --}}
    <div class="bg-white border-2 border-slate-900 rounded-2xl shadow-[4px_4px_0px_#18181b] mb-6 overflow-hidden">
        <div class="bg-amber-500 px-5 py-3.5 border-b-2 border-slate-900 flex items-center justify-between text-slate-950">
            <div>
                <p class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-900/80">Rute Perjalanan</p>
                <h3 class="font-heading font-black text-xl mt-0.5">
                    {{ $jadwal->kotaAsal->nama_kota }}
                    <span class="mx-1.5">&rarr;</span>
                    {{ $jadwal->kotaTujuan->nama_kota }}
                </h3>
            </div>
            <div class="font-heading font-black text-lg bg-white px-3 py-1 rounded-xl border-2 border-slate-900 shadow-[1.5px_1.5px_0px_#18181b]">
                Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
            </div>
        </div>

        <div class="px-5 py-4 grid grid-cols-2 md:grid-cols-3 gap-4 text-xs bg-stone-50">
            <div>
                <p class="font-heading font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Waktu Berangkat</p>
                <p class="font-black text-slate-900 text-sm">
                    {{ $jadwal->waktu_berangkat ? \Carbon\Carbon::parse($jadwal->waktu_berangkat)->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                </p>
            </div>
            <div>
                <p class="font-heading font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Armada Travel</p>
                <p class="font-bold text-slate-900 text-sm">
                    {{ $jadwal->kendaraan->nama_kendaraan ?? $jadwal->kendaraan->jenis_kendaraan }}
                </p>
                <p class="text-[11px] text-slate-500 font-mono">{{ $jadwal->kendaraan->nomor_polisi }}</p>
            </div>
            <div class="col-span-2 md:col-span-1">
                <p class="font-heading font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Kapasitas Kursi</p>
                <p class="font-bold text-slate-900 text-sm">
                    Maksimal {{ $jadwal->kendaraan->kapasitas_kursi ?? 5 }} Penumpang
                </p>
            </div>
        </div>
    </div>

    {{-- Form Pilih Kursi --}}
    <form action="{{ route('member.pesanan.store', $jadwal->id) }}" method="POST" id="formPesan" x-data="{ kursiDipilih: null }">
        @csrf

        <div class="bg-white border-2 border-slate-900 rounded-2xl shadow-[5px_5px_0px_#18181b] p-6">
            <h2 class="font-heading font-black text-slate-900 text-base mb-1">Denah Kursi Kendaraan</h2>
            <p class="text-xs text-slate-600 mb-5 font-medium">Klik salah satu kursi kosong di bawah ini untuk memilih posisi duduk Anda.</p>

            {{-- Legenda Denah Kursi --}}
            <div class="flex flex-wrap items-center gap-4 mb-6 p-3 bg-stone-50 rounded-xl border border-slate-200 text-xs font-heading font-bold">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-white border-2 border-slate-900 shadow-[1px_1px_0px_#18181b]"></div>
                    <span class="text-slate-800">Tersedia</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-amber-500 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b]"></div>
                    <span class="text-slate-950">Dipilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-stone-200 border-2 border-slate-300 bg-hatch-pattern"></div>
                    <span class="text-slate-400">Sudah Terisi</span>
                </div>
            </div>

            {{-- Grid Kursi Dinamis --}}
            @php
                $kapasitas = $jadwal->kendaraan->kapasitas_kursi ?? 5;
            @endphp

            <div class="p-6 bg-stone-50 rounded-2xl border-2 border-slate-900/60 mb-6">
                <div class="flex items-center justify-between text-[11px] font-heading font-black uppercase tracking-wider text-slate-500 mb-4 pb-2 border-b border-slate-300">
                    <span>Sopir / Kemudi</span>
                    <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700">Depan Armada</span>
                </div>

                <div class="grid grid-cols-5 gap-3 sm:gap-4">
                    @for ($i = 1; $i <= $kapasitas; $i++)
                        @php $terisi = in_array($i, $kursiTerisi); @endphp
                        <label class="{{ $terisi ? 'cursor-not-allowed' : 'cursor-pointer' }} relative block group">
                            <input
                                type="radio"
                                name="nomor_kursi"
                                value="{{ $i }}"
                                class="sr-only peer"
                                {{ $terisi ? 'disabled' : '' }}
                                x-on:change="kursiDipilih = {{ $i }}"
                            >
                            <div class="
                                w-full aspect-square flex flex-col items-center justify-center rounded-xl border-2 transition-all duration-150 select-none
                                {{ $terisi
                                    ? 'bg-stone-200 border-slate-300 text-slate-400 bg-hatch-pattern'
                                    : 'bg-white border-slate-900 text-slate-900 shadow-[3px_3px_0px_#18181b] hover:bg-amber-100 hover:-translate-y-0.5 peer-checked:bg-amber-500 peer-checked:text-slate-950 peer-checked:shadow-[3px_3px_0px_#18181b]'
                                }}
                            ">
                                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10V7a2 2 0 012-2h10a2 2 0 012 2v3M5 10h14M5 10l-1 7h16l-1-7M8 17v2m8-2v2"/>
                                </svg>
                                <span class="font-heading font-black text-xs sm:text-sm">#{{ $i }}</span>
                            </div>
                        </label>
                    @endfor
                </div>
            </div>

            {{-- Ringkasan Pilihan --}}
            <div class="bg-amber-50 rounded-2xl border-2 border-slate-900 px-5 py-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-500">Pilihan Kursi Anda</span>
                    <div class="font-heading font-black text-slate-900 text-lg">
                        <span x-text="kursiDipilih ? 'Nomor Kursi #' + kursiDipilih : 'Belum Memilih Kursi'"></span>
                    </div>
                </div>

                <div class="sm:text-right">
                    <span class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-500">Total Tarif</span>
                    <div class="font-heading font-black text-amber-600 text-xl">
                        Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Tombol Tindakan --}}
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <x-button href="{{ route('jadwal.cari') }}" variant="secondary" size="md" class="w-full sm:w-auto">
                    Batal
                </x-button>

                <x-button type="submit" variant="primary" size="md" class="w-full sm:flex-1">
                    <span>Lanjutkan ke Pembayaran</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </x-button>
            </div>
        </div>
    </form>
</div>
@endsection
