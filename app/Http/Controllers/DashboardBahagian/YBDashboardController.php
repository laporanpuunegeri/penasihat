<?php

namespace App\Http\Controllers\DashboardBahagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// --- 1. IMPORT MODEL PENASIHAT ---
use App\Models\LaporanPandanganUndang;
use App\Models\LaporanKesMahkamah;
use App\Models\LaporanGubalanUndang;
use App\Models\LaporanPindaanUndang;
use App\Models\LaporanSemakanUndang;
use App\Models\LaporanMesyuarat;
use App\Models\Kestatatertib;
use App\Models\LainLainTugasan;

// --- 2. IMPORT MODEL GUAMAN ---
use App\Models\GuamanCase; 

// --- 3. IMPORT MODEL KEWANGAN ---
use App\Models\Kewangan; 

class YBDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tentukan Tahun 
        $tahun = $request->tahun ?? now()->year;
        
        // 2. Buat versi String untuk perbandingan tepat
        $tahunString = trim((string)$tahun);

        // =============================================
        // A. DATA PENASIHAT (8 SKOP) - KEKAL
        // =============================================
        $dataPenasihat = [
            'pandangan'  => $this->getGlobalStats(LaporanPandanganUndang::class, $tahun, 'Pandangan'),
            'mahkamah'   => $this->getGlobalStats(LaporanKesMahkamah::class, $tahun, 'Kes Mahkamah'),
            'gubalan'    => $this->getGlobalStats(LaporanGubalanUndang::class, $tahun, 'Gubalan'),
            'pindaan'    => $this->getGlobalStats(LaporanPindaanUndang::class, $tahun, 'Pindaan'),
            'semakan'    => $this->getGlobalStats(LaporanSemakanUndang::class, $tahun, 'Semakan'),
            'mesyuarat'  => $this->getGlobalStats(LaporanMesyuarat::class, $tahun, 'Mesyuarat'),
            'tatatertib' => $this->getGlobalStats(Kestatatertib::class, $tahun, 'Tatatertib'),
            'tugasan'    => $this->getGlobalStats(LainLainTugasan::class, $tahun, 'Lain-lain'),
        ];

        // =============================================
        // B. DATA GUAMAN - KEKAL
        // =============================================
        $dataGuaman = $this->getGlobalStats(GuamanCase::class, $tahun, 'Kes Guaman');

        // =============================================
        // C. DATA KEWANGAN (YANG DIBETULKAN)
        // =============================================
        
        $totalPeruntukan = 0;
        $totalBelanja    = 0;

        // Tarik SEMUA data (Tanpa sebarang filter SQL supaya tak terlepas data)
        $semuaKewangan = Kewangan::all();

        foreach ($semuaKewangan as $item) {
            
            // Bersihkan Tahun DB
            $tahunDB = trim((string)$item->tahun);

            // --- LOGIK POWER ---
            // Terima data jika:
            // 1. Tahun database SAMA dengan tahun diminta (2026)
            // 2. ATAU Tahun database KOSONG ("")
            // 3. ATAU Tahun database NULL
            
            if ($tahunDB === $tahunString || $tahunDB === "" || $item->tahun === null) {
                
                // Cuci Peruntukan (Buang 'RM', ',', ' ')
                $p = (float) str_replace([',', 'RM', ' '], '', $item->peruntukan);
                $totalPeruntukan += $p;

                // Cuci Belanja
                $b = (float) str_replace([',', 'RM', ' '], '', $item->belanja);
                $totalBelanja += $b;
            }
        }

        // Kira Baki
        $totalBaki = $totalPeruntukan - $totalBelanja;
        
        // Data untuk Pie Chart
        $dataKewanganChart = [
            'labels' => ['Belanja', 'Baki'],
            'datasets' => [[
                'data' => [$totalBelanja, $totalBaki],
                'backgroundColor' => ['#ef5350', '#66bb6a'], 
            ]]
        ];

        // PENTING: Guna view 'dashboard.yb' sebab view lain takkan faham variable $dataPenasihat
        return view('dashboard.yb', compact(
            'dataPenasihat', 
            'dataGuaman', 
            'dataKewanganChart', 
            'totalPeruntukan', 
            'totalBelanja', 
            'totalBaki', 
            'tahun'
        ));
    }

    /**
     * Helper Function: Tarik data bulanan
     */
    private function getGlobalStats($model, $tahun, $label)
    {
        $data = $model::selectRaw('EXTRACT(MONTH FROM created_at) as bulan, COUNT(*) as jumlah')
            ->whereYear('created_at', $tahun)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->pluck('jumlah', 'bulan')->toArray();

        $monthlyCounts = array_fill(0, 12, 0);
        foreach ($data as $bulan => $jumlah) {
            $monthlyCounts[(int)$bulan - 1] = $jumlah;
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogos', 'Sep', 'Okt', 'Nov', 'Dis'],
            'datasets' => [['label' => $label, 'data' => $monthlyCounts]]
        ];
    }
}