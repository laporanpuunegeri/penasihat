<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanSemakanUndang;
use Illuminate\Support\Facades\Auth;

class LaporansemakanundangController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanSemakanUndang::query();

        // ✅ Paparan ikut peranan
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // ✅ Tapisan ikut bulan daftar
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('laporansemakanundang.index', compact('data', 'user'));
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

        LaporanSemakanUndang::create([
            'tajuk' => $validated['tajuk'],
            'tindakan' => $validated['tindakan'],
            'status' => $validated['status'],
            'user_id' => auth()->id(),
            'negeri' => auth()->user()->negeri,
        ]);

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan semakan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanSemakanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

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

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->update($validated);

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanSemakanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->delete();

        return redirect()->route('laporansemakanundang.index')
                         ->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Tentukan jika pengguna semasa boleh edit/padam
     */
    protected function canEdit(LaporanSemakanUndang $laporan)
    {
        $user = auth()->user();
        return $user->role === 'pa' && $user->negeri === $laporan->negeri
            || $laporan->user_id === $user->id;
    }
}
