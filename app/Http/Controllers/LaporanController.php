<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPandanganUndang;
use App\Models\LaporanKesMahkamah;
use App\Models\LaporanGubalanUndang;
use App\Models\LaporanPindaanUndang;
use App\Models\LaporanSemakanUndang;
use App\Models\Kestatatertib;
use App\Models\LainLainTugasan;
use App\Models\LaporanMesyuarat;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $kategori_list = [
            'Perlembagaan',
            'Tanah / PBT',
            'Undang-Undang Pentadbiran / Perkhidmatan',
            'Perjanjian / MOU',
            'Penswastaan',
            'Lain-lain',
        ];

        $laporan = LaporanPandanganUndang::whereMonth('tarikh_terima', $bulan)
            ->whereYear('tarikh_terima', $tahun)
            ->get();

        $laporan_kesmahkamah = LaporanKesMahkamah::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_gubalan = LaporanGubalanUndang::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_pindaan = LaporanPindaanUndang::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_semakan = LaporanSemakanUndang::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_mesyuarat = LaporanMesyuarat::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_tatatertib = Kestatatertib::whereMonth('tarikh_terima', $bulan)
            ->whereYear('tarikh_terima', $tahun)
            ->get();

        $laporan_lainlain = LainLainTugasan::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        return view('laporan.index', compact(
            'kategori_list',
            'laporan',
            'laporan_kesmahkamah',
            'laporan_gubalan',
            'laporan_pindaan',
            'laporan_semakan',
            'laporan_mesyuarat',
            'laporan_tatatertib',
            'laporan_lainlain',
            'bulan',
            'tahun'
        ));
    }

    public function laporanKesMahkamah(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $laporan_kesmahkamah = LaporanKesMahkamah::whereMonth('tarikh_daftar', $bulan)
            ->whereYear('tarikh_daftar', $tahun)
            ->get();

        return view('laporan.kesmahkamah', compact('laporan_kesmahkamah', 'bulan', 'tahun'));
    }

    public function laporanMesyuarat(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $laporan_mesyuarat = LaporanMesyuarat::whereMonth('tarikh_mesyuarat', $bulan)
            ->whereYear('tarikh_mesyuarat', $tahun)
            ->get();

        return view('laporan.mesyuarat', compact('laporan_mesyuarat', 'bulan', 'tahun'));
    }

    public function pandanganUndang()
    {
        return view('laporan.pandanganundang');
    }

    public function gubalanUndang()
    {
        return view('laporan.gubalanundang');
    }

    public function pindaanUndang()
    {
        return view('laporan.pindaanundang');
    }

    public function semakanUndang()
    {
        return view('laporan.semakanundang');
    }

    public function tatatertib()
    {
        return view('laporan.tatatertib');
    }

    public function lainLain()
    {
        return view('laporan.lainlain');
    }
}
