@extends('layouts.admin')

@section('title', 'Master Armada — GoTix.')
@section('page-title', 'Master Data Armada Kendaraan')
@section('page-subtitle', 'Kelola unit armada travel eksekutif berkapasitas 5 kursi terproteksi.')

@section('konten')
<!-- Bar Aksi Atas -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border-2 border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[2px_2px_0px_#18181b]">
        <span>Total Unit Armada:</span>
        <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 font-black">{{ $daftarKendaraan->count() }}</span>
    </div>
    <x-button href="{{ route('admin.kendaraan.create') }}" variant="primary" size="md">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Tambah Armada Baru</span>
    </x-button>
</div>

<!-- Grid Kartu Armada (Tactile Cards) -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse ($daftarKendaraan as $kendaraan)
        <div class="bg-white border-2 border-slate-900 rounded-2xl p-6 shadow-[4px_4px_0px_#18181b] flex flex-col justify-between hover:-translate-y-0.5 transition duration-150">
            <div>
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-400 border-2 border-slate-900 text-slate-950 flex items-center justify-center shadow-[2px_2px_0px_#18181b]">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-stone-100 border-2 border-slate-900 text-xs font-heading font-black text-slate-900 shadow-[1px_1px_0px_#18181b]">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ $kendaraan->kapasitas_kursi ?? 5 }} Kursi</span>
                    </span>
                </div>

                <h3 class="font-heading font-black text-slate-950 text-xl">{{ $kendaraan->jenis_kendaraan }}</h3>
                <div class="mt-4 space-y-2 text-xs">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Nomor Polisi</span>
                        <span class="font-mono font-black text-slate-950 text-sm">{{ $kendaraan->nomor_polisi }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Warna Armada</span>
                        <span class="font-bold text-slate-900">{{ $kendaraan->warna }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-stone-50 border border-slate-900">
                        <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Jadwal Terdaftar</span>
                        <span class="font-black text-slate-950">{{ $kendaraan->jadwal_count }} Jadwal</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t-2 border-slate-900 flex items-center justify-end gap-2">
                <x-button href="{{ route('admin.kendaraan.edit', $kendaraan->id) }}" variant="primary" size="sm" class="flex-1">
                    Edit Armada
                </x-button>
                <form action="{{ route('admin.kendaraan.destroy', $kendaraan->id) }}" method="POST"
                      onsubmit="return confirm('Hapus unit armada {{ $kendaraan->nomor_polisi }}?')"
                      class="flex-1">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" class="w-full">
                        Hapus
                    </x-button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white border-2 border-slate-900 rounded-2xl p-12 text-center shadow-[4px_4px_0px_#18181b]">
            <p class="text-base font-bold text-slate-600">Belum ada unit armada yang terdaftar.</p>
            <div class="mt-4">
                <x-button href="{{ route('admin.kendaraan.create') }}" variant="primary" size="md">
                    Tambah Armada Pertama &rarr;
                </x-button>
            </div>
        </div>
    @endforelse
</div>
@endsection
