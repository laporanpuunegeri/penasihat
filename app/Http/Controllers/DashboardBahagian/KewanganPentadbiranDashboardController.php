<?php

namespace App\Http\Controllers\DashboardBahagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kewangan;       
use App\Models\WaranPerjawatan; 
use Illuminate\Support\Facades\Auth;

class KewanganPentadbiranDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. Ambil Input Tahun & User
        $tahunRequest = $request->input('tahun', date('Y'));
        $user = Auth::user();

        // 2. Sediakan Bakul Data Kosong
        $data_graf = [
            '10000' => ['tajuk' => 'EMOLUMEN', 'peruntukan' => 0, 'belanja' => 0, 'baki' => 0, 'peratus' => 0],
            '20000' => ['tajuk' => 'PERKHIDMATAN DAN BEKALAN', 'peruntukan' => 0, 'belanja' => 0, 'baki' => 0, 'peratus' => 0],
            '30000' => ['tajuk' => 'ASET', 'peruntukan' => 0, 'belanja' => 0, 'baki' => 0, 'peratus' => 0],
            '40000' => ['tajuk' => 'PEMBERIAN DAN KENAAN BAYARAN TETAP', 'peruntukan' => 0, 'belanja' => 0, 'baki' => 0, 'peratus' => 0],
            '50000' => ['tajuk' => 'PERBELANJAAN LAIN-LAIN', 'peruntukan' => 0, 'belanja' => 0, 'baki' => 0, 'peratus' => 0],
        ];

        // Variable Grand Total
        $totalPeruntukan = 0;
        $totalBelanja = 0;
        $rekodKewangan = collect();

        // 3. Proses Data
        if (class_exists(Kewangan::class)) {
            
            // Tarik semua data (Brute Force)
            $semuaData = Kewangan::all();

            // Tapis Data (Filter)
            $rekodKewangan = $semuaData->filter(function ($item) use ($tahunRequest, $user) {
                // Bersihkan Data DB
                $tahunDB    = trim((string)$item->tahun);
                $tahunReq   = trim((string)$tahunRequest);
                $negeriDB   = strtolower(trim($item->negeri));
                $negeriUser = strtolower(trim($user->negeri));

                // A. Filter Negeri (User Biasa Sahaja)
                if ($user->negeri && $negeriDB !== $negeriUser) {
                    return false;
                }

                // B. Filter Tahun (Logic: Terima Tahun Sama ATAU Tahun Kosong)
                if ($tahunDB !== $tahunReq && $tahunDB !== "") {
                    return false;
                }

                return true;
            });

            // Loop & Kira
            foreach ($rekodKewangan as $item) {
                
                // Bersihkan Nombor
                $peruntukan = (float) str_replace([',', 'RM', ' '], '', $item->peruntukan);
                $belanja    = (float) str_replace([',', 'RM', ' '], '', $item->belanja);

                // Kenal pasti Group Kod (11000 -> 10000)
                $kodGroup = substr((string)$item->kod_objek, 0, 1) . '0000';

                // Tambah ke Bakul
                if (isset($data_graf[$kodGroup])) {
                    $data_graf[$kodGroup]['peruntukan'] += $peruntukan;
                    $data_graf[$kodGroup]['belanja']    += $belanja;
                }
            }

            // Pengiraan Akhir (Formula Abang)
            foreach ($data_graf as $kod => $data) {
                
                // 1. Kira Baki (Peruntukan - Belanja)
                $baki = $data['peruntukan'] - $data['belanja'];
                
                // 2. Kira Peratus Belanja (Belanja / Peruntukan * 100)
                $peratus = ($data['peruntukan'] > 0) 
                    ? ($data['belanja'] / $data['peruntukan']) * 100 
                    : 0;

                // Simpan nilai
                $data_graf[$kod]['baki']    = $baki;
                $data_graf[$kod]['peratus'] = $peratus;

                // Grand Total
                $totalPeruntukan += $data['peruntukan'];
                $totalBelanja    += $data['belanja'];
            }
        }

        // Prestasi Keseluruhan
        $prestasi = ($totalPeruntukan > 0) ? ($totalBelanja / $totalPeruntukan) * 100 : 0;

        // Data Waran (Kekal)
        $waranData = collect();
        $metadata = ['totalWaran'=>0, 'totalIsi'=>0, 'totalKosong'=>0, 'tahun'=>$tahunRequest];
        
        if (class_exists(WaranPerjawatan::class)) {
            $waranData = WaranPerjawatan::all();
            if($waranData->isNotEmpty()) {
                $metadata = [
                    'totalWaran' => $waranData->sum('bil'),     
                    'totalIsi' => $waranData->sum('isi'),     
                    'totalKosong' => $waranData->sum('kosong'),  
                    'tahun' => $tahunRequest
                ];
            }
        }

        $tahun = $tahunRequest;

        return view('dashboard.pentadbirandankewangan', compact(
            'data_graf', 
            'waranData', 
            'metadata', 
            'tahun', 
            'rekodKewangan', 
            'totalPeruntukan', 
            'totalBelanja', 
            'prestasi'
        ));
    }
}