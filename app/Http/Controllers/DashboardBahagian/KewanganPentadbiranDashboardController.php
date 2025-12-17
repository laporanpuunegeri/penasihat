<?php

namespace App\Http\Controllers\DashboardBahagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kewangan;       
use App\Models\WaranPerjawatan; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
class KewanganPentadbiranDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        
        $data_graf = [];
        $waranData = collect();
        $metadata = [];

        // ==========================================
        // LOGIK 1: DATA KEWANGAN
        // ==========================================
        $senarai_kod = [
            '10000' => 'EMOLUMEN',
            '20000' => 'PERKHIDMATAN DAN BEKALAN',
            '30000' => 'ASET',
            '40000' => 'PEMBERIAN DAN KENAAN BAYARAN TETAP',
            '50000' => 'PERBELANJAAN LAIN-LAIN'
        ];

        foreach ($senarai_kod as $kod => $tajuk) {
            $start = (int)$kod;
            $end   = $start + 10000;

            if (class_exists(Kewangan::class)) {
                $query = Kewangan::where('tahun', $tahun)
                    ->where('kod_objek', '>=', $start)
                    ->where('kod_objek', '<', $end);

                // Ambil jumlah Peruntukan
                $peruntukan = $query->sum('peruntukan');
                
                // ====================================================
                // 2. PEMBETULAN UTAMA (CAMPUR 12 BULAN)
                // ====================================================
                // Kita suruh database campur semua column bulan-bulan tu
                $belanja = $query->sum(DB::raw('
                    COALESCE(belanja_jan, 0) + 
                    COALESCE(belanja_feb, 0) + 
                    COALESCE(belanja_mac, 0) + 
                    COALESCE(belanja_apr, 0) + 
                    COALESCE(belanja_mei, 0) + 
                    COALESCE(belanja_jun, 0) + 
                    COALESCE(belanja_jul, 0) + 
                    COALESCE(belanja_ogos, 0) + 
                    COALESCE(belanja_sep, 0) + 
                    COALESCE(belanja_okt, 0) + 
                    COALESCE(belanja_nov, 0) + 
                    COALESCE(belanja_dis, 0)
                '));
                
                $baki    = $peruntukan - $belanja;
                $peratus = ($peruntukan > 0) ? ($belanja / $peruntukan) * 100 : 0;
            } else {
                $peruntukan = $belanja = $baki = $peratus = 0;
            }

            $data_graf[$kod] = [
                'tajuk'      => $tajuk,
                'peruntukan' => $peruntukan,
                'belanja'    => $belanja,
                'baki'       => $baki,
                'peratus'    => $peratus
            ];
        }

        // ==========================================
        // LOGIK 2: DATA PENTADBIRAN (WARAN PERJAWATAN)
        // ==========================================
        if (class_exists(WaranPerjawatan::class)) {
            $waranData = WaranPerjawatan::orderBy('id')->get();
            
            // Check kalau table kosong, elak error sum
            if($waranData->isNotEmpty()) {
                $metadata = [
                    'totalWaran'  => $waranData->sum('bil'),     // Ikut column migration baru
                    'totalIsi'    => $waranData->sum('isi'),     // Ikut column migration baru
                    'totalKosong' => $waranData->sum('kosong'),  // Ikut column migration baru
                    'tahun'       => $tahun
                ];
            } else {
                $metadata = ['totalWaran'=>0, 'totalIsi'=>0, 'totalKosong'=>0, 'tahun'=>$tahun];
            }
            
        } else {
             $metadata = [
                'totalWaran'  => 0,
                'totalIsi'    => 0,
                'totalKosong' => 0,
                'tahun'       => $tahun
            ];
        }

        return view('dashboard.pentadbirandankewangan', compact('data_graf', 'waranData', 'metadata', 'tahun'));
    }
}