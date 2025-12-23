<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen130;
use Illuminate\Support\Facades\Auth;

class Seksyen130Controller extends Controller
{
    public function index()
    {
        $senarai = PermohonanSeksyen130::where('agensi_id', Auth::guard('agensi')->id())->get();
        return view('permohonan.seksyen130.index', compact('senarai'));
    }

    public function create()
    {
        return view('permohonan.seksyen130.create');
    }

    public function store(Request $request)
    {
        $data = new PermohonanSeksyen130();
        $data->agensi_id = Auth::guard('agensi')->id();
        $this->saveData($data, $request);

        return redirect()->route('permohonan.seksyen130')->with('success', 'Borang 8A berjaya didaftarkan!');
    }

    public function show($id)
    {
        $data = PermohonanSeksyen130::findOrFail($id);
        // Kita guna 'show' supaya seragam macam Seksyen 97
        return view('permohonan.seksyen130.show', compact('data'));
    }

    public function edit($id)
    {
        $data = PermohonanSeksyen130::findOrFail($id);
        return view('permohonan.seksyen130.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen130::findOrFail($id);
        $this->saveData($data, $request);

        return redirect()->route('permohonan.seksyen130')->with('success', 'Rekod berjaya dikemaskini!');
    }

    private function saveData($data, $request)
    {
        $data->nama_pemilik = $request->nama_pemilik;
        $data->no_kp_pemilik = $request->no_kp_pemilik;
        $data->alamat_pemilik = $request->alamat_pemilik;
        $data->jenis_hakmilik = $request->jenis_hakmilik;
        $data->no_hakmilik = $request->no_hakmilik;
        $data->no_lot = $request->no_lot;
        $data->luas = $request->luas;
        $data->mukim = $request->mukim;
        $data->daerah = $request->daerah;
        
        if (!$data->exists) {
            $data->no_fail = "PTMT/S130/" . date('Y') . "/" . rand(100, 999);
            $data->status = 'Baru';
            $data->tarikh_notis = now();
            $data->nama_pentadbir = "MOHD SHAIFUL HIZAM BIN JOHAN";
        }
        $data->save();
    }
}