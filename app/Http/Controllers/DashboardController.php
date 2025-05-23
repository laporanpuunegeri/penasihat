<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LaporanPandanganUndang;
use App\Models\LaporanMesyuarat;
use App\Models\Kestatatertib;
use App\Models\LainLainTugasan;
use App\Models\LaporanKesMahkamah;
use App\Models\LaporanGubalanUndang;
use App\Models\LaporanPindaanUndang;
use App\Models\LaporanSemakanUndang;
use App\Models\Pergerakan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Akses tidak dibenarkan.');
        }

        $filter = $this->getFilterByRole($user);

        $undang      = $this->kiraSuku(LaporanPandanganUndang::class, $filter);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class, $filter);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class, $filter);
        $lain        = $this->kiraSuku(LainLainTugasan::class, $filter);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class, $filter);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class, $filter);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class, $filter);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class, $filter);

        $bulan = now()->month;
        $tahun = now()->year;

        $undangBulanIni = $this->kiraBulan(LaporanPandanganUndang::class, $filter, $bulan, $tahun);
        $tatatertibBulanIni = $this->kiraBulan(Kestatatertib::class, $filter, $bulan, $tahun);
        $mesyuaratBulanIni = $this->kiraBulan(LaporanMesyuarat::class, $filter, $bulan, $tahun);
        $lainBulanIni = $this->kiraBulan(LainLainTugasan::class, $filter, $bulan, $tahun);
        $kesMahkamahBulanIni = $this->kiraBulan(LaporanKesMahkamah::class, $filter, $bulan, $tahun);
        $gubalanBulanIni = $this->kiraBulan(LaporanGubalanUndang::class, $filter, $bulan, $tahun);
        $pindaanBulanIni = $this->kiraBulan(LaporanPindaanUndang::class, $filter, $bulan, $tahun);
        $semakanBulanIni = $this->kiraBulan(LaporanSemakanUndang::class, $filter, $bulan, $tahun);

        $bulanIni = $undangBulanIni + $tatatertibBulanIni + $mesyuaratBulanIni + $lainBulanIni +
                    $kesMahkamahBulanIni + $gubalanBulanIni + $pindaanBulanIni + $semakanBulanIni;

        $belumSelesai = LaporanPandanganUndang::where($filter)
            ->where('status', 'Dalam Proses')
            ->count();

    $melepasiTarikh = LaporanPandanganUndang::where(function ($q) {
        $q->whereNull('ringkasan_pandangan')
          ->orWhere('ringkasan_pandangan', '');
    })
    ->when($filter, fn($q) => $this->applyFilter($q, $filter))
    ->count();


        return view('dashboard', compact(
            'undang', 'tatatertib', 'mesyuarat', 'lain',
            'kesmahkamah', 'gubalan', 'pindaan', 'semakan',
            'undangBulanIni', 'tatatertibBulanIni', 'mesyuaratBulanIni', 'lainBulanIni',
            'kesMahkamahBulanIni', 'gubalanBulanIni', 'pindaanBulanIni', 'semakanBulanIni',
            'bulanIni', 'belumSelesai', 'melepasiTarikh'
        ));
    }

    private function kiraSuku($model, $filter)
    {
        $query = $model::query();
        if (isset($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        } elseif (isset($filter['negeri'])) {
            $query->where('negeri', $filter['negeri']);
        }

        $suku = [0, 0, 0, 0];
        foreach ($query->get() as $item) {
            if ($item->created_at) {
                $quarter = ceil(Carbon::parse($item->created_at)->month / 3);
                $suku[$quarter - 1]++;
            }
        }
        return $suku;
    }

    private function kiraBulan($model, $filter, $bulan, $tahun)
    {
        $query = $model::query();
        if (isset($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        } elseif (isset($filter['negeri'])) {
            $query->where('negeri', $filter['negeri']);
        }
        return $query->whereMonth('created_at', $bulan)
                     ->whereYear('created_at', $tahun)
                     ->count();
    }

    private function getFilterByRole($user)
    {
        if ($user->role === 'super_admin') {
            return [];
        } elseif (in_array($user->role, ['yb', 'pa'])) {
            return ['negeri' => $user->negeri];
        } else {
            return ['user_id' => $user->id];
        }
    }
}
