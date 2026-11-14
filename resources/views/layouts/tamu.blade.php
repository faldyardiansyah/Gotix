<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GoTix — Pemesanan Tiket Travel Antar Kota se-Indonesia')</title>

    <!-- Google Fonts: Bricolage Grotesque & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">

    <!-- AOS (Animate On Scroll) Stylesheet -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-stone-50 text-slate-900 antialiased font-sans selection:bg-amber-500 selection:text-slate-950"
      x-data="{ mobileMenu: false }">

    <!-- Splash Screen / Loading Overlay -->
    <div id="gotix-splash"
         x-data="{ showSplash: !sessionStorage.getItem('gotix_splash_shown') }"
         x-init="if (showSplash) { setTimeout(() => { showSplash = false; sessionStorage.setItem('gotix_splash_shown', 'true'); }, 700); } else { $el.remove(); }"
         x-show="showSplash"
         x-transition:leave="transition ease-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-950 text-white pointer-events-none"
         x-cloak>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 rounded-full bg-white border-2 border-slate-900 text-slate-950 flex items-center justify-center shadow-[3px_3px_0px_#f59e0b]">
                <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="#18181B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 6v6m4-6v6m4-6v6M3 10v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9M3 10l9-7 9 7"/>
                </svg>
            </div>
            <span class="font-heading text-2xl font-black tracking-tight text-white">
                GoTix<span class="text-amber-400">.</span>
            </span>
        </div>
        <!-- Progress Bar Minimalis -->
        <div class="w-44 h-1.5 bg-slate-800 rounded-full overflow-hidden border border-slate-700">
            <div class="h-full bg-amber-400 rounded-full animate-[progress_0.8s_ease-in-out_forwards]"></div>
        </div>
        <p class="text-[11px] font-heading font-bold text-slate-400 mt-3 tracking-widest uppercase">Memuat GoTix Travel...</p>
    </div>

    <!-- Sticky Navbar Gotix -->
    <header class="sticky top-0 z-40 w-full border-b-2 border-slate-900 bg-white/95 backdrop-blur-md transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 md:grid md:grid-cols-[1fr_auto_1fr] md:items-center">
                <!-- KIRI: Brand Logo GoTix -->
                <div class="flex items-center justify-start">
                    <a href="{{ request()->routeIs('member.*') ? route('member.tiket.index') : route('beranda') }}"
                       class="flex items-center gap-3 group"
                       title="{{ request()->routeIs('member.*') ? 'Portal Member GoTix' : 'Beranda GoTix' }}">
                        <div class="w-10 h-10 rounded-full bg-white border-2 border-slate-900 text-slate-950 flex items-center justify-center shadow-[2.5px_2.5px_0px_#18181b] transition-transform group-hover:rotate-6 group-hover:scale-105 duration-200">
                            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="#18181B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 6v6m4-6v6m4-6v6M3 10v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9M3 10l9-7 9 7"/>
                            </svg>
                        </div>
                        <span class="font-heading text-2xl font-black tracking-tight text-slate-950">
                            GoTix<span class="text-amber-500">.</span>
                        </span>
                    </a>
                </div>

                <!-- TENGAH: Menu Navigasi Desktop (True Center) -->
                <nav class="hidden md:flex items-center justify-center gap-2">
                    @if (request()->routeIs('member.*'))
                        <!-- Nav Link Member (Desktop) -->
                        <a href="{{ route('member.tiket.index') }}"
                           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs lg:text-sm font-heading font-extrabold rounded-xl transition {{ request()->routeIs('member.tiket.*') ? 'text-slate-950 bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            </svg>
                            <span>Tiket Saya</span>
                        </a>

                        <a href="{{ route('member.pesanan.index') }}"
                           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs lg:text-sm font-heading font-extrabold rounded-xl transition {{ request()->routeIs('member.pesanan.*') || request()->routeIs('member.pembayaran.*') ? 'text-slate-950 bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                            <span>Pesanan & Pembayaran</span>
                        </a>

                        <a href="{{ route('member.keluhan.index') }}"
                           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs lg:text-sm font-heading font-extrabold rounded-xl transition {{ request()->routeIs('member.keluhan.*') ? 'text-slate-950 bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                            </svg>
                            <span>Bantuan & Keluhan</span>
                        </a>
                    @else
                        <!-- Nav Link Publik (Desktop) -->
                        <a href="{{ route('beranda') }}"
                           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs lg:text-sm font-heading font-extrabold rounded-xl transition {{ request()->routeIs('beranda') ? 'text-slate-950 bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            <span>Beranda</span>
                        </a>

                        <a href="{{ route('jadwal.cari') }}"
                           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs lg:text-sm font-heading font-extrabold rounded-xl transition {{ request()->routeIs('jadwal.*') ? 'text-slate-950 bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <span>Cari Jadwal</span>
                        </a>
                    @endif
                </nav>

                <!-- KANAN: Nav Menu Kanan (Desktop) / Hamburger (Mobile) -->
                <div class="flex items-center justify-end gap-3">
                    @auth
                        <!-- Jika Pengguna Sudah Login -->
                        @if (Auth::user()->isMember())
                            @if (!request()->routeIs('member.*'))
                                <a href="{{ route('member.tiket.index') }}"
                                   class="hidden md:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border-2 border-slate-900 bg-amber-50 hover:bg-amber-100 shadow-[2px_2px_0px_#18181b] transition">
                                    <div class="w-6 h-6 rounded-lg bg-amber-500 border border-slate-900 flex items-center justify-center font-heading font-black text-xs text-slate-950">
                                        {{ strtoupper(substr(Auth::user()->nama ?? 'M', 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-heading font-extrabold text-slate-950 max-w-[130px] truncate">{{ Auth::user()->nama }}</span>
                                </a>
                            @else
                                <div class="hidden md:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border-2 border-slate-900 bg-amber-50 shadow-[2px_2px_0px_#18181b]">
                                    <div class="w-6 h-6 rounded-lg bg-amber-500 border border-slate-900 flex items-center justify-center font-heading font-black text-xs text-slate-950">
                                        {{ strtoupper(substr(Auth::user()->nama ?? 'M', 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-heading font-extrabold text-slate-950 max-w-[130px] truncate">{{ Auth::user()->nama }}</span>
                                </div>
                            @endif
                        @elseif (Auth::user()->isOperator())
                            <a href="{{ route('operator.pembayaran.index') }}"
                               class="hidden md:inline-block px-4 py-2 text-xs font-heading font-black text-amber-950 bg-amber-100 border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_#18181b] hover:bg-amber-200 transition">
                                Panel Operator
                            </a>
                        @elseif (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dasbor') }}"
                               class="hidden md:inline-block px-4 py-2 text-xs font-heading font-black text-slate-100 bg-slate-900 border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_#18181b] hover:bg-slate-800 transition">
                                Panel Admin
                            </a>
                        @endif

                        <!-- Form Logout Wajib POST -->
                        <form action="{{ route('keluar') }}" method="POST" class="hidden md:inline m-0">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-heading font-bold text-rose-900 bg-white hover:bg-rose-50 border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_#18181b] hover:-translate-y-0.5 active:translate-y-0.5 transition cursor-pointer"
                                    title="Keluar dari akun">
                                <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    @else
                        <!-- Tombol Masuk & Daftar (Desktop) -->
                        <div class="hidden md:flex items-center gap-2">
                            <a href="{{ route('masuk') }}"
                               class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-heading font-bold text-slate-900 bg-white hover:bg-slate-50 border-2 border-slate-900 rounded-xl shadow-[2.5px_2.5px_0px_#18181b] hover:-translate-y-0.5 active:translate-y-0.5 transition">
                                Masuk
                            </a>
                            <a href="{{ route('daftar') }}"
                               class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-heading font-black text-slate-950 bg-amber-500 hover:bg-amber-400 border-2 border-slate-900 rounded-xl shadow-[2.5px_2.5px_0px_#18181b] hover:-translate-y-0.5 active:translate-y-0.5 transition">
                                Daftar
                            </a>
                        </div>
                    @endauth

                    <!-- Tombol Menu Mobile Hamburger -->
                    <button type="button" @click="mobileMenu = !mobileMenu"
                            class="p-2 rounded-xl text-slate-900 bg-stone-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] hover:bg-amber-100 transition focus:outline-none md:hidden"
                            aria-label="Buka menu navigasi">
                        <svg x-show="!mobileMenu" class="w-5 h-5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg x-show="mobileMenu" class="w-5 h-5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Dropdown Menu Mobile -->
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t-2 border-slate-900 bg-white px-4 pt-3 pb-5 space-y-2 shadow-xl"
             x-cloak>
            @if (request()->routeIs('member.*'))
                <!-- Menu Mobile Portal Member -->
                <div class="px-2 py-1 text-[11px] font-heading font-black text-slate-400 uppercase tracking-wider">
                    Navigasi Portal Member
                </div>
                <a href="{{ route('member.tiket.index') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-slate-900 {{ request()->routeIs('member.tiket.*') ? 'bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'hover:bg-amber-50' }}">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    <span>Tiket Saya</span>
                </a>
                <a href="{{ route('member.pesanan.index') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-slate-900 {{ request()->routeIs('member.pesanan.*') || request()->routeIs('member.pembayaran.*') ? 'bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'hover:bg-amber-50' }}">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                    <span>Riwayat Pesanan & Pembayaran</span>
                </a>
                <a href="{{ route('member.keluhan.index') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-slate-900 {{ request()->routeIs('member.keluhan.*') ? 'bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'hover:bg-amber-50' }}">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>Pusat Bantuan & Keluhan</span>
                </a>
                <a href="{{ route('jadwal.cari') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-amber-950 bg-amber-50 border border-slate-900">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>+ Cari Jadwal Baru</span>
                </a>
            @else
                <!-- Menu Mobile Publik -->
                <a href="{{ route('beranda') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-slate-900 hover:bg-amber-50 border border-transparent hover:border-slate-900 transition">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Beranda</span>
                </a>
                <a href="{{ route('jadwal.cari') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-slate-900 hover:bg-amber-50 border border-transparent hover:border-slate-900 transition">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Cari Jadwal Travel</span>
                </a>
            @endif

            @auth
                <div class="pt-3 border-t-2 border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-heading font-extrabold text-slate-950">{{ Auth::user()->nama }}</span>
                    <form action="{{ route('keluar') }}" method="POST" class="inline m-0">
                        @csrf
                        <button type="submit"
                                class="px-3.5 py-1.5 text-xs font-heading font-black text-rose-900 bg-rose-50 border border-slate-900 rounded-xl shadow-[1.5px_1.5px_0px_#18181b] cursor-pointer">
                            Keluar Akun
                        </button>
                    </form>
                </div>
            @else
                <div class="pt-3 border-t-2 border-slate-100 grid grid-cols-2 gap-2">
                    <a href="{{ route('masuk') }}"
                       class="text-center py-2.5 text-xs font-heading font-bold text-slate-900 bg-white border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_#18181b]">
                        Masuk
                    </a>
                    <a href="{{ route('daftar') }}"
                       class="text-center py-2.5 text-xs font-heading font-black text-slate-950 bg-amber-500 border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_#18181b]">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Area Konten Utama -->
    <main class="flex-grow">
        @yield('konten')
    </main>

    <!-- Footer Gotix: Adaptif sesuai rute -->
    @hasSection('footer')
        @yield('footer')
    @elseif (request()->routeIs('member.*'))
        <x-footer-mini />
    @else
        <x-footer-crew />
    @endif

    <!-- AOS Script & Safety Fallback (Menjamin Tampilan Tidak Pernah Blong / Macet) -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    once: true,
                    duration: 650,
                    easing: 'ease-out-cubic',
                    offset: 30,
                    disableMutationObserver: false,
                });
            }

            // Safety Fallback: Jaminan mutlak elemen tidak pernah stuck opacity: 0 jika AOS CDN telat
            setTimeout(function () {
                var splashEl = document.getElementById('gotix-splash');
                if (splashEl) {
                    splashEl.style.opacity = '0';
                    setTimeout(function () { splashEl.remove(); }, 300);
                }
                if (typeof AOS !== 'undefined') {
                    AOS.refreshHard();
                } else {
                    document.querySelectorAll('[data-aos]').forEach(function (el) {
                        el.style.opacity = '1';
                        el.style.transform = 'none';
                    });
                }
            }, 900);
        });
    </script>
</body>
</html>
