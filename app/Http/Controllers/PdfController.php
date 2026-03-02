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
use App\Models\LampiranKesMahkamah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    public function laporan(Request $request)
    {

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $user = Auth::user();
        
    
        $filter = in_array(strtolower($user->role), ['pa', 'yb']) 
            ? ['negeri' => $user->negeri] 
            : ['user_id' => $user->id];

        $kategori_list = ['Perlembagaan', 'Tanah / PBT', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Perjanjian / MOU', 'Penswastaan', 'Lain-lain'];

        $kategori_kes = collect(['Perlembagaan', 'Tanah / PBT', 'Rujukan tanah', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan', 'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain'])
            ->map(fn($label) => ['label' => $label, 'key' => Str::lower(trim($label))]);

        $lampiran = LampiranKesMahkamah::query()
            ->when(in_array(strtolower($user->role), ['pa', 'yb']), fn($q) => $q->where('negeri', $user->negeri))
            ->when(!in_array(strtolower($user->role), ['pa', 'yb']), fn($q) => $q->where('user_id', $user->id))
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->get()->keyBy(fn($item) => Str::lower(trim($item->kategori)));

        $lampiranKes = $lampiran->map(fn($item) => [
            'bil_aktif' => $item->bil_aktif ?? 0, 'majistret' => $item->majistret ?? 0, 'sesi' => $item->sesi ?? 0,
            'tinggi' => $item->tinggi ?? 0, 'rayuan' => $item->rayuan ?? 0, 'persk' => $item->persk ?? 0,
            'status' => $item->status ?? '-',
        ])->toArray();

        $jumlahKeseluruhan = [
            'bil_aktif' => $lampiran->sum('bil_aktif'), 'majistret' => $lampiran->sum('majistret'),
            'sesi' => $lampiran->sum('sesi'), 'tinggi' => $lampiran->sum('tinggi'),
            'rayuan' => $lampiran->sum('rayuan'), 'persk' => $lampiran->sum('persk'),
        ];

     
        $laporan_pandangan = LaporanPandanganUndang::where($filter)
            ->where('is_current', true)
            ->whereYear('tarikh_terima', $tahun)
            ->whereMonth('tarikh_terima', $bulan)
            ->orderBy('tarikh_terima', 'asc')
            ->get();

        return Pdf::loadView('laporan.pdf', [
            'bulan' => $bulan, 'tahun' => $tahun, 'user' => $user,
            'kategori_list' => $kategori_list, 'kategori_kes' => $kategori_kes,
            'laporan' => $laporan_pandangan, 

            'laporan_kesmahkamah' => LaporanKesMahkamah::where($filter)
                ->whereMonth('tarikh_sebutan', $bulan)->whereYear('tarikh_sebutan', $tahun)
                ->orderBy('tarikh_sebutan', 'asc')->get(),

            'laporan_gubalan' => LaporanGubalanUndang::where($filter)
                ->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)
                ->orderBy('created_at', 'asc')->get(),

            'laporan_pindaan' => LaporanPindaanUndang::where($filter)
                ->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)
                ->orderBy('created_at', 'asc')->get(),

            'laporan_semakan' => LaporanSemakanUndang::where($filter)
                ->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)
                ->orderBy('created_at', 'asc')->get(),

            'laporan_mesyuarat' => LaporanMesyuarat::where($filter)
                ->whereMonth('tarikh_mesyuarat', $bulan)
                ->whereYear('tarikh_mesyuarat', $tahun)
                ->orderBy('tarikh_mesyuarat', 'asc')->get(),

            'laporan_tatatertib' => Kestatatertib::where($filter)
                ->whereMonth('tarikh_terima', $bulan)->whereYear('tarikh_terima', $tahun)
                ->orderBy('tarikh_terima', 'asc')->get(),

            'laporan_lainlain' => LainLainTugasan::where($filter)
                ->whereMonth('tarikh', $bulan)
                ->whereYear('tarikh', $tahun)
                ->orderBy('tarikh', 'asc')->get(),

            'lampiran_kesmahkamah' => $lampiranKes,
            'jumlah_keseluruhan' => $jumlahKeseluruhan,
        ])
      
        ->setPaper('a4', 'portrait') 
        ->stream('Laporan_Aktiviti_Bulanan.pdf');
    }
}