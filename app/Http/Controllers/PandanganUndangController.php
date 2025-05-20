<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanpandanganundangController extends Controller
{
    public function index()
    {
        return view('laporanpandanganundang.index');
    }

    public function create()
    {
        return view('laporanpandanganundang.create');
    }

    public function store(Request $request)
    {
        // logic store
    }
}
