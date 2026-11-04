@extends('layouts.tamu')

@section('title', 'Kelola Pengguna — Admin Gotix')

@section('konten')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        modalPeran: false,
        namaUser: '',
        peranUser: '',
        bukaUbahPeran(nama, peran) {
            this.namaUser = nama;
            this.peranUser = peran;
            this.modalPeran = true;
        },
        resetPassword(nama) {
            if (confirm('Atur ulang kata sandi pengguna ' + nama + ' ke sandi bawaan (password123)?')) {
                alert('Kata sandi untuk ' + nama + ' berhasil direset.');
            }
        }
     }">

    @include('admin._nav', [
        'judul' => 'Manajemen Akun Pengguna',
        'deskripsi' => 'Kelola hak akses pengguna (Administrator, Petugas Operator Loket, dan Penumpang Member).'
    ])

    @php
        $daftarPengguna = [
            [
                'id' => 1,
                'nama' => 'Administrator Gotix',
                'username' => 'admin',
                'email' => 'admin@gotix.test',
                'telepon' => '0812-3456-7890',
                'peran' => 'Admin',
                'badge' => 'bg-purple-50 text-purple-700 border-purple-200',
                'terdaftar' => '14 Mar 2026',
            ],
            [
                'id' => 2,
                'nama' => 'Operator Loket Gambir',
                'username' => 'operator',
                'email' => 'operator@gotix.test',
                'telepon' => '0812-3456-7891',
                'peran' => 'Operator',
                'badge' => 'bg-sky-50 text-sky-700 border-sky-200',
                'terdaftar' => '15 Mar 2026',
            ],
            [
                'id' => 3,
                'nama' => 'Budi Santoso',
                'username' => 'member1',
                'email' => 'budi@gotix.test',
                'telepon' => '0812-7000-1001',
                'peran' => 'Member',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'terdaftar' => '16 Mar 2026',
            ],
            [
                'id' => 4,
                'nama' => 'Siti Rahma',
                'username' => 'member2',
                'email' => 'siti@gotix.test',
                'telepon' => '0812-7000-1002',
                'peran' => 'Member',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'terdaftar' => '18 Mar 2026',
            ],
            [
                'id' => 5,
                'nama' => 'Agus Setiawan',
                'username' => 'member3',
                'email' => 'agus@gotix.test',
                'telepon' => '0812-7000-1003',
                'peran' => 'Member',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'terdaftar' => '20 Mar 2026',
            ],
        ];
    @endphp

    <!-- Card Tabel Pengguna -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up" data-aos-duration="500">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nama & Username</th>
                        <th class="px-6 py-4">Kontak (Email / Telp)</th>
                        <th class="px-6 py-4">Peran Akses</th>
                        <th class="px-6 py-4">Tgl Registrasi</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($daftarPengguna as $user)
                        <tr class="hover:bg-slate-50/60 transition duration-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 text-xs">{{ $user['nama'] }}</div>
                                <div class="text-[11px] text-slate-400 font-mono font-medium">@<span>{{ $user['username'] }}</span></div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="text-slate-800">{{ $user['email'] }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $user['telepon'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $user['badge'] }}">
                                    {{ $user['peran'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                {{ $user['terdaftar'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <button type="button"
                                        @click="bukaUbahPeran('{{ $user['nama'] }}', '{{ $user['peran'] }}')"
                                        class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">
                                    Ubah Peran
                                </button>
                                <button type="button"
                                        @click="resetPassword('{{ $user['nama'] }}')"
                                        class="px-3 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg transition cursor-pointer">
                                    Reset Sandi
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Ubah Peran -->
    <div x-show="modalPeran"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-cloak>
        <div @click.away="modalPeran = false"
             class="bg-white border border-slate-200 rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4">
            <h3 class="font-heading font-bold text-slate-900 text-base">Ubah Peran Pengguna</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Sesuaikan hak akses untuk pengguna <strong class="text-slate-900" x-text="namaUser"></strong>.
            </p>
            <div>
                <select x-model="peranUser"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="Admin">Administrator (Hak Akses Penuh)</option>
                    <option value="Operator">Operator Loket (Verifikasi & Kasir)</option>
                    <option value="Member">Member (Penumpang Standar)</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="modalPeran = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                <button type="button" @click="alert('Peran pengguna berhasil diperbarui.'); modalPeran = false;"
                        class="px-5 py-2 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl">Simpan Peran</button>
            </div>
        </div>
    </div>
</div>
@endsection
