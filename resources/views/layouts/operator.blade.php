<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel Operator Loket — Gotix Travel')</title>

    <!-- Google Fonts: Bricolage Grotesque & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-full flex flex-col bg-stone-50 text-slate-900 antialiased font-sans selection:bg-amber-500 selection:text-slate-950"
    x-data="{ mobileMenu: false }">

    <!-- Sticky Navbar Operator Loket -->
    <header class="sticky top-0 z-40 w-full border-b-2 border-slate-900 bg-white/95 backdrop-blur-md transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo & Badge Operator (Kiri) -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('operator.pembayaran.index') }}"
                        class="flex items-center gap-3 group"
                        title="Panel Operator Loket Gotix">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 border-2 border-slate-900 text-slate-950 flex items-center justify-center font-heading font-black text-xl transition-transform group-hover:-rotate-3 duration-200 shadow-[2.5px_2.5px_0px_#18181b]">
                            G
                        </div>
                        <span class="font-heading text-2xl font-black tracking-tight text-slate-950">
                            Gotix<span class="text-amber-500">.</span>
                        </span>
                    </a>

                    <!-- Nav Link Operator (Desktop) -->
                    <nav class="hidden md:flex items-center gap-2 ml-4">
                        <a href="{{ route('operator.pembayaran.index') }}"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs lg:text-sm font-heading font-extrabold rounded-xl transition {{ request()->routeIs('operator.pembayaran.*') ? 'text-slate-950 bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                            <span>Dasbor & Verifikasi</span>
                        </a>

                        <a href="{{ route('operator.loket.index') }}"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs lg:text-sm font-heading font-extrabold rounded-xl transition {{ request()->routeIs('operator.loket.*') ? 'text-slate-950 bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            </svg>
                            <span>Kasir Loket</span>
                        </a>

                        <a href="{{ route('operator.riwayat.index') }}"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs lg:text-sm font-heading font-extrabold rounded-xl transition {{ request()->routeIs('operator.riwayat.*') ? 'text-slate-950 bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Riwayat Transaksi</span>
                        </a>
                    </nav>
                </div>

                <!-- Nav Menu Kanan (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                    <!-- Identitas Operator -->
                    <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border-2 border-slate-900 bg-amber-50 shadow-[2px_2px_0px_#18181b]">
                        <div class="w-7 h-7 rounded-lg bg-amber-500 border border-slate-900 flex items-center justify-center font-heading font-black text-xs text-slate-950">
                            {{ strtoupper(substr(Auth::user()->nama ?? 'OP', 0, 2)) }}
                        </div>
                        <div class="text-left leading-tight">
                            <div class="text-xs font-heading font-extrabold text-slate-950 max-w-[140px] truncate">
                                {{ Auth::user()->nama }}
                            </div>
                        </div>
                    </div>

                    <!-- Form Logout Wajib POST -->
                    <form action="{{ route('keluar') }}" method="POST" class="inline m-0">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-heading font-bold text-rose-900 bg-white hover:bg-rose-50 border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_#18181b] hover:-translate-y-0.5 active:translate-y-0.5 transition cursor-pointer"
                            title="Keluar dari akun Operator">
                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                    @endauth
                </div>

                <!-- Tombol Menu Mobile Hamburger -->
                <div class="flex items-center md:hidden">
                    <button type="button" @click="mobileMenu = !mobileMenu"
                        class="p-2 rounded-xl text-slate-900 bg-stone-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] hover:bg-amber-100 transition focus:outline-none"
                        aria-label="Buka menu navigasi operator">
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
            <div class="px-2 py-1 text-[11px] font-heading font-black text-slate-400 uppercase tracking-wider">
                Menu Operasional Loket
            </div>

            <a href="{{ route('operator.pembayaran.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-slate-900 {{ request()->routeIs('operator.pembayaran.*') ? 'bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'hover:bg-amber-50' }}">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
                <span>Dasbor & Verifikasi</span>
            </a>

            <a href="{{ route('operator.loket.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-slate-900 {{ request()->routeIs('operator.loket.*') ? 'bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'hover:bg-amber-50' }}">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
                <span>Kasir Loket (Walk-in)</span>
            </a>

            <a href="{{ route('operator.riwayat.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-heading font-extrabold text-slate-900 {{ request()->routeIs('operator.riwayat.*') ? 'bg-amber-100 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'hover:bg-amber-50' }}">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>Riwayat Transaksi</span>
            </a>

            @auth
            <div class="pt-3 border-t-2 border-slate-900 mt-2 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-500 border border-slate-900 flex items-center justify-center font-heading font-black text-xs text-slate-950">
                        {{ strtoupper(substr(Auth::user()->nama ?? 'OP', 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-xs font-heading font-extrabold text-slate-950">{{ Auth::user()->nama }}</div>
                        <div class="text-[10px] text-slate-500 font-bold">Shift Loket Aktif</div>
                    </div>
                </div>
                <form action="{{ route('keluar') }}" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-heading font-bold text-rose-900 bg-rose-50 border border-rose-300 rounded-xl">
                        Keluar
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </header>

    <!-- Main Content Operator -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8 md:py-10">
        @yield('konten')
    </main>

    <!-- Footer Khusus Operator -->
    <x-footer-mini role="Operator" />

</body>

</html>