<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaranPerjawatan;

class PentadbiranController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard.pentadbiran');
    }
    
    public function indexWaran(Request $request)
    {
        $waranData = WaranPerjawatan::orderBy('id')->get();
        // Memuatkan resources/views/pentadbiran/waran.blade.php
        return view('pentadbiran.waran', compact('waranData')); 
    }

    public function editWaran(Request $request)
    {
        $waranData = WaranPerjawatan::orderBy('id')->get();
        
        // 🔥 PEMBETULAN UTAMA: Menggunakan view 'pentadbiran.waran-edit' 
        // Ini adalah cara yang paling biasa untuk Laravel membaca fail bernama 'waran.edit.blade.php'
        // tanpa subfolder, iaitu dengan menukarkan titik (selepas folder) kepada sempang.
        // ATAU, jika ralat berterusan, cuba nama fail penuh:
        // return view('pentadbiran.waran.edit', compact('waranData')); 
        
        // Kita akan anggap anda telah menamakan semula fail kepada 'waran-edit.blade.php' 
        // untuk mengikut konvensyen dot notation Laravel:
        return view('pentadbiran.waran-edit', compact('waranData')); 
    }

    public function updateWaran(Request $request)
    {
        // Pengesahan data yang masuk dari borang
        $validatedData = $request->validate([
            'waran' => 'required|array',
            
            // PEMBETULAN NAMA JADUAL DARI RALAT SEBELUM INI (waran_perjawatans)
            'waran.*.id' => 'required|integer|exists:waran_perjawatans,id', 
            
            'waran.*.persekutuan' => 'required|integer|min:0',
            'waran.*.negeri' => 'required|integer|min:0',
            'waran.*.nota' => 'nullable|string|max:255',
        ]);

        foreach ($validatedData['waran'] as $id => $data) {
            $waran = WaranPerjawatan::find($data['id']); 
            if ($waran) {
                $bil_waran = $waran->bil;
                $bil_isi = $data['persekutuan'] + $data['negeri'];
                $bil_kosong = $bil_waran - $bil_isi;
                
                $waran->update([
                    'persekutuan' => $data['persekutuan'],
                    'negeri' => $data['negeri'],
                    'isi' => $bil_isi,
                    'kosong' => $bil_kosong,
                    'nota' => $data['nota'],
                ]);
            }
        }

        // Kembali ke route edit waran, yang kini menggunakan view 'waran-edit'
        return redirect()->route('pentadbiran.waran.edit')->with('success', 'Waran Perjawatan berjaya dikemaskini.');
    }
}