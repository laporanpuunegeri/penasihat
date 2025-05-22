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

        // Logik penapisan data ikut peranan
        $filter = $this->getFilterByRole($user);

        // Kiraan sukuan bagi setiap modul
        $undang      = $this->kiraSuku(LaporanPandanganUndang::class, $filter);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class, $filter);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class, $filter);
        $lain        = $this->kiraSuku(LainLainTugasan::class, $filter);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class, $filter);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class, $filter);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class, $filter);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class, $filter);

        // Pergerakan hanya ikut user_id
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

    /**
     * Kira bilangan data mengikut suku tahun
     */
   private function kiraSuku($model, $filter)
{
    $query = $model::query();

    if (isset($filter['user_id'])) {
        $query->where('user_id', $filter['user_id']);
    } elseif (isset($filter['negeri'])) {
        $query->where('negeri', $filter['negeri']);
    }

    $data = $query->get();
    $suku = [0, 0, 0, 0];

    foreach ($data as $item) {
        if ($item->created_at) {
            $bulan = Carbon::parse($item->created_at)->month;
            $quarter = ceil($bulan / 3);
            $suku[$quarter - 1]++;
        }
    }
    
dd($undang, $tatatertib, $mesyuarat); // atau mana-mana
    return $suku;
}

    /**
     * Tetapkan filter berdasarkan peranan pengguna
     */
    private function getFilterByRole($user)
    {
        if (in_array($user->role, ['yb', 'pa'])) {
            return ['negeri' => $user->negeri];
        }

        return ['user_id' => $user->id];
    }
}
