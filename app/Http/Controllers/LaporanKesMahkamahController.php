<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKesMahkamah;
use App\Models\Pergerakan;
use Illuminate\Support\Facades\Auth;

class LaporankesmahkamahController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanKesMahkamah::query();

        // Paparan ikut peranan
        if ($user->role === 'pa' || $user->role === 'yb') {
            // PA & YB boleh lihat semua laporan dalam negeri
            $query->where('negeri', $user->negeri);
        } else {
            // User biasa hanya lihat laporan sendiri
            $query->where('user_id', $user->id);
        }

        // Tapisan bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tarikh_sebutan', $request->bulan)
                  ->whereYear('tarikh_sebutan', now()->year);
        }

        $data = $query->orderBy('tarikh_sebutan', 'desc')->get();

        return view('laporankesmahkamah.index', compact('data'));
    }

    public function create()
    {
        return view('laporankesmahkamah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_kes' => 'required|string',
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required|string',
            'isu' => 'required|string',
            'skop_tugas' => 'required|string',
            'ringkasan_hujahan' => 'required|string',
            'status' => 'required|string',
        ]);

        $user = Auth::user();

        $validated['user_id'] = $user->id;
        $validated['negeri'] = $user->negeri;

        $laporan = LaporanKesMahkamah::create($validated);

        // ✅ Simpan ke jadual pergerakan
        Pergerakan::create([
            'user_id' => $user->id,
            'negeri' => $user->negeri,
            'jenis' => $validated['jenis_kes'],
            'catatan' => $validated['fakta_ringkas'],
            'tarikh' => $validated['tarikh_sebutan'],
        ]);

        return redirect()->route('laporankesmahkamah.index')
                         ->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);

        // Sekat pengguna yang tiada hak
        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        return view('laporankesmahkamah.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_kes' => 'required|string',
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required|string',
            'isu' => 'required|string',
            'skop_tugas' => 'required|string',
            'ringkasan_hujahan' => 'required|string',
            'status' => 'required|string',
        ]);

        $laporan = LaporanKesMahkamah::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->update($validated);

        return redirect()->route('laporankesmahkamah.index')
                         ->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);

        if (! $this->canEdit($laporan)) {
            abort(403);
        }

        $laporan->delete();

        return redirect()->route('laporankesmahkamah.index')
                         ->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * Semakan sama ada pengguna semasa boleh ubah laporan ini.
     */
    protected function canEdit(LaporanKesMahkamah $laporan)
    {
        $user = Auth::user();

        return $user->role === 'pa' && $user->negeri === $laporan->negeri
            || $laporan->user_id === $user->id;
    }
}
