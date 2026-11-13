@extends('layouts.operator')

@section('title', 'Kasir Pemesanan Loket (Walk-in) — Operator Gotix')

@section('konten')
@php
    $jadwalJs = $jadwalAktif->map(function ($j) {
        $terisi = $j->pesanan
            ->filter(fn($p) => in_array($p->status->value, ['Belum Bayar', 'Dalam Proses', 'Lunas']))
            ->pluck('nomor_kursi')
            ->values()
            ->all();

        return [
            'id' => $j->id,
            'asal' => $j->kotaAsal->nama_kota ?? 'Kota Asal',
            'tujuan' => $j->kotaTujuan->nama_kota ?? 'Kota Tujuan',
            'tanggal' => $j->tanggal_berangkat ? $j->tanggal_berangkat->format('d M Y') : '-',
            'jam' => $j->jam_berangkat ? substr((string) $j->jam_berangkat, 0, 5) . ' WIB' : '-',
            'harga' => (int) $j->harga,
            'armada' => ($j->kendaraan->jenis_kendaraan ?? 'Travel') . ' (' . ($j->kendaraan->nomor_polisi ?? '-') . ')',
            'kapasitas' => (int) ($j->kendaraan->kapasitas_kursi ?? config('gotix.kapasitas_kursi', 5)),
            'kursi_terisi' => $terisi,
        ];
    });

    $defaultJadwalId = $jadwalJs->first()['id'] ?? 0;
@endphp

