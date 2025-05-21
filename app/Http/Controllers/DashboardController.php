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

        // Jika boss atau YB - ikut negeri
        if (in_array($user->role, ['boss', 'yb'])) {
            $filter = ['negeri' => $user->negeri];
        } else {
            // Jika user biasa - ikut user_id
            $filter = ['user_id' => $user->id];
        }

        $undang      = $this->kiraSuku(LaporanPandanganUndang::class, $filter);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class, $filter);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class, $filter);
        $lain        = $this->kiraSuku(LainLainTugasan::class, $filter);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class, $filter);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class, $filter);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class, $filter);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class, $filter);

        $pergerakan = Pergerakan::where('user_id', $user->id)->get()->map(function ($item) {
            return [
                'title'   => $item->jenis ?? 'Pergerakan',
                'start'   => $item->tarikh,
                'end'     => $item->tarikh,
                'catatan' => $item->catatan ?? '-',
            ];
        });

        return view('dashboard', compact(
            'undang', 'tatatertib', 'mesyuarat', 'lain',
            'kesmahkamah', 'gubalan', 'pindaan', 'semakan',
            'pergerakan'
        ));
    }

    private function kiraSuku($model)
{
    $user = auth()->user();

    // Jika role adalah yb, tapis ikut negeri
    if ($user->role === 'yb') {
        $data = $model::where('negeri', $user->negeri)->get();
    } else {
        $data = $model::where('user_id', $user->id)->get();
    }

    $suku = [0, 0, 0, 0];

    foreach ($data as $item) {
        $bulan = \Carbon\Carbon::parse($item->created_at)->month;
        $quarter = ceil($bulan / 3);
        $suku[$quarter - 1]++;
    }

    return $suku;
}
