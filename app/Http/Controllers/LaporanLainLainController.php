<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LainLainTugasan;

class LaporanLainLainController extends Controller
{
    public function index(Request $request)
    {
        $query = LainLainTugasan::query();

        // ✅ Tapis ikut user semasa
        $query->where('user_id', auth()->id());

        // ✅ Tapis ikut bulan & tahun jika dipilih
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->latest()->get();

        return view('lainlaintugasan.index', compact('data'));
    }

    public function create()
    {
        return view('lainlaintugasan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tugasan.*.perihal' => 'required|string',
            'tugasan.*.tarikh' => 'required|date',
            'tugasan.*.tindakan' => 'nullable|string',
        ]);

        $data = $request->input('tugasan');

        foreach ($data as $item) {
            LainLainTugasan::create([
                'perihal' => $item['perihal'],
                'tarikh' => $item['tarikh'],
                'tindakan' => $item['tindakan'],
                'user_id' => auth()->id(),
                'negeri' => auth()->user()->negeri,
            ]);
        }

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dihantar.');
    }

    public function edit($id)
    {
        $data = LainLainTugasan::findOrFail($id);
        return view('lainlaintugasan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        $request->validate([
            'perihal' => 'required|string',
            'tarikh' => 'required|date',
            'tindakan' => 'nullable|string',
        ]);

        $tugasan->update($request->only('perihal', 'tarikh', 'tindakan'));

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);
        $tugasan->delete();

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dipadam.');
    }
}
