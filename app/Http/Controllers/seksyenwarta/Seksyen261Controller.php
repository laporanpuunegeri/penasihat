<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen261;
use Illuminate\Support\Facades\Auth;

class Seksyen261Controller extends Controller
{
    public function index()
    {
        $senarai = PermohonanSeksyen261::where('agensi_id', Auth::guard('agensi')->id())->get();
        return view('permohonan.seksyen261.index', compact('senarai'));
    }

    public function create() { return view('permohonan.seksyen261.create'); }

    public function store(Request $request)
    {
        $data = new PermohonanSeksyen261();
        $data->agensi_id = Auth::guard('agensi')->id();
        $this->saveData($data, $request);
        return redirect()->route('permohonan.seksyen261')->with('success', 'Borang 16G berjaya didaftarkan!');
    }

    public function show($id)
    {
        $data = PermohonanSeksyen261::findOrFail($id);
        return view('permohonan.seksyen261.show', compact('data'));
    }

    public function edit($id)
    {
        $data = PermohonanSeksyen261::findOrFail($id);
        return view('permohonan.seksyen261.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen261::findOrFail($id);
        $this->saveData($data, $request);
        return redirect()->route('permohonan.seksyen261')->with('success', 'Rekod berjaya dikemaskini!');
    }

    private function saveData($data, $request)
    {
        $data->no_fail = $request->no_fail;
        $data->nama_penggadai = $request->nama_penggadai;
        $data->alamat_penggadai = $request->alamat_penggadai;
        $data->nama_pemegang_gadai = $request->nama_pemegang_gadai;
        $data->tempat_siasatan = $request->tempat_siasatan;
        $data->tarikh_siasatan = $request->tarikh_siasatan;
        $data->masa_siasatan = $request->masa_siasatan;
        $data->mukim = $request->mukim;
        $data->no_lot = $request->no_lot;
        $data->jenis_hakmilik = $request->jenis_hakmilik;
        $data->no_hakmilik = $request->no_hakmilik;
        $data->bahagian_tanah = $request->bahagian_tanah;
        $data->no_daftar_gadaian = $request->no_daftar_gadaian;

        // Logic Kunci Status
        if (!$data->exists) {
            $data->status = 'Baru';
            $data->tarikh_notis = now();
        }
        $data->nama_pentadbir = $request->nama_pentadbir ?? "MOHD HAIRY BIN JAPAH";
        $data->save();
    }
}