<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen175d;
use Illuminate\Support\Facades\Auth;

class Seksyen175dController extends Controller
{
    public function index()
    {
        $senarai = PermohonanSeksyen175d::where('agensi_id', Auth::guard('agensi')->id())->get();
        return view('permohonan.seksyen175d.index', compact('senarai'));
    }

    public function create() { return view('permohonan.seksyen175d.create'); }

    public function store(Request $request)
    {
        $data = new PermohonanSeksyen175d();
        $data->agensi_id = Auth::guard('agensi')->id();
        $this->saveData($data, $request);
        return redirect()->route('permohonan.seksyen175d')->with('success', 'Borang 10H berjaya didaftarkan!');
    }

    public function show($id)
    {
        $data = PermohonanSeksyen175d::findOrFail($id);
        return view('permohonan.seksyen175d.show', compact('data'));
    }

    public function edit($id)
    {
        $data = PermohonanSeksyen175d::findOrFail($id);
        return view('permohonan.seksyen175d.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen175d::findOrFail($id);
        $this->saveData($data, $request);
        return redirect()->route('permohonan.seksyen175d')->with('success', 'Rekod berjaya dikemaskini!');
    }

    public function destroy($id)
    {
        PermohonanSeksyen175d::findOrFail($id)->delete();
        return redirect()->route('permohonan.seksyen175d')->with('success', 'Rekod dipadam!');
    }

    private function saveData($data, $request)
    {
        $data->no_fail = $request->no_fail;
        $data->daerah = $request->daerah;
        $data->mukim = $request->mukim;
        $data->jenis_hakmilik = $request->jenis_hakmilik;
        $data->no_hakmilik = $request->no_hakmilik;
        $data->no_lot = $request->no_lot;
        $data->luas = $request->luas;
        $data->nama_pemilik = $request->nama_pemilik;
        $data->no_kp_pemilik = $request->no_kp_pemilik;
        $data->bahagian_tanah = $request->bahagian_tanah;
        
        if (!$data->exists) {
            $data->status = 'Baru';
            $data->tarikh_notis = now();
        }
        
        $data->nama_pentadbir = $request->nama_pentadbir ?? "BARI'AH BINTI DZULKIFLI";
        $data->save();
    }
}