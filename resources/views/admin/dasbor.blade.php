@extends('layouts.admin')

@section('title', 'Dasbor Admin — Gotix Travel')
@section('page-title', 'Dasbor')
@section('page-subtitle', 'Ringkasan operasional Gotix hari ini.')

@section('konten')

{{-- 1. Tindakan utama: pesanan yang menunggu verifikasi --}}
@if ($menanggguVerifikasi > 0)
    <a href="{{ route('admin.pesanan.index') }}"
       class="group mb-6 flex items-center justify-between gap-4 rounded-2xl bg-amber-400 px-6 py-5 text-amber-950 transition hover:bg-amber-300 focus:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/60">
        <div>
            <div class="font-heading text-2xl font-extrabold leading-tight sm:text-3xl">
                {{ $menanggguVerifikasi }} pesanan menunggu verifikasi
            </div>
            <p class="mt-1 text-sm font-medium text-amber-900/80">
                Penumpang sudah membayar dan menunggu konfirmasi dari Anda.
            </p>
        </div>
        <span class="shrink-0 rounded-xl bg-amber-950 px-4 py-2.5 text-sm font-bold text-amber-50 transition group-hover:bg-black">
            Verifikasi sekarang
        </span>
    </a>
@else
    <div class="mb-6 flex items-center gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-5">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>
        <div>
            <div class="font-heading text-lg font-bold text-emerald-900">Semua pesanan sudah diverifikasi</div>
            <p class="text-sm text-emerald-800/80">Tidak ada yang perlu ditindaklanjuti saat ini.</p>
        </div>
    </div>
@endif

{{-- 2. Angka ringkas: satu strip, bukan empat kartu terpisah --}}
<dl class="mb-8 grid grid-cols-2 divide-x divide-y divide-slate-200 overflow-hidden rounded-2xl border border-slate-200 bg-white lg:grid-cols-4 lg:divide-y-0">
    <div class="px-6 py-5">
        <dt class="text-sm font-medium text-slate-500">Jadwal</dt>
        <dd class="mt-1 font-heading text-4xl font-extrabold tabular-nums text-slate-900">{{ $totalJadwal }}</dd>
    </div>
    <div class="px-6 py-5">
        <dt class="text-sm font-medium text-slate-500">Pesanan masuk</dt>
        <dd class="mt-1 font-heading text-4xl font-extrabold tabular-nums text-slate-900">{{ $totalPesanan }}</dd>
    </div>
    <div class="px-6 py-5">
        <dt class="text-sm font-medium text-slate-500">Pesanan lunas</dt>
        <dd class="mt-1 font-heading text-4xl font-extrabold tabular-nums text-emerald-600">{{ $totalLunas }}</dd>
    </div>
    <div class="px-6 py-5">
        <dt class="text-sm font-medium text-slate-500">Member terdaftar</dt>
        <dd class="mt-1 font-heading text-4xl font-extrabold tabular-nums text-slate-900">{{ $totalPengguna }}</dd>
    </div>
