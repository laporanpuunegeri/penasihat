<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanMesyuarat;

class LaporanmesyuaratController extends Controller
{
    /**
     * Papar senarai laporan mesyuarat dengan tapisan bulan & status (berdasarkan user login)
     */
    public function index(Request $request)
    {
        $query = LaporanMesyuarat::query();

        // ✅ Tapis ikut user semasa
        $query->where('user_id', auth()->id());

        // Tapisan ikut bulan & tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('tarikh_mesyuarat', 'desc')->get();

        return view('laporanmesyuarat.index', compact('data'));
    }

    /**
     * Papar borang daftar laporan baru
     */
    public function create()
    {
        return view('laporanmesyuarat.create');
    }

    /**
     * Simpan laporan mesyuarat baharu
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

        // ✅ Tambah maklumat user
        $validated['user_id'] = auth()->id();
        $validated['negeri'] = auth()->user()->negeri;

        LaporanMesyuarat::create($validated);

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya disimpan.');
    }

    /**
     * Papar borang kemaskini laporan
     */
    public function edit($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);
        return view('laporanmesyuarat.edit', compact('laporan'));
    }

    /**
     * Simpan kemaskini laporan mesyuarat
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
        $laporan->update($validated);

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dikemaskini.');
    }

    /**
     * Padam laporan dari pangkalan data
     */
    public function destroy($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dipadam.');
    }
}
