<div class="mb-8">
    <!-- Header Ringkas Member -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b-2 border-slate-900">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 border-2 border-slate-900 text-slate-950 font-heading text-xs font-black shadow-[2px_2px_0px_#18181b] mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Area Penumpang GoTix</span>
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                {{ $judul ?? 'Portal Member' }}
            </h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">
                {{ $deskripsi ?? 'Kelola tiket perjalanan, pantau status pembayaran, dan layanan bantuan Anda.' }}
            </p>
        </div>

        @auth
        <div class="flex items-center gap-3 bg-white border-2 border-slate-900 rounded-2xl px-4 py-2.5 shadow-[3px_3px_0px_#18181b] self-start sm:self-auto">
            <div class="w-10 h-10 rounded-xl bg-amber-500 border-2 border-slate-900 text-slate-950 flex items-center justify-center font-heading font-black text-base shadow-[1.5px_1.5px_0px_#18181b]">
                {{ strtoupper(substr(Auth::user()->nama ?? 'M', 0, 1)) }}
            </div>
            <div class="text-left">
                <div class="text-xs font-heading font-extrabold text-slate-900 line-clamp-1">{{ Auth::user()->nama ?? 'Member' }}</div>
                <div class="text-[11px] text-slate-500 font-mono">{{ Auth::user()->telepon ?? Auth::user()->username }}</div>
            </div>
        </div>
        @endauth
    </div>

    <!-- Tab Navigasi Member (Responsive Mobile-Friendly) -->
    <div class="mt-5 flex items-center gap-2.5 overflow-x-auto pb-2 scrollbar-none">
        <a href="{{ route('member.tiket.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-heading font-bold rounded-full border-2 border-slate-900 transition-all duration-150 whitespace-nowrap {{ request()->routeIs('member.tiket.*') ? 'bg-amber-500 text-slate-950 shadow-[3px_3px_0px_#18181b]' : 'bg-white text-slate-800 shadow-[2px_2px_0px_#18181b] hover:bg-slate-50 hover:-translate-y-0.5' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('member.tiket.*') ? 'text-slate-950' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
            </svg>
            <span>Tiket Saya</span>
        </a>

        <a href="{{ route('member.pesanan.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-heading font-bold rounded-full border-2 border-slate-900 transition-all duration-150 whitespace-nowrap {{ request()->routeIs('member.pesanan.*') || request()->routeIs('member.pembayaran.*') ? 'bg-amber-500 text-slate-950 shadow-[3px_3px_0px_#18181b]' : 'bg-white text-slate-800 shadow-[2px_2px_0px_#18181b] hover:bg-slate-50 hover:-translate-y-0.5' }}">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
            </svg>
            <span>Pesanan & Pembayaran</span>
        </a>

        <a href="{{ route('member.keluhan.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-heading font-bold rounded-full border-2 border-slate-900 transition-all duration-150 whitespace-nowrap {{ request()->routeIs('member.keluhan.*') ? 'bg-amber-500 text-slate-950 shadow-[3px_3px_0px_#18181b]' : 'bg-white text-slate-800 shadow-[2px_2px_0px_#18181b] hover:bg-slate-50 hover:-translate-y-0.5' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('member.keluhan.*') ? 'text-slate-950' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
            </svg>
            <span>Layanan Keluhan</span>
        </a>

        <a href="{{ route('jadwal.cari') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-heading font-bold rounded-full border-2 border-dashed border-slate-900 bg-amber-50 text-slate-900 hover:bg-amber-100 transition whitespace-nowrap ml-auto">
            <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Cari Jadwal Baru</span>
        </a>
    </div>
</div>