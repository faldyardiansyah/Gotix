<div class="mb-6 sm:mb-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-5 border-b-2 border-slate-900">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-amber-100 border-2 border-slate-900 text-slate-950 text-[11px] font-heading font-black shadow-[1.5px_1.5px_0px_#18181b] mb-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span>PANEL OPERASIONAL LOKET</span>
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                {{ $judul ?? 'Dasbor Operator' }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 font-medium">
                {{ $deskripsi ?? 'Verifikasi bukti transfer penumpang dan layani transaksi langsung tiket travel loket.' }}
            </p>
        </div>

        <!-- Quick Switcher Tabs (3 Modul Operasional Nyata) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none shrink-0">
            <a href="{{ route('operator.pembayaran.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-heading font-extrabold rounded-xl transition whitespace-nowrap {{ request()->routeIs('operator.pembayaran.*') ? 'bg-amber-500 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] hover:bg-slate-50' }}">
                <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
                <span>Dasbor & Verifikasi</span>
            </a>

            <a href="{{ route('operator.loket.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-heading font-extrabold rounded-xl transition whitespace-nowrap {{ request()->routeIs('operator.loket.*') ? 'bg-amber-500 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] hover:bg-slate-50' }}">
                <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
                <span>Kasir Loket</span>
            </a>

            <a href="{{ route('operator.riwayat.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-heading font-extrabold rounded-xl transition whitespace-nowrap {{ request()->routeIs('operator.riwayat.*') ? 'bg-amber-500 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b] hover:bg-slate-50' }}">
                <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>Riwayat Transaksi</span>
            </a>
        </div>
    </div>
</div>
