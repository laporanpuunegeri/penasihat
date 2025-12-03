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
     * INDEX: Senarai Laporan (Default Bulan Semasa)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LainLainTugasan::query();

        // 1. Filter Ikut Peranan
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // 2. Filter Ikut Bulan (Default: Bulan Semasa)
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = date('Y');

        if ($bulanPilihan !== 'all') {
            // Filter ikut 'created_at' atau 'tarikh' (ikut kesesuaian)
            $query->whereMonth('created_at', $bulanPilihan)
                  ->whereYear('created_at', $tahunSemasa);
        }

        $data = $query->latest()->get();

        return view('lainlaintugasan.index', compact('data', 'user'));
    }

    public function create()
    {
        return view('lainlaintugasan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'perihal' => 'required|string',
            'tarikh' => 'required|date',
            'tindakan' => 'required|string',
        ]);

        $user = Auth::user();

        // Simpan single entry (bukan loop) sebab borang create.blade.php hantar satu rekod je
        LainLainTugasan::create([
            'perihal' => $request->perihal,
            'tarikh' => $request->tarikh,
            'tindakan' => $request->tindakan,
            'user_id' => $user->id,
            'negeri' => $user->negeri,
            'hantar_kepada_boss' => $request->has('hantar_kepada_boss'),
        ]);

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dihantar.');
    }

    public function edit($id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        if (! $this->canEdit($tugasan)) {
            abort(403);
        }

        return view('lainlaintugasan.edit', compact('tugasan'));
    }

    public function update(Request $request, $id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        if (! $this->canEdit($tugasan)) {
            abort(403);
        }

        $request->validate([
            'perihal' => 'required|string',
            'tarikh' => 'required|date',
            'tindakan' => 'required|string',
        ]);

        $tugasan->update($request->only('perihal', 'tarikh', 'tindakan'));

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        if (! $this->canEdit($tugasan)) {
            abort(403);
        }

        $tugasan->delete();

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Helper: Check Permission
     */
    protected function canEdit(LainLainTugasan $tugasan)
    {
        $user = Auth::user();
        return ($user->role === 'pa' && $user->negeri === $tugasan->negeri)
            || ($user->id === $tugasan->user_id);
    }

    /**
     * 7. PECAHAN BULAN (Data Grafik: Tindakan)
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

        // Setting Column
        $colKategori = 'tindakan'; 

        // Senarai Tetap
        $standardList = [
            'Telah Hadir',
            'Telah Bincang',
            'Telah Disemak',
            'Selesai'
        ];

        // Query Database
        $dbData = LainLainTugasan::select($colKategori, DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy($colKategori)
            ->pluck('total', $colKategori)
            ->toArray();

        $dataPecahan = collect();
        $labels = [];
        $totals = [];

        foreach ($standardList as $item) {
            $count = $dbData[$item] ?? 0;

            // Masuk ke collection
            if ($count > 0) {
                $dataPecahan->push((object)[
                    'kategori' => $item,
                    'total' => $count
                ]);
                
                $labels[] = $item;
                $totals[] = $count;
            }
        }

        return view('lainlaintugasan.pecahan', compact(
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan'
        ));
    }
}