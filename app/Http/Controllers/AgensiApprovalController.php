<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgensiUser;

class AgensiApprovalController extends Controller
{
    // Cuma paparkan yang PENDING sahaja
    public function index()
    {
        // Safety: Super Admin sahaja
        if(auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Akses tidak dibenarkan.');
        }

        $senaraiPending = AgensiUser::where('status', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('tetapan.kelulusan.index', compact('senaraiPending'));
    }

    public function approve($id)
    {
        $user = AgensiUser::findOrFail($id);
        $user->update(['status' => 'aktif']);
        return back()->with('success', 'Permohonan agensi telah DILULUSKAN. Pengguna kini boleh log masuk.');
    }

    public function reject($id)
    {
        $user = AgensiUser::findOrFail($id);
        $user->update(['status' => 'tolak']);
        return back()->with('success', 'Permohonan agensi telah DITOLAK.');
    }
}