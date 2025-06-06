<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lainlaintugasan;

class LaporanLainLainController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');

        $query = Lainlaintugasan::query();

        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
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
        $data = $request->input('tugasan');

        foreach ($data as $item) {
            Lainlaintugasan::create([
                'perihal' => $item['perihal'],
                'tarikh' => $item['tarikh'],
                'tindakan' => $item['tindakan'],
            ]);
        }

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dihantar.');
    }

    public function edit($id)
    {
        $data = Lainlaintugasan::findOrFail($id);
        return view('lainlaintugasan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $tugasan = Lainlaintugasan::findOrFail($id);
        $tugasan->update($request->only('perihal', 'tarikh', 'tindakan'));

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $tugasan = Lainlaintugasan::findOrFail($id);
        $tugasan->delete();

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dipadam.');
    }
}
