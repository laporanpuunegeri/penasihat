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

        // Paparan statistik utama
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;

        $queryPandangan = LaporanPandanganUndang::query();

        // Tapis ikut role
        if (isset($filter['user_id'])) {
            $queryPandangan->where('user_id', $filter['user_id']);
        } elseif (isset($filter['negeri'])) {
            $queryPandangan->where('negeri', $filter['negeri']);
        }

        // Data untuk statistik atas
        $bulanIni = (clone $queryPandangan)->whereMonth('created_at', $bulanSekarang)
                                           ->whereYear('created_at', $tahunSekarang)
                                           ->count();

        $belumSelesai = (clone $queryPandangan)->where('status', 'Dalam Proses')->count();

        $melepasiTarikh = (clone $queryPandangan)->where('status', 'Dalam Proses')
                                                 ->whereDate('tarikh_selesai', '<', Carbon::today())
                                                 ->count();

        // Paparan carta laporan
        $undang      = $this->kiraSuku(LaporanPandanganUndang::class, $filter);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class, $filter);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class, $filter);
        $lain        = $this->kiraSuku(LainLainTugasan::class, $filter);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class, $filter);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class, $filter);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class, $filter);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class, $filter);

        // Pergerakan hanya pengguna semasa
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
            'bulanIni', 'belumSelesai', 'melepasiTarikh', 'pergerakan'
        ));
    }

    /**
     * Kira data laporan mengikut suku tahun
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

        return $suku;
    }

    /**
     * Tentukan penapisan berdasarkan peranan pengguna
     */
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
