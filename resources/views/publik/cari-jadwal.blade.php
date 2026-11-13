@extends('layouts.tamu')

@section('title', 'Cari Jadwal Travel Antar Kota — GoTix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Header Halaman -->
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 border-2 border-slate-900 text-slate-950 font-heading text-xs font-black shadow-[2px_2px_0px_#18181b] mb-3">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span>Reservasi Online GoTix</span>
        </div>
        <h1 class="font-heading text-2xl md:text-4xl font-black text-slate-900 tracking-tight">
            Cari Jadwal Keberangkatan Travel
        </h1>
        <p class="text-sm text-slate-600 mt-1.5 max-w-2xl font-medium">
            Pilih rute kota asal, kota tujuan, dan tanggal keberangkatan untuk menemukan armada travel eksekutif terbaik dengan jaminan kursi pasti.
        </p>
    </div>

    <!-- Kotak Filter Pencarian -->
    <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[5px_5px_0px_#18181b] mb-10">
        <form action="{{ route('jadwal.cari') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Kota Asal -->
            <div>
                <label for="kota_asal" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                    Kota Asal
                </label>
                <div class="relative">
                    <select id="kota_asal" name="kota_asal"
                        class="w-full bg-white border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition appearance-none cursor-pointer">
                        <option value="">Semua Kota Asal</option>
                        @foreach ($daftarKota as $kota)
                        <option value="{{ $kota->nama_kota }}" {{ request('kota_asal') === $kota->nama_kota ? 'selected' : '' }}>
                            {{ $kota->nama_kota }}
                        </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-900">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kota Tujuan -->
            <div>
                <label for="kota_tujuan" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                    Kota Tujuan
                </label>
                <div class="relative">
                    <select id="kota_tujuan" name="kota_tujuan"
                        class="w-full bg-white border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition appearance-none cursor-pointer">
                        <option value="">Semua Kota Tujuan</option>
                        @foreach ($daftarKota as $kota)
                        <option value="{{ $kota->nama_kota }}" {{ request('kota_tujuan') === $kota->nama_kota ? 'selected' : '' }}>
                            {{ $kota->nama_kota }}
                        </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-900">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tanggal Berangkat -->
            <div>
                <label for="tanggal" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                    Tanggal Berangkat
                </label>
                <input type="date" id="tanggal" name="tanggal"
                    value="{{ request('tanggal') }}"
                    min="{{ date('Y-m-d') }}"
                    class="w-full bg-white border-2 border-slate-900 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="primary" size="md" class="flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Cari Jadwal</span>
                </x-button>

                @if (request()->hasAny(['kota_asal', 'kota_tujuan', 'tanggal']))
                <a href="{{ route('jadwal.cari') }}"
                    class="p-2.5 bg-stone-100 hover:bg-stone-200 border-2 border-slate-900 rounded-full text-slate-900 shadow-[2px_2px_0px_#18181b] transition"
                    title="Reset Filter">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Hasil Pencarian -->
    @if ($hasilJadwal->isNotEmpty())
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-heading font-black text-xl text-slate-900">
                Ditemukan <span class="text-amber-600 font-black">{{ $hasilJadwal->count() }}</span> Jadwal Keberangkatan
            </h2>
            <span class="text-xs text-slate-500 font-medium">Kapasitas maksimal 5 kursi per armada</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($hasilJadwal as $jadwal)
            @php
            $terisi = $jadwal->pesanan->whereIn('status', [
            \App\Enums\StatusPesanan::BelumBayar,
            \App\Enums\StatusPesanan::DalamProses,
            \App\Enums\StatusPesanan::Lunas
            ])->count();
            $kapasitas = $jadwal->kendaraan?->kapasitas_kursi ?? 5;
            $sisaKursi = max(0, $kapasitas - $terisi);
            $waktuBerangkat = $jadwal->waktu_berangkat ? \Carbon\Carbon::parse($jadwal->waktu_berangkat) : null;
            @endphp

            <div class="bg-white border-2 border-slate-900 rounded-2xl shadow-[4px_4px_0px_#18181b] hover:shadow-[6px_6px_0px_#18181b] hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between overflow-hidden">
                <div>
                    <!-- Header Kartu -->
                    <div class="px-5 py-3.5 bg-amber-50 border-b-2 border-slate-900 flex items-center justify-between gap-3">
                        <span class="text-xs font-heading font-bold px-2.5 py-0.5 rounded-full bg-white border border-slate-900 text-slate-900 shadow-[1px_1px_0px_#18181b]">
                            {{ $jadwal->kendaraan?->nama_kendaraan ?? $jadwal->kendaraan?->jenis_kendaraan ?? 'Minibus Eksekutif' }}
                        </span>

                        @if ($sisaKursi > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-heading font-black bg-emerald-100 text-emerald-950 border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            Sisa {{ $sisaKursi }} Kursi
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-heading font-black bg-rose-100 text-rose-950 border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            Kursi Penuh
                        </span>
                        @endif
                    </div>

                    <!-- Rute Asal -> Tujuan -->
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex-1">
                                <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-400">Kota Asal</span>
                                <h4 class="font-heading font-black text-xl text-slate-900 truncate">{{ $jadwal->kotaAsal?->nama_kota }}</h4>
                            </div>

                            <div class="px-2.5 py-1 bg-amber-100 border border-slate-900 rounded-full text-slate-950 text-xs font-bold shrink-0">
                                &rarr;
                            </div>

                            <div class="flex-1 text-right">
                                <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-400">Kota Tujuan</span>
                                <h4 class="font-heading font-black text-xl text-slate-900 truncate">{{ $jadwal->kotaTujuan?->nama_kota }}</h4>
                            </div>
                        </div>

                        <div class="p-3 bg-stone-50 rounded-xl border border-slate-200 flex items-center justify-between text-xs text-slate-700 font-medium">
                            <div>
                                <span class="font-bold text-slate-900">{{ $waktuBerangkat ? $waktuBerangkat->translatedFormat('d M Y') : '-' }}</span>
                                <span class="text-slate-400">&bull;</span>
                                <span>Pukul {{ $waktuBerangkat ? $waktuBerangkat->format('H:i') . ' WIB' : '-' }}</span>
                            </div>
                            <span class="font-mono text-slate-500 font-bold">{{ $jadwal->kendaraan?->nomor_polisi ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer & Harga -->
                <div class="px-5 py-3.5 border-t-2 border-slate-900 bg-white flex items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-400 block">Tarif Per Kursi</span>
                        <span class="font-heading font-black text-xl text-slate-900">
                            Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($sisaKursi > 0)
                    @auth
                    <x-button href="{{ route('member.pesanan.create', $jadwal->id) }}" variant="primary" size="sm">
                        <span>Pesan Kursi</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </x-button>
                    @else
                    <x-button href="{{ route('masuk') }}" variant="dark" size="sm">
                        <span>Masuk untuk Pesan</span>
                    </x-button>
                    @endauth
                    @else
                    <span class="px-3.5 py-1.5 text-xs font-heading font-bold text-slate-400 bg-stone-100 rounded-full border border-slate-300">
                        Kursi Habis
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- Keadaan Kosong (Empty State) -->
    <div class="text-center py-12 px-6 bg-white border-2 border-slate-900 rounded-2xl mb-12 shadow-[5px_5px_0px_#18181b]">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-slate-900 text-amber-600 flex items-center justify-center mx-auto mb-4 shadow-[3px_3px_0px_#18181b]">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
        <h3 class="font-heading font-black text-slate-900 text-xl">Jadwal Tidak Ditemukan</h3>
        <p class="text-sm text-slate-600 mt-2 max-w-md mx-auto font-medium">
            Maaf, belum ada jadwal keberangkatan untuk rute dan tanggal yang Anda pilih. Coba sesuaikan filter pencarian atau lihat pilihan jadwal terdekat berikut.
        </p>
        <div class="mt-5">
            <x-button href="{{ route('jadwal.cari') }}" variant="secondary" size="sm">
                Tampilkan Semua Jadwal
            </x-button>
        </div>
    </div>

    <!-- Rekomendasi Jadwal Alternatif -->
    @if ($jadwalRekomendasi->isNotEmpty())
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-heading font-black text-xl text-slate-900">
                    Jadwal Alternatif yang Tersedia
                </h2>
                <p class="text-xs text-slate-600 font-medium">Pilihan rute terdekat yang siap dipesan untuk perjalanan Anda berikutnya.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach ($jadwalRekomendasi as $jadwal)
            @php
            $terisi = $jadwal->pesanan->whereIn('status', [
            \App\Enums\StatusPesanan::BelumBayar,
            \App\Enums\StatusPesanan::DalamProses,
            \App\Enums\StatusPesanan::Lunas
            ])->count();
            $kapasitas = $jadwal->kendaraan?->kapasitas_kursi ?? 5;
            $sisaKursi = max(0, $kapasitas - $terisi);
            $waktuBerangkat = $jadwal->waktu_berangkat ? \Carbon\Carbon::parse($jadwal->waktu_berangkat) : null;
            @endphp

            <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] hover:shadow-[5px_5px_0px_#18181b] hover:-translate-y-0.5 transition duration-150 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-[11px] font-heading font-bold px-2 py-0.5 rounded bg-stone-100 text-slate-800 border border-slate-900">
                            {{ $jadwal->kendaraan?->nama_kendaraan ?? $jadwal->kendaraan?->jenis_kendaraan ?? 'Travel' }}
                        </span>
                        @if ($sisaKursi > 0)
                        <span class="text-[11px] font-heading font-black text-emerald-950 bg-emerald-100 border border-slate-900 px-2 py-0.5 rounded-full shadow-[1px_1px_0px_#18181b]">
                            Sisa {{ $sisaKursi }} Kursi
                        </span>
                        @else
                        <span class="text-[11px] font-heading font-black text-rose-950 bg-rose-100 border border-slate-900 px-2 py-0.5 rounded-full shadow-[1px_1px_0px_#18181b]">
                            Penuh
                        </span>
                        @endif
                    </div>

                    <div class="font-heading font-black text-slate-900 text-base mb-1">
                        {{ $jadwal->kotaAsal?->nama_kota }} &rarr; {{ $jadwal->kotaTujuan?->nama_kota }}
                    </div>
                    <div class="text-xs text-slate-600 mb-4 font-medium">
                        {{ $waktuBerangkat ? $waktuBerangkat->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                    </div>
                </div>

                <div class="pt-3 border-t-2 border-slate-900 flex items-center justify-between">
                    <span class="font-heading font-black text-slate-900 text-sm">
                        Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                    </span>

                    @auth
                    <x-button href="{{ route('member.pesanan.create', $jadwal->id) }}" variant="primary" size="sm">
                        Pesan
                    </x-button>
                    @else
                    <x-button href="{{ route('masuk') }}" variant="secondary" size="sm">
                        Pesan
                    </x-button>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif
</div>
@endsection