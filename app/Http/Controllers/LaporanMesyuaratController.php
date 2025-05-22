<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanMesyuarat;
use App\Models\Pergerakan;

class LaporanmesyuaratController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanMesyuarat::query();

        $query->where('user_id', auth()->id());

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

    public function create()
    {
        return view('laporanmesyuarat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mesyuarat' => 'required|string|max:255',
            'isu' => 'required|string|max:1000',
            'tarikh_mesyuarat' => 'required|date',
            'status' => 'required|string|max:100',
            'pandangan' => 'required|in:Lisan,Bertulis',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['negeri'] = auth()->user()->negeri;

        // Simpan laporan mesyuarat
        $laporan = LaporanMesyuarat::create($validated);

        // ✅ Automatik tambah ke pergerakan
        Pergerakan::create([
            'user_id' => auth()->id(),
            'tarikh' => $validated['tarikh_mesyuarat'],
            'jenis' => 'Mesyuarat',
            'catatan' => $validated['mesyuarat'],
        ]);

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat & pergerakan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);
        return view('laporanmesyuarat.edit', compact('laporan'));
    }

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

    public function destroy($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dipadam.');
    }
}
