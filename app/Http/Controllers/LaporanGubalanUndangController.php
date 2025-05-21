<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanGubalanUndang;

class LaporanGubalanUndangController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanGubalanUndang::query();

        // Papar data milik pengguna log masuk sahaja
        $query->where('user_id', auth()->id());

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->latest()->get();

        return view('laporangubalanundang.index', compact('data'));
    }

    public function create()
    {
        return view('laporangubalanundang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string',
            'tindakan' => 'required|string',
            'status' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['negeri'] = auth()->user()->negeri;

        LaporanGubalanUndang::create($validated);

        return redirect('/laporangubalanundang')->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanGubalanUndang::findOrFail($id);
        return view('laporangubalanundang.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string',
            'tindakan' => 'required|string',
            'status' => 'required|string',
        ]);

        $laporan = LaporanGubalanUndang::findOrFail($id);
        $laporan->update($validated);

        return redirect('/laporangubalanundang')->with('success', 'Laporan berjaya dikemas kini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanGubalanUndang::findOrFail($id);
        $laporan->delete();

        return redirect('/laporangubalanundang')->with('success', 'Laporan berjaya dipadam.');
    }
}
