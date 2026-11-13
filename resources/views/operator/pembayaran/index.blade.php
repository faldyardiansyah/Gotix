@extends('layouts.operator')

@section('title', 'Verifikasi Pembayaran Penumpang — Operator Gotix')

@section('konten')
<div class="space-y-6 sm:space-y-8"
     x-data="{
        tabFilter: 'semua',
        matchesFilter(status) {
            if (this.tabFilter === 'semua') return true;
            if (this.tabFilter === 'dalam_proses') return status === 'Dalam Proses';
            if (this.tabFilter === 'belum_bayar') return status === 'Belum Bayar';
            return true;
        }
     }">

    @include('operator._nav', [
        'judul' => 'Verifikasi Pembayaran Penumpang',
        'deskripsi' => 'Periksa struk transfer manual yang diunggah penumpang untuk memvalidasi dan menerbitkan tiket resmi armada travel.'
    ])

    <!-- Flash Notification Sukses / Gagal -->
    @if (session('sukses'))
        <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] text-xs sm:text-sm font-bold text-emerald-950 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 border border-slate-900 text-slate-950 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    @if (session('gagal'))
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] text-xs sm:text-sm font-bold text-rose-950 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-rose-500 border border-slate-900 text-white flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </div>
            <span>{{ session('gagal') }}</span>
        </div>
    @endif

    @php
        $countDalamProses = $daftarPesanan->filter(fn($p) => ($p->status->value ?? (string)$p->status) === 'Dalam Proses')->count();
        $countBelumBayar = $daftarPesanan->filter(fn($p) => ($p->status->value ?? (string)$p->status) === 'Belum Bayar')->count();
    @endphp

    <!-- Metric Counter Ringkas (Aktual dari Database) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Perlu Tindakan Verifikasi -->
        <div class="bg-amber-100 border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-heading font-black uppercase tracking-wider text-slate-900">Perlu Tindakan</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse border border-slate-900"></span>
            </div>
            <div class="my-2">
                <div class="font-heading text-3xl sm:text-4xl font-black text-slate-950 tracking-tight">
                    {{ $countDalamProses }}
                </div>
                <div class="text-xs font-bold text-slate-700 mt-0.5">Struk Siap Diperiksa</div>
            </div>
            <div class="text-[11px] text-slate-600 font-medium border-t border-slate-900/20 pt-2">
                Prioritas verifikasi sebelum jadwal armada
            </div>
        </div>

        <!-- Card 2: Menunggu Bukti Penumpang -->
        <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-heading font-black uppercase tracking-wider text-slate-500">Belum Bayar</span>
                <span class="w-2.5 h-2.5 rounded-full bg-slate-400 border border-slate-900"></span>
            </div>
            <div class="my-2">
                <div class="font-heading text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    {{ $countBelumBayar }}
                </div>
                <div class="text-xs font-bold text-slate-600 mt-0.5">Menunggu Transfer</div>
            </div>
            <div class="text-[11px] text-slate-500 font-medium border-t border-slate-200 pt-2">
                Dalam tenggat batas pembayaran
            </div>
        </div>

        <!-- Card 3: Total Tiket Terverifikasi -->
        <div class="bg-emerald-50 border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-heading font-black uppercase tracking-wider text-emerald-950">Total Terverifikasi</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border border-slate-900"></span>
            </div>
            <div class="my-2">
                <div class="font-heading text-3xl sm:text-4xl font-black text-emerald-950 tracking-tight">
                    {{ $selesaiVerifikasi }}
                </div>
                <div class="text-xs font-bold text-emerald-900 mt-0.5">Tiket Berstatus Lunas</div>
            </div>
            <div class="text-[11px] text-emerald-800 font-medium border-t border-slate-900/20 pt-2">
                E-Tiket resmi telah terbit ke akun penumpang
            </div>
        </div>

        <!-- Card 4: Pintasan Cepat Kasir Loket -->
        <div class="bg-stone-100 border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-heading font-black uppercase tracking-wider text-slate-700">Layanan Loket</span>
                <svg class="w-4 h-4 text-slate-900 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </div>
            <div class="my-2">
                <div class="font-heading text-lg font-black text-slate-900 leading-tight">
                    Pemesanan Walk-in
                </div>
                <div class="text-xs text-slate-600 mt-0.5">Penjualan tiket langsung di konter</div>
            </div>
            <div class="pt-2 border-t border-slate-300">
                <x-button href="{{ route('operator.loket.index') }}" variant="primary" size="sm" class="w-full text-center">
                    Buka Kasir Loket &rarr;
                </x-button>
            </div>
        </div>
    </div>

    <!-- Antrean Daftar Pembayaran Real Database -->
    <x-card title="Antrean Verifikasi Pembayaran"
            subtitle="Pemeriksaan bukti transfer yang diunggah penumpang travel">
        <x-slot:headerAction>
            <!-- Tab Filter Kategori -->
            <div class="flex items-center gap-1.5 p-1 rounded-xl bg-stone-100 border border-slate-900 text-xs">
                <button type="button"
                        @click="tabFilter = 'semua'"
                        :class="tabFilter === 'semua' ? 'bg-amber-500 text-slate-950 font-black shadow-[1.5px_1.5px_0px_#18181b]' : 'text-slate-600 hover:text-slate-950 font-bold'"
                        class="px-2.5 py-1 rounded-lg transition border border-transparent">
                    Semua ({{ $daftarPesanan->count() }})
                </button>
                <button type="button"
                        @click="tabFilter = 'dalam_proses'"
                        :class="tabFilter === 'dalam_proses' ? 'bg-amber-500 text-slate-950 font-black shadow-[1.5px_1.5px_0px_#18181b]' : 'text-slate-600 hover:text-slate-950 font-bold'"
                        class="px-2.5 py-1 rounded-lg transition border border-transparent">
                    Perlu Ditinjau ({{ $countDalamProses }})
                </button>
                <button type="button"
                        @click="tabFilter = 'belum_bayar'"
                        :class="tabFilter === 'belum_bayar' ? 'bg-amber-500 text-slate-950 font-black shadow-[1.5px_1.5px_0px_#18181b]' : 'text-slate-600 hover:text-slate-950 font-bold'"
                        class="px-2.5 py-1 rounded-lg transition border border-transparent">
                    Belum Bayar ({{ $countBelumBayar }})
                </button>
            </div>
        </x-slot:headerAction>

        <!-- Tampilan Desktop (Tabel Bergaris Tinta) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-stone-100 text-[11px] font-heading font-black text-slate-700 uppercase tracking-wider border-b-2 border-slate-900">
                    <tr>
                        <th class="px-5 py-3.5">Kode & Pemesan</th>
                        <th class="px-5 py-3.5">Rute & Armada</th>
                        <th class="px-5 py-3.5">Nominal & Rekening</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 border-b-2 border-slate-900 divide-slate-200">
                    @forelse ($daftarPesanan as $pesanan)
                        @php
                            $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
                        @endphp
                        <tr class="hover:bg-amber-50/50 transition duration-150"
                            x-show="matchesFilter('{{ $statusVal }}')">
                            <td class="px-5 py-4">
                                <div class="font-mono text-xs font-black text-slate-950">
                                    #{{ $pesanan->kode_pesanan }}
                                </div>
                                <div class="font-bold text-slate-900 text-xs mt-0.5">
                                    {{ $pesanan->pengguna?->nama ?? $pesanan->nama_penumpang ?? 'Tamu Walk-in' }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    {{ $pesanan->pengguna?->telepon ?? $pesanan->telepon_penumpang ?? '-' }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-950 text-xs">
                                    {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} &rarr; {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-600 mt-0.5">
                                    {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                                    &bull; {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                                </div>
                                <div class="text-[11px] font-semibold text-amber-700 mt-0.5">
                                    Kursi #{{ $pesanan->nomor_kursi }} &bull; {{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? 'Travel' }}
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-heading font-black text-slate-950 text-sm">
                                    Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                                </div>
                                @if ($pesanan->konfirmasiPembayaran)
                                    <div class="text-[11px] font-bold text-emerald-700 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span class="max-w-[150px] truncate">{{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono">
                                        Resi: {{ $pesanan->konfirmasiPembayaran->nomor_resi ?? '-' }}
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400 italic mt-0.5">Belum ada struk</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <x-badge-status :status="$pesanan->status" size="sm" />
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <x-button href="{{ route('operator.pembayaran.show', $pesanan->id) }}"
                                          variant="{{ $statusVal === 'Dalam Proses' ? 'primary' : 'outline' }}"
                                          size="sm">
                                    <span>Periksa Bukti</span>
                                    <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 border-2 border-slate-900 flex items-center justify-center mx-auto mb-3 shadow-[2px_2px_0px_#18181b]">
                                    <svg class="w-6 h-6 text-slate-950 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                    </svg>
                                </div>
                                <div class="font-heading font-extrabold text-slate-900 text-base">Antrean Bersih!</div>
                                <div class="text-xs text-slate-500 mt-1">Tidak ada transaksi yang membutuhkan verifikasi pembayaran saat ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tampilan Mobile / Tablet Kecil (Kartu Taktil Responsif) -->
        <div class="md:hidden space-y-4">
            @forelse ($daftarPesanan as $pesanan)
                @php
                    $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
                @endphp
                <div class="p-4 rounded-2xl border-2 border-slate-900 bg-white shadow-[3px_3px_0px_#18181b] space-y-3"
                     x-show="matchesFilter('{{ $statusVal }}')">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                        <div>
                            <span class="font-mono text-xs font-black text-slate-950">#{{ $pesanan->kode_pesanan }}</span>
                            <div class="text-xs font-bold text-slate-900">{{ $pesanan->pengguna?->nama ?? $pesanan->nama_penumpang ?? 'Tamu Walk-in' }}</div>
                        </div>
                        <x-badge-status :status="$pesanan->status" size="sm" />
                    </div>

                    <div class="text-xs space-y-1 text-slate-700">
                        <div class="font-bold text-slate-900">
                            {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} &rarr; {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                        </div>
                        <div class="text-slate-500">
                            {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }} &bull;
                            {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }} (Kursi #{{ $pesanan->nomor_kursi }})
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-slate-500">Total Tagihan:</span>
                            <span class="font-heading font-black text-slate-950 text-sm">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                        </div>
                        @if ($pesanan->konfirmasiPembayaran)
                            <div class="text-[11px] text-emerald-700 font-semibold truncate">
                                Pengirim: {{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}
                            </div>
                        @else
                            <div class="text-[11px] text-slate-400 italic">Belum ada struk transfer</div>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-slate-200">
                        <x-button href="{{ route('operator.pembayaran.show', $pesanan->id) }}"
                                  variant="{{ $statusVal === 'Dalam Proses' ? 'primary' : 'outline' }}"
                                  size="sm"
                                  class="w-full text-center">
                            Periksa Bukti &rarr;
                        </x-button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-stone-50 border-2 border-slate-900 rounded-2xl">
                    <div class="font-heading font-extrabold text-slate-900">Tidak Ada Transaksi</div>
                    <p class="text-xs text-slate-500 mt-1">Antrean verifikasi pembayaran saat ini kosong.</p>
                </div>
            @endforelse
        </div>
    </x-card>
</div>
@endsection
