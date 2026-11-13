@extends('layouts.tamu')

@section('title', 'Verifikasi Struk #' . $pesanan->kode_pesanan . ' — Operator Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        modalBukti: false,
        modalTolak: false,
        previewImageSrc: '{{ $pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path ? route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) : '' }}'
     }">

    @php
        $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
        $badgeClass = match ($statusVal) {
            'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
            'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    @endphp

    <!-- Breadcrumb & Navigasi Kembali -->
    <div class="mb-6 flex items-center justify-between gap-4" data-aos="fade-down" data-aos-duration="400">
        <a href="{{ route('operator.pembayaran.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Verifikasi</span>
        </a>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
            <span class="w-2 h-2 rounded-full {{ $statusVal === 'Lunas' ? 'bg-emerald-500' : ($statusVal === 'Dalam Proses' ? 'bg-sky-500 animate-pulse' : 'bg-amber-500') }}"></span>
            Status: {{ $statusVal }}
        </span>
    </div>

    @if (session('sukses'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    @if (session('gagal'))
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 text-xs font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span>{{ session('gagal') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl p-4 text-xs font-medium space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: Rincian Pesanan & Pembayaran -->
        <div class="lg:col-span-7 space-y-6" data-aos="fade-right" data-aos-duration="500">
            <!-- Header Kartu -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Transaksi</span>
                        <h1 class="font-heading text-2xl font-extrabold text-slate-900 mt-0.5">#{{ $pesanan->kode_pesanan }}</h1>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400">Waktu Pemesanan</div>
                        <div class="text-xs font-bold text-slate-800 font-mono">{{ $pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-' }} WIB</div>
                    </div>
                </div>

                <!-- Info Pemesan -->
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nama Penumpang</div>
                        <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $pesanan->pengguna?->nama ?? '-' }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $pesanan->pengguna?->telepon ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nomor Kursi</div>
                        <div class="text-sm font-bold text-sky-600 mt-0.5">Kursi #{{ $pesanan->nomor_kursi }}</div>
                        <div class="text-xs text-slate-500">{{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? '-' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})</div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Batas Pembayaran:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $pesanan->batas_bayar ? $pesanan->batas_bayar->format('d M Y, H:i') : '-' }} WIB</span>
                </div>
            </div>

            <!-- Rute & Jadwal -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-heading font-bold text-slate-900 text-sm">Informasi Rute Perjalanan</h2>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kota Asal</span>
                        <span class="font-bold text-slate-900">{{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kota Tujuan</span>
                        <span class="font-bold text-slate-900">{{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200">
                        <span class="text-slate-500">Tanggal Keberangkatan</span>
                        <span class="font-bold text-slate-900">
                            {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                            &bull; {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Perbandingan Tagihan vs Data Transfer -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-heading font-bold text-slate-900 text-sm">Pencocokan Nominal & Data Transfer Terakhir</h2>
                <div class="border border-slate-200 rounded-2xl p-4 space-y-2.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Tarif Tagihan Resmi</span>
                        <span class="font-mono font-bold text-slate-900 text-sm">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Rekening / Pengirim</span>
                        <span class="font-medium text-slate-800">{{ $pesanan->konfirmasiPembayaran?->rekening_pengirim ?? 'Belum ada konfirmasi' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Nomor Resi / Referensi</span>
                        <span class="font-mono font-bold text-slate-800">{{ $pesanan->konfirmasiPembayaran?->nomor_resi ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Waktu Transfer</span>
                        <span class="text-slate-800 font-medium">
                            {{ $pesanan->konfirmasiPembayaran?->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }}
                            {{ $pesanan->konfirmasiPembayaran?->jam_transfer ? substr($pesanan->konfirmasiPembayaran->jam_transfer, 0, 5) . ' WIB' : '' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Riwayat Unggahan Bukti (P0-04 Audit Retention) -->
            @if ($pesanan->riwayatPembayaran && $pesanan->riwayatPembayaran->count() > 0)
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="font-heading font-bold text-slate-900 text-sm">Riwayat Unggahan & Verifikasi (Audit Trail)</h2>
                    <div class="space-y-3">
                        @foreach ($pesanan->riwayatPembayaran as $index => $riwayat)
                            <div class="p-3.5 border border-slate-200 rounded-2xl text-xs space-y-2 {{ $riwayat->status === 'Ditolak' ? 'bg-rose-50/50 border-rose-200' : ($riwayat->status === 'Diterima' ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50') }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">Unggahan ke-{{ $pesanan->riwayatPembayaran->count() - $index }} (Resi: <span class="font-mono">{{ $riwayat->nomor_resi }}</span>)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $riwayat->status === 'Diterima' ? 'bg-emerald-100 text-emerald-800' : ($riwayat->status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800') }}">
                                        {{ $riwayat->status }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    Waktu Unggah: {{ $riwayat->created_at ? $riwayat->created_at->format('d M Y, H:i') : '-' }} WIB &bull; Rekening: {{ $riwayat->rekening_pengirim }}
                                </div>
                                @if ($riwayat->diverifikasi_oleh)
                                    <div class="text-[11px] text-slate-600 pt-1 border-t border-slate-200/60">
                                        Diverifikasi oleh: <strong>{{ $riwayat->verifikator?->nama ?? 'Petugas' }}</strong> pada {{ $riwayat->diverifikasi_pada ? $riwayat->diverifikasi_pada->format('d M Y, H:i') : '-' }} WIB
                                    </div>
                                @endif
                                @if ($riwayat->alasan_penolakan)
                                    <div class="text-[11px] text-rose-700 bg-rose-100/70 p-2 rounded-xl">
                                        <strong>Alasan Penolakan:</strong> {{ $riwayat->alasan_penolakan }}
                                    </div>
                                @endif
                                <div class="pt-1">
                                    <button type="button"
                                            @click="previewImageSrc = '{{ route('pembayaran.bukti.riwayat', $riwayat->id) }}'; modalBukti = true"
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-sky-600 hover:text-sky-800">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                        </svg>
                                        <span>Lihat Bukti Ini</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Pratinjau Struk & Tombol Aksi -->
        <div class="lg:col-span-5 space-y-6" data-aos="fade-left" data-aos-duration="500">
            <!-- Box Pratinjau Bukti Transfer -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-heading font-bold text-slate-900 text-sm">Berkas Bukti Transfer Aktif</h2>
                    <span class="text-[11px] text-slate-400">Klik untuk perbesar</span>
                </div>

                @if ($pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path)
                    <div @click="previewImageSrc = '{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}'; modalBukti = true"
                         class="relative border border-slate-200 rounded-2xl overflow-hidden bg-slate-100 group cursor-pointer shadow-xs aspect-[3/4] flex items-center justify-center">
                        <img src="{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}"
                             alt="Bukti Transfer Penumpang" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/80 backdrop-blur-sm text-xs font-semibold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                </svg>
                                <span>Perbesar Struk</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-8 border border-slate-200 rounded-2xl bg-slate-50 text-center space-y-2">
                        <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <p class="text-xs font-semibold text-slate-600">Belum ada struk transfer yang diunggah</p>
                        <p class="text-[11px] text-slate-400">Penumpang belum mengunggah atau sedang merevisi bukti bayar.</p>
                    </div>
                @endif
            </div>

            <!-- Tombol Aksi Verifikasi / Tolak (State Guarded) -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-3">
                @if ($statusVal === 'Dalam Proses')
                    <form action="{{ route('operator.pembayaran.verifikasi', $pesanan->id) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin memvalidasi pembayaran ini dan menerbitkan E-Tiket resmi?')">
                        @csrf
                        <button type="submit"
                                class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm shadow-emerald-600/20 flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Terima & Terbitkan Tiket</span>
                        </button>
                    </form>

                    <button type="button"
                            @click="modalTolak = true"
                            class="w-full py-3 px-4 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 border border-rose-200 font-bold text-xs uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                        <span>Tolak Pembayaran...</span>
                    </button>
                @elseif ($statusVal === 'Lunas')
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-1">
                        <div class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Pembayaran Telah Lunas</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">
                            Diverifikasi oleh: <strong>{{ $pesanan->konfirmasiPembayaran?->verifikator?->nama ?? 'Petugas' }}</strong>
                            <br>pada {{ $pesanan->konfirmasiPembayaran?->diverifikasi_pada ? $pesanan->konfirmasiPembayaran->diverifikasi_pada->format('d M Y, H:i') : '-' }} WIB
                        </p>
                    </div>
                @elseif ($statusVal === 'Belum Bayar')
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 space-y-2">
                        <div class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Menunggu Pembayaran Penumpang</span>
                        </div>
                        @if ($pesanan->konfirmasiPembayaran?->alasan_penolakan)
                            <div class="text-[11px] text-rose-700 bg-white/80 border border-rose-200 rounded-xl p-2.5 space-y-1">
                                <div class="font-bold">Penolakan Terakhir:</div>
                                <div>{{ $pesanan->konfirmasiPembayaran->alasan_penolakan }}</div>
                                <div class="text-[10px] text-slate-500 pt-1">
                                    Oleh: {{ $pesanan->konfirmasiPembayaran->verifikator?->nama ?? 'Petugas' }} &bull; {{ $pesanan->konfirmasiPembayaran->diverifikasi_pada ? $pesanan->konfirmasiPembayaran->diverifikasi_pada->format('d M Y, H:i') : '-' }} WIB
                                </div>
                            </div>
                        @endif
                        <p class="text-[11px] text-amber-700">Penumpang dapat mengunggah bukti perbaikan sebelum batas bayar.</p>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-center text-xs font-semibold text-rose-800">
                        Pesanan telah berstatus DIBATALKAN.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Form Tolak Pembayaran dengan Alasan Wajib -->
    <div x-show="modalTolak"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;">
        <div @click.away="modalTolak = false"
             class="bg-white rounded-3xl border border-slate-200 p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-heading font-bold text-slate-900 text-base">Alasan Penolakan Pembayaran</h3>
                <button type="button" @click="modalTolak = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('operator.pembayaran.tolak', $pesanan->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="alasan_penolakan" class="block text-xs font-semibold text-slate-700 mb-1">
                        Jelaskan alasan penolakan kepada penumpang: <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="alasan_penolakan"
                              name="alasan_penolakan"
                              rows="4"
                              required
                              placeholder="Contoh: Nominal transfer kurang dari tagihan, struk buram tidak terbaca, atau nomor rekening tujuan salah."
                              class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Pesanan akan dikembalikan ke status <strong>Belum Bayar</strong> agar penumpang dapat mengunggah bukti yang benar sebelum batas pembayaran.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTolak = false"
                            class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm shadow-rose-600/20">
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pratinjau Gambar Struk (Zoom In) -->
    <div x-show="modalBukti"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs cursor-zoom-out"
         @click="modalBukti = false"
         style="display: none;">
        <div class="relative max-w-3xl max-h-[90vh] bg-transparent" @click.stop>
            <img :src="previewImageSrc"
                 alt="Bukti Transfer Penumpang"
                 class="max-h-[85vh] w-auto mx-auto rounded-2xl shadow-2xl border-2 border-white/20 object-contain">
            <button @click="modalBukti = false"
                    class="absolute -top-3 -right-3 p-2 bg-slate-900/80 hover:bg-slate-900 text-white rounded-full border border-white/20 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
@endsection
