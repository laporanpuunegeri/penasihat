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

        // --- A. FILTER VISIBILITI (Global vs User) ---
        // Rule: PA, YB, Admin, Super Admin nampak SEMUA data (Global).
        $role = strtolower($user->role);
        $globalViewRoles = ['super_admin', 'pa', 'yb', 'admin'];

        if (in_array($role, $globalViewRoles)) {
            // Global View: Tiada filter tambahan (Nampak semua data)
        } else {
            // User View: Hanya nampak data sendiri
            $query->where('user_id', $user->id);
        }

        // --- B. FILTER TARIKH (Guna 'created_at') ---
        // Default: Bulan Semasa (date('n'))
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = $request->input('tahun', date('Y')); // Tambah input tahun

        if ($bulanPilihan !== 'all') {
            $query->whereMonth('created_at', $bulanPilihan);
        }
        
        // Filter tahun sentiasa aktif (kecuali logic khas, tapi standardnya perlu tahun)
        $query->whereYear('created_at', $tahunSemasa);

        // 3. Susunan Data
        $data = $query->orderBy('created_at', 'desc')->get();

        // Pass tahun ke view supaya dropdown tahun tak reset
        return view('laporansemakanundang.index', compact('data', 'user', 'tahunSemasa', 'bulanPilihan'));
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
            abort(403, 'Tiada kebenaran untuk edit.');
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
            abort(403, 'Tiada kebenaran untuk kemaskini.');
        }

        $laporan->update($validated);

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanSemakanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk padam.');
        }

        $laporan->delete();

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Helper: Check Permission
     * Rule: Global Roles boleh semua, User biasa hanya sendiri.
     */
    protected function canEdit(LaporanSemakanUndang $laporan)
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
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

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