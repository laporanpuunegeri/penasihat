<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SemakanUndangController extends Controller
{
    public function index()
    {
        return view('laporansemakanundang.index');
    }

    public function create()
    {
        return view('laporansemakanundang.create');
    }

    public function store(Request $request)
    {
        // Logic simpan data
    }
}
