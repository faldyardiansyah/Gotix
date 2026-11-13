@extends('layouts.tamu')

@section('title', 'Layanan Keluhan Penumpang — Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('member._nav', [
        'judul' => 'Pusat Bantuan & Keluhan',
        'deskripsi' => 'Sampaikan kritik, kendala perjalanan, atau pertanyaan seputar reservasi tiket Anda.'
    ])

    <!-- Aksi Buat Keluhan Baru -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-heading font-black text-lg text-slate-900">Riwayat Pengaduan Anda</h2>
            <p class="text-xs text-slate-600 font-medium">Tim operasional kami akan merespons pengaduan maksimal dalam 1x24 jam kerja.</p>
        </div>

        <x-button href="{{ route('member.keluhan.create') }}" variant="primary" size="sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Ajukan Keluhan Baru</span>
        </x-button>
    </div>

    @if (session('sukses'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-100 border-2 border-slate-900 text-xs font-bold text-emerald-950 flex items-center gap-2 shadow-[3px_3px_0px_#18181b]">
            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($daftarKeluhan as $keluhan)
            @php
                $isDibalas = !empty($keluhan->balasan);
                $statusLabel = $isDibalas ? 'Sudah Dibalas' : 'Menunggu Tanggapan';
                $tiketKode = $keluhan->pesanan?->kode_pesanan;
                $kategoriLabel = $keluhan->kategori instanceof \App\Enums\KategoriKeluhan ? $keluhan->kategori->value : (string) $keluhan->kategori;
                $tglFormatted = $keluhan->created_at ? $keluhan->created_at->translatedFormat('d M Y, H:i') : '-';
                $dibalasPada = $keluhan->dibalas_pada ? \Carbon\Carbon::parse($keluhan->dibalas_pada)->translatedFormat('d M Y, H:i') : '-';
                $petugasNama = $keluhan->petugasBalas?->nama ?? 'Admin Gotix';
            @endphp

            <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[4px_4px_0px_#18181b] space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b-2 border-slate-900">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        @if ($tiketKode)
                            <span class="font-mono text-xs font-black px-2.5 py-0.5 rounded bg-stone-100 border-1.5 border-slate-900 text-slate-900 shadow-[1px_1px_0px_#18181b]">
                                {{ $tiketKode }}
                            </span>
                        @endif
                        <span class="text-xs font-heading font-extrabold text-slate-950 bg-amber-200 border-1.5 border-slate-900 px-3 py-0.5 rounded-full shadow-[1px_1px_0px_#18181b]">
                            {{ $kategoriLabel }}
                        </span>
                        <span class="text-xs text-slate-500 font-medium">{{ $tglFormatted }} WIB</span>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-heading font-black border-1.5 border-slate-900 self-start sm:self-auto shadow-[1px_1px_0px_#18181b] {{ $isDibalas ? 'bg-emerald-100 text-emerald-950' : 'bg-amber-100 text-amber-950' }}">
                        <span class="w-2 h-2 rounded-full {{ $isDibalas ? 'bg-emerald-600' : 'bg-amber-500 animate-pulse' }}"></span>
                        {{ $statusLabel }}
                    </span>
                </div>

                <!-- Isi Keluhan -->
                <div>
                    <div class="text-[10px] font-heading font-extrabold uppercase tracking-wider text-slate-400 mb-1">Rincian Keluhan Penumpang</div>
                    <p class="text-sm text-slate-900 font-medium leading-relaxed">
                        {{ $keluhan->deskripsi }}
                    </p>
                </div>

                <!-- Tanggapan Admin / CS jika ada -->
                @if ($isDibalas)
                    <div class="p-4 rounded-xl bg-amber-50 border-2 border-slate-900 text-xs space-y-2 shadow-[2px_2px_0px_#18181b]">
                        <div class="flex items-center justify-between text-slate-700">
                            <span class="font-heading font-black text-slate-900 flex items-center gap-1.5 text-xs">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Tanggapan dari {{ $petugasNama }}
                            </span>
                            <span class="font-mono text-[11px] text-slate-500">{{ $dibalasPada }} WIB</span>
                        </div>
                        <p class="text-slate-800 font-medium leading-relaxed">
                            {{ $keluhan->balasan }}
                        </p>
                    </div>
                @else
                    <div class="text-xs text-slate-500 italic font-medium p-3 rounded-xl bg-stone-50 border border-slate-200">
                        &bull; Keluhan Anda telah masuk dalam antrean dan sedang ditinjau oleh staf operasional Gotix.
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-16 bg-white border-2 border-slate-900 rounded-2xl p-8 shadow-[5px_5px_0px_#18181b]">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-slate-900 text-amber-600 flex items-center justify-center mx-auto mb-4 shadow-[3px_3px_0px_#18181b]">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="font-heading font-black text-slate-900 text-xl">Tidak Ada Keluhan Aktif</h3>
                <p class="text-sm text-slate-600 mt-2 font-medium">Layanan perjalanan Anda berjalan lancar tanpa laporan kendala.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
