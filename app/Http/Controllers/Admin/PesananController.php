<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(): View
    {
        return view('admin.pesanan.index');
    }

    public function show(string $id): View
    {
        return view('admin.pesanan.show', ['id' => $id]);
    }
}

