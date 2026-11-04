@extends('layouts.tamu')

@section('title', 'Pilih Kursi — ' . $jadwal->kotaAsal->nama_kota . ' → ' . $jadwal->kotaTujuan->nama_kota . ' — Gotix')

@section('konten')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-6">
        <a href="{{ route('beranda') }}" class="hover:text-sky-600 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('jadwal.cari') }}" class="hover:text-sky-600 transition">Cari Jadwal</a>
        <span>/</span>
        <span class="text-slate-800 font-medium">Pesan Tiket</span>
    </nav>

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="font-heading text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">
            Pilih Nomor Kursi
        </h1>
        <p class="text-sm text-slate-500 mt-1">Pilih kursi yang tersedia untuk perjalanan Anda.</p>
    </div>

    {{-- Flash Error --}}
    @if ($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Info Jadwal --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-6 overflow-hidden">
        <div class="bg-slate-900 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="text-white">
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Rute Perjalanan</p>
                    <p class="font-heading font-bold text-xl mt-0.5">
                        {{ $jadwal->kotaAsal->nama_kota }}
                        <span class="text-sky-400 mx-2">→</span>
                        {{ $jadwal->kotaTujuan->nama_kota }}
                    </p>
                </div>
            </div>
        </div>
        <div class="px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-0.5">Tanggal</p>
                <p class="font-semibold text-slate-800">{{ $jadwal->tanggal_berangkat->translatedFormat('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-0.5">Jam</p>
                <p class="font-semibold text-slate-800">{{ substr($jadwal->jam_berangkat, 0, 5) }} WIB</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-0.5">Armada</p>
                <p class="font-semibold text-slate-800">{{ $jadwal->kendaraan->jenis_kendaraan }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-0.5">Harga / Kursi</p>
                <p class="font-bold text-sky-600 text-base">Rp {{ number_format($jadwal->harga, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Form Pilih Kursi --}}
    <form action="{{ route('member.pesanan.store', $jadwal->id) }}" method="POST" id="formPesan">
        @csrf

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 md:p-6">
            <h2 class="font-heading font-bold text-slate-900 text-sm mb-1">Denah Kursi Kendaraan</h2>
            <p class="text-xs text-slate-500 mb-5">Klik kursi yang tersedia (putih) untuk memilihnya. Kursi abu-abu sudah terisi.</p>

            {{-- Legenda --}}
            <div class="flex items-center gap-4 mb-5 text-xs text-slate-600">
                <div class="flex items-center gap-1.5">
                    <div class="w-7 h-7 rounded-lg bg-white border-2 border-slate-200"></div>
                    <span>Tersedia</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-7 h-7 rounded-lg bg-slate-200 border-2 border-slate-300"></div>
                    <span>Terisi</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-7 h-7 rounded-lg bg-sky-600 border-2 border-sky-700"></div>
                    <span>Dipilih</span>
                </div>
            </div>

            {{-- Grid Kursi --}}
            @php
                $kapasitas = $jadwal->kendaraan->kapasitas_kursi ?? 5;
            @endphp

            <div class="grid grid-cols-5 gap-3 mb-6" x-data="{ kursiDipilih: null }">
                @for ($i = 1; $i <= $kapasitas; $i++)
                    @php $terisi = in_array($i, $kursiTerisi); @endphp
                    <label
                        class="{{ $terisi ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }} relative"
                        x-bind:class="kursiDipilih === {{ $i }} ? '' : ''"
                    >
                        <input
                            type="radio"
                            name="nomor_kursi"
                            value="{{ $i }}"
                            class="sr-only peer"
                            {{ $terisi ? 'disabled' : '' }}
                            x-on:change="kursiDipilih = {{ $i }}"
                        >
                        <div class="
                            w-full aspect-square flex flex-col items-center justify-center rounded-xl border-2 transition-all duration-150
                            {{ $terisi
                                ? 'bg-slate-100 border-slate-200 text-slate-400'
                                : 'bg-white border-slate-200 text-slate-700 hover:border-sky-500 hover:bg-sky-50 peer-checked:bg-sky-600 peer-checked:border-sky-700 peer-checked:text-white'
                            }}
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10V7a2 2 0 012-2h10a2 2 0 012 2v3M5 10h14M5 10l-1 7h16l-1-7M8 17v2m8-2v2"/>
                            </svg>
                            <span class="text-xs font-bold">{{ $i }}</span>
                        </div>
                    </label>
                @endfor
            </div>

            {{-- Info Harga --}}
            <div class="bg-slate-50 rounded-xl px-4 py-3 mb-5 flex items-center justify-between">
                <span class="text-sm text-slate-600">Total Pembayaran</span>
                <span class="font-heading font-bold text-sky-600 text-lg">Rp {{ number_format($jadwal->harga, 0, ',', '.') }}</span>
            </div>

            {{-- Tombol --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('jadwal.cari') }}"
                   class="flex-1 text-center px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition">
                    Konfirmasi Pemesanan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

