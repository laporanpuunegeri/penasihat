<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPandanganUndang;
use App\Models\LaporanKesMahkamah;
use App\Models\LaporanGubalanUndang;
use App\Models\LaporanPindaanUndang;
use App\Models\LaporanSemakanUndang;
use App\Models\LaporanMesyuarat;
use App\Models\Kestatatertib;
use App\Models\LainLainTugasan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PdfController extends Controller
{
    public function laporan(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->bulan ?? now()->month;
        $tahun = now()->year;

        $tarikh_awal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tarikh_akhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Penapis peranan
        if (in_array($user->role, ['pa', 'yb'])) {
            $filter = ['negeri' => $user->negeri];
        } else {
            $filter = ['user_id' => $user->id];
        }

        $kategori_list = [
            'Perlembagaan',
            'Tanah / PBT',
            'Undang-Undang Pentadbiran / Perkhidmatan',
            'Perjanjian / MOU',
            'Penswastaan',
            'Lain-lain',
        ];

        return Pdf::loadView('laporan.pdf', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'kategori_list' => $kategori_list,
            'user' => $user,

            'laporan' => LaporanPandanganUndang::where($filter)
                ->whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])
                ->get(),

            'laporan_kesmahkamah' => LaporanKesMahkamah::where($filter)
                ->whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])
                ->get(),

            'laporan_gubalan' => LaporanGubalanUndang::where($filter)
                ->whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])
                ->get(),

            'laporan_pindaan' => LaporanPindaanUndang::where($filter)
                ->whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])
                ->get(),

            'laporan_semakan' => LaporanSemakanUndang::where($filter)
                ->whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])
                ->get(),

            'laporan_mesyuarat' => LaporanMesyuarat::where($filter)
                ->whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])
                ->get(),

            'laporan_tatatertib' => Kestatatertib::where($filter)
                ->whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])
                ->get(),

            'laporan_lainlain' => LainLainTugasan::where($filter)
                ->whereBetween('created_at', [$tarikh_awal, $tarikh_akhir])
                ->get(),
        ])
        ->setPaper('a4', 'portrait')
        ->stream('Laporan_Aktiviti_Bulanan.pdf');
    }
}
