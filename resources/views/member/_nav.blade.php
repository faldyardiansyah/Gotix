<div class="mb-8" data-aos="fade-down" data-aos-duration="400">
    <!-- Header Ringkas Member -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-sky-50 border border-sky-200/60 text-sky-700 text-xs font-semibold mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                Area Penumpang Gotix
            </div>
            <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $judul ?? 'Portal Member' }}
            </h1>
            <p class="text-sm text-slate-600 mt-1">
                {{ $deskripsi ?? 'Kelola tiket perjalanan, pantau status pembayaran, dan layanan bantuan Anda.' }}
            </p>
        </div>

        @auth
        <div class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm self-start sm:self-auto">
            <div class="w-9 h-9 rounded-lg bg-slate-900 text-white flex items-center justify-center font-heading font-bold text-sm">
                {{ strtoupper(substr(Auth::user()->nama ?? 'M', 0, 1)) }}
            </div>
            <div class="text-left">
                <div class="text-xs font-semibold text-slate-900 line-clamp-1">{{ Auth::user()->nama ?? 'Member' }}</div>
                <div class="text-[11px] text-slate-500 font-mono">{{ Auth::user()->telepon ?? Auth::user()->username }}</div>
            </div>
        </div>
        @endauth
    </div>

    <!-- Tab Navigasi Member (Responsive Mobile-Friendly) -->
    <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('member.tiket.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('member.tiket.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('member.tiket.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
            </svg>
            <span>Tiket Saya</span>
        </a>

        <a href="{{ route('member.pesanan.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('member.pesanan.*') || request()->routeIs('member.pembayaran.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('member.pesanan.*') || request()->routeIs('member.pembayaran.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v1.5a.75.75 0 0 0 .75.75H3m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m0 0h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 0 .75.75h1.5m-15-12v15m0 0h15" />
            </svg>
            <span>Pesanan & Pembayaran</span>
        </a>

        <a href="{{ route('member.keluhan.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition duration-150 whitespace-nowrap {{ request()->routeIs('member.keluhan.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-4 h-4 {{ request()->routeIs('member.keluhan.*') ? 'text-sky-400' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
            </svg>
            <span>Layanan Keluhan</span>
        </a>
    </div>
</div>

