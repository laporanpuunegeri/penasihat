<?php

namespace App\Http\Controllers\DashboardBahagian;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Import Model Laporan (Untuk 8 Graf Utama)
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
    /**
     * DASHBOARD PENASIHAT (8 Graf Utama)
     * Route: /dashboard/penasihat (atau route utama dashboard)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) abort(403, 'Sila log masuk.');

        // --- SAFETY REDIRECT ---
        // Jika staff Pentadbiran/Kewangan tersesat masuk sini, tendang ke dashboard mereka
        if ($user->bahagian == 'Bahagian Pentadbiran') {
            return redirect()->route('dashboard.pentadbiran');
        }
        if ($user->bahagian == 'Bahagian Kewangan') {
            return redirect()->route('dashboard.kewangan');
        }

        // 1. Filter Data (Ikut Role/Negeri)
        $filter = $this->getFilterByRole($user);
        $tahun = $request->tahun ?? now()->year;

        // 2. Prepare Data untuk Setiap Graf (Format Chart.js)
        $dataPandanganUndang = $this->getMonthlyData(LaporanPandanganUndang::class, $filter, $tahun, 'Pandangan Undang-Undang');
        $dataKesMahkamah     = $this->getMonthlyData(LaporanKesMahkamah::class, $filter, $tahun, 'Kes Mahkamah');
        $dataGubalan         = $this->getMonthlyData(LaporanGubalanUndang::class, $filter, $tahun, 'Gubalan');
        $dataPindaan         = $this->getMonthlyData(LaporanPindaanUndang::class, $filter, $tahun, 'Pindaan');
        $dataSemakan         = $this->getMonthlyData(LaporanSemakanUndang::class, $filter, $tahun, 'Semakan');
        $dataMesyuarat       = $this->getMonthlyData(LaporanMesyuarat::class, $filter, $tahun, 'Mesyuarat');
        $dataTataterib       = $this->getMonthlyData(Kestatatertib::class, $filter, $tahun, 'Tatatertib');
        $dataTugasan         = $this->getMonthlyData(LainLainTugasan::class, $filter, $tahun, 'Lain-lain Tugasan');

        // Return ke view khusus 'dashboard.penasihat'
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

    // --- Helper Functions ---

    private function getFilterByRole($user)
    {
        // Admin/Super Admin nampak semua
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

    // Fungsi Statistik Bulanan (Jan-Dis)
    private function getMonthlyData($model, $filter, $tahun, $label)
    {
        // Array kosong untuk 12 bulan
        $monthlyCounts = array_fill(0, 12, 0); 

        // Query DB grouping by Month
        // Nota: Syntax PostgreSQL guna EXTRACT, MySQL guna MONTH()
        // Saya kekalkan EXTRACT sebab error log abang sebelum ni tunjuk PostgreSQL environment
        $data = $model::selectRaw('EXTRACT(MONTH FROM created_at) as bulan, COUNT(*) as jumlah')
            ->when($filter, fn($q) => $this->applyFilter($q, $filter))
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan')
            ->pluck('jumlah', 'bulan')
            ->toArray();

        // Masukkan data DB ke dalam array 12 bulan
        foreach ($data as $bulan => $jumlah) {
            $index = (int)$bulan - 1; 
            if (isset($monthlyCounts[$index])) {
                $monthlyCounts[$index] = $jumlah;
            }
        }

        // Return format JSON structure untuk Chart.js
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