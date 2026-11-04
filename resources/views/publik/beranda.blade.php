@extends('layouts.tamu')

@section('title', 'Gotix — Pesan Tiket Travel Antar Kota se-Indonesia')

@section('konten')
@php
    $kotaPilihan = isset($daftarKota) && $daftarKota->isNotEmpty()
        ? $daftarKota->pluck('nama_kota')->toArray()
        : ['Bandar Lampung', 'Metro', 'Pringsewu', 'Kotabumi', 'Liwa', 'Tanggamus'];
@endphp

<!-- HERO SECTION DUA KOLOM -->
<section class="relative pt-10 pb-12 md:pt-16 md:pb-16 bg-white border-b border-slate-200/80 overflow-hidden">
    <!-- Subtle Pattern Latar Belakang -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#f1f5f9_1px,transparent_1px),linear-gradient(to_bottom,#f1f5f9_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-70 pointer-events-none"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            <!-- Kolom Kiri: Teks & CTA -->
            <div class="lg:col-span-7 text-center lg:text-left" data-aos="fade-right" data-aos-duration="600">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-700 text-xs font-semibold mb-5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-sky-600 animate-pulse"></span>
                    <span>Armada Travel Eksekutif se-Indonesia</span>
                </div>

                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                    Perjalanan Antar Kota Nyaman, <br class="hidden sm:inline">
                    <span class="text-sky-600">Pasti Dapat Kursi</span> Tanpa Bentrok.
                </h1>

                <p class="mt-4 text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Pesan tiket travel eksekutif antar kota dengan kapasitas eksklusif maksimal 5 penumpang per armada. Dilengkapi teknologi transaksi terproteksi bebas double-booking dan verifikasi transfer cepat.
                </p>

                <!-- Tombol Aksi Cepat Hero -->
                <div class="mt-7 flex flex-wrap items-center justify-center lg:justify-start gap-3">
                    <a href="#form-cari-jadwal"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white text-sm font-bold rounded-xl transition duration-150 shadow-md shadow-sky-600/20">
                        <span>Pesan Tiket Sekarang</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                        </svg>
                    </a>
                    <a href="#rute-populer"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold rounded-xl transition duration-150">
                        <span>Lihat Rute Populer</span>
                    </a>
                </div>

                <!-- 3 Fitur Unggulan Mikro -->
                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-center lg:justify-start gap-5 text-xs font-medium text-slate-600">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Maksimal 5 Kursi</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Database Pessimistic Lock</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Verifikasi Manual Aman</span>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Foto Mobil Travel Asli & Badge Mengambang -->
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-duration="600" data-aos-delay="150">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <!-- Kartu Foto Travel Asli -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200/90 bg-slate-100 shadow-xl">
                        <img src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1200&q=80"
                             alt="Armada Travel Eksekutif Gotix"
                             class="w-full h-72 sm:h-84 lg:h-96 object-cover transform hover:scale-102 transition duration-500 ease-out"
                             loading="eager" />

                        <!-- Gradient Overlay Tipis Bawah -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/20 to-transparent"></div>

                        <!-- Keterangan Kendaraan di Bawah Gambar -->
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded-md bg-sky-500 text-[10px] font-bold uppercase tracking-wider text-white">
                                    Executive Shuttle
                                </span>
                                <span class="text-xs text-slate-200">Toyota HiAce Premio</span>
                            </div>
                            <p class="text-xs text-slate-300 font-medium line-clamp-1">Kabin sejuk ber-AC, reclining seat empuk, & bagasi luas.</p>
                        </div>
                    </div>

                    <!-- Floating Badge Atas Kanan -->
                    <div class="absolute -top-3 -right-3 sm:-right-4 bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 shadow-lg flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                            ✓
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Kapasitas Kursi</div>
                            <div class="text-xs font-bold text-slate-900">Garansi 5 Penumpang</div>
                        </div>
                    </div>

                    <!-- Floating Badge Bawah Kiri -->
                    <div class="hidden sm:flex absolute -bottom-3 -left-3 sm:-left-4 bg-white border border-slate-200/90 rounded-xl px-3.5 py-2.5 shadow-lg items-center gap-2.5">
                        <div class="flex text-amber-400 text-xs">
                            ★ ★ ★ ★ ★
                        </div>
                        <div class="text-xs font-bold text-slate-900">
                            4.9<span class="text-slate-400 font-normal">/5 Penumpang Puas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FORM PENCARIAN JADWAL CEPAT (BOX KONTRAS GELAP SLATE-900) -->
