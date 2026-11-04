@extends('layouts.tamu')

@section('title', 'Daftar Pesanan & Transaksi — Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('member._nav', [
        'judul' => 'Riwayat Pesanan & Pembayaran',
        'deskripsi' => 'Pantau status verifikasi transfer bank, tagihan aktif, dan arsip pemesanan tiket Anda.'
    ])

    @if ($daftarPesanan->isEmpty())
        <div class="text-center py-16 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm" data-aos="fade-up">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v1.5c0 .414.336.75.75.75H3m0 0a60.07 60.07 0 0 0 15.797 2.101c.727.198 1.453-.342 1.453-1.096V7.5M3.75 4.5h16.5m-16.5 0a2.25 2.25 0 0 0-2.25 2.25v10.5a2.25 2.25 0 0 0 2.25 2.25h16.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25m0 0V3.75a.75.75 0 0 0-.75-.75H4.5a.75.75 0 0 0-.75.75V4.5" />
                </svg>
            </div>
            <h3 class="font-heading font-bold text-slate-900 text-lg">Belum Ada Riwayat Pesanan</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Anda belum memiliki transaksi pemesanan tiket travel. Temukan rute dan jadwalkan perjalanan Anda sekarang.</p>
            <a href="{{ route('jadwal.cari') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl transition shadow-sm">
                Cari Jadwal Travel
            </a>
        </div>
    @else
        <!-- Container List / Tabel Pesanan -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up" data-aos-duration="500">
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Kode Booking</th>
                            <th class="px-6 py-4">Rute Perjalanan</th>
                            <th class="px-6 py-4">Jadwal & Kursi</th>
                            <th class="px-6 py-4">Total Tagihan</th>
                            <th class="px-6 py-4">Status Pesanan</th>
                            <th class="px-6 py-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($daftarPesanan as $pesanan)
                            @php
                                $rute = ($pesanan->jadwal?->kotaAsal?->nama_kota ?? '-') . ' → ' . ($pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-');
                                $tglFormat = $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-';
                                $jamFormat = $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-';
                                $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
                                $badgeClass = match ($statusVal) {
                                    'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-rose-50 text-rose-700 border-rose-200',
                                };
                                $dotClass = match ($statusVal) {
                                    'Lunas' => 'bg-emerald-500',
                                    'Dalam Proses' => 'bg-sky-500',
                                    'Belum Bayar' => 'bg-amber-500',
                                    default => 'bg-rose-500',
                                };
                                $bisaBayar = ($statusVal === 'Belum Bayar');
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition duration-100">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-slate-900">{{ $pesanan->kode_pesanan }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $rute }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? 'Travel' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-slate-800 text-xs font-medium">{{ $tglFormat }} &bull; {{ $jamFormat }}</div>
                                    <div class="text-[11px] text-slate-500">Nomor Kursi: #{{ $pesanan->nomor_kursi }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900">
                                    Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                        {{ $statusVal === 'Dalam Proses' ? 'Menunggu Verifikasi' : $statusVal }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if ($bisaBayar)
                                        <a href="{{ route('member.pembayaran.show', $pesanan->kode_pesanan) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition shadow-xs">
                                            Bayar Sekarang
                                        </a>
                                    @else
                                        <a href="{{ route('member.pesanan.show', $pesanan->kode_pesanan) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card List View -->
            <div class="divide-y divide-slate-100 md:hidden">
                @foreach ($daftarPesanan as $pesanan)
                    @php
                        $rute = ($pesanan->jadwal?->kotaAsal?->nama_kota ?? '-') . ' → ' . ($pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-');
                        $tglFormat = $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-';
                        $jamFormat = $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-';
                        $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
                        $badgeClass = match ($statusVal) {
                            'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-rose-50 text-rose-700 border-rose-200',
                        };
                        $dotClass = match ($statusVal) {
                            'Lunas' => 'bg-emerald-500',
                            'Dalam Proses' => 'bg-sky-500',
                            'Belum Bayar' => 'bg-amber-500',
                            default => 'bg-rose-500',
                        };
                        $bisaBayar = ($statusVal === 'Belum Bayar');
                    @endphp
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs font-bold text-slate-900 px-2 py-0.5 rounded bg-slate-100 border border-slate-200">
                                {{ $pesanan->kode_pesanan }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $statusVal === 'Dalam Proses' ? 'Menunggu Verifikasi' : $statusVal }}
                            </span>
                        </div>

                        <div>
                            <div class="font-bold text-slate-900 text-sm">{{ $rute }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $tglFormat }} &bull; {{ $jamFormat }} (Kursi #{{ $pesanan->nomor_kursi }})</div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <div>
                                <div class="text-[10px] uppercase font-semibold text-slate-400">Total Tarif</div>
                                <div class="font-extrabold text-slate-900 text-base">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</div>
                            </div>

                            <div>
                                @if ($bisaBayar)
                                    <a href="{{ route('member.pembayaran.show', $pesanan->kode_pesanan) }}"
                                       class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition shadow-xs">
                                        Bayar Sekarang
                                    </a>
                                @else
                                    <a href="{{ route('member.pesanan.show', $pesanan->kode_pesanan) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                                        Detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
