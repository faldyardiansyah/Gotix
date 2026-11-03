<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Gotix - Travel Lampung Modern' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;700&family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-sky-500 selection:text-white bg-slate-50 text-slate-800">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-200/80 bg-white/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-bold text-xl tracking-tight text-slate-900">
                <span class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-extrabold text-lg shadow-sm">G</span>
                <span>Gotix<span class="text-sky-600">.</span></span>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('masuk') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 transition-colors">Masuk</a>
                <a href="{{ route('daftar') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-xl hover:bg-sky-700 transition-all shadow-sm shadow-sky-200">
                    Daftar
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama Dinamis -->
    <main class="flex-grow">
        @yield('konten')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} Gotix Travel Lampung. Diolah secara presisi & murni.
        </div>
    </footer>

</body>
</html>