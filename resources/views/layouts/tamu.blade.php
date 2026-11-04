<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Gotix — Pemesanan Tiket Travel Antar Kota se-Indonesia')</title>

    <!-- Google Fonts: Bricolage Grotesque & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">

    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 antialiased font-sans selection:bg-sky-600 selection:text-white"
      x-data="{ mobileMenu: false }">

    <!-- Splash Screen / Loading Overlay (Hanya Muncul Sekali Saat Initial Load & Tidak Menghalangi Klik) -->
    <div x-data="{ showSplash: !sessionStorage.getItem('gotix_splash_viewed') }"
         x-init="if (showSplash) { setTimeout(() => { showSplash = false; sessionStorage.setItem('gotix_splash_viewed', 'true'); }, 950); }"
         x-show="showSplash"
         x-transition.opacity.duration.400ms
         class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-950 text-white pointer-events-none"
         style="display: none;"
         x-cloak>
        <div class="flex items-center gap-3 mb-6">
            <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center font-heading text-2xl font-bold text-sky-400">
                G
            </div>
            <span class="font-heading text-2xl font-bold tracking-tight text-white">
                Gotix<span class="text-sky-400">.</span>
            </span>
        </div>
        <!-- Progress Bar Minimalis -->
        <div class="w-48 h-1 bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full bg-sky-500 rounded-full animate-[progress_0.9s_ease-in-out_forwards]"></div>
        </div>
        <p class="text-xs font-medium text-slate-400 mt-4 tracking-wide uppercase">Memuat Antarmuka...</p>
    </div>

    <style>
        @keyframes progress {
            0% { width: 0%; }
            60% { width: 75%; }
            100% { width: 100%; }
        }
    </style>

    <!-- Sticky Navbar -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo & Link Beranda -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('beranda') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-heading font-bold text-lg transition-transform group-hover:scale-95 duration-200 shadow-sm">
                            G
                        </div>
                        <span class="font-heading text-xl font-bold tracking-tight text-slate-900">
                            Gotix<span class="text-sky-600">.</span>
                        </span>
                    </a>

                    <!-- Nav Link Beranda Desktop -->
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('beranda') }}"
                           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ request()->routeIs('beranda') ? 'text-sky-600 bg-sky-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Beranda
                        </a>
                        <a href="{{ route('jadwal.cari') }}"
                           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ request()->routeIs('jadwal.*') ? 'text-sky-600 bg-sky-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Cari Jadwal
                        </a>
                    </nav>
                </div>

                <!-- Nav Menu Kanan (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <!-- Menu Khusus Member -->
                        @if (Auth::user()->isMember())
                            <a href="{{ route('member.tiket.index') }}"
                               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ request()->routeIs('member.tiket.*') ? 'text-sky-600 bg-sky-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                Tiket Saya
                            </a>
                            <a href="{{ route('member.pesanan.index') }}"
                               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ request()->routeIs('member.pesanan.*') ? 'text-sky-600 bg-sky-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                Pesanan
                            </a>
                        @elseif (Auth::user()->isOperator())
                            <a href="{{ route('operator.pembayaran.index') }}"
                               class="px-3 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 rounded-lg">
                                Panel Operator
                            </a>
                        @elseif (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dasbor') }}"
                               class="px-3 py-1.5 text-xs font-semibold text-purple-700 bg-purple-50 rounded-lg">
                                Panel Admin
                            </a>
                        @endif

                        <!-- Profil Singkat -->
                        <div class="h-4 w-px bg-slate-200"></div>
                        <div class="text-xs font-bold text-slate-800">
                            {{ Auth::user()->nama }}
                        </div>

                        <!-- Form Tombol Keluar Akun (Wajib POST) -->
                        <form action="{{ route('keluar') }}" method="POST" class="inline m-0">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                </svg>
                                <span>Keluar Akun</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('masuk') }}"
                           class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition duration-150">
                            Masuk
                        </a>
                        <a href="{{ route('daftar') }}"
                           class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition duration-150 shadow-xs">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Tombol Hamburger Mobile -->
                <div class="flex items-center md:hidden">
                    <button type="button" @click="mobileMenu = !mobileMenu"
                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg x-show="mobileMenu" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Dropdown Mobile -->
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-5 space-y-2 shadow-lg"
             x-cloak>
            <a href="{{ route('beranda') }}"
               class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-slate-100">
                Beranda
            </a>
            <a href="{{ route('jadwal.cari') }}"
               class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-slate-100">
                Cari Jadwal Travel
            </a>

            @auth
                @if (Auth::user()->isMember())
                    <a href="{{ route('member.tiket.index') }}"
                       class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-slate-100">
                        Tiket Saya
                    </a>
                    <a href="{{ route('member.pesanan.index') }}"
                       class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-slate-100">
                        Riwayat Pesanan
                    </a>
                    <a href="{{ route('member.keluhan.index') }}"
                       class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-800 hover:bg-slate-100">
                        Pusat Keluhan
                    </a>
                @elseif (Auth::user()->isOperator())
                    <a href="{{ route('operator.pembayaran.index') }}"
                       class="block px-3 py-2 rounded-xl text-xs font-semibold text-sky-700 bg-sky-50">
                        Panel Operator Loket
                    </a>
                @elseif (Auth::user()->isAdmin())
                    <a href="{{ route('admin.dasbor') }}"
                       class="block px-3 py-2 rounded-xl text-xs font-semibold text-purple-700 bg-purple-50">
                        Panel Dasbor Admin
                    </a>
                @endif

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700">{{ Auth::user()->nama }}</span>
                    <!-- Form Logout Mobile -->
                    <form action="{{ route('keluar') }}" method="POST" class="inline m-0">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 rounded-xl cursor-pointer">
                            Keluar Akun
                        </button>
                    </form>
                </div>
            @else
                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2">
                    <a href="{{ route('masuk') }}"
                       class="text-center py-2 text-xs font-semibold text-slate-700 bg-slate-100 rounded-xl">
                        Masuk
                    </a>
                    <a href="{{ route('daftar') }}"
                       class="text-center py-2 text-xs font-bold text-white bg-sky-600 rounded-xl">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('konten')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-heading font-bold text-sm">
                        G
                    </div>
                    <span class="font-heading font-bold text-slate-900 tracking-tight">Gotix Travel Indonesia</span>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-slate-500">
                    <a href="{{ route('beranda') }}" class="hover:text-slate-900">Beranda</a>
                    <span>&bull;</span>
                    <a href="{{ route('jadwal.cari') }}" class="hover:text-slate-900">Jadwal Perjalanan</a>
                    <span>&bull;</span>
                    <span>Layanan Antar Kota se-Indonesia</span>
                </div>

                <p class="text-xs text-slate-500 text-center md:text-right">
                    &copy; {{ date('Y') }} Gotix Travel. Seluruh hak cipta dilindungi undang-undang.
                </p>
            </div>
        </div>
    </footer>

    <!-- AOS Animation Library Script -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    once: true,
                    duration: 600,
                    easing: 'ease-out-cubic',
                    offset: 50,
                });
            }
        });
    </script>
</body>
</html>
