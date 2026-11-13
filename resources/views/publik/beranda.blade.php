@extends('layouts.tamu')

@section('title', 'GoTix — Pesan Tiket Travel Antar Kota')

@section('konten')
@php
    $kotaPilihan = isset($daftarKota) && $daftarKota->isNotEmpty()
        ? $daftarKota->pluck('nama_kota')->toArray()
        : ['Indramayu', 'Cirebon', 'Jakarta', 'Bandung', 'Bekasi', 'Semarang'];
@endphp

<!-- HERO SECTION TACTILE DUA KOLOM -->
<section class="relative pt-10 pb-14 md:pt-14 md:pb-16 bg-[#FAFAF9] border-b-2 border-slate-900 overflow-hidden">
    <!-- Tactile Hatch / Grid Subtle Pattern -->
    <div class="absolute inset-0 bg-[radial-gradient(#18181b_0.75px,transparent_0.75px)] [background-size:16px_16px] opacity-10 pointer-events-none"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            <!-- Kolom Kiri: Headline & Deskripsi -->
            <div class="lg:col-span-7 text-center lg:text-left">
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-950 tracking-tight leading-[1.15]">
                    Perjalanan Antar Kota Nyaman, <br class="hidden sm:inline">
                    <span class="relative inline-block text-amber-600 underline decoration-amber-400 decoration-wavy decoration-2">
                        Pasti Dapat Kursi
                    </span> Tanpa Bentrok.
                </h1>

                <p class="mt-5 text-sm sm:text-base text-slate-700 leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                    Pesan tiket travel eksekutif antar kota dengan kapasitas eksklusif maksimal 5 penumpang per armada. Dilengkapi teknologi transaksi terproteksi bebas double-booking dan verifikasi transfer cepat.
                </p>
            </div>

            <!-- Kolom Kanan: Foto Travel & Badge Retro -->
            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <!-- Kartu Foto Travel Retro -->
                    <div class="relative overflow-hidden rounded-2xl border-2 border-slate-900 bg-white shadow-[6px_6px_0px_#18181b]">
                        <img src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1200&q=80"
                             alt="Armada Travel Eksekutif GoTix"
                             class="w-full h-72 sm:h-84 lg:h-96 object-cover"
                             loading="eager" />

                        <!-- Gradient Overlay Tipis Bawah -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>

                        <!-- Keterangan Kendaraan di Bawah Gambar -->
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded-md bg-amber-500 text-[10px] font-extrabold uppercase tracking-wider text-slate-950 border border-slate-950">
                                    Executive Shuttle
                                </span>
                                <span class="text-xs text-slate-100 font-bold">Toyota HiAce Premio</span>
                            </div>
                            <p class="text-xs text-slate-200 font-medium line-clamp-1">Kabin sejuk ber-AC, reclining seat empuk, & bagasi lega.</p>
                        </div>
                    </div>

                    <!-- Floating Retro Badge Atas Kanan -->
                    <div class="absolute -top-3 -right-3 sm:-right-4 bg-amber-400 border-2 border-slate-900 rounded-xl px-3.5 py-2 shadow-[3px_3px_0px_#18181b] flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-white text-slate-950 border border-slate-900 flex items-center justify-center font-black text-xs">
                            5
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-900 font-bold uppercase tracking-wider">Kapasitas Kursi</div>
                            <div class="text-xs font-black text-slate-950">Garansi 5 Penumpang</div>
                        </div>
                    </div>

                    <!-- Floating Retro Badge Bawah Kiri -->
                    <div class="hidden sm:flex absolute -bottom-3 -left-3 sm:-left-4 bg-white border-2 border-slate-900 rounded-xl px-3.5 py-2.5 shadow-[3px_3px_0px_#18181b] items-center gap-2.5">
                        <div class="flex text-amber-500 text-xs font-black">
                            ★★★★★
                        </div>
                        <div class="text-xs font-extrabold text-slate-900">
                            4.9<span class="text-slate-500 font-normal">/5 Penumpang Puas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FORM PENCARIAN JADWAL CEPAT (TACTILE RETRO BOX) -->
