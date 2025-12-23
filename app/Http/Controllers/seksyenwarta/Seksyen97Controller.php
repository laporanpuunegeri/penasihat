<?php

namespace App\Http\Controllers\seksyenwarta; 

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use App\Models\PermohonanSeksyen97; 

class Seksyen97Controller extends Controller
{
    // 1. PAPAR SENARAI PERMOHONAN
    public function index()
    {
        $senarai = PermohonanSeksyen97::where('agensi_id', Auth::guard('agensi')->id())
                                      ->orderBy('created_at', 'desc')
                                      ->get();

        return view('permohonan.seksyen97.index', compact('senarai'));
    }

    // 2. PAPAR BORANG PERMOHONAN BARU
    public function create()
    {
        return view('permohonan.seksyen97.create');
    }

    // 3. SIMPAN DATA KE DATABASE
    public function store(Request $request)
    {
        $request->validate([
            'nama_pemilik' => 'required',
            'no_hakmilik' => 'required',
            'jumlah_besar' => 'required',
        ]);

        $permohonan = new PermohonanSeksyen97();
        $permohonan->agensi_id = Auth::guard('agensi')->id();

        $this->saveData($permohonan, $request);

        return redirect()->route('permohonan.seksyen97')->with('success', 'Borang 6A berjaya disimpan!');
    }

    // 4. PAPAR SURAT (SAYA DAH TUKAR KE SHOW.BLADE.PHP)
    public function show($id)
    {
        $data = PermohonanSeksyen97::findOrFail($id);
        
        // PEMBETULAN: Guna 'show' bukan 'pdf'
        return view('permohonan.seksyen97.show', compact('data'));
    }

    // 5. PAPAR BORANG EDIT
    public function edit($id)
    {
        $data = PermohonanSeksyen97::findOrFail($id);
        return view('permohonan.seksyen97.edit', compact('data'));
    }

    // 6. KEMASKINI DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pemilik' => 'required',
            'no_hakmilik' => 'required',
            'jumlah_besar' => 'required',
        ]);

        $permohonan = PermohonanSeksyen97::findOrFail($id);
        $this->saveData($permohonan, $request);

        return redirect()->route('permohonan.seksyen97')->with('success', 'Rekod berjaya dikemaskini!');
    }

    // 7. PADAM DATA
    public function destroy($id)
    {
        $permohonan = PermohonanSeksyen97::findOrFail($id);
        $permohonan->delete();

        return redirect()->route('permohonan.seksyen97')->with('success', 'Rekod berjaya dipadam!');
    }

    // FUNCTION HELPER UNTUK SIMPAN/UPDATE
    private function saveData($permohonan, $request)
    {
        $permohonan->nama_pemilik = $request->nama_pemilik;
        $permohonan->no_kp_pemilik = $request->no_kp_pemilik;
        $permohonan->alamat_pemilik = $request->alamat_pemilik;
        $permohonan->jenis_hakmilik = $request->jenis_hakmilik;
        $permohonan->no_hakmilik = $request->no_hakmilik;
        $permohonan->no_lot = $request->no_lot;
        $permohonan->mukim = $request->mukim;
        $permohonan->daerah = $request->daerah;
        $permohonan->sewa_tahun_semasa = $request->sewa_tahun_semasa;
        $permohonan->tempoh_tunggakan = $request->tempoh_tunggakan;
        $permohonan->jumlah_tunggakan = $request->jumlah_tunggakan;
        $permohonan->denda = $request->denda;
        $permohonan->kos_notis = $request->kos_notis;
        $permohonan->jumlah_besar = $request->jumlah_besar;
        $permohonan->nama_bank = $request->nama_bank;
        $permohonan->alamat_bank = $request->alamat_bank;

        if (!$permohonan->exists) {
            $permohonan->no_fail = "PTMT/" . date('Y') . "/" . rand(1000,9999);
            $permohonan->tarikh_notis = now();
            $permohonan->nama_pentadbir = "MOHD SHAIFUL HIZAM BIN JOHAN";
            $permohonan->status = 'Baru';
        }

        $permohonan->save();
    }
}