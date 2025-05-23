<?php

namespace App\Http\Controllers;

use App\Models\Pergerakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PergerakanController extends Controller
{
    /**
     * Papar kalendar pergerakan:
     * - Boss/YB boleh tapis ikut pegawai.
     * - User biasa hanya lihat sendiri.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $senarai_pegawai = [];

        if (in_array($user->role, ['boss', 'yb'])) {
            $query = Pergerakan::with('user');

            if ($request->filled('pegawai_id')) {
                $query->where('user_id', $request->pegawai_id);
            }

            $pergerakan = $query->get()->map(function ($item) {
                return [
                    'title'   => $item->jenis . ' - ' . ($item->user->name ?? 'Tanpa Nama'),
                    'start'   => $item->tarikh,
                    'end'     => $item->tarikh,
                    'catatan' => $item->catatan ?? '-',
                ];
            });
$senarai_pegawai = User::where('role', 'user')
    ->where('name', '!=', 'Super Admin') // atau tapis ikut ID
    ->get();


        } else {
            // Hanya papar pergerakan sendiri
            $pergerakan = Pergerakan::where('user_id', $user->id)->get()->map(function ($item) {
                return [
                    'title'   => $item->jenis,
                    'start'   => $item->tarikh,
                    'end'     => $item->tarikh,
                    'catatan' => $item->catatan ?? '-',
                ];
            });
        }

        return view('pergerakan.index', [
            'pergerakan' => $pergerakan,
            'senarai_pegawai' => $senarai_pegawai,
        ]);
    }

    /**
     * Papar borang daftar pergerakan.
     */
    public function create()
    {
        return view('pergerakan.create');
    }

    /**
     * Simpan pergerakan ke dalam pangkalan data.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tarikh'  => 'required|date',
            'jenis'   => 'required|string|max:255',
            'catatan' => 'nullable|string|max:1000',
        ]);

        Pergerakan::create([
            'user_id' => Auth::id(),
            'tarikh'  => $request->tarikh,
            'jenis'   => $request->jenis,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('pergerakan.index')
                         ->with('success', 'Pergerakan berjaya didaftarkan.');
    }
}
