<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKesMahkamah;

class LaporanKesMahkamahController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanKesMahkamah::query();

        // Tapisan ikut bulan tarikh daftar
        if ($request->filled('bulan')) {
            $query->whereMonth('tarikh_daftar', $request->bulan)
                  ->whereYear('tarikh_daftar', now()->year);
        }

        // Susunan ikut tarikh daftar (terbaru dahulu)
        $data = $query->orderBy('tarikh_daftar', 'desc')->get();

        return view('laporankesmahkamah.index', compact('data'));
    }

    public function create()
    {
        return view('laporankesmahkamah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_kes' => 'required',
            'tarikh_daftar' => 'required|date',
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required',
            'isu' => 'required',
            'skop_tugas' => 'required',
            'ringkasan_hujahan' => 'required',
            'status' => 'required',
        ]);

        LaporanKesMahkamah::create($validated);

        return redirect()->route('laporankesmahkamah.index')->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);
        return view('laporankesmahkamah.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_kes' => 'required',
            'tarikh_daftar' => 'required|date',
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required',
            'isu' => 'required',
            'skop_tugas' => 'required',
            'ringkasan_hujahan' => 'required',
            'status' => 'required',
        ]);

        $laporan = LaporanKesMahkamah::findOrFail($id);
        $laporan->update($validated);

        return redirect()->route('laporankesmahkamah.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporankesmahkamah.index')->with('success', 'Laporan berjaya dipadam.');
    }
}
