<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kestatatertib;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KestatatertibController extends Controller
{
    /**
     * INDEX: Senarai Laporan (Kekal Ketat mengikut user_id, melainkan YB/PA)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kestatatertib::query();

        // 1. Filter Ikut Peranan
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } elseif ($user->role === 'super_admin') {
            // Super Admin melihat SEMUA data (tiada filter)
        } else {
            // User biasa nampak rekod sendiri sahaja
            $query->where('user_id', $user->id); 
        }

        // 2. Filter Ikut Bulan 
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = date('Y');

        if ($bulanPilihan !== 'all') {
            $query->whereMonth('created_at', $bulanPilihan)
                  ->whereYear('created_at', $tahunSemasa);
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
     * Helper: Check Permission
     */
    protected function canEdit(Kestatatertib $laporan)
    {
        $user = auth()->user();
        return ($user->role === 'pa' && $user->negeri === $laporan->negeri)
            || ($laporan->user_id === $user->id);
    }

    /**
     * FUNGSI DRILL-DOWN: Pecahan Kes Tatatertib Mengikut KATEGORI
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $user = Auth::user();
        
        $namaBulan = Carbon::create()->month($bulan)->format('F');

        // 1. Re-apply Filter Logic (DILONGGARKAN untuk statistik pecahan)
        $query = Kestatatertib::query();
        
        // Filter ikut NEGERI/Wilayah, kecuali Super Admin (yang nampak semua)
        if ($user->role !== 'super_admin') {
            $query->where('negeri', $user->negeri);
        }
        
        // 2. Build Query Pecahan (Guna column 'kategori')
        try {
            // 🔥 PERUBAHAN UTAMA: Guna COALESCE/IFNULL untuk pastikan rekod NULL dikira 🔥
            // COALESCE digunakan untuk PostgreSQL/MySQL untuk menukar NULL kepada 'Tiada Kategori'
            $dataPecahan = $query->select(
                DB::raw("COALESCE(kategori, 'Tiada Kategori') as kategori"),
                DB::raw('count(*) as jumlah')
            )
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy(DB::raw("COALESCE(kategori, 'Tiada Kategori')"))
            ->orderBy('jumlah', 'desc')
            ->get();
            
        } catch (\Exception $e) {
            // Jika terdapat ralat SQL (cth: column 'kategori' tiada), set data kosong
            $dataPecahan = collect(); 
        }
            
        // 3. Kira Jumlah Keseluruhan
        $labels = $dataPecahan->pluck('kategori');
        $totals = $dataPecahan->pluck('jumlah');
        $jumlahKeseluruhan = $dataPecahan->sum('jumlah');

        // 4. Return ke View
        return view('kestatatertib.pecahan', compact(
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan', 
            'jumlahKeseluruhan'
        ));
    }
}