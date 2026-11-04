<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(): View
    {
        return view('admin.pengguna.index');
    }

    public function show(string $id): View
    {
        return view('admin.pengguna.show', ['id' => $id]);
    }
}
