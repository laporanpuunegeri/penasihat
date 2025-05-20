<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPandanganUndang;
use Illuminate\Support\Facades\Auth;

class LaporanPandanganUndangController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanPandanganUndang::query();

        // Tapisan ikut bulan sahaja berdasarkan created_at
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan)
                  ->whereYear('created_at', now()->year);
        }

        $data = $query->latest()->get();

        return view('laporanpandanganundang.index', compact('data'));
    }

    public function create()
    {
        return view('laporanpandanganundang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'isu' => 'required|string',
            'tarikh_terima' => 'required|date',
            'fakta_ringkasan' => 'required|string',
            'isu_detail' => 'required|string',
            'ringkasan_pandangan' => 'required|string',
            'jenis_pandangan' => 'nullable|string',
            'status' => 'required|string',
            'tarikh_selesai' => 'nullable|date',
            'belum_selesai' => 'nullable|boolean',
            'dirujuk_jpn' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $bossId = null;

        if ($user->role === 'pa' && $user->boss_id) {
            $bossId = $user->boss_id;
        } elseif ($user->role === 'user' && $request->has('hantar_kepada_boss')) {
            $bossId = $user->boss_id;
        }

        LaporanPandanganUndang::create([
            'kategori' => $request->kategori,
            'isu' => $request->isu,
            'tarikh_terima' => $request->tarikh_terima,
            'fakta_ringkasan' => $request->fakta_ringkasan,
            'isu_detail' => $request->isu_detail,
            'ringkasan_pandangan' => $request->ringkasan_pandangan,
            'jenis_pandangan' => $request->jenis_pandangan,
            'status' => $request->status,
            'tarikh_selesai' => $request->belum_selesai ? null : $request->tarikh_selesai,
            'belum_selesai' => $request->has('belum_selesai'),
            'dirujuk_jpn' => $request->has('dirujuk_jpn'),
            'created_by' => $user->id,
            'boss_id' => $bossId,
        ]);

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanPandanganUndang::findOrFail($id);
        return view('laporanpandanganundang.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'isu' => 'required|string',
            'tarikh_terima' => 'required|date',
            'fakta_ringkasan' => 'required|string',
            'isu_detail' => 'required|string',
            'ringkasan_pandangan' => 'required|string',
            'jenis_pandangan' => 'nullable|string',
            'status' => 'required|string',
            'tarikh_selesai' => 'nullable|date',
            'belum_selesai' => 'nullable|boolean',
            'dirujuk_jpn' => 'nullable|boolean',
        ]);

        $laporan = LaporanPandanganUndang::findOrFail($id);
        $user = Auth::user();

        $bossId = $laporan->boss_id;
        if ($user->role === 'pa' && $user->boss_id) {
            $bossId = $user->boss_id;
        } elseif ($user->role === 'user' && $request->has('hantar_kepada_boss')) {
            $bossId = $user->boss_id;
        }

        $laporan->update([
            'isu' => $request->isu,
            'tarikh_terima' => $request->tarikh_terima,
            'fakta_ringkasan' => $request->fakta_ringkasan,
            'isu_detail' => $request->isu_detail,
            'ringkasan_pandangan' => $request->ringkasan_pandangan,
            'jenis_pandangan' => $request->jenis_pandangan,
            'status' => $request->status,
            'tarikh_selesai' => $request->belum_selesai ? null : $request->tarikh_selesai,
            'belum_selesai' => $request->has('belum_selesai'),
            'dirujuk_jpn' => $request->has('dirujuk_jpn'),
            'boss_id' => $bossId,
        ]);

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanPandanganUndang::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya dipadam.');
    }
}
