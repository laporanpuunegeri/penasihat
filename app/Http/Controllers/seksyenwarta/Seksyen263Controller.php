<?php

namespace App\Http\Controllers\seksyenwarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanSeksyen263;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Seksyen263Controller extends Controller
{
    public function index()
    {
        // Ambil senarai permohonan mengikut agensi yang sedang login
        $senarai = PermohonanSeksyen263::where('agensi_id', Auth::guard('agensi')->id())->get();
        return view('permohonan.seksyen263.index', compact('senarai'));
    }

    public function create()
    {
        return view('permohonan.seksyen263.create');
    }

    public function store(Request $request)
    {
        $data = new PermohonanSeksyen263();
        $data->agensi_id = Auth::guard('agensi')->id();
        $data->status = 'Baru'; // Status default untuk agensi
        
        $this->saveData($data, $request);

        return redirect()->route('permohonan.seksyen263')->with('success', 'Borang 16H berjaya didaftarkan!');
    }

    public function show($id)
    {
        $data = PermohonanSeksyen263::findOrFail($id);
        return view('permohonan.seksyen263.show', compact('data'));
    }

    public function edit($id)
    {
        $data = PermohonanSeksyen263::findOrFail($id);
        return view('permohonan.seksyen263.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = PermohonanSeksyen263::findOrFail($id);
        
        // Agensi tidak boleh kemaskini status melalui form Edit
        $this->saveData($data, $request);

        return redirect()->route('permohonan.seksyen263')->with('success', 'Rekod Borang 16H berjaya dikemaskini!');
    }

    private function saveData($data, $request)
    {
        $data->no_fail = $request->no_fail;
        $data->nama_pentadbir_tanah = $request->nama_pentadbir_tanah;
        $data->ic_pentadbir = $request->ic_pentadbir;
        $data->tarikh_lelongan = $request->tarikh_lelongan;
        $data->hari_lelongan = $request->hari_lelongan;
        $data->masa_lelongan = $request->masa_lelongan;
        $data->tempat_lelongan = $request->tempat_lelongan;
        $data->harga_rizab = $request->harga_rizab;
        $data->deposit_sepuluh_peratus = $request->deposit_sepuluh_peratus;
        $data->amaun_hutang = $request->amaun_hutang;
        $data->nama_pemegang_gadai = $request->nama_pemegang_gadai;
        $data->tarikh_perintah = $request->tarikh_perintah;

        // Logik Pengiraan Tarikh Akhir Bayaran (120 hari dari tarikh lelongan)
        // Berdasarkan syarat (d) dalam Borang 16H 
        if ($request->tarikh_lelongan) {
            $data->tarikh_akhir_bayaran = Carbon::parse($request->tarikh_lelongan)->addDays(120);
        }

        // Maklumat Tanah
        $data->mukim = $request->mukim;
        $data->no_lot = $request->no_lot;
        $data->jenis_hakmilik = $request->jenis_hakmilik;
        $data->no_hakmilik = $request->no_hakmilik;
        $data->bahagian_tanah = $request->bahagian_tanah;
        $data->no_daftar_gadaian = $request->no_daftar_gadaian;

        $data->save();
    }
}