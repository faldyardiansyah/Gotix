@extends('layouts.admin')

@section('title', 'Dasbor Admin — GoTix.')
@section('page-title', 'Dasbor Operasional')
@section('page-subtitle', 'Ringkasan operasional Gotix Travel hari ini.')

@section('konten')

{{-- 1. Tindakan utama: pesanan yang menunggu verifikasi --}}
@if ($menanggguVerifikasi > 0)
    <div class="mb-8 rounded-2xl border-2 border-slate-900 bg-amber-400 p-6 shadow-[4px_4px_0px_#18181b] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white border-2 border-slate-900 flex items-center justify-center shrink-0 shadow-[2px_2px_0px_#18181b]">
                <svg class="w-6 h-6 text-slate-950 animate-bounce" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <h2 class="font-heading text-xl sm:text-2xl font-black text-slate-950 leading-tight">
                    {{ $menanggguVerifikasi }} Pesanan Menunggu Verifikasi
                </h2>
                <p class="text-xs sm:text-sm font-semibold text-slate-900/80 mt-0.5">
                    Penumpang telah mengunggah bukti transfer manual dan menanti konfirmasi Anda.
                </p>
            </div>
        </div>
        <x-button href="{{ route('admin.pesanan.index') }}" variant="dark" size="md" class="shrink-0">
            <span>Verifikasi Sekarang</span>
            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </x-button>
    </div>
@else
    <div class="mb-8 rounded-2xl border-2 border-slate-900 bg-emerald-100 p-5 shadow-[4px_4px_0px_#18181b] flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white border-2 border-slate-900 flex items-center justify-center shrink-0 shadow-[2px_2px_0px_#18181b]">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>
        <div>
            <div class="font-heading text-base font-black text-emerald-950">Semua pesanan sudah terverifikasi</div>
            <p class="text-xs font-semibold text-emerald-800">Tidak ada antrean pembayaran yang tertunda saat ini. Sistem operasional berjalan lancar.</p>
        </div>
    </div>
@endif

