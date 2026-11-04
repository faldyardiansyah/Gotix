<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Operator — Gotix Travel')</title>

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

        <!-- Mobile Overlay -->
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

        <!-- Sidebar Operator -->
        <aside class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 bg-slate-900 border-r border-slate-700/60 transition-transform duration-200 ease-in-out
                      lg:static lg:translate-x-0 lg:z-auto lg:flex-shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-cloak>

            <!-- Brand -->
            <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-700/60 shrink-0">
                <div class="w-8 h-8 rounded-xl bg-sky-500 text-white flex items-center justify-center font-heading font-black text-sm shadow-sm">
                    G
                </div>
                <div>
                    <div class="font-heading text-base font-bold tracking-tight text-white leading-none">Gotix</div>
                    <div class="text-[10px] font-semibold text-sky-300 mt-0.5 uppercase tracking-wider">Operator Loket</div>
                </div>
            </div>

            <!-- Navigasi Operator -->
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 mb-3">Menu Operasional</div>

                <a href="{{ route('operator.pembayaran.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('operator.pembayaran.*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    <span>Verifikasi Pembayaran</span>
                </a>

                <a href="{{ route('operator.loket.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition duration-150
                          {{ request()->routeIs('operator.loket.*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v1.5a.75.75 0 0 0 .75.75H3m0 0h1.5m12.75 0a60.07 60.07 0 0 1-15.797 2.101M3.75 4.5h16.5M3.75 4.5a2.25 2.25 0 0 0-2.25 2.25v10.5a2.25 2.25 0 0 0 2.25 2.25h16.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H3.75Z" />
                    </svg>
                    <span>Penjualan Loket (Walk-in)</span>
                </a>
            </nav>

            <!-- Footer Profil & Logout -->
            <div class="border-t border-slate-700/60 p-4 shrink-0">
                @auth
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-700 text-sky-300 flex items-center justify-center font-heading font-bold text-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white truncate">{{ Auth::user()->nama }}</div>
                            <div class="text-[11px] text-slate-400 font-medium">Petugas Loket Aktif</div>
                        </div>
                    </div>
                    <form action="{{ route('keluar') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold text-slate-400 hover:text-white hover:bg-rose-600/20 border border-slate-700 hover:border-rose-800/60 rounded-xl transition duration-150 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            <span>Keluar Akun</span>
                        </button>
                    </form>
                @endauth

                <div class="mt-3 pt-3 border-t border-slate-700/60">
                    <a href="{{ route('beranda') }}"
                       class="flex items-center gap-2 text-xs font-medium text-slate-500 hover:text-slate-300 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Area Konten Kanan -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Header -->
            <header class="sticky top-0 z-10 flex items-center justify-between h-16 px-4 sm:px-6 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs shrink-0">
                <div class="flex items-center gap-4">
                    <button type="button" @click="sidebarOpen = true"
                            class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="font-heading text-sm sm:text-base font-bold text-slate-900 leading-tight">
                            @yield('page-title', 'Operator Dashboard')
                        </h1>
                        <p class="text-[11px] text-slate-400 font-medium hidden sm:block">
                            @yield('page-subtitle', 'Panel Operasional Loket & Verifikasi Pembayaran')
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <div class="hidden md:flex items-center gap-2">
                            <div class="text-right">
                                <div class="text-xs font-bold text-slate-800 leading-none">{{ Auth::user()->nama }}</div>
                                <div class="text-[11px] text-sky-600 font-semibold flex items-center justify-end gap-1 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                    <span>Operator Aktif</span>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('keluar') }}" method="POST" class="inline m-0">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                </svg>
                                <span class="hidden sm:inline">Keluar</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('konten')
            </main>
        </div>
    </div>
</body>
</html>

