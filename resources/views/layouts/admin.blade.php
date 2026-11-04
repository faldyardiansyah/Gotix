<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin — Gotix Travel')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-slate-100 antialiased font-sans text-slate-900 selection:bg-sky-600 selection:text-white"
      x-data="{ sidebarOpen: false }">

    <div class="flex h-full min-h-screen">

        <!-- ====================================
             SIDEBAR KIRI (Desktop Tetap, Mobile Overlay)
             ==================================== -->
        <!-- Mobile Overlay backdrop -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-in-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in-out duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-20 bg-slate-950/60 backdrop-blur-sm lg:hidden"
             x-cloak>
        </div>

        <!-- Sidebar Panel -->
        <aside class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 bg-slate-950 border-r border-slate-800/70 transition-transform duration-200 ease-in-out
                      lg:static lg:translate-x-0 lg:z-auto lg:flex-shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-cloak>

            <!-- Brand Logo di Sidebar -->
            <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-800/80 shrink-0">
                <div class="w-8 h-8 rounded-xl bg-sky-600 text-white flex items-center justify-center font-heading font-black text-sm shadow-sm">
                    G
                </div>
                <div>
                    <div class="font-heading text-base font-bold tracking-tight text-white leading-none">Gotix</div>
                    <div class="text-[10px] font-semibold text-sky-400 mt-0.5 uppercase tracking-wider">Admin Panel</div>
                </div>
            </div>

            <!-- Menu Navigasi Sidebar -->
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 mb-3">Menu Utama</div>

                <a href="{{ route('admin.dasbor') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('admin.dasbor') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                    <span>Dasbor</span>
                </a>

                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 pt-5 pb-2">Master Data</div>

                <a href="{{ route('admin.jadwal.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('admin.jadwal.*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Master Jadwal</span>
                </a>

                <a href="{{ route('admin.kota.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('admin.kota.*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span>Master Kota</span>
                </a>

                <a href="{{ route('admin.kendaraan.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('admin.kendaraan.*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <span>Master Armada</span>
                </a>

                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 pt-5 pb-2">Transaksi & Pengguna</div>

                <a href="{{ route('admin.pesanan.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('admin.pesanan.*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    <span>Daftar Pesanan</span>
                </a>

                <a href="{{ route('admin.pengguna.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('admin.pengguna.*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span>Data Pengguna</span>
                </a>

                <a href="{{ route('admin.keluhan.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('admin.keluhan.*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>Keluhan Member</span>
                </a>
            </nav>

            <!-- Footer Sidebar: Profil & Logout -->
            <div class="border-t border-slate-800/80 p-4 shrink-0">
                @auth
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 text-sky-400 flex items-center justify-center font-heading font-bold text-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white truncate">{{ Auth::user()->nama }}</div>
                            <div class="text-[11px] text-slate-400 font-medium">Administrator</div>
                        </div>
                    </div>
                    <form action="{{ route('keluar') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold text-slate-400 hover:text-white hover:bg-rose-600/20 border border-slate-800 hover:border-rose-800/60 rounded-xl transition duration-150 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            <span>Keluar Akun</span>
                        </button>
                    </form>
                @endauth

                <div class="mt-3 pt-3 border-t border-slate-800/60">
                    <a href="{{ route('beranda') }}"
                       class="flex items-center gap-2 text-xs font-medium text-slate-500 hover:text-slate-300 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span>Kembali ke Beranda Publik</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- ====================================
             AREA KONTEN UTAMA (Kanan)
             ==================================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Header Bar -->
            <header class="sticky top-0 z-10 flex items-center justify-between h-16 px-4 sm:px-6 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs shrink-0">
                <!-- Tombol Toggle Mobile + Judul Halaman -->
                <div class="flex items-center gap-4">
                    <button type="button" @click="sidebarOpen = true"
                            class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="font-heading text-sm sm:text-base font-bold text-slate-900 leading-tight">
                            @yield('page-title', 'Admin Dashboard')
                        </h1>
                        <p class="text-[11px] text-slate-400 font-medium hidden sm:block">
                            @yield('page-subtitle', 'Panel Kontrol Administrasi Gotix Travel')
                        </p>
                    </div>
                </div>

                <!-- Header Kanan: hanya profil (tombol keluar ada di sidebar bawah) -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="text-right">
                            <div class="text-xs font-bold text-slate-800 leading-none">{{ Auth::user()->nama }}</div>
                            <div class="text-[11px] text-emerald-600 font-semibold flex items-center justify-end gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Admin Aktif</span>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Konten Halaman -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('konten')
            </main>
        </div>
    </div>
</body>
</html>