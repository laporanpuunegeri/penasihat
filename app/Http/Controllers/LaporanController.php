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
use App\Models\LampiranKesMahkamah;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bulan = $request->get('bulan', now()->month);
        $tahun = now()->year;

        $kategori_list = [
            'Perlembagaan',
            'Tanah / PBT',
            'Rujukan tanah',
            'Undang-Undang Pentadbiran / Perkhidmatan',
            'Kemalangan',
            'Perjanjian / Penswastaan',
            'Pendakwaan',
            'Lain-lain',
        ];

        $filter = in_array(strtolower($user->role), ['pa', 'yb']) 
            ? ['negeri' => $user->negeri] 
            : ['user_id' => $user->id];

        $laporan = LaporanPandanganUndang::where($filter)
            ->whereMonth('tarikh_terima', $bulan)
            ->whereYear('tarikh_terima', $tahun)
            ->get();

        $laporan_kesmahkamah = LaporanKesMahkamah::where($filter)
            ->whereMonth('tarikh_sebutan', $bulan)
            ->whereYear('tarikh_sebutan', $tahun)
            ->get();

        $laporan_gubalan = LaporanGubalanUndang::where($filter)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_pindaan = LaporanPindaanUndang::where($filter)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_semakan = LaporanSemakanUndang::where($filter)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_mesyuarat = LaporanMesyuarat::where($filter)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $laporan_tatatertib = Kestatatertib::where($filter)
            ->whereMonth('tarikh_terima', $bulan)
            ->whereYear('tarikh_terima', $tahun)
            ->get();

        $laporan_lainlain = LainLainTugasan::where($filter)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // ✅ Papar data Lampiran II (betul ikut role)
        $lampiran_kesmahkamah = LampiranKesMahkamah::query()
            ->when(in_array(strtolower($user->role), ['pa', 'yb']), fn($q) => $q->where('negeri', $user->negeri))
            ->when(!in_array(strtolower($user->role), ['pa', 'yb']), fn($q) => $q->where('user_id', $user->id))
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
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
            'lampiran_kesmahkamah',
            'bulan',
            'tahun'
        ));
    }

    public function lampiranForm()
    {
        $this->authorizePA();

        $kategori_list = [
            'Perlembagaan',
            'Tanah / PBT',
            'Rujukan tanah',
            'Undang-Undang Pentadbiran / Perkhidmatan',
            'Kemalangan',
            'Perjanjian / Penswastaan',
            'Pendakwaan',
            'Lain-lain',
        ];

        $bulan = now()->month;
        $tahun = now()->year;

        $rekod = LampiranKesMahkamah::where('user_id', auth()->id())
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('kategori');

        return view('lampiran.form', compact('kategori_list', 'rekod', 'bulan', 'tahun'));
    }

    public function simpanLampiran(Request $request)
    {
        $this->authorizePA();

        $kategori_list = [
            'Perlembagaan',
            'Tanah / PBT',
            'Rujukan tanah',
            'Undang-Undang Pentadbiran / Perkhidmatan',
            'Kemalangan',
            'Perjanjian / Penswastaan',
            'Pendakwaan',
            'Lain-lain',
        ];

        $data = $request->input('data');
        $bulan = now()->month;
        $tahun = now()->year;
        $user = auth()->user();

        LampiranKesMahkamah::where('user_id', $user->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->delete();

        foreach ($kategori_list as $i => $kategori) {
            LampiranKesMahkamah::create([
                'user_id'   => $user->id,
                'negeri'    => $user->negeri,
                'kategori'  => $kategori,
                'bil_aktif' => $data[$i][0] ?? 0,
                'majistret' => $data[$i][1] ?? 0,
                'sesi'      => $data[$i][2] ?? 0,
                'tinggi'    => $data[$i][3] ?? 0,
                'rayuan'    => $data[$i][4] ?? 0,
                'persk'     => $data[$i][5] ?? 0,
                'status'    => $data[$i][6] ?? '-',
                'bulan'     => $bulan,
                'tahun'     => $tahun,
            ]);
        }

        return redirect()->route('laporan.index')->with('success', 'Data Lampiran II berjaya disimpan.');
    }

    protected function authorizePA()
    {
        if (auth()->user()->role !== 'pa') {
            abort(403, 'Akses hanya dibenarkan kepada PA.');
        }
    }
}
