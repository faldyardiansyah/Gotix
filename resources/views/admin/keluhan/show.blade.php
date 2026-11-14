@extends('layouts.admin')

@section('title', 'Detail Keluhan — GoTix.')
@section('page-title', 'Detail Keluhan Pelanggan')
@section('page-subtitle', 'Tinjau keluhan penumpang dan kirimkan tanggapan atau solusi resmi.')

@section('konten')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.keluhan.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-heading font-black text-slate-700 hover:text-slate-950 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Keluhan</span>
        </a>
    </div>

    @php
        $kategoriVal = $keluhan->kategori instanceof \App\Enums\KategoriKeluhan ? $keluhan->kategori->value : (string) ($keluhan->kategori ?? '-');
    @endphp

    <!-- Kartu Informasi Keluhan -->
    <x-card title="Laporan Keluhan #KLH-{{ $keluhan->id }}" subtitle="Diajukan oleh penumpang melalui portal keluhan" variant="white">
        <div class="space-y-4 text-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b-2 border-slate-900">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-heading font-black bg-amber-100 text-amber-950 border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                        Kategori: {{ $kategoriVal }}
                    </span>
                    <h2 class="font-heading font-black text-slate-950 text-base mt-2">
                        Keluhan dari {{ $keluhan->pengguna?->nama ?? 'Pengguna' }}
                    </h2>
                    <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                        Email: {{ $keluhan->pengguna?->email ?? '-' }} &bull; Telp: {{ $keluhan->pengguna?->telepon ?? '-' }}
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-slate-500 font-bold uppercase text-[10px]">Waktu Diajukan:</span>
                    <div class="font-bold text-slate-900 text-xs">{{ $keluhan->created_at ? $keluhan->created_at->format('d M Y, H:i') : '-' }} WIB</div>
                </div>
            </div>

            <div>
                <span class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-2">
                    Uraian Keluhan:
                </span>
                <div class="p-4 rounded-xl bg-stone-50 border-2 border-slate-900 text-sm text-slate-900 leading-relaxed whitespace-pre-wrap font-medium shadow-[2px_2px_0px_#18181b]">
                    {{ $keluhan->deskripsi }}
                </div>
            </div>

            @if ($keluhan->balasan)
                <div class="mt-4 pt-4 border-t-2 border-slate-900 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-heading font-black text-xs uppercase tracking-wider text-emerald-900">Tanggapan Resmi Admin:</span>
                        <span class="text-[11px] text-slate-500 font-semibold">Dibalas pada {{ $keluhan->dibalas_pada ? $keluhan->dibalas_pada->format('d M Y, H:i') : '-' }} WIB</span>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-50 border-2 border-slate-900 text-sm text-emerald-950 leading-relaxed whitespace-pre-wrap font-medium shadow-[2px_2px_0px_#059669]">
                        {{ $keluhan->balasan }}
                    </div>
                </div>
            @endif
        </div>
    </x-card>

    <!-- Form Kirim Balasan Admin -->
    <x-card title="{{ $keluhan->balasan ? 'Perbarui Balasan Keluhan' : 'Kirim Tanggapan / Solusi Resmi' }}"
            subtitle="Tanggapan Anda akan langsung tersinkronisasi dan dapat dibaca oleh member pada akunnya"
            variant="paper">
        <form action="{{ route('admin.keluhan.balas', $keluhan->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block font-heading font-black text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                    Isi Tanggapan Resmi Admin <span class="text-rose-600">*</span>
                </label>
                <textarea name="balasan" rows="4" required
                          placeholder="Tuliskan respon resmi, klarifikasi jadwal/armada, dan tindak lanjut dari pihak operasional GoTix..."
                          class="w-full rounded-xl border-2 border-slate-900 bg-white p-3.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">{{ old('balasan', $keluhan->balasan) }}</textarea>
                @error('balasan')
                    <span class="text-xs text-rose-600 font-bold mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-end pt-2">
                <x-button type="submit" variant="primary" size="md">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                    <span>Kirim Tanggapan ke Penumpang</span>
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
