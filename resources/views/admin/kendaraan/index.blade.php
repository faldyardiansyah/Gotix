@extends('layouts.tamu')

@section('title', 'Master Armada Travel — Admin Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        modalTambah: false,
        modalEdit: false,
        modelEdit: '',
        platEdit: '',
        warnaEdit: '',
        statusEdit: 'Aktif',
        bukaEdit(model, plat, warna, status) {
            this.modelEdit = model;
            this.platEdit = plat;
            this.warnaEdit = warna;
            this.statusEdit = status;
            this.modalEdit = true;
        }
     }">

    @include('admin._nav', [
        'judul' => 'Master Armada Kendaraan',
        'deskripsi' => 'Manajemen unit armada operasional berkapasitas 5 kursi eksklusif di seluruh rute travel.'
    ])

    <!-- Aksi Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-aos="fade-down" data-aos-duration="400">
        <div class="text-xs text-slate-500">
            Total Unit: <span class="font-bold text-slate-900">4 Unit Kendaraan Siap Operasi</span>
        </div>

        <button type="button" @click="modalTambah = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 active:bg-sky-800 rounded-xl transition shadow-sm self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Armada Baru</span>
        </button>
    </div>

    @php
        $daftarArmada = [
            [
                'id' => 1,
                'model' => 'Toyota HiAce Premio Executive',
                'plat' => 'BE 1212 AT',
                'warna' => 'Hitam Metalik',
                'kapasitas' => 5,
                'status' => 'Aktif',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
            ],
            [
                'id' => 2,
                'model' => 'Isuzu Elf Executive Captain Seat',
                'plat' => 'BE 4677 DT',
                'warna' => 'Biru Tua',
                'kapasitas' => 5,
                'status' => 'Aktif',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
            ],
            [
                'id' => 3,
                'model' => 'Toyota Avanza Veloz Prime',
                'plat' => 'BE 7890 XY',
                'warna' => 'Putih Mutiara',
                'kapasitas' => 5,
                'status' => 'Aktif',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
            ],
            [
                'id' => 4,
                'model' => 'Toyota Innova Zenix Hybrid',
                'plat' => 'DD 1928 MN',
                'warna' => 'Abu-abu Gelap',
                'kapasitas' => 5,
                'status' => 'Servis Rutin',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500',
            ],
        ];
    @endphp

    <!-- Tabel Master Armada -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up" data-aos-duration="500">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tipe & Model Mobil</th>
                        <th class="px-6 py-4">Nomor Polisi</th>
                        <th class="px-6 py-4">Warna Eksterior</th>
                        <th class="px-6 py-4">Kapasitas Kursi</th>
                        <th class="px-6 py-4">Status Kondisi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($daftarArmada as $item)
                        <tr class="hover:bg-slate-50/60 transition duration-100">
                            <td class="px-6 py-4 font-bold text-slate-900 text-xs">
                                {{ $item['model'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs font-bold px-2.5 py-1 rounded bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $item['plat'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $item['warna'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-sky-600">
                                {{ $item['kapasitas'] }} Penumpang
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $item['badge'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item['dot'] }}"></span>
                                    {{ $item['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <button type="button"
                                        @click="bukaEdit('{{ $item['model'] }}', '{{ $item['plat'] }}', '{{ $item['warna'] }}', '{{ $item['status'] }}')"
                                        class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">
                                    Edit
                                </button>
                                <button type="button"
                                        onclick="if(confirm('Hapus armada {{ $item['plat'] }}?')) alert('Unit kendaraan berhasil dihapus.');"
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

    <!-- Modal Tambah Armada -->
    <div x-show="modalTambah"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-cloak>
        <div @click.away="modalTambah = false"
             class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4">
            <h3 class="font-heading font-bold text-slate-900 text-base">Tambah Unit Armada Baru</h3>
            <form onsubmit="event.preventDefault(); alert('Unit kendaraan baru berhasil ditambahkan.'); modalTambah = false; window.location.reload();" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Model / Varian Kendaraan</label>
                    <input type="text" required placeholder="Contoh: Toyota HiAce Premio Executive"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Polisi (Plat)</label>
                    <input type="text" required placeholder="Contoh: B 1029 KJA"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Warna Kendaraan</label>
                    <input type="text" required placeholder="Contoh: Silver Metalik"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambah = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl">Simpan Armada</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Armada -->
    <div x-show="modalEdit"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-cloak>
        <div @click.away="modalEdit = false"
             class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4">
            <h3 class="font-heading font-bold text-slate-900 text-base">Edit Unit Armada</h3>
            <form onsubmit="event.preventDefault(); alert('Data armada berhasil diperbarui.'); modalEdit = false;" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Model / Varian Kendaraan</label>
                    <input type="text" x-model="modelEdit" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Polisi (Plat)</label>
                    <input type="text" x-model="platEdit" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Warna Kendaraan</label>
                    <input type="text" x-model="warnaEdit" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Operasional</label>
                    <select x-model="statusEdit"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="Aktif">Aktif Beroperasi</option>
                        <option value="Servis Rutin">Servis Rutin / Perbaikan</option>
                        <option value="Nonaktif">Nonaktif Sementara</option>
                    </select>
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
