<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin — GoTix.')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700;800;900&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-stone-50 antialiased font-sans text-slate-900 selection:bg-amber-500 selection:text-slate-950"
      x-data="{ sidebarOpen: false }">

    <div class="flex h-full min-h-screen">

        <!-- ====================================
             SIDEBAR KIRI (Midnight Ink #18181B)
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
             class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden"
             x-cloak>
        </div>

        <!-- Sidebar Panel -->
        <aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#18181B] border-r-2 border-slate-900 transition-transform duration-200 ease-in-out
                      lg:static lg:translate-x-0 lg:z-auto lg:flex-shrink-0 shadow-[4px_0px_0px_#18181b]"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-cloak>

            <!-- Brand Logo di Sidebar -->
            <div class="flex items-center justify-between px-6 h-20 border-b-2 border-stone-800 shrink-0 bg-stone-900/60">
                <a href="{{ route('admin.dasbor') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-full bg-white border-2 border-slate-900 text-slate-950 flex items-center justify-center shadow-[2.5px_2.5px_0px_#f59e0b] transition-transform group-hover:rotate-6 group-hover:scale-105 duration-200 shrink-0">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="#18181B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 6v6m4-6v6m4-6v6M3 10v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9M3 10l9-7 9 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-heading text-xl font-black tracking-tight text-white leading-none">
                            GoTix<span class="text-amber-400">.</span>
                        </div>
                        <div class="inline-flex items-center gap-1.5 mt-1 px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[10px] font-heading font-black uppercase tracking-wider border border-slate-900 shadow-[1px_1px_0px_#18181b]">
                            Admin Panel
                        </div>
                    </div>
                </a>
                <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-stone-400 hover:text-white hover:bg-stone-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Menu Navigasi Sidebar -->
            <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
                <div class="text-[10px] font-heading font-black text-stone-400 uppercase tracking-widest px-3 mb-2">
                    Menu Utama
                </div>

                <a href="{{ route('admin.dasbor') }}"
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150
                          {{ request()->routeIs('admin.dasbor')
                              ? 'bg-amber-500 text-slate-950 font-heading font-black border-2 border-slate-900 shadow-[3px_3px_0px_#000]'
                              : 'text-stone-300 hover:text-white hover:bg-stone-900 font-bold border-2 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                    <span>Dasbor</span>
                </a>

                <div class="text-[10px] font-heading font-black text-stone-400 uppercase tracking-widest px-3 pt-5 pb-2">
                    Master Data
                </div>

                <a href="{{ route('admin.jadwal.index') }}"
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150
                          {{ request()->routeIs('admin.jadwal.*')
                              ? 'bg-amber-500 text-slate-950 font-heading font-black border-2 border-slate-900 shadow-[3px_3px_0px_#000]'
                              : 'text-stone-300 hover:text-white hover:bg-stone-900 font-bold border-2 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Master Jadwal</span>
                </a>

                <a href="{{ route('admin.kota.index') }}"
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150
                          {{ request()->routeIs('admin.kota.*')
                              ? 'bg-amber-500 text-slate-950 font-heading font-black border-2 border-slate-900 shadow-[3px_3px_0px_#000]'
                              : 'text-stone-300 hover:text-white hover:bg-stone-900 font-bold border-2 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span>Master Kota</span>
                </a>

                <a href="{{ route('admin.kendaraan.index') }}"
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150
                          {{ request()->routeIs('admin.kendaraan.*')
                              ? 'bg-amber-500 text-slate-950 font-heading font-black border-2 border-slate-900 shadow-[3px_3px_0px_#000]'
                              : 'text-stone-300 hover:text-white hover:bg-stone-900 font-bold border-2 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <span>Master Armada</span>
                </a>

                <div class="text-[10px] font-heading font-black text-stone-400 uppercase tracking-widest px-3 pt-5 pb-2">
                    Transaksi & Pengguna
                </div>

                <a href="{{ route('admin.pesanan.index') }}"
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150
                          {{ request()->routeIs('admin.pesanan.*')
                              ? 'bg-amber-500 text-slate-950 font-heading font-black border-2 border-slate-900 shadow-[3px_3px_0px_#000]'
                              : 'text-stone-300 hover:text-white hover:bg-stone-900 font-bold border-2 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    <span>Daftar Pesanan</span>
                </a>

                <a href="{{ route('admin.pengguna.index') }}"
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150
                          {{ request()->routeIs('admin.pengguna.*')
                              ? 'bg-amber-500 text-slate-950 font-heading font-black border-2 border-slate-900 shadow-[3px_3px_0px_#000]'
                              : 'text-stone-300 hover:text-white hover:bg-stone-900 font-bold border-2 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span>Data Pengguna</span>
                </a>

                <a href="{{ route('admin.keluhan.index') }}"
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150
                          {{ request()->routeIs('admin.keluhan.*')
                              ? 'bg-amber-500 text-slate-950 font-heading font-black border-2 border-slate-900 shadow-[3px_3px_0px_#000]'
                              : 'text-stone-300 hover:text-white hover:bg-stone-900 font-bold border-2 border-transparent' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>Keluhan Member</span>
                </a>
            </nav>

            <!-- Footer Sidebar: Profil & Logout -->
            <div class="border-t-2 border-stone-800 p-4 shrink-0 bg-stone-900/40 space-y-3">
                @auth
                    <div class="flex items-center gap-3 p-2 rounded-xl bg-stone-900 border border-stone-800">
                        <div class="w-9 h-9 rounded-lg bg-amber-400 text-slate-950 border-1.5 border-slate-900 flex items-center justify-center font-heading font-black text-sm shrink-0 shadow-[1px_1px_0px_#000]">
                            {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-white truncate">{{ Auth::user()->nama }}</div>
                            <div class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Administrator</span>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('keluar') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-heading font-bold text-stone-300 hover:text-white bg-stone-900 hover:bg-rose-600 border-2 border-stone-800 hover:border-slate-950 rounded-xl transition duration-150 shadow-[2px_2px_0px_#000] active:translate-y-0.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            <span>Keluar Akun</span>
                        </button>
                    </form>
                @endauth

                <div class="pt-2 border-t border-stone-800/80">
                    <a href="{{ route('beranda') }}"
                       class="flex items-center gap-2 text-xs font-bold text-stone-400 hover:text-amber-400 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
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
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- Top Header Bar (Tactile Style) -->
            <header class="sticky top-0 z-30 flex items-center justify-between h-20 px-4 sm:px-6 lg:px-8 bg-white/95 backdrop-blur-md border-b-2 border-slate-900 shadow-xs shrink-0">
                <!-- Tombol Toggle Mobile + Judul Halaman -->
                <div class="flex items-center gap-4">
                    <button type="button" @click="sidebarOpen = true"
                            class="lg:hidden p-2 rounded-xl text-slate-900 hover:bg-stone-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="font-heading text-lg sm:text-2xl font-black text-slate-950 leading-tight tracking-tight">
                            @yield('page-title', 'Dasbor Admin')
                        </h1>
                        <p class="text-xs text-slate-600 font-medium hidden sm:block">
                            @yield('page-subtitle', 'Panel Kontrol Administrasi GoTix Travel')
                        </p>
                    </div>
                </div>

                <!-- Header Kanan: Status Aktif Administrator -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full border-2 border-slate-900 bg-emerald-100 text-emerald-950 font-heading font-black text-xs shadow-[2px_2px_0px_#18181b]">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            <span>{{ Auth::user()->nama }} &bull; Admin Aktif</span>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Konten Halaman -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-stone-50">
                <!-- Flash Messages -->
                @if (session('sukses') || session('success') || session('status'))
                    <div class="mb-6 rounded-2xl border-2 border-slate-900 bg-emerald-50 text-emerald-950 p-4 sm:p-5 shadow-[4px_4px_0px_#18181b] flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white border-2 border-slate-900 flex items-center justify-center shrink-0 shadow-[1px_1px_0px_#18181b]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div class="text-xs sm:text-sm font-bold">
                            {{ session('sukses') ?? session('success') ?? session('status') }}
                        </div>
                    </div>
                @endif

                @if (session('gagal') || session('error'))
                    <div class="mb-6 rounded-2xl border-2 border-slate-900 bg-rose-50 text-rose-950 p-4 sm:p-5 shadow-[4px_4px_0px_#18181b] flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-500 text-white border-2 border-slate-900 flex items-center justify-center shrink-0 shadow-[1px_1px_0px_#18181b]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div class="text-xs sm:text-sm font-bold">
                            {{ session('gagal') ?? session('error') }}
                        </div>
                    </div>
                @endif

                @yield('konten')
            </main>
        </div>
    </div>
</body>
</html>