{{-- 2. Statistik Utama (4 Kartu Tactile) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Jadwal -->
    <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between hover:-translate-y-0.5 transition duration-150">
        <div class="flex items-center justify-between">
            <span class="text-xs font-heading font-black text-slate-500 uppercase tracking-wider">Jadwal Perjalanan</span>
            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-950 border-1.5 border-slate-900 flex items-center justify-center shadow-[1.5px_1.5px_0px_#18181b]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <div class="font-heading text-3xl sm:text-4xl font-black text-slate-950">{{ $totalJadwal }}</div>
            <p class="text-xs text-slate-600 font-semibold mt-1">Rute keberangkatan aktif</p>
        </div>
    </div>

    <!-- Pesanan Masuk -->
    <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between hover:-translate-y-0.5 transition duration-150">
        <div class="flex items-center justify-between">
            <span class="text-xs font-heading font-black text-slate-500 uppercase tracking-wider">Pesanan Masuk</span>
            <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-950 border-1.5 border-slate-900 flex items-center justify-center shadow-[1.5px_1.5px_0px_#18181b]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <div class="font-heading text-3xl sm:text-4xl font-black text-slate-950">{{ $totalPesanan }}</div>
            <p class="text-xs text-slate-600 font-semibold mt-1">Total transaksi tercatat</p>
        </div>
    </div>

    <!-- Pesanan Lunas -->
    <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between hover:-translate-y-0.5 transition duration-150">
        <div class="flex items-center justify-between">
            <span class="text-xs font-heading font-black text-slate-500 uppercase tracking-wider">Pesanan Lunas</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-950 border-1.5 border-slate-900 flex items-center justify-center shadow-[1.5px_1.5px_0px_#18181b]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <div class="font-heading text-3xl sm:text-4xl font-black text-emerald-600">{{ $totalLunas }}</div>
            <p class="text-xs text-slate-600 font-semibold mt-1">Tiket siap cetak & berangkat</p>
        </div>
    </div>

    <!-- Member Terdaftar -->
    <div class="bg-white border-2 border-slate-900 rounded-2xl p-5 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between hover:-translate-y-0.5 transition duration-150">
        <div class="flex items-center justify-between">
            <span class="text-xs font-heading font-black text-slate-500 uppercase tracking-wider">Pengguna Sistem</span>
            <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-950 border-1.5 border-slate-900 flex items-center justify-center shadow-[1.5px_1.5px_0px_#18181b]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <div class="font-heading text-3xl sm:text-4xl font-black text-slate-950">{{ $totalPengguna }}</div>
            <p class="text-xs text-slate-600 font-semibold mt-1">Admin, Operator & Member</p>
        </div>
    </div>
</div>

{{-- 3. Area Konten: Jadwal Terbaru & Panel Kelola --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Kolom Kiri: Tabel Jadwal Terbaru (2/3 lebar) -->
    <div class="lg:col-span-2">
        <x-card title="Jadwal Terbaru & Keterisian Kursi" subtitle="Keberangkatan terdekat beserta kuota 5 kursi terproteksi" variant="white">
            <x-slot:headerAction>
                <x-button href="{{ route('admin.jadwal.index') }}" variant="secondary" size="sm">
                    <span>Lihat Semua Jadwal</span>
                    <svg class="w-3.5 h-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </x-button>
            </x-slot:headerAction>

            <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
                <table class="w-full text-left text-sm">
                    <thead class="border-b-2 border-slate-900 bg-stone-100 text-xs font-heading font-black text-slate-800 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-5 py-3.5">Rute Perjalanan</th>
                            <th scope="col" class="px-4 py-3.5">Waktu Berangkat</th>
                            <th scope="col" class="px-4 py-3.5">Armada</th>
                            <th scope="col" class="px-4 py-3.5 text-right">Tarif</th>
                            <th scope="col" class="px-5 py-3.5 text-center">Status Kursi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 border-slate-900 divide-stone-200 font-medium">
                        @forelse ($jadwalTerbaru as $jadwal)
                            @php
                                $kapasitas = max((int) ($jadwal->kendaraan?->kapasitas_kursi ?? 5), 1);
                                $terisi = min(
                                    $jadwal->pesanan->whereIn('status', [
                                        \App\Enums\StatusPesanan::BelumBayar,
                                        \App\Enums\StatusPesanan::DalamProses,
                                        \App\Enums\StatusPesanan::Lunas,
                                    ])->count(),
                                    $kapasitas
                                );
                                $penuh = $terisi >= $kapasitas;
                                $tanggal = $jadwal->tanggal_berangkat
                                    ? \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->locale('id')->translatedFormat('D, d M Y')
                                    : '-';
                            @endphp
                            <tr class="hover:bg-amber-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="font-heading font-black text-slate-950 text-sm">
                                        {{ $jadwal->kotaAsal?->nama_kota ?? '-' }}
                                        <span class="text-amber-600 font-black px-1">&rarr;</span>
                                        {{ $jadwal->kotaTujuan?->nama_kota ?? '-' }}
                                    </div>
                                    <span class="text-[11px] text-slate-500 font-mono">ID: #JDW-{{ $jadwal->id }}</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-700">
                                    <div class="font-bold text-slate-900">{{ $tanggal }}</div>
                                    <div class="text-slate-500 font-semibold">{{ $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}</div>
                                </td>
                                <td class="px-4 py-4 text-xs text-slate-700">
                                    <div class="font-bold text-slate-900">{{ $jadwal->kendaraan?->jenis_kendaraan ?? '-' }}</div>
                                    <div class="text-slate-500 font-mono font-semibold">{{ $jadwal->kendaraan?->nomor_polisi ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 text-right font-heading font-black text-slate-950 text-sm whitespace-nowrap">
                                    Rp {{ number_format($jadwal->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-slate-900 {{ $penuh ? 'bg-rose-100 text-rose-950' : 'bg-emerald-100 text-emerald-950' }} text-xs font-heading font-extrabold shadow-[1px_1px_0px_#18181b]">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $penuh ? 'bg-rose-600' : 'bg-emerald-600' }}"></span>
                                        <span>{{ $terisi }}/{{ $kapasitas }} Kursi</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500 font-medium">
                                    Belum ada jadwal keberangkatan yang dijadwalkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <!-- Kolom Kanan: Akses Cepat Kelola Master Data (1/3 lebar) -->
    <div class="space-y-6">
        <x-card title="Pusat Kendali Master" subtitle="Kelola modul operasional utama" variant="paper">
            <div class="space-y-3">
                <a href="{{ route('admin.jadwal.index') }}"
                   class="group flex items-center justify-between p-3.5 rounded-xl border-2 border-slate-900 bg-white hover:bg-amber-100 shadow-[2px_2px_0px_#18181b] hover:-translate-y-0.5 transition duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-400 text-slate-950 border border-slate-900 flex items-center justify-center font-bold text-xs shadow-[1px_1px_0px_#18181b]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-heading font-black text-slate-950 group-hover:text-amber-950">Master Jadwal</div>
                            <div class="text-[11px] text-slate-500 font-semibold">Rute, jam & tarif travel</div>
                        </div>
                    </div>
                    <span class="w-6 h-6 rounded-full bg-slate-100 border border-slate-900 text-slate-900 flex items-center justify-center font-black text-xs">
                        {{ $totalJadwal }}
                    </span>
                </a>

                <a href="{{ route('admin.kendaraan.index') }}"
                   class="group flex items-center justify-between p-3.5 rounded-xl border-2 border-slate-900 bg-white hover:bg-amber-100 shadow-[2px_2px_0px_#18181b] hover:-translate-y-0.5 transition duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-sky-300 text-slate-950 border border-slate-900 flex items-center justify-center font-bold text-xs shadow-[1px_1px_0px_#18181b]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-heading font-black text-slate-950 group-hover:text-amber-950">Master Armada</div>
                            <div class="text-[11px] text-slate-500 font-semibold">Mobil 5 kursi, plat & warna</div>
                        </div>
                    </div>
                    <span class="w-6 h-6 rounded-full bg-slate-100 border border-slate-900 text-slate-900 flex items-center justify-center font-black text-xs">
                        {{ $totalKendaraan }}
                    </span>
                </a>

                <a href="{{ route('admin.kota.index') }}"
                   class="group flex items-center justify-between p-3.5 rounded-xl border-2 border-slate-900 bg-white hover:bg-amber-100 shadow-[2px_2px_0px_#18181b] hover:-translate-y-0.5 transition duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-300 text-slate-950 border border-slate-900 flex items-center justify-center font-bold text-xs shadow-[1px_1px_0px_#18181b]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-heading font-black text-slate-950 group-hover:text-amber-950">Master Kota</div>
                            <div class="text-[11px] text-slate-500 font-semibold">Daftar kota & terminal</div>
                        </div>
                    </div>
                    <span class="w-6 h-6 rounded-full bg-slate-100 border border-slate-900 text-slate-900 flex items-center justify-center font-black text-xs">
                        {{ $totalKota }}
                    </span>
                </a>

                <a href="{{ route('admin.keluhan.index') }}"
                   class="group flex items-center justify-between p-3.5 rounded-xl border-2 border-slate-900 bg-white hover:bg-amber-100 shadow-[2px_2px_0px_#18181b] hover:-translate-y-0.5 transition duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-rose-300 text-slate-950 border border-slate-900 flex items-center justify-center font-bold text-xs shadow-[1px_1px_0px_#18181b]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-heading font-black text-slate-950 group-hover:text-amber-950">Keluhan Member</div>
                            <div class="text-[11px] text-slate-500 font-semibold">Tanggapi masukan penumpang</div>
                        </div>
                    </div>
                    <span class="text-xs font-heading font-extrabold text-slate-400 group-hover:text-slate-900">&rarr;</span>
                </a>
            </div>

            <div class="mt-5 pt-4 border-t-2 border-slate-900">
                <x-button href="{{ route('admin.jadwal.create') }}" variant="primary" size="md" class="w-full">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Jadwal Baru</span>
                </x-button>
            </div>
        </x-card>
    </div>

</div>

@endsection
