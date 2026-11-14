@props([
    'role' => 'Member',
])

<footer class="mt-auto border-t-2 border-slate-900 bg-white py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-600">
        <div class="flex items-center gap-2.5">
            <div class="w-6 h-6 rounded-full bg-white border border-slate-900 text-slate-950 flex items-center justify-center shadow-[1.5px_1.5px_0px_#18181b]">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="#18181B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 6v6m4-6v6m4-6v6M3 10v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9M3 10l9-7 9 7"/>
                </svg>
            </div>
            <span class="font-heading font-extrabold text-slate-900">GoTix Travel Indonesia</span>
            <span class="text-slate-400">&bull;</span>
            <span class="text-slate-500">
                @if ($role === 'Operator')
                    Panel Operasional Loket & Kasir
                @elseif ($role === 'Admin')
                    Panel Manajemen Sistem
                @else
                    Portal Layanan Penumpang
                @endif
            </span>
        </div>

        <div class="flex items-center gap-4 text-xs font-semibold">
            @if ($role === 'Operator')
                <a href="{{ route('operator.pembayaran.index') }}" class="text-slate-700 hover:text-amber-600 transition">Dasbor & Verifikasi</a>
                <a href="{{ route('operator.loket.index') }}" class="text-slate-700 hover:text-amber-600 transition">Kasir Loket</a>
                <a href="{{ route('operator.riwayat.index') }}" class="text-slate-700 hover:text-amber-600 transition">Riwayat Transaksi</a>
            @else
                <a href="{{ route('beranda') }}" class="hover:text-amber-600 transition">Beranda</a>
                <a href="{{ route('jadwal.cari') }}" class="hover:text-amber-600 transition">Cari Jadwal</a>
                <a href="{{ route('member.tiket.index') }}" class="hover:text-amber-600 transition">Tiket Saya</a>
                <a href="{{ route('member.keluhan.index') }}" class="hover:text-amber-600 transition">Bantuan</a>
            @endif
        </div>

        <p class="text-slate-500">
            &copy; {{ date('Y') }} GoTix. Hak cipta dilindungi.
        </p>
    </div>
</footer>
