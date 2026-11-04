@extends('layouts.tamu')

@section('title', 'Laporan & Transaksi Tiket Nasional — Admin Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('admin._nav', [
        'judul' => 'Laporan Transaksi & Reservasi',
        'deskripsi' => 'Rekapitulasi penjualan tiket travel dan pemantauan perputaran pendapatan di seluruh rute Indonesia.'
    ])

    <!-- Statistik Ringkas di Bagian Atas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" data-aos="fade-up" data-aos-duration="400">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Transaksi Masuk</div>
            <div class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">1.482</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">&uarr; +12% dibanding bulan lalu</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Pendapatan Tiket</div>
            <div class="font-heading text-2xl sm:text-3xl font-extrabold text-sky-600 mt-1">Rp 214,8 Jt</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Pendapatan kotor transfer & loket</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tiket Terverifikasi Lunas</div>
            <div class="font-heading text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">1.390</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Tingkat keberhasilan 93.8%</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pesanan Dibatalkan</div>
            <div class="font-heading text-2xl sm:text-3xl font-extrabold text-rose-600 mt-1">92</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Melewati batas bayar 24 jam</div>
        </div>
    </div>

    <!-- Filter Bar & Rentang Tanggal -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4" data-aos="fade-down" data-aos-duration="400">
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Dari Tanggal</label>
                <input type="date" value="{{ date('Y-m-01') }}"
                       class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Sampai Tanggal</label>
                <input type="date" value="{{ date('Y-m-d') }}"
                       class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
            <div class="self-end">
                <button type="button" onclick="alert('Filter laporan diterapkan.')"
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition cursor-pointer">
                    Terapkan Filter
                </button>
            </div>
        </div>

        <div class="self-end md:self-auto">
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                </svg>
                <span>Unduh Rekap Laporan</span>
            </button>
        </div>
    </div>

    @php
        $rekapTransaksi = [
            [
                'kode' => 'GTX-908211',
                'pemesan' => 'Budi Santoso',
                'rute' => 'Jakarta → Bandung',
                'kursi' => 2,
                'total' => 'Rp 120.000',
                'metode' => 'Transfer Manual (BCA)',
                'tgl' => date('d M Y, H:i', strtotime('-1 day')),
                'status' => 'Lunas',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            ],
            [
                'kode' => 'GTX-774102',
                'pemesan' => 'Siti Rahma',
                'rute' => 'Yogyakarta → Surabaya',
                'kursi' => 4,
                'total' => 'Rp 180.000',
                'metode' => 'Transfer Manual (BCA)',
                'tgl' => date('d M Y, H:i'),
                'status' => 'Dalam Proses',
                'badge' => 'bg-sky-50 text-sky-700 border-sky-200',
            ],
            [
                'kode' => 'GTX-LOKET8821',
                'pemesan' => 'Hendra Kusuma (Walk-in)',
                'rute' => 'Jakarta → Bandung',
                'kursi' => 3,
                'total' => 'Rp 120.000',
                'metode' => 'Kasir Tunai (Loket)',
                'tgl' => date('d M Y, H:i'),
                'status' => 'Lunas',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            ],
            [
                'kode' => 'GTX-321550',
                'pemesan' => 'Agus Setiawan',
                'rute' => 'Medan → Pekanbaru',
                'kursi' => 1,
                'total' => 'Rp 240.000',
                'metode' => 'Transfer Manual',
                'tgl' => date('d M Y, H:i', strtotime('-2 days')),
                'status' => 'Belum Bayar',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
            ],
            [
                'kode' => 'GTX-112093',
                'pemesan' => 'Dewi Lestari',
                'rute' => 'Semarang → Solo',
                'kursi' => 3,
                'total' => 'Rp 85.000',
                'metode' => 'Transfer Manual',
                'tgl' => date('d M Y, H:i', strtotime('-20 days')),
                'status' => 'Dibatalkan',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
            ],
        ];
    @endphp

    <!-- Tabel Transaksi Nasional -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up" data-aos-duration="500">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Kode Booking</th>
                        <th class="px-6 py-4">Nama Penumpang</th>
                        <th class="px-6 py-4">Rute Perjalanan</th>
                        <th class="px-6 py-4">Tarif & Pembayaran</th>
                        <th class="px-6 py-4">Waktu Pemesanan</th>
                        <th class="px-6 py-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($rekapTransaksi as $trx)
                        <tr class="hover:bg-slate-50/60 transition duration-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono font-bold text-slate-900 text-xs">{{ $trx['kode'] }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-900 text-xs whitespace-nowrap">
                                {{ $trx['pemesan'] }}
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-800">
                                {{ $trx['rute'] }} <span class="text-slate-400">(Seat #{{ $trx['kursi'] }})</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 text-xs">{{ $trx['total'] }}</div>
                                <div class="text-[11px] text-slate-500">{{ $trx['metode'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                {{ $trx['tgl'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $trx['badge'] }}">
                                    {{ $trx['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
