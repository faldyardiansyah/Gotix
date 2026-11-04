@extends('layouts.tamu')

@section('title', 'Verifikasi Struk #' . ($id ?? 'GTX-774102') . ' — Operator Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        modalBukti: false,
        modalTolak: false,
        alasanTolak: '',
        terimaPembayaran() {
            if (confirm('Apakah Anda yakin memvalidasi pembayaran ini dan menerbitkan E-Tiket resmi?')) {
                alert('Pembayaran berhasil diverifikasi. E-Tiket otomatis dikirim ke akun penumpang.');
                window.location.href = '{{ route('operator.pembayaran.index') }}';
            }
        },
        konfirmasiTolak() {
            if (!this.alasanTolak.trim()) {
                alert('Mohon cantumkan alasan penolakan agar penumpang mengetahui instruksi perbaikan.');
                return;
            }
            alert('Pembayaran telah ditolak dengan alasan: ' + this.alasanTolak + '. Status pesanan dikembalikan ke Belum Bayar.');
            this.modalTolak = false;
            window.location.href = '{{ route('operator.pembayaran.index') }}';
        }
     }">

    <!-- Breadcrumb & Navigasi Kembali -->
    <div class="mb-6 flex items-center justify-between gap-4" data-aos="fade-down" data-aos-duration="400">
        <a href="{{ route('operator.pembayaran.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Verifikasi</span>
        </a>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
            <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
            Menunggu Verifikasi Operator
        </span>
    </div>

    @php
        $bookingCode = $id ?? 'GTX-774102';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: Rincian Pesanan & Pembayaran -->
        <div class="lg:col-span-7 space-y-6" data-aos="fade-right" data-aos-duration="500">
            <!-- Header Kartu -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Transaksi</span>
                        <h1 class="font-heading text-2xl font-extrabold text-slate-900 mt-0.5">#{{ $bookingCode }}</h1>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400">Waktu Kirim Konfirmasi</div>
                        <div class="text-xs font-bold text-slate-800 font-mono">{{ date('d M Y, H:i') }} WIB</div>
                    </div>
                </div>

                <!-- Info Pemesan -->
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nama Penumpang</div>
                        <div class="text-sm font-bold text-slate-900 mt-0.5">Siti Rahma</div>
                        <div class="text-xs text-slate-500 font-mono">0812-7000-1002</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nomor Kursi</div>
                        <div class="text-sm font-bold text-sky-600 mt-0.5">Kursi #04 (Kapasitas 5)</div>
                        <div class="text-xs text-slate-500">Isuzu Elf Executive (BE 4677 DT)</div>
                    </div>
                </div>
            </div>

            <!-- Rute & Jadwal -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-heading font-bold text-slate-900 text-sm">Informasi Rute Perjalanan</h2>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kota Asal</span>
                        <span class="font-bold text-slate-900">Yogyakarta (Malioboro)</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kota Tujuan</span>
                        <span class="font-bold text-slate-900">Surabaya (Gubeng)</span>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200">
                        <span class="text-slate-500">Tanggal Keberangkatan</span>
                        <span class="font-bold text-slate-900">{{ date('d M Y', strtotime('+5 days')) }} &bull; 13:00 WIB</span>
                    </div>
                </div>
            </div>

            <!-- Perbandingan Tagihan vs Data Transfer -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-heading font-bold text-slate-900 text-sm">Pencocokan Nominal & Rekening</h2>
                <div class="border border-slate-200 rounded-2xl p-4 space-y-2.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Tarif Resmi Sistem</span>
                        <span class="font-mono font-bold text-slate-900 text-sm">Rp 180.000</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Bank Tujuan Gotix</span>
                        <span class="font-medium text-slate-800">BCA (8820 1928 33 a.n PT Gotix Travel)</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Nama Rekening Pengirim</span>
                        <span class="font-medium text-slate-800">Siti Rahma</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Nomor Resi / Referensi Bank</span>
                        <span class="font-mono text-slate-800 font-semibold">TRX-2026110488219</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pratinjau Struk & Tombol Aksi -->
        <div class="lg:col-span-5 space-y-6" data-aos="fade-left" data-aos-duration="500">
            <!-- Box Pratinjau Bukti Transfer -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-heading font-bold text-slate-900 text-sm">Berkas Bukti Transfer</h2>
                    <span class="text-[11px] text-slate-400">Klik gambar untuk perbesar</span>
                </div>

                <!-- Gambar Bukti Transfer Mockup / Klik Zoom -->
                <div @click="modalBukti = true"
                     class="relative border border-slate-200 rounded-2xl overflow-hidden bg-slate-100 group cursor-pointer shadow-xs aspect-[3/4] flex items-center justify-center">
                    <!-- SVG Struk Transfer Modern -->
                    <div class="w-full h-full p-6 bg-slate-900 text-white flex flex-col justify-between group-hover:scale-102 transition duration-200">
                        <div>
                            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                                <div class="font-bold text-sm tracking-wide text-sky-400">BCA Mobile</div>
                                <div class="text-[10px] text-emerald-400 font-semibold px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">BERHASIL</div>
                            </div>
                            <div class="space-y-2 text-xs">
                                <div>
                                    <div class="text-[10px] text-slate-400">Penerima</div>
                                    <div class="font-semibold text-slate-200">PT GOTIX TRAVEL INDONESIA</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-400">Jumlah Transfer</div>
                                    <div class="text-lg font-bold text-white font-mono">Rp 180.000</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-400">Nomor Referensi</div>
                                    <div class="font-mono text-slate-300 text-[11px]">TRX-2026110488219</div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-800 text-[10px] text-slate-500 text-center">
                            Ketuk untuk melihat ukuran penuh
                        </div>
                    </div>

                    <!-- Overlay Icon Zoom -->
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/80 backdrop-blur-sm text-xs font-semibold">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                            </svg>
                            <span>Perbesar Struk</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Verifikasi Tegas -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-3">
                <button type="button"
                        @click="terimaPembayaran()"
                        class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm shadow-emerald-600/20 flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>Terima & Terbitkan Tiket</span>
                </button>

                <button type="button"
                        @click="modalTolak = true"
                        class="w-full py-3 px-4 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 border border-rose-200 font-bold text-xs uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                    <span>Tolak Pembayaran</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Pratinjau Gambar Struk (Zoom In) -->
    <div x-show="modalBukti"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-cloak>
        <div @click.away="modalBukti = false"
             class="bg-white border border-slate-200 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-heading font-bold text-slate-900 text-sm">Detail Tangkapan Layar Struk Transfer</h3>
                <button @click="modalBukti = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Full preview -->
            <div class="bg-slate-900 text-white rounded-2xl p-6 font-mono text-xs space-y-4 border border-slate-800">
                <div class="text-center font-bold text-sky-400 text-sm pb-2 border-b border-slate-800">
                    BUKTI TRANSFER BCA MOBILE
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-400">WAKTU:</span>
                        <span>{{ date('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">DARI REKENING:</span>
                        <span>SITI RAHMA (***4812)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">KE REKENING:</span>
                        <span>PT GOTIX TRAVEL INDONESIA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">NOMOR REK:</span>
                        <span>8820 1928 33</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-800 text-sm font-bold">
                        <span class="text-slate-400">NOMINAL:</span>
                        <span class="text-emerald-400">Rp 180.000,00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">STATUS:</span>
                        <span class="text-emerald-400">TRANSAKSI BERHASIL</span>
                    </div>
                </div>
            </div>
            <button @click="modalBukti = false"
                    class="w-full py-2.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">
                Tutup Pratinjau
            </button>
        </div>
    </div>

    <!-- Modal Form Tolak Pembayaran -->
    <div x-show="modalTolak"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-cloak>
        <div @click.away="modalTolak = false"
             class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center gap-2.5 text-rose-600">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <h3 class="font-heading font-bold text-slate-900 text-base">Alasan Penolakan Pembayaran</h3>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                Tuliskan penjelasan yang jelas mengapa bukti transfer ini tidak dapat diverifikasi (misal: nominal kurang, bukti buram, atau rekening tidak sesuai).
            </p>
            <div>
                <textarea x-model="alasanTolak" rows="3" required
                          placeholder="Contoh: Nominal struk transfer Rp 100.000, kurang dari total tagihan resmi Rp 180.000. Mohon transfer kekurangan atau hubungi CS."
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 leading-relaxed"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="modalTolak = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="konfirmasiTolak()"
                        class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm cursor-pointer">
                    Kirim Penolakan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
