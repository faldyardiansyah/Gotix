@extends('layouts.operator')

@section('title', 'Verifikasi Struk #' . $pesanan->kode_pesanan . ' — Operator Gotix')

@section('konten')
<div class="space-y-6 sm:space-y-8"
     x-data="{
        modalBukti: false,
        modalTolak: false,
        previewImageSrc: '{{ $pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path ? route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) : '' }}'
     }">

    @php
        $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
    @endphp

    <!-- Breadcrumb & Navigasi Kembali -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-slate-900">
        <div class="flex flex-wrap items-center gap-2">
            <x-button href="{{ route('operator.pembayaran.index') }}" variant="outline" size="sm">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Antrean Verifikasi</span>
            </x-button>

            <x-button href="{{ route('operator.loket.index', ['tab' => 'riwayat']) }}#riwayat-transaksi" variant="outline" size="sm">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>Riwayat Loket</span>
            </x-button>

            <span class="text-xs font-bold text-slate-400 hidden sm:inline">&bull;</span>
            <span class="text-xs font-mono font-black text-slate-900 hidden sm:inline">Transaksi #{{ $pesanan->kode_pesanan }}</span>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-heading font-black text-slate-500 uppercase tracking-wider">Status Pesanan:</span>
            <x-badge-status :status="$pesanan->status" size="md" />
        </div>
    </div>

    <!-- Flash Notifications -->
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

    @if ($errors->any())
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] text-xs sm:text-sm font-bold text-rose-950 space-y-1">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>Perhatian Validasi:</span>
            </div>
            <ul class="list-disc list-inside text-rose-800 space-y-0.5 pl-6 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        <!-- Kolom Kiri: Rincian Pesanan & Audit Trail (lg:col-span-7) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Card 1: Rincian Transaksi & Kontak Penumpang -->
            <x-card title="Rincian Transaksi & Penumpang"
                    subtitle="Identitas pemesan dan batas waktu pembayaran transaksi">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 bg-stone-50 border-2 border-slate-900 rounded-xl">
                        <div>
                            <span class="text-[11px] font-heading font-black uppercase tracking-wider text-slate-500">Nomor Transaksi</span>
                            <div class="font-mono text-xl font-black text-slate-950 mt-0.5">#{{ $pesanan->kode_pesanan }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-heading font-black uppercase tracking-wider text-slate-500">Waktu Booking</span>
                            <div class="text-xs font-mono font-bold text-slate-800 mt-0.5">
                                {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-' }} WIB
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div class="p-3.5 bg-white border-2 border-slate-900 rounded-xl">
                            <span class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-500">Nama Penumpang</span>
                            <div class="font-heading font-black text-slate-950 text-sm mt-0.5">
                                {{ $pesanan->pengguna?->nama ?? $pesanan->nama_penumpang ?? '-' }}
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">
                                {{ $pesanan->pengguna?->telepon ?? $pesanan->telepon_penumpang ?? '-' }}
                            </div>
                            <div class="mt-2">
                                @if ($pesanan->pengguna_id)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-slate-900">
                                        Member Terdaftar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-900 border border-slate-900">
                                        Walk-in Non-Member
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-3.5 bg-white border-2 border-slate-900 rounded-xl">
                            <span class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-500">Batas Pembayaran</span>
                            <div class="font-mono font-bold text-slate-950 text-xs mt-1">
                                {{ $pesanan->batas_bayar ? $pesanan->batas_bayar->format('d M Y, H:i') : '-' }} WIB
                            </div>
                            @if ($pesanan->dibuat_oleh_operator_id)
                                <div class="mt-2 pt-2 border-t border-slate-200 text-[11px] text-slate-600">
                                    Dibuat oleh Operator ID: <span class="font-mono font-bold">{{ $pesanan->dibuat_oleh_operator_id }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Card 2: Informasi Rute & Armada Perjalanan -->
            <x-card title="Informasi Rute & Armada"
                    subtitle="Trayek travel, nomor armada, dan nomor kursi terpilih">
                <div class="p-4 rounded-xl bg-amber-50 border-2 border-slate-900 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600">Trayek Keberangkatan</span>
                        <span class="font-heading font-black text-slate-950 text-sm">
                            {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} &rarr; {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-900/10">
                        <span class="text-slate-600 font-medium">Jadwal Keberangkatan</span>
                        <span class="font-bold text-slate-950">
                            {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }} &bull;
                            {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-900/10">
                        <span class="text-slate-600 font-medium">Armada Kendaraan</span>
                        <span class="font-bold text-slate-950">
                            {{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? '-' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-900/10">
                        <span class="text-xs text-slate-600 font-medium">Nomor Kursi</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-xl bg-amber-500 border-2 border-slate-900 font-heading font-black text-slate-950 text-sm shadow-[1.5px_1.5px_0px_#18181b]">
                            Kursi #{{ $pesanan->nomor_kursi }}
                        </span>
                    </div>
                </div>
            </x-card>

            <!-- Card 3: Pencocokan Tagihan vs Bukti Transfer -->
            <x-card title="Pencocokan Nominal & Data Transfer"
                    subtitle="Verifikasi kesesuaian antara tarif resmi tiket dan data transfer penumpang">
                <div class="border-2 border-slate-900 rounded-xl p-4 bg-white space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-600 font-bold uppercase tracking-wider">Tarif Tagihan Tiket</span>
                        <span class="font-heading font-black text-xl text-slate-950">
                            Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="pt-2 border-t border-slate-200 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Metode / Rekening Pengirim:</span>
                            <span class="font-bold text-slate-900">
                                @if ($pesanan->konfirmasiPembayaran)
                                    {{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}
                                @elseif ($pesanan->dibuat_oleh_operator_id)
                                    Pembayaran Tunai di Konter Loket
                                @else
                                    Belum ada konfirmasi
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Resi / Referensi:</span>
                            <span class="font-mono font-bold text-slate-900">
                                @if ($pesanan->konfirmasiPembayaran)
                                    {{ $pesanan->konfirmasiPembayaran->nomor_resi ?? '-' }}
                                @elseif ($pesanan->dibuat_oleh_operator_id)
                                    KASIR-LOKET-{{ $pesanan->kode_pesanan }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Waktu Pembayaran:</span>
                            <span class="font-medium text-slate-900">
                                @if ($pesanan->konfirmasiPembayaran)
                                    {{ $pesanan->konfirmasiPembayaran->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }}
                                    {{ $pesanan->konfirmasiPembayaran->jam_transfer ? substr($pesanan->konfirmasiPembayaran->jam_transfer, 0, 5) . ' WIB' : '' }}
                                @elseif ($pesanan->dibuat_oleh_operator_id)
                                    {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-' }} WIB
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Card 4: Audit Trail Riwayat Unggahan -->
            @if ($pesanan->riwayatPembayaran && $pesanan->riwayatPembayaran->count() > 0)
                <x-card title="Riwayat Unggahan & Verifikasi (Audit Trail)"
                        subtitle="Catatan kronologis seluruh bukti pembayaran yang pernah diunggah penumpang">
                    <div class="space-y-3">
                        @foreach ($pesanan->riwayatPembayaran as $index => $riwayat)
                            <div class="p-4 border-2 border-slate-900 rounded-xl text-xs space-y-2 {{ $riwayat->status === 'Ditolak' ? 'bg-rose-50' : ($riwayat->status === 'Diterima' ? 'bg-emerald-50' : 'bg-stone-50') }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-heading font-black text-slate-950">
                                        Unggahan ke-{{ $pesanan->riwayatPembayaran->count() - $index }} (Resi: <span class="font-mono">{{ $riwayat->nomor_resi }}</span>)
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-heading font-black uppercase tracking-wider border border-slate-900 {{ $riwayat->status === 'Diterima' ? 'bg-emerald-200 text-emerald-950' : ($riwayat->status === 'Ditolak' ? 'bg-rose-200 text-rose-950' : 'bg-amber-200 text-amber-950') }}">
                                        {{ $riwayat->status }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-600">
                                    Diunggah pada: {{ $riwayat->created_at ? $riwayat->created_at->format('d M Y, H:i') : '-' }} WIB &bull; Pengirim: <strong>{{ $riwayat->rekening_pengirim }}</strong>
                                </div>
                                @if ($riwayat->diverifikasi_oleh)
                                    <div class="text-[11px] text-slate-700 pt-1.5 border-t border-slate-900/10">
                                        Diverifikasi oleh: <strong>{{ $riwayat->verifikator?->nama ?? 'Petugas' }}</strong> pada {{ $riwayat->diverifikasi_pada ? $riwayat->diverifikasi_pada->format('d M Y, H:i') : '-' }} WIB
                                    </div>
                                @endif
                                @if ($riwayat->alasan_penolakan)
                                    <div class="text-[11px] text-rose-950 bg-rose-100 border border-slate-900 p-2.5 rounded-xl font-medium">
                                        <strong>Alasan Penolakan:</strong> {{ $riwayat->alasan_penolakan }}
                                    </div>
                                @endif
                                <div class="pt-1">
                                    <button type="button"
                                            @click="previewImageSrc = '{{ route('pembayaran.bukti.riwayat', $riwayat->id) }}'; modalBukti = true"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-900 hover:text-amber-700 underline cursor-pointer">
                                        <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                        </svg>
                                        <span>Periksa Berkas Unggahan Ini</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Kolom Kanan: Pratinjau Struk & Panel Tindakan Operator (lg:col-span-5) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Box Pratinjau Struk Bukti Transfer -->
            <x-card title="Berkas Bukti Pembayaran"
                    subtitle="Pemeriksaan struk transfer manual atau bukti kasir loket">
                @if ($pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path)
                    <div @click="previewImageSrc = '{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}'; modalBukti = true"
                         class="relative border-2 border-slate-900 rounded-2xl overflow-hidden bg-stone-100 group cursor-pointer shadow-[3px_3px_0px_#18181b] aspect-[3/4] flex items-center justify-center">
                        <img src="{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}"
                             alt="Bukti Transfer Penumpang"
                             class="w-full h-full object-cover group-hover:scale-102 transition duration-200">
                        <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition duration-150 flex items-center justify-center text-white">
                            <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-950/90 border-2 border-white/40 text-xs font-heading font-black">
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                </svg>
                                <span>Perbesar Struk</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 text-center mt-2 font-medium">
                        Klik pada gambar untuk membuka mode pembesaran struk.
                    </p>
                @elseif ($pesanan->dibuat_oleh_operator_id)
                    <div class="p-8 border-2 border-slate-900 rounded-2xl bg-amber-50 text-center space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500 border-2 border-slate-900 flex items-center justify-center mx-auto shadow-[2px_2px_0px_#18181b]">
                            <svg class="w-6 h-6 text-slate-950 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            </svg>
                        </div>
                        <p class="text-xs font-heading font-black text-slate-950">Transaksi Kasir Loket (Walk-in)</p>
                        <p class="text-[11px] text-slate-600 font-medium">
                            Pembayaran tunai lunas diterima langsung di konter loket stasiun/terminal.
                        </p>
                    </div>
                @else
                    <div class="p-8 border-2 border-dashed border-slate-300 rounded-2xl bg-stone-50 text-center space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 border-2 border-slate-900 flex items-center justify-center mx-auto shadow-[2px_2px_0px_#18181b]">
                            <svg class="w-6 h-6 text-slate-900 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Belum Ada Struk Transfer</p>
                        <p class="text-[11px] text-slate-500">Penumpang belum mengunggah struk atau sedang merevisi bukti bayar.</p>
                    </div>
                @endif
            </x-card>

            <!-- Card: Panel Keputusan Verifikasi Operator (State Guarded) -->
            <x-card title="Keputusan Operator"
                    subtitle="Validasi atau penolakan pembayaran transaksi penumpang">
                @if ($statusVal === 'Dalam Proses')
                    <div class="space-y-3">
                        <form action="{{ route('operator.pembayaran.verifikasi', $pesanan->id) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin memvalidasi pembayaran ini dan menerbitkan E-Tiket resmi?')">
                            @csrf
                            <x-button variant="primary" size="lg" type="submit" class="w-full">
                                <svg class="w-5 h-5 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span>Terima & Terbitkan Tiket</span>
                            </x-button>
                        </form>

                        <x-button variant="danger" size="md" type="button" class="w-full" @click="modalTolak = true">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            <span>Tolak Pembayaran...</span>
                        </x-button>
                    </div>
                @elseif ($statusVal === 'Lunas')
                    <div class="p-4 rounded-xl bg-emerald-50 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] space-y-2 text-center">
                        <div class="inline-flex items-center gap-1.5 text-xs font-heading font-black text-emerald-950">
                            <svg class="w-4 h-4 text-emerald-600 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Pembayaran Telah Lunas & Sah</span>
                        </div>
                        <p class="text-[11px] text-emerald-900 font-medium">
                            @if ($pesanan->dibuat_oleh_operator_id)
                                Diterbitkan oleh Petugas Loket ID: <strong>{{ $pesanan->dibuat_oleh_operator_id }}</strong>
                            @else
                                Diverifikasi oleh: <strong>{{ $pesanan->konfirmasiPembayaran?->verifikator?->nama ?? 'Petugas Loket' }}</strong>
                            @endif
                            <br>pada {{ $pesanan->konfirmasiPembayaran?->diverifikasi_pada ? $pesanan->konfirmasiPembayaran->diverifikasi_pada->format('d M Y, H:i') : ($pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-') }} WIB
                        </p>
                    </div>
                @elseif ($statusVal === 'Belum Bayar')
                    <div class="p-4 rounded-xl bg-amber-50 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] space-y-2">
                        <div class="inline-flex items-center gap-1.5 text-xs font-heading font-black text-amber-950">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Menunggu Pembayaran Penumpang</span>
                        </div>
                        @if ($pesanan->konfirmasiPembayaran?->alasan_penolakan)
                            <div class="text-[11px] text-rose-950 bg-rose-100 border border-slate-900 rounded-xl p-2.5 space-y-1">
                                <div class="font-bold">Alasan Penolakan Terakhir:</div>
                                <div>{{ $pesanan->konfirmasiPembayaran->alasan_penolakan }}</div>
                                <div class="text-[10px] text-slate-600 pt-1">
                                    Oleh: {{ $pesanan->konfirmasiPembayaran->verifikator?->nama ?? 'Petugas' }} &bull; {{ $pesanan->konfirmasiPembayaran->diverifikasi_pada ? $pesanan->konfirmasiPembayaran->diverifikasi_pada->format('d M Y, H:i') : '-' }} WIB
                                </div>
                            </div>
                        @endif
                        <p class="text-[11px] text-amber-900 font-medium">
                            Penumpang memiliki kesempatan mengunggah bukti transfer yang valid sebelum batas pembayaran habis.
                        </p>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-rose-50 border-2 border-slate-900 text-center text-xs font-bold text-rose-950">
                        Pesanan ini telah berstatus DIBATALKAN.
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Modal Form Tolak Pembayaran dengan Alasan Wajib -->
    <div x-show="modalTolak"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         style="display: none;"
         x-cloak>
        <div @click.away="modalTolak = false"
             class="bg-white rounded-2xl border-2 border-slate-900 p-6 max-w-lg w-full shadow-[8px_8px_0px_#18181b] space-y-4">
            <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900">
                <h3 class="font-heading font-black text-slate-950 text-base">Alasan Penolakan Pembayaran</h3>
                <button type="button" @click="modalTolak = false" class="text-slate-500 hover:text-slate-900 transition">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('operator.pembayaran.tolak', $pesanan->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="alasan_penolakan" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Jelaskan alasan penolakan kepada penumpang: <span class="text-rose-600 font-black">*</span>
                    </label>
                    <textarea id="alasan_penolakan"
                              name="alasan_penolakan"
                              rows="4"
                              required
                              placeholder="Contoh: Nominal transfer kurang dari tagihan tiket, foto struk buram tidak terbaca, atau nomor rekening bank tujuan salah."
                              class="w-full bg-white border-2 border-slate-900 rounded-xl p-3 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-rose-600 focus:shadow-[3px_3px_0px_#e11d48] shadow-[2px_2px_0px_#18181b] transition"></textarea>
                    <p class="text-[11px] text-slate-500 mt-1 font-medium">
                        Pesanan akan otomatis dikembalikan ke status <strong>Belum Bayar</strong> agar penumpang dapat mengunggah bukti perbaikan sebelum batas pembayaran.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                    <x-button type="button" variant="outline" size="sm" @click="modalTolak = false">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="danger" size="sm">
                        Kirim Penolakan
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pratinjau Gambar Struk (Zoom In) -->
    <div x-show="modalBukti"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs cursor-zoom-out"
         @click="modalBukti = false"
         style="display: none;"
         x-cloak>
        <div class="relative max-w-3xl max-h-[90vh] bg-transparent" @click.stop>
            <img :src="previewImageSrc"
                 alt="Bukti Transfer Penumpang"
                 class="max-h-[85vh] w-auto mx-auto rounded-2xl shadow-[8px_8px_0px_#18181b] border-2 border-slate-900 object-contain bg-white">
            <button @click="modalBukti = false"
                    class="absolute -top-3 -right-3 p-2 bg-slate-950 hover:bg-slate-900 text-white rounded-full border-2 border-white shadow-[2px_2px_0px_#18181b] transition cursor-pointer">
                <svg class="w-4 h-4 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
@endsection
