@extends('layouts.operator')

@section('title', 'Riwayat Transaksi Loket — Operator Gotix')

@section('konten')
<div class="space-y-6 sm:space-y-8">

    @include('operator._nav', [
        'judul' => 'Riwayat Transaksi Loket',
        'deskripsi' => 'Pencarian dan arsip seluruh transaksi reservasi tiket loket yang telah berhasil diterbitkan dan berstatus lunas.'
    ])

    <!-- Metric KPI Transaksi Loket -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Transaksi Lunas -->
        <div class="bg-amber-100 border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-heading font-black uppercase tracking-wider text-slate-900">Total Transaksi</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border border-slate-900"></span>
            </div>
            <div class="my-2">
                <div class="font-heading text-3xl sm:text-4xl font-black text-slate-950 tracking-tight">
                    {{ $totalLunas }}
                </div>
                <div class="text-xs font-bold text-slate-700 mt-0.5">Tiket Berstatus Lunas</div>
            </div>
            <div class="text-[11px] text-slate-600 font-medium border-t border-slate-900/20 pt-2">
                Seluruh pesanan resmi di basis data
            </div>
        </div>

        <!-- Card 2: Transaksi Walk-in -->
        <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-heading font-black uppercase tracking-wider text-purple-900">Walk-in (Umum)</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-900 border border-slate-900">Non-Member</span>
            </div>
            <div class="my-2">
                <div class="font-heading text-3xl sm:text-4xl font-black text-slate-950 tracking-tight">
                    {{ $totalWalkin }}
                </div>
                <div class="text-xs font-bold text-slate-600 mt-0.5">Penumpang Konter</div>
            </div>
            <div class="text-[11px] text-slate-500 font-medium border-t border-slate-200 pt-2">
                Transaksi tiket tanpa akun member
            </div>
        </div>

        <!-- Card 3: Transaksi Pelanggan Member -->
        <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-heading font-black uppercase tracking-wider text-blue-900">Member Terdaftar</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-900 border border-slate-900">Akun</span>
            </div>
            <div class="my-2">
                <div class="font-heading text-3xl sm:text-4xl font-black text-slate-950 tracking-tight">
                    {{ $totalMember }}
                </div>
                <div class="text-xs font-bold text-slate-600 mt-0.5">Pelanggan Terdaftar</div>
            </div>
            <div class="text-[11px] text-slate-500 font-medium border-t border-slate-200 pt-2">
                Transaksi terhubung akun penumpang
            </div>
        </div>

        <!-- Card 4: Pintasan Kasir Baru -->
        <div class="bg-stone-100 border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-heading font-black uppercase tracking-wider text-slate-700">Layanan Loket</span>
                <svg class="w-4 h-4 text-slate-900 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <div class="my-2">
                <div class="font-heading text-lg font-black text-slate-900 leading-tight">
                    Pemesanan Baru
                </div>
                <div class="text-xs text-slate-600 mt-0.5">Buka kasir untuk melayani penumpang</div>
            </div>
            <div class="pt-2 border-t border-slate-300">
                <x-button href="{{ route('operator.loket.index') }}" variant="primary" size="sm" class="w-full text-center">
                    Buka Kasir Loket &rarr;
                </x-button>
            </div>
        </div>
    </div>

    <!-- Arsip & Pencarian Transaksi -->
    <x-card title="Pencarian & Daftar Riwayat Transaksi"
            subtitle="Gunakan kolom pencarian untuk menemukan tiket berdasarkan kode booking, nama, telepon, atau rute">
        <x-slot:headerAction>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 border border-slate-900 text-emerald-950 text-xs font-heading font-black">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    <span>Total: {{ $riwayatPesanan->total() }} Transaksi</span>
                </span>
            </div>
        </x-slot:headerAction>

        <!-- Form Pencarian & Filter Kategori Tipe Pelanggan -->
        <form method="GET" action="{{ route('operator.riwayat.index') }}"
              class="mb-6 space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input type="text"
                           name="cari"
                           value="{{ request('cari') }}"
                           placeholder="Cari kode booking (contoh: GTX-...), nama penumpang, atau nomor telepon..."
                           class="w-full bg-white border-2 border-slate-900 rounded-xl pl-10 pr-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] shadow-[2px_2px_0px_#18181b] transition">
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <x-button type="submit" variant="primary" size="md">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <span>Cari</span>
                    </x-button>

                    @if (request('cari') || request('tipe'))
                        <x-button href="{{ route('operator.riwayat.index') }}" variant="outline" size="md">
                            <span>Reset</span>
                        </x-button>
                    @endif
                </div>
            </div>

            <!-- Tab Filter Tipe Pelanggan -->
            <div class="flex items-center gap-2 pt-1 border-t border-slate-200">
                <span class="text-xs font-heading font-bold text-slate-600">Filter Tipe:</span>
                <a href="{{ route('operator.riwayat.index', array_merge(request()->except('tipe', 'page'), ['tipe' => ''])) }}"
                   class="px-2.5 py-1 rounded-lg text-xs font-heading font-extrabold transition {{ !request('tipe') ? 'bg-slate-900 text-white' : 'bg-stone-100 text-slate-700 hover:bg-slate-200' }}">
                    Semua
                </a>
                <a href="{{ route('operator.riwayat.index', array_merge(request()->except('tipe', 'page'), ['tipe' => 'walkin'])) }}"
                   class="px-2.5 py-1 rounded-lg text-xs font-heading font-extrabold transition {{ request('tipe') === 'walkin' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-900 hover:bg-purple-100' }}">
                    Walk-in ({{ $totalWalkin }})
                </a>
                <a href="{{ route('operator.riwayat.index', array_merge(request()->except('tipe', 'page'), ['tipe' => 'member'])) }}"
                   class="px-2.5 py-1 rounded-lg text-xs font-heading font-extrabold transition {{ request('tipe') === 'member' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-900 hover:bg-blue-100' }}">
                    Member ({{ $totalMember }})
                </a>
            </div>
        </form>

        @if ($riwayatPesanan->isNotEmpty())
            <!-- Tampilan Desktop (Tabel Bergaris Tinta) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-stone-100 text-[11px] font-heading font-black text-slate-700 uppercase tracking-wider border-b-2 border-slate-900">
                        <tr>
                            <th class="px-5 py-3.5">Kode Booking</th>
                            <th class="px-5 py-3.5">Pelanggan & Kontak</th>
                            <th class="px-5 py-3.5">Rute & Waktu</th>
                            <th class="px-5 py-3.5 text-center">Kursi</th>
                            <th class="px-5 py-3.5 text-right">Tarif</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 border-b-2 border-slate-900 divide-slate-200">
                        @foreach ($riwayatPesanan as $rp)
                            <tr class="hover:bg-amber-50/50 transition duration-150">
                                <td class="px-5 py-4 font-mono font-black text-slate-950 text-xs">
                                    #{{ $rp->kode_pesanan }}
                                    <div class="text-[10px] text-slate-500 font-normal mt-0.5">
                                        {{ $rp->created_at ? $rp->created_at->format('d/m/Y H:i') : '-' }} WIB
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-950 text-xs">
                                        {{ $rp->pengguna?->nama ?? $rp->nama_penumpang ?? '-' }}
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-1.5">
                                        @if ($rp->pengguna_id)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-900 border border-slate-900">
                                                Member
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-900 border border-slate-900">
                                                Walk-in
                                            </span>
                                        @endif
                                        <span class="text-[11px] text-slate-500 font-mono">
                                            {{ $rp->pengguna?->telepon ?? $rp->telepon_penumpang ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <div class="font-bold text-slate-900">
                                        {{ $rp->jadwal?->kotaAsal?->nama_kota ?? '-' }} &rarr; {{ $rp->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $rp->jadwal?->tanggal_berangkat ? $rp->jadwal->tanggal_berangkat->format('d M Y') : '-' }} &bull;
                                        {{ $rp->jadwal?->jam_berangkat ? substr((string) $rp->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $rp->jadwal?->kendaraan?->jenis_kendaraan ?? 'Travel' }} ({{ $rp->jadwal?->kendaraan?->nomor_polisi ?? '-' }})
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-amber-100 border border-slate-900 font-heading font-black text-xs text-slate-950 shadow-[1px_1px_0px_#18181b]">
                                        #{{ $rp->nomor_kursi }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right font-heading font-black text-slate-950 text-sm whitespace-nowrap">
                                    Rp {{ number_format($rp->harga_snapshot, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <x-badge-status :status="$rp->status" size="sm" />
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <x-button href="{{ route('operator.pembayaran.show', $rp->id) }}" variant="outline" size="sm">
                                        <span>Detail</span>
                                        <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tampilan Mobile (Kartu Taktil Responsif) -->
            <div class="md:hidden space-y-3">
                @foreach ($riwayatPesanan as $rp)
                    <div class="p-4 rounded-xl border-2 border-slate-900 bg-white shadow-[2px_2px_0px_#18181b] space-y-2.5 text-xs">
                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-200">
                            <div>
                                <span class="font-mono font-black text-slate-950">#{{ $rp->kode_pesanan }}</span>
                                <div class="font-bold text-slate-900">{{ $rp->pengguna?->nama ?? $rp->nama_penumpang ?? '-' }}</div>
                            </div>
                            <x-badge-status :status="$rp->status" size="sm" />
                        </div>

                        <div class="text-slate-600 space-y-0.5">
                            <div class="font-bold text-slate-900">
                                {{ $rp->jadwal?->kotaAsal?->nama_kota ?? '-' }} &rarr; {{ $rp->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                            </div>
                            <div class="text-[11px] text-slate-500">
                                {{ $rp->jadwal?->tanggal_berangkat ? $rp->jadwal->tanggal_berangkat->format('d M Y') : '-' }} &bull;
                                {{ $rp->jadwal?->jam_berangkat ? substr((string) $rp->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                            </div>
                            <div class="mt-1">
                                @if ($rp->pengguna_id)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-900 border border-slate-900">
                                        Member Terdaftar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-900 border border-slate-900">
                                        Walk-in Umum
                                    </span>
                                @endif
                                <span class="text-[11px] font-mono text-slate-500 ml-1">
                                    {{ $rp->pengguna?->telepon ?? $rp->telepon_penumpang ?? '' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                            <div>
                                <span class="font-bold text-amber-800">Kursi #{{ $rp->nomor_kursi }}</span> &bull;
                                <span class="font-heading font-black text-slate-950 text-sm">Rp {{ number_format($rp->harga_snapshot, 0, ',', '.') }}</span>
                            </div>
                            <x-button href="{{ route('operator.pembayaran.show', $rp->id) }}" variant="outline" size="sm">
                                <span>Detail</span>
                            </x-button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links Taktil -->
            @if ($riwayatPesanan->hasPages())
                <div class="pt-4 border-t-2 border-slate-900 mt-4">
                    {{ $riwayatPesanan->links() }}
                </div>
            @endif
        @else
            <div class="p-8 text-center bg-stone-50 border-2 border-dashed border-slate-300 rounded-2xl space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 border-2 border-slate-900 flex items-center justify-center mx-auto shadow-[2px_2px_0px_#18181b]">
                    <svg class="w-6 h-6 text-slate-900 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <div class="font-heading font-extrabold text-slate-900">Tidak Ada Transaksi Ditemukan</div>
                <p class="text-xs text-slate-500">
                    @if (request('cari'))
                        Tidak ada pesanan yang cocok dengan kata kunci "<strong>{{ request('cari') }}</strong>".
                    @else
                        Belum ada riwayat transaksi loket yang tercatat.
                    @endif
                </p>
                @if (request('cari') || request('tipe'))
                    <div class="pt-2">
                        <x-button href="{{ route('operator.riwayat.index') }}" variant="outline" size="sm">
                            Tampilkan Seluruh Transaksi
                        </x-button>
                    </div>
                @endif
            </div>
        @endif
    </x-card>
</div>
@endsection
