<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PesananLoketController extends Controller
{
    public function index(): View
    {
        return view('operator.loket.index');
    }

    public function create(): View
    {
        return view('operator.loket.index');
    }
}
