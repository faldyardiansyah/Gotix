<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TiketController extends Controller
{
    public function index(): View
    {
        return view('member.tiket.index');
    }

    public function show(string $kode): View
    {
        return view('member.tiket.show', ['kode' => $kode]);
    }
}

