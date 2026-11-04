<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(): View
    {
        return view('admin.jadwal.index');
    }

    public function create(): View
    {
        return view('admin.jadwal.create');
    }

    public function edit(string $id): View
    {
        return view('admin.jadwal.edit', ['id' => $id]);
    }

    public function show(string $id): View
    {
        return view('admin.jadwal.show', ['id' => $id]);
    }
}
