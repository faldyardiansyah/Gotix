@extends('layouts.tamu')

@section('title', 'Layanan Keluhan Penumpang — Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('member._nav', [
        'judul' => 'Pusat Bantuan & Keluhan',
        'deskripsi' => 'Sampaikan kritik, kendala perjalanan, atau pertanyaan seputar reservasi tiket Anda.'
    ])

    <!-- Aksi Buat Keluhan Baru -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-aos="fade-down" data-aos-duration="400">
        <div>
            <h2 class="font-heading text-lg font-bold text-slate-900">Riwayat Pengaduan Anda</h2>
            <p class="text-xs text-slate-500">Tim operasional kami akan merespons pengaduan maksimal dalam 1x24 jam kerja.</p>
        </div>

        <a href="{{ route('member.keluhan.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Ajukan Keluhan Baru</span>
        </a>
    </div>

    @php
        $daftarKeluhan = [
            [
                'id' => 1,
                'tiket' => 'GTX-JKTBDG',
                'kategori' => 'Pelayanan',
                'tanggal' => date('d M Y', strtotime('-2 days')),
                'deskripsi' => 'Sopir tiba di titik penjemputan Pool Gambir terlambat 15 menit dari jadwal awal.',
                'status' => 'Dibalas',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
                'balasan' => 'Mohon maaf atas ketidaknyamanan Anda. Keterlambatan terjadi akibat penutupan jalur protokol oleh aparat. Kami telah menegur kru terkait untuk mitigasi rute berikutnya.',
                'dibalas_pada' => date('d M Y, H:i', strtotime('-1 day')),
                'petugas' => 'CS Gotix Pusat',
            ],
            [
                'id' => 2,
                'tiket' => 'GTX-321550',
                'kategori' => 'Pembayaran',
                'tanggal' => date('d M Y'),
                'deskripsi' => 'Saya sudah mentransfer nominal tiket via BCA namun status pesanan masih bertuliskan Belum Bayar setelah 10 menit.',
                'status' => 'Pending',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500',
                'balasan' => null,
                'dibalas_pada' => null,
                'petugas' => null,
            ],
        ];
    @endphp

    <div class="space-y-4" data-aos="fade-up" data-aos-duration="500">
        @forelse ($daftarKeluhan as $keluhan)
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800">
                            {{ $keluhan['tiket'] }}
                        </span>
                        <span class="text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-full">
                            Kategori: {{ $keluhan['kategori'] }}
                        </span>
                        <span class="text-xs text-slate-400">{{ $keluhan['tanggal'] }}</span>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $keluhan['badge'] }} self-start sm:self-auto">
                        <span class="w-1.5 h-1.5 rounded-full {{ $keluhan['dot'] }}"></span>
                        {{ $keluhan['status'] }}
                    </span>
                </div>

                <!-- Isi Keluhan -->
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Rincian Keluhan Penumpang</div>
                    <p class="text-sm text-slate-800 leading-relaxed">
                        {{ $keluhan['deskripsi'] }}
                    </p>
                </div>

                <!-- Tanggapan Admin / CS jika ada -->
                @if ($keluhan['balasan'])
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1.5">
                        <div class="flex items-center justify-between text-slate-500">
                            <span class="font-bold text-slate-900 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                                Tanggapan {{ $keluhan['petugas'] }}
                            </span>
                            <span>{{ $keluhan['dibalas_pada'] }}</span>
                        </div>
                        <p class="text-slate-700 leading-relaxed">
                            {{ $keluhan['balasan'] }}
                        </p>
                    </div>
                @else
                    <div class="text-xs text-slate-400 italic">
                        Menunggu tinjauan tim operasional Gotix...
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-16 bg-white border border-slate-200 rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-slate-900 text-lg">Tidak Ada Keluhan Aktif</h3>
                <p class="text-sm text-slate-500 mt-1">Layanan perjalanan Anda berjalan lancar tanpa ada laporan kendala.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
