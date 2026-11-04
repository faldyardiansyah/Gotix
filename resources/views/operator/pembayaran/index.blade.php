@extends('layouts.tamu')

@section('title', 'Verifikasi Pembayaran Penumpang — Operator Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('operator._nav', [
        'judul' => 'Verifikasi Pembayaran Penumpang',
        'deskripsi' => 'Periksa struk transfer manual yang diunggah penumpang untuk menerbitkan tiket resmi armada travel.'
    ])

    @php
        $daftarTransaksi = [
            [
                'id' => 1,
                'kode' => 'GTX-774102',
                'pemesan' => 'Siti Rahma',
                'telepon' => '0812-7000-1002',
                'rute' => 'Yogyakarta (Malioboro) → Surabaya (Gubeng)',
                'jadwal' => date('d M Y', strtotime('+5 days')) . ' • 13:00 WIB',
                'kursi' => 4,
                'total' => 'Rp 180.000',
                'bank' => 'BCA (a.n Siti Rahma)',
                'tgl_transfer' => date('d M Y, H:i'),
                'status' => 'Menunggu Verifikasi',
                'badge' => 'bg-sky-50 text-sky-700 border-sky-200',
                'dot' => 'bg-sky-500',
                'prioritas' => true,
            ],
            [
                'id' => 2,
                'kode' => 'GTX-883912',
                'pemesan' => 'Agus Setiawan',
                'telepon' => '0812-7000-1003',
                'rute' => 'Jakarta (Gambir) → Bandung (Dipatiukur)',
                'jadwal' => date('d M Y', strtotime('+1 day')) . ' • 08:30 WIB',
                'kursi' => 3,
                'total' => 'Rp 120.000',
                'bank' => 'Mandiri (a.n Agus Setiawan)',
                'tgl_transfer' => date('d M Y, H:i', strtotime('-1 hour')),
                'status' => 'Menunggu Verifikasi',
                'badge' => 'bg-sky-50 text-sky-700 border-sky-200',
                'dot' => 'bg-sky-500',
                'prioritas' => true,
            ],
            [
                'id' => 3,
                'kode' => 'GTX-908211',
                'pemesan' => 'Budi Santoso',
                'telepon' => '0812-7000-1001',
                'rute' => 'Jakarta (Gambir) → Bandung (Dipatiukur)',
                'jadwal' => date('d M Y', strtotime('+2 days')) . ' • 08:30 WIB',
                'kursi' => 2,
                'total' => 'Rp 120.000',
                'bank' => 'BCA (a.n Budi Santoso)',
                'tgl_transfer' => date('d M Y', strtotime('-1 day')),
                'status' => 'Lunas',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
                'prioritas' => false,
            ],
            [
                'id' => 4,
                'kode' => 'GTX-551029',
                'pemesan' => 'Dewi Lestari',
                'telepon' => '0812-7000-1004',
                'rute' => 'Semarang (Simpang Lima) → Malang (Arjosari)',
                'jadwal' => date('d M Y', strtotime('+3 days')) . ' • 09:00 WIB',
                'kursi' => 1,
                'total' => 'Rp 195.000',
                'bank' => 'BRI (a.n Dewi Lestari)',
                'tgl_transfer' => date('d M Y', strtotime('-2 days')),
                'status' => 'Ditolak',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot' => 'bg-rose-500',
                'prioritas' => false,
            ],
        ];
    @endphp

    <!-- Metric Counter Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6" data-aos="fade-up" data-aos-duration="400">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Perlu Tindakan Verifikasi</div>
            <div class="font-heading text-3xl font-extrabold text-sky-600 mt-1">2 Transaksi</div>
            <div class="text-xs text-slate-500 mt-0.5">Harap segera diverifikasi sebelum jadwal armada</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Terverifikasi Hari Ini</div>
            <div class="font-heading text-3xl font-extrabold text-emerald-600 mt-1">14 Tiket</div>
            <div class="text-xs text-slate-500 mt-0.5">Semua e-tiket otomatis diterbitkan ke penumpang</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Penolakan Transfer</div>
            <div class="font-heading text-3xl font-extrabold text-slate-800 mt-1">1 Berkas</div>
            <div class="text-xs text-slate-500 mt-0.5">Alasan: Nominal transfer kurang dari tarif tiket</div>
        </div>
    </div>

    <!-- Container Tabel Transaksi -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up" data-aos-duration="500">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Kode / Pemesan</th>
                        <th class="px-6 py-4">Rute & Armada</th>
                        <th class="px-6 py-4">Nominal & Bank</th>
                        <th class="px-6 py-4">Waktu Transfer</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($daftarTransaksi as $item)
                        <tr class="hover:bg-slate-50/60 transition duration-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-mono font-bold text-slate-900">{{ $item['kode'] }}</div>
                                <div class="font-semibold text-slate-800 text-xs mt-0.5">{{ $item['pemesan'] }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $item['telepon'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 text-xs">{{ $item['rute'] }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $item['jadwal'] }} (Kursi #{{ $item['kursi'] }})</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-extrabold text-slate-900">{{ $item['total'] }}</div>
                                <div class="text-[11px] text-slate-500">{{ $item['bank'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                {{ $item['tgl_transfer'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $item['badge'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item['dot'] }}"></span>
                                    {{ $item['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('operator.pembayaran.show', $item['kode']) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl transition shadow-xs {{ $item['prioritas'] ? 'bg-sky-600 hover:bg-sky-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                                    <span>Periksa Bukti</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List View -->
        <div class="divide-y divide-slate-100 md:hidden">
            @foreach ($daftarTransaksi as $item)
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono text-xs font-bold text-slate-900 px-2 py-0.5 rounded bg-slate-100 border border-slate-200">
                            {{ $item['kode'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $item['badge'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $item['dot'] }}"></span>
                            {{ $item['status'] }}
                        </span>
                    </div>

                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $item['pemesan'] }} ({{ $item['telepon'] }})</div>
                        <div class="text-xs text-slate-700 font-medium mt-0.5">{{ $item['rute'] }}</div>
                        <div class="text-[11px] text-slate-400">{{ $item['jadwal'] }} &bull; Kursi #{{ $item['kursi'] }}</div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $item['total'] }}</div>
                            <div class="text-[11px] text-slate-400">{{ $item['bank'] }}</div>
                        </div>

                        <a href="{{ route('operator.pembayaran.show', $item['kode']) }}"
                           class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold rounded-xl transition shadow-xs {{ $item['prioritas'] ? 'bg-sky-600 hover:bg-sky-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            <span>Periksa Bukti</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
