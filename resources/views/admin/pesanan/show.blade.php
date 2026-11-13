@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $pesanan->kode_pesanan)

@section('konten')
<div class="space-y-6" x-data="{ modalTolak: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pesanan.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="font-heading font-bold text-xl sm:text-2xl text-slate-900">
                        Pesanan #{{ $pesanan->kode_pesanan }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Dibuat pada {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y H:i') : '-' }} WIB
                    </p>
                </div>
            </div>
        </div>

        @php
            $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
            $badgeColor = match ($statusVal) {
                'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                default => 'bg-rose-50 text-rose-700 border-rose-200',
            };
        @endphp

        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $badgeColor }} self-start sm:self-auto">
            <span class="w-2 h-2 rounded-full {{ $statusVal === 'Lunas' ? 'bg-emerald-500' : ($statusVal === 'Dalam Proses' ? 'bg-sky-500 animate-pulse' : 'bg-amber-500') }}"></span>
            Status: {{ $statusVal }}
        </span>
    </div>

    @if (session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-medium">
            {{ session('sukses') }}
        </div>
    @endif

    @if (session('gagal'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 text-xs font-medium">
            {{ session('gagal') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl p-4 text-xs font-medium space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rincian Data Perjalanan & Penumpang -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h2 class="font-heading font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">
                Data Perjalanan & Penumpang
            </h2>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Kode Booking</span>
                    <span class="font-mono font-bold text-slate-900">{{ $pesanan->kode_pesanan }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nama Pemesan</span>
                    <a href="{{ route('admin.pengguna.show', $pesanan->pengguna_id) }}" class="font-semibold text-sky-600 hover:underline">
                        {{ $pesanan->pengguna?->nama ?? '-' }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">No. Telepon</span>
                    <span class="font-mono text-slate-800">{{ $pesanan->pengguna?->telepon ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Rute Perjalanan</span>
                    <span class="font-semibold text-slate-900">
                        {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} → {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Armada</span>
                    <span class="text-slate-800">{{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? '-' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Waktu Berangkat</span>
                    <span class="text-slate-800">
                        {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                        pukul {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nomor Kursi</span>
                    <span class="font-bold text-sky-600">Kursi #{{ $pesanan->nomor_kursi }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Batas Pembayaran</span>
                    <span class="font-mono font-bold text-slate-800">{{ $pesanan->batas_bayar ? $pesanan->batas_bayar->format('d M Y H:i') : '-' }} WIB</span>
                </div>
                <div class="flex justify-between pt-3 border-t border-slate-100 text-sm">
                    <span class="font-bold text-slate-700">Total Tarif</span>
                    <span class="font-extrabold text-sky-600">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Histori Riwayat Pembayaran (Audit Trail) -->
            @if ($pesanan->riwayatPembayaran && $pesanan->riwayatPembayaran->count() > 0)
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <h3 class="font-heading font-bold text-slate-900 text-xs">Riwayat Unggahan & Verifikasi:</h3>
                    <div class="space-y-2">
                        @foreach ($pesanan->riwayatPembayaran as $index => $riwayat)
                            <div class="p-3 border rounded-xl text-xs space-y-1.5 {{ $riwayat->status === 'Ditolak' ? 'bg-rose-50/50 border-rose-200' : ($riwayat->status === 'Diterima' ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200') }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">Unggahan #{{ $pesanan->riwayatPembayaran->count() - $index }} (Resi: {{ $riwayat->nomor_resi }})</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $riwayat->status === 'Diterima' ? 'bg-emerald-100 text-emerald-800' : ($riwayat->status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800') }}">
                                        {{ $riwayat->status }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $riwayat->created_at ? $riwayat->created_at->format('d M Y H:i') : '-' }} WIB &bull; Rek: {{ $riwayat->rekening_pengirim }}
                                </div>
                                @if ($riwayat->diverifikasi_oleh)
                                    <div class="text-[11px] text-slate-600">
                                        Petugas: <strong>{{ $riwayat->verifikator?->nama ?? 'Petugas' }}</strong> ({{ $riwayat->diverifikasi_pada ? $riwayat->diverifikasi_pada->format('d M Y H:i') : '-' }} WIB)
                                    </div>
                                @endif
                                @if ($riwayat->alasan_penolakan)
                                    <div class="text-[11px] text-rose-700 bg-rose-100/60 p-2 rounded-lg">
                                        <strong>Alasan:</strong> {{ $riwayat->alasan_penolakan }}
                                    </div>
                                @endif
                                <div class="pt-1">
                                    <a href="{{ route('pembayaran.bukti.riwayat', $riwayat->id) }}" target="_blank"
                                       class="text-[11px] font-semibold text-sky-600 hover:underline">
                                        Lihat File Bukti ↗
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Bukti Pembayaran & Aksi Verifikasi / Tolak -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between space-y-4">
            <div>
                <h2 class="font-heading font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">
                    Bukti Pembayaran & Status
                </h2>

                @if ($pesanan->konfirmasiPembayaran)
                    <div class="mt-4 space-y-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Rekening Pengirim</span>
                            <span class="font-semibold text-slate-900">{{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nomor Resi / Referensi</span>
                            <span class="font-mono font-bold text-slate-800">{{ $pesanan->konfirmasiPembayaran->nomor_resi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Waktu Transfer</span>
                            <span class="text-slate-700 font-medium">
                                {{ $pesanan->konfirmasiPembayaran->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }}
                                {{ $pesanan->konfirmasiPembayaran->jam_transfer ? substr($pesanan->konfirmasiPembayaran->jam_transfer, 0, 5) . ' WIB' : '' }}
                            </span>
                        </div>

                        <div class="pt-2">
                            <span class="font-semibold text-slate-700 block mb-2">Foto Struk Bukti Transfer Terakhir:</span>
                            @if ($pesanan->konfirmasiPembayaran->bukti_path)
                                <a href="{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}" target="_blank"
                                   class="block border border-slate-200 rounded-xl overflow-hidden hover:opacity-90 transition">
                                    <img src="{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}"
                                         alt="Bukti Transfer" class="w-full max-h-52 object-cover bg-slate-50">
                                    <span class="block text-center text-[11px] text-sky-600 py-1.5 bg-slate-50 border-t border-slate-100 font-medium">
                                        Perbesar bukti transfer ↗
                                    </span>
                                </a>
                            @else
                                <div class="p-4 bg-slate-50 rounded-xl text-center text-xs text-slate-400">File bukti tidak ditemukan.</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-5 bg-slate-50 border border-slate-200 rounded-xl text-center text-xs text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700">Belum ada bukti pembayaran diunggah.</p>
                        <p class="text-slate-400">Member belum melakukan konfirmasi transfer manual.</p>
                    </div>
                @endif
            </div>

            <!-- Tombol Aksi Admin: Verifikasi / Tolak (State Guarded) -->
            <div class="pt-4 border-t border-slate-100">
                @if ($statusVal === 'Dalam Proses')
                    <div class="flex items-center gap-3">
                        <form action="{{ route('admin.pesanan.verifikasi', $pesanan->id) }}" method="POST" class="flex-1"
                              onsubmit="return confirm('Verifikasi pesanan ini menjadi Lunas?')">
                            @csrf
                            <button type="submit"
                                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs text-center cursor-pointer">
                                Terima (Lunas)
                            </button>
                        </form>

                        <button type="button"
                                @click="modalTolak = true"
                                class="flex-1 py-2.5 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-xl transition text-center cursor-pointer">
                            Tolak Pesanan...
                        </button>
                    </div>
                @elseif ($statusVal === 'Lunas')
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center text-xs text-emerald-800 space-y-0.5">
                        <div class="font-bold">Pesanan LUNAS</div>
                        <div class="text-[11px]">
                            Diverifikasi oleh: <strong>{{ $pesanan->konfirmasiPembayaran?->verifikator?->nama ?? 'Petugas' }}</strong>
                            pada {{ $pesanan->konfirmasiPembayaran?->diverifikasi_pada ? $pesanan->konfirmasiPembayaran->diverifikasi_pada->format('d M Y H:i') : '-' }} WIB
                        </div>
                    </div>
                @elseif ($statusVal === 'Belum Bayar')
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 space-y-1">
                        <div class="font-bold">Menunggu Pembayaran Penumpang</div>
                        @if ($pesanan->konfirmasiPembayaran?->alasan_penolakan)
                            <div class="text-[11px] text-rose-700 bg-white/70 p-2 rounded-lg">
                                <strong>Penolakan Terakhir:</strong> {{ $pesanan->konfirmasiPembayaran->alasan_penolakan }}
                                <div class="text-[10px] text-slate-500 pt-0.5">
                                    Oleh: {{ $pesanan->konfirmasiPembayaran->verifikator?->nama ?? 'Petugas' }} ({{ $pesanan->konfirmasiPembayaran->diverifikasi_pada ? $pesanan->konfirmasiPembayaran->diverifikasi_pada->format('d M Y H:i') : '-' }} WIB)
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-center text-xs font-semibold text-rose-700">
                        Pesanan ini telah DIBATALKAN.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Form Tolak Pembayaran Admin -->
    <div x-show="modalTolak"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;">
        <div @click.away="modalTolak = false"
             class="bg-white rounded-2xl border border-slate-200 p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-heading font-bold text-slate-900 text-sm">Alasan Penolakan Pembayaran</h3>
                <button type="button" @click="modalTolak = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pesanan.tolak', $pesanan->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="admin_alasan_penolakan" class="block text-xs font-semibold text-slate-700 mb-1">
                        Jelaskan alasan penolakan: <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="admin_alasan_penolakan"
                              name="alasan_penolakan"
                              rows="4"
                              required
                              placeholder="Contoh: Bukti transfer tidak valid atau nominal tidak sesuai."
                              class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Status pesanan akan dikembalikan ke <strong>Belum Bayar</strong> agar penumpang dapat mengunggah bukti yang benar.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTolak = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-xs">
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
