<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanSemakanUndang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class LaporansemakanundangController extends Controller
{
    /**
     * INDEX: Senarai Laporan (Default Bulan Semasa)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanSemakanUndang::query();

        // 1. Filter Ikut Peranan
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // 2. Filter Ikut Bulan (Logic Baru)
        // Default: Bulan Semasa (date('n'))
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = date('Y');

        if ($bulanPilihan !== 'all') {
            // Tapis ikut bulan yang dipilih (atau default bulan semasa)
            $query->whereMonth('created_at', $bulanPilihan)
                  ->whereYear('created_at', $tahunSemasa);
        }

        // 3. Susunan Data
        $data = $query->orderBy('created_at', 'desc')->get();

        return view('laporansemakanundang.index', compact('data', 'user'));
    }

    public function create()
    {
        return view('laporansemakanundang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string|max:500',
            'tindakan' => 'required|string|max:1000',
            'status' => 'required|string|max:100',
        ]);

        LaporanSemakanUndang::create([
            'tajuk' => $validated['tajuk'],
            'tindakan' => $validated['tindakan'],
            'status' => $validated['status'],
            'user_id' => auth()->id(),
            'negeri' => auth()->user()->negeri,
        ]);

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan semakan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanSemakanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        return view('laporansemakanundang.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string|max:500',
            'tindakan' => 'required|string|max:1000',
            'status' => 'required|string|max:100',
        ]);

        $laporan = LaporanSemakanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->update($validated);

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanSemakanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->delete();

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Helper: Check Permission
     */
    protected function canEdit(LaporanSemakanUndang $laporan)
    {
        $user = auth()->user();
        return ($user->role === 'pa' && $user->negeri === $laporan->negeri)
            || ($laporan->user_id === $user->id);
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
        $dbData = LaporanSemakanUndang::select($colKategori, DB::raw('count(*) as total'))
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

        return view('laporansemakanundang.pecahan', compact(
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan'
        ));
    }
}