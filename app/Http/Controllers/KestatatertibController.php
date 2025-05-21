<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kestatatertib;

class KestatatertibController extends Controller
{
    public function index(Request $request)
    {
        $query = Kestatatertib::query();

        // Hanya ambil data milik pengguna log masuk
        $query->where('user_id', auth()->id());

        // Tapisan ikut bulan berdasarkan created_at
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('kestatatertib.index', compact('data'));
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
        return view('kestatatertib.edit', ['laporan' => $kestatatertib]);
    }

    public function update(Request $request, Kestatatertib $kestatatertib)
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

        $kestatatertib->update($validated);

        return redirect()->route('kestatatertib.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy(Kestatatertib $kestatatertib)
    {
        $kestatatertib->delete();

        return back()->with('success', 'Laporan berjaya dipadam.');
    }
}
