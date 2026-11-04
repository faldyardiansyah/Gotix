<div class="mb-8" data-aos="fade-down" data-aos-duration="400">
    <!-- Header Ringkas Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-sky-50 border border-sky-200/60 text-sky-700 text-xs font-semibold mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                Panel Kontrol & Master Data Nasional
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $judul ?? 'Pusat Manajemen Admin' }}
            </h1>
            <p class="text-sm text-slate-600 mt-1">
                {{ $deskripsi ?? 'Konfigurasi jadwal keberangkatan, armada kendaraan, tarif rute antar kota, dan pengguna sistem Gotix.' }}
            </p>
        </div>

        @auth
        <div class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm self-start sm:self-auto">
            <div class="w-9 h-9 rounded-lg bg-slate-900 text-white flex items-center justify-center font-heading font-bold text-sm">
                AD
            </div>
            <div class="text-left">
                <div class="text-xs font-semibold text-slate-900 line-clamp-1">{{ Auth::user()->nama ?? 'Administrator' }}</div>
                <div class="text-[11px] text-emerald-600 font-mono font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Hak Akses Penuh
                </div>
            </div>
        </div>
        @endauth
    </div>

    <!-- Sub-menu Navigasi Admin (Responsive Mobile/Desktop Scrollable) -->
    <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('admin.dasbor') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('admin.dasbor') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('admin.dasbor') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
            <span>Ringkasan Dasbor</span>
        </a>

        <a href="{{ route('admin.jadwal.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('admin.jadwal.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('admin.jadwal.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span>Master Jadwal</span>
        </a>

        <a href="{{ route('admin.kota.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('admin.kota.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('admin.kota.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
            </svg>
            <span>Master Kota</span>
        </a>

        <a href="{{ route('admin.kendaraan.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('admin.kendaraan.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('admin.kendaraan.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
            </svg>
            <span>Master Armada</span>
        </a>

        <a href="{{ route('admin.pengguna.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('admin.pengguna.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('admin.pengguna.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
            <span>Kelola Pengguna</span>
        </a>

        <a href="{{ route('admin.pesanan.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('admin.pesanan.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('admin.pesanan.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
            </svg>
            <span>Laporan & Transaksi</span>
        </a>
    </div>
</div>

