@extends('layouts.tamu')

@section('title', 'Daftar Pesanan & Transaksi — Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('member._nav', [
        'judul' => 'Riwayat Pesanan & Pembayaran',
        'deskripsi' => 'Pantau status verifikasi transfer bank, tagihan aktif, dan arsip pemesanan tiket Anda.'
    ])

    @if ($daftarPesanan->isEmpty())
        <div class="text-center py-16 bg-white border-2 border-slate-900 rounded-2xl p-8 shadow-[5px_5px_0px_#18181b]">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-slate-900 text-amber-600 flex items-center justify-center mx-auto mb-4 shadow-[3px_3px_0px_#18181b]">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v1.5c0 .414.336.75.75.75H3m0 0a60.07 60.07 0 0 0 15.797 2.101c.727.198 1.453-.342 1.453-1.096V7.5M3.75 4.5h16.5m-16.5 0a2.25 2.25 0 0 0-2.25 2.25v10.5a2.25 2.25 0 0 0 2.25 2.25h16.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25m0 0V3.75a.75.75 0 0 0-.75-.75H4.5a.75.75 0 0 0-.75.75V4.5" />
                </svg>
            </div>
            <h3 class="font-heading font-black text-slate-900 text-xl">Belum Ada Riwayat Pesanan</h3>
            <p class="text-sm text-slate-600 mt-2 max-w-md mx-auto font-medium">
                Anda belum memiliki transaksi pemesanan tiket travel. Temukan rute perjalanan Anda dan lakukan pemesanan secara mudah.
            </p>
            <div class="mt-6">
                <x-button href="{{ route('jadwal.cari') }}" variant="primary" size="md">
                    <span>Cari Jadwal Travel</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </x-button>
            </div>
        </div>
    @else
        <!-- Container List / Tabel Pesanan -->
        <div class="bg-white border-2 border-slate-900 rounded-2xl overflow-hidden shadow-[5px_5px_0px_#18181b]">
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-amber-50 border-b-2 border-slate-900 text-xs font-heading font-black text-slate-900 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Kode Booking</th>
                            <th class="px-6 py-4">Rute Perjalanan</th>
                            <th class="px-6 py-4">Waktu & Kursi</th>
                            <th class="px-6 py-4">Total Tagihan</th>
                            <th class="px-6 py-4">Status Pesanan</th>
                            <th class="px-6 py-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-100">
                        @foreach ($daftarPesanan as $pesanan)
                            @php
                                $rute = ($pesanan->jadwal?->kotaAsal?->nama_kota ?? '-') . ' → ' . ($pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-');
                                $waktuBerangkat = $pesanan->jadwal?->waktu_berangkat ? \Carbon\Carbon::parse($pesanan->jadwal->waktu_berangkat) : null;
                                $bisaBayar = ($pesanan->status === \App\Enums\StatusPesanan::BelumBayar);
                            @endphp
                            <tr class="hover:bg-amber-50/40 transition duration-100">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono font-black text-xs px-2.5 py-1 rounded bg-stone-100 border-1.5 border-slate-900 text-slate-900 shadow-[1px_1px_0px_#18181b]">
                                        {{ $pesanan->kode_pesanan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-heading font-bold text-slate-900 text-sm">{{ $rute }}</div>
                                    <div class="text-[11px] text-slate-500 font-medium">
                                        {{ $pesanan->jadwal?->kendaraan?->nama_kendaraan ?? 'Travel' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-slate-900 text-xs font-bold">
                                        {{ $waktuBerangkat ? $waktuBerangkat->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-600 font-heading font-black">Nomor Kursi: #{{ $pesanan->nomor_kursi }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-heading font-black text-slate-900">
                                    Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-badge-status :status="$pesanan->status" size="sm" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if ($bisaBayar)
                                        <x-button href="{{ route('member.pembayaran.show', $pesanan->kode_pesanan) }}" variant="primary" size="sm">
                                            <span>Bayar</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                            </svg>
                                        </x-button>
                                    @elseif ($pesanan->status === \App\Enums\StatusPesanan::Lunas)
                                        <x-button href="{{ route('member.tiket.show', $pesanan->kode_pesanan) }}" variant="dark" size="sm">
                                            <span>Tiket</span>
                                        </x-button>
                                    @else
                                        <x-button href="{{ route('member.pesanan.show', $pesanan->kode_pesanan) }}" variant="secondary" size="sm">
                                            <span>Detail</span>
                                        </x-button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card List View -->
            <div class="divide-y-2 divide-slate-100 md:hidden">
                @foreach ($daftarPesanan as $pesanan)
                    @php
                        $rute = ($pesanan->jadwal?->kotaAsal?->nama_kota ?? '-') . ' → ' . ($pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-');
                        $waktuBerangkat = $pesanan->jadwal?->waktu_berangkat ? \Carbon\Carbon::parse($pesanan->jadwal->waktu_berangkat) : null;
                        $bisaBayar = ($pesanan->status === \App\Enums\StatusPesanan::BelumBayar);
                    @endphp
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs font-black text-slate-900 px-2 py-0.5 rounded bg-stone-100 border-1.5 border-slate-900 shadow-[1px_1px_0px_#18181b]">
                                {{ $pesanan->kode_pesanan }}
                            </span>
                            <x-badge-status :status="$pesanan->status" size="sm" />
                        </div>

                        <div>
                            <div class="font-heading font-bold text-slate-900 text-base">{{ $rute }}</div>
                            <div class="text-xs text-slate-500 mt-0.5 font-medium">
                                {{ $waktuBerangkat ? $waktuBerangkat->translatedFormat('d M Y, H:i') . ' WIB' : '-' }} &bull; Kursi #{{ $pesanan->nomor_kursi }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <div>
                                <div class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-400">Total Tagihan</div>
                                <div class="font-heading font-black text-slate-900 text-base">
                                    Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                                </div>
                            </div>

                            <div>
                                @if ($bisaBayar)
                                    <x-button href="{{ route('member.pembayaran.show', $pesanan->kode_pesanan) }}" variant="primary" size="sm">
                                        Bayar Sekarang
                                    </x-button>
                                @elseif ($pesanan->status === \App\Enums\StatusPesanan::Lunas)
                                    <x-button href="{{ route('member.tiket.show', $pesanan->kode_pesanan) }}" variant="dark" size="sm">
                                        Lihat Tiket
                                    </x-button>
                                @else
                                    <x-button href="{{ route('member.pesanan.show', $pesanan->kode_pesanan) }}" variant="secondary" size="sm">
                                        Detail
                                    </x-button>
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
