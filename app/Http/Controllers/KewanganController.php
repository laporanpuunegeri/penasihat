<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KewanganRecord;

class KewanganController extends Controller
{
    // --- 1. LAPORAN PRESTASI PERBELANJAAN KESELURUHAN (INDEX) ---
    public function index(Request $request)
    {
        if (!Auth::check() || 
            (strtolower(Auth::user()->role) !== 'pa' && 
             strtolower(Auth::user()->role) !== 'yb' && 
             strtolower(Auth::user()->role) !== 'eo' &&
             Auth::user()->bahagian !== 'Bahagian Kewangan')) 
        {
            abort(403, 'Anda tiada kebenaran untuk akses modul ini.');
        }

        $tahun_dipilih = $request->input('tahun', date('Y'));
        $negeri_user   = Auth::user()->negeri; // Ambil negeri user semasa

        $laporan_kewangan = [
            '10000' => ['tajuk' => 'EMOLUMEN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '20000' => ['tajuk' => 'PERKHIDMATAN & BEKALAN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '30000' => ['tajuk' => 'ASET', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '40000' => ['tajuk' => 'PEMBERIAN & KENAAN BAYARAN TETAP', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '50000' => ['tajuk' => 'PERBELANJAAN - PERBELANJAAN LAIN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
        ];

        // === TAPIS DATA IKUT TAHUN DAN NEGERI ===
        $records = KewanganRecord::where('tahun', $tahun_dipilih)
                    ->where('negeri', $negeri_user) // <--- WAJIB ADA
                    ->orderBy('kod_objek', 'asc')
                    ->get();

        foreach ($records as $record) {
            if (array_key_exists($record->kod_utama, $laporan_kewangan)) {
                $laporan_kewangan[$record->kod_utama]['items'][] = [
                    'id'        => $record->id,
                    'kod'       => $record->kod_objek,
                    'butiran'   => $record->butiran,
                    'siling'    => $record->peruntukan,
                    'belanja'   => $record->belanja
                ];
                $laporan_kewangan[$record->kod_utama]['total_peruntukan'] += $record->peruntukan;
                $laporan_kewangan[$record->kod_utama]['total_belanja'] += $record->belanja;
            }
        }

        $grand_total_peruntukan = 0;
        $grand_total_belanja = 0;

        foreach($laporan_kewangan as $main) {
            $grand_total_peruntukan += $main['total_peruntukan'];
            $grand_total_belanja += $main['total_belanja'];
        }

        $grand_total_baki = $grand_total_peruntukan - $grand_total_belanja;
        $grand_peratus = $grand_total_peruntukan > 0 ? ($grand_total_belanja / $grand_total_peruntukan) * 100 : 0;

        return view('kewangan.index', compact(
            'laporan_kewangan', 'grand_total_peruntukan', 'grand_total_belanja', 'grand_total_baki', 'grand_peratus', 'tahun_dipilih'
        ));
    }

    // --- 2. SUKU TAHUN ---
    public function sukuTahun(Request $request)
    {
        $tahun_dipilih = $request->input('tahun', date('Y'));
        $negeri_user   = Auth::user()->negeri;

        $laporan_kewangan = [
            '10000' => ['tajuk' => 'EMOLUMEN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '20000' => ['tajuk' => 'PERKHIDMATAN & BEKALAN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '30000' => ['tajuk' => 'ASET', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '40000' => ['tajuk' => 'PEMBERIAN & KENAAN BAYARAN TETAP', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '50000' => ['tajuk' => 'PERBELANJAAN - PERBELANJAAN LAIN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
        ];

        // === TAPIS IKUT NEGERI JUGA ===
        $records = KewanganRecord::where('tahun', $tahun_dipilih)
                    ->where('negeri', $negeri_user) // <--- WAJIB ADA
                    ->orderBy('kod_objek', 'asc')
                    ->get();

        foreach ($records as $record) {
            if (array_key_exists($record->kod_utama, $laporan_kewangan)) {
                $laporan_kewangan[$record->kod_utama]['items'][] = $record;
                $laporan_kewangan[$record->kod_utama]['total_peruntukan'] += $record->peruntukan;
                $laporan_kewangan[$record->kod_utama]['total_belanja'] += $record->belanja;
            }
        }

        $grand_total_peruntukan = 0;
        $grand_total_belanja = 0;

        foreach($laporan_kewangan as $data) {
            $grand_total_peruntukan += $data['total_peruntukan'];
            $grand_total_belanja += $data['total_belanja'];
        }

        return view('kewangan.suku_tahun', compact(
            'tahun_dipilih', 'laporan_kewangan', 'grand_total_peruntukan', 'grand_total_belanja'
        ));
    }

    // --- 3. PERBANDINGAN ---
    public function perbandingan(Request $request)
    {
        $tahun_semasa = $request->input('tahun', date('Y'));
        $tahun_lepas  = $tahun_semasa - 1;
        $negeri_user  = Auth::user()->negeri;

        $laporan = [
            '10000' => ['tajuk' => 'EMOLUMEN', 'items' => []],
            '20000' => ['tajuk' => 'PERKHIDMATAN & BEKALAN', 'items' => []],
            '30000' => ['tajuk' => 'ASET', 'items' => []],
            '40000' => ['tajuk' => 'PEMBERIAN & KENAAN BAYARAN TETAP', 'items' => []],
            '50000' => ['tajuk' => 'PERBELANJAAN - PERBELANJAAN LAIN', 'items' => []],
        ];

        // === TAPIS IKUT NEGERI ===
        $rekod_semasa = KewanganRecord::where('tahun', $tahun_semasa)->where('negeri', $negeri_user)->get()->keyBy('kod_objek');
        $rekod_lepas  = KewanganRecord::where('tahun', $tahun_lepas)->where('negeri', $negeri_user)->get()->keyBy('kod_objek');

        $all_kod_objek = $rekod_semasa->pluck('kod_objek')->merge($rekod_lepas->pluck('kod_objek'))->unique()->sort();

        foreach ($all_kod_objek as $kod) {
            $item_semasa = $rekod_semasa[$kod] ?? null;
            $item_lepas  = $rekod_lepas[$kod] ?? null;
            $kod_utama = $item_semasa->kod_utama ?? $item_lepas->kod_utama ?? 'Lain-lain';
            $butiran   = $item_semasa->butiran ?? $item_lepas->butiran ?? '-';

            if (array_key_exists($kod_utama, $laporan)) {
                $laporan[$kod_utama]['items'][] = [
                    'kod_objek' => $kod,
                    'butiran'   => $butiran,
                    'belanja_semasa' => $item_semasa->belanja ?? 0,
                    'belanja_lepas'  => $item_lepas->belanja ?? 0,
                ];
            }
        }

        return view('kewangan.perbandingan', compact('laporan', 'tahun_semasa', 'tahun_lepas'));
    }

    // --- 4. SIMPAN DATA (STORE) ---
    public function store(Request $request)
    {
        $request->validate([
            'kod_utama' => 'required',
            'kod_objek' => 'required',
            'butiran'   => 'required',
            'peruntukan'=> 'required|numeric',
            'belanja'   => 'nullable|numeric',
            'tahun'     => 'nullable|integer',
        ]);

        $tahun_input = $request->input('tahun', date('Y'));

        KewanganRecord::create([
            'negeri'    => Auth::user()->negeri, // <--- SIMPAN NEGERI USER SECARA AUTOMATIK
            'kod_utama' => $request->kod_utama,
            'kod_objek' => $request->kod_objek,
            'butiran'   => $request->butiran,
            'peruntukan'=> $request->peruntukan,
            'belanja'   => $request->belanja ?? 0,
            'tahun'     => $tahun_input,
        ]);

        return redirect()->route('kewangan.index', ['tahun' => $tahun_input])->with('success', 'Rekod berjaya ditambah!');
    }

    // --- FUNGSI LAIN (Kekal) ---
    public function waran() { return view('kewangan.waran'); }
    public function prestasi() { return view('kewangan.prestasi'); }
    public function create() { return view('kewangan.create'); }
    public function edit($id) {
        $record = KewanganRecord::findOrFail($id);
        // Pastikan user tak boleh edit negeri orang lain
        if($record->negeri !== Auth::user()->negeri) abort(403); 
        return view('kewangan.edit', compact('record'));
    }

    public function update(Request $request, $id) {
        $record = KewanganRecord::findOrFail($id);
        if($record->negeri !== Auth::user()->negeri) abort(403); 

        $s1 = $request->belanja_s1 ?? 0;
        $s2 = $request->belanja_s2 ?? 0;
        $s3 = $request->belanja_s3 ?? 0;
        $s4 = $request->belanja_s4 ?? 0;
        $total_belanja = $s1 + $s2 + $s3 + $s4;

        $record->update([
            'kod_utama' => $request->kod_utama,
            'kod_objek' => $request->kod_objek,
            'butiran'   => $request->butiran,
            'peruntukan'=> $request->peruntukan,
            'belanja_s1'=> $s1, 'belanja_s2'=> $s2, 'belanja_s3'=> $s3, 'belanja_s4'=> $s4,
            'belanja'   => $total_belanja, 
        ]);

        return redirect()->back()->with('success', 'Rekod berjaya dikemaskini!');
    }

    public function destroy($id) {
        $record = KewanganRecord::findOrFail($id);
        if($record->negeri !== Auth::user()->negeri) abort(403); 
        $tahun = $record->tahun;
        $record->delete();
        return redirect()->route('kewangan.index', ['tahun' => $tahun]);
    }
}