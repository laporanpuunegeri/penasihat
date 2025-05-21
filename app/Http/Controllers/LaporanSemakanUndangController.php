<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanSemakanUndang;
use Illuminate\Support\Facades\Auth;

class LaporansemakanundangController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanSemakanUndang::query();

        // ✅ Tapis ikut user semasa
        $query->where('user_id', auth()->id());

        // ✅ Tapisan ikut bulan tarikh daftar
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('laporansemakanundang.index', compact('data'));
    }

    public function create()
    {
        return view('laporansemakanundang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string|max:500',
            'tindakan' => 'required|string|max:1000',
            'status' => 'required|string|max:100',
        ]);

        LaporanSemakanUndang::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'negeri' => auth()->user()->negeri,
        ]));

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan semakan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanSemakanUndang::findOrFail($id);
        return view('laporansemakanundang.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tajuk' => 'required|string|max:500',
            'tindakan' => 'required|string|max:1000',
            'status' => 'required|string|max:100',
        ]);

        $laporan = LaporanSemakanUndang::findOrFail($id);
        $laporan->update($validated);

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanSemakanUndang::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan berjaya dipadam.');
    }
}
