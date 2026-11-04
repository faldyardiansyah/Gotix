<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Kota;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        $daftarKota = Kota::orderBy('nama_kota')->get();

        return view('publik.beranda', compact('daftarKota'));
    }
}
