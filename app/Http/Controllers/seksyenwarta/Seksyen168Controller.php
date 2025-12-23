<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen168;
use Illuminate\Support\Facades\Auth;

class Seksyen168Controller extends Controller
{
    public function index()
    {
        $senarai = PermohonanSeksyen168::where('agensi_id', Auth::guard('agensi')->id())->get();
        return view('permohonan.seksyen168.index', compact('senarai'));
    }

    public function create()
    {
        return view('permohonan.seksyen168.create');
    }

    public function store(Request $request)
    {
        $data = new PermohonanSeksyen168();
        $data->agensi_id = Auth::guard('agensi')->id();
        $this->saveData($data, $request);

        return redirect()->route('permohonan.seksyen168')->with('success', 'Permohonan Seksyen 168 berjaya didaftarkan!');
    }

    public function show($id)
    {
        $data = PermohonanSeksyen168::findOrFail($id);
        return view('permohonan.seksyen168.show', compact('data'));
    }

    public function edit($id)
    {
        $data = PermohonanSeksyen168::findOrFail($id);
        return view('permohonan.seksyen168.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen168::findOrFail($id);
        $this->saveData($data, $request);

        return redirect()->route('permohonan.seksyen168')->with('success', 'Rekod berjaya dikemaskini!');
    }

    public function destroy($id)
    {
        $data = PermohonanSeksyen168::findOrFail($id);
        $data->delete();
        return redirect()->route('permohonan.seksyen168')->with('success', 'Rekod berjaya dipadam!');
    }

    private function saveData($data, $request)
    {
        $data->nama_pemilik = $request->nama_pemilik;
        $data->no_kp_pemilik = $request->no_kp_pemilik;
        $data->alamat_pemilik = $request->alamat_pemilik;
        $data->jenis_hakmilik = $request->jenis_hakmilik;
        $data->no_hakmilik = $request->no_hakmilik;
        $data->no_lot = $request->no_lot;
        $data->mukim = $request->mukim;
        $data->daerah = $request->daerah;
        $data->luas = $request->luas;
        $data->status = $request->status ?? 'Baru';
        $data->sebab_permohonan = $request->sebab_permohonan;
        
        if (!$data->exists) {
            $data->no_fail = "PTMT/S168/" . date('Y') . "/" . rand(100, 999);
            $data->tarikh_notis = now();
            $data->nama_pentadbir = "MOHD SHAIFUL HIZAM BIN JOHAN";
        } else {
            // Update fields khas edit
            if($request->has('nama_pentadbir')) $data->nama_pentadbir = $request->nama_pentadbir;
        }
        $data->save();
    }
}