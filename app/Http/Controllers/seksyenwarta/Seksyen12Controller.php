<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PermohonanSeksyen12;
use Carbon\Carbon;

class Seksyen12Controller extends Controller
{
    /**
     * Papar Senarai Permohonan (Untuk Agensi)
     */
    public function index() 
    { 
        $rekod = PermohonanSeksyen12::where('agensi_id', Auth::guard('agensi')->id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Diselaraskan kepada folder permohonan/seksyen12/index
        return view('permohonan.seksyen12.index', compact('rekod')); 
    }

    /**
     * Borang Tambah Baru
     */
    public function create() 
    {
        return view('permohonan.seksyen12.create');
    }

    /**
     * Simpan Data Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'            => 'required',
            'no_kp'           => 'required',
            'jawatan'         => 'required',
            'tarikh_lantikan' => 'required|date',
            'tarikh_tt'       => 'required|date',
            'no_fail'         => 'required',
        ]);

        PermohonanSeksyen12::create([
            'agensi_id'       => Auth::guard('agensi')->id(),
            'nama'            => $request->nama,
            'no_kp'           => $request->no_kp,
            'jawatan'         => $request->jawatan,
            'position'        => $request->position,
            'pelantikan_bm'   => $request->pelantikan_bm, 
            'pelantikan_bi'   => $request->pelantikan_bi, 
            'tarikh_lantikan' => $request->tarikh_lantikan,
            'tarikh_tt'       => $request->tarikh_tt,
            'no_fail'         => $request->no_fail,
            'status'          => 'Belum Disemak',
        ]);

        return redirect()->route('permohonan.seksyen12')->with('success', 'Permohonan Seksyen 12 berjaya dihantar!');
    }

    /**
     * PAPARAN LIVE PREVIEW (Ganti fungsi download Word)
     */
    public function show($id)
    {
        $data = PermohonanSeksyen12::findOrFail($id);
        return view('permohonan.seksyen12.show', compact('data'));
    }

    /**
     * Borang Edit
     */
    public function edit($id)
    {
        $data = PermohonanSeksyen12::findOrFail($id);
        return view('permohonan.seksyen12.edit', compact('data'));
    }

    /**
     * Kemaskini Data
     */
    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen12::findOrFail($id);
        
        $request->validate([
            'nama'  => 'required',
            'no_kp' => 'required',
        ]);

        $data->update($request->all());

        return redirect()->route('permohonan.seksyen12')->with('success', 'Rekod berjaya dikemaskini!');
    }
}