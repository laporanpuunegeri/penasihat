<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanMesyuarat;
use App\Models\Pergerakan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <--- DITAMBAH UNTUK QUERY GRAF

class LaporanMesyuaratController extends Controller
{
    /**
     * Papar senarai laporan mesyuarat
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanMesyuarat::query();

        // Filter ikut role
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tarikh_mesyuarat', $request->bulan)
                  ->whereYear('tarikh_mesyuarat', now()->year);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporanMesyuarat = $query->orderBy('tarikh_mesyuarat', 'desc')->get();

        return view('laporanmesyuarat.index', compact('laporanMesyuarat', 'user'));
    }

    /**
     * Papar form create
     */
    public function create()
    {
        return view('laporanmesyuarat.create');
    }

    /**
     * Simpan laporan mesyuarat baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mesyuarat' => 'required|string|max:255',
            'isu' => 'required|string|max:1000',
            'tarikh_mesyuarat' => 'required|date',
            'status' => 'required|string|max:100',
            'pandangan' => 'required|in:Lisan,Bertulis',
        ]);

        $user = Auth::user();
        $validated['user_id'] = $user->id;
        $validated['negeri'] = $user->negeri;

        // Simpan laporan mesyuarat
        $laporan = LaporanMesyuarat::create($validated);

        // Tambah pergerakan automatik (semua field wajib)
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

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat & pergerakan berjaya disimpan.');
    }

    /**
     * Papar form edit
     */
    public function edit($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        return view('laporanmesyuarat.edit', compact('laporan'));
    }

    /**
     * Update laporan mesyuarat
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
            abort(403);
        }

        $laporan->update($validated);

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dikemaskini.');
    }

    /**
     * Hapus laporan mesyuarat
     */
    public function destroy($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->delete();

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dipadam.');
    }

    /**
     * Tentukan jika user boleh edit/padam
     */
    protected function canEdit(LaporanMesyuarat $laporan)
    {
        $user = Auth::user();
        return ($user->role === 'pa' && $user->negeri === $laporan->negeri)
            || ($laporan->user_id === $user->id);
    }

    // ====================================================================
    // 🔥 FUNGSI DRILL-DOWN BARU (PECAHAN BULAN) 🔥
    // ====================================================================
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

        // Menggunakan column 'pandangan' untuk membezakan Lisan/Bertulis
        $colKategori = 'pandangan'; 
        $standardList = ['Lisan', 'Bertulis'];

        // QUERY DATABASE
        try {
            $dbData = LaporanMesyuarat::select($colKategori, DB::raw('count(*) as total'))
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->groupBy($colKategori)
                ->pluck('total', $colKategori)
                ->toArray();
        } catch (\Exception $e) {
            $dbData = [];
        }

        // GABUNGKAN DATA
        $dataPecahan = collect();
        $labels = [];
        $totals = [];

        foreach ($standardList as $item) {
            $count = $dbData[$item] ?? 0;

            $dataPecahan->push((object)[
                'kategori' => $item, 
                'total' => $count
            ]);

            $labels[] = $item;
            $totals[] = $count;
        }

        return view('laporanmesyuarat.pecahan', compact(
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan'
        ));
    }
}