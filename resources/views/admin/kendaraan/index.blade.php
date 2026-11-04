@extends('layouts.admin')

@section('title', 'Master Armada — Admin Gotix')
@section('page-title', 'Master Data Armada Kendaraan')
@section('page-subtitle', 'Kelola unit kendaraan travel berkapasitas 5 kursi.')

@section('konten')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="text-sm text-slate-600">
        Total <strong class="text-slate-900">{{ $daftarKendaraan->count() }}</strong> unit armada
    </div>
    <a href="{{ route('admin.kendaraan.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Tambah Armada</span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse ($daftarKendaraan as $kendaraan)
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                    {{ $kendaraan->kapasitas_kursi ?? 5 }} Kursi
                </span>
            </div>

            <h3 class="font-heading font-bold text-slate-900 text-sm">{{ $kendaraan->jenis_kendaraan }}</h3>
            <div class="mt-2 space-y-1 text-xs text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 w-20">Nomor Polisi</span>
                    <span class="font-mono font-semibold text-slate-900">{{ $kendaraan->nomor_polisi }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 w-20">Warna</span>
                    <span class="font-medium">{{ $kendaraan->warna }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 w-20">Total Jadwal</span>
                    <span class="font-medium">{{ $kendaraan->jadwal_count }} jadwal</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                <a href="{{ route('admin.kendaraan.edit', $kendaraan->id) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-lg transition">
                    Edit
                </a>
                <form action="{{ route('admin.kendaraan.destroy', $kendaraan->id) }}" method="POST"
                      onsubmit="return confirm('Hapus armada {{ $kendaraan->nomor_polisi }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-3 py-12 text-center bg-white border border-slate-200 rounded-2xl">
            <div class="text-sm text-slate-500">Belum ada armada kendaraan terdaftar.</div>
        </div>
    @endforelse
</div>
@endsection
