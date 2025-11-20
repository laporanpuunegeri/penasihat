<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MesyuaratController extends Controller
{
    public function index()
    {
        return view('laporanmesyuarat.index');
    }

    public function create()
    {
        return view('laporanmesyuarat.create');
    }

    public function store(Request $request)
    {
        // Logic simpan data
    }
}
