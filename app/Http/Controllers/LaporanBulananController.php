<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LaporanPandanganUndang;
use App\Models\LaporanKesMahkamah;
use App\Models\LaporanGubalanUndang;
use App\Models\LaporanPindaanUndang;
use App\Models\LaporanSemakanUndang;
use App\Models\LaporanMesyuarat;
use App\Models\Kestatatertib;
use App\Models\LainLainTugasan;

class LaporanBulananController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('m');
        $tahun = $request->tahun ?? now()->format('Y');

        $tarikh_awal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tarikh_akhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        return view('laporanbulanan.index', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'pandangan' => LaporanPandanganUndang::whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])->get(),
            'kesmahkamah' => LaporanKesMahkamah::whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])->get(),
            'gubalan' => LaporanGubalanUndang::whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])->get(),
            'pindaan' => LaporanPindaanUndang::whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])->get(),
            'semakan' => LaporanSemakanUndang::whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])->get(),
            'mesyuarat' => LaporanMesyuarat::whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])->get(),
            'tatatertib' => Kestatatertib::whereBetween('tarikh_terima', [$tarikh_awal, $tarikh_akhir])->get(),
            'lain' => LainLainTugasan::whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])->get(),
        ]);
    }
}
