<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPindaanUndang;
use Illuminate\Support\Facades\Log;

class LaporanpindaanundangController extends Controller
{
    /**
     * Papar senarai semua laporan dengan tapisan tindakan jika ada
     */
    public function index(Request $request)
    {
        $query = LaporanPindaanUndang::query();

        // Tapisan ikut tindakan sahaja
        if ($request->filled('tindakan')) {
            $query->where('tindakan', $request->tindakan);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('laporanpindaanundang.index', compact('data'));
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
            LaporanPindaanUndang::create($validated);

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
        $laporan->delete();

        return redirect()->route('laporanpindaanundang.index')
                         ->with('success', 'Laporan berjaya dipadam.');
    }
}