<div class="space-y-6 sm:space-y-8"
     x-data="{
        jadwalList: @js($jadwalJs),
        jadwalId: {{ $defaultJadwalId }},
        kursiTerpilih: null,
        tipePenumpang: '{{ old('tipe_penumpang', 'walkin') }}',

        get currentJadwal() {
            return this.jadwalList.find(j => j.id == this.jadwalId) || null;
        },

        get kapasitas() {
            return this.currentJadwal ? this.currentJadwal.kapasitas : 5;
        },

        get kursiTerisi() {
            return this.currentJadwal ? this.currentJadwal.kursi_terisi : [];
        },

        get tarif() {
            return this.currentJadwal ? this.currentJadwal.harga : 0;
        },

        pilihKursi(no) {
            if (this.kursiTerisi.includes(no)) {
                return;
            }
            this.kursiTerpilih = no;
        },

        formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }
     }">

    @include('operator._nav', [
        'judul' => 'Kasir Loket & Penumpang Walk-in',
        'deskripsi' => 'Layanan penjualan tiket langsung di loket terminal untuk penumpang umum (Walk-in) maupun pelanggan Member.'
    ])

    <!-- Flash Notifications -->
    @if (session('sukses'))
        <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] text-xs sm:text-sm font-bold text-emerald-950 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 border border-slate-900 text-slate-950 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] text-xs sm:text-sm font-bold text-rose-950 space-y-1">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>Kendala Validasi Transaksi Loket:</span>
            </div>
            <ul class="list-disc list-inside text-rose-800 space-y-0.5 pl-6 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($jadwalAktif->isEmpty())
        <div class="text-center py-16 bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[4px_4px_0px_#18181b]">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 border-2 border-slate-900 text-slate-950 flex items-center justify-center mx-auto mb-3 shadow-[2px_2px_0px_#18181b]">
                <svg class="w-7 h-7 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h3 class="font-heading font-black text-slate-950 text-xl">Tidak Ada Jadwal Keberangkatan Aktif</h3>
            <p class="text-xs text-slate-500 mt-1 font-medium">Saat ini belum ada jadwal armada aktif di masa mendatang yang siap dipesan dari loket.</p>
        </div>
    @else
        <!-- Split-View Kasir Loket (Desktop 12 Kolom, Mobile 1 Kolom) -->
        <form action="{{ route('operator.loket.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Hidden Input Kursi Terpilih & Tipe Penumpang -->
            <input type="hidden" name="nomor_kursi" :value="kursiTerpilih" required>
            <input type="hidden" name="tipe_penumpang" :value="tipePenumpang">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                <!-- Kolom Kiri: Pemilihan Jadwal & Denah Kursi Real (lg:col-span-7) -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Box Pilihan Trayek & Jadwal -->
                    <x-card title="Pilih Trayek & Jadwal Keberangkatan"
                            subtitle="Pilih trayek armada aktif untuk keberangkatan penumpang">
                        <x-slot:headerAction>
                            <a href="{{ route('jadwal.cari') }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs font-heading font-bold text-slate-700 hover:text-amber-600 transition"
                               title="Buka halaman pencarian jadwal publik di tab baru">
                                <span>Katalog Publik</span>
                                <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        </x-slot:headerAction>

                        <div class="space-y-4">
                            <div>
                                <label for="jadwal_id" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                                    Trayek & Waktu Keberangkatan <span class="text-rose-600 font-black">*</span>
                                </label>
                                <div class="relative">
                                    <select name="jadwal_id" id="jadwal_id" required
                                            x-model="jadwalId"
                                            @change="kursiTerpilih = null"
                                            class="w-full bg-white border-2 border-slate-900 rounded-xl px-3.5 py-3 text-xs sm:text-sm font-bold text-slate-900 focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] shadow-[2px_2px_0px_#18181b] transition appearance-none cursor-pointer">
                                        @foreach ($jadwalAktif as $jadwal)
                                            @php
                                                $ruteStr = ($jadwal->kotaAsal->nama_kota ?? '-') . ' → ' . ($jadwal->kotaTujuan->nama_kota ?? '-');
                                                $tglStr = $jadwal->tanggal_berangkat ? $jadwal->tanggal_berangkat->format('d M Y') : '-';
                                                $jamStr = $jadwal->jam_berangkat ? substr((string) $jadwal->jam_berangkat, 0, 5) : '-';
                                                $hrgStr = 'Rp ' . number_format($jadwal->harga, 0, ',', '.');
                                            @endphp
                                            <option value="{{ $jadwal->id }}" {{ old('jadwal_id', $defaultJadwalId) == $jadwal->id ? 'selected' : '' }}>
                                                {{ $ruteStr }} ({{ $tglStr }} {{ $jamStr }} WIB) — {{ $hrgStr }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-900">
                                        <svg class="w-4 h-4 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Jadwal Aktif Highlight -->
                            <template x-if="currentJadwal">
                                <div class="p-4 bg-amber-50 rounded-xl border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                    <div>
                                        <div class="font-heading font-black text-slate-950 text-sm">
                                            <span x-text="currentJadwal.asal"></span> &rarr; <span x-text="currentJadwal.tujuan"></span>
                                        </div>
                                        <div class="text-[11px] text-slate-600 font-medium mt-0.5">
                                            Armada: <span class="font-bold text-slate-900" x-text="currentJadwal.armada"></span>
                                            &bull; Berangkat: <span class="font-bold text-slate-900" x-text="currentJadwal.tanggal + ', ' + currentJadwal.jam"></span>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-900/10">
                                        <div class="font-heading font-black text-amber-950 text-base" x-text="formatRupiah(currentJadwal.harga)"></div>
                                        <div class="text-[10px] font-bold text-slate-600" x-text="'Kapasitas: ' + currentJadwal.kapasitas + ' Kursi'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </x-card>

                    <!-- Box Denah Kursi Real -->
                    <x-card title="Pilih Nomor Kursi Armada"
                            subtitle="Klik salah satu kursi kosong pada denah tata letak armada">
                        <x-slot:headerAction>
                            <!-- Legenda Kursi Taktil -->
                            <div class="flex items-center gap-3 text-xs font-heading font-bold">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3.5 h-3.5 rounded-md bg-amber-500 border border-slate-900 shadow-[1px_1px_0px_#18181b]"></span>
                                    <span>Dipilih</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3.5 h-3.5 rounded-md bg-white border border-slate-900 shadow-[1px_1px_0px_#18181b]"></span>
                                    <span>Kosong</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3.5 h-3.5 rounded-md bg-slate-200 border border-slate-400 bg-hatch-pattern"></span>
                                    <span>Terisi</span>
                                </span>
                            </div>
                        </x-slot:headerAction>

                        <div class="pt-2">
                            <!-- Indikator Bagian Depan Kendaraan / Supir -->
                            <div class="mb-4 pb-3 border-b-2 border-slate-200 flex items-center justify-between text-xs text-slate-500 font-bold uppercase tracking-wider">
                                <span class="flex items-center gap-1.5 text-slate-700">
                                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.002A11.959 11.959 0 0 1 12 2.714Z" />
                                    </svg>
                                    Depan / Area Sopir
                                </span>
                                <span class="text-[10px] text-slate-400">Pintu Masuk</span>
                            </div>

                            <!-- Dynamic Seat Grid -->
                            <div class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                                <template x-for="seatNo in Array.from({length: kapasitas}, (_, i) => i + 1)" :key="seatNo">
                                    <button type="button"
                                            @click="pilihKursi(seatNo)"
                                            :disabled="kursiTerisi.includes(seatNo)"
                                            :class="{
                                                'bg-slate-200 border-2 border-slate-400 text-slate-400 bg-hatch-pattern cursor-not-allowed': kursiTerisi.includes(seatNo),
                                                'bg-amber-500 border-2 border-slate-900 text-slate-950 shadow-[3px_3px_0px_#18181b] -translate-y-0.5 ring-2 ring-amber-400': kursiTerpilih === seatNo,
                                                'bg-white border-2 border-slate-900 text-slate-900 hover:bg-amber-100 hover:border-amber-600 shadow-[2px_2px_0px_#18181b] active:translate-y-0.5 cursor-pointer': !kursiTerisi.includes(seatNo) && kursiTerpilih !== seatNo
                                            }"
                                            class="rounded-xl p-3 text-center transition flex flex-col items-center justify-center select-none">
                                        <span class="text-[9px] font-heading font-black uppercase tracking-wider">Seat</span>
                                        <span class="font-heading text-lg font-black" x-text="seatNo"></span>
                                        <span class="text-[9px] font-bold mt-0.5"
                                              x-text="kursiTerisi.includes(seatNo) ? 'Terisi' : (kursiTerpilih === seatNo ? 'Dipilih' : 'Kosong')"></span>
                                    </button>
                                </template>
                            </div>

                            <p class="text-[11px] text-slate-500 mt-4 font-medium text-center">
                                Pastikan nomor kursi yang dipilih sesuai dengan preferensi penumpang di konter loket.
                            </p>
                        </div>
                    </x-card>
                </div>

                <!-- Kolom Kanan: Data Penumpang & Pembayaran Loket (lg:col-span-5) -->
                <div class="lg:col-span-5 space-y-6">
                    <x-card title="Identitas Penumpang & Bayar"
                            subtitle="Catat data penumpang dan terbitkan tiket resmi loket">
                        <div class="space-y-5">
                            <!-- Pilihan Tipe Penumpang -->
                            <div>
                                <span class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-2">
                                    Tipe Pelanggan Loket
                                </span>
                                <div class="grid grid-cols-2 gap-2 p-1 bg-stone-100 border-2 border-slate-900 rounded-xl">
                                    <label class="flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-heading font-black cursor-pointer transition select-none"
                                           :class="tipePenumpang === 'walkin' ? 'bg-amber-500 text-slate-950 shadow-[1.5px_1.5px_0px_#18181b]' : 'text-slate-600 hover:text-slate-900'">
                                        <input type="radio" name="_tipe" value="walkin" x-model="tipePenumpang" class="sr-only">
                                        <span>Walk-in (Umum)</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-heading font-black cursor-pointer transition select-none"
                                           :class="tipePenumpang === 'member' ? 'bg-amber-500 text-slate-950 shadow-[1.5px_1.5px_0px_#18181b]' : 'text-slate-600 hover:text-slate-900'">
                                        <input type="radio" name="_tipe" value="member" x-model="tipePenumpang" class="sr-only">
                                        <span>Member Terdaftar</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Input Form Walk-in Non-Member -->
                            <div x-show="tipePenumpang === 'walkin'" class="space-y-4">
                                <div>
                                    <x-input name="nama_penumpang"
                                             id="nama_penumpang"
                                             label="Nama Lengkap Penumpang"
                                             placeholder="Contoh: Ahmad Fauzi"
                                             value="{{ old('nama_penumpang') }}"
                                             hint="Nama yang tercantum pada E-Tiket travel" />
                                </div>

                                <div>
                                    <x-input name="telepon_penumpang"
                                             id="telepon_penumpang"
                                             label="Nomor Telepon / WhatsApp"
                                             placeholder="Contoh: 081234567890"
                                             value="{{ old('telepon_penumpang') }}"
                                             hint="Nomor aktif untuk konfirmasi keberangkatan" />
                                </div>
                            </div>

                            <!-- Input Form Member Terdaftar -->
                            <div x-show="tipePenumpang === 'member'" class="space-y-2">
                                <label for="pengguna_id" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
                                    Pilih Akun Member Pelanggan
                                </label>
                                <div class="relative">
                                    <select name="pengguna_id" id="pengguna_id"
                                            class="w-full bg-white border-2 border-slate-900 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-900 focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] shadow-[2px_2px_0px_#18181b] transition appearance-none cursor-pointer">
                                        <option value="" {{ old('pengguna_id') ? '' : 'selected' }}>-- Pilih dari daftar member --</option>
                                        @foreach ($daftarMember as $member)
                                            <option value="{{ $member->id }}" {{ old('pengguna_id') == $member->id ? 'selected' : '' }}>
                                                {{ $member->nama }} ({{ $member->telepon ?? $member->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-900">
                                        <svg class="w-4 h-4 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium">
                                    Data nama & nomor telepon akan otomatis terisi dari profil member jika input walk-in dikosongkan.
                                </p>
                            </div>

                            <!-- Ringkasan Transaksi & Tarif -->
                            <div class="p-4 rounded-xl bg-stone-50 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] space-y-2.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-600 font-medium">Nomor Kursi:</span>
                                    <span class="font-heading font-black text-sm text-slate-950"
                                          x-text="kursiTerpilih ? 'Kursi #' + kursiTerpilih : 'Belum Dipilih'"></span>
                                </div>

                                <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200">
                                    <span class="text-slate-600 font-medium">Total Tarif Tagihan:</span>
                                    <span class="font-heading font-black text-base sm:text-lg text-slate-950"
                                          x-text="formatRupiah(tarif)"></span>
                                </div>

                                <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200">
                                    <span class="text-slate-600 font-medium">Status Transaksi:</span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-heading font-black uppercase tracking-wider bg-emerald-200 text-emerald-950 border border-slate-900">
                                        Langsung Lunas
                                    </span>
                                </div>

                                <div class="text-[10px] text-slate-500 pt-1.5 border-t border-slate-200">
                                    Petugas: <strong class="text-slate-900">{{ Auth::user()->nama }}</strong> &bull; Pesanan otomatis tercatat sebagai transaksi loket resmi.
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <x-button variant="primary" size="lg" type="submit" class="w-full"
                                          ::disabled="!kursiTerpilih"
                                          ::class="!kursiTerpilih ? 'opacity-60 cursor-not-allowed' : ''">
                                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                    </svg>
                                    <span>Proses & Terbitkan Tiket Loket</span>
                                </x-button>
                                <p class="text-[11px] text-slate-500 text-center mt-2 font-medium" x-show="!kursiTerpilih">
                                    Silakan klik salah satu kursi kosong pada denah sebelum memproses.
                                </p>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </form>
    @endif

    <!-- Akses Cepat ke Riwayat Transaksi -->
    <div class="mt-8 p-5 sm:p-6 rounded-2xl bg-stone-100 border-2 border-slate-900 shadow-[3px_3px_0px_#18181b] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-500 border-2 border-slate-900 flex items-center justify-center shrink-0 shadow-[2px_2px_0px_#18181b]">
                <svg class="w-5 h-5 stroke-[2.5] text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <h3 class="font-heading font-black text-slate-950 text-base">Arsip & Riwayat Transaksi Loket</h3>
                <p class="text-xs text-slate-600 font-medium mt-0.5">
                    Mencari transaksi pemesanan tiket loket sebelumnya? Buka halaman khusus riwayat transaksi.
                </p>
            </div>
        </div>

        <x-button href="{{ route('operator.riwayat.index') }}" variant="outline" size="md" class="shrink-0">
            <span>Buka Riwayat Transaksi &rarr;</span>
        </x-button>
    </div>
</div>
@endsection