<section id="form-cari-jadwal" class="relative -mt-6 z-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-2xl border border-slate-800" data-aos="zoom-in" data-aos-duration="500">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-6 pb-4 border-b border-slate-800">
            <div>
                <h2 class="font-heading text-lg sm:text-xl font-bold tracking-tight text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Cari Jadwal Keberangkatan Travel</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Pilih rute kota asal, tujuan, dan tanggal untuk cek ketersediaan sisa kursi.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-800 text-slate-300 border border-slate-700/80 w-fit">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>Update Kursi Real-Time</span>
            </span>
        </div>

        <form action="{{ route('jadwal.cari') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Kota Asal -->
            <div>
                <label for="input_kota_asal" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Kota Asal
                </label>
                <div class="relative">
                    <select id="input_kota_asal" name="kota_asal"
                            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-white focus:bg-slate-800/90 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                        <option value="" class="text-slate-400">Pilih Kota Asal...</option>
                        @foreach ($kotaPilihan as $namaKota)
                            <option value="{{ $namaKota }}" {{ request('kota_asal') === $namaKota ? 'selected' : '' }} class="bg-slate-900 text-white">
                                {{ $namaKota }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kota Tujuan -->
            <div>
                <label for="input_kota_tujuan" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Kota Tujuan
                </label>
                <div class="relative">
                    <select id="input_kota_tujuan" name="kota_tujuan"
                            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-white focus:bg-slate-800/90 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                        <option value="" class="text-slate-400">Pilih Kota Tujuan...</option>
                        @foreach ($kotaPilihan as $namaKota)
                            <option value="{{ $namaKota }}" {{ request('kota_tujuan') === $namaKota ? 'selected' : '' }} class="bg-slate-900 text-white">
                                {{ $namaKota }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tanggal Berangkat -->
            <div>
                <label for="input_tanggal" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Tanggal Berangkat
                </label>
                <input type="date" id="input_tanggal" name="tanggal"
                       value="{{ request('tanggal', date('Y-m-d')) }}"
                       min="{{ date('Y-m-d') }}"
                       class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-white focus:bg-slate-800/90 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition [color-scheme:dark]">
            </div>

            <!-- Tombol Cari -->
            <div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white text-sm font-bold rounded-xl transition duration-150 shadow-lg shadow-sky-600/30 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Cari Jadwal Travel</span>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- SECTION JADWAL POPULER DARI DATABASE -->
<section id="rute-populer" class="py-16 md:py-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10" data-aos="fade-up">
            <div>
                <div class="text-xs font-bold text-sky-600 uppercase tracking-wider mb-1">Jadwal Keberangkatan</div>
                <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Rute Perjalanan Tersedia
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-xl">
                    Data jadwal nyata dari sistem Gotix Lampung. Pilih rute dan pesan kursi secara langsung.
                </p>
            </div>
            <a href="{{ route('jadwal.cari') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-700 hover:text-sky-800 transition">
                <span>Lihat Seluruh Jadwal</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        @if (isset($jadwalPopuler) && $jadwalPopuler->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" data-aos="fade-up" data-aos-duration="600">
                @foreach ($jadwalPopuler as $jadwal)
                    @php
                        $terisiCount = $jadwal->pesanan->whereIn('status', [
                            \App\Enums\StatusPesanan::BelumBayar,
                            \App\Enums\StatusPesanan::DalamProses,
                            \App\Enums\StatusPesanan::Lunas,
                        ])->count();
                        $kapasitas = $jadwal->kendaraan?->kapasitas_kursi ?? 5;
                        $sisaKursi = max(0, $kapasitas - $terisiCount);
                    @endphp
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-slate-300 transition duration-200 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $sisaKursi > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                    {{ $sisaKursi > 0 ? $sisaKursi . ' kursi tersedia' : 'Penuh' }}
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400">
                                    {{ $jadwal->tanggal_berangkat ? date('d M Y', strtotime($jadwal->tanggal_berangkat)) : '-' }}
                                </span>
                            </div>

                            <div class="space-y-1 mb-4">
                                <div class="text-xs text-slate-400 uppercase font-semibold">Rute Travel</div>
                                <div class="text-base font-extrabold text-slate-900 group-hover:text-sky-600 transition flex items-center gap-2">
                                    <span>{{ $jadwal->kotaAsal?->nama_kota ?? '-' }}</span>
                                    <span class="text-sky-500 text-sm">?</span>
                                    <span>{{ $jadwal->kotaTujuan?->nama_kota ?? '-' }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium pt-1">
                                    {{ $jadwal->kendaraan?->jenis_kendaraan ?? 'Armada Travel' }} � {{ $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) . ' WIB' : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-[10px] uppercase font-semibold text-slate-400">Tarif</div>
                                <div class="text-sm font-extrabold text-slate-900">
                                    Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                                </div>
                            </div>

                            @auth
                                @if ($sisaKursi > 0)
                                    <a href="{{ route('member.pesanan.create', $jadwal->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition duration-150">
                                        <span>Pesan</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="px-3 py-1.5 text-xs font-semibold text-slate-400 bg-slate-100 rounded-xl cursor-not-allowed">Penuh</span>
                                @endif
                            @else
                                <a href="{{ route('masuk') }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-600 hover:text-white rounded-xl transition duration-150">
                                    <span>Pesan</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white border border-slate-200 rounded-2xl">
                <p class="text-slate-500 text-sm">Belum ada jadwal keberangkatan aktif di database.</p>
                <a href="{{ route('jadwal.cari') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 hover:text-sky-700">
                    Buka Pencarian Jadwal ?
                </a>
            </div>
        @endif
    </div>
</section>

<!-- SECTION 4 KEUNGGULAN GOTIX TRAVEL -->
<section class="py-14 bg-white border-y border-slate-200/80">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12" data-aos="fade-up">
            <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Standar Kenyamanan Travel Eksekutif
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">
                Dirancang khusus untuk penumpang yang mengutamakan ketepatan waktu, ruang gerak lega, dan privasi selama perjalanan.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-duration="600">
            <!-- Pilar 1 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    5
                </div>
                <h3 class="font-heading text-sm font-bold text-slate-900">Maksimal 5 Penumpang</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tidak ada lagi kabin sesak atau berhimpitan. Setiap penumpang mendapatkan kursi eksklusif dengan ruang kaki luas.
                </p>
            </div>

            <!-- Pilar 2 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    🔒
                </div>
                <h3 class="font-heading text-sm font-bold text-slate-900">Anti Double-Booking</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Sistem reservasi divalidasi dengan proteksi kunci pesanan transaksi database sehingga kursi yang Anda pilih tidak akan terambil orang lain.
                </p>
            </div>

            <!-- Pilar 3 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    ⚡
                </div>
                <h3 class="font-heading text-sm font-bold text-slate-900">Verifikasi Cepat & Transparan</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pembayaran transfer bank langsung diverifikasi oleh operator loket terpercaya tanpa biaya administrasi tersembunyi.
                </p>
            </div>

            <!-- Pilar 4 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    🛡️
                </div>
                <h3 class="font-heading text-sm font-bold text-slate-900">Armada Prima & Terawat</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Armada modern Toyota HiAce Premio, Isuzu Elf Executive, & Avanza Veloz dengan sopir berpengalaman dan berlisensi resmi.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SECTION TESTIMONI PENGGUNA ASLI -->
<section class="py-16 md:py-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-xl mx-auto mb-10" data-aos="fade-up">
            <div class="text-xs font-bold text-sky-600 uppercase tracking-wider mb-1">Pengalaman Nyata</div>
            <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Dipercaya Ribuan Penumpang
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Ulasan asli dari penumpang yang telah menikmati perjalanan antar kota bersama Gotix.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-aos="fade-up" data-aos-duration="600">
            <!-- Testimoni 1 -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex text-amber-400 text-sm">★★★★★</div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    "Pesan travel Jakarta - Bandung lewat Gotix praktis sekali. Kursi nomor 2 benar-benar pas dan nggak sempit sama sekali karena mobil cuma diisi 5 orang."
                </p>
                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 font-bold text-xs flex items-center justify-center">
                        RN
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900">Rizky Nugraha</div>
                        <div class="text-[11px] text-slate-400">Rute Jakarta ➔ Bandung</div>
                    </div>
                </div>
            </div>

            <!-- Testimoni 2 -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex text-amber-400 text-sm">★★★★★</div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    "Sangat suka dengan verifikasi pembayarannya yang cepat. Bukti transfer diupload langsung diverifikasi operator dan tiket langsung terbit di akun."
                </p>
                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center">
                        AW
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900">Anisa Wulandari</div>
                        <div class="text-[11px] text-slate-400">Rute Yogyakarta ➔ Surabaya</div>
                    </div>
                </div>
            </div>

            <!-- Testimoni 3 -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex text-amber-400 text-sm">★★★★★</div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    "Armada HiAce Premio-nya bersih, AC dingin, dan pengemudinya sangat sopan mengendarai lewat tol. Pilihan terbaik untuk dinas antarkota."
                </p>
                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center">
                        FS
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900">Farhan Setiawan</div>
                        <div class="text-[11px] text-slate-400">Rute Bandar Lampung ➔ Jakarta</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION (CTA) SECTION -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 md:pb-20">
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl p-8 sm:p-12 text-center text-white shadow-xl border border-slate-800" data-aos="fade-up">
        <!-- Background Glow Accent -->
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-sky-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-2xl mx-auto space-y-4">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-sky-500/15 text-sky-400 border border-sky-400/20">
                Pesan Cepat Tanpa Ribet
            </span>
            <h2 class="font-heading text-2xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                Siap Melakukan Perjalanan Antar Kota?
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl mx-auto">
                Temukan jadwal travel terbaik hari ini dan pastikan kursi nyaman Anda sebelum kehabisan kuota 5 kursi.
            </p>
            <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                <a href="#form-cari-jadwal"
                   class="px-6 py-3 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white text-sm font-bold rounded-xl transition duration-150 shadow-lg shadow-sky-600/30">
                    Cari Jadwal Sekarang
                </a>
                @guest
                    <a href="{{ route('daftar') }}"
                       class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold rounded-xl border border-slate-700 transition duration-150">
                        Daftar Akun Member
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection
