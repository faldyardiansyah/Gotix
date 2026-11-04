@extends('layouts.tamu')

@section('title', 'Verifikasi Pembayaran Penumpang — Operator Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('operator._nav', [
        'judul' => 'Verifikasi Pembayaran Penumpang',
        'deskripsi' => 'Periksa struk transfer manual yang diunggah penumpang untuk menerbitkan tiket resmi armada travel.'
    ])

    @if (session('sukses'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-medium flex items-center justify-between shadow-xs">
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    @if (session('gagal'))
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl px-5 py-3.5 text-sm font-medium flex items-center justify-between shadow-xs">
            <span>{{ session('gagal') }}</span>
        </div>
    @endif

    <!-- Metric Counter Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6" data-aos="fade-up" data-aos-duration="400">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Perlu Tindakan Verifikasi</div>
            <div class="font-heading text-3xl font-extrabold text-sky-600 mt-1">{{ $daftarPesanan->count() }} Transaksi</div>
            <div class="text-xs text-slate-500 mt-0.5">Harap segera diverifikasi sebelum jadwal armada</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Terverifikasi</div>
            <div class="font-heading text-3xl font-extrabold text-emerald-600 mt-1">{{ $selesaiVerifikasi }} Tiket</div>
            <div class="text-xs text-slate-500 mt-0.5">Semua e-tiket otomatis diterbitkan ke penumpang</div>
        </div>
    </div>

    <!-- Tabel Daftar Pembayaran Real Database -->
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden" data-aos="fade-up" data-aos-duration="500">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Kode / Pemesan</th>
                        <th class="px-6 py-4">Rute & Armada</th>
                        <th class="px-6 py-4">Nominal Tagihan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($daftarPesanan as $pesanan)
                        @php
                            $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
                            $badge = match ($statusVal) {
                                'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                                'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                default => 'bg-rose-50 text-rose-700 border-rose-200',
                            };
                            $dot = match ($statusVal) {
                                'Dalam Proses' => 'bg-sky-500',
                                'Belum Bayar' => 'bg-amber-500',
                                'Lunas' => 'bg-emerald-500',
                                default => 'bg-rose-500',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-mono text-xs font-bold text-slate-900">{{ $pesanan->kode_pesanan }}</div>
                                <div class="font-semibold text-slate-800 text-xs mt-0.5">{{ $pesanan->pengguna?->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $pesanan->pengguna?->telepon ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 text-xs">
                                    {{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} → {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                                    • {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                                    (Kursi #{{ $pesanan->nomor_kursi }})
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-extrabold text-slate-900">
                                    Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                                </div>
                                @if ($pesanan->konfirmasiPembayaran)
                                    <div class="text-[11px] text-emerald-600 font-medium">
                                        a.n {{ $pesanan->konfirmasiPembayaran->nama_pengirim }}
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400">Belum ada struk</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                    {{ $statusVal }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('operator.pembayaran.show', $pesanan->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl transition shadow-xs bg-sky-600 hover:bg-sky-700 text-white">
                                    <span>Periksa Bukti</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                Tidak ada transaksi yang menunggu verifikasi saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
