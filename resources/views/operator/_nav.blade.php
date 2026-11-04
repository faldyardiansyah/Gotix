<div class="mb-8" data-aos="fade-down" data-aos-duration="400">
    <!-- Header Ringkas Operator -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-sky-50 border border-sky-200/60 text-sky-700 text-xs font-semibold mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                Panel Operasional Loket & Reservasi
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $judul ?? 'Dasbor Operator' }}
            </h1>
            <p class="text-sm text-slate-600 mt-1">
                {{ $deskripsi ?? 'Verifikasi bukti transfer penumpang dan layani transaksi langsung tiket travel loket.' }}
            </p>
        </div>

        @auth
        <div class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm self-start sm:self-auto">
            <div class="w-9 h-9 rounded-lg bg-sky-600 text-white flex items-center justify-center font-heading font-bold text-sm">
                OP
            </div>
            <div class="text-left">
                <div class="text-xs font-semibold text-slate-900 line-clamp-1">{{ Auth::user()->nama ?? 'Petugas Loket' }}</div>
                <div class="text-[11px] text-slate-500 font-mono">ID: {{ Auth::user()->username ?? 'operator' }} &bull; Shift Aktif</div>
            </div>
        </div>
        @endauth
    </div>

    <!-- Sub-menu Navigasi Operator (Responsive Mobile/Tablet) -->
    <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('operator.pembayaran.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('operator.pembayaran.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('operator.pembayaran.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
            </svg>
            <span>Verifikasi Pembayaran</span>
        </a>

        <a href="{{ route('operator.loket.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('operator.loket.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('operator.loket.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v1.5a.75.75 0 0 0 .75.75H3m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m-15-12v15m0 0h15" />
            </svg>
            <span>Kasir Loket / Walk-in</span>
        </a>
    </div>
</div>

