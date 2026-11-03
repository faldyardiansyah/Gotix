<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        return view('publik.beranda');
    }
}