</dl>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- 3. Jadwal terbaru, kursi digambar per kursi --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white lg:col-span-2">
        <header class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
            <h2 class="font-heading text-base font-bold text-slate-900">Jadwal terbaru</h2>
            <a href="{{ route('admin.jadwal.index') }}"
               class="rounded text-sm font-semibold text-sky-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500">
                Lihat semua jadwal
            </a>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[34rem] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500">
                    <tr>
                        <th scope="col" class="px-6 py-3">Rute</th>
                        <th scope="col" class="px-3 py-3">Berangkat</th>
                        <th scope="col" class="px-3 py-3">Armada</th>
                        <th scope="col" class="px-3 py-3 text-right">Tarif</th>
                        <th scope="col" class="px-6 py-3">Kursi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($jadwalTerbaru as $jadwal)
                        @php
                            $kapasitas = max((int) ($jadwal->kendaraan?->kapasitas_kursi ?? 5), 1);
                            $terisi = min(
                                $jadwal->pesanan->whereIn('status', [
                                    \App\Enums\StatusPesanan::BelumBayar,
                                    \App\Enums\StatusPesanan::DalamProses,
                                    \App\Enums\StatusPesanan::Lunas,
                                ])->count(),
                                $kapasitas
                            );
                            $penuh = $terisi >= $kapasitas;
                            $tanggal = $jadwal->tanggal_berangkat
                                ? \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->locale('id')->translatedFormat('D, d M Y')
                                : null;
                        @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $jadwal->kotaAsal?->nama_kota ?? '-' }}
                                <span class="mx-1 font-normal text-slate-400" aria-label="ke">ke</span>
                                {{ $jadwal->kotaTujuan?->nama_kota ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-slate-700">
                                <div>{{ $tanggal ?? '-' }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $jadwal->jam_berangkat ? substr($jadwal->jam_berangkat, 0, 5) . ' WIB' : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 text-slate-700">
                                <div>{{ $jadwal->kendaraan?->jenis_kendaraan ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $jadwal->kendaraan?->nomor_polisi ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-right font-semibold tabular-nums text-slate-900">
                                Rp {{ number_format((int) $jadwal->harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex gap-1" role="img" aria-label="{{ $terisi }} dari {{ $kapasitas }} kursi terisi">
                                        @for ($i = 1; $i <= $kapasitas; $i++)
                                            <span class="h-3.5 w-3.5 rounded-[4px] {{ $i <= $terisi ? ($penuh ? 'bg-rose-500' : 'bg-sky-600') : 'border border-slate-300 bg-white' }}"></span>
                                        @endfor
                                    </div>
                                    <span class="whitespace-nowrap text-xs font-semibold {{ $penuh ? 'text-rose-600' : 'text-slate-600' }}">
                                        {{ $penuh ? 'Penuh' : $terisi . '/' . $kapasitas }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <p class="font-semibold text-slate-900">Belum ada jadwal</p>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan jadwal pertama agar penumpang bisa memesan.</p>
                                <a href="{{ route('admin.jadwal.index') }}"
                                   class="mt-4 inline-block rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">
                                    Tambah jadwal
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- 4. Pintasan: daftar sederhana --}}
    <nav aria-label="Pintasan kelola data" class="self-start overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <h2 class="border-b border-slate-200 px-6 py-4 font-heading text-base font-bold text-slate-900">Kelola</h2>
        <ul class="divide-y divide-slate-100">
            <li>
                <a href="{{ route('admin.jadwal.index') }}"
                   class="flex items-center justify-between gap-3 px-6 py-4 transition hover:bg-slate-50 focus:outline-none focus-visible:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500">
                    <span>
                        <span class="block text-sm font-semibold text-slate-900">Jadwal dan rute</span>
                        <span class="block text-xs text-slate-500">Jam berangkat, armada, dan tarif</span>
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold tabular-nums text-slate-700">{{ $totalJadwal }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.kendaraan.index') }}"
                   class="flex items-center justify-between gap-3 px-6 py-4 transition hover:bg-slate-50 focus:outline-none focus-visible:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500">
                    <span>
                        <span class="block text-sm font-semibold text-slate-900">Armada</span>
                        <span class="block text-xs text-slate-500">Mobil 5 kursi, nomor polisi, warna</span>
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold tabular-nums text-slate-700">{{ $totalKendaraan }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.pesanan.index') }}"
                   class="flex items-center justify-between gap-3 px-6 py-4 transition hover:bg-slate-50 focus:outline-none focus-visible:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500">
                    <span>
                        <span class="block text-sm font-semibold text-slate-900">Pesanan</span>
                        <span class="block text-xs text-slate-500">Pembayaran dan keterisian kursi</span>
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold tabular-nums text-slate-700">{{ $totalPesanan }}</span>
                </a>
            </li>
        </ul>
    </nav>

</div>
@endsection