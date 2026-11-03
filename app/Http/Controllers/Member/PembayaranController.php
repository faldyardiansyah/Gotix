<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function show(string $kode): View
    {
        return view('member.pembayaran.show', ['kode' => $kode]);
    }

    public function konfirmasi(string $kode): View
    {
        return view('member.pembayaran.konfirmasi', ['kode' => $kode]);
    }
}

