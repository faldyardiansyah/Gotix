<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(): View
    {
        return view('member.pesanan.index');
    }

    public function show(string $kode): View
    {
        return view('member.pesanan.show', ['kode' => $kode]);
    }
}

