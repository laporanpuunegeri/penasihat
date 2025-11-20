<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kestatatertib;
use Illuminate\Support\Facades\Auth;

class KestatatertibController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kestatatertib::query();

        // ✅ Tapis ikut peranan
        if ($user->role === 'yb' || $user->role === 'pa') {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // ✅ Tapisan ikut bulan (tarikh daftar)
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('kestatatertib.index', compact('data', 'user'));
    }

    public function create()
    {
        return view('kestatatertib.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarikh_terima' => 'required|date',
            'kategori' => 'required|string|max:255',
            'fakta_ringkasan' => 'nullable|string',
            'isu' => 'nullable|string',
            'ringkasan_pandangan' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'tarikh_selesai' => 'nullable|date',
        ]);

        $validated['hantar_kepada_boss'] = $request->has('hantar_kepada_boss');
        $validated['tarikh_daftar'] = now();
        $validated['user_id'] = auth()->id();
        $validated['negeri'] = auth()->user()->negeri;

        Kestatatertib::create($validated);

        return redirect()->route('kestatatertib.index')->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit(Kestatatertib $kestatatertib)
    {
        if (! $this->canEdit($kestatatertib)) {
            abort(403);
        }

        return view('kestatatertib.edit', ['laporan' => $kestatatertib]);
    }

    public function update(Request $request, Kestatatertib $kestatatertib)
    {
        if (! $this->canEdit($kestatatertib)) {
            abort(403);
        }

        $validated = $request->validate([
            'tarikh_terima' => 'required|date',
            'kategori' => 'required|string|max:255',
            'fakta_ringkasan' => 'nullable|string',
            'isu' => 'nullable|string',
            'ringkasan_pandangan' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'tarikh_selesai' => 'nullable|date',
        ]);

        $validated['hantar_kepada_boss'] = $request->has('hantar_kepada_boss');

        $kestatatertib->update($validated);

        return redirect()->route('kestatatertib.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy(Kestatatertib $kestatatertib)
    {
        if (! $this->canEdit($kestatatertib)) {
            abort(403);
        }

        $kestatatertib->delete();

        return back()->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Tentukan jika pengguna semasa boleh sunting atau padam
     */
    protected function canEdit(Kestatatertib $laporan)
    {
        $user = auth()->user();
        return $user->role === 'pa' && $user->negeri === $laporan->negeri
            || $laporan->user_id === $user->id;
    }
}
