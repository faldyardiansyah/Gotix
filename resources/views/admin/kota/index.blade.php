@extends('layouts.tamu')

@section('title', 'Master Kota se-Indonesia — Admin Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        modalTambah: false,
        modalEdit: false,
        namaKotaEdit: '',
        provinsiEdit: '',
        bukaEdit(nama, prov) {
            this.namaKotaEdit = nama;
            this.provinsiEdit = prov;
            this.modalEdit = true;
        }
     }">

    @include('admin._nav', [
        'judul' => 'Master Data Kota Destinasi',
        'deskripsi' => 'Kelola daftar kota asal dan tujuan travel Gotix di seluruh wilayah Indonesia.'
    ])

    <!-- Aksi Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-aos="fade-down" data-aos-duration="400">
        <div class="text-xs text-slate-500">
            Total terdaftar: <span class="font-bold text-slate-900">20 Kota & Destinasi Hub Nasional</span>
        </div>

        <button type="button" @click="modalTambah = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Kota Baru</span>
        </button>
    </div>

    @php
        $daftarKota = [
            ['id' => 1, 'nama' => 'Jakarta (Gambir / Rawamangun)', 'provinsi' => 'DKI Jakarta', 'kode' => 'JKT', 'jumlah_rute' => 12],
            ['id' => 2, 'nama' => 'Bandung (Dipatiukur / Pasteur)', 'provinsi' => 'Jawa Barat', 'kode' => 'BDG', 'jumlah_rute' => 10],
            ['id' => 3, 'nama' => 'Yogyakarta (Malioboro / Jombor)', 'provinsi' => 'DI Yogyakarta', 'kode' => 'YOG', 'jumlah_rute' => 8],
            ['id' => 4, 'nama' => 'Surabaya (Gubeng / Bungurasih)', 'provinsi' => 'Jawa Timur', 'kode' => 'SUB', 'jumlah_rute' => 9],
            ['id' => 5, 'nama' => 'Semarang (Simpang Lima / Poncol)', 'provinsi' => 'Jawa Tengah', 'kode' => 'SMG', 'jumlah_rute' => 7],
            ['id' => 6, 'nama' => 'Malang (Arjosari / Klojen)', 'provinsi' => 'Jawa Timur', 'kode' => 'MLG', 'jumlah_rute' => 6],
            ['id' => 7, 'nama' => 'Medan (Amplas / Petisah)', 'provinsi' => 'Sumatera Utara', 'kode' => 'MED', 'jumlah_rute' => 5],
            ['id' => 8, 'nama' => 'Pekanbaru (Arengka / Sudirman)', 'provinsi' => 'Riau', 'kode' => 'PKU', 'jumlah_rute' => 4],
            ['id' => 9, 'nama' => 'Bandar Lampung (Rajabasa)', 'provinsi' => 'Lampung', 'kode' => 'TKG', 'jumlah_rute' => 6],
            ['id' => 10, 'nama' => 'Makassar (Dayah / Panakkukang)', 'provinsi' => 'Sulawesi Selatan', 'kode' => 'UPG', 'jumlah_rute' => 4],
        ];
    @endphp

    <!-- Tabel Master Kota -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up" data-aos-duration="500">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nama Kota & Hub</th>
                        <th class="px-6 py-4">Provinsi</th>
                        <th class="px-6 py-4">Kode Singkat</th>
                        <th class="px-6 py-4">Jadwal Aktif</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($daftarKota as $item)
                        <tr class="hover:bg-slate-50/60 transition duration-100">
                            <td class="px-6 py-4 font-bold text-slate-900 text-xs">
                                {{ $item['nama'] }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $item['provinsi'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $item['kode'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                {{ $item['jumlah_rute'] }} Rute Operasional
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <button type="button"
                                        @click="bukaEdit('{{ $item['nama'] }}', '{{ $item['provinsi'] }}')"
                                        class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">
                                    Edit
                                </button>
                                <button type="button"
                                        onclick="if(confirm('Hapus kota {{ $item['nama'] }}?')) alert('Kota berhasil dihapus.');"
                                        class="px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition cursor-pointer">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Kota -->
    <div x-show="modalTambah"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-cloak>
        <div @click.away="modalTambah = false"
             class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4">
            <h3 class="font-heading font-bold text-slate-900 text-base">Tambah Kota Destinasi Baru</h3>
            <form onsubmit="event.preventDefault(); alert('Kota baru berhasil didaftarkan ke sistem.'); modalTambah = false; window.location.reload();" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kota & Hub Terminal/Pool</label>
                    <input type="text" required placeholder="Contoh: Denpasar (Ubung / Kuta)"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Provinsi</label>
                    <input type="text" required placeholder="Contoh: Bali"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambah = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl">Simpan Kota</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kota -->
    <div x-show="modalEdit"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-cloak>
        <div @click.away="modalEdit = false"
             class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4">
            <h3 class="font-heading font-bold text-slate-900 text-base">Edit Kota Destinasi</h3>
            <form onsubmit="event.preventDefault(); alert('Data kota berhasil diperbarui.'); modalEdit = false;" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kota & Hub Terminal/Pool</label>
                    <input type="text" x-model="namaKotaEdit" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Provinsi</label>
                    <input type="text" x-model="provinsiEdit" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEdit = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
