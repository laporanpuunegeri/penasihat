<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKesMahkamah;

class LaporankesmahkamahController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanKesMahkamah::query();

        // Tapis ikut pengguna log masuk
        $query->where('user_id', auth()->id());

        if ($request->filled('bulan')) {
            $query->whereMonth('tarikh_sebutan', $request->bulan)
                  ->whereYear('tarikh_sebutan', now()->year);
        }

        $data = $query->orderBy('tarikh_sebutan', 'desc')->get();

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
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required',
            'isu' => 'required',
            'skop_tugas' => 'required',
            'ringkasan_hujahan' => 'required',
            'status' => 'required',
        ]);

        // Tambah user_id dan negeri
        $validated['user_id'] = auth()->id();
        $validated['negeri'] = auth()->user()->negeri;

        LaporanKesMahkamah::create($validated);

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya disimpan.');
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
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required',
            'isu' => 'required',
            'skop_tugas' => 'required',
            'ringkasan_hujahan' => 'required',
            'status' => 'required',
        ]);

        $laporan = LaporanKesMahkamah::findOrFail($id);
        $laporan->update($validated);

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);
        $laporan->delete();

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya dipadam.');
    }
}
