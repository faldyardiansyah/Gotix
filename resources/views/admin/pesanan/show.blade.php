@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $pesanan->kode_pesanan . ' — GoTix.')
@section('page-title', 'Detail Pesanan #' . $pesanan->kode_pesanan)
@section('page-subtitle', 'Informasi lengkap reservasi tiket, bukti transfer manual, dan status pembayaran.')

@section('konten')
<div class="space-y-6" x-data="{ modalTolak: false }">
    <!-- Header Aksi & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pesanan.index') }}"
               class="p-2 bg-white text-slate-900 border-2 border-slate-900 rounded-xl hover:bg-amber-100 shadow-[2px_2px_0px_#18181b] transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="font-heading font-black text-xl sm:text-2xl text-slate-950">
                    Pesanan #{{ $pesanan->kode_pesanan }}
                </h1>
                <p class="text-xs text-slate-600 font-semibold mt-0.5">
                    Dibuat pada {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y H:i') : '-' }} WIB
                </p>
            </div>
        </div>

        <div>
            <x-badge-status :status="$pesanan->status" size="md" />
        </div>
    </div>

    @php
        $statusVal = $pesanan->status instanceof \App\Enums\StatusPesanan ? $pesanan->status->value : (string) $pesanan->status;
        $isWalkIn = ($pesanan->pengguna?->peran?->value ?? '') === 'Operator';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Kolom Kiri: Rincian Perjalanan & Penumpang -->
        <div class="space-y-6">
            <x-card title="Data Perjalanan & Penumpang" subtitle="Informasi rute, armada, dan pemesan tiket" variant="white">
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Kode Booking</span>
                        <span class="font-mono font-black text-slate-950 text-sm">{{ $pesanan->kode_pesanan }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Nama Pemesan</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.pengguna.show', $pesanan->pengguna_id) }}" class="font-bold text-amber-700 hover:text-amber-900 underline">
                                {{ $pesanan->pengguna?->nama ?? '-' }}
                            </a>
                            @if ($isWalkIn)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-heading font-black bg-stone-200 border border-slate-900">Loket</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-heading font-black bg-amber-100 border border-slate-900">Member</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">No. Telepon / WhatsApp</span>
                        <span class="font-mono font-bold text-slate-950">{{ $pesanan->pengguna?->telepon ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Rute Perjalanan</span>
                        <span class="font-black text-slate-950 text-sm">
                            {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }}
                            <span class="text-amber-600 px-1">&rarr;</span>
                            {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Armada Kendaraan</span>
                        <span class="font-bold text-slate-950">{{ $pesanan->jadwal?->kendaraan?->jenis_kendaraan ?? '-' }} ({{ $pesanan->jadwal?->kendaraan?->nomor_polisi ?? '-' }})</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Waktu Keberangkatan</span>
                        <span class="font-bold text-slate-950">
                            {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }} &bull;
                            {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50 border-2 border-slate-900 shadow-[1.5px_1.5px_0px_#18181b]">
                        <span class="text-amber-950 font-heading font-black uppercase text-[11px]">Nomor Kursi</span>
                        <span class="font-heading font-black text-amber-950 text-base">Kursi #{{ $pesanan->nomor_kursi }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Batas Waktu Bayar</span>
                        <span class="font-mono font-bold text-slate-800">{{ $pesanan->batas_bayar ? $pesanan->batas_bayar->format('d M Y H:i') : '-' }} WIB</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-stone-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] mt-2">
                        <span class="font-heading font-black text-slate-950 uppercase text-xs">Total Pembayaran</span>
                        <span class="font-heading font-black text-slate-950 text-lg">Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Histori Riwayat Pembayaran (Audit Trail) -->
                @if ($pesanan->riwayatPembayaran && $pesanan->riwayatPembayaran->count() > 0)
                    <div class="mt-6 pt-5 border-t-2 border-slate-900 space-y-3">
                        <h3 class="font-heading font-black text-slate-950 text-xs uppercase tracking-wider">
                            Riwayat Unggahan & Verifikasi ({{ $pesanan->riwayatPembayaran->count() }})
                        </h3>
                        <div class="space-y-3">
                            @foreach ($pesanan->riwayatPembayaran as $index => $riwayat)
                                <div class="p-3.5 border-2 border-slate-900 rounded-xl text-xs space-y-2 {{ $riwayat->status === 'Ditolak' ? 'bg-rose-50 shadow-[2px_2px_0px_#e11d48]' : ($riwayat->status === 'Diterima' ? 'bg-emerald-50 shadow-[2px_2px_0px_#059669]' : 'bg-white shadow-[2px_2px_0px_#18181b]') }}">
                                    <div class="flex items-center justify-between">
                                        <span class="font-heading font-black text-slate-900">Unggahan #{{ $pesanan->riwayatPembayaran->count() - $index }} (Resi: {{ $riwayat->nomor_resi }})</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-heading font-black border border-slate-900 {{ $riwayat->status === 'Diterima' ? 'bg-emerald-200 text-emerald-950' : ($riwayat->status === 'Ditolak' ? 'bg-rose-200 text-rose-950' : 'bg-amber-200 text-amber-950') }}">
                                            {{ $riwayat->status }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 font-semibold">
                                        {{ $riwayat->created_at ? $riwayat->created_at->format('d M Y H:i') : '-' }} WIB &bull; Rek: {{ $riwayat->rekening_pengirim }}
                                    </div>
                                    @if ($riwayat->diverifikasi_oleh)
                                        <div class="text-[11px] text-slate-800 font-medium">
                                            Petugas: <strong>{{ $riwayat->verifikator?->nama ?? 'Petugas' }}</strong> ({{ $riwayat->diverifikasi_pada ? $riwayat->diverifikasi_pada->format('d M Y H:i') : '-' }} WIB)
                                        </div>
                                    @endif
                                    @if ($riwayat->alasan_penolakan)
                                        <div class="text-[11px] text-rose-900 bg-rose-100 border border-rose-300 p-2 rounded-lg font-semibold">
                                            <strong>Alasan Penolakan:</strong> {{ $riwayat->alasan_penolakan }}
                                        </div>
                                    @endif
                                    <div class="pt-1">
                                        <a href="{{ route('pembayaran.bukti.riwayat', $riwayat->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-[11px] font-heading font-extrabold text-amber-700 hover:text-amber-900 underline">
                                            <span>Lihat File Struk ↗</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Kolom Kanan: Bukti Transfer & Verifikasi -->
        <div class="space-y-6">
            <x-card title="Bukti Transfer & Tindakan" subtitle="Tinjau bukti pembayaran manual yang diunggah" variant="white">
                @if ($pesanan->konfirmasiPembayaran)
                    <div class="space-y-4 text-xs">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                            <span class="text-slate-500 font-bold uppercase text-[10px]">Rekening Pengirim</span>
                            <span class="font-bold text-slate-950">{{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                            <span class="text-slate-500 font-bold uppercase text-[10px]">Nomor Resi / Referensi</span>
                            <span class="font-mono font-black text-slate-950">{{ $pesanan->konfirmasiPembayaran->nomor_resi }}</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                            <span class="text-slate-500 font-bold uppercase text-[10px]">Waktu Transfer</span>
                            <span class="font-bold text-slate-950">
                                {{ $pesanan->konfirmasiPembayaran->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }}
                                {{ $pesanan->konfirmasiPembayaran->jam_transfer ? substr($pesanan->konfirmasiPembayaran->jam_transfer, 0, 5) . ' WIB' : '' }}
                            </span>
                        </div>

                        <div>
                            <span class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-2">
                                Foto Struk Bukti Transfer:
                            </span>
                            @if ($pesanan->konfirmasiPembayaran->bukti_path)
                                <a href="{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}" target="_blank"
                                   class="block border-2 border-slate-900 rounded-2xl overflow-hidden hover:opacity-95 shadow-[3px_3px_0px_#18181b] transition">
                                    <img src="{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}"
                                         alt="Bukti Transfer" class="w-full max-h-64 object-cover bg-stone-100">
                                    <span class="block text-center text-xs font-heading font-black text-slate-950 py-2 bg-amber-400 border-t-2 border-slate-900">
                                        Klik untuk Perbesar Bukti Transfer ↗
                                    </span>
                                </a>
                            @else
                                <div class="p-6 bg-stone-50 border-2 border-slate-900 rounded-2xl text-center text-xs font-bold text-slate-500">
                                    File bukti tidak ditemukan di penyimpanan server.
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-6 bg-amber-50 border-2 border-slate-900 rounded-2xl text-center text-xs text-amber-950 space-y-1 shadow-[2px_2px_0px_#18181b]">
                        <p class="font-heading font-black text-sm">Belum Ada Bukti Pembayaran</p>
                        <p class="font-medium text-slate-600">Penumpang belum mengunggah bukti transfer manual untuk pesanan ini.</p>
                    </div>
                @endif

                <!-- Tombol Aksi Verifikasi / Tolak -->
                <div class="mt-6 pt-5 border-t-2 border-slate-900">
                    @if ($statusVal === 'Dalam Proses')
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <form action="{{ route('admin.pesanan.verifikasi', $pesanan->id) }}" method="POST" class="w-full sm:flex-1"
                                  onsubmit="return confirm('Verifikasi pembayaran ini dan ubah status pesanan menjadi Lunas?')">
                                @csrf
                                <x-button type="submit" variant="primary" size="md" class="w-full">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Terima & Set Lunas</span>
                                </x-button>
                            </form>

                            <x-button type="button" @click="modalTolak = true" variant="danger" size="md" class="w-full sm:flex-1">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Tolak Pembayaran...</span>
                            </x-button>
                        </div>
                    @elseif ($statusVal === 'Lunas')
                        <div class="p-4 bg-emerald-100 border-2 border-slate-900 rounded-2xl text-center text-xs text-emerald-950 space-y-1 shadow-[3px_3px_0px_#18181b]">
                            <div class="font-heading font-black text-sm">Pesanan Telah LUNAS</div>
                            <div class="font-semibold text-slate-700">
                                Diverifikasi oleh: <strong>{{ $pesanan->konfirmasiPembayaran?->verifikator?->nama ?? 'Petugas' }}</strong>
                                pada {{ $pesanan->konfirmasiPembayaran?->diverifikasi_pada ? $pesanan->konfirmasiPembayaran->diverifikasi_pada->format('d M Y H:i') : '-' }} WIB
                            </div>
                        </div>
                    @elseif ($statusVal === 'Belum Bayar')
                        <div class="p-4 bg-amber-100 border-2 border-slate-900 rounded-2xl text-xs text-amber-950 space-y-1 shadow-[2px_2px_0px_#18181b]">
                            <div class="font-heading font-black text-sm">Menunggu Pembayaran Penumpang</div>
                            <p class="font-medium text-slate-700">Penumpang masih dalam tenggat waktu pembayaran sebelum pesanan otomatis kedaluwarsa.</p>
                            @if ($pesanan->konfirmasiPembayaran?->alasan_penolakan)
                                <div class="text-[11px] text-rose-900 bg-white border border-rose-300 p-2 rounded-xl mt-2 font-bold">
                                    <strong>Penolakan Sebelumnya:</strong> {{ $pesanan->konfirmasiPembayaran->alasan_penolakan }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 bg-rose-100 border-2 border-slate-900 rounded-2xl text-center text-xs font-heading font-black text-rose-950 shadow-[2px_2px_0px_#18181b]">
                            Pesanan ini telah DIBATALKAN / KEDALUWARSA.
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>

    <!-- Modal Form Tolak Pembayaran Admin -->
    <div x-show="modalTolak"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
         style="display: none;">
        <div @click.away="modalTolak = false"
             class="bg-white rounded-2xl border-2 border-slate-900 p-6 max-w-md w-full shadow-[6px_6px_0px_#18181b] space-y-4">
            <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900">
                <h3 class="font-heading font-black text-slate-950 text-base">Alasan Penolakan Pembayaran</h3>
                <button type="button" @click="modalTolak = false" class="p-1 rounded-lg text-slate-500 hover:text-slate-950">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pesanan.tolak', $pesanan->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="admin_alasan_penolakan" class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                        Jelaskan alasan penolakan: <span class="text-rose-600">*</span>
                    </label>
                    <textarea id="admin_alasan_penolakan"
                              name="alasan_penolakan"
                              rows="4"
                              required
                              placeholder="Contoh: Bukti transfer buram, mutasi belum masuk, atau nominal transfer tidak sesuai tiket."
                              class="w-full rounded-xl border-2 border-slate-900 bg-white p-3 text-xs font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b]"></textarea>
                    <p class="text-[11px] text-slate-500 font-semibold mt-1.5">
                        Status pesanan akan dikembalikan ke <strong>Belum Bayar</strong> agar penumpang dapat mengunggah bukti yang benar.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <x-button type="button" @click="modalTolak = false" variant="secondary" size="sm">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="danger" size="sm">
                        Kirim Penolakan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
