<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen62; // Panggil model 62
use Illuminate\Support\Facades\Auth;

class Seksyen62Controller extends Controller
{
    public function index()
    {
        $rekod = PermohonanSeksyen62::where('agensi_id', Auth::guard('agensi')->id())
                    ->orderBy('created_at', 'desc')
                    ->get();
        return view('permohonan.seksyen62.index', compact('rekod'));
    }
    public function show($id)
    {
    // Cari rekod seksyen 62 mengikut ID
    $data = \App\Models\PermohonanSeksyen62::findOrFail($id);
    
    return view('permohonan.seksyen62.show', compact('data'));
    }

    public function create()
    {
        return view('permohonan.seksyen62.create');
    }

    public function store(Request $request)
    {
        // Simpan semua data dari borang (BM & BI)
        $data = $request->all();
        $data['agensi_id'] = Auth::guard('agensi')->id();
        $data['status'] = 'Belum Disemak';

        PermohonanSeksyen62::create($data);

        return redirect()->route('permohonan.seksyen62')->with('success', 'Permohonan Seksyen 62 berjaya dihantar!');
    }

    public function edit($id)
{
    $data = \App\Models\PermohonanSeksyen62::findOrFail($id);
    return view('permohonan.seksyen62.edit', compact('data'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'tujuan_bm' => 'required',
        'tujuan_bi' => 'required',
    ]);

    $permohonan = \App\Models\PermohonanSeksyen62::findOrFail($id);
    $permohonan->update($request->all());

    return redirect()->route('permohonan.seksyen62')->with('success', 'Permohonan berjaya dikemaskini!');
}
}