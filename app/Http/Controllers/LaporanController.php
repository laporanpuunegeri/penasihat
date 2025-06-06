<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPandanganUndang;
use App\Models\LaporanKesMahkamah;
use App\Models\Laporangubalanundang;
use App\Models\Laporanpindaanundang;
use App\Models\Laporansemakanundang;
use App\Models\Kestatatertib;
use App\Models\Lainlaintugasan;
use App\Models\Laporanmesyuarat;

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

        $laporan_kesmahkamah = LaporanKesMahkamah::whereMonth('tarikh_daftar', $bulan)
            ->whereYear('tarikh_daftar', $tahun)
            ->get();

        $laporan_gubalan = Laporangubalanundang::whereMonth('tarikh_daftar', $bulan)
            ->whereYear('tarikh_daftar', $tahun)
            ->get();

        $laporan_pindaan = Laporanpindaanundang::whereMonth('tarikh_daftar', $bulan)
            ->whereYear('tarikh_daftar', $tahun)
            ->get();

        $laporan_semakan = Laporansemakanundang::whereMonth('tarikh_daftar', $bulan)
            ->whereYear('tarikh_daftar', $tahun)
            ->get();

        $laporan_mesyuarat = Laporanmesyuarat::whereMonth('tarikh_mesyuarat', $bulan)
            ->whereYear('tarikh_mesyuarat', $tahun)
            ->get();

        $laporan_tatatertib = Kestatatertib::whereMonth('tarikh_terima', $bulan)
            ->whereYear('tarikh_terima', $tahun)
            ->get();

        $laporan_lainlain = Lainlaintugasan::whereMonth('tarikh_daftar', $bulan)
            ->whereYear('tarikh_daftar', $tahun)
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

    // ✅ Paparan penuh laporan kes mahkamah
    public function laporanKesMahkamah(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $laporan_kesmahkamah = LaporanKesMahkamah::whereMonth('tarikh_daftar', $bulan)
            ->whereYear('tarikh_daftar', $tahun)
            ->get();

        return view('laporan.kesmahkamah', compact('laporan_kesmahkamah', 'bulan', 'tahun'));
    }

    // ✅ Paparan penuh laporan mesyuarat
    public function laporanMesyuarat(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $laporan_mesyuarat = Laporanmesyuarat::whereMonth('tarikh_mesyuarat', $bulan)
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
