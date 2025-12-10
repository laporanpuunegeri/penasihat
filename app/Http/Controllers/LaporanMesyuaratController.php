<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanMesyuarat;
use App\Models\Pergerakan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LaporanMesyuaratController extends Controller
{
    /**
     * 1. INDEX: Papar senarai laporan mesyuarat
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanMesyuarat::query();

        // --- A. FILTER VISIBILITI (Global vs User) ---
        $role = strtolower($user->role);
        $globalViewRoles = ['super_admin', 'pa', 'yb', 'admin'];

        if (in_array($role, $globalViewRoles)) {
            // Global View: Nampak semua data
        } else {
            // User View: Hanya nampak data sendiri
            $query->where('user_id', $user->id);
        }

        // --- B. FILTER TARIKH (Guna 'tarikh_mesyuarat') ---
        $bulan = $request->input('bulan', 'all');
        $tahun = $request->input('tahun', date('Y'));

        // Filter Tahun
        if ($tahun != 'all') {
            $query->whereYear('tarikh_mesyuarat', $tahun);
        }

        // Filter Bulan
        if ($bulan != 'all') {
            $query->whereMonth('tarikh_mesyuarat', $bulan);
        }

        // Filter Status (Optional)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporanMesyuarat = $query->orderBy('tarikh_mesyuarat', 'desc')->get();

        return view('laporanmesyuarat.index', compact('laporanMesyuarat', 'user', 'tahun', 'bulan'));
    }

    /**
     * 2. CREATE: Papar form create
     */
    public function create()
    {
        return view('laporanmesyuarat.create');
    }

    /**
     * 3. STORE: Simpan laporan mesyuarat (Single or Multi-Day)
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'mesyuarat' => 'required|string|max:255',
            'isu' => 'required|string|max:1000',
            'tarikh_mesyuarat' => 'required|date',
            'status' => 'required|string|max:100',
            'pandangan' => 'required|in:Lisan,Bertulis',
            // Validation untuk multi-day (jika ada)
            'tarikh_akhir' => 'nullable|date|after_or_equal:tarikh_mesyuarat',
        ]);

        $user = Auth::user();
        
        // Data asas yang sama untuk semua hari
        $baseData = [
            'mesyuarat' => $validated['mesyuarat'],
            'isu' => $validated['isu'],
            'status' => $validated['status'],
            'pandangan' => $validated['pandangan'],
            'user_id' => $user->id,
            'negeri' => $user->negeri,
        ];

        // --- LOGIC SIMPANAN (MULTI VS SINGLE) ---
        if ($request->has('is_multiday') && $request->tarikh_akhir) {
            
            // A. MESYUARAT BERBILANG HARI (LOOPING)
            $startDate = Carbon::parse($request->tarikh_mesyuarat);
            $endDate = Carbon::parse($request->tarikh_akhir);
            
            // Dapatkan semua tarikh dalam julat
            $period = CarbonPeriod::create($startDate, $endDate);

            $dayCount = 1;
            foreach ($period as $date) {
                $currentDate = $date->format('Y-m-d');

                // 1. Simpan Laporan Mesyuarat
                LaporanMesyuarat::create(array_merge($baseData, [
                    'tarikh_mesyuarat' => $currentDate
                ]));

                // 2. Simpan Pergerakan (Automatik)
                Pergerakan::create([
                    'user_id'     => $user->id,
                    'tarikh'      => $currentDate,
                    'tarikh_mula' => $currentDate, 
                    'tarikh_akhir'=> $currentDate, 
                    'jenis'       => 'Mesyuarat',
                    // Tambah nota hari untuk pergerakan
                    'catatan'     => $validated['mesyuarat'] . ' (Hari ke-' . $dayCount . ')',
                    'negeri'      => $user->negeri,
                    'kenderaan'   => '', 
                ]);
                $dayCount++;
            }
            
            $message = 'Rekod mesyuarat bersiri (' . ($dayCount - 1) . ' hari) berjaya disimpan.';

        } else {
            
            // B. MESYUARAT SEHARI (BIASA)
            // 1. Simpan Laporan Mesyuarat
            LaporanMesyuarat::create(array_merge($baseData, [
                'tarikh_mesyuarat' => $validated['tarikh_mesyuarat']
            ]));

            // 2. Simpan Pergerakan
            Pergerakan::create([
                'user_id'     => $user->id,
                'tarikh'      => $validated['tarikh_mesyuarat'],
                'tarikh_mula' => $validated['tarikh_mesyuarat'], 
                'tarikh_akhir'=> $validated['tarikh_mesyuarat'], 
                'jenis'       => 'Mesyuarat',
                'catatan'     => $validated['mesyuarat'],
                'negeri'      => $user->negeri,
                'kenderaan'   => '', 
            ]);
            
            $message = 'Laporan mesyuarat & pergerakan berjaya disimpan.';
        }

        return redirect()->route('laporanmesyuarat.index')->with('success', $message);
    }

    /**
     * 4. EDIT: Papar form edit
     */
    public function edit($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk edit.');
        }

        return view('laporanmesyuarat.edit', compact('laporan'));
    }

    /**
     * 5. UPDATE: Update laporan mesyuarat
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mesyuarat' => 'required|string|max:255',
            'isu' => 'required|string|max:1000',
            'tarikh_mesyuarat' => 'required|date',
            'status' => 'required|string|max:100',
            'pandangan' => 'required|in:Lisan,Bertulis',
        ]);

        $laporan = LaporanMesyuarat::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk kemaskini.');
        }

        $laporan->update($validated);

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dikemaskini.');
    }

    /**
     * 6. DESTROY: Hapus laporan mesyuarat
     */
    public function destroy($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk padam.');
        }

        $laporan->delete();

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dipadam.');
    }

    /**
     * Helper: Tentukan jika user boleh edit/padam
     */
    protected function canEdit(LaporanMesyuarat $laporan)
    {
        $user = Auth::user();
        $role = strtolower($user->role);

        // 1. Super Admin, PA, YB, Admin boleh edit SEMUA
        if (in_array($role, ['super_admin', 'pa', 'yb', 'admin'])) {
            return true;
        }

        // 2. User biasa edit sendiri
        return ($laporan->user_id === $user->id);
    }

    /**
     * 7. PECAHAN BULAN (Data Grafik)
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = Carbon::create()->month($bulan)->format('F');

        $colKategori = 'pandangan'; 
        $standardList = ['Lisan', 'Bertulis'];

        // Gunakan 'tarikh_mesyuarat' supaya tally dengan index
        $dbData = LaporanMesyuarat::select($colKategori, DB::raw('count(*) as total'))
            ->whereMonth('tarikh_mesyuarat', $bulan)
            ->whereYear('tarikh_mesyuarat', $tahun)
            ->groupBy($colKategori)
            ->pluck('total', $colKategori)
            ->toArray();

        $dataPecahan = collect();
        $labels = [];
        $totals = [];

        foreach ($standardList as $item) {
            $count = $dbData[$item] ?? 0;
            $dataPecahan->push((object)['kategori' => $item, 'total' => $count]);
            $labels[] = $item;
            $totals[] = $count;
        }

        return view('laporanmesyuarat.pecahan', compact('bulan', 'tahun', 'namaBulan', 'labels', 'totals', 'dataPecahan'));
    }
}