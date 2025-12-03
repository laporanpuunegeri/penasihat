<?php

namespace App\Http\Controllers\DashboardBahagian; // FIX 1: Namespace yang betul

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\LaporanPandanganUndang;
use App\Models\LaporanMesyuarat;
use App\Models\Kestatatertib;
use App\Models\LainLainTugasan;
use App\Models\LaporanKesMahkamah;
use App\Models\LaporanGubalanUndang;
use App\Models\LaporanPindaanUndang;
use App\Models\LaporanSemakanUndang;

class PenasihatDashboardController extends Controller // FIX 1: Nama Class yang betul
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) abort(403, 'Akses tidak dibenarkan.');

        $filter = $this->getFilterByRole($user);

        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;
        $pegawaiId = $request->pegawai;
        $agensi = $request->agensi;

        if ($pegawaiId) $filter['user_id'] = $pegawaiId;

        // SENARAI PEGAWAI
        $senaraiPegawai = User::where('role', 'user')
            ->when($user->role !== 'super_admin', function ($query) use ($user) {
                $query->where('negeri', $user->negeri);
            })
            ->orderBy('name')
            ->get();

        // SENARAI BULAN
        $senaraiBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
            5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
        ];

        // SENARAI TAHUN (mengikut Pandangan Undang-undang)
        $senaraiTahun = LaporanPandanganUndang::selectRaw('DISTINCT EXTRACT(YEAR FROM created_at) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        // SENARAI AGENSI (mengikut Pandangan Undang-undang)
        $senaraiAgensi = LaporanPandanganUndang::select('agensi')
            ->whereNotNull('agensi')
            ->distinct()
            ->pluck('agensi');

        // KIRA SUKU (Data untuk Chart)
        $undang = $this->kiraSuku(LaporanPandanganUndang::class, $filter);
        $dataPandangUndang = $undang; 
        
        $tatatertib = $this->kiraSuku(Kestatatertib::class, $filter);
        $mesyuarat = $this->kiraSuku(LaporanMesyuarat::class, $filter);
        $lain = $this->kiraSuku(LainLainTugasan::class, $filter);
        $kesmahkamah = $this->kiraSuku(LaporanKesMahkamah::class, $filter);
        $gubalan = $this->kiraSuku(LaporanGubalanUndang::class, $filter);
        $pindaan = $this->kiraSuku(LaporanPindaanUndang::class, $filter);
        $semakan = $this->kiraSuku(LaporanSemakanUndang::class, $filter);

        // KIRA BULAN
        $undangBulanIni = $this->kiraBulan(LaporanPandanganUndang::class, $filter, $bulan, $tahun, $agensi);
        $tatatertibBulanIni = $this->kiraBulan(Kestatatertib::class, $filter, $bulan, $tahun);
        $mesyuaratBulanIni = $this->kiraBulan(LaporanMesyuarat::class, $filter, $bulan, $tahun);
        $lainBulanIni = $this->kiraBulan(LainLainTugasan::class, $filter, $bulan, $tahun); 
        $kesMahkamahBulanIni = $this->kiraBulan(LaporanKesMahkamah::class, $filter, $bulan, $tahun);
        $gubalanBulanIni = $this->kiraBulan(LaporanGubalanUndang::class, $filter, $bulan, $tahun);
        $pindaanBulanIni = $this->kiraBulan(LaporanPindaanUndang::class, $filter, $bulan, $tahun);
        $semakanBulanIni = $this->kiraBulan(LaporanSemakanUndang::class, $filter, $bulan, $tahun);

        // JUMLAH BULAN INI
        $bulanIni = $undangBulanIni + $tatatertibBulanIni + $mesyuaratBulanIni + $lainBulanIni +
            $kesMahkamahBulanIni + $gubalanBulanIni + $pindaanBulanIni + $semakanBulanIni;

        // STATUS PANDANGAN – ikut agensi jika dipilih
        $pandanganQuery = LaporanPandanganUndang::query()
            ->when($filter, fn($q) => $this->applyFilter($q, $filter))
            ->when(!empty($agensi) && $agensi !== 'Semua', fn($q) => $q->where('agensi', $agensi));

        $belumSelesai = (clone $pandanganQuery)->where('status', 'Dalam Proses')->count();
        $melepasiTarikh = (clone $pandanganQuery)
            ->where(function ($q) {
                $q->whereNull('ringkasan_pandangan')->orWhere('ringkasan_pandangan', '');
            })
            ->where('created_at', '<', Carbon::now()->subDays(14))
            ->count();

        $sudahTindakan = (clone $pandanganQuery)
            ->whereNotNull('ringkasan_pandangan')
            ->where('ringkasan_pandangan', '!=', '')
            ->count();

        $belumTindakan = (clone $pandanganQuery)
            ->where(function ($q) {
                $q->whereNull('ringkasan_pandangan')->orWhere('ringkasan_pandangan', '');
            })
            ->count();

        // GRAF AGENSI — HANYA PANDANGAN UNDANG-UNDANG
        $agensiQuery = LaporanPandanganUndang::selectRaw('agensi, COUNT(*) as jumlah')
            ->when($filter, fn($q) => $this->applyFilter($q, $filter))
            ->when(!empty($agensi) && $agensi !== 'Semua', fn($q) => $q->where('agensi', $agensi))
            ->groupBy('agensi');

        $agensiData = $agensiQuery->pluck('jumlah', 'agensi')->toArray();

        // GRAF PEGAWAI (tiada kaitan agensi)
        $pegawaiData = $senaraiPegawai->map(function ($pegawai) {
            $jumlah = LaporanPandanganUndang::where('user_id', $pegawai->id)->count()
                + LaporanKesMahkamah::where('user_id', $pegawai->id)->count()
                + LaporanGubalanUndang::where('user_id', $pegawai->id)->count()
                + LaporanPindaanUndang::where('user_id', $pegawai->id)->count()
                + LaporanSemakanUndang::where('user_id', $pegawai->id)->count()
                + LaporanMesyuarat::where('user_id', $pegawai->id)->count()
                + Kestatatertib::where('user_id', $pegawai->id)->count()
                + LainLainTugasan::where('user_id', $pegawai->id)->count();

            return [
                'nama' => $pegawai->name,
                'jumlah' => $jumlah
            ];
        });

        // TOTAL – Pandangan ikut agensi, lain tidak
        $totalPandangan = LaporanPandanganUndang::when($filter, fn($q) => $this->applyFilter($q, $filter))
            ->when(!empty($agensi) && $agensi !== 'Semua', fn($q) => $q->where('agensi', $agensi))
            ->count();

        $totalMahkamah = LaporanKesMahkamah::when($filter, fn($q) => $this->applyFilter($q, $filter))->count();
        $totalGubalan = LaporanGubalanUndang::when($filter, fn($q) => $this->applyFilter($q, $filter))->count();
        $totalPindaan = LaporanPindaanUndang::when($filter, fn($q) => $this->applyFilter($q, $filter))->count();
        $totalSemakan = LaporanSemakanUndang::when($filter, fn($q) => $this->applyFilter($q, $filter))->count();
        $totalMesyuarat = LaporanMesyuarat::when($filter, fn($q) => $this->applyFilter($q, $filter))->count();
        $totalTatatertib = Kestatatertib::when($filter, fn($q) => $this->applyFilter($q, $filter))->count();
        $totalLain = LainLainTugasan::when($filter, fn($q) => $this->applyFilter($q, $filter))->count();

        // 🔥 FIX 2 & 3: Tukar nama view ke dashboard.penasihat dan gunakan array sintaks untuk naming
        return view('dashboard.penasihat', [ 
            // Variable yang dihantar ke View (Mesti sepadan dengan kod JS anda)
            'dataPandanganUndang' => $dataPandangUndang, 
            'dataKesMahkamah' => $kesmahkamah, 
            'dataGubalan' => $gubalan, 
            'dataPindaan' => $pindaan, 
            'dataSemakan' => $semakan, 
            'dataMesyuarat' => $mesyuarat, 
            // Menggunakan ejaan 'dataTataterib' untuk memadankan View lama anda
            'dataTataterib' => $tatatertib, 
            'dataTugasan' => $lain, 
            
            // Variable lain (untuk paparan selain graf)
            'undang' => $undang,
            'tatatertib' => $tatatertib,
            'mesyuarat' => $mesyuarat,
            'lain' => $lain,
            'kesmahkamah' => $kesmahkamah,
            'gubalan' => $gubalan,
            'pindaan' => $pindaan,
            'semakan' => $semakan,
            'undangBulanIni' => $undangBulanIni,
            'tatatertibBulanIni' => $tatatertibBulanIni,
            'mesyuaratBulanIni' => $mesyuaratBulanIni,
            'lainBulanIni' => $lainBulanIni,
            'kesMahkamahBulanIni' => $kesMahkamahBulanIni,
            'gubalanBulanIni' => $gubalanBulanIni,
            'pindaanBulanIni' => $pindaanBulanIni,
            'semakanBulanIni' => $semakanBulanIni,
            'bulanIni' => $bulanIni,
            'belumSelesai' => $belumSelesai,
            'melepasiTarikh' => $melepasiTarikh,
            'sudahTindakan' => $sudahTindakan,
            'belumTindakan' => $belumTindakan,
            'agensiData' => $agensiData,
            'pegawaiData' => $pegawaiData,
            'totalPandangan' => $totalPandangan,
            'totalMahkamah' => $totalMahkamah,
            'totalGubalan' => $totalGubalan,
            'totalPindaan' => $totalPindaan,
            'totalSemakan' => $totalSemakan,
            'totalMesyuarat' => $totalMesyuarat,
            'totalTatatertib' => $totalTatatertib,
            'totalLain' => $totalLain,
            'senaraiPegawai' => $senaraiPegawai,
            'senaraiBulan' => $senaraiBulan,
            'senaraiTahun' => $senaraiTahun,
            'senaraiAgensi' => $senaraiAgensi,
        ]);
    }

    private function getFilterByRole($user)
    {
        if ($user->role === 'super_admin') return [];
        if (in_array($user->role, ['yb', 'pa'])) return ['negeri' => $user->negeri];
        return ['user_id' => $user->id];
    }

    private function applyFilter($query, $filter)
    {
        foreach ($filter as $key => $value) {
            $query->where($key, $value);
        }
        return $query;
    }

    private function kiraSuku($model, $filter)
    {
        $query = $model::query();
        $this->applyFilter($query, $filter);
        $suku = [0, 0, 0, 0];

        foreach ($query->get() as $item) {
            if ($item->created_at) {
                $quarter = ceil(Carbon::parse($item->created_at)->month / 3);
                $suku[$quarter - 1]++;
            }
        }

        return $suku;
    }

    private function kiraBulan($model, $filter, $bulan, $tahun, $agensi = null)
    {
        $query = $model::query();
        $this->applyFilter($query, $filter);

        if ($model === LaporanPandanganUndang::class && !empty($agensi) && $agensi !== 'Semua') {
            $query->where('agensi', $agensi);
        }

        return $query->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->count();
    }
}