@extends('layouts.tamu')

@section('title', 'Instruksi Pembayaran #' . ($kode ?? 'GTX-321550') . ' — Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        copiedBank: false,
        copiedRekening: false,
        copiedTotal: false,
        timeLeft: 86395, // 23 jam 59 menit
        timerString: '',
        initTimer() {
            setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                    let hours = Math.floor(this.timeLeft / 3600);
                    let minutes = Math.floor((this.timeLeft % 3600) / 60);
                    let seconds = this.timeLeft % 60;
                    this.timerString = String(hours).padStart(2, '0') + ':' +
                                      String(minutes).padStart(2, '0') + ':' +
                                      String(seconds).padStart(2, '0');
                }
            }, 1000);
        },
        copyText(text, target) {
            navigator.clipboard.writeText(text);
            this[target] = true;
            setTimeout(() => this[target] = false, 2000);
        }
     }"
     x-init="initTimer()">

    <!-- Header & Timer Countdown -->
    <div class="mb-8 text-center" data-aos="fade-down" data-aos-duration="400">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold mb-3">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            Menunggu Transfer Pembayaran
        </div>
        <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Selesaikan Pembayaran Tiket Anda
        </h1>
        <p class="text-sm text-slate-600 mt-1 max-w-lg mx-auto">
            Silakan lakukan transfer manual sesuai nominal pas sebelum batas waktu berakhir untuk mengamankan kursi Anda.
        </p>

        <!-- Countdown Box -->
        <div class="mt-4 inline-flex items-center gap-3 px-5 py-2.5 bg-slate-900 text-white rounded-2xl shadow-sm border border-slate-800">
            <svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div class="text-xs text-slate-300 font-medium">Batas Waktu Bayar:</div>
            <div class="font-mono text-base font-bold text-sky-400 tracking-wider" x-text="timerString || '23:59:45'">
                23:59:45
            </div>
        </div>
    </div>

    @php
        $bookingCode = $kode ?? 'GTX-321550';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: Rekening & Petunjuk Transfer -->
        <div class="lg:col-span-7 space-y-6" data-aos="fade-right" data-aos-duration="500">
            <!-- Nominal Tagihan -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Tagihan Transfer</div>
                        <div class="font-heading text-3xl font-extrabold text-slate-900 mt-1">Rp 240.000</div>
                        <div class="text-xs text-slate-500 mt-0.5">Kode Pemesanan: <span class="font-mono font-bold text-slate-800">{{ $bookingCode }}</span></div>
                    </div>

                    <button type="button"
                            @click="copyText('240000', 'copiedTotal')"
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                        </svg>
                        <span x-text="copiedTotal ? 'Tersalin!' : 'Salin'">Salin</span>
                    </button>
                </div>
            </div>

            <!-- Rekening Tujuan Resmi -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pilihan Rekening Resmi Gotix</div>

                <!-- Kartu Rekening BCA -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-10 rounded-xl bg-blue-700 text-white font-extrabold text-xs flex items-center justify-center tracking-wider">
                            BCA
                        </div>
                        <div>
                            <div class="font-mono text-base font-bold text-slate-900">8820 1928 33</div>
                            <div class="text-xs text-slate-500">PT Gotix Travel Indonesia</div>
                        </div>
                    </div>
                    <button type="button"
                            @click="copyText('8820192833', 'copiedRekening')"
                            class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                        <span x-text="copiedRekening ? 'Tersalin!' : 'Salin'">Salin</span>
                    </button>
                </div>

                <!-- Kartu Rekening Mandiri -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-10 rounded-xl bg-amber-600 text-white font-extrabold text-[11px] flex items-center justify-center tracking-tight">
                            MANDIRI
                        </div>
                        <div>
                            <div class="font-mono text-base font-bold text-slate-900">1140 0293 8841</div>
                            <div class="text-xs text-slate-500">PT Gotix Travel Indonesia</div>
                        </div>
                    </div>
                    <button type="button"
                            @click="copyText('114002938841', 'copiedBank')"
                            class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                        <span x-text="copiedBank ? 'Tersalin!' : 'Salin'">Salin</span>
                    </button>
                </div>
            </div>

            <!-- Petunjuk Langkah Pembayaran -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <h3 class="font-heading font-bold text-slate-900 text-sm mb-3">Panduan Transfer Singkat</h3>
                <ol class="list-decimal list-inside space-y-2 text-xs text-slate-600 leading-relaxed">
                    <li>Gunakan ATM, Mobile Banking (BCA Mobile, Livin'), atau Internet Banking.</li>
                    <li>Transfer persis sejumlah <strong>Rp 240.000</strong> ke salah satu rekening di atas.</li>
                    <li>Simpan tangkapan layar (screenshot) atau struk bukti pembayaran yang jelas.</li>
                    <li>Unggah bukti pada formulir di samping untuk diverifikasi oleh operator kami.</li>
                </ol>
            </div>
        </div>

        <!-- Kolom Kanan: Form Unggah Bukti Pembayaran -->
        <div class="lg:col-span-5" data-aos="fade-left" data-aos-duration="500">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm sticky top-24">
                <div class="flex items-center gap-2.5 mb-5 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-200 flex items-center justify-center text-sky-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-heading font-bold text-slate-900 text-base">Konfirmasi Pembayaran</h2>
                        <div class="text-[11px] text-slate-500">Unggah bukti transfer manual Anda</div>
                    </div>
                </div>

                <form action="#" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="event.preventDefault(); alert('Bukti transfer berhasil dikirim. Operator Gotix akan segera memverifikasi pesanan Anda.'); window.location.href='{{ route('member.pesanan.index') }}';">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Rekening Pengirim</label>
                        <input type="text" name="rekening_pengirim" required placeholder="Contoh: Budi Santoso"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Resi / Referensi Bank</label>
                        <input type="text" name="nomor_resi" required placeholder="Contoh: 20261104192801"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition font-mono">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transfer</label>
                            <input type="date" name="tanggal_transfer" value="{{ date('Y-m-d') }}" required
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Transfer</label>
                            <input type="time" name="jam_transfer" value="{{ date('H:i') }}" required
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Struk Bukti Transfer</label>
                        <div class="relative border-2 border-dashed border-slate-300 hover:border-sky-500 rounded-2xl p-4 text-center cursor-pointer transition bg-slate-50/50">
                            <input type="file" name="bukti" accept="image/*,.pdf" required
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <svg class="w-8 h-8 text-slate-400 mx-auto mb-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <div class="text-xs font-semibold text-slate-700">Pilih berkas struk / foto</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">JPG, PNG, atau PDF (Maks. 5MB)</div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm shadow-sky-600/20 cursor-pointer">
                        Kirim Konfirmasi Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
