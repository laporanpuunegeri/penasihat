<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agensi;
use Illuminate\Support\Facades\Auth;

class AgensiController extends Controller
{
    // 1. SENARAI AGENSI
    public function index()
    {
        $user = Auth::user();

        // Papar agensi ikut negeri user sahaja (supaya tak bercampur negeri lain)
        $agensis = Agensi::where('negeri', $user->negeri)
                         ->orWhere('negeri', 'Persekutuan') // Optional
                         ->orderBy('nama_agensi', 'ASC')
                         ->get();

        return view('agensi.index', compact('agensis'));
    }

    // 2. SIMPAN AGENSI BARU
    public function store(Request $request)
    {
        $request->validate([
            'nama_agensi' => 'required|string|unique:agensis,nama_agensi',
        ]);

        Agensi::create([
            'nama_agensi' => $request->nama_agensi,
            'negeri' => Auth::user()->negeri, // Auto set ikut negeri PA
        ]);

        return redirect()->back()->with('success', 'Agensi berjaya ditambah.');
    }

    // 3. PADAM AGENSI
    public function destroy($id)
    {
        $agensi = Agensi::findOrFail($id);
        $agensi->delete();

        return redirect()->back()->with('success', 'Agensi berjaya dipadam.');
    }
}