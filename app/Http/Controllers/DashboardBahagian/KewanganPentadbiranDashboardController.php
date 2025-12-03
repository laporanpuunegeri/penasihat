<?php

namespace App\Http\Controllers\DashboardBahagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Sila pastikan Model ini wujud dan diimport
use App\Models\Kewangan;       
use App\Models\WaranPerjawatan; 
use Illuminate\Support\Facades\Auth;

class KewanganPentadbiranDashboardController extends Controller
{
    /**
     * Method Utama untuk memaparkan Dashboard Pentadbiran/Kewangan/Gabungan
     * Method ini dipanggil oleh ketiga-tiga route:
     * - dashboard.pentadbiran
     * - dashboard.kewangan
     * - dashboard.pentadbirandankewangan
     */
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

            // Pastikan model Kewangan wujud sebelum memanggilnya
            if (class_exists(Kewangan::class)) {
                $query = Kewangan::where('tahun', $tahun)
                    ->where('kod_objek', '>=', $start)
                    ->where('kod_objek', '<', $end);

                $peruntukan = $query->sum('peruntukan');
                $belanja    = $query->sum('belanja');
                $baki       = $peruntukan - $belanja;
                $peratus    = ($peruntukan > 0) ? ($belanja / $peruntukan) * 100 : 0;
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
            $metadata = [
                'totalWaran'  => $waranData->sum('bil'),
                'totalIsi'    => $waranData->sum('isi'),
                'totalKosong' => $waranData->sum('kosong'),
                'tahun'       => $tahun
            ];
        } else {
             $metadata = [
                'totalWaran'  => 0,
                'totalIsi'    => 0,
                'totalKosong' => 0,
                'tahun'       => $tahun
            ];
        }

        // Sentiasa pulangkan view gabungan, tidak kira route mana yang dipanggil
        return view('dashboard.pentadbirandankewangan', compact('data_graf', 'waranData', 'metadata', 'tahun'));
    }
}