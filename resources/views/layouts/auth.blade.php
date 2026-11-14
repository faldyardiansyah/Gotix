<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Autentikasi — GoTix')</title>

    <!-- Google Fonts: Bricolage Grotesque & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700;800;900&family=DM+Sans:ital,opsz,wght@0,9..40,400;500;600;700;1,9..40,400&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full flex flex-col bg-[#FAFAF9] text-slate-900 antialiased font-sans selection:bg-amber-500 selection:text-slate-950 relative overflow-x-hidden">
    <!-- Tactile Subtle Pattern Background -->
    <div class="absolute inset-0 bg-[radial-gradient(#18181b_0.75px,transparent_0.75px)] [background-size:16px_16px] opacity-10 pointer-events-none"></div>

    <!-- Header Autentikasi Minimalis (Bukan Navbar Publik / Member) -->
    <header class="relative z-10 w-full border-b-2 border-slate-900 bg-white/90 backdrop-blur-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo GOTIX -->
            <a href="{{ route('beranda') }}" class="flex items-center gap-3 group" title="Kembali ke Beranda GoTix">
                <div class="w-10 h-10 rounded-full bg-white border-2 border-slate-900 text-slate-950 flex items-center justify-center shadow-[2.5px_2.5px_0px_#18181b] transition-transform group-hover:rotate-6 group-hover:scale-105 duration-200">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="#18181B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 6v6m4-6v6m4-6v6M3 10v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9M3 10l9-7 9 7"/>
                    </svg>
                </div>
                <span class="font-heading text-2xl font-black tracking-tight text-slate-950">
                    GoTix<span class="text-amber-500">.</span>
                </span>
            </a>

            <!-- Tombol Kembali ke Beranda -->
            <a href="{{ route('beranda') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border-2 border-slate-900 bg-white font-heading text-xs font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] hover:bg-slate-50 active:translate-x-0.5 active:translate-y-0.5 transition">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Beranda</span>
            </a>
        </div>
    </header>

    <!-- Konten Utama Autentikasi -->
    <main class="relative z-10 flex-grow flex items-center justify-center px-4 py-8 sm:py-12">
        @yield('konten')
    </main>

    <!-- Footer Minimalis Khusus Autentikasi -->
    <footer class="relative z-10 border-t-2 border-slate-900 bg-white py-5">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600 font-medium">
            <p>&copy; {{ date('Y') }} GoTix Travel Indonesia. Sistem Reservasi Tiket Terproteksi.</p>
            <div class="flex items-center gap-4 text-xs font-bold text-slate-700">
                <a href="{{ route('beranda') }}" class="hover:text-amber-600 transition">Beranda</a>
                <span>&bull;</span>
                <a href="{{ route('jadwal.cari') }}" class="hover:text-amber-600 transition">Cari Jadwal</a>
                <span>&bull;</span>
                <a href="mailto:bantuan@gotix.id" class="hover:text-amber-600 transition">Bantuan</a>
            </div>
        </div>
    </footer>
</body>
</html>
