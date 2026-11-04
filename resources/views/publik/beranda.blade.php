@extends('layouts.tamu')

@section('title', 'Gotix — Pesan Tiket Travel Antar Kota se-Indonesia')

@section('konten')
@php
    $kotaPilihan = isset($daftarKota) && $daftarKota->isNotEmpty()
        ? $daftarKota->pluck('nama_kota')->toArray()
        : [
            'Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Semarang',
            'Malang', 'Solo', 'Medan', 'Pekanbaru', 'Bandar Lampung', 'Makassar', 'Denpasar'
        ];

    $rutePopuler = [
        [
            'asal' => 'Jakarta',
            'tujuan' => 'Bandung',
            'durasi' => '2 Jam 30 Mnt',
            'harga' => 'Rp 120.000',
            'armada' => 'HiAce Premio 5 Seat',
            'tag' => 'Paling Populer',
        ],
        [
            'asal' => 'Yogyakarta',
            'tujuan' => 'Surabaya',
            'durasi' => '4 Jam 15 Mnt',
            'harga' => 'Rp 180.000',
            'armada' => 'Isuzu Elf Executive',
            'tag' => 'Favorit Wisata',
        ],
        [
            'asal' => 'Semarang',
            'tujuan' => 'Malang',
            'durasi' => '4 Jam 45 Mnt',
            'harga' => 'Rp 195.000',
            'armada' => 'Avanza Veloz Prime',
            'tag' => 'Jalur Tol Trans',
        ],
        [
            'asal' => 'Medan',
            'tujuan' => 'Pekanbaru',
            'durasi' => '7 Jam',
            'harga' => 'Rp 240.000',
            'armada' => 'Executive Long Seat',
            'tag' => 'Lintas Sumatera',
        ],
        [
            'asal' => 'Bandar Lampung',
            'tujuan' => 'Jakarta',
            'durasi' => '4 Jam 30 Mnt',
            'harga' => 'Rp 220.000',
            'armada' => 'HiAce Shuttle Express',
            'tag' => 'Via Penyeberangan',
        ],
        [
            'asal' => 'Makassar',
            'tujuan' => 'Parepare',
            'durasi' => '3 Jam',
            'harga' => 'Rp 110.000',
            'armada' => 'Innova Zenix Prime',
            'tag' => 'Trans Sulawesi',
        ],
    ];
@endphp

