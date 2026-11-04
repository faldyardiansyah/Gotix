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
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 antialiased font-sans selection:bg-sky-600 selection:text-white"
      x-data="{ mobileMenu: false }">

    <!-- Splash Screen / Loading Overlay (Hanya Muncul Sekali Saat Initial Load & Tidak Pernah Mengunci Layar) -->
    <div id="gotix-splash"
         x-data="{ showSplash: !sessionStorage.getItem('gotix_splash_shown') }"
         x-init="if (showSplash) { setTimeout(() => { showSplash = false; sessionStorage.setItem('gotix_splash_shown', 'true'); }, 850); } else { $el.remove(); }"
         x-show="showSplash"
         x-transition:leave="transition ease-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-950 text-white pointer-events-none"
         x-cloak>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-2xl bg-sky-500/15 border border-sky-400/30 flex items-center justify-center font-heading text-2xl font-black text-sky-400 shadow-lg shadow-sky-500/10">
                G
            </div>
            <span class="font-heading text-2xl font-bold tracking-tight text-white">
                Gotix<span class="text-sky-400">.</span>
            </span>
        </div>
        <!-- Progress Bar Minimalis -->
        <div class="w-44 h-1 bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full bg-sky-500 rounded-full animate-[progress_0.8s_ease-in-out_forwards]"></div>
        </div>
        <p class="text-[11px] font-semibold text-slate-400 mt-3 tracking-widest uppercase">Memuat Gotix Travel...</p>
    </div>

    <!-- Sticky Navbar -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-200/80 bg-white/95 backdrop-blur-md transition">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo & Navigasi Kiri -->
                <div class="flex items-center gap-7">
                    <a href="{{ route('beranda') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-heading font-black text-lg transition-transform group-hover:scale-95 duration-200 shadow-sm">
                            G
                        </div>
                        <span class="font-heading text-xl font-bold tracking-tight text-slate-900">
                            Gotix<span class="text-sky-600">.</span>
                        </span>
                    </a>

                    <!-- Nav Link Desktop -->
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('beranda') }}"
                           class="px-3.5 py-2 text-xs font-semibold rounded-xl transition {{ request()->routeIs('beranda') ? 'text-sky-700 bg-sky-50 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Beranda
                        </a>
                        <a href="{{ route('jadwal.cari') }}"
                           class="px-3.5 py-2 text-xs font-semibold rounded-xl transition {{ request()->routeIs('jadwal.*') ? 'text-sky-700 bg-sky-50 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Cari Jadwal
                        </a>
                    </nav>
                </div>

                <!-- Nav Menu Kanan (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <!-- Navigasi Sesuai Peran -->
                        @if (Auth::user()->isMember())
                            <a href="{{ route('member.tiket.index') }}"
                               class="px-3 py-1.5 text-xs font-semibold rounded-xl transition {{ request()->routeIs('member.tiket.*') ? 'text-sky-700 bg-sky-50 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                Tiket Saya
                            </a>
                            <a href="{{ route('member.pesanan.index') }}"
                               class="px-3 py-1.5 text-xs font-semibold rounded-xl transition {{ request()->routeIs('member.pesanan.*') ? 'text-sky-700 bg-sky-50 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                Riwayat Pesanan
                            </a>
                        @elseif (Auth::user()->isOperator())
                            <a href="{{ route('operator.pembayaran.index') }}"
                               class="px-3 py-1.5 text-xs font-bold text-sky-800 bg-sky-50 border border-sky-200 rounded-xl">
                                Panel Operator
                            </a>
                        @elseif (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dasbor') }}"
                               class="px-3 py-1.5 text-xs font-bold text-indigo-800 bg-indigo-50 border border-indigo-200 rounded-xl">
                                Panel Admin
                            </a>
                        @endif

                        <!-- Profil Singkat & Tombol Keluar (POST) -->
                        <div class="h-4 w-px bg-slate-200 mx-1"></div>
                        <div class="text-xs font-bold text-slate-800 max-w-[130px] truncate" title="{{ Auth::user()->nama }}">
                            {{ Auth::user()->nama }}
                        </div>

                        <!-- Form Logout Wajib POST -->
                        <form action="{{ route('keluar') }}" method="POST" class="inline m-0">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                </svg>
                                <span>Keluar</span>
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

                <!-- Tombol Menu Mobile Hamburger -->
                <div class="flex items-center md:hidden">
                    <button type="button" @click="mobileMenu = !mobileMenu"
                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none"
                            aria-label="Buka menu navigasi">
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

        <!-- Dropdown Menu Mobile -->
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
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
                @elseif (Auth::user()->isOperator())
                    <a href="{{ route('operator.pembayaran.index') }}"
                       class="block px-3 py-2 rounded-xl text-xs font-bold text-sky-700 bg-sky-50">
                        Panel Operator Loket
                    </a>
                @elseif (Auth::user()->isAdmin())
                    <a href="{{ route('admin.dasbor') }}"
                       class="block px-3 py-2 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50">
                        Panel Admin Master
                    </a>
                @endif

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800">{{ Auth::user()->nama }}</span>
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

    <!-- Area Konten Utama -->
    <main class="flex-grow">
        @yield('konten')
    </main>

    <!-- Footer Modern SaaS -->
    <footer class="border-t border-slate-200 bg-white mt-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <!-- Kolom Brand -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center font-heading font-black text-sm">
                            G
                        </div>
                        <span class="font-heading font-bold text-lg text-slate-900 tracking-tight">Gotix Travel Indonesia</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                        Platform reservasi travel eksekutif antar kota se-Indonesia dengan sistem kuota 5 kursi terproteksi transaksi database tanpa double-booking dan pembayaran manual terverifikasi.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-emerald-700 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Sistem Reservasi Aktif &bull; Layanan 24 Jam</span>
                    </div>
                </div>

                <!-- Kolom Navigasi Cepat -->
                <div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Layanan Gotix</h3>
                    <ul class="space-y-2 text-xs text-slate-500 font-medium">
                        <li><a href="{{ route('beranda') }}" class="hover:text-sky-600 transition">Beranda Utama</a></li>
                        <li><a href="{{ route('jadwal.cari') }}" class="hover:text-sky-600 transition">Pencarian Jadwal</a></li>
                        <li><a href="{{ route('jadwal.cari', ['kota_asal' => 'Jakarta', 'kota_tujuan' => 'Bandung']) }}" class="hover:text-sky-600 transition">Travel Jakarta - Bandung</a></li>
                        <li><a href="{{ route('jadwal.cari', ['kota_asal' => 'Yogyakarta', 'kota_tujuan' => 'Surabaya']) }}" class="hover:text-sky-600 transition">Travel Jogja - Surabaya</a></li>
                    </ul>
                </div>

                <!-- Kolom Hubungi Kami & Bank -->
                <div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Rekening Pembayaran</h3>
                    <div class="text-xs text-slate-600 space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div class="font-bold text-slate-900">{{ config('gotix.bank_name', 'BCA') }}</div>
                        <div class="font-mono text-slate-800">{{ config('gotix.bank_account', '0000000000') }}</div>
                        <div class="text-[11px] text-slate-500">a/n {{ config('gotix.bank_holder', 'PT Gotix Travel') }}</div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Gotix Travel Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
                <div class="flex items-center gap-4">
                    <span>Trans-Jawa & Trans-Sumatera</span>
                    <span>&bull;</span>
                    <span>Modern Travel SaaS</span>
                </div>
            </div>
        </div>
    </footer>

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
