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
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * 1. INDEX: Paparan Utama Dashboard Laporan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        // --- A. SET FILTER VISIBILITI ---
        // Rule: PA, YB, Admin, Super Admin nampak SEMUA data (Global).
        // User biasa nampak data SENDIRI sahaja.
        
        $filter = [];
        $role = strtolower($user->role);
        $globalViewRoles = ['super_admin', 'pa', 'yb', 'admin'];

        if (!in_array($role, $globalViewRoles)) {
            // User View: Hanya nampak rekod user_id sendiri
            $filter['user_id'] = $user->id;
        }

        // --- B. PANDANGAN UNDANG-UNDANG (LOGIC TRIPLE THREAT) ---
        // Cari rekod yang: Diterima bulan ni ATAU Ada Tindakan bulan ni ATAU Selesai bulan ni
        $laporan = LaporanPandanganUndang::query()
            ->where($filter)
            ->where('is_current', true)
            ->where(function($q) use ($tahun, $bulan) {
                if ($tahun != 'all') {
                    // 1: Tarikh Terima
                    $q->where(function($sub) use ($tahun, $bulan) {
                        $sub->whereYear('tarikh_terima', $tahun);
                        if($bulan != 'all') $sub->whereMonth('tarikh_terima', $bulan);
                    })
                    // 2: Updated At (Tarikh Tindakan)
                    ->orWhere(function($sub) use ($tahun, $bulan) {
                        $sub->whereYear('updated_at', $tahun);
                        if($bulan != 'all') $sub->whereMonth('updated_at', $bulan);
                    })
                    // 3: Tarikh Selesai
                    ->orWhere(function($sub) use ($tahun, $bulan) {
                        $sub->whereYear('tarikh_selesai', $tahun);
                        if($bulan != 'all') $sub->whereMonth('tarikh_selesai', $bulan);
                    });
                }
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        // --- C. DATA MODUL LAIN (Simple Filter) ---

        // 1. Laporan Kes Mahkamah (Guna tarikh_sebutan)
        $laporan_kesmahkamah = LaporanKesMahkamah::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('tarikh_sebutan', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('tarikh_sebutan', $bulan))
            ->orderBy('tarikh_sebutan', 'desc')
            ->get();

        // 2. Laporan Gubalan (Guna created_at)
        $laporan_gubalan = LaporanGubalanUndang::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        // 3. Laporan Pindaan (Guna created_at)
        $laporan_pindaan = LaporanPindaanUndang::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        // 4. Laporan Semakan (Guna created_at)
        $laporan_semakan = LaporanSemakanUndang::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        // 5. Laporan Mesyuarat (🔥 KEMASKINI: Guna tarikh_mesyuarat 🔥)
        $laporan_mesyuarat = LaporanMesyuarat::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('tarikh_mesyuarat', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('tarikh_mesyuarat', $bulan))
            ->orderBy('tarikh_mesyuarat', 'desc') // Susun ikut tarikh mesyuarat terkini
            ->get();

        // 6. Kes Tatatertib (Guna tarikh_terima)
        $laporan_tatatertib = Kestatatertib::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('tarikh_terima', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('tarikh_terima', $bulan))
            ->get();

        // 7. Lain-lain Tugasan (Guna created_at)
        $laporan_lainlain = LainLainTugasan::where($filter)
            ->when($tahun != 'all', fn($q) => $q->whereYear('tarikh', $tahun))
            ->when($bulan != 'all', fn($q) => $q->whereMonth('tarikh', $bulan))
            ->get();

        // --- D. LAMPIRAN KES MAHKAMAH (Statistik) ---
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

    /**
     * 2. PAPAR BORANG LAMPIRAN
     */
    public function lampiranForm() {
        $this->authorizePA();
        $kategori_list = ['Perlembagaan', 'Tanah / PBT', 'Rujukan tanah', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan', 'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain'];
        $bulan = now()->month; 
        $tahun = now()->year;
        $rekod = LampiranKesMahkamah::where('user_id', Auth::id())
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->get()
                    ->keyBy('kategori');

        return view('lampiran.form', compact('kategori_list', 'rekod', 'bulan', 'tahun'));
    }

    /**
     * 3. SIMPAN DATA LAMPIRAN
     */
    public function simpanLampiran(Request $request) {
        $this->authorizePA();
        $kategori_list = ['Perlembagaan', 'Tanah / PBT', 'Rujukan tanah', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan', 'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain'];
        $data = $request->input('data');
        $bulan = now()->month; 
        $tahun = now()->year; 
        $user = Auth::user();

        // Reset data bulan semasa sebelum simpan baru (untuk elak duplicate)
        LampiranKesMahkamah::where('user_id', $user->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->delete();

        foreach ($kategori_list as $i => $kategori) {
            LampiranKesMahkamah::create([
                'user_id' => $user->id, 
                'negeri' => $user->negeri, 
                'kategori' => $kategori,
                'bil_aktif' => $data[$i][0] ?? 0, 
                'majistret' => $data[$i][1] ?? 0, 
                'sesi' => $data[$i][2] ?? 0,
                'tinggi' => $data[$i][3] ?? 0, 
                'rayuan' => $data[$i][4] ?? 0, 
                'persk' => $data[$i][5] ?? 0,
                'status' => $data[$i][6] ?? '-', 
                'bulan' => $bulan, 
                'tahun' => $tahun,
            ]);
        }
        return redirect()->route('laporan.index')->with('success', 'Data Lampiran II berjaya disimpan.');
    }

    /**
     * Helper: Semak Kebenaran (PA Sahaja)
     */
    protected function authorizePA() {
        if (strtolower(Auth::user()->role) !== 'pa') { 
            abort(403, 'Akses hanya dibenarkan kepada PA.'); 
        }
    }
}