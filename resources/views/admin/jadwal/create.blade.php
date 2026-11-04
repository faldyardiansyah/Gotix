@extends('layouts.tamu')

@section('title', 'Tambah Jadwal Baru — Admin Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Tombol Kembali -->
    <div class="mb-6 flex items-center justify-between" data-aos="fade-down" data-aos-duration="400">
        <a href="{{ route('admin.jadwal.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Master Jadwal</span>
        </a>
    </div>

    @php
        $kotaSeIndonesia = [
            'Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Semarang',
            'Malang', 'Solo (Surakarta)', 'Cirebon', 'Bogor', 'Medan',
            'Pekanbaru', 'Palembang', 'Padang', 'Bandar Lampung', 'Makassar',
            'Denpasar', 'Banjarmasin', 'Balikpapan', 'Pontianak', 'Manado'
        ];
    @endphp

    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 md:p-10 shadow-sm space-y-6" data-aos="fade-up" data-aos-duration="500">
        <div class="pb-5 border-b border-slate-100">
            <h1 class="font-heading text-2xl font-extrabold text-slate-900">Tambah Jadwal Armada Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Daftarkan trayek perjalanan baru antar kota di seluruh Indonesia.</p>
        </div>

        <form action="#" method="POST" class="space-y-5" onsubmit="event.preventDefault(); alert('Jadwal perjalanan baru berhasil diterbitkan dan siap dipesan penumpang.'); window.location.href='{{ route('admin.jadwal.index') }}';">
            @csrf

            <!-- Kota Asal & Kota Tujuan Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kota_asal" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kota Keberangkatan (Asal)
                    </label>
                    <div class="relative">
                        <select name="kota_asal" id="kota_asal" required
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 appearance-none cursor-pointer">
                            <option value="" disabled selected>Pilih kota asal</option>
                            @foreach ($kotaSeIndonesia as $kota)
                                <option value="{{ $kota }}">{{ $kota }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="kota_tujuan" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kota Tujuan (Destinasi)
                    </label>
                    <div class="relative">
                        <select name="kota_tujuan" id="kota_tujuan" required
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 appearance-none cursor-pointer">
                            <option value="" disabled selected>Pilih kota tujuan</option>
                            @foreach ($kotaSeIndonesia as $kota)
                                <option value="{{ $kota }}">{{ $kota }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Armada Kendaraan -->
            <div>
                <label for="kendaraan_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Pilihan Armada Kendaraan (Kapasitas Tetap 5 Kursi)
                </label>
                <div class="relative">
                    <select name="kendaraan_id" id="kendaraan_id" required
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 appearance-none cursor-pointer">
                        <option value="1">Toyota HiAce Premio Executive — Plat BE 1212 AT (Hitam) — 5 Seat</option>
                        <option value="2">Isuzu Elf Executive — Plat BE 4677 DT (Biru) — 5 Seat</option>
                        <option value="3">Toyota Avanza Veloz — Plat BE 7890 XY (Putih) — 5 Seat</option>
                        <option value="4">Toyota Innova Zenix — Plat DD 1928 MN (Abu-abu) — 5 Seat</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tanggal, Jam, dan Estimasi Durasi Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal_berangkat" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tanggal Berangkat
                    </label>
                    <input type="date" name="tanggal_berangkat" id="tanggal_berangkat" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>

                <div>
                    <label for="jam_berangkat" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Jam Berangkat
                    </label>
                    <input type="time" name="jam_berangkat" id="jam_berangkat" required value="08:30"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>

                <div>
                    <label for="durasi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Estimasi Durasi
                    </label>
                    <input type="text" name="durasi" id="durasi" required placeholder="Contoh: 3 Jam 30 Menit" value="3 Jam 30 Menit"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <!-- Tarif Tiket Resmi -->
            <div>
                <label for="harga" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Tarif Tiket Per Kursi (Rupiah)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" name="harga" id="harga" required min="10000" step="5000" value="120000"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 font-mono font-bold">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.jadwal.index') }}"
                   class="px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-xl transition">
                    Batalkan
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm cursor-pointer">
                    Simpan & Terbitkan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
