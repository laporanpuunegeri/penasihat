<?php

namespace App\Http\Controllers\DashboardBahagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
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
        $user = Auth::user();
        if (!$user) abort(403, 'Sila log masuk.');

        // Normalise Role & Bahagian
        $userRole = strtolower($user->role);
        $userBahagian = strtoupper(trim($user->bahagian));
        $tahun = $request->tahun ?? now()->year;

        // --- 1. REDIRECT KHAS UNTUK YB / PA ---
        if ($userRole == 'yb' || $userRole == 'pa') {
            return redirect()->route('dashboard.yb');
        }

        // --- 2. REDIRECT BERDASARKAN BAHAGIAN ---
        if ($userBahagian == 'BAHAGIAN PENTADBIRAN') {
            return redirect()->route('dashboard.pentadbiran');
        }
        if ($userBahagian == 'BAHAGIAN KEWANGAN') {
            return redirect()->route('dashboard.kewangan');
        }

        // --- 3. LOGIK KUNCI NEGERI (UNTUK PENASIHAT) ---
        $userIdsInState = User::where('negeri', $user->negeri)->pluck('id')->toArray();

      
        $dataPandanganUndang = $this->getMonthlyStats(LaporanPandanganUndang::class, $userIdsInState, $tahun, 'Pandangan');
        $dataKesMahkamah     = $this->getMonthlyStats(LaporanKesMahkamah::class, $userIdsInState, $tahun, 'Kes Mahkamah');
        $dataGubalan         = $this->getMonthlyStats(LaporanGubalanUndang::class, $userIdsInState, $tahun, 'Gubalan');
        $dataPindaan         = $this->getMonthlyStats(LaporanPindaanUndang::class, $userIdsInState, $tahun, 'Pindaan');
        $dataSemakan         = $this->getMonthlyStats(LaporanSemakanUndang::class, $userIdsInState, $tahun, 'Semakan');
        $dataMesyuarat       = $this->getMonthlyStats(LaporanMesyuarat::class, $userIdsInState, $tahun, 'Mesyuarat');
        $dataTataterib       = $this->getMonthlyStats(Kestatatertib::class, $userIdsInState, $tahun, 'Tatatertib');
        $dataTugasan         = $this->getMonthlyStats(LainLainTugasan::class, $userIdsInState, $tahun, 'Lain-lain');

        return view('dashboard.penasihat', compact(
            'dataPandanganUndang', 'dataKesMahkamah', 'dataGubalan', 
            'dataPindaan', 'dataSemakan', 'dataMesyuarat', 
            'dataTataterib', 'dataTugasan', 'tahun'
        ));
    }

    private function getMonthlyStats($model, $userIds, $tahun, $label)
    {
      
        $data = $model::selectRaw('EXTRACT(MONTH FROM created_at) as bulan, COUNT(*) as jumlah')
            ->whereIn('user_id', $userIds)
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