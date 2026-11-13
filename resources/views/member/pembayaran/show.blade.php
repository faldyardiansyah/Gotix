@extends('layouts.tamu')

@section('title', 'Instruksi Pembayaran #' . $pesanan->kode_pesanan . ' — Gotix')

@section('konten')
<div class="max-w-[1140px] mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10"
    x-data="{
        copiedRekening: false,
        copiedNominal: false,
        previewUrl: null,
        fileName: '',
        submitting: false,
        handleFile(e) {
            const file = e.target.files[0];
            if (file) {
                this.fileName = file.name;
                if (file.type.startsWith('image/')) {
                    this.previewUrl = URL.createObjectURL(file);
                } else {
                    this.previewUrl = null;
                }
            }
        },
        copyText(text, target) {
            navigator.clipboard.writeText(text);
            this[target] = true;
            setTimeout(() => this[target] = false, 2500);
        }
     }">

    @php
    $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
    $isExpired = $pesanan->batas_bayar && now()->gt($pesanan->batas_bayar);
    $waktuBerangkat = $pesanan->jadwal ? \Carbon\Carbon::parse($pesanan->jadwal->waktu_berangkat) : null;
    $batasBayar = $pesanan->batas_bayar ? \Carbon\Carbon::parse($pesanan->batas_bayar) : null;
    $bankNama = config('gotix.bank.nama', 'Bank Central Asia (BCA)');
    $bankRekening = config('gotix.bank.rekening', '0000000000');
    $bankPemilik = config('gotix.bank.pemilik', 'PT Gotix Transportasi Indonesia');
    @endphp

    <!-- Tombol Kembali ke Detail Pesanan -->
    <div class="mb-6 flex items-center justify-between gap-4">
        <a href="{{ route('member.pesanan.show', $pesanan->kode_pesanan) }}"
            class="inline-flex items-center gap-2 text-xs font-heading font-bold text-slate-700 hover:text-slate-950 px-3.5 py-1.5 rounded-xl border-2 border-slate-900 bg-white shadow-[2px_2px_0px_#18181b] hover:-translate-y-0.5 active:translate-y-0.5 transition">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Detail Pesanan</span>
        </a>

        <div class="hidden sm:inline-flex items-center gap-2 text-xs font-heading font-extrabold text-slate-600">
            <span>Kode Pesanan:</span>
            <span class="font-mono font-black text-slate-950 bg-amber-100 border border-slate-900 px-2 py-0.5 rounded-md shadow-[1px_1px_0px_#18181b]">
                {{ $pesanan->kode_pesanan }}
            </span>
        </div>
    </div>

    <!-- Alert Kesalahan / Flash Session -->
    @if (session('gagal'))
    <div class="mb-6 bg-rose-100 border-2 border-slate-900 text-rose-950 rounded-2xl p-4 text-xs font-bold shadow-[3px_3px_0px_#18181b] flex items-center gap-2">
        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
        <span>{{ session('gagal') }}</span>
    </div>
    @endif

    <!-- Alert Penolakan Pembayaran Sebelumnya -->
    @if ($statusVal === 'Belum Bayar' && $pesanan->konfirmasiPembayaran?->alasan_penolakan)
    <div class="mb-6 bg-rose-50 border-2 border-rose-600 rounded-2xl p-4 shadow-[3px_3px_0px_#e11d48] space-y-1.5">
        <div class="flex items-center gap-2 text-rose-900 font-heading font-black text-xs uppercase tracking-wider">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 15.376Z" />
            </svg>
            <span>Pembayaran Sebelumnya Ditolak Operator Loket</span>
        </div>
        <p class="text-xs text-rose-950 font-medium">
            Alasan: <strong class="underline decoration-rose-400">{{ $pesanan->konfirmasiPembayaran->alasan_penolakan }}</strong>
        </p>
        <p class="text-[11px] text-rose-800">
            Silakan lakukan transfer ulang atau unggah berkas bukti pembayaran yang benar dan terbaca jelas melalui formulir di bawah.
        </p>
    </div>
    @endif

    <!-- LAYOUT DUA KOLOM PROPORSIONAL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- KOLOM KIRI (Ringkasan Tagihan & Rekening Tujuan) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card 1: Ringkasan Tagihan & Nominal Utama -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 sm:p-7 shadow-[4px_4px_0px_#18181b] space-y-5">
                <!-- Header Card & Status Badge -->
                <div class="flex items-center justify-between gap-2 pb-4 border-b-2 border-slate-100">
                    <span class="text-xs font-heading font-extrabold uppercase tracking-wider text-slate-500">
                        Ringkasan Tagihan
                    </span>
                    @if ($statusVal === 'Belum Bayar')
                    @if ($isExpired)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-100 text-rose-950 border border-slate-900 text-[11px] font-heading font-black">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                        <span>Kedaluwarsa</span>
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 text-slate-950 border border-slate-900 text-[11px] font-heading font-black">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Menunggu Transfer</span>
                    </span>
                    @endif
                    @elseif ($statusVal === 'Dalam Proses')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 text-slate-950 border border-slate-900 text-[11px] font-heading font-black">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Sedang Diverifikasi</span>
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-950 border border-slate-900 text-[11px] font-heading font-black">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        <span>Lunas</span>
                    </span>
                    @endif
                </div>

                <!-- Nominal Transfer Utama (Visual Spotlight) -->
                <div class="bg-amber-50 border-2 border-slate-900 rounded-xl p-4 sm:p-5 shadow-[2px_2px_0px_#18181b]">
                    <div class="text-[11px] font-heading font-black uppercase tracking-wider text-amber-900 mb-1">
                        Total Nominal Transfer
                    </div>
                    <div class="flex items-baseline justify-between gap-2 flex-wrap">
                        <div class="text-3xl sm:text-4xl font-heading font-black text-slate-950 tracking-tight">
                            Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                        </div>
                        <button type="button"
                            @click="copyText('{{ $pesanan->harga_snapshot }}', 'copiedNominal')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-heading font-bold text-slate-900 bg-white hover:bg-slate-50 border border-slate-900 rounded-lg shadow-[1px_1px_0px_#18181b] active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer"
                            title="Salin nominal transfer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                            </svg>
                            <span x-text="copiedNominal ? 'Tersalin!' : 'Salin'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-amber-950 font-medium mt-2">
                        Transfer persis sesuai nominal tanpa pembulatan agar verifikasi dapat diproses cepat.
                    </p>
                </div>

                <!-- Detail Rute & Jadwal -->
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Rute Perjalanan</span>
                        <span class="font-heading font-extrabold text-slate-900">
                            {{ $pesanan->jadwal->kotaAsal->nama_kota ?? '-' }} ➔ {{ $pesanan->jadwal->kotaTujuan->nama_kota ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Keberangkatan</span>
                        <span class="font-bold text-slate-900">
                            {{ $waktuBerangkat ? $waktuBerangkat->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Nomor Kursi</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-stone-100 border border-slate-900 font-heading font-black text-slate-900">
                            Kursi {{ str_pad($pesanan->nomor_kursi, 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Armada Travel</span>
                        <span class="font-bold text-slate-900">
                            {{ $pesanan->jadwal->kendaraan->nama_kendaraan ?? 'Minibus Eksekutif' }}
                        </span>
                    </div>
                </div>

                <!-- Batas Waktu Pembayaran (Expiry Deadline) -->
                <div class="pt-2">
                    <div class="p-3.5 rounded-xl border-2 {{ $isExpired ? 'bg-rose-50 border-rose-600 text-rose-950' : 'bg-stone-50 border-slate-900 text-slate-900' }} shadow-[2px_2px_0px_#18181b]">
                        <div class="text-[10px] font-heading font-black uppercase tracking-wider {{ $isExpired ? 'text-rose-800' : 'text-slate-500' }}">
                            Batas Waktu Pembayaran
                        </div>
                        <div class="text-sm font-heading font-black mt-0.5 {{ $isExpired ? 'text-rose-950' : 'text-slate-950' }}">
                            {{ $batasBayar ? $batasBayar->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                        </div>
                        @if ($statusVal === 'Belum Bayar')
                        @if ($isExpired)
                        <p class="text-[11px] font-semibold text-rose-800 mt-1">
                            Waktu pembayaran telah berakhir. Pesanan ini telah kedaluwarsa.
                        </p>
                        @else
                        <p class="text-[11px] font-medium text-slate-600 mt-1">
                            Harap selesaikan transfer sebelum batas waktu di atas agar pesanan tidak dibatalkan otomatis.
                        </p>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Informasi Rekening Tujuan Transfer -->
            <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 sm:p-7 shadow-[4px_4px_0px_#18181b] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b-2 border-slate-100">
                    <span class="text-xs font-heading font-extrabold uppercase tracking-wider text-slate-500">
                        Rekening Tujuan Transfer
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-400 border border-slate-900 text-slate-950 text-[10px] font-heading font-black">
                        Resmi Gotix
                    </span>
                </div>

                <!-- Detail Bank & Nomor Rekening -->
                <div class="space-y-3">
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Nama Bank</span>
                        <div class="font-heading font-black text-lg text-slate-950 mt-0.5">
                            {{ $bankNama }}
                        </div>
                    </div>

                    <!-- Nomor Rekening & Tombol Salin Terpisah (Tidak Bertabrakan) -->
                    <div class="p-3.5 bg-stone-50 border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_#18181b] space-y-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Nomor Rekening Tujuan</span>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="font-mono font-black text-2xl tracking-wider text-slate-950 select-all break-all">
                                {{ $bankRekening }}
                            </span>
                            <button type="button"
                                @click="copyText('{{ $bankRekening }}', 'copiedRekening')"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-heading font-black text-slate-950 bg-amber-400 hover:bg-amber-300 border-2 border-slate-900 rounded-xl shadow-[1.5px_1.5px_0px_#18181b] active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer shrink-0">
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                </svg>
                                <span x-text="copiedRekening ? 'Tersalin!' : 'Salin Nomor'"></span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Pemilik Rekening</span>
                        <div class="font-bold text-sm text-slate-900 mt-0.5">
                            a.n. {{ $bankPemilik }}
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-600 font-medium space-y-1">
                    <p>&bull; Harap simpan struk transfer atau tangkapan layar transaksi.</p>
                    <p>&bull; Unggah bukti transfer melalui formulir konfirmasi di samping.</p>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (Formulir Konfirmasi Pembayaran) -->
        <div class="lg:col-span-7">
            <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 sm:p-8 shadow-[4px_4px_0px_#18181b] space-y-6">

                <div>
                    <h2 class="font-heading text-xl sm:text-2xl font-black text-slate-950 tracking-tight">
                        Konfirmasi Pembayaran
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 font-medium">
                        Lengkapi rincian transfer dan unggah struk bukti bayar Anda untuk diverifikasi oleh operator loket.
                    </p>
                </div>

                <!-- Notifikasi Error Validasi Form -->
                @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border-2 border-rose-600 text-xs text-rose-950 shadow-[2px_2px_0px_#e11d48] space-y-1">
                    <div class="font-heading font-black text-rose-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

                @if ($statusVal === 'Belum Bayar')
                @if ($isExpired)
                <div class="p-6 rounded-xl bg-stone-100 border-2 border-slate-300 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 border-2 border-slate-900 mx-auto flex items-center justify-center font-black text-xl shadow-[2px_2px_0px_#18181b]">
                        !
                    </div>
                    <h3 class="font-heading font-black text-base text-slate-900">
                        Formulir Pembayaran Telah Ditutup
                    </h3>
                    <p class="text-xs text-slate-600 max-w-sm mx-auto font-medium">
                        Pesanan ini telah melewati batas waktu pembayaran. Silakan lakukan pemesanan tiket baru melalui halaman pencarian jadwal.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('jadwal.cari') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 font-heading font-black text-xs text-slate-950 rounded-xl border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] active:translate-x-0.5 active:translate-y-0.5 transition">
                            <span>Cari Jadwal Baru</span>
                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
                @else
                <!-- FORM UNGGAH BUKTI & KONFIRMASI -->
                <form action="{{ route('member.pembayaran.store', $pesanan->kode_pesanan) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-5"
                    @submit="submitting = true">
                    @csrf

                    <!-- Nama & Rekening Pengirim -->
                    <div>
                        <label for="rekening_pengirim" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                            Nama & Rekening Pengirim <span class="text-rose-600">*</span>
                        </label>
                        <input type="text"
                            name="rekening_pengirim"
                            id="rekening_pengirim"
                            value="{{ old('rekening_pengirim', $pesanan->konfirmasiPembayaran?->rekening_pengirim) }}"
                            required
                            placeholder="Contoh: Budi Santoso - BCA 1234567890"
                            class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                        <p class="text-[11px] text-slate-500 mt-1 font-medium">
                            Tulis nama pemilik rekening serta bank asal yang Anda gunakan saat transfer.
                        </p>
                    </div>

                    <!-- Nomor Referensi / Resi Transfer (Opsional) -->
                    <div>
                        <label for="nomor_resi" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                            Nomor Referensi / ID Transaksi <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input type="text"
                            name="nomor_resi"
                            id="nomor_resi"
                            value="{{ old('nomor_resi', $pesanan->konfirmasiPembayaran?->nomor_resi) }}"
                            placeholder="Contoh: 202611130001 atau nomor referensi m-Banking"
                            class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition font-mono">
                        <p class="text-[11px] text-slate-500 mt-1 font-medium">
                            Nomor transaksi atau nomor referensi unik pada struk bank Anda jika ada.
                        </p>
                    </div>

                    <!-- Grid Tanggal & Waktu Transfer (Lebar Seimbang, Label Tidak Terpotong) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tanggal Transfer -->
                        <div>
                            <label for="tanggal_transfer" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5 whitespace-nowrap">
                                Tanggal Transfer <span class="text-rose-600">*</span>
                            </label>
                            <input type="date"
                                name="tanggal_transfer"
                                id="tanggal_transfer"
                                value="{{ old('tanggal_transfer', date('Y-m-d')) }}"
                                required
                                class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                        </div>

                        <!-- Waktu Transfer -->
                        <div>
                            <label for="jam_transfer" class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5 whitespace-nowrap">
                                Waktu Transfer <span class="text-rose-600">*</span>
                            </label>
                            <input type="time"
                                name="jam_transfer"
                                id="jam_transfer"
                                value="{{ old('jam_transfer', date('H:i')) }}"
                                required
                                class="w-full bg-[#FAFAF9] border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                        </div>
                    </div>

                    <!-- Area Unggah Bukti Transfer -->
                    <div>
                        <label class="block text-xs font-heading font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                            Foto / Berkas Bukti Transfer <span class="text-rose-600">*</span>
                        </label>

                        <div class="relative border-2 border-dashed border-slate-900 rounded-xl p-5 sm:p-6 text-center bg-[#FAFAF9] hover:bg-amber-50/50 transition cursor-pointer">
                            <input type="file"
                                name="bukti_transfer"
                                id="bukti_transfer"
                                required
                                accept="image/*,.pdf"
                                @change="handleFile($event)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <!-- Placeholder Saat Belum Memilih Berkas -->
                            <template x-if="!previewUrl && !fileName">
                                <div class="space-y-2 py-2">
                                    <div class="w-12 h-12 rounded-xl bg-amber-400 border-2 border-slate-900 mx-auto flex items-center justify-center text-slate-950 shadow-[1.5px_1.5px_0px_#18181b]">
                                        <svg class="w-6 h-6 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-heading font-extrabold text-slate-950">
                                        Pilih foto struk atau tangkapan layar transfer
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium">
                                        Format: JPG, JPEG, PNG, WEBP, PDF (Maksimal 5 MB)
                                    </div>
                                </div>
                            </template>

                            <!-- Pratinjau Gambar Langsung -->
                            <template x-if="previewUrl">
                                <div class="space-y-2 py-1">
                                    <img :src="previewUrl" alt="Pratinjau Struk" class="max-h-56 mx-auto rounded-xl object-contain border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]">
                                    <div class="text-xs font-heading font-bold text-emerald-800 flex items-center justify-center gap-1.5 pt-1">
                                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span x-text="fileName"></span>
                                    </div>
                                    <span class="text-[11px] text-slate-500 font-medium block">
                                        Klik area ini jika ingin mengganti berkas yang dipilih
                                    </span>
                                </div>
                            </template>

                            <!-- Tampilan File Non-Gambar (misal PDF) -->
                            <template x-if="!previewUrl && fileName">
                                <div class="space-y-2 py-3">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-100 border-2 border-slate-900 mx-auto flex items-center justify-center text-emerald-900 font-black text-sm shadow-[1.5px_1.5px_0px_#18181b]">
                                        PDF
                                    </div>
                                    <div class="text-xs font-heading font-bold text-slate-950" x-text="fileName"></div>
                                    <span class="text-[11px] text-slate-500 font-medium block">
                                        Klik area ini jika ingin mengganti berkas yang dipilih
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tombol Kirim Bukti Pembayaran -->
                    <div class="pt-3">
                        <button type="submit"
                            :disabled="submitting"
                            class="w-full py-3.5 px-5 bg-amber-500 hover:bg-amber-400 active:translate-x-0.5 active:translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed text-slate-950 font-heading font-black text-sm rounded-xl border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] transition flex items-center justify-center gap-2 cursor-pointer">
                            <span x-show="!submitting" class="flex items-center gap-2">
                                <span>Kirim Bukti Pembayaran</span>
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>
                            </span>
                            <span x-show="submitting" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengunggah Berkas...</span>
                            </span>
                        </button>
                    </div>
                </form>
                @endif
                @else
                <!-- TAMPILAN JIKA STATUS SUDAH DALAM PROSES ATAU LUNAS -->
                <div class="space-y-5">
                    @if ($pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path)
                    <div class="space-y-3">
                        <span class="text-xs font-heading font-black text-slate-900 uppercase tracking-wider block">
                            Berkas Bukti Transfer yang Diunggah:
                        </span>
                        <div class="border-2 border-slate-900 rounded-xl overflow-hidden bg-stone-50 shadow-[2px_2px_0px_#18181b]">
                            <img src="{{ route('pembayaran.bukti', $pesanan->konfirmasiPembayaran->id) }}"
                                alt="Bukti Transfer" class="w-full max-h-72 object-cover">
                        </div>
                        <div class="p-3.5 bg-stone-50 border-2 border-slate-900 rounded-xl text-xs text-slate-800 space-y-1 font-medium shadow-[1.5px_1.5px_0px_#18181b]">
                            <p>&bull; Rekening Pengirim: <strong>{{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}</strong></p>
                            <p>&bull; Nomor Referensi: <strong class="font-mono">{{ $pesanan->konfirmasiPembayaran->nomor_resi }}</strong></p>
                            <p>&bull; Waktu Transfer: <strong>{{ $pesanan->konfirmasiPembayaran->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }}</strong></p>
                        </div>
                    </div>
                    @endif

                    <div class="pt-2">
                        <a href="{{ route('member.pesanan.show', $pesanan->kode_pesanan) }}"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-5 bg-white hover:bg-slate-50 font-heading font-bold text-xs text-slate-900 rounded-xl border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] active:translate-x-0.5 active:translate-y-0.5 transition">
                            <span>Lihat Rincian Riwayat Pesanan</span>
                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection