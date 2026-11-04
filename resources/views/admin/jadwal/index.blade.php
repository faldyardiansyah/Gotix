@extends('layouts.tamu')

@section('title', 'Kelola Master Jadwal — Admin Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('admin._nav', [
        'judul' => 'Master Jadwal Keberangkatan',
        'deskripsi' => 'Atur rute perjalanan antar kota se-Indonesia, armada yang bertugas, tarif resmi, dan kuota kursi.'
    ])

    <!-- Aksi Bar & Filter -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4" data-aos="fade-down" data-aos-duration="400">
        <!-- Filter Cepat -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider shrink-0">Filter:</span>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-white shadow-xs">
                Semua Jadwal
            </button>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                Pulau Jawa
            </button>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                Sumatera
            </button>
            <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                Hari Ini
            </button>
        </div>

        <a href="{{ route('admin.jadwal.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm self-start md:self-auto shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Jadwal Baru</span>
        </a>
    </div>

    @php
        $daftarJadwal = [
            [
                'id' => 1,
                'asal' => 'Jakarta (Gambir)',
                'tujuan' => 'Bandung (Dipatiukur)',
                'tanggal' => date('d M Y', strtotime('+1 day')),
                'jam' => '08:30 WIB',
                'armada' => 'Toyota HiAce Premio (BE 1212 AT)',
                'harga' => 'Rp 120.000',
                'sisa_kursi' => '3 / 5 Kursi',
                'sisa_class' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            ],
            [
                'id' => 2,
                'asal' => 'Yogyakarta (Malioboro)',
                'tujuan' => 'Surabaya (Gubeng)',
                'tanggal' => date('d M Y', strtotime('+2 days')),
                'jam' => '13:00 WIB',
                'armada' => 'Isuzu Elf Executive (BE 4677 DT)',
                'harga' => 'Rp 180.000',
                'sisa_kursi' => '1 / 5 Kursi',
                'sisa_class' => 'text-amber-700 bg-amber-50 border-amber-200',
            ],
            [
                'id' => 3,
                'asal' => 'Semarang (Simpang Lima)',
                'tujuan' => 'Malang (Arjosari)',
                'tanggal' => date('d M Y', strtotime('+2 days')),
                'jam' => '09:00 WIB',
                'armada' => 'Toyota Avanza Veloz (BE 7890 XY)',
                'harga' => 'Rp 195.000',
                'sisa_kursi' => '2 / 5 Kursi',
                'sisa_class' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            ],
            [
                'id' => 4,
                'asal' => 'Medan (Amplas)',
                'tujuan' => 'Pekanbaru (Arengka)',
                'tanggal' => date('d M Y', strtotime('+3 days')),
                'jam' => '19:30 WIB',
                'armada' => 'Isuzu Elf Long (BK 9011 AP)',
                'harga' => 'Rp 240.000',
                'sisa_kursi' => 'Penuh (0/5)',
                'sisa_class' => 'text-rose-700 bg-rose-50 border-rose-200',
            ],
            [
                'id' => 5,
                'asal' => 'Makassar (Dayah)',
                'tujuan' => 'Parepare (Bau Massepe)',
                'tanggal' => date('d M Y', strtotime('+4 days')),
                'jam' => '14:00 WITA',
                'armada' => 'Toyota Innova Zenix (DD 1928 MN)',
                'harga' => 'Rp 110.000',
                'sisa_kursi' => '4 / 5 Kursi',
                'sisa_class' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            ],
        ];
    @endphp

    <!-- Tabel Master Jadwal -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up" data-aos-duration="500">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Rute Perjalanan</th>
                        <th class="px-6 py-4">Jadwal Keberangkatan</th>
                        <th class="px-6 py-4">Armada & Kapasitas</th>
                        <th class="px-6 py-4">Tarif Tiket</th>
                        <th class="px-6 py-4">Sisa Kuota</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($daftarJadwal as $jadwal)
                        <tr class="hover:bg-slate-50/60 transition duration-100">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <span>{{ $jadwal['asal'] }}</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                    <span>{{ $jadwal['tujuan'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-semibold text-slate-800">{{ $jadwal['tanggal'] }}</div>
                                <div class="text-[11px] text-slate-500">{{ $jadwal['jam'] }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="text-slate-800 font-medium">{{ $jadwal['armada'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900">
                                {{ $jadwal['harga'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $jadwal['sisa_class'] }}">
                                    {{ $jadwal['sisa_kursi'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <a href="{{ route('admin.jadwal.edit', $jadwal['id']) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                    <span>Edit</span>
                                </a>
                                <button type="button"
                                        onclick="if(confirm('Hapus jadwal ini? Jadwal yang sudah memiliki pesanan tiket tidak dapat dihapus.')) alert('Jadwal berhasil dihapus dari sistem.')"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition cursor-pointer">
                                    <span>Hapus</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List View -->
        <div class="divide-y divide-slate-100 md:hidden">
            @foreach ($daftarJadwal as $jadwal)
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <div class="font-bold text-slate-900 text-sm">
                            {{ $jadwal['asal'] }} → {{ $jadwal['tujuan'] }}
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $jadwal['sisa_class'] }}">
                            {{ $jadwal['sisa_kursi'] }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-500">
                        <div>{{ $jadwal['tanggal'] }} &bull; {{ $jadwal['jam'] }}</div>
                        <div class="mt-0.5 text-slate-700 font-medium">{{ $jadwal['armada'] }}</div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <div class="font-bold text-slate-900 text-sm">{{ $jadwal['harga'] }}</div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.jadwal.edit', $jadwal['id']) }}"
                               class="px-3 py-1 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg">
                                Edit
                            </a>
                            <button type="button" onclick="confirm('Hapus jadwal?')"
                                    class="px-3 py-1 text-xs font-semibold text-rose-700 bg-rose-50 rounded-lg">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
