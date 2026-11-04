@extends('layouts.tamu')

@section('title', 'Ajukan Keluhan Baru — Gotix')

@section('konten')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        pesan: '',
        maksKarakter: 200,
        get sisa() {
            return this.maksKarakter - this.pesan.length;
        }
     }">

    <!-- Tombol Kembali -->
    <div class="mb-6 flex items-center justify-between" data-aos="fade-down" data-aos-duration="400">
        <a href="{{ route('member.keluhan.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Keluhan</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6" data-aos="fade-up" data-aos-duration="500">
        <!-- Header Form -->
        <div class="pb-5 border-b border-slate-100">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-sky-50 border border-sky-200/60 text-sky-700 text-xs font-semibold mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                Formulir Aspirasi Penumpang
            </div>
            <h1 class="font-heading text-2xl font-extrabold text-slate-900">Ajukan Keluhan Perjalanan</h1>
            <p class="text-xs text-slate-600 mt-1">
                Kritik dan saran Anda sangat berharga untuk peningkatan mutu armada dan ketepatan layanan Gotix.
            </p>
        </div>

        <form action="#" method="POST" class="space-y-5" onsubmit="event.preventDefault(); alert('Keluhan Anda telah berhasil dikirim. Tim Customer Service Gotix akan segera menindaklanjuti.'); window.location.href='{{ route('member.keluhan.index') }}';">
            @csrf

            <!-- Pilih Kode Tiket / Pesanan -->
            <div>
                <label for="pesanan_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Tiket / Pesanan Terkait (Opsional)
                </label>
                <div class="relative">
                    <select name="pesanan_id" id="pesanan_id"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition appearance-none cursor-pointer">
                        <option value="">-- Pilih Tiket Perjalanan Terkait (Bila Ada) --</option>
                        <option value="GTX-JKTBDG">GTX-JKTBDG — Jakarta → Bandung ({{ date('d M Y', strtotime('+2 days')) }})</option>
                        <option value="GTX-YOGSUB">GTX-YOGSUB — Yogyakarta → Surabaya ({{ date('d M Y', strtotime('+5 days')) }})</option>
                        <option value="GTX-321550">GTX-321550 — Medan → Pekanbaru ({{ date('d M Y', strtotime('+1 day')) }})</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kategori Masalah -->
            <div>
                <label for="kategori" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Kategori Masalah
                </label>
                <div class="relative">
                    <select name="kategori" id="kategori" required
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition appearance-none cursor-pointer">
                        <option value="" disabled selected>Pilih kategori keluhan</option>
                        <option value="Pelayanan">Pelayanan (Kru / Pengemudi / Sopir)</option>
                        <option value="Armada">Kondisi Armada (AC, Kebersihan, Bagasi)</option>
                        <option value="Pembayaran">Pembayaran & Konfirmasi Transfer</option>
                        <option value="Akun">Akun & Kendala Profil Member</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Detail Masalah (Max 200 Karakter) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="deskripsi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Rincian Detail Keluhan
                    </label>
                    <span class="text-[11px] font-mono" :class="sisa < 20 ? 'text-rose-600 font-bold' : 'text-slate-400'">
                        Sisa <span x-text="sisa"></span> karakter
                    </span>
                </div>
                <textarea name="deskripsi" id="deskripsi" rows="4" required
                          maxlength="200"
                          x-model="pesan"
                          placeholder="Jelaskan secara singkat kendala yang Anda alami (maksimal 200 karakter)..."
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition leading-relaxed"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('member.keluhan.index') }}"
                   class="px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-xl transition">
                    Batalkan
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm cursor-pointer">
                    Kirim Laporan Keluhan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
