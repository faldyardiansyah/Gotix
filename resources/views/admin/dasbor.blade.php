@extends('layouts.tamu')

@section('title', 'Dasbor Kontrol Pusat — Admin Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @include('admin._nav', [
        'judul' => 'Dasbor Kontrol Pusat Gotix',
        'deskripsi' => 'Ringkasan operasional nasional, statistik pendapatan, keterisian kursi armada, dan tindak lanjut cepat.'
    ])

    <!-- Statistik Utama -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" data-aos="fade-up" data-aos-duration="400">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pendapatan Bulan Ini</div>
            <div class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Rp 48,2 Jt</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">&uarr; +18.4% target tercapai</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tiket Terjual</div>
            <div class="font-heading text-2xl sm:text-3xl font-extrabold text-sky-600 mt-1">324 Tiket</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Rata-rata 10-12 armada / hari</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</div>
            <div class="font-heading text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">3 Berkas</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Tugas operator loket aktif</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tingkat Keterisian Kursi</div>
            <div class="font-heading text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">86.5%</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Rata-rata 4.3 dari 5 kursi</div>
        </div>
    </div>

    <!-- Quick Access Master Data Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" data-aos="fade-up" data-aos-duration="500">
        <!-- Card 1: Master Jadwal -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-200 text-sky-600 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-slate-900 text-base">Kelola Jadwal & Trayek</h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Atur jam operasional, penugasan nomor polisi mobil, serta penetapan tarif resmi per kursi antar kota se-Indonesia.
                </p>
            </div>
            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">18 Jadwal Aktif</span>
                <a href="{{ route('admin.jadwal.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700">
                    Buka Jadwal &rarr;
                </a>
            </div>
        </div>

        <!-- Card 2: Master Armada & Kota -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-slate-900 text-base">Armada & Destinasi Kota</h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Kelola data unit mobil eksekutif berkapasitas 5 seat (HiAce, Elf, Innova) dan daftar kota asal/tujuan transit.
                </p>
            </div>
            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">20 Kota & 4 Unit</span>
                <a href="{{ route('admin.kendaraan.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700">
                    Buka Armada &rarr;
                </a>
            </div>
        </div>

        <!-- Card 3: Laporan & Penjualan -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-slate-900 text-base">Rekapitulasi Keuangan</h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Pantau arus kas masuk dari transfer perbankan penumpang serta penerimaan loket walk-in secara transparan.
                </p>
            </div>
            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Bebas Fee Pihak Ke-3</span>
                <a href="{{ route('admin.pesanan.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700">
                    Buka Laporan &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
