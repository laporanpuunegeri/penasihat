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
        $tahun = request('tahun', now()->year);

        // --- 1. SET FILTER VISIBILITI ---
        // Rule: PA, YB, Admin, Super Admin nampak SEMUA data (Global).
        // User biasa nampak data SENDIRI.
        
        $filter = [];
        $role = strtolower($user->role);
        $globalViewRoles = ['super_admin', 'pa', 'yb', 'admin'];

        if (in_array($role, $globalViewRoles)) {
            // Global View: Tiada filter tambahan (nampak semua)
        } else {
            // User View: Hanya nampak rekod user_id sendiri
            $filter['user_id'] = $user->id;
        }

        // --- 2. PANDANGAN UNDANG-UNDANG (LOGIC TRIPLE THREAT) ---
        // Cari rekod yang: Diterima bulan ni ATAU Ada Tindakan bulan ni ATAU Selesai bulan ni
        $laporan = LaporanPandanganUndang::query()
            ->where($filter) // Terapkan filter role tadi
            ->where('is_current', true)
            ->where(function($q) use ($tahun, $bulan) {
                if ($tahun != 'all') {
                    // A: Tarikh Terima
                    $q->where(function($sub) use ($tahun, $bulan) {
                        $sub->whereYear('tarikh_terima', $tahun);
                        if($bulan != 'all') $sub->whereMonth('tarikh_terima', $bulan);
                    })
                    // B: Updated At (Tarikh Tindakan)
                    ->orWhere(function($sub) use ($tahun, $bulan) {
                        $sub->whereYear('updated_at', $tahun);
                        if($bulan != 'all') $sub->whereMonth('updated_at', $bulan);
                    })
                    // C: Tarikh Selesai
                    ->orWhere(function($sub) use ($tahun, $bulan) {
                        $sub->whereYear('tarikh_selesai', $tahun);
                        if($bulan != 'all') $sub->whereMonth('tarikh_selesai', $bulan);
                    });
                }
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        // --- 3. DATA LAIN (Guna filter yang sama) ---
        $laporan_kesmahkamah = LaporanKesMahkamah::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('tarikh_sebutan', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('tarikh_sebutan', $bulan))
            ->get();

        $laporan_gubalan = LaporanGubalanUndang::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        $laporan_pindaan = LaporanPindaanUndang::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        $laporan_semakan = LaporanSemakanUndang::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        $laporan_mesyuarat = LaporanMesyuarat::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        $laporan_tatatertib = Kestatatertib::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('tarikh_terima', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('tarikh_terima', $bulan))
            ->get();

        $laporan_lainlain = LainLainTugasan::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        // --- 4. LAMPIRAN KES MAHKAMAH (Statistik) ---
        $lampiran_kesmahkamah = LampiranKesMahkamah::query()
            ->when(!in_array($role, $globalViewRoles), fn($q) => $q->where('user_id', $user->id))
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $kategori_list = ['Perlembagaan', 'Tanah / PBT', 'Rujukan tanah', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan', 'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain'];

        return view('laporan.index', compact(
            'kategori_list', 'laporan', 'laporan_kesmahkamah', 'laporan_gubalan', 
            'laporan_pindaan', 'laporan_semakan', 'laporan_mesyuarat', 'laporan_tatatertib', 
            'laporan_lainlain', 'lampiran_kesmahkamah', 'bulan', 'tahun'
        ));
    }

    public function lampiranForm() {
        $this->authorizePA();
        $kategori_list = ['Perlembagaan', 'Tanah / PBT', 'Rujukan tanah', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan', 'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain'];
        $bulan = now()->month; $tahun = now()->year;
        $rekod = LampiranKesMahkamah::where('user_id', auth()->id())->where('bulan', $bulan)->where('tahun', $tahun)->get()->keyBy('kategori');
        return view('lampiran.form', compact('kategori_list', 'rekod', 'bulan', 'tahun'));
    }

    public function simpanLampiran(Request $request) {
        $this->authorizePA();
        $kategori_list = ['Perlembagaan', 'Tanah / PBT', 'Rujukan tanah', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan', 'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain'];
        $data = $request->input('data');
        $bulan = now()->month; $tahun = now()->year; $user = auth()->user();
        LampiranKesMahkamah::where('user_id', $user->id)->where('bulan', $bulan)->where('tahun', $tahun)->delete();
        foreach ($kategori_list as $i => $kategori) {
            LampiranKesMahkamah::create([
                'user_id' => $user->id, 'negeri' => $user->negeri, 'kategori' => $kategori,
                'bil_aktif' => $data[$i][0] ?? 0, 'majistret' => $data[$i][1] ?? 0, 'sesi' => $data[$i][2] ?? 0,
                'tinggi' => $data[$i][3] ?? 0, 'rayuan' => $data[$i][4] ?? 0, 'persk' => $data[$i][5] ?? 0,
                'status' => $data[$i][6] ?? '-', 'bulan' => $bulan, 'tahun' => $tahun,
            ]);
        }
        return redirect()->route('laporan.index')->with('success', 'Data Lampiran II berjaya disimpan.');
    }

    protected function authorizePA() {
        if (auth()->user()->role !== 'pa') { abort(403, 'Akses hanya dibenarkan kepada PA.'); }
    }
}