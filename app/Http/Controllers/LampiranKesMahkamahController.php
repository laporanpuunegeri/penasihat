<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Tambah ini untuk Transaction
use App\Models\LampiranKesMahkamah;

class LampiranKesMahkamahController extends Controller
{
    // Senarai kategori yang tetap (supaya konsisten)
    private $kategori_list = [
        'Perlembagaan', 
        'Tanah / PBT', 
        'Rujukan tanah',
        'Undang-Undang Pentadbiran / Perkhidmatan', 
        'Kemalangan',
        'Perjanjian / Penswastaan', 
        'Pendakwaan', 
        'Lain-lain',
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Pastikan role user betul (PA sahaja)
            if (auth()->user()->role !== 'pa') {
                abort(403, 'Akses terhad kepada PA sahaja.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Default kepada bulan/tahun semasa jika tiada pilihan
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Ambil data dan susun ikut 'kategori' sebagai key untuk mudah dipanggil di View
        $lampiran = LampiranKesMahkamah::where('user_id', $user->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('kategori');

        return view('lampiran.index', [
            'lampiran'      => $lampiran,
            'kategori_list' => $this->kategori_list,
            'bulan'         => $bulan,
            'tahun'         => $tahun
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        
        // Ambil data dari form (Input name mesti array: data[Kategori][field])
        $dataInput = $request->input('data', []);

        // Guna Transaction supaya data selamat (Atomicity)
        DB::transaction(function () use ($user, $bulan, $tahun, $dataInput) {
            
            // 1. Padam rekod lama untuk bulan/tahun tersebut (elak duplikasi)
            LampiranKesMahkamah::where('user_id', $user->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->delete();

            // 2. Simpan rekod baru
            foreach ($dataInput as $kategori => $val) {
                
                // Pastikan input nombor adalah integer (tukar kosong jadi 0)
                LampiranKesMahkamah::create([
                    'user_id'   => $user->id,
                    'negeri'    => $user->negeri,
                    'kategori'  => $kategori,
                    'bil_aktif' => intval($val['bil_aktif'] ?? 0),
                    'majistret' => intval($val['majistret'] ?? 0),
                    'sesi'      => intval($val['sesi'] ?? 0),
                    'tinggi'    => intval($val['tinggi'] ?? 0),
                    'rayuan'    => intval($val['rayuan'] ?? 0),
                    'persk'     => intval($val['persk'] ?? 0),
                    'status'    => $val['status'] ?? '-',
                    'bulan'     => $bulan,
                    'tahun'     => $tahun,
                ]);
            }
        });

        return redirect()->route('lampiran.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Lampiran II berjaya disimpan.');
    }
}