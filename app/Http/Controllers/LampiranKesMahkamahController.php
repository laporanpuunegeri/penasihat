<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LampiranKesMahkamah;

class LampiranKesMahkamahController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'pa') {
                abort(403, 'Akses terhad kepada PA sahaja.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $lampiran = LampiranKesMahkamah::where('user_id', $user->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('kategori');

        $kategori_list = [
            'Perlembagaan', 'Tanah / PBT', 'Rujukan tanah',
            'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan',
            'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain',
        ];

        return view('lampiran.index', compact('lampiran', 'kategori_list', 'bulan', 'tahun'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $data = $request->input('data', []);

        LampiranKesMahkamah::where('user_id', $user->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->delete();

        foreach ($data as $kategori => $val) {
            LampiranKesMahkamah::create([
                'user_id'   => $user->id,
                'negeri'    => $user->negeri,
                'kategori'  => $kategori,
                'bil_aktif' => $val['bil_aktif'] ?? 0,
                'majistret' => $val['majistret'] ?? 0,
                'sesi'      => $val['sesi'] ?? 0,
                'tinggi'    => $val['tinggi'] ?? 0,
                'rayuan'    => $val['rayuan'] ?? 0,
                'persk'     => $val['persk'] ?? 0,
                'status'    => $val['status'] ?? '-',
                'bulan'     => $bulan,
                'tahun'     => $tahun,
            ]);
        }

        return redirect()->route('lampiran.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Lampiran II berjaya disimpan.');
    }
}
