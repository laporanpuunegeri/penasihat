<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen175a;
use Illuminate\Support\Facades\Auth;

class Seksyen175aController extends Controller
{
    public function index()
    {
        $senarai = PermohonanSeksyen175a::where('agensi_id', Auth::guard('agensi')->id())->get();
        return view('permohonan.seksyen175a.index', compact('senarai'));
    }

    public function create() 
    { 
        return view('permohonan.seksyen175a.create'); 
    }

    public function store(Request $request)
    {
        $data = new PermohonanSeksyen175a();
        $data->agensi_id = Auth::guard('agensi')->id();
        $this->saveData($data, $request);
        return redirect()->route('permohonan.seksyen175a')->with('success', 'Borang 10E berjaya didaftarkan!');
    }

    public function show($id)
    {
        $data = PermohonanSeksyen175a::findOrFail($id);
        return view('permohonan.seksyen175a.show', compact('data'));
    }

    // --- TAMBAHAN UNTUK EDIT & UPDATE ---
    public function edit($id)
    {
        $data = PermohonanSeksyen175a::findOrFail($id);
        return view('permohonan.seksyen175a.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen175a::findOrFail($id);
        $this->saveData($data, $request);
        return redirect()->route('permohonan.seksyen175a')->with('success', 'Rekod berjaya dikemaskini!');
    }

    public function destroy($id)
    {
        $data = PermohonanSeksyen175a::findOrFail($id);
        $data->delete();
        return redirect()->route('permohonan.seksyen175a')->with('success', 'Rekod berjaya dipadam!');
    }

    private function saveData($data, $request)
    {
        $data->no_fail = $request->no_fail;
        $data->jenis_hakmilik = $request->jenis_hakmilik;
        $data->no_hakmilik = $request->no_hakmilik;
        $data->no_lot = $request->no_lot;
        $data->luas = $request->luas;
        $data->mukim = $request->mukim;
        $data->daerah = $request->daerah;
        $data->sebab_penyediaan = $request->sebab_penyediaan;
        $data->status = $request->status ?? 'Baru';
        $data->tarikh_notis = $request->tarikh_notis ?? now();
        $data->nama_pentadbir = $request->nama_pentadbir ?? "BARI'AH BINTI DZULKIFLI";
        $data->save();
    }
}