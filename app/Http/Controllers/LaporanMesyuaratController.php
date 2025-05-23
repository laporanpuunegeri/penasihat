<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanMesyuarat;
use App\Models\Pergerakan;
use Illuminate\Support\Facades\Auth;

class LaporanmesyuaratController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanMesyuarat::query();

        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('tarikh_mesyuarat', 'desc')->get();

        return view('laporanmesyuarat.index', compact('data', 'user'));
    }

    public function create()
    {
        return view('laporanmesyuarat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mesyuarat' => 'required|string|max:255',
            'isu' => 'required|string|max:1000',
            'tarikh_mesyuarat' => 'required|date',
            'status' => 'required|string|max:100',
            'pandangan' => 'required|in:Lisan,Bertulis',
        ]);

        $user = Auth::user();

        $validated['user_id'] = $user->id;
        $validated['negeri'] = $user->negeri;

        $laporan = LaporanMesyuarat::create($validated);

        // ✅ Tambah pergerakan automatik
        Pergerakan::create([
            'user_id' => $user->id,
            'tarikh' => $validated['tarikh_mesyuarat'],
            'jenis' => 'Mesyuarat',
            'catatan' => $validated['mesyuarat'],
            'negeri' => $user->negeri,
        ]);

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat & pergerakan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        return view('laporanmesyuarat.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mesyuarat' => 'required|string|max:255',
            'isu' => 'required|string|max:1000',
            'tarikh_mesyuarat' => 'required|date',
            'status' => 'required|string|max:100',
            'pandangan' => 'required|in:Lisan,Bertulis',
        ]);

        $laporan = LaporanMesyuarat::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->update($validated);

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanMesyuarat::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->delete();

        return redirect()->route('laporanmesyuarat.index')
                         ->with('success', 'Laporan mesyuarat berjaya dipadam.');
    }

    /**
     * Tentukan jika pengguna boleh edit/padam
     */
    protected function canEdit(LaporanMesyuarat $laporan)
    {
        $user = Auth::user();
        return $user->role === 'pa' && $user->negeri === $laporan->negeri
            || $laporan->user_id === $user->id;
    }
}
