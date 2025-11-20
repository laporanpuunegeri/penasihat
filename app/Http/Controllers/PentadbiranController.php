<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- 1. TAMBAH INI

class PentadbiranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 2. TAMBAH LOGIK KESELAMATAN & VIEW
        
        // Semak kebenaran (PA, YB, atau Bahagian Pentadbiran)
        if (!Auth::check() || 
            (strtolower(Auth::user()->role) !== 'pa' && 
             strtolower(Auth::user()->role) !== 'yb' && 
             Auth::user()->bahagian !== 'Bahagian Pentadbiran')) 
        {
            abort(403, 'Anda tiada kebenaran untuk akses modul ini.');
        }

        // Papar fail view
        return view('pentadbiran.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // KESELAMATAN TAMBAHAN (Hanya Bahagian Pentadbiran boleh 'isi')
        if (!Auth::check() || Auth::user()->bahagian !== 'Bahagian Pentadbiran') {
            abort(403, 'Hanya Bahagian Pentadbiran boleh mencipta rekod baru.');
        }

        return view('pentadbiran.create'); // Papar borang
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // KESELAMATAN TAMBAHAN
        if (!Auth::check() || Auth::user()->bahagian !== 'Bahagian Pentadbiran') {
            abort(403, 'Hanya Bahagian Pentadbiran boleh menyimpan rekod baru.');
        }

        // ... (Letak logik untuk simpan data borang di sini) ...
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // ... (Letak logik keselamatan anda di sini) ...
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // ... (Letak logik keselamatan anda di sini) ...
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // ... (Letak logik keselamatan anda di sini) ...
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // ... (Letak logik keselamatan anda di sini) ...
    }
}