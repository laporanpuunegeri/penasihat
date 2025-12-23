<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen64;
use Illuminate\Support\Facades\Auth;

class Seksyen64Controller extends Controller
{
    /**
     * Papar Senarai Permohonan Seksyen 64
     */
    public function index()
    {
        $rekod = PermohonanSeksyen64::where('agensi_id', Auth::guard('agensi')->id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('permohonan.seksyen64.index', compact('rekod'));
    }

    /**
     * Borang Daftar Baru
     */
    public function create()
    {
        return view('permohonan.seksyen64.create');
    }

    /**
     * Simpan Data Seksyen 64
     */
    public function store(Request $request)
{
    // 1. Validasi Data (Pastikan data penting diisi)
    $request->validate([
        'no_warta_asal' => 'required',
        'tarikh_warta_asal' => 'required|date',
        'daerah' => 'required',
        'mukim' => 'required',
        'no_lot' => 'required',
        'luas' => 'required|numeric',
        'tujuan_bm' => 'required',
    ]);

    // 2. Simpan ke Database
    $permohonan = new PermohonanSeksyen64();
    
    // Auto-detect Agensi ID dari user yang sedang login
    $permohonan->agensi_id = Auth::guard('agensi')->id();

    // Maklumat dari Borang
    $permohonan->no_warta_asal = $request->no_warta_asal;
    $permohonan->tarikh_warta_asal = $request->tarikh_warta_asal;
    
    $permohonan->daerah = $request->daerah;
    $permohonan->mukim = $request->mukim;
    $permohonan->no_lot = $request->no_lot;
    $permohonan->no_pa = $request->no_pa;
    $permohonan->luas = $request->luas;

    $permohonan->tujuan_bm = $request->tujuan_bm;
    $permohonan->tujuan_bi = $request->tujuan_bi;
    $permohonan->kawalan_bm = $request->kawalan_bm;
    $permohonan->kawalan_bi = $request->kawalan_bi;

    // Data Default / Auto
    $permohonan->no_fail = "PTG/S64/" . date('Y') . "/" . rand(1000,9999); // Contoh no fail sementara
    $permohonan->tarikh_tt = now();
    $permohonan->status = 'Baru';

    $permohonan->save();

    // 3. Kembali ke senarai dengan mesej sukses
    return redirect()->route('permohonan.seksyen64')->with('success', 'Permohonan Seksyen 64 berjaya dihantar!');
}

    /**
     * Papar Live Preview (Show)
     */
    public function show($id)
    {
        $data = PermohonanSeksyen64::findOrFail($id);
        return view('permohonan.seksyen64.show', compact('data'));
    }

    /**
     * Borang Edit
     */
    public function edit($id)
    {
        $data = PermohonanSeksyen64::findOrFail($id);
        return view('permohonan.seksyen64.edit', compact('data'));
    }

    /**
     * Kemaskini Data
     */
    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen64::findOrFail($id);
        
        $request->validate([
            'no_warta_asal' => 'required',
            'no_lot' => 'required',
            'luas' => 'required|numeric',
        ]);

        $data->update($request->all());

        return redirect()->route('permohonan.seksyen64')->with('success', 'Rekod Seksyen 64 berjaya dikemaskini!');
    }
}