<!-- Hero Section Modern & Elegan -->
<section class="relative pt-10 pb-16 md:pt-16 md:pb-20 border-b border-slate-200/80 overflow-hidden bg-white">
    <!-- Subtle Grid Background -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#f8fafc_1px,transparent_1px),linear-gradient(to_bottom,#f8fafc_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-80 pointer-events-none"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center mb-12">
            <!-- Kolom Teks Hero -->
            <div class="lg:col-span-7 text-center lg:text-left" data-aos="fade-right" data-aos-duration="500">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 border border-sky-200/80 text-sky-700 text-xs font-semibold mb-4">
                    <span class="w-2 h-2 rounded-full bg-sky-600 animate-pulse"></span>
                    Armada Eksekutif Antar Kota se-Indonesia
                </div>

                <h1 class="font-heading text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Perjalanan Nyaman.<br class="hidden sm:inline">
                    <span class="text-sky-600">Pilih Kursi Sendiri</span> Tanpa Bentrok.
                </h1>

                <p class="mt-4 text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Layanan pemesanan tiket travel modern berkapasitas 5 penumpang dengan jaminan kursi pasti dan verifikasi pembayaran manual tanpa biaya perantara.
                </p>

                <!-- Keunggulan Singkat Badge -->
                <div class="mt-6 flex flex-wrap items-center justify-center lg:justify-start gap-4 text-xs font-semibold text-slate-700">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Maksimal 5 Penumpang</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Kabin AC & Reclining</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Penjemputan Door to Door</span>
                    </div>
                </div>
            </div>

            <!-- Kolom Visual Banner Mobil Travel Eksekutif -->
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-duration="500">
                <div class="relative bg-slate-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl border border-slate-800 overflow-hidden">
                    <!-- Aksen Glow Latar Belakang -->
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                        <div>
                            <span class="text-[10px] uppercase font-semibold text-sky-400 tracking-wider">Armada Standar Gotix</span>
                            <div class="font-heading font-bold text-base text-white">Toyota HiAce Premio Executive</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-sky-500/10 border border-sky-400/30 text-sky-300 font-mono text-xs font-bold">
                            5 Seat
                        </span>
                    </div>

                    <!-- Visual Vector Ilustrasi Mobil Travel Minimalis -->
                    <div class="py-6 flex items-center justify-center">
                        <svg class="w-full max-w-[280px] h-32 text-slate-200" viewBox="0 0 320 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Body Mobil Travel Shuttle -->
                            <path d="M20 95 C20 95 25 55 65 45 C105 35 240 35 265 45 C290 55 305 75 305 95 L305 110 C305 113 302 115 299 115 L260 115 C260 100 245 90 230 90 C215 90 200 100 200 115 L120 115 C120 100 105 90 90 90 C75 90 60 100 60 115 L25 115 C22 115 20 113 20 110 Z" fill="#1e293b" stroke="#38bdf8" stroke-width="2"/>
                            <!-- Jendela Kaca Depan & Samping -->
                            <path d="M70 52 L115 52 L115 80 L62 80 Z" fill="#0f172a" stroke="#64748b" stroke-width="1.5"/>
                            <path d="M125 52 L185 52 L185 80 L125 80 Z" fill="#0f172a" stroke="#64748b" stroke-width="1.5"/>
                            <path d="M195 52 L250 52 C265 58 275 68 280 80 L195 80 Z" fill="#0f172a" stroke="#38bdf8" stroke-width="1.5"/>
                            <!-- Roda Depan & Belakang -->
                            <circle cx="90" cy="115" r="18" fill="#0f172a" stroke="#94a3b8" stroke-width="3"/>
                            <circle cx="90" cy="115" r="7" fill="#38bdf8"/>
                            <circle cx="230" cy="115" r="18" fill="#0f172a" stroke="#94a3b8" stroke-width="3"/>
                            <circle cx="230" cy="115" r="7" fill="#38bdf8"/>
                            <!-- Lampu Depan LED -->
                            <rect x="290" y="82" width="10" height="6" rx="2" fill="#38bdf8"/>
                        </svg>
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-4 border-t border-slate-800 text-center">
                        <div>
                            <div class="text-[10px] text-slate-400">Jarak Kaki</div>
                            <div class="text-xs font-bold text-slate-200">Luas (Legroom)</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400">Bagasi</div>
                            <div class="text-xs font-bold text-slate-200">10 Kg / Orang</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400">Port USB</div>
                            <div class="text-xs font-bold text-sky-400">Tersedia</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Schedule Search Box (Kontras Slate-900) -->
        <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/10 border border-slate-800 text-white" data-aos="zoom-in" data-aos-duration="500">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span class="text-xs uppercase tracking-wider font-semibold text-slate-300">Cari Jadwal Travel Tersedia</span>
                </div>
                <span class="text-xs text-slate-400 hidden sm:inline-block">Pilih Tanggal & Rute Kota se-Indonesia</span>
            </div>

            <form action="{{ route('jadwal.cari') }}" method="GET" class="space-y-4 md:space-y-0 md:grid md:grid-cols-12 md:gap-4 items-end">
                <!-- Kota Asal -->
                <div class="md:col-span-4">
                    <label for="kota_asal" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Kota Asal (Keberangkatan)
                    </label>
                    <div class="relative">
                        <select name="kota_asal" id="kota_asal" required
                                class="w-full bg-slate-800/90 border border-slate-700 rounded-xl px-3.5 py-3 text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500 appearance-none cursor-pointer">
                            <option value="" disabled selected>Pilih kota keberangkatan</option>
                            @foreach ($kotaPilihan as $namaKota)
                                <option value="{{ $namaKota }}" {{ request('kota_asal') === $namaKota ? 'selected' : '' }} class="bg-slate-900 text-white">
                                    {{ $namaKota }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Kota Tujuan -->
                <div class="md:col-span-4">
                    <label for="kota_tujuan" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Kota Tujuan (Destinasi)
                    </label>
                    <div class="relative">
                        <select name="kota_tujuan" id="kota_tujuan" required
                                class="w-full bg-slate-800/90 border border-slate-700 rounded-xl px-3.5 py-3 text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500 appearance-none cursor-pointer">
                            <option value="" disabled selected>Pilih kota tujuan</option>
                            @foreach ($kotaPilihan as $namaKota)
                                <option value="{{ $namaKota }}" {{ request('kota_tujuan') === $namaKota ? 'selected' : '' }} class="bg-slate-900 text-white">
                                    {{ $namaKota }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tanggal Berangkat -->
                <div class="md:col-span-4 lg:col-span-2">
                    <label for="tanggal" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Tanggal Perjalanan
                    </label>
                    <input type="date" name="tanggal" id="tanggal"
                           min="{{ date('Y-m-d') }}"
                           value="{{ request('tanggal', date('Y-m-d')) }}"
                           required
                           class="w-full bg-slate-800/90 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500 [color-scheme:dark]">
                </div>

                <!-- Tombol Submit -->
                <div class="md:col-span-12 lg:col-span-2">
                    <button type="submit"
                            class="w-full py-3 px-5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-500 active:bg-sky-700 rounded-xl transition duration-150 shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <span>Cari Jadwal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Bagian Rute Perjalanan Populer Se-Indonesia (Gaya AppKonkos Travel Cards) -->
<section class="py-16 md:py-20 bg-slate-50 border-b border-slate-200/80">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10" data-aos="fade-down" data-aos-duration="400">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-sky-600">Pilihan Cepat Rute Favorit</span>
                <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                    Rute Perjalanan Populer Se-Indonesia
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Klik rute di bawah untuk langsung memeriksa ketersediaan kursi armada travel hari ini.
                </p>
            </div>

            <a href="{{ route('jadwal.cari') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-800 hover:text-sky-600 transition shrink-0">
                <span>Lihat Seluruh Rute Nasional</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        <!-- Grid Kartu Rute Populer Interaktif -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" data-aos="fade-up" data-aos-duration="500">
            @foreach ($rutePopuler as $rute)
                <a href="{{ route('jadwal.cari', ['kota_asal' => $rute['asal'], 'kota_tujuan' => $rute['tujuan'], 'tanggal' => date('Y-m-d')]) }}"
                   class="group bg-white border border-slate-200 hover:border-sky-500 hover:shadow-md rounded-2xl p-5 transition duration-200 flex flex-col justify-between">
                    <div>
                        <!-- Header Kartu -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 group-hover:bg-sky-50 group-hover:text-sky-700 transition">
                                {{ $rute['tag'] }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono">{{ $rute['durasi'] }}</span>
                        </div>

                        <!-- Rute Asal -> Tujuan -->
                        <div class="flex items-center gap-3 my-2">
                            <div class="font-heading font-extrabold text-base text-slate-900 group-hover:text-sky-600 transition">
                                {{ $rute['asal'] }}
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-1 transition duration-150 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                            <div class="font-heading font-extrabold text-base text-slate-900 group-hover:text-sky-600 transition">
                                {{ $rute['tujuan'] }}
                            </div>
                        </div>

                        <div class="text-xs text-slate-500 mt-1">
                            Armada: {{ $rute['armada'] }}
                        </div>
                    </div>

                    <!-- Footer Tarif -->
                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Mulai Dari</span>
                            <div class="font-heading font-extrabold text-base text-slate-900">
                                {{ $rute['harga'] }}
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1 text-xs font-bold text-sky-600 group-hover:underline">
                            Cari Tiket
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Fitur Unggulan Sistem Gotix -->
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-xl mx-auto mb-12">
            <h2 class="font-heading text-2xl font-bold text-slate-900 tracking-tight">
                Standar Perjalanan Eksekutif Gotix
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-slate-600">
                Pemesanan tiket yang transparan, tanpa biaya admin tambahan pihak ketiga.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-sky-600 mb-4 shadow-xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-slate-900 text-base mb-1.5">Pilih Kursi Sendiri</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tentukan nomor kursi 1 s/d 5 yang Anda inginkan secara interaktif sebelum membayar tiket.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-sky-600 mb-4 shadow-xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-slate-900 text-base mb-1.5">Jaminan Kursi Pasti</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Dilindungi penguncian database unik. Kursi yang telah dipesan tidak akan pernah tertimpa penumpang lain.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-sky-600 mb-4 shadow-xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-slate-900 text-base mb-1.5">Verifikasi Langsung</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pembayaran transfer bank diverifikasi secara akurat oleh petugas operator loket resmi Gotix.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
