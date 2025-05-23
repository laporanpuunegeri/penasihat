<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanGubalanUndang;
use Illuminate\Support\Facades\Auth;

class LaporanGubalanUndangController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanGubalanUndang::query();

        // Paparan ikut peranan
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // Tapis bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->latest()->get();

        return view('laporangubalanundang.index', compact('data', 'user'));
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

        $user = Auth::user();

        $validated['user_id'] = $user->id;
        $validated['negeri'] = $user->negeri;

        LaporanGubalanUndang::create($validated);

        return redirect('/laporangubalanundang')->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanGubalanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

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

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->update($validated);

        return redirect('/laporangubalanundang')->with('success', 'Laporan berjaya dikemas kini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanGubalanUndang::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->delete();

        return redirect('/laporangubalanundang')->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Tentukan jika pengguna semasa boleh ubah/padam laporan.
     */
    protected function canEdit(LaporanGubalanUndang $laporan)
    {
        $user = Auth::user();
        return $user->role === 'pa' && $user->negeri === $laporan->negeri
            || $user->id === $laporan->user_id;
