<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LainLainTugasan;
use Illuminate\Support\Facades\Auth;

class LaporanLainLainController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LainLainTugasan::query();

        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->latest()->get();

        return view('lainlaintugasan.index', compact('data', 'user'));
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

        $tugasans = $request->input('tugasan');
        $user = Auth::user();

        foreach ($tugasans as $item) {
            LainLainTugasan::create([
                'perihal' => $item['perihal'],
                'tarikh' => $item['tarikh'],
                'tindakan' => $item['tindakan'] ?? null,
                'user_id' => $user->id,
                'negeri' => $user->negeri,
            ]);
        }

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dihantar.');
    }

    public function edit($id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        if (! $this->canEdit($tugasan)) {
            abort(403);
        }

        return view('lainlaintugasan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $tugasan = LainLainTugasan::findOrFail($id);

        if (! $this->canEdit($tugasan)) {
            abort(403);
        }

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

        if (! $this->canEdit($tugasan)) {
            abort(403);
        }

        $tugasan->delete();

        return redirect()->route('lainlaintugasan.index')->with('success', 'Laporan berjaya dipadam.');
    }

    protected function canEdit(LainLainTugasan $tugasan)
    {
        $user = Auth::user();
        return $user->role === 'pa' && $user->negeri === $tugasan->negeri
            || $user->id === $tugasan->user_id;
    }
}