<section id="form-cari-jadwal" class="relative -mt-6 z-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white text-slate-900 rounded-2xl p-6 sm:p-8 border-2 border-slate-900 shadow-[6px_6px_0px_#18181b]">
        <div class="mb-6 pb-4 border-b-2 border-slate-100">
            <h2 class="font-heading text-lg sm:text-xl font-extrabold tracking-tight text-slate-950 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-amber-400 border border-slate-900 flex items-center justify-center text-slate-950 text-xs font-black">
                    🔍
                </span>
                <span>Cari Jadwal Keberangkatan Travel</span>
            </h2>
            <p class="text-xs text-slate-600 mt-1 font-medium">Pilih kota asal, kota tujuan, dan tanggal untuk cek ketersediaan kursi secara real-time.</p>
        </div>

        <form action="{{ route('jadwal.cari') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Kota Asal -->
            <div>
                <label for="input_kota_asal" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">
                    Kota Asal
                </label>
                <div class="relative">
                    <select id="input_kota_asal" name="kota_asal"
                            class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-3 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                        <option value="">Pilih Kota Asal...</option>
                        @foreach ($kotaPilihan as $namaKota)
                            <option value="{{ $namaKota }}" {{ request('kota_asal') === $namaKota ? 'selected' : '' }}>
                                {{ $namaKota }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kota Tujuan -->
            <div>
                <label for="input_kota_tujuan" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">
                    Kota Tujuan
                </label>
                <div class="relative">
                    <select id="input_kota_tujuan" name="kota_tujuan"
                            class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-3 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                        <option value="">Pilih Kota Tujuan...</option>
                        @foreach ($kotaPilihan as $namaKota)
                            <option value="{{ $namaKota }}" {{ request('kota_tujuan') === $namaKota ? 'selected' : '' }}>
                                {{ $namaKota }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tanggal Berangkat -->
            <div>
                <label for="input_tanggal" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2">
                    Tanggal Berangkat
                </label>
                <input type="date" id="input_tanggal" name="tanggal"
                       value="{{ request('tanggal', date('Y-m-d')) }}"
                       min="{{ date('Y-m-d') }}"
                       class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-3 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
            </div>

            <!-- Tombol Cari -->
            <div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-amber-500 hover:bg-amber-400 active:translate-x-0.5 active:translate-y-0.5 text-slate-950 text-sm font-extrabold rounded-xl border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] transition cursor-pointer">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Cari Jadwal</span>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- SECTION JADWAL POPULER DARI DATABASE -->
<section id="rute-populer" class="py-14 md:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <div class="text-xs font-extrabold text-amber-700 uppercase tracking-wider mb-1">Jadwal Keberangkatan</div>
                <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">
                    Rute Perjalanan Tersedia
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-xl font-medium">
                    Data jadwal nyata dari sistem tiket GoTix. Pilih rute dan pilih nomor kursi secara instan.
                </p>
            </div>
            <a href="{{ route('jadwal.cari') }}"
               class="inline-flex items-center gap-1.5 text-xs font-extrabold text-slate-900 hover:text-amber-700 underline decoration-2 transition">
                <span>Lihat Seluruh Jadwal</span>
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        @if (isset($jadwalPopuler) && $jadwalPopuler->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
                    <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] hover:shadow-[6px_6px_0px_#18181b] transition flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border border-slate-900 {{ $sisaKursi > 0 ? 'bg-emerald-100 text-emerald-900' : 'bg-rose-100 text-rose-900' }}">
                                    {{ $sisaKursi > 0 ? $sisaKursi . ' kursi tersedia' : 'Penuh' }}
                                </span>
                                <span class="text-[11px] font-bold text-slate-600">
                                    {{ $jadwal->tanggal_berangkat ? date('d M Y', strtotime($jadwal->tanggal_berangkat)) : '-' }}
                                </span>
                            </div>

                            <div class="space-y-1 mb-4">
                                <div class="text-[11px] text-slate-500 uppercase font-bold tracking-wider">Rute Travel</div>
                                <div class="text-base font-extrabold text-slate-950 flex items-center gap-2">
                                    <span>{{ $jadwal->kotaAsal?->nama_kota ?? '-' }}</span>
                                    <span class="text-amber-600 font-black">➔</span>
                                    <span>{{ $jadwal->kotaTujuan?->nama_kota ?? '-' }}</span>
                                </div>
                                <div class="text-xs text-slate-600 font-semibold pt-1">
                                    {{ $jadwal->kendaraan?->jenis_kendaraan ?? 'Armada Travel' }} · {{ $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) . ' WIB' : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t-2 border-slate-100 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-500">Tarif / Kursi</div>
                                <div class="text-base font-black text-slate-950">
                                    Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                                </div>
                            </div>

                            @auth
                                @if ($sisaKursi > 0)
                                    @if (Auth::user()->isMember())
                                        <a href="{{ route('member.pesanan.create', $jadwal->id) }}"
                                           class="inline-flex items-center gap-1 px-3.5 py-1.5 text-xs font-black text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl border border-slate-900 shadow-[2px_2px_0px_#18181b] active:translate-x-0.5 active:translate-y-0.5 transition">
                                            <span>Pesan</span>
                                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </a>
                                    @elseif (Auth::user()->isOperator())
                                        <a href="{{ route('operator.loket.index') }}"
                                           class="inline-flex items-center gap-1 px-3.5 py-1.5 text-xs font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl border border-slate-900 shadow-[2px_2px_0px_#18181b] transition">
                                            <span>Loket</span>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.jadwal.show', $jadwal->id) }}"
                                           class="inline-flex items-center gap-1 px-3.5 py-1.5 text-xs font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl border border-slate-900 shadow-[2px_2px_0px_#18181b] transition">
                                            <span>Detail</span>
                                        </a>
                                    @endif
                                @else
                                    <span class="px-3.5 py-1.5 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl border border-slate-300 cursor-not-allowed">Penuh</span>
                                @endif
                            @else
                                <a href="{{ route('masuk') }}"
                                   class="inline-flex items-center gap-1 px-3.5 py-1.5 text-xs font-black text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl border border-slate-900 shadow-[2px_2px_0px_#18181b] active:translate-x-0.5 active:translate-y-0.5 transition">
                                    <span>Pesan</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white border-2 border-slate-900 rounded-2xl shadow-[4px_4px_0px_#18181b] p-6">
                <p class="text-slate-600 text-sm font-semibold">Belum ada jadwal keberangkatan aktif saat ini.</p>
                <a href="{{ route('jadwal.cari') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-extrabold text-amber-700 hover:text-amber-800 underline decoration-2">
                    Buka Pencarian Jadwal ➔
                </a>
            </div>
        @endif
    </div>
</section>

<!-- SECTION 4 KEUNGGULAN GOTIX TRAVEL -->
<section class="py-14 md:py-16 bg-white border-y-2 border-slate-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">
                Standar Kenyamanan Travel Eksekutif
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 font-medium">
                Dirancang khusus untuk penumpang yang mengutamakan ketepatan waktu, ruang gerak lega, dan privasi selama perjalanan antar kota.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pilar 1 -->
            <div class="p-6 rounded-2xl bg-[#FAFAF9] border-2 border-slate-900 shadow-[4px_4px_0px_#18181b] space-y-3">
                <div class="w-10 h-10 rounded-xl bg-amber-400 text-slate-950 border border-slate-900 flex items-center justify-center font-black text-sm shadow-[2px_2px_0px_#18181b]">
                    5
                </div>
                <h3 class="font-heading text-sm font-extrabold text-slate-950">Maksimal 5 Penumpang</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Tidak ada lagi kabin sesak atau berhimpitan. Setiap penumpang mendapatkan kursi eksklusif dengan ruang kaki luas.
                </p>
            </div>

            <!-- Pilar 2 -->
            <div class="p-6 rounded-2xl bg-[#FAFAF9] border-2 border-slate-900 shadow-[4px_4px_0px_#18181b] space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-400 text-slate-950 border border-slate-900 flex items-center justify-center font-black text-sm shadow-[2px_2px_0px_#18181b]">
                    ✓
                </div>
                <h3 class="font-heading text-sm font-extrabold text-slate-950">Anti Double-Booking</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Sistem reservasi divalidasi dengan proteksi kunci database transaksi sehingga kursi yang Anda pilih tidak akan terambil orang lain.
                </p>
            </div>

            <!-- Pilar 3 -->
            <div class="p-6 rounded-2xl bg-[#FAFAF9] border-2 border-slate-900 shadow-[4px_4px_0px_#18181b] space-y-3">
                <div class="w-10 h-10 rounded-xl bg-sky-300 text-slate-950 border border-slate-900 flex items-center justify-center font-black text-sm shadow-[2px_2px_0px_#18181b]">
                    ⚡
                </div>
                <h3 class="font-heading text-sm font-extrabold text-slate-950">Verifikasi Terpercaya</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Pembayaran transfer bank langsung diverifikasi oleh operator loket terpercaya dengan bukti tersimpan aman di private storage.
                </p>
            </div>

            <!-- Pilar 4 -->
            <div class="p-6 rounded-2xl bg-[#FAFAF9] border-2 border-slate-900 shadow-[4px_4px_0px_#18181b] space-y-3">
                <div class="w-10 h-10 rounded-xl bg-rose-300 text-slate-950 border border-slate-900 flex items-center justify-center font-black text-sm shadow-[2px_2px_0px_#18181b]">
                    ★
                </div>
                <h3 class="font-heading text-sm font-extrabold text-slate-950">Armada Prima & Bersih</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Armada modern Toyota HiAce Premio & Elf Executive dengan pengemudi berpengalaman, sopan, dan berlisensi resmi.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
