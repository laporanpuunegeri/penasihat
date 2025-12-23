<?php

namespace App\Http\Controllers\SeksyenWarta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WartaAdminController extends Controller
{
    // Senarai Tajuk untuk paparan
    private $senaraiTajuk = [
        '12'   => 'Seksyen 12 - Serah Balik Kurnia Semula',
        '62'   => 'Seksyen 62 - Pewartaan Rizab',
        '64'   => 'Seksyen 64 - Pembatalan Rizab',
        '97'   => 'Seksyen 97 & 98 - Notis Tuntutan',
        '130'  => 'Seksyen 130 - Pelucuthakan Tanah',
        '168'  => 'Seksyen 168 - Gantian Hakmilik Hilang',
        '175a' => 'Seksyen 175A - Penyelesaian Pusaka',
        '175d' => 'Seksyen 175D - Perintah Pentadbir Tanah',
        '261'  => 'Seksyen 261 - Lelongan Tanah',
        '263'  => 'Seksyen 263 - Jualan Atas Permintaan Gadai',
        '326'  => 'Seksyen 326 - Notis Memotong Kaveat',
    ];

    /**
     * INDEX: Senarai Semakan (Admin)
     */
    public function index(Request $request)
    {
        $seksyen = $request->get('type', '12'); 
        $search  = $request->get('search'); 

        // 1. Tentukan Model Dinamik
        $suffix = ctype_digit($seksyen) ? $seksyen : strtoupper($seksyen);
        $modelClass = "\\App\\Models\\PermohonanSeksyen" . $suffix;
        $title = $this->senaraiTajuk[$seksyen] ?? "Seksyen " . strtoupper($seksyen);

        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', "Model untuk Seksyen $seksyen belum dijumpai.");
        }

        $query = $modelClass::with('agensi');

        // 2. Logic Search (No Fail & Agensi)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_fail', 'LIKE', "%{$search}%")
                  ->orWhereHas('agensi', function($qAgensi) use ($search) {
                      $qAgensi->where('nama_agensi', 'LIKE', "%{$search}%");
                  });
            });
        }

        $senarai = $query->latest()->get();

        return view('admin.warta.index', compact('senarai', 'title', 'seksyen'));
    }

    /**
     * SHOW: Admin Lihat Borang (Guna View Agensi)
     */
    public function show(Request $request, $id)
    {
        // Ambil jenis seksyen dari URL (?type=12)
        $seksyen = $request->get('type', '12');
        
        // 1. Tentukan Model
        $suffix = ctype_digit($seksyen) ? $seksyen : strtoupper($seksyen);
        $modelClass = "\\App\\Models\\PermohonanSeksyen" . $suffix;

        if (!class_exists($modelClass)) {
            return back()->with('error', 'Model tidak dijumpai.');
        }

        // 2. Cari Data
        $data = $modelClass::findOrFail($id);

        // 3. Tentukan View (Guna view folder 'permohonan' yang kita buat untuk Agensi)
        // Contoh: permohonan.seksyen12.show, permohonan.seksyen263.show
        $viewName = "permohonan.seksyen" . strtolower($seksyen) . ".show";

        if (!view()->exists($viewName)) {
            return back()->with('error', "Fail paparan ($viewName) belum wujud.");
        }

        // Paparkan view tersebut
        return view($viewName, compact('data'));
    }

    /**
     * SAHKAN: Update Status
     */
    public function sahkan(Request $request, $id)
    {
        $seksyen = $request->get('type', '12');
        $suffix = ctype_digit($seksyen) ? $seksyen : strtoupper($seksyen);
        $modelClass = "\\App\\Models\\PermohonanSeksyen" . $suffix;
        
        if (class_exists($modelClass)) {
            $data = $modelClass::find($id);
            if ($data) {
                $data->update(['status' => 'Telah Disemak']);
                return redirect()->back()->with('success', "Rekod berjaya disahkan!");
            }
        }
        return redirect()->back()->with('error', 'Ralat sistem.');
    }
}