<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LainLainTugasan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanLainLainController extends Controller
{
    /**
     * 1. INDEX: Senarai Laporan (Filter guna 'tarikh')
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LainLainTugasan::query();

        // --- A. FILTER VISIBILITI (Global vs User) ---
        // Rule: PA, YB, Admin, Super Admin nampak SEMUA data.
        $role = strtolower($user->role);
        $globalViewRoles = ['super_admin', 'pa', 'yb', 'admin'];

        if (in_array($role, $globalViewRoles)) {
            // Global View: Tiada filter user_id (nampak semua)
        } else {
            // User View: Hanya nampak data sendiri
            $query->where('user_id', $user->id);
        }

        // --- B. FILTER TARIKH (Guna column 'tarikh') ---
        $bulan = $request->input('bulan', 'all'); 
        $tahun = $request->input('tahun', date('Y'));

        // Filter Tahun
        if ($tahun != 'all') {
            $query->whereYear('tarikh', $tahun);
        }

        // Filter Bulan (Jika user pilih 'all', jangan filter bulan)
        if ($bulan != 'all') {
            $query->whereMonth('tarikh', $bulan);
        }

        $data = $query->orderBy('tarikh', 'desc')->get();

        return view('lainlaintugasan.index', compact('data', 'user', 'tahun', 'bulan'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('lainlaintugasan.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'perihal' => 'required|string',
            'tarikh' => 'required|date',
            'tindakan' => 'required|string',
        ]);

        $user = Auth::user();

        LainLainTugasan::create([
            'perihal' => $request->perihal,
            'tarikh' => $request->tarikh,
            'tindakan' => $request->tindakan,
            'user_id' => $user->id,
            'negeri' => $user->negeri,
            'hantar_kepada_boss' => $request->has('hantar_kepada_boss'),
            'created_by' => $user->id,
        ]);

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dihantar.');
    }

    /**
     * 4. EDIT
     */
    public function edit($id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        if (! $this->canEdit($tugasan)) {
            abort(403, 'Tiada kebenaran untuk edit.');
        }

        return view('lainlaintugasan.edit', compact('tugasan'));
    }

    /**
     * 5. UPDATE
     */
    public function update(Request $request, $id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        if (! $this->canEdit($tugasan)) {
            abort(403, 'Tiada kebenaran untuk kemaskini.');
        }

        $request->validate([
            'perihal' => 'required|string',
            'tarikh' => 'required|date',
            'tindakan' => 'required|string',
        ]);

        $tugasan->update($request->only('perihal', 'tarikh', 'tindakan'));

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    /**
     * 6. DESTROY
     */
    public function destroy($id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        if (! $this->canEdit($tugasan)) {
            abort(403, 'Tiada kebenaran untuk padam.');
        }

        $tugasan->delete();

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * 7. PECAHAN BULAN (Data Grafik)
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

        $colKategori = 'tindakan'; 
        $standardList = ['Telah Hadir', 'Telah Bincang', 'Telah Disemak', 'Selesai'];

        // Query guna 'tarikh' supaya konsisten dengan index
        $dbData = LainLainTugasan::select($colKategori, DB::raw('count(*) as total'))
            ->whereMonth('tarikh', $bulan) 
            ->whereYear('tarikh', $tahun)
            ->groupBy($colKategori)
            ->pluck('total', $colKategori)
            ->toArray();

        $dataPecahan = collect();
        $labels = [];
        $totals = [];

        foreach ($standardList as $item) {
            $count = $dbData[$item] ?? 0;
            if ($count > 0) {
                $dataPecahan->push((object)['kategori' => $item, 'total' => $count]);
                $labels[] = $item;
                $totals[] = $count;
            }
        }

        // Dummy data for view compatibility
        $dataAgensi = collect(); $labelsAgensi = []; $totalsAgensi = [];

        return view('lainlaintugasan.pecahan', compact('bulan', 'tahun', 'namaBulan', 'labels', 'totals', 'dataPecahan', 'dataAgensi', 'labelsAgensi', 'totalsAgensi'));
    }

    /**
     * Helper: Check Permission
     */
    protected function canEdit(LainLainTugasan $tugasan)
    {
        $user = Auth::user();
        $role = strtolower($user->role);
        
        // 1. Super Admin, PA, YB, Admin boleh buat semua (Global)
        if (in_array($role, ['super_admin', 'pa', 'yb', 'admin'])) {
            return true;
        }
        
        // 2. User asal boleh edit/padam rekod sendiri
        if ($user->id === $tugasan->user_id) {
            return true;
        }
        
        return false;
    }
}