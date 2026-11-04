@extends('layouts.tamu')

@section('title', 'Cari Jadwal Travel Antar Kota — Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Header Halaman -->
    <div class="mb-8" data-aos="fade-down" data-aos-duration="400">
        <h1 class="font-heading text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">
            Cari Jadwal Keberangkatan Travel
        </h1>
        <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
            Pilih rute kota asal, kota tujuan, dan tanggal keberangkatan untuk menemukan armada travel executive terbaik di seluruh Indonesia.
        </p>
    </div>

    <!-- Kotak Filter Pencarian -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm mb-10" data-aos="fade-up" data-aos-duration="500">
        <form action="{{ route('jadwal.cari') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Kota Asal -->
            <div>
                <label for="kota_asal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kota Asal
                </label>
                <div class="relative">
                    <select id="kota_asal" name="kota_asal"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                        <option value="">Semua Kota Asal</option>
                        @foreach ($daftarKota as $kota)
                            <option value="{{ $kota->nama_kota }}" {{ request('kota_asal') === $kota->nama_kota ? 'selected' : '' }}>
                                {{ $kota->nama_kota }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kota Tujuan -->
            <div>
                <label for="kota_tujuan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kota Tujuan
                </label>
                <div class="relative">
                    <select id="kota_tujuan" name="kota_tujuan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
                        <option value="">Semua Kota Tujuan</option>
                        @foreach ($daftarKota as $kota)
                            <option value="{{ $kota->nama_kota }}" {{ request('kota_tujuan') === $kota->nama_kota ? 'selected' : '' }}>
                                {{ $kota->nama_kota }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tanggal Berangkat -->
            <div>
                <label for="tanggal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tanggal Keberangkatan
                </label>
                <input type="date" id="tanggal" name="tanggal"
                       value="{{ request('tanggal') }}"
                       min="{{ date('Y-m-d') }}"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white text-sm font-semibold rounded-xl transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Cari Jadwal</span>
                </button>
                @if (request()->hasAny(['kota_asal', 'kota_tujuan', 'tanggal']))
                    <a href="{{ route('jadwal.cari') }}"
                       class="p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition"
                       title="Reset Filter">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
                <h2 class="font-heading text-lg font-bold text-slate-900">
                    Ditemukan <span class="text-sky-600">{{ $hasilJadwal->count() }}</span> Jadwal Perjalanan
                </h2>
                <span class="text-xs text-slate-500 font-medium">Kapasitas maksimal 5 kursi per armada</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5" data-aos="fade-up" data-aos-duration="500">
                @foreach ($hasilJadwal as $jadwal)
                    @php
                        $terisi = $jadwal->pesanan->whereIn('status', [
                            \App\Enums\StatusPesanan::BelumBayar,
                            \App\Enums\StatusPesanan::DalamProses,
                            \App\Enums\StatusPesanan::Lunas
                        ])->count();
                        $kapasitas = $jadwal->kendaraan?->kapasitas_kursi ?? 5;
                        $sisaKursi = max(0, $kapasitas - $terisi);
                        $tgl = $jadwal->tanggal_berangkat ? date('d M Y', strtotime($jadwal->tanggal_berangkat)) : '-';
                        $jam = $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) . ' WIB' : '-';
                    @endphp

                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:border-slate-300 transition duration-150 flex flex-col justify-between">
                        <div>
                            <!-- Header Kartu -->
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $jadwal->kendaraan?->jenis_kendaraan ?? 'Executive Shuttle' }} &bull; {{ $jadwal->kendaraan?->nomor_polisi ?? '-' }}
                                </span>

                                @if ($sisaKursi > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Sisa {{ $sisaKursi }} Kursi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Kursi Penuh
                                    </span>
                                @endif
                            </div>

                            <!-- Rute Asal -> Tujuan -->
                            <div class="space-y-3 mb-5">
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-sky-50 border border-sky-200/80 flex items-center justify-center text-sky-600 mt-0.5 shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Kota Keberangkatan</div>
                                        <div class="text-sm font-bold text-slate-900">{{ $jadwal->kotaAsal?->nama_kota }}</div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 mt-0.5 shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Kota Kedatangan</div>
                                        <div class="text-sm font-bold text-slate-900">{{ $jadwal->kotaTujuan?->nama_kota }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 text-xs text-slate-600 mb-4 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>{{ $tgl }} &bull; <strong class="text-slate-900 font-semibold">{{ $jam }}</strong></span>
                            </div>
                        </div>

                        <!-- Footer & Harga -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] uppercase font-semibold text-slate-400 block">Tarif Per Tiket</span>
                                <span class="text-base font-extrabold text-slate-900">
                                    Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                                </span>
                            </div>

                            @if ($sisaKursi > 0)
                                @auth
                                    <a href="{{ route('member.tiket.index') }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-xs">
                                        <span>Pesan Kursi</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ route('masuk') }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-xs">
                                        <span>Masuk untuk Pesan</span>
                                    </a>
                                @endauth
                            @else
                                <button type="button" disabled
                                        class="px-4 py-2 text-xs font-semibold text-slate-400 bg-slate-100 rounded-xl cursor-not-allowed">
                                    Kursi Habis
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Keadaan Kosong (Empty State) -->
        <div class="text-center py-12 px-6 bg-white border border-slate-200 rounded-2xl mb-12 shadow-sm" data-aos="fade-up">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <h3 class="font-heading font-bold text-slate-900 text-lg">Jadwal Tidak Ditemukan</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                Maaf, belum ada jadwal keberangkatan untuk rute dan tanggal yang Anda pilih. Coba sesuaikan filter pencarian atau lihat pilihan jadwal terdekat berikut.
            </p>
            <a href="{{ route('jadwal.cari') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                Tampilkan Semua Jadwal
            </a>
        </div>

        <!-- Rekomendasi Jadwal Alternatif -->
        @if ($jadwalRekomendasi->isNotEmpty())
            <div class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-slate-900">
                            Jadwal Alternatif yang Tersedia
                        </h2>
                        <p class="text-xs text-slate-500">Rute terdekat yang siap dipesan untuk perjalanan Anda berikutnya.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5" data-aos="fade-up" data-aos-duration="500">
                    @foreach ($jadwalRekomendasi as $jadwal)
                        @php
                            $terisi = $jadwal->pesanan->whereIn('status', [
                                \App\Enums\StatusPesanan::BelumBayar,
                                \App\Enums\StatusPesanan::DalamProses,
                                \App\Enums\StatusPesanan::Lunas
                            ])->count();
                            $kapasitas = $jadwal->kendaraan?->kapasitas_kursi ?? 5;
                            $sisaKursi = max(0, $kapasitas - $terisi);
                            $tgl = $jadwal->tanggal_berangkat ? date('d M Y', strtotime($jadwal->tanggal_berangkat)) : '-';
                            $jam = $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) . ' WIB' : '-';
                        @endphp

                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-slate-300 transition duration-150 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $jadwal->kendaraan?->jenis_kendaraan ?? 'Travel' }}
                                    </span>
                                    @if ($sisaKursi > 0)
                                        <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                            Sisa {{ $sisaKursi }} Kursi
                                        </span>
                                    @else
                                        <span class="text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full">
                                            Penuh
                                        </span>
                                    @endif
                                </div>

                                <div class="font-bold text-slate-900 text-sm mb-1">
                                    {{ $jadwal->kotaAsal?->nama_kota }} → {{ $jadwal->kotaTujuan?->nama_kota }}
                                </div>
                                <div class="text-xs text-slate-500 mb-4">
                                    {{ $tgl }} &bull; {{ $jam }}
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-sm">
                                    Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                                </span>

                                @auth
                                    <a href="{{ route('member.tiket.index') }}"
                                       class="px-3 py-1.5 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition">
                                        Pesan
                                    </a>
                                @else
                                    <a href="{{ route('masuk') }}"
                                       class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                        Pesan
                                    </a>
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
