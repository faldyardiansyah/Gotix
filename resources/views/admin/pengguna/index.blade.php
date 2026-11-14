@extends('layouts.admin')

@section('title', 'Data Pengguna — GoTix.')
@section('page-title', 'Manajemen Pengguna Sistem')
@section('page-subtitle', 'Daftar seluruh akun Admin, Operator Loket, dan Member terdaftar.')

@section('konten')
<!-- Bar Aksi Atas -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border-2 border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[2px_2px_0px_#18181b]">
        <span>Total Pengguna:</span>
        <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 font-black">{{ $daftarPengguna->count() }}</span>
    </div>
    <x-button href="{{ route('admin.pengguna.create') }}" variant="primary" size="md">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Tambah Pengguna</span>
    </x-button>
</div>

<div x-data="{ tab: 'semua', search: '' }">
    <!-- Bar Filter Peran & Pencarian -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="tab = 'semua'"
                    :class="tab === 'semua' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer">
                Semua Pengguna ({{ $daftarPengguna->count() }})
            </button>
            <button type="button" @click="tab = 'Member'"
                    :class="tab === 'Member' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer">
                Member ({{ $daftarPengguna->where('peran.value', 'Member')->count() }})
            </button>
            <button type="button" @click="tab = 'Operator'"
                    :class="tab === 'Operator' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer">
                Operator Loket ({{ $daftarPengguna->where('peran.value', 'Operator')->count() }})
            </button>
            <button type="button" @click="tab = 'Admin'"
                    :class="tab === 'Admin' ? 'bg-amber-400 text-slate-950 border-2 border-slate-900 shadow-[2px_2px_0px_#18181b]' : 'bg-white text-slate-700 border-2 border-slate-900 shadow-[1px_1px_0px_#18181b] hover:bg-stone-100'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-heading font-black transition cursor-pointer">
                Admin ({{ $daftarPengguna->where('peran.value', 'Admin')->count() }})
            </button>
        </div>

        <div class="relative w-full md:w-64">
            <input type="text" x-model="search" placeholder="Cari nama / email / username..."
                   class="w-full rounded-xl border-2 border-slate-900 bg-white px-3.5 py-1.5 pl-9 text-xs font-bold text-slate-900 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b] transition">
            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
    </div>

    <!-- Tabel Pengguna (Tactile Card) -->
    <x-card title="Daftar Pengguna Sistem" subtitle="Kelola identitas, peran hak akses, dan pantau riwayat perjalanan akun" variant="white">
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-stone-100 border-b-2 border-slate-900 text-xs font-heading font-black text-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Nama & Username</th>
                        <th class="px-4 py-3.5">Kontak & Email</th>
                        <th class="px-4 py-3.5 text-center">Peran Akun</th>
                        <th class="px-4 py-3.5 text-center">Total Pesanan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 border-slate-900 divide-stone-200 font-medium">
                    @forelse ($daftarPengguna as $pengguna)
                        @php
                            $peranVal = $pengguna->peran instanceof \App\Enums\PeranPengguna ? $pengguna->peran->value : (string) $pengguna->peran;
                            $roleBadge = match ($peranVal) {
                                'Admin' => 'bg-purple-100 text-purple-950 border-slate-900',
                                'Operator' => 'bg-sky-100 text-sky-950 border-slate-900',
                                default => 'bg-amber-100 text-amber-950 border-slate-900',
                            };
                        @endphp
                        <tr x-show="(tab === 'semua' || tab === '{{ $peranVal }}') && ('{{ strtolower($pengguna->nama) }} {{ strtolower($pengguna->username ?? '') }} {{ strtolower($pengguna->email ?? '') }}'.includes(search.toLowerCase()))"
                            class="hover:bg-amber-50/60 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-400 text-slate-950 border-2 border-slate-900 flex items-center justify-center font-heading font-black text-sm shrink-0 shadow-[1.5px_1.5px_0px_#18181b]">
                                        {{ strtoupper(substr($pengguna->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-heading font-black text-slate-950 text-sm">{{ $pengguna->nama }}</div>
                                        <div class="text-xs text-slate-500 font-mono font-semibold">
                                            {{ $pengguna->username ? '@' . $pengguna->username : '—' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs">
                                <div class="font-bold text-slate-900">{{ $pengguna->email ?? '-' }}</div>
                                <div class="text-slate-500 font-mono font-semibold mt-0.5">{{ $pengguna->telepon ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-heading font-black border-1.5 {{ $roleBadge }} shadow-[1px_1px_0px_#18181b]">
                                    {{ $peranVal }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center font-heading font-black text-slate-950 text-base">
                                {{ $pengguna->pesanan_count }}
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <x-button href="{{ route('admin.pengguna.show', $pengguna->id) }}" variant="secondary" size="sm">
                                    Detail Profil
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm font-bold text-slate-600">
                                Belum ada data pengguna yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
