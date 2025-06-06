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
        $bulan = $request->bulan ?? now()->month;
        $tahun = now()->year;

        $tarikh_awal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tarikh_akhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

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
            'user' => Auth::user(),

            // Guna tarikh_daftar sahaja tanpa created_by
            'laporan' => LaporanPandanganUndang::whereBetween('tarikh_daftar', [$tarikh_awal, $tarikh_akhir])->get(),
            'laporan_kesmahkamah' => LaporanKesMahkamah::whereBetween('tarikh_daftar', [$tarikh_awal, $tarikh_akhir])->get(),
            'laporan_gubalan' => LaporanGubalanUndang::whereBetween('tarikh_daftar', [$tarikh_awal, $tarikh_akhir])->get(),
            'laporan_pindaan' => LaporanPindaanUndang::whereBetween('tarikh_daftar', [$tarikh_awal, $tarikh_akhir])->get(),
            'laporan_semakan' => LaporanSemakanUndang::whereBetween('tarikh_daftar', [$tarikh_awal, $tarikh_akhir])->get(),
            'laporan_mesyuarat' => LaporanMesyuarat::whereBetween('tarikh_daftar', [$tarikh_awal, $tarikh_akhir])->get(),
            'laporan_tatatertib' => Kestatatertib::whereBetween('tarikh_daftar', [$tarikh_awal, $tarikh_akhir])->get(),
            'laporan_lainlain' => LainLainTugasan::whereBetween('tarikh_daftar', [$tarikh_awal, $tarikh_akhir])->get(),
        ])
        ->setPaper('a4', 'portrait')
        ->stream('Laporan_Aktiviti_Bulanan.pdf');
    }
}
