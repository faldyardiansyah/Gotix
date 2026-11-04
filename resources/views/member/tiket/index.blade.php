@extends('layouts.tamu')

@section('title', 'Tiket Saya — Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('member._nav', [
        'judul' => 'E-Tiket Perjalanan Saya',
        'deskripsi' => 'Daftar tiket aktif yang siap digunakan dan riwayat perjalanan Anda di seluruh rute Indonesia.'
    ])

    @if ($tiketList->isEmpty())
        <div class="text-center py-16 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm" data-aos="fade-up">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
            </div>
            <h3 class="font-heading font-bold text-slate-900 text-lg">Belum Ada Tiket Perjalanan</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Anda belum memiliki tiket perjalanan aktif. Silakan cari jadwal keberangkatan travel dan lakukan reservasi.</p>
            <a href="{{ route('jadwal.cari') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl transition shadow-sm">
                Cari Jadwal Travel
            </a>
        </div>
    @else
        <!-- Grid Tiket -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5" data-aos="fade-up" data-aos-duration="500">
            @foreach ($tiketList as $item)
                @php
                    $kode = $item->kode_pesanan;
                    $asal = $item->jadwal?->kotaAsal?->nama_kota ?? 'Kota Asal';
                    $tujuan = $item->jadwal?->kotaTujuan?->nama_kota ?? 'Kota Tujuan';
                    $tgl = $item->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($item->jadwal->tanggal_berangkat)) : '-';
                    $jam = $item->jadwal?->jam_berangkat ? substr($item->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-';
                    $kursi = $item->nomor_kursi;
                    $armada = ($item->jadwal?->kendaraan?->jenis_kendaraan ?? 'Travel') . ' (' . ($item->jadwal?->kendaraan?->nomor_polisi ?? '-') . ')';
                    $statusVal = $item->status?->value ?? (string) $item->status;
                    $statusClass = match ($statusVal) {
                        'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'Dalam Proses' => 'bg-sky-50 text-sky-700 border-sky-200',
                        'Belum Bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                    };
                    $dotClass = match ($statusVal) {
                        'Lunas' => 'bg-emerald-500',
                        'Dalam Proses' => 'bg-sky-500',
                        'Belum Bayar' => 'bg-amber-500',
                        default => 'bg-slate-400',
                    };
                @endphp

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:border-slate-300 transition duration-150 flex flex-col justify-between">
                    <!-- Header Kartu -->
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $kode }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                    {{ $statusVal === 'Lunas' ? 'Tiket Aktif' : ($statusVal === 'Dalam Proses' ? 'Verifikasi Bayar' : $statusVal) }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 font-medium">Kursi #{{ $kursi }}</span>
                        </div>

                        <!-- Rute Antar Kota -->
                        <div class="space-y-3 mb-5">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-sky-50 border border-sky-200/80 flex items-center justify-center text-sky-600 mt-0.5 shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Kota Asal</div>
                                    <div class="text-sm font-bold text-slate-900">{{ $asal }}</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 mt-0.5 shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Kota Tujuan</div>
                                    <div class="text-sm font-bold text-slate-900">{{ $tujuan }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="text-xs text-slate-500 mb-4 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <span class="font-medium text-slate-600">Armada:</span> {{ $armada }}
                        </div>
                    </div>

                    <!-- Footer Kartu -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            <span class="font-semibold text-slate-800">{{ $tgl }}</span> &bull; {{ $jam }}
                        </div>

                        <a href="{{ route('member.tiket.show', $kode) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition duration-150 shadow-xs">
                            <span>Lihat E-Tiket</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
