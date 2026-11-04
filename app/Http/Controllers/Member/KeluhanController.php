<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KeluhanController extends Controller
{
    public function index(): View
    {
        return view('member.keluhan.index');
    }

    public function create(): View
    {
        return view('member.keluhan.create');
    }
}
