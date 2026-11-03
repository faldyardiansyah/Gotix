<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KotaController extends Controller
{
    public function index(): View
    {
        return view('admin.kota.index');
    }

    public function create(): View
    {
        return view('admin.kota.create');
    }

    public function edit(string $id): View
    {
        return view('admin.kota.edit', ['id' => $id]);
    }
}

