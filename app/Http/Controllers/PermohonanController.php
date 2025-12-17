<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PermohonanWarta; 

class PermohonanController extends Controller
{
    public function paparSeksyen12() { 
        return view('permohonan.seksyen12'); 
    }

    public function paparSeksyen62() { 
        return view('permohonan.seksyen62'); 
    }

    public function paparSeksyen64() { 
        return view('permohonan.seksyen64'); 
    }

    public function paparSeksyen9798() { 
        return view('permohonan.seksyen9798'); 
    }

    public function paparSeksyen130() { 
        return view('permohonan.seksyen130'); 
    }

    public function paparSeksyen168() { 
        return view('permohonan.seksyen168'); 
    }

    public function paparSeksyen175A() { 
        return view('permohonan.seksyen175A'); 
    }

    public function paparSeksyen175D() { 
        return view('permohonan.seksyen175D'); 
    }

    public function paparSeksyen261() { 
        return view('permohonan.seksyen261'); 
    }

    public function paparSeksyen263() { 
        return view('permohonan.seksyen263'); 
    }

    public function paparSeksyen326() { 
        return view('permohonan.seksyen326'); 
    }

    public function store(Request $request)
    {
        // Validation asas (boleh tambah ikut keperluan setiap seksyen)
        $request->validate([
            'jenis_warta' => 'required',
            // 'tajuk_bm' => 'required', // Contoh validation lain
        ]);

        // Simpan ke Database
        PermohonanWarta::create([
            'agensi_id' => Auth::guard('agensi')->id(),
            'jenis_warta' => $request->jenis_warta,
            'status' => 'baru',
        
            'tajuk_bm' => $request->tajuk_bm,
            'tajuk_bi' => $request->tajuk_bi,
            'nama_calon_bm' => $request->nama_calon_bm,

        ]);

        return redirect()->route('dashboard.warta')->with('success', 'Permohonan berjaya dihantar!');
    }
}