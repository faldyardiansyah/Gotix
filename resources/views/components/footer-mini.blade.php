<footer class="mt-auto border-t-2 border-slate-900 bg-white py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-600">
        <div class="flex items-center gap-2.5">
            <div class="w-6 h-6 rounded-lg bg-amber-500 border border-slate-900 text-slate-950 flex items-center justify-center font-heading font-black text-xs shadow-[1.5px_1.5px_0px_#18181b]">
                G
            </div>
            <span class="font-heading font-extrabold text-slate-900">Gotix Travel Indonesia</span>
            <span class="text-slate-400">&bull;</span>
            <span class="text-slate-500">Portal Layanan Penumpang</span>
        </div>

        <div class="flex items-center gap-4 text-xs font-semibold">
            <a href="{{ route('beranda') }}" class="hover:text-amber-600 transition">Beranda</a>
            <a href="{{ route('jadwal.cari') }}" class="hover:text-amber-600 transition">Cari Jadwal</a>
            <a href="{{ route('member.tiket.index') }}" class="hover:text-amber-600 transition">Tiket Saya</a>
            <a href="{{ route('member.keluhan.index') }}" class="hover:text-amber-600 transition">Bantuan</a>
        </div>

        <p class="text-slate-500">
            &copy; {{ date('Y') }} Gotix. Hak cipta dilindungi.
        </p>
    </div>
</footer>
