<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CariJadwalController extends Controller
{
    public function index(Request $request): View
    {
        return view('publik.cari-jadwal');
    }
}

