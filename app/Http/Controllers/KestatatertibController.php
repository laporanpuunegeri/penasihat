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
     * 1. INDEX: Senarai Laporan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kestatatertib::query();

        // --- A. FILTER VISIBILITI (LOGIC BARU) ---
        // 1. VIP (YB, PA, Super Admin, Admin) -> Nampak SEMUA (Tiada Filter)
        if (in_array(strtolower($user->role), ['super_admin', 'yb', 'pa', 'admin'])) {
            // Global View: Jangan letak filter
        } 
        // 2. EO -> Nampak Ikut Negeri
        elseif (in_array(strtolower($user->role), ['eo'])) {
            $query->where('negeri', $user->negeri);
        }
        // 3. User Biasa -> Nampak Rekod Sendiri
        else {
            $query->where('user_id', $user->id);
        }

        // --- B. FILTER TARIKH ---
        $bulan = $request->input('bulan', 'all'); 
        $tahun = $request->input('tahun', date('Y'));

        // Filter Tahun
        if ($tahun != 'all') {
            $query->whereYear('tarikh_terima', $tahun);
        }

        // Filter Bulan
        if ($bulan != 'all') {
            $query->whereMonth('tarikh_terima', $bulan);
        }

        $data = $query->orderBy('tarikh_terima', 'desc')->get();

        return view('kestatatertib.index', compact('data', 'user', 'tahun', 'bulan'));
    }

    /**
     * 2. CREATE
     */
    public function create()
    {
        return view('kestatatertib.create');
    }

    /**
     * 3. STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'tarikh_terima' => 'required|date',
            'kategori' => 'required|string',
            'fakta_ringkasan' => 'nullable|string',
            'isu' => 'nullable|string',
            'ringkasan_pandangan' => 'nullable|string',
            'status' => 'nullable|string',
            'tarikh_selesai' => 'nullable|date',
        ]);

        $user = Auth::user();

        Kestatatertib::create([
            'tarikh_terima' => $request->tarikh_terima,
            'kategori' => $request->kategori,
            'fakta_ringkasan' => $request->fakta_ringkasan,
            'isu' => $request->isu,
            'ringkasan_pandangan' => $request->ringkasan_pandangan,
            'status' => $request->status,
            'tarikh_selesai' => $request->tarikh_selesai,
            'hantar_kepada_boss' => $request->has('hantar_kepada_boss'),
            'user_id' => $user->id,
            'negeri' => $user->negeri,
            'created_by' => $user->id,
        ]);

        return redirect()->route('kestatatertib.index')->with('success', 'Kes berjaya direkodkan.');
    }

    /**
     * 4. EDIT
     */
    public function edit($id)
    {
        $laporan = Kestatatertib::findOrFail($id);

        if (! $this->authorizeAction($laporan)) {
            abort(403, 'Tiada kebenaran untuk edit.');
        }

        return view('kestatatertib.edit', compact('laporan'));
    }

    /**
     * 5. UPDATE
     */
    public function update(Request $request, $id)
    {
        $laporan = Kestatatertib::findOrFail($id);

        if (! $this->authorizeAction($laporan)) {
            abort(403, 'Tiada kebenaran untuk kemaskini.');
        }

        $request->validate([
            'tarikh_terima' => 'required|date',
            'kategori' => 'required|string',
            'fakta_ringkasan' => 'nullable|string',
            'isu' => 'nullable|string',
            'ringkasan_pandangan' => 'nullable|string',
            'status' => 'nullable|string',
            'tarikh_selesai' => 'nullable|date',
        ]);

        $laporan->update([
            'tarikh_terima' => $request->tarikh_terima,
            'kategori' => $request->kategori,
            'fakta_ringkasan' => $request->fakta_ringkasan,
            'isu' => $request->isu,
            'ringkasan_pandangan' => $request->ringkasan_pandangan,
            'status' => $request->status,
            'tarikh_selesai' => $request->tarikh_selesai,
            'hantar_kepada_boss' => $request->has('hantar_kepada_boss'),
        ]);

        return redirect()->route('kestatatertib.index')->with('success', 'Kes berjaya dikemaskini.');
    }

    /**
     * 6. DESTROY
     */
    public function destroy($id)
    {
        $laporan = Kestatatertib::findOrFail($id);

        if (! $this->authorizeAction($laporan)) {
            abort(403, 'Tiada kebenaran untuk padam.');
        }

        $laporan->delete();

        return redirect()->route('kestatatertib.index')->with('success', 'Kes berjaya dipadam.');
    }

    /**
     * 7. PECAHAN BULAN (DRILL DOWN DARI DASHBOARD)
     * 🔥 Ini yang penting untuk fix graf kosong & undefined variable 🔥
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $user = auth()->user();

        // Query Asas
        $query = Kestatatertib::query();

        // 1. FILTER VISIBILITI (YB Nampak Semua)
        if (in_array(strtolower($user->role), ['super_admin', 'yb', 'pa', 'admin'])) {
            // Tiada filter
        } elseif (in_array(strtolower($user->role), ['eo'])) {
            $query->where('negeri', $user->negeri);
        } else {
            $query->where('user_id', $user->id);
        }

        // 2. GET DATA SENARAI (Guna created_at)
        $data = $query->whereMonth('created_at', $bulan)
                      ->whereYear('created_at', $tahun)
                      ->orderBy('created_at', 'desc')
                      ->get();

        // 3. KIRA STATISTIK (UNTUK GRAF & TABLE)
        $pecahanKategori = Kestatatertib::query();
        
        // Apply balik filter role
        if (in_array(strtolower($user->role), ['eo'])) {
             $pecahanKategori->where('negeri', $user->negeri);
        } elseif (!in_array(strtolower($user->role), ['super_admin', 'yb', 'pa', 'admin'])) {
             $pecahanKategori->where('user_id', $user->id);
        }

        // 🔥 TUKAR SINI: Guna 'as jumlah' supaya Table boleh baca 🔥
        $dataPecahan = $pecahanKategori->select('kategori', \DB::raw('count(*) as jumlah')) 
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('kategori')
            ->orderBy('jumlah', 'desc')
            ->get();

        $labels = $dataPecahan->pluck('kategori');
        $totals = $dataPecahan->pluck('jumlah'); // Chart guna data dari 'jumlah'
        $jumlahKeseluruhan = $dataPecahan->sum('jumlah'); // Table footer guna 'jumlah'

        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

        return view('kestatatertib.pecahan', compact(
            'data', 'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan', 
            'jumlahKeseluruhan'
        ));
    }

    /**
     * HELPER: Authorize Action
     */
    protected function authorizeAction(Kestatatertib $laporan)
    {
        $user = Auth::user();
        $role = strtolower($user->role);
        
        // 1. Super Admin, PA, YB, Admin boleh buat semua (Global)
        if (in_array($role, ['super_admin', 'pa', 'yb', 'admin'])) {
            return true;
        }
        
        // 2. User asal boleh edit/padam rekod sendiri
        if ($user->id === $laporan->user_id) {
            return true;
        }
        
        return false;
    }
}