<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    public function index(): View
    {
        return view('admin.kendaraan.index');
    }

    public function create(): View
    {
        return view('admin.kendaraan.create');
    }

    public function edit(string $id): View
    {
        return view('admin.kendaraan.edit', ['id' => $id]);
    }
}

