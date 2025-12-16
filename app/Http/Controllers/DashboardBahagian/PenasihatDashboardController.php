<?php

namespace App\Http\Controllers\DashboardBahagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LaporanPandanganUndang;
use App\Models\LaporanKesMahkamah;
use App\Models\LaporanGubalanUndang;
use App\Models\LaporanPindaanUndang;
use App\Models\LaporanSemakanUndang;
use App\Models\LaporanMesyuarat;
use App\Models\Kestatatertib;
use App\Models\LainLainTugasan;

class PenasihatDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) abort(403, 'Sila log masuk.');

        // --- 1. TENDANG KELUAR (Pentadbiran & Kewangan) ---
        if ($user->bahagian == 'Bahagian Pentadbiran') {
            return redirect()->route('dashboard.pentadbiran');
        }
        if ($user->bahagian == 'Bahagian Kewangan') {
            return redirect()->route('dashboard.kewangan');
        }

        // --- 2. BENARKAN MASUK (Penasihat, Semakan, Syariah) ---
        $allowedBahagian = [
            'Bahagian Penasihat', 
            'Bahagian Semakan', 
            'Bahagian Syariah'
        ];

        if (!in_array($user->bahagian, $allowedBahagian) && $user->role !== 'super_admin') {
             abort(403, 'Anda tiada akses ke Dashboard ini.');
        }

        // --- 3. PROSES DATA ---
        
        // Filter Data (Ikut Role/Negeri)
        $filter = $this->getFilterByRole($user);
        $tahun = $request->tahun ?? now()->year;

        // Prepare Data untuk Setiap Graf
        $dataPandanganUndang = $this->getMonthlyData(LaporanPandanganUndang::class, $filter, $tahun, 'Pandangan Undang-Undang');
        $dataKesMahkamah     = $this->getMonthlyData(LaporanKesMahkamah::class, $filter, $tahun, 'Kes Mahkamah');
        $dataGubalan         = $this->getMonthlyData(LaporanGubalanUndang::class, $filter, $tahun, 'Gubalan');
        $dataPindaan         = $this->getMonthlyData(LaporanPindaanUndang::class, $filter, $tahun, 'Pindaan');
        $dataSemakan         = $this->getMonthlyData(LaporanSemakanUndang::class, $filter, $tahun, 'Semakan');
        $dataMesyuarat       = $this->getMonthlyData(LaporanMesyuarat::class, $filter, $tahun, 'Mesyuarat');
        $dataTataterib       = $this->getMonthlyData(Kestatatertib::class, $filter, $tahun, 'Tatatertib');
        $dataTugasan         = $this->getMonthlyData(LainLainTugasan::class, $filter, $tahun, 'Lain-lain Tugasan');

        // Return ke View
        return view('dashboard.penasihat', compact(
            'dataPandanganUndang',
            'dataKesMahkamah',
            'dataGubalan',
            'dataPindaan',
            'dataSemakan',
            'dataMesyuarat',
            'dataTataterib',
            'dataTugasan'
        ));
    }

    // --- HELPER FUNCTIONS ---

    private function getFilterByRole($user)
    {
        // Admin nampak semua
        if (in_array($user->role, ['super_admin'])) return [];
        
        // YB/PA/EO nampak ikut Negeri
        if (in_array($user->role, ['yb', 'pa', 'eo'])) return ['negeri' => $user->negeri];
        
        // User biasa nampak rekod sendiri sahaja
        return ['user_id' => $user->id];
    }

    private function applyFilter($query, $filter)
    {
        foreach ($filter as $key => $value) {
            $query->where($key, $value);
        }
        return $query;
    }

    private function getMonthlyData($model, $filter, $tahun, $label)
    {
        $monthlyCounts = array_fill(0, 12, 0); 

        $data = $model::selectRaw('EXTRACT(MONTH FROM created_at) as bulan, COUNT(*) as jumlah')
            ->when($filter, fn($q) => $this->applyFilter($q, $filter))
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan')
            ->pluck('jumlah', 'bulan')
            ->toArray();

        foreach ($data as $bulan => $jumlah) {
            $index = (int)$bulan - 1; 
            if (isset($monthlyCounts[$index])) {
                $monthlyCounts[$index] = $jumlah;
            }
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogos', 'Sep', 'Okt', 'Nov', 'Dis'],
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $monthlyCounts,
                    'backgroundColor' => '#1565c0', 
                    'borderColor' => '#1565c0',
                    'borderWidth' => 1
                ]
            ]
        ];
    }
}