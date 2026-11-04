@extends('layouts.tamu')

@section('title', 'Kasir Pemesanan Loket (Walk-in) — Operator Gotix')

@section('konten')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        ruteTerpilih: 'jkt-bdg',
        tarifRute: 120000,
        kursiTerpilih: 3,
        kursiTerisi: [1, 5], // Kursi 1 dan 5 sudah terisi penumpang lain
        namaPenumpang: '',
        teleponPenumpang: '',
        jenisKelamin: 'L',
        metodeBayar: 'tunai',
        uangDiterima: 150000,
        modalCetak: false,
        kodeTiketInstan: 'GTX-LOKET' + Math.floor(1000 + Math.random() * 9000),

        gantiRute(event) {
            let val = event.target.value;
            this.ruteTerpilih = val;
            if (val === 'jkt-bdg') {
                this.tarifRute = 120000;
                this.kursiTerisi = [1, 5];
            } else if (val === 'yog-sub') {
                this.tarifRute = 180000;
                this.kursiTerisi = [2, 4];
            } else if (val === 'smg-mlg') {
                this.tarifRute = 195000;
                this.kursiTerisi = [1, 2, 3];
            } else if (val === 'med-pku') {
                this.tarifRute = 240000;
                this.kursiTerisi = [4];
            }
            if (this.kursiTerisi.includes(this.kursiTerpilih)) {
                this.kursiTerpilih = null;
            }
        },

        pilihKursi(no) {
            if (this.kursiTerisi.includes(no)) {
                alert('Kursi #' + no + ' sudah dipesan oleh penumpang lain. Silakan pilih nomor kursi yang kosong.');
                return;
            }
            this.kursiTerpilih = no;
        },

        submitLoket() {
            if (!this.kursiTerpilih) {
                alert('Harap pilih nomor kursi pada denah visual armada terlebih dahulu.');
                return;
            }
            if (!this.namaPenumpang.trim() || !this.teleponPenumpang.trim()) {
                alert('Harap lengkapi nama dan nomor telepon penumpang.');
                return;
            }
            this.modalCetak = true;
        }
     }">

    @include('operator._nav', [
        'judul' => 'Kasir Loket & Penumpang Walk-in',
        'deskripsi' => 'Layanan reservasi langsung di konter loket stasiun/terminal untuk penumpang tanpa aplikasi.'
    ])

    <form @submit.prevent="submitLoket()" class="space-y-8" data-aos="fade-up" data-aos-duration="500">
        <!-- Grid 2 Kolom: Pilihan Rute & Denah Kursi -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Kolom Kiri: Rute & Denah Visual Kursi -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Box Pilihan Jadwal & Rute -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-200/80 flex items-center justify-center text-sky-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-heading font-bold text-slate-900 text-sm">Pilih Trayek & Jadwal Armada</h2>
                            <div class="text-[11px] text-slate-500">Rute travel antar kota se-Indonesia</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                Trayek Rute Keberangkatan
                            </label>
                            <div class="relative">
                                <select @change="gantiRute($event)"
                                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition appearance-none cursor-pointer">
                                    <option value="jkt-bdg">Jakarta (Gambir) → Bandung (Dipatiukur) — Rp 120.000</option>
                                    <option value="yog-sub">Yogyakarta (Malioboro) → Surabaya (Gubeng) — Rp 180.000</option>
                                    <option value="smg-mlg">Semarang (Simpang Lima) → Malang (Arjosari) — Rp 195.000</option>
                                    <option value="med-pku">Medan (Amplas) → Pekanbaru (Arengka) — Rp 240.000</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Tanggal Berangkat
                                </label>
                                <input type="date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Jam Operasional
                                </label>
                                <input type="text" value="08:30 WIB (Pagi)" readonly
                                       class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 font-semibold cursor-not-allowed">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Box Denah Visual Pemilihan Kursi (Kapasitas 5 Eksklusif) -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="font-heading font-bold text-slate-900 text-sm">Denah Kursi Armada (Kapasitas 5)</h2>
                            <p class="text-[11px] text-slate-500">Pilih salah satu kursi yang masih berstatus kosong</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200">
                            Terpilih: Kursi #<span x-text="kursiTerpilih || 'Belum dipilih'"></span>
                        </span>
                    </div>

                    <!-- Layout Denah Kabin Mini Travel -->
                    <div class="p-6 bg-slate-900 rounded-2xl border border-slate-800 text-white max-w-sm mx-auto">
                        <!-- Depan: Pengemudi & Kursi 1 -->
                        <div class="text-[10px] uppercase font-semibold text-slate-400 text-center mb-4 tracking-wider">
                            Depan (Area Kemudi Sopir)
                        </div>

                        <!-- Baris 1: Sopir + Kursi Depan (Seat 1) -->
                        <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-800">
                            <div class="h-16 rounded-xl bg-slate-800 border border-slate-700 flex flex-col items-center justify-center text-slate-400">
                                <span class="text-xs font-bold">KEMUDI</span>
                                <span class="text-[10px] text-slate-500">Sopir Gotix</span>
                            </div>

                            <button type="button"
                                    @click="pilihKursi(1)"
                                    :class="kursiTerisi.includes(1) ? 'bg-rose-950/60 border-rose-800 text-rose-300 cursor-not-allowed' : (kursiTerpilih === 1 ? 'bg-sky-600 border-sky-400 text-white ring-2 ring-sky-300' : 'bg-slate-800 border-slate-700 hover:border-sky-500 text-slate-200 cursor-pointer')"
                                    class="h-16 rounded-xl border flex flex-col items-center justify-center transition shadow-xs">
                                <span class="font-heading font-bold text-base">Seat 01</span>
                                <span class="text-[10px]" x-text="kursiTerisi.includes(1) ? 'Terisi' : (kursiTerpilih === 1 ? 'Dipilih' : 'Kosong')"></span>
                            </button>
                        </div>

                        <!-- Baris 2: Kursi Tengah (Seat 2 & 3) -->
                        <div class="grid grid-cols-2 gap-4 py-4 border-b border-slate-800">
                            <button type="button"
                                    @click="pilihKursi(2)"
                                    :class="kursiTerisi.includes(2) ? 'bg-rose-950/60 border-rose-800 text-rose-300 cursor-not-allowed' : (kursiTerpilih === 2 ? 'bg-sky-600 border-sky-400 text-white ring-2 ring-sky-300' : 'bg-slate-800 border-slate-700 hover:border-sky-500 text-slate-200 cursor-pointer')"
                                    class="h-16 rounded-xl border flex flex-col items-center justify-center transition shadow-xs">
                                <span class="font-heading font-bold text-base">Seat 02</span>
                                <span class="text-[10px]" x-text="kursiTerisi.includes(2) ? 'Terisi' : (kursiTerpilih === 2 ? 'Dipilih' : 'Kosong')"></span>
                            </button>

                            <button type="button"
                                    @click="pilihKursi(3)"
                                    :class="kursiTerisi.includes(3) ? 'bg-rose-950/60 border-rose-800 text-rose-300 cursor-not-allowed' : (kursiTerpilih === 3 ? 'bg-sky-600 border-sky-400 text-white ring-2 ring-sky-300' : 'bg-slate-800 border-slate-700 hover:border-sky-500 text-slate-200 cursor-pointer')"
                                    class="h-16 rounded-xl border flex flex-col items-center justify-center transition shadow-xs">
                                <span class="font-heading font-bold text-base">Seat 03</span>
                                <span class="text-[10px]" x-text="kursiTerisi.includes(3) ? 'Terisi' : (kursiTerpilih === 3 ? 'Dipilih' : 'Kosong')"></span>
                            </button>
                        </div>

                        <!-- Baris 3: Kursi Belakang (Seat 4 & 5) -->
                        <div class="grid grid-cols-2 gap-4 pt-4">
                            <button type="button"
                                    @click="pilihKursi(4)"
                                    :class="kursiTerisi.includes(4) ? 'bg-rose-950/60 border-rose-800 text-rose-300 cursor-not-allowed' : (kursiTerpilih === 4 ? 'bg-sky-600 border-sky-400 text-white ring-2 ring-sky-300' : 'bg-slate-800 border-slate-700 hover:border-sky-500 text-slate-200 cursor-pointer')"
                                    class="h-16 rounded-xl border flex flex-col items-center justify-center transition shadow-xs">
                                <span class="font-heading font-bold text-base">Seat 04</span>
                                <span class="text-[10px]" x-text="kursiTerisi.includes(4) ? 'Terisi' : (kursiTerpilih === 4 ? 'Dipilih' : 'Kosong')"></span>
                            </button>

                            <button type="button"
                                    @click="pilihKursi(5)"
                                    :class="kursiTerisi.includes(5) ? 'bg-rose-950/60 border-rose-800 text-rose-300 cursor-not-allowed' : (kursiTerpilih === 5 ? 'bg-sky-600 border-sky-400 text-white ring-2 ring-sky-300' : 'bg-slate-800 border-slate-700 hover:border-sky-500 text-slate-200 cursor-pointer')"
                                    class="h-16 rounded-xl border flex flex-col items-center justify-center transition shadow-xs">
                                <span class="font-heading font-bold text-base">Seat 05</span>
                                <span class="text-[10px]" x-text="kursiTerisi.includes(5) ? 'Terisi' : (kursiTerpilih === 5 ? 'Dipilih' : 'Kosong')"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Legenda Denah -->
                    <div class="flex items-center justify-center gap-6 text-xs text-slate-600 pt-2">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded bg-slate-800 border border-slate-600"></span>
                            <span>Kosong</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded bg-sky-600"></span>
                            <span>Pilihan Anda</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded bg-rose-900 border border-rose-700"></span>
                            <span>Sudah Terisi</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Data Penumpang & Pembayaran Loket -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Box Data Penumpang Offline -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4">
                    <h2 class="font-heading font-bold text-slate-900 text-sm pb-2 border-b border-slate-100">
                        Identitas Penumpang Walk-in
                    </h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Penumpang</label>
                        <input type="text" x-model="namaPenumpang" required placeholder="Contoh: Hendra Kusuma"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                        <input type="text" x-model="teleponPenumpang" required placeholder="Contoh: 0813-8899-2211"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Kelamin</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-semibold"
                                   :class="jenisKelamin === 'L' ? 'bg-sky-50 border-sky-500 text-sky-800' : 'border-slate-200 text-slate-700'">
                                <input type="radio" name="jk" value="L" x-model="jenisKelamin" class="sr-only">
                                <span>Laki-laki</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-semibold"
                                   :class="jenisKelamin === 'P' ? 'bg-sky-50 border-sky-500 text-sky-800' : 'border-slate-200 text-slate-700'">
                                <input type="radio" name="jk" value="P" x-model="jenisKelamin" class="sr-only">
                                <span>Perempuan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Box Metode Pembayaran Loket & Ringkasan -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4">
                    <h2 class="font-heading font-bold text-slate-900 text-sm pb-2 border-b border-slate-100">
                        Metode Pembayaran Loket
                    </h2>

                    <!-- Pilihan Tunai / QRIS -->
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex flex-col items-center justify-center p-3 border rounded-2xl cursor-pointer text-center transition"
                               :class="metodeBayar === 'tunai' ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" name="metode" value="tunai" x-model="metodeBayar" class="sr-only">
                            <svg class="w-5 h-5 mb-1 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v1.5a.75.75 0 0 0 .75.75H3m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m-15-12v15m0 0h15" />
                            </svg>
                            <span class="text-xs font-bold">Tunai (Cash)</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-3 border rounded-2xl cursor-pointer text-center transition"
                               :class="metodeBayar === 'qris' ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                            <input type="radio" name="metode" value="qris" x-model="metodeBayar" class="sr-only">
                            <svg class="w-5 h-5 mb-1 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                            </svg>
                            <span class="text-xs font-bold">QRIS Loket</span>
                        </label>
                    </div>

                    <!-- Total Tarif Tagihan -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Tarif Tiket Armada</span>
                            <span class="font-bold text-slate-900" x-text="'Rp ' + tarifRute.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Status Verifikasi</span>
                            <span class="font-bold text-emerald-600">Lunas Instan (Loket)</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Total Wajib Bayar</span>
                            <span class="font-heading text-xl font-extrabold text-sky-600" x-text="'Rp ' + tarifRute.toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <!-- Tombol Cetak Tiket Loket Instan -->
                    <button type="submit"
                            class="w-full py-3.5 px-4 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm shadow-sky-600/20 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.641-2.062-1.17-2.915a11.96 11.96 0 0 1 7.45-7.45c.853.529 1.839.93 2.915 1.17a11.96 11.96 0 0 1-7.45 7.45c-.853-.529-1.839-.93-2.915-1.17ZM6 18v3h12v-3m0-6V4H6v8m12 0H6" />
                        </svg>
                        <span>Cetak Tiket Loket Instan</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Cetak Struk Tiket Loket Sukses -->
    <div x-show="modalCetak"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-cloak>
        <div @click.away="modalCetak = false"
             class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-5">
            <!-- Icon Sukses -->
            <div class="text-center">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <h3 class="font-heading font-extrabold text-slate-900 text-lg">Tiket Loket Berhasil Diterbitkan</h3>
                <p class="text-xs text-slate-500 mt-1">Pembayaran telah diterima dan nomor kursi berhasil dikunci di sistem.</p>
            </div>

            <!-- Rincian Struk Tiket Kasir Thermal Model -->
            <div class="bg-slate-50 border border-dashed border-slate-300 rounded-2xl p-5 font-mono text-xs space-y-3">
                <div class="text-center font-bold text-slate-900 border-b border-dashed border-slate-300 pb-2">
                    GOTIX TRAVEL INDONESIA<br>
                    <span class="text-[10px] text-slate-500 font-normal">Loket Konter Resmi Gotix</span>
                </div>
                <div class="space-y-1 text-slate-700">
                    <div class="flex justify-between">
                        <span>KODE:</span>
                        <span class="font-bold text-slate-900" x-text="kodeTiketInstan"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>PENUMPANG:</span>
                        <span class="font-bold" x-text="namaPenumpang"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>KURSI:</span>
                        <span class="font-bold text-sky-600" x-text="'SEAT #' + kursiTerpilih"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>METODE:</span>
                        <span class="uppercase font-semibold" x-text="metodeBayar"></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-dashed border-slate-300 text-sm font-bold text-slate-900">
                        <span>TOTAL:</span>
                        <span x-text="'Rp ' + tarifRute.toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="window.print()"
                        class="flex-1 py-3 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.641-2.062-1.17-2.915a11.96 11.96 0 0 1 7.45-7.45c.853.529 1.839.93 2.915 1.17a11.96 11.96 0 0 1-7.45 7.45c-.853-.529-1.839-.93-2.915-1.17ZM6 18v3h12v-3m0-6V4H6v8m12 0H6" />
                    </svg>
                    <span>Cetak Kertas Thermal</span>
                </button>
                <button type="button" @click="modalCetak = false; window.location.reload();"
                        class="py-3 px-4 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

