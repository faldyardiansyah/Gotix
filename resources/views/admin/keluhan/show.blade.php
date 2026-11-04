@extends('layouts.admin')

@section('title', 'Detail Keluhan Pelanggan — Admin Gotix')
@section('page-title', 'Detail Keluhan Pelanggan')
@section('page-subtitle', 'Tinjau keluhan member dan kirimkan tanggapan / solusi resmi.')

@section('konten')
<div class="max-w-3xl space-y-6">
    <div>
        <a href="{{ route('admin.keluhan.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Keluhan</span>
        </a>
    </div>

    @if (session('sukses'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
            {{ session('sukses') }}
        </div>
    @endif

    <!-- Kartu Informasi Keluhan -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                    Kategori: {{ $keluhan->kategori?->value ?? (string) $keluhan->kategori }}
                </span>
                <h2 class="font-heading font-bold text-slate-900 text-base mt-2">
                    Keluhan dari {{ $keluhan->pengguna?->nama ?? 'Pengguna' }}
                </h2>
                <div class="text-xs text-slate-400 mt-0.5">
                    Email: {{ $keluhan->pengguna?->email ?? '-' }} • Telp: {{ $keluhan->pengguna?->telepon ?? '-' }}
                </div>
            </div>
            <div class="text-right text-xs text-slate-400">
                <span>Diajukan pada:</span>
                <div class="font-semibold text-slate-700">{{ $keluhan->created_at ? $keluhan->created_at->format('d M Y, H:i') : '-' }} WIB</div>
            </div>
        </div>

        <div>
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1.5">Isi Keluhan:</span>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-sm text-slate-800 leading-relaxed whitespace-pre-wrap">
                {{ $keluhan->deskripsi }}
            </div>
        </div>

        @if ($keluhan->balasan)
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Tanggapan / Solusi Resmi:</span>
                    <span class="text-[11px] text-slate-400">Dibalas pada {{ $keluhan->dibalas_pada ? $keluhan->dibalas_pada->format('d M Y, H:i') : '-' }} WIB</span>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200 text-sm text-emerald-950 leading-relaxed whitespace-pre-wrap">
                    {{ $keluhan->balasan }}
                </div>
            </div>
        @endif
    </div>

    <!-- Form Kirim Balasan Admin -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <h3 class="font-heading font-bold text-slate-900 text-sm mb-1">
            {{ $keluhan->balasan ? 'Perbarui Balasan Keluhan' : 'Kirim Tanggapan / Balas Keluhan' }}
        </h3>
        <p class="text-xs text-slate-500 mb-4">
            Tanggapan Anda akan disimpan dan dapat dibaca oleh member pada dasbor keluhan akunnya.
        </p>

        <form action="{{ route('admin.keluhan.balas', $keluhan->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <textarea name="balasan" rows="4" required
                          placeholder="Tuliskan respon resmi dan tindak lanjut dari pihak admin Gotix..."
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-600 transition">{{ old('balasan', $keluhan->balasan) }}</textarea>
                @error('balasan')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-end">
                <button type="submit"
                        class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Kirim Tanggapan ke Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
