<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPindaanUndang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanpindaanundangController extends Controller
{
    /**
     * 1. INDEX: Senarai Laporan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanPindaanUndang::query();

        // --- A. FILTER VISIBILITI (Global vs User) ---
        // Rule: PA, YB, Admin, Super Admin nampak SEMUA data (Global).
        $role = strtolower($user->role);
        $globalViewRoles = ['super_admin', 'pa', 'yb', 'admin'];

        if (in_array($role, $globalViewRoles)) {
            // Global View: Tiada filter tambahan (Nampak semua)
        } else {
            // User View: Hanya nampak data sendiri
            $query->where('user_id', $user->id);
        }

        // --- B. FILTER TARIKH (Guna created_at) ---
        $bulan = $request->input('bulan', 'all'); 
        $tahun = $request->input('tahun', date('Y'));

        // Filter Tahun
        if ($tahun != 'all') {
            $query->whereYear('created_at', $tahun);
        }

        // Filter Bulan
        if ($bulan != 'all') {
            $query->whereMonth('created_at', $bulan);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('laporanpindaanundang.index', compact('data', 'user', 'tahun', 'bulan'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('laporanpindaanundang.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string|max:255',
            'tindakan' => 'required|string|max:1000',
            'status' => 'required|string|max:1000',
        ]);

        try {
            LaporanPindaanUndang::create(array_merge($validated, [
                'user_id' => auth()->id(),
                'negeri' => auth()->user()->negeri,
            ]));

            return redirect()->route('laporanpindaanundang.index')
                             ->with('success', 'Laporan berjaya disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal simpan laporan pindaan: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Ralat semasa menyimpan data. Sila cuba semula.'
            ])->withInput();
        }
    }

    /**
     * 4. EDIT
     */
    public function edit($id)
    {
        $laporan = LaporanPindaanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk edit.');
        }

        return view('laporanpindaanundang.edit', compact('laporan'));
    }

    /**
     * 5. UPDATE
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string|max:255',
            'tindakan' => 'required|string|max:1000',
            'status' => 'required|string|max:1000',
        ]);

        $laporan = LaporanPindaanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk kemaskini.');
        }

        $laporan->update($validated);

        return redirect()->route('laporanpindaanundang.index')
                         ->with('success', 'Laporan berjaya dikemaskini.');
    }

    /**
     * 6. DESTROY
     */
    public function destroy($id)
    {
        $laporan = LaporanPindaanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk padam.');
        }

        $laporan->delete();

        return redirect()->route('laporanpindaanundang.index')
                         ->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Helper: Check Permission
     */
    protected function canEdit(LaporanPindaanUndang $laporan)
    {
        $user = auth()->user();
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

        $colKategori = 'tajuk'; 

        // Query Database
        $dataPecahan = LaporanPindaanUndang::select($colKategori, DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy($colKategori)
            ->orderBy('total', 'desc')
            ->get();

        $labels = $dataPecahan->pluck($colKategori);
        $totals = $dataPecahan->pluck('total');

        return view('laporanpindaanundang.pecahan', compact(
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan'
        ));
    }
}