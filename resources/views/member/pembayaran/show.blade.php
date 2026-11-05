@extends('layouts.tamu')

@section('title', 'Instruksi Pembayaran #' . $pesanan->kode_pesanan . ' — Gotix')

@section('konten')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        copiedBank: false,
        copiedRekening: false,
        copiedTotal: false,
        previewUrl: null,
        fileName: '',
        handleFile(e) {
            const file = e.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.previewUrl = URL.createObjectURL(file);
            }
        },
        copyText(text, target) {
            navigator.clipboard.writeText(text);
            this[target] = true;
            setTimeout(() => this[target] = false, 2000);
        }
     }">

    <!-- Header Status -->
    <div class="mb-8 text-center" data-aos="fade-down" data-aos-duration="400">
        @php
            $statusVal = $pesanan->status?->value ?? (string) $pesanan->status;
        @endphp

        @if ($statusVal === 'Belum Bayar')
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Menunggu Transfer Pembayaran
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Selesaikan Pembayaran Tiket Anda
            </h1>
            <p class="text-sm text-slate-600 mt-1 max-w-lg mx-auto">
                Silakan lakukan transfer manual ke rekening resmi Gotix dan unggah foto/struk bukti transfer Anda di bawah ini.
            </p>
        @elseif ($statusVal === 'Dalam Proses')
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-xs font-semibold mb-3">
                <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                Bukti Sedang Diverifikasi Operator
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Bukti Transfer Berhasil Dikirim
            </h1>
            <p class="text-sm text-slate-600 mt-1 max-w-lg mx-auto">
                Operator loket sedang memeriksa bukti bayar Anda. E-Tiket akan terbit otomatis begitu diverifikasi.
            </p>
        @else
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Pembayaran Lunas
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Tiket Perjalanan Anda Aktif
            </h1>
        @endif
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl p-4 text-xs font-medium space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: Rekening & Petunjuk Transfer -->
        <div class="lg:col-span-7 space-y-6" data-aos="fade-right" data-aos-duration="500">
            <!-- Nominal Tagihan -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Tagihan Transfer</div>
                        <div class="font-heading text-3xl font-extrabold text-slate-900 mt-1">
                            Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            Kode Pemesanan: <span class="font-mono font-bold text-slate-800">{{ $pesanan->kode_pesanan }}</span>
                        </div>
                    </div>

                    <button type="button"
                            @click="copyText('{{ $pesanan->harga_snapshot }}', 'copiedTotal')"
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
                <h2 class="font-heading font-bold text-slate-900 text-sm">Rekening Tujuan Pembayaran</h2>

                <!-- BCA -->
                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-600 text-white font-heading font-bold text-xs flex items-center justify-center">
                            BCA
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">Bank Central Asia (BCA)</div>
                            <div class="font-mono font-bold text-slate-900 text-sm mt-0.5">8820 1928 33</div>
                            <div class="text-[11px] text-slate-400">a.n PT Gotix Travel Indonesia</div>
                        </div>
                    </div>
                    <button type="button" @click="copyText('8820192833', 'copiedRekening')"
                            class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition">
                        <span x-text="copiedRekening ? 'Tersalin!' : 'Salin'">Salin</span>
                    </button>
                </div>

                <div class="text-[11px] text-slate-500 leading-relaxed space-y-1">
                    <p>• Masukkan nominal transfer tepat sejumlah <strong>Rp {{ number_format($pesanan->harga_snapshot, 0, ',', '.') }}</strong>.</p>
                    <p>• Cantumkan kode booking <strong>{{ $pesanan->kode_pesanan }}</strong> pada kolom berita acara transfer.</p>
                </div>
            </div>

            <!-- Rincian Pesanan Singkat -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-3 text-xs">
                <h3 class="font-heading font-bold text-slate-900 text-sm pb-2 border-b border-slate-100">Ringkasan Jadwal</h3>
                <div class="flex justify-between">
                    <span class="text-slate-500">Rute:</span>
                    <span class="font-bold text-slate-900">{{ $pesanan->jadwal?->kotaAsal?->nama_kota ?? '-' }} → {{ $pesanan->jadwal?->kotaTujuan?->nama_kota ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu:</span>
                    <span class="font-medium text-slate-900">
                        {{ $pesanan->jadwal?->tanggal_berangkat ? date('d M Y', strtotime($pesanan->jadwal->tanggal_berangkat)) : '-' }}
                        pukul {{ $pesanan->jadwal?->jam_berangkat ? substr($pesanan->jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nomor Kursi:</span>
                    <span class="font-bold text-sky-600">Kursi #{{ $pesanan->nomor_kursi }}</span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Upload Bukti Transfer -->
        <div class="lg:col-span-5 space-y-6" data-aos="fade-left" data-aos-duration="500">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm">
                <div class="flex items-center gap-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-200 flex items-center justify-center text-sky-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-heading font-bold text-slate-900 text-base">Konfirmasi Pembayaran</h2>
                        <div class="text-[11px] text-slate-500">Unggah foto struk transfer manual Anda</div>
                    </div>
                </div>

                @if ($statusVal === 'Belum Bayar')
                    <!-- FORM ASLI MENGIRIM FILE BUKTI KE DATABASE -->
                    <form action="{{ route('member.pembayaran.store', $pesanan->kode_pesanan) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama & Rekening Pengirim</label>
                            <input type="text" name="rekening_pengirim" required value="{{ old('rekening_pengirim', Auth::user()->nama ?? '') }}"
                                   placeholder="Contoh: BCA - Budi Santoso"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Resi / Referensi Transfer (Opsional)</label>
                            <input type="text" name="nomor_resi" value="{{ old('nomor_resi') }}"
                                   placeholder="Contoh: TRX98124892 (Boleh kosong, otomatis dibuat)"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition font-mono">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transfer</label>
                                <input type="date" name="tanggal_transfer" value="{{ old('tanggal_transfer', date('Y-m-d')) }}"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Transfer</label>
                                <input type="time" name="jam_transfer" value="{{ old('jam_transfer', date('H:i')) }}"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Foto / Tangkapan Layar Struk Transfer</label>
                            
                            <!-- Box Upload File -->
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-sky-500 rounded-2xl p-4 text-center cursor-pointer transition bg-slate-50/50">
                                <input type="file" name="bukti_transfer" accept="image/*" required @change="handleFile($event)"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                
                                <template x-if="!previewUrl">
                                    <div>
                                        <svg class="w-8 h-8 text-slate-400 mx-auto mb-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <div class="text-xs font-semibold text-slate-700">Pilih berkas foto struk transfer</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">JPG, JPEG, PNG (Maks. 5MB)</div>
                                    </div>
                                </template>

                                <!-- Pratinjau Gambar Langsung Setelah Dipilih -->
                                <template x-if="previewUrl">
                                    <div class="space-y-2">
                                        <img :src="previewUrl" alt="Pratinjau Foto" class="max-h-48 mx-auto rounded-xl object-contain border border-slate-200">
                                        <div class="text-xs font-semibold text-emerald-600 flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            <span x-text="fileName"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 block">Klik area ini jika ingin mengganti foto</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full py-3 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm shadow-sky-600/20 cursor-pointer">
                            Kirim Foto Bukti Transfer
                        </button>
                    </form>
                @else
                    <!-- SUDAH UPLOAD BUKTI -->
                    <div class="space-y-4">
                        @if ($pesanan->konfirmasiPembayaran && $pesanan->konfirmasiPembayaran->bukti_path)
                            <div>
                                <span class="text-xs font-semibold text-slate-700 block mb-2">Struk yang Telah Diunggah:</span>
                                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50">
                                    <img src="{{ asset('storage/' . $pesanan->konfirmasiPembayaran->bukti_path) }}"
                                         alt="Bukti Transfer" class="w-full max-h-64 object-cover">
                                </div>
                                <div class="text-[11px] text-slate-500 mt-2 space-y-0.5">
                                    <p>• Rekening Pengirim: <strong>{{ $pesanan->konfirmasiPembayaran->rekening_pengirim }}</strong></p>
                                    <p>• Resi/Referensi: <strong class="font-mono">{{ $pesanan->konfirmasiPembayaran->nomor_resi }}</strong></p>
                                    <p>• Waktu Transfer: <strong>{{ $pesanan->konfirmasiPembayaran->tanggal_transfer ? date('d M Y', strtotime($pesanan->konfirmasiPembayaran->tanggal_transfer)) : '-' }} {{ $pesanan->konfirmasiPembayaran->jam_transfer ? substr($pesanan->konfirmasiPembayaran->jam_transfer, 0, 5) : '' }}</strong></p>
                                </div>
                            </div>
                        @endif

                        <div class="pt-2">
                            <a href="{{ route('member.pesanan.show', $pesanan->kode_pesanan) }}"
                               class="w-full block py-2.5 text-center text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                                Lihat Detail Reservasi
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
