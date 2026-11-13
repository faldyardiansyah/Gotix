@extends('layouts.tamu')

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

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        jadwalList: @js($jadwalJs),
        jadwalId: {{ $defaultJadwalId }},
        kursiTerpilih: null,

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
        'deskripsi' => 'Layanan reservasi langsung di konter loket stasiun/terminal untuk penumpang tiket antar kota.'
    ])

    @if (session('sukses'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center gap-2" data-aos="fade-down">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold">{{ session('sukses') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-xs text-rose-800 space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                Kendala Validasi Transaksi Loket:
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($jadwalAktif->isEmpty())
        <div class="text-center py-16 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h3 class="font-heading font-bold text-slate-900 text-lg">Tidak Ada Jadwal Keberangkatan Aktif</h3>
            <p class="text-xs text-slate-500 mt-1">Saat ini belum ada jadwal perjalanan masa mendatang yang siap dipesan.</p>
        </div>
    @else
        <form action="{{ route('operator.loket.store') }}" method="POST" class="space-y-8" data-aos="fade-up" data-aos-duration="500">
            @csrf

            <!-- Hidden Input Kursi Terpilih -->
            <input type="hidden" name="nomor_kursi" :value="kursiTerpilih" required>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Kolom Kiri: Pilihan Jadwal & Denah Kursi Real -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Box Pilihan Trayek -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-200/80 flex items-center justify-center text-sky-600">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-heading font-bold text-slate-900 text-sm">Pilih Trayek & Jadwal Keberangkatan</h2>
                                <div class="text-[11px] text-slate-500">Jadwal armada aktif dari basis data operasional Gotix</div>
                            </div>
                        </div>

                        <div>
                            <label for="jadwal_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                Trayek Rute Keberangkatan
                            </label>
                            <div class="relative">
                                <select name="jadwal_id" id="jadwal_id" required
                                        x-model="jadwalId"
                                        @change="kursiTerpilih = null"
                                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition appearance-none cursor-pointer">
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
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <template x-if="currentJadwal">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-900" x-text="currentJadwal.armada"></span>
                                    <div class="text-[11px] text-slate-500" x-text="'Keberangkatan: ' + currentJadwal.tanggal + ' ' + currentJadwal.jam"></div>
                                </div>
                                <div class="text-right">
                                    <div class="font-extrabold text-sky-600" x-text="formatRupiah(currentJadwal.harga)"></div>
                                    <div class="text-[10px] text-slate-400" x-text="'Kapasitas ' + currentJadwal.kapasitas + ' Seat'"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Denah Kursi Real -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="font-heading font-bold text-slate-900 text-sm">Pilih Nomor Kursi</h2>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-sky-600"></span> Dipilih</span>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-slate-100 border border-slate-300"></span> Kosong</span>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-rose-100 text-rose-500 border border-rose-200"></span> Terisi</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-3 pt-2">
                            <template x-for="seatNo in Array.from({length: kapasitas}, (_, i) => i + 1)" :key="seatNo">
                                <button type="button"
                                        @click="pilihKursi(seatNo)"
                                        :disabled="kursiTerisi.includes(seatNo)"
                                        :class="{
                                            'bg-rose-50 border-rose-200 text-rose-400 cursor-not-allowed': kursiTerisi.includes(seatNo),
                                            'bg-sky-600 text-white border-sky-600 shadow-sm ring-2 ring-sky-300': kursiTerpilih === seatNo,
                                            'bg-slate-50 border-slate-200 text-slate-700 hover:border-sky-400 hover:bg-sky-50/50': !kursiTerisi.includes(seatNo) && kursiTerpilih !== seatNo
                                        }"
                                        class="border rounded-2xl p-3 text-center transition flex flex-col items-center justify-center cursor-pointer">
                                    <span class="text-[10px] uppercase font-semibold">Seat</span>
                                    <span class="font-heading text-lg font-extrabold" x-text="seatNo"></span>
                                    <span class="text-[9px]" x-text="kursiTerisi.includes(seatNo) ? 'Terisi' : (kursiTerpilih === seatNo ? 'Dipilih' : 'Kosong')"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Identitas Penumpang & Pembayaran Loket -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-200/80 flex items-center justify-center text-sky-600">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-heading font-bold text-slate-900 text-sm">Akun / Identitas Penumpang</h2>
                                <div class="text-[11px] text-slate-500">Pilih akun member pelanggan loket</div>
                            </div>
                        </div>

                        <div x-data="{ tipePenumpang: '{{ old('tipe_penumpang', 'walkin') }}' }">
                            <div class="flex items-center gap-2 mb-3">
                                <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="radio" name="tipe_penumpang" value="walkin" x-model="tipePenumpang" class="text-sky-600 focus:ring-sky-500">
                                    <span>Walk-in (Non-Member)</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 cursor-pointer ml-3">
                                    <input type="radio" name="tipe_penumpang" value="member" x-model="tipePenumpang" class="text-sky-600 focus:ring-sky-500">
                                    <span>Member Terdaftar</span>
                                </label>
                            </div>

                            <!-- Input Walk-in Non-Member -->
                            <div x-show="tipePenumpang === 'walkin'" class="space-y-3">
                                <div>
                                    <label for="nama_penumpang" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                        Nama Lengkap Penumpang <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nama_penumpang" id="nama_penumpang" value="{{ old('nama_penumpang') }}" placeholder="Contoh: Ahmad Fauzi"
                                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>
                                <div>
                                    <label for="telepon_penumpang" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                        Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="telepon_penumpang" id="telepon_penumpang" value="{{ old('telepon_penumpang') }}" placeholder="Contoh: 081234567890"
                                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>
                            </div>

                            <!-- Input Member Terdaftar -->
                            <div x-show="tipePenumpang === 'member'">
                                <label for="pengguna_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Pilih Akun Member
                                </label>
                                <div class="relative">
                                    <select name="pengguna_id" id="pengguna_id"
                                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition appearance-none cursor-pointer">
                                        <option value="" {{ old('pengguna_id') ? '' : 'selected' }}>Pilih pelanggan member</option>
                                        @foreach ($daftarMember as $member)
                                            <option value="{{ $member->id }}" {{ old('pengguna_id') == $member->id ? 'selected' : '' }}>
                                                {{ $member->nama }} ({{ $member->telepon ?? $member->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Tarif & Konfirmasi Bayar Tunai -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 pt-4">
                            <div class="flex items-center justify-between text-xs text-slate-600">
                                <span>Total Tagihan Tiket</span>
                                <span class="font-extrabold text-sm text-slate-900" x-text="formatRupiah(tarif)"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-600">
                                <span>Kursi Dipilih</span>
                                <span class="font-bold text-sky-600" x-text="kursiTerpilih ? 'Kursi #' + kursiTerpilih : 'Belum Dipilih'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-200">
                                <span>Status Transaksi</span>
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 text-[11px]">
                                    Langsung Lunas
                                </span>
                            </div>
                        </div>

                        <button type="submit"
                                :disabled="!kursiTerpilih"
                                :class="kursiTerpilih ? 'bg-sky-600 hover:bg-sky-700 text-white cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="w-full py-3 text-xs font-bold rounded-xl transition duration-150 shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75" />
                            </svg>
                            <span>Proses & Cetak Tiket Loket</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif

    <!-- Riwayat Transaksi Loket Terbaru -->
    @if ($riwayatPesanan->isNotEmpty())
        <div class="mt-12 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4" data-aos="fade-up">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="font-heading font-bold text-slate-900 text-sm">Riwayat Transaksi Loket Terbaru</h2>
                    <div class="text-[11px] text-slate-500">Daftar reservasi konter yang telah berhasil diterbitkan</div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Status Lunas
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Kode Booking</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3">Rute & Waktu</th>
                            <th class="px-4 py-3 text-center">Kursi</th>
                            <th class="px-4 py-3 text-right">Tarif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($riwayatPesanan as $rp)
                            <tr class="hover:bg-slate-50/60 transition text-xs">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                    {{ $rp->kode_pesanan }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $rp->pengguna->nama ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $rp->jadwal->kotaAsal->nama_kota ?? '-' }} &rarr; {{ $rp->jadwal->kotaTujuan->nama_kota ?? '-' }}
                                    <div class="text-[10px] text-slate-400">
                                        {{ $rp->jadwal->tanggal_berangkat ? $rp->jadwal->tanggal_berangkat->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-bold">
                                    #{{ $rp->nomor_kursi }}
                                </td>
                                <td class="px-4 py-3 text-right font-extrabold text-slate-900">
                                    Rp {{ number_format($rp->harga_snapshot, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
