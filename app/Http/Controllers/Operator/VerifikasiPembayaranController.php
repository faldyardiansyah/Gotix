<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class VerifikasiPembayaranController extends Controller
{
    public function index(): View
    {
        return view('operator.pembayaran.index');
    }

    public function show(string $id): View
    {
        return view('operator.pembayaran.show', ['id' => $id]);
    }
}
