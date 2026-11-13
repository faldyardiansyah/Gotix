@extends('layouts.tamu')

@section('title', 'Ajukan Keluhan Baru — Gotix')

@section('konten')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        pesan: '{{ old('deskripsi', '') }}',
        maksKarakter: {{ (int) config('gotix.keluhan.deskripsi_max', 1000) }},
        get sisa() {
            return this.maksKarakter - this.pesan.length;
        }
     }">

    <!-- Tombol Kembali -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('member.keluhan.index') }}"
           class="inline-flex items-center gap-2 text-xs font-heading font-bold text-slate-700 hover:text-slate-950 px-3.5 py-1.5 rounded-full border-2 border-slate-900 bg-white shadow-[2px_2px_0px_#18181b] transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Keluhan</span>
        </a>
    </div>

    <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 sm:p-8 shadow-[5px_5px_0px_#18181b] space-y-6">
        <!-- Header Form -->
        <div class="pb-5 border-b-2 border-slate-900">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 border-2 border-slate-900 text-slate-950 font-heading text-xs font-black shadow-[2px_2px_0px_#18181b] mb-2.5">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Formulir Aspirasi Penumpang</span>
            </div>
            <h1 class="font-heading text-2xl font-black text-slate-900">Ajukan Keluhan Perjalanan</h1>
            <p class="text-xs text-slate-600 mt-1 font-medium">
                Kritik dan masukan Anda sangat berharga untuk peningkatan mutu armada dan ketepatan layanan Gotix.
            </p>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-100 border-2 border-slate-900 text-xs text-rose-950 space-y-1 font-bold shadow-[3px_3px_0px_#18181b]">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Mohon Periksa Kolom Input:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-900 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('member.keluhan.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Pilih Kode Tiket / Pesanan -->
            <div>
                <label for="pesanan_id" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                    Tiket / Pesanan Terkait (Opsional)
                </label>
                <div class="relative">
                    <select name="pesanan_id" id="pesanan_id"
                            class="w-full bg-white border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition appearance-none cursor-pointer">
                        <option value="">-- Pilih Tiket Perjalanan Terkait (Bila Ada) --</option>
                        @foreach ($daftarPesanan as $pesanan)
                            @php
                                $rute = ($pesanan->jadwal?->kotaAsal?->nama_kota ?? 'Asal') . ' → ' . ($pesanan->jadwal?->kotaTujuan?->nama_kota ?? 'Tujuan');
                                $tgl = $pesanan->jadwal?->waktu_berangkat ? \Carbon\Carbon::parse($pesanan->jadwal->waktu_berangkat)->format('d M Y') : '-';
                            @endphp
                            <option value="{{ $pesanan->id }}" {{ old('pesanan_id') == $pesanan->id ? 'selected' : '' }}>
                                {{ $pesanan->kode_pesanan }} — {{ $rute }} (Kursi #{{ $pesanan->nomor_kursi }}, {{ $tgl }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-900">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kategori Masalah -->
            <div>
                <label for="kategori" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                    Kategori Masalah <span class="text-rose-600 font-black">*</span>
                </label>
                <div class="relative">
                    <select name="kategori" id="kategori" required
                            class="w-full bg-white border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition appearance-none cursor-pointer">
                        <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih kategori keluhan</option>
                        @foreach ($kategori as $kat)
                            @php
                                $val = $kat instanceof \App\Enums\KategoriKeluhan ? $kat->value : (string) $kat;
                            @endphp
                            <option value="{{ $val }}" {{ old('kategori') == $val ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-900">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Detail Masalah (Max 1000 Karakter) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="deskripsi" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900">
                        Rincian Detail Keluhan <span class="text-rose-600 font-black">*</span>
                    </label>
                    <span class="text-[11px] font-mono font-bold" :class="sisa < 50 ? 'text-rose-600' : 'text-slate-500'">
                        Sisa <span x-text="sisa"></span> karakter
                    </span>
                </div>
                <textarea name="deskripsi" id="deskripsi" rows="5" required
                          minlength="{{ (int) config('gotix.keluhan.deskripsi_min', 10) }}"
                          maxlength="{{ (int) config('gotix.keluhan.deskripsi_max', 1000) }}"
                          x-model="pesan"
                          placeholder="Jelaskan secara rinci kendala yang Anda alami (antara 10 s/d 1.000 karakter)..."
                          class="w-full bg-white border-2 border-slate-900 rounded-xl px-3.5 py-3 text-xs font-medium text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition leading-relaxed"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <x-button href="{{ route('member.keluhan.index') }}" variant="secondary" size="md">
                    Batalkan
                </x-button>
                <x-button type="submit" variant="primary" size="md">
                    Kirim Laporan Keluhan
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection
