<?php

namespace App\Http\Controllers\DashboardBahagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Import Model
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

        // Normalise Bahagian User (Tukar jadi Huruf Besar & Buang Space tepi)
        // Ini penting supaya "Bahagian Penasihat" sama dengan "BAHAGIAN PENASIHAT"
        $userBahagian = strtoupper(trim($user->bahagian));

        // --- 1. TENDANG KELUAR (Pentadbiran & Kewangan) ---
        if ($userBahagian == 'BAHAGIAN PENTADBIRAN') {
            return redirect()->route('dashboard.pentadbiran');
        }
        if ($userBahagian == 'BAHAGIAN KEWANGAN') {
            return redirect()->route('dashboard.kewangan');
        }

        // --- 2. BENARKAN MASUK (WHITELIST - HURUF BESAR) ---
        $allowedBahagian = [
            'BAHAGIAN PENASIHAT', 
            'BAHAGIAN SEMAKAN', 
            'BAHAGIAN SYARIAH'
        ];

        // Kalau user BUKAN dari 3 bahagian ni DAN BUKAN Super Admin, kita block
        if (!in_array($userBahagian, $allowedBahagian) && $user->role !== 'super_admin') {
             abort(403, 'ANDA TIADA AKSES KE DASHBOARD INI.');
        }

        // --- 3. PROSES DATA ---
        
        // Filter Data (Ikut Role)
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

        // --- 4. TENTUKAN VIEW BERDASARKAN BAHAGIAN ---
        $viewName = 'dashboard.penasihat'; // Default view

        if ($userBahagian == 'BAHAGIAN SEMAKAN') {
            $viewName = 'dashboard.semakan';
        }
        if ($userBahagian == 'BAHAGIAN SYARIAH') {
            $viewName = 'dashboard.syariah'; // Pastikan file syariah.blade.php wujud jika nak guna
        }

        // Return ke View yang betul dengan Data
        return view($viewName, compact(
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
        // 🔥 1. GROUP VIP: NAMPAK SEMUA DATA (FULL) 🔥
        // YB, PA, dan Super Admin nampak semua tanpa filter negeri/user
        // Saya tambah strtolower supaya tak kisah 'yb' atau 'YB'
        if (in_array(strtolower($user->role), ['super_admin', 'yb', 'pa'])) {
            return []; // Array kosong = SELECT * FROM table (Tanpa WHERE)
        }
        
        // 2. GROUP NEGERI: Nampak ikut negeri sahaja (Contoh: EO)
        if (in_array(strtolower($user->role), ['eo'])) {
             return ['negeri' => $user->negeri];
        }
        
        // 3. USER BIASA: Nampak kerja sendiri sahaja
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

        // Query DB grouping by Month
        // NOTA: Guna EXTRACT(MONTH) untuk PostgreSQL (Render)
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