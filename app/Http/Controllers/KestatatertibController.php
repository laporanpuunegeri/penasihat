<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kestatatertib;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KestatatertibController extends Controller
{
    /**
     * INDEX: Senarai Laporan (Default Bulan Semasa)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kestatatertib::query();

        // 1. Filter Ikut Peranan
        if ($user->role === 'yb' || $user->role === 'pa') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // 2. Filter Ikut Bulan (Logic Baru)
        // Default: Bulan Semasa (date('n'))
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = date('Y');

        if ($bulanPilihan !== 'all') {
            // Filter ikut bulan 'created_at' (tarikh daftar)
            $query->whereMonth('created_at', $bulanPilihan)
                  ->whereYear('created_at', $tahunSemasa);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('kestatatertib.index', compact('data', 'user'));
    }

    public function create()
    {
        return view('kestatatertib.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarikh_terima' => 'required|date',
            'kategori' => 'required|string|max:255',
            'fakta_ringkasan' => 'nullable|string',
            'isu' => 'nullable|string',
            'ringkasan_pandangan' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'tarikh_selesai' => 'nullable|date',
        ]);

        $validated['hantar_kepada_boss'] = $request->has('hantar_kepada_boss');
        $validated['tarikh_daftar'] = now();
        $validated['user_id'] = auth()->id();
        $validated['negeri'] = auth()->user()->negeri;

        Kestatatertib::create($validated);

        return redirect()->route('kestatatertib.index')->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit(Kestatatertib $kestatatertib)
    {
        if (! $this->canEdit($kestatatertib)) {
            abort(403);
        }

        // Namakan variable 'laporan' supaya konsisten dengan view edit.blade.php
        return view('kestatatertib.edit', ['laporan' => $kestatatertib]);
    }

    public function update(Request $request, Kestatatertib $kestatatertib)
    {
        if (! $this->canEdit($kestatatertib)) {
            abort(403);
        }

        $validated = $request->validate([
            'tarikh_terima' => 'required|date',
            'kategori' => 'required|string|max:255',
            'fakta_ringkasan' => 'nullable|string',
            'isu' => 'nullable|string',
            'ringkasan_pandangan' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'tarikh_selesai' => 'nullable|date',
        ]);

        $validated['hantar_kepada_boss'] = $request->has('hantar_kepada_boss');

        $kestatatertib->update($validated);

        return redirect()->route('kestatatertib.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy(Kestatatertib $kestatatertib)
    {
        if (! $this->canEdit($kestatatertib)) {
            abort(403);
        }

        $kestatatertib->delete();

        return back()->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Helper: Check Permission
     */
    protected function canEdit(Kestatatertib $laporan)
    {
        $user = auth()->user();
        return ($user->role === 'pa' && $user->negeri === $laporan->negeri)
            || ($laporan->user_id === $user->id);
    }

/**
     * 7. PECAHAN BULAN
     * Fungsi ini memecahkan jumlah kes mengikut kategori/status.
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $user = Auth::user();
        
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

        // 1. Re-apply Filter Logic (Wajib, supaya data ikut peranan yang login)
        $query = Kestatatertib::query();
        if ($user->role === 'yb' || $user->role === 'pa') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // 2. Build Query Pecahan
        $dataPecahan = $query->select('status as kategori', DB::raw('count(*) as jumlah'))
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('status')
            ->orderBy('jumlah', 'desc')
            ->get();
            
        // 3. Kira Jumlah Keseluruhan
        $labels = $dataPecahan->pluck('kategori');
        $totals = $dataPecahan->pluck('jumlah');
        $jumlahKeseluruhan = $dataPecahan->sum('jumlah');


        // 4. Return ke View yang BETUL
        return view('kestatatertib.pecahan', compact( // 🔥 VIEW NAMA DIBETULKAN
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan', 
            'jumlahKeseluruhan'
        ));
    }    
}