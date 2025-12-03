<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanGubalanUndang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanGubalanUndangController extends Controller
{
    /**
     * 1. INDEX: Senarai Laporan (Default Bulan Semasa)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanGubalanUndang::query();

        // A. Filter Ikut Role
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // B. Filter Bulan (Updated)
        // Default: Bulan Semasa (date('n'))
        // Jika user pilih 'all', set variable jadi 'all'
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = date('Y');

        if ($bulanPilihan !== 'all') {
            // Filter ikut bulan 'created_at'
            $query->whereMonth('created_at', $bulanPilihan)
                  ->whereYear('created_at', $tahunSemasa);
        }

        $data = $query->latest()->get();

        return view('laporangubalanundang.index', compact('data', 'user'));
    }

    public function create()
    {
        return view('laporangubalanundang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string',
            'tindakan' => 'required|string',
            'status' => 'required|string',
        ]);

        $user = Auth::user();

        $validated['user_id'] = $user->id;
        $validated['negeri'] = $user->negeri;

        LaporanGubalanUndang::create($validated);

        return redirect()->route('laporangubalanundang.index')->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanGubalanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        return view('laporangubalanundang.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string',
            'tindakan' => 'required|string',
            'status' => 'required|string',
        ]);

        $laporan = LaporanGubalanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->update($validated);

        return redirect()->route('laporangubalanundang.index')->with('success', 'Laporan berjaya dikemas kini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanGubalanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->delete();

        return redirect()->route('laporangubalanundang.index')->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Helper: Check Permission
     */
    protected function canEdit(LaporanGubalanUndang $laporan)
    {
        $user = Auth::user();

        return (
            ($user->role === 'pa' && $user->negeri === $laporan->negeri) ||
            ($user->id === $laporan->user_id)
        );
    }

    /**
     * 7. PECAHAN BULAN (Data Grafik)
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = Carbon::create()->month($bulan)->format('F');

        // Setting Column
        $colKategori = 'tajuk'; 

        // Senarai Tetap (3 Item Utama)
        $standardList = [
            'Rang Undang-Undang',
            'Perundangan Subsidiari Substantif',
            'Pemberitahuan Awam (G.N)'
        ];

        // Query Database
        $dbData = LaporanGubalanUndang::select($colKategori, DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy($colKategori)
            ->pluck('total', $colKategori)
            ->toArray();

        $dataPecahan = collect();
        $labels = [];
        $totals = [];

        // Loop untuk pastikan 3 kategori tu sentiasa keluar walaupun 0
        foreach ($standardList as $item) {
            $count = $dbData[$item] ?? 0;

            // Masuk ke collection (untuk Table)
            if ($count > 0) {
                $dataPecahan->push((object)[
                    'kategori' => $item,
                    'total' => $count
                ]);
                
                // Masuk ke array (untuk Graf)
                $labels[] = $item;
                $totals[] = $count;
            }
        }

        return view('laporangubalanundang.pecahan', compact(
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan'
        ));
    }
}