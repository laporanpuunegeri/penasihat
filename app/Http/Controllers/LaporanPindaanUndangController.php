<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPindaanUndang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LaporanpindaanundangController extends Controller
{
    /**
     * Papar senarai semua laporan dengan tapisan bulan & peranan
     * DEFAULT: Bulan Semasa
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanPindaanUndang::query();

        // 1. Filter Ikut Peranan (Role)
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // 2. Filter Ikut Bulan (Logic Baru)
        // Ambil input 'bulan'. Jika kosong, guna bulan semasa (date('n')).
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = date('Y');

        if ($bulanPilihan !== 'all') {
            // Tapis ikut bulan yang dipilih (atau default bulan semasa)
            $query->whereMonth('created_at', $bulanPilihan)
                  ->whereYear('created_at', $tahunSemasa);
        }

        // 3. Susunan Data
        $data = $query->orderBy('created_at', 'desc')->get();

        return view('laporanpindaanundang.index', compact('data', 'user'));
    }

    /**
     * Papar borang daftar laporan baru
     */
    public function create()
    {
        return view('laporanpindaanundang.create');
    }

    /**
     * Simpan laporan baru
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
     * Papar borang sunting laporan
     */
    public function edit($id)
    {
        $laporan = LaporanPindaanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        return view('laporanpindaanundang.edit', compact('laporan'));
    }

    /**
     * Kemaskini laporan sedia ada
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
            abort(403);
        }

        $laporan->update($validated);

        return redirect()->route('laporanpindaanundang.index')
                         ->with('success', 'Laporan berjaya dikemaskini.');
    }

    /**
     * Padam laporan
     */
    public function destroy($id)
    {
        $laporan = LaporanPindaanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->delete();

        return redirect()->route('laporanpindaanundang.index')
                         ->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Tentukan hak akses semasa untuk edit / padam
     */
    protected function canEdit(LaporanPindaanUndang $laporan)
    {
        $user = auth()->user();

        return ($user->role === 'pa' && $user->negeri === $laporan->negeri)
            || ($laporan->user_id === $user->id);
    }

    /**
     * --- FUNGSI DRILL-DOWN: PINDAAN UNDANG-UNDANG ---
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = Carbon::create()->month($bulan)->format('F');

        // --- SETTING: NAMA COLUMN DATABASE ---
        $colKategori = 'tajuk'; 

        // 1. Ambil Data Pecahan
        $dataPecahan = LaporanPindaanUndang::select($colKategori, \Illuminate\Support\Facades\DB::raw('count(*) as total'))
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