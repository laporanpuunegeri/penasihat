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

        // Paparan suku
        $undang      = $this->kiraSuku(LaporanPandanganUndang::class, $filter);
        $tatatertib  = $this->kiraSuku(Kestatatertib::class, $filter);
        $mesyuarat   = $this->kiraSuku(LaporanMesyuarat::class, $filter);
        $lain        = $this->kiraSuku(LainLainTugasan::class, $filter);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class, $filter);
        $gubalan     = $this->kiraSuku(LaporanGubalanUndang::class, $filter);
        $pindaan     = $this->kiraSuku(LaporanPindaanUndang::class, $filter);
        $semakan     = $this->kiraSuku(LaporanSemakanUndang::class, $filter);

        // Jumlah bulan ini
        $undangBulanIni      = $this->kiraBulanIni(LaporanPandanganUndang::class, $filter);
        $tatatertibBulanIni  = $this->kiraBulanIni(Kestatatertib::class, $filter);
        $mesyuaratBulanIni   = $this->kiraBulanIni(LaporanMesyuarat::class, $filter);
        $lainBulanIni        = $this->kiraBulanIni(LainLainTugasan::class, $filter);
        $kesmahkamahBulanIni = $this->kiraBulanIni(LaporanKesMahkamah::class, $filter);
        $gubalanBulanIni     = $this->kiraBulanIni(LaporanGubalanUndang::class, $filter);
        $pindaanBulanIni     = $this->kiraBulanIni(LaporanPindaanUndang::class, $filter);
        $semakanBulanIni     = $this->kiraBulanIni(LaporanSemakanUndang::class, $filter);

        // Ringkasan tambahan
        $bulanIni = $undangBulanIni + $tatatertibBulanIni + $mesyuaratBulanIni + $lainBulanIni +
                    $kesmahkamahBulanIni + $gubalanBulanIni + $pindaanBulanIni + $semakanBulanIni;

        $belumSelesai = LaporanPandanganUndang::where('status', 'Dalam Proses')
            ->when($filter, fn($q) => $this->applyFilter($q, $filter))
            ->count();

        $melepasiTarikh = LaporanPandanganUndang::where('status', 'Dalam Proses')
            ->whereDate('tarikh_selesai', '<', now())
            ->when($filter, fn($q) => $this->applyFilter($q, $filter))
            ->count();

        return view('dashboard', compact(
            'undang', 'tatatertib', 'mesyuarat', 'lain', 'kesmahkamah', 'gubalan', 'pindaan', 'semakan',
            'bulanIni', 'belumSelesai', 'melepasiTarikh',
            'undangBulanIni', 'tatatertibBulanIni', 'mesyuaratBulanIni', 'lainBulanIni',
            'kesmahkamahBulanIni', 'gubalanBulanIni', 'pindaanBulanIni', 'semakanBulanIni'
        ));
    }

    private function kiraSuku($model, $filter)
    {
        $query = $model::query();
        $this->applyFilter($query, $filter);
        $data = $query->get();

        $suku = [0, 0, 0, 0];
        foreach ($data as $item) {
            if ($item->created_at) {
                $quarter = ceil(Carbon::parse($item->created_at)->month / 3);
                $suku[$quarter - 1]++;
            }
        }
        return $suku;
    }

    private function kiraBulanIni($model, $filter)
    {
        $query = $model::query();
        $this->applyFilter($query, $filter);
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year)
                     ->count();
    }

    private function getFilterByRole($user)
    {
        return match ($user->role) {
            'super_admin' => [],
            'yb', 'pa' => ['negeri' => $user->negeri],
            default => ['user_id' => $user->id],
        };
    }

    private function applyFilter($query, $filter)
    {
        if (isset($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        } elseif (isset($filter['negeri'])) {
            $query->where('negeri', $filter['negeri']);
        }
        return $query;
    }
}
