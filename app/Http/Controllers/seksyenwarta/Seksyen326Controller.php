<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen326;
use Illuminate\Support\Facades\Auth;

class Seksyen326Controller extends Controller
{
    public function index()
    {
        $senarai = PermohonanSeksyen326::where('agensi_id', Auth::guard('agensi')->id())->get();
        return view('permohonan.seksyen326.index', compact('senarai'));
    }

    public function create() { return view('permohonan.seksyen326.create'); }

    public function store(Request $request)
    {
        $data = new PermohonanSeksyen326();
        $data->agensi_id = Auth::guard('agensi')->id();
        $data->status = 'Baru';
        $this->saveData($data, $request);
        return redirect()->route('permohonan.seksyen326')->with('success', 'Borang 19C berjaya didaftarkan!');
    }

    public function show($id)
    {
        $data = PermohonanSeksyen326::findOrFail($id);
        return view('permohonan.seksyen326.show', compact('data'));
    }

    public function edit($id)
    {
        $data = PermohonanSeksyen326::findOrFail($id);
        return view('permohonan.seksyen326.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen326::findOrFail($id);
        $this->saveData($data, $request);
        return redirect()->route('permohonan.seksyen326')->with('success', 'Rekod berjaya dikemaskini!');
    }

   private function saveData($data, $request)
{
    $data->no_fail = $request->no_fail;
    $data->nama_penerima = $request->nama_penerima;
    $data->ic_penerima = $request->ic_penerima;
    $data->alamat_penerima = $request->alamat_penerima;
    $data->no_perserahan_kaveat = $request->no_perserahan_kaveat;
    $data->nama_pemohon = $request->nama_pemohon;
    
    // Simpan Pilihan Dropdown & Input Teks
    $data->jenis_kawasan = $request->jenis_kawasan;
    $data->nama_kawasan = $request->nama_kawasan;
    $data->jenis_lot = $request->jenis_lot;
    $data->no_lot = $request->no_lot;

    $data->jenis_hakmilik = $request->jenis_hakmilik;
    $data->no_hakmilik = $request->no_hakmilik;
    $data->tarikh_notis = $request->tarikh_notis;
    $data->nama_pentadbir = $request->nama_pentadbir;
    $data->save();
}
}