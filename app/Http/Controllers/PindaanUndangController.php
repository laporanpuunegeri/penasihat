<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PindaanUndangController extends Controller
{
    public function index()
    {
        return view('laporanpindaanundang.index');
    }

    public function create()
    {
        return view('laporanpindaanundang.create');
    }

    public function store(Request $request)
    {
        // Logic simpan data
    }
}
