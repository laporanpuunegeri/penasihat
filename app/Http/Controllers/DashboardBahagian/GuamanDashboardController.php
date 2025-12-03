<?php

namespace App\Http\Controllers\DashboardBahagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GuamanCase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Pastikan Carbon diimport

class GuamanDashboardController extends Controller
{
    /**
     * Helper untuk mendapatkan data jumlah kes mengikut bulan (12 bulan).
     */
    private function getMonthlyData($model, $tahun, $label)
    {
        // 1. Array untuk menyimpan kiraan bagi 12 bulan (default 0)
        $monthlyCounts = array_fill(0, 12, 0); 

        // 2. Query untuk mengira kes yang dicipta mengikut bulan
        $data = $model::select(DB::raw('EXTRACT(MONTH FROM created_at) as bulan'), DB::raw('COUNT(*) as jumlah'))
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan')
            ->pluck('jumlah', 'bulan')
            ->toArray();

        // 3. Masukkan jumlah ke dalam array 12 bulan
        foreach ($data as $bulan => $jumlah) {
            $index = (int)$bulan - 1; 
            if (isset($monthlyCounts[$index])) {
                $monthlyCounts[$index] = $jumlah;
            }
        }

        // 4. Format data untuk Chart.js
        return [
            'labels' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogos', 'Sep', 'Okt', 'Nov', 'Dis'],
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $monthlyCounts,
                    'backgroundColor' => 'rgba(66, 135, 245, 0.7)',
                    'borderColor' => 'rgba(66, 135, 245, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Paparkan Dashboard Modul Guaman.
     * Mengambil data statistik: Kendalian Oleh, Kod Perkara, dan Mahkamah.
     */
    public function dashboard()
    {
        $tahun = date('Y'); 

        // 1. Ambil SEMUA DATA KES (Dasar untuk semua kiraan)
        $allCases = GuamanCase::all();
        $totalCases = $allCases->count();
        
        // 2. LOGIK PENJANAAN DATA GRAF PIE
        
        // 2a. Kendalian Oleh
        $dataKendalian = $allCases->groupBy('kendalian_oleh')->map(fn ($item) => $item->count());
        $casesByKendalianGraph = [
            'labels' => $dataKendalian->keys()->map(fn ($label) => \Illuminate\Support\Str::limit($label, 15)),
            'data' => $dataKendalian->values(),
        ];
        
        // 2b. Kod Perkara
        $dataKod = $allCases->groupBy('kod_perkara')->map(fn ($item) => $item->count());
        $casesByKodGraph = [
            'labels' => $dataKod->keys()->map(fn ($label) => 'KOD ' . $label),
            'data' => $dataKod->values(),
        ];
        
        // 2c. Mahkamah
        $dataMahkamah = $allCases->groupBy('mahkamah')->map(fn ($item) => $item->count());
        $casesByMahkamahGraph = [
            'labels' => $dataMahkamah->keys()->map(fn ($label) => \Illuminate\Support\Str::limit($label, 20)),
            'data' => $dataMahkamah->values(),
        ];
        
        // 3. LOGIK DATA GRAF BULANAN (BAR CHART)
        $dataMonthlyGraph = $this->getMonthlyData(GuamanCase::class, $tahun, 'Kes Berdaftar ('.$tahun.')');

        // 4. Tentukan Tajuk
        $title = "Dashboard Modul Guaman";
        
        // 5. Hantar data ke view
        return view('dashboard.guaman', compact(
            'totalCases', 
            'casesByKendalianGraph', 
            'casesByKodGraph', 
            'casesByMahkamahGraph', 
            'dataMonthlyGraph', 
            'title'
        ));
    }
}