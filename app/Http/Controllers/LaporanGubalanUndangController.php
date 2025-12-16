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
     * 1. INDEX: Senarai Laporan (Global View untuk YB/PA/Admin)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanGubalanUndang::query();

        // --- A. FILTER VISIBILITI (Global vs User) ---
        // Rule: PA, YB, Admin, Super Admin nampak SEMUA data.
        $role = strtolower($user->role);
        $globalViewRoles = ['super_admin', 'pa', 'yb', 'admin'];

        if (in_array($role, $globalViewRoles)) {
            // Global View: Nampak semua data (Tiada filter user_id/negeri)
        } else {
            // User View: Hanya nampak data sendiri
            $query->where('user_id', $user->id);
        }

        // --- B. FILTER TARIKH (Guna created_at) ---
        $bulan = $request->input('bulan', 'all'); 
        
        // 🔥 UPDATE: Default Tahun 'all' supaya data lama keluar jika tak pilih tahun
        $tahun = $request->input('tahun', 'all');

        // Filter Tahun
        if ($tahun != 'all') {
            $query->whereYear('created_at', $tahun);
        }

        // Filter Bulan
        if ($bulan != 'all') {
            $query->whereMonth('created_at', $bulan);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('laporangubalanundang.index', compact('data', 'user', 'tahun', 'bulan'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('laporangubalanundang.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string',
            'tindakan' => 'required|string',
            'status' => 'required|string',
        ]);

        $user = Auth::user();

        // Simpan data
        LaporanGubalanUndang::create([
            'tajuk' => $request->tajuk,
            'tindakan' => $request->tindakan,
            'status' => $request->status,
            'user_id' => $user->id,
            'negeri' => $user->negeri,
        ]);

        return redirect()->route('laporangubalanundang.index')->with('success', 'Laporan berjaya disimpan.');
    }

    /**
     * 4. EDIT
     */
    public function edit($id)
    {
        $laporan = LaporanGubalanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk edit.');
        }

        return view('laporangubalanundang.edit', compact('laporan'));
    }

    /**
     * 5. UPDATE
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string',
            'tindakan' => 'required|string',
            'status' => 'required|string',
        ]);

        $laporan = LaporanGubalanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk kemaskini.');
        }

        $laporan->update($validated);

        return redirect()->route('laporangubalanundang.index')->with('success', 'Laporan berjaya dikemas kini.');
    }

    /**
     * 6. DESTROY
     */
    public function destroy($id)
    {
        $laporan = LaporanGubalanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403, 'Tiada kebenaran untuk padam.');
        }

        $laporan->delete();

        return redirect()->route('laporangubalanundang.index')->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Helper: Check Permission (Global Access)
     */
    protected function canEdit(LaporanGubalanUndang $laporan)
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
        $user = auth()->user();
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

        // Nama column dalam database
        $colKategori = 'tajuk'; 

        // 1. SETUP QUERY & FILTER ROLE
        $query = \App\Models\LaporanGubalanUndang::query();

        // Logic Filter: YB/PA nampak semua
        if (in_array(strtolower($user->role), ['super_admin', 'yb', 'pa', 'admin'])) {
            // Tiada filter
        } elseif (in_array(strtolower($user->role), ['eo'])) {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // 2. DAPATKAN SENARAI DATA PENUH ($data)
        $data = $query->clone()
                      ->whereMonth('created_at', $bulan)
                      ->whereYear('created_at', $tahun)
                      ->orderBy('created_at', 'desc')
                      ->get();

        // 3. STATISTIK DYNAMIC (KIRA JUMLAH)
        $dataPecahanRaw = $query->select($colKategori, \DB::raw('count(*) as jumlah'))
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy($colKategori)
            ->orderBy('jumlah', 'desc')
            ->get();

        // 4. FORMATKAN DATA (HANTAR DUA-DUA 'TOTAL' & 'JUMLAH')
        $dataPecahan = collect();
        $labels = [];
        $totals = [];

        foreach ($dataPecahanRaw as $item) {
            $namaTajuk = $item->$colKategori ?? 'Tiada Tajuk';
            
            $dataPecahan->push((object)[
                'kategori' => $namaTajuk,      // Untuk View yang guna 'kategori'
                'jenis_gubalan' => $namaTajuk, // Backup nama
                'jumlah' => $item->jumlah,     // Backup nama
                'total' => $item->jumlah       // 🔥 INI WAJIB ADA SEBAB VIEW CARI 'total' 🔥
            ]);

            $labels[] = $namaTajuk;
            $totals[] = $item->jumlah;
        }

        $jumlahKeseluruhan = array_sum($totals);

        return view('laporangubalanundang.pecahan', compact(
            'data', 'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan',
            'jumlahKeseluruhan'
        ));
    }
}