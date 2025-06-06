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
        $undang      = $this->kiraSuku(LaporanPandanganUndang::class);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class);
        $lain        = $this->kiraSuku(LainLainTugasan::class);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class);

        return view('dashboard', compact(
            'undang', 'tatatertib', 'mesyuarat', 'lain',
            'kesmahkamah', 'gubalan', 'pindaan', 'semakan'
        ));
    }

    private function kiraSuku($model)
    {
        $suku = [0, 0, 0, 0];

        foreach ($model::select('created_at')->get() as $item) {
            $bulan = Carbon::parse($item->created_at)->month;
            $quarter = ceil($bulan / 3);
            $suku[$quarter - 1]++;
        }

        return $suku;
    }

    public function boss()
    {
        return view('dashboard.boss');
    }

    public function pa()
    {
        $undang      = $this->kiraSuku(LaporanPandanganUndang::class);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class);
        $lain        = $this->kiraSuku(LainLainTugasan::class);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class);

        return view('dashboard.pa', compact(
            'undang', 'tatatertib', 'mesyuarat', 'lain',
            'kesmahkamah', 'gubalan', 'pindaan', 'semakan'
        ));
    }

    public function yb()
    {
        $undang      = $this->kiraSuku(LaporanPandanganUndang::class);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class);
        $lain        = $this->kiraSuku(LainLainTugasan::class);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class);

        return view('dashboard.yb', compact(
            'undang', 'tatatertib', 'mesyuarat', 'lain',
            'kesmahkamah', 'gubalan', 'pindaan', 'semakan'
        ));
    }

    public function user()
    {
        $undang      = $this->kiraSuku(LaporanPandanganUndang::class);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class);
        $lain        = $this->kiraSuku(LainLainTugasan::class);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class);

        $user = auth()->user();

        $pergerakan = Pergerakan::where('user_id', $user->id)->get()->map(function ($item) {
            return [
                'title'   => $item->jenis ?? 'Pergerakan',
                'start'   => $item->tarikh,
                'end'     => $item->tarikh,
                'catatan' => $item->catatan ?? '-',
            ];
        });

        return view('dashboard.user', compact(
            'undang', 'tatatertib', 'mesyuarat', 'lain',
            'kesmahkamah', 'gubalan', 'pindaan', 'semakan',
            'pergerakan'
        ));
    }
}
