@extends('layouts.admin')

@section('title', 'Master Kota — Admin Gotix')
@section('page-title', 'Master Data Kota')
@section('page-subtitle', 'Daftar kota asal & tujuan yang tersedia di sistem Gotix.')

@section('konten')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="text-sm text-slate-600">
        Total <strong class="text-slate-900">{{ $daftarKota->count() }}</strong> kota terdaftar
    </div>
    <a href="{{ route('admin.kota.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Tambah Kota</span>
    </a>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50/80 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">ID</th>
                    <th class="px-5 py-3.5">Nama Kota / Wilayah</th>
                    <th class="px-5 py-3.5">Jadwal Sebagai Asal</th>
                    <th class="px-5 py-3.5">Jadwal Sebagai Tujuan</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($daftarKota as $kota)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-3.5 text-xs font-mono text-slate-400">#{{ $kota->id }}</td>
                        <td class="px-5 py-3.5 font-semibold text-slate-900">{{ $kota->nama_kota }}</td>
                        <td class="px-5 py-3.5 text-xs text-slate-700">{{ $kota->jadwal_asal_count ?? 0 }} jadwal</td>
                        <td class="px-5 py-3.5 text-xs text-slate-700">{{ $kota->jadwal_tujuan_count ?? 0 }} jadwal</td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.kota.edit', $kota->id) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-lg transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.kota.destroy', $kota->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus kota {{ $kota->nama_kota }}? Data jadwal terkait mungkin terpengaruh.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                            Belum ada data kota tersimpan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
