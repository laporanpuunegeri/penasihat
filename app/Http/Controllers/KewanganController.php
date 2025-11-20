<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KewanganRecord;
use PDF;

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
        $negeri_user   = Auth::user()->negeri;

        // Struktur Asas Accordion
        $laporan_kewangan = [
            '10000' => ['tajuk' => 'EMOLUMEN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '20000' => ['tajuk' => 'PERKHIDMATAN & BEKALAN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '30000' => ['tajuk' => 'ASET', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '40000' => ['tajuk' => 'PEMBERIAN & KENAAN BAYARAN TETAP', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '50000' => ['tajuk' => 'PERBELANJAAN - PERBELANJAAN LAIN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
        ];

        $records = KewanganRecord::where('tahun', $tahun_dipilih)
                    ->where('negeri', $negeri_user)
                    ->orderBy('kod_objek', 'asc')
                    ->get();

        foreach ($records as $record) {
            if (array_key_exists($record->kod_utama, $laporan_kewangan)) {
                $laporan_kewangan[$record->kod_utama]['items'][] = $record; // Simpan object rekod terus
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

        $grand_total_baki = $grand_total_peruntukan - $grand_total_belanja;
        $grand_peratus = $grand_total_peruntukan > 0 ? ($grand_total_belanja / $grand_total_peruntukan) * 100 : 0;

        return view('kewangan.index', compact(
            'laporan_kewangan', 'grand_total_peruntukan', 'grand_total_belanja', 'grand_total_baki', 'grand_peratus', 'tahun_dipilih'
        ));
    }

    // --- 2. LAPORAN PRESTASI PERBELANJAAN SUKU TAHUN ---
    public function sukuTahun(Request $request)
    {
        // 1. Ambil tahun dari URL atau guna tahun semasa
        $tahun_dipilih = $request->input('tahun', date('Y'));
        $negeri_user   = Auth::user()->negeri;

        // 2. Struktur Asas (Sama seperti Index supaya konsisten)
        $laporan_kewangan = [
            '10000' => ['tajuk' => 'EMOLUMEN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '20000' => ['tajuk' => 'PERKHIDMATAN & BEKALAN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '30000' => ['tajuk' => 'ASET', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '40000' => ['tajuk' => 'PEMBERIAN & KENAAN BAYARAN TETAP', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
            '50000' => ['tajuk' => 'PERBELANJAAN - PERBELANJAAN LAIN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
        ];

        // 3. Tarik Data Database (Tapis Tahun & Negeri)
        $records = KewanganRecord::where('tahun', $tahun_dipilih)
                    ->where('negeri', $negeri_user)
                    ->orderBy('kod_objek', 'asc')
                    ->get();

        // 4. Masukkan Data ke dalam Struktur
        foreach ($records as $record) {
            if (array_key_exists($record->kod_utama, $laporan_kewangan)) {
                $laporan_kewangan[$record->kod_utama]['items'][] = $record;
                
                // Kira sub-total untuk header kategori
                $laporan_kewangan[$record->kod_utama]['total_peruntukan'] += $record->peruntukan;
                $laporan_kewangan[$record->kod_utama]['total_belanja'] += $record->belanja;
            }
        }

        // 5. Kira Jumlah Besar (Grand Total) untuk footer table
        $grand_total_peruntukan = 0;
        $grand_total_belanja = 0;

        foreach($laporan_kewangan as $data) {
            $grand_total_peruntukan += $data['total_peruntukan'];
            $grand_total_belanja += $data['total_belanja'];
        }

        // 6. Hantar ke View
        return view('kewangan.suku_tahun', compact(
            'tahun_dipilih', 
            'laporan_kewangan', 
            'grand_total_peruntukan', 
            'grand_total_belanja'
        ));
    }

// --- 3. PERBANDINGAN 3 TAHUN ---
    public function perbandingan(Request $request)
    {
        // 1. Tentukan 3 Tahun
        $tahun_semasa  = $request->input('tahun', date('Y')); // <--- Ini variable yang View cari
        $tahun_lepas   = $tahun_semasa - 1;
        $tahun_2_lepas = $tahun_semasa - 2;

        $negeri_user   = Auth::user()->negeri;

        // 2. Struktur Asas
        $laporan = [
            '10000' => ['tajuk' => 'EMOLUMEN', 'items' => []],
            '20000' => ['tajuk' => 'PERKHIDMATAN & BEKALAN', 'items' => []],
            '30000' => ['tajuk' => 'ASET', 'items' => []],
            '40000' => ['tajuk' => 'PEMBERIAN & KENAAN BAYARAN TETAP', 'items' => []],
            '50000' => ['tajuk' => 'PERBELANJAAN - PERBELANJAAN LAIN', 'items' => []],
        ];

        // 3. Tarik Data
        $rekod_semasa  = KewanganRecord::where('tahun', $tahun_semasa)->where('negeri', $negeri_user)->get()->keyBy('kod_objek');
        $rekod_lepas   = KewanganRecord::where('tahun', $tahun_lepas)->where('negeri', $negeri_user)->get()->keyBy('kod_objek');
        $rekod_2_lepas = KewanganRecord::where('tahun', $tahun_2_lepas)->where('negeri', $negeri_user)->get()->keyBy('kod_objek');

        // 4. Gabungkan Semua Kod Objek
        $all_kod_objek = $rekod_semasa->pluck('kod_objek')
                            ->merge($rekod_lepas->pluck('kod_objek'))
                            ->merge($rekod_2_lepas->pluck('kod_objek'))
                            ->unique()
                            ->sort();

        foreach ($all_kod_objek as $kod) {
            $item_semasa  = $rekod_semasa[$kod] ?? null;
            $item_lepas   = $rekod_lepas[$kod] ?? null;
            $item_2_lepas = $rekod_2_lepas[$kod] ?? null;

            $kod_utama = $item_semasa->kod_utama ?? $item_lepas->kod_utama ?? $item_2_lepas->kod_utama ?? 'Lain-lain';
            $butiran   = $item_semasa->butiran ?? $item_lepas->butiran ?? $item_2_lepas->butiran ?? '-';

            if (array_key_exists($kod_utama, $laporan)) {
                $laporan[$kod_utama]['items'][] = [
                    'kod_objek'       => $kod,
                    'butiran'         => $butiran,
                    'belanja_semasa'  => $item_semasa->belanja ?? 0,
                    'belanja_lepas'   => $item_lepas->belanja ?? 0,
                    'belanja_2_lepas' => $item_2_lepas->belanja ?? 0,
                ];
            }
        }

        // HANTAR KE VIEW (Pastikan ejaan 'tahun_semasa' sama dengan View)
        return view('kewangan.perbandingan', compact('laporan', 'tahun_semasa', 'tahun_lepas', 'tahun_2_lepas'));
    }

    // --- 2. BORANG TAMBAH (CREATE) ---
    public function create()
    {
        return view('kewangan.create');
    }

    // --- 3. SIMPAN DATA (STORE) ---
    public function store(Request $request)
    {
        // Validate input asas
        $request->validate([
            'kod_utama' => 'required',
            'kod_objek' => 'required',
            'butiran'   => 'required',
            'peruntukan'=> 'required|numeric',
            // Belanja boleh null, tapi kalau ada mesti numeric
            'belanja'   => 'nullable|numeric', 
        ]);

        $tahun_input = $request->input('tahun', date('Y'));

        // Kira total belanja secara automatik dari backend (Backup kalau JS tak jalan)
        // Atau guna nilai dari request jika ada.
        // Di sini kita ambil nilai input individual suku tahun
        $s1 = $request->belanja_s1 ?? 0;
        $s2 = $request->belanja_s2 ?? 0;
        $s3 = $request->belanja_s3 ?? 0;
        $s4 = $request->belanja_s4 ?? 0;
        
        // Logic: Kalau user tak hantar total 'belanja' dari hidden input, kita kira sendiri
        $total_belanja = $request->belanja ?? ($s1 + $s2 + $s3 + $s4);

        KewanganRecord::create([
            'negeri'     => Auth::user()->negeri,
            'kod_utama'  => $request->kod_utama,
            'kod_objek'  => $request->kod_objek,
            'butiran'    => $request->butiran,
            'peruntukan' => $request->peruntukan,
            'tahun'      => $tahun_input,
            
            // --- INI YANG TERTINGGAL TADI (WAJIB ADA) ---
            'belanja_s1' => $s1,
            'belanja_s2' => $s2,
            'belanja_s3' => $s3,
            'belanja_s4' => $s4,
            // -------------------------------------------

            'belanja'    => $total_belanja,
        ]);

        return redirect()->route('kewangan.index', ['tahun' => $tahun_input])
                         ->with('success', 'Rekod berjaya ditambah!');
    }

    // --- 4. BORANG EDIT (EDIT) ---
    public function edit($id)
    {
        $record = KewanganRecord::findOrFail($id);
        if($record->negeri !== Auth::user()->negeri) abort(403);
        return view('kewangan.edit', compact('record'));
    }

  // --- 5. KEMASKINI DATA (UPDATE) ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'kod_utama' => 'required',
            'kod_objek' => 'required',
            'butiran'   => 'required',
            'peruntukan'=> 'required|numeric',
            'belanja'   => 'nullable|numeric',
        ]);

        $record = KewanganRecord::findOrFail($id);
        
        // Security check: Pastikan user edit negeri sendiri sahaja
        if($record->negeri !== Auth::user()->negeri) abort(403);

        // Ambil nilai suku tahun
        $s1 = $request->belanja_s1 ?? 0;
        $s2 = $request->belanja_s2 ?? 0;
        $s3 = $request->belanja_s3 ?? 0;
        $s4 = $request->belanja_s4 ?? 0;

        // Logic: Kalau hidden input belanja kosong, backend tolong kirakan
        $total_belanja = $request->belanja ?? ($s1 + $s2 + $s3 + $s4);

        $record->update([
            'kod_utama'  => $request->kod_utama,
            'kod_objek'  => $request->kod_objek,
            'butiran'    => $request->butiran,
            'peruntukan' => $request->peruntukan,
            
            // --- INI YANG TERTINGGAL TADI (WAJIB ADA) ---
            'belanja_s1' => $s1,
            'belanja_s2' => $s2,
            'belanja_s3' => $s3,
            'belanja_s4' => $s4,
            // -------------------------------------------

            'belanja'    => $total_belanja,
        ]);

        return redirect()->route('kewangan.index', ['tahun' => $record->tahun])
                         ->with('success', 'Rekod berjaya dikemaskini!');
    }
    // --- 6. HAPUS DATA (DESTROY) ---
    public function destroy($id)
    {
        $record = KewanganRecord::findOrFail($id);
        if($record->negeri !== Auth::user()->negeri) abort(403);
        $tahun_asal = $record->tahun;
        $record->delete();

        return redirect()->route('kewangan.index', ['tahun' => $tahun_asal])->with('success', 'Rekod berjaya dihapuskan!');
    }

        // --- 7. CETAK PDF ---


public function cetakPdf(Request $request)
{
    $tahun = $request->input('tahun');

    // 1. TARIK DATA DARI DATABASE
    // Menggunakan KewanganRecord
    $rekod_db = KewanganRecord::where('tahun', $tahun)
                ->orderBy('kod_objek', 'asc') // Susun ikut kod objek (10000, 20000...)
                ->get();

    // 2. DEFINISI NAMA TAJUK UTAMA (MAPPING)
    $tajuk_kod = [
        '10000' => 'EMOLUMEN',
        '20000' => 'PERKHIDMATAN DAN BEKALAN',
        '30000' => 'ASET',
        '40000' => 'PEMBERIAN DAN KENAAN BAYARAN TETAP',
        '50000' => 'PERBELANJAAN LAIN-LAIN',
    ];

    // 3. PROSES DATA UNTUK GROUPING (Logic PHP)
    $laporan_kewangan = [];
    
    // Initialize Grand Totals (Untuk Footer Table)
    $grand_total_peruntukan = 0;
    $grand_total_belanja = 0;
    $grand_s1 = 0; $grand_s2 = 0; $grand_s3 = 0; $grand_s4 = 0;

    foreach ($rekod_db as $item) {
        // Ambil digit pertama kod objek (Contoh: 11000 -> '1')
        // Pastikan column di DB nama dia 'kod_objek'
        $first_digit = substr((string)$item->kod_objek, 0, 1); 
        $kod_utama = $first_digit . '0000'; // Jadi '10000'

        // Jika group ini belum wujud, create array dia
        if (!isset($laporan_kewangan[$kod_utama])) {
            $laporan_kewangan[$kod_utama] = [
                'tajuk' => $tajuk_kod[$kod_utama] ?? 'LAIN-LAIN',
                'total_peruntukan' => 0,
                'total_belanja' => 0,
                'items' => []
            ];
        }

        // Kira Total Belanja Item ini
        // PASTIKAN column DB anda nama dia: 'belanja_s1', 'belanja_s2' etc.
        $total_item_belanja = $item->belanja_s1 + $item->belanja_s2 + $item->belanja_s3 + $item->belanja_s4;
        
        // Tambah property 'belanja' manual ke dalam object item untuk view guna
        $item->belanja = $total_item_belanja; 
        
        // Masukkan ke dalam list items
        $laporan_kewangan[$kod_utama]['items'][] = $item;

        // Update Sub-Total Group
        $laporan_kewangan[$kod_utama]['total_peruntukan'] += $item->peruntukan;
        $laporan_kewangan[$kod_utama]['total_belanja'] += $total_item_belanja;

        // Update Grand Total (Keseluruhan)
        $grand_total_peruntukan += $item->peruntukan;
        $grand_total_belanja += $total_item_belanja;
        $grand_s1 += $item->belanja_s1;
        $grand_s2 += $item->belanja_s2;
        $grand_s3 += $item->belanja_s3;
        $grand_s4 += $item->belanja_s4;
    }

    // Sort array supaya urutan betul (10000, 20000...)
    ksort($laporan_kewangan);

    // 4. HANTAR DATA KE VIEW
    $viewData = [
        'title' => 'LAPORAN PRESTASI PERBELANJAAN TAHUN ' . $tahun,
        'tahun' => $tahun,
        'laporan_kewangan' => $laporan_kewangan,
        
        // Variable Totals
        'grand_total_peruntukan' => $grand_total_peruntukan,
        'grand_total_belanja' => $grand_total_belanja,
        'grand_s1' => $grand_s1,
        'grand_s2' => $grand_s2,
        'grand_s3' => $grand_s3,
        'grand_s4' => $grand_s4,
    ];

    $pdf = PDF::loadView('kewangan.pdf_suku_tahun', $viewData);
    $pdf->setPaper('a4', 'landscape');

    return $pdf->stream('Laporan_Suku_Tahun_'.$tahun.'.pdf');
}
}