@extends('layouts.tamu')

@section('konten')
    <!-- Hero Section -->
    <section class="relative bg-white border-b border-slate-200/80 py-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-3xl mx-auto text-center space-y-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-xs font-semibold text-sky-700">
                    ✨ Perjalanan Nyaman Antar Kota Lampung
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Pesan Tiket Travel Cepat, Aman, & Tanpa Ribet.
                </h1>
                <p class="text-lg text-slate-600">
                    Layanan transportasi terpercaya menghubungkan kota-kota di Provinsi Lampung dengan armada pilihan berkapasitas eksklusif.
                </p>

                <!-- Form Kotak Pencarian Jadwal Cepat -->
                <div class="pt-6">
                    <form action="{{ route('jadwal.cari') }}" method="GET" class="bg-slate-900 p-4 rounded-2xl shadow-xl flex flex-col md:flex-row gap-3">
                        <div class="flex-1 text-left">
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Kota Asal</label>
                            <input type="text" name="asal" placeholder="Misal: Bandar Lampung" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl text-sm focus:outline-none focus:border-sky-500">
                        </div>
                        <div class="flex-1 text-left">
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Kota Tujuan</label>
                            <input type="text" name="tujuan" placeholder="Misal: Pringsewu" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl text-sm focus:outline-none focus:border-sky-500">
                        </div>
                        <div class="flex-1 text-left">
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Berangkat</label>
                            <input type="date" name="tanggal" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl text-sm focus:outline-none focus:border-sky-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm rounded-xl transition-all shadow-sm">
                                Cari Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Unggulan -->
    <section id="fitur" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-12">
                <h2 class="text-2xl font-bold text-slate-900">Kenapa Memilih Gotix?</h2>
                <p class="text-slate-500 text-sm mt-1">Kami mengutamakan keamanan dan ketepatan waktu perjalanan Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-lg">💺</div>
                    <h3 class="font-bold text-slate-900 text-lg">Pilih Kursi Sendiri</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Bebas memilih posisi duduk di armada (kapasitas 5 kursi eksklusif) sesuai kenyamanan Anda.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-lg">🔒</div>
                    <h3 class="font-bold text-slate-900 text-lg">Antisipasi Kursi Ganda</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Sistem penguncian basis data ketat memastikan kursi yang Anda pesan tidak akan bisa dipesan orang lain.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-lg">⚡</div>
                    <h3 class="font-bold text-slate-900 text-lg">Proses Cepat</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Pemesanan praktis dengan konfirmasi transfer langsung diverifikasi oleh operator loket kami.</p>
                </div>
            </div>
        </div>
    </section>
@endsection