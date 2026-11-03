<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KeluhanController extends Controller
{
    public function index(): View
    {
        return view('admin.keluhan.index');
    }

    public function show(string $id): View
    {
        return view('admin.keluhan.show', ['id' => $id]);
    }
}

