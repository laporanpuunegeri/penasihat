<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kewangan;
use Illuminate\Support\Facades\Auth;
use PDF;

class KewanganController extends Controller
{
    /**
     * Helper: Mengambil dan memproses data untuk semua view
     */
   protected function getLaporanKewangan(Request $request)
{
    $user = Auth::user();
    $query = Kewangan::query();

    // Filter Negeri berdasarkan user
    $query->where('negeri', $user->negeri);

    // Filter Tahun
    $tahun_dipilih = $request->input('tahun', date('Y'));
    if ($tahun_dipilih !== 'all' && $tahun_dipilih !== null) {
        $query->where(function ($q) use ($tahun_dipilih) {
            $q->where('tahun', $tahun_dipilih)->orWhereNull('tahun');
        });
    }

    $data = $query->orderBy('kod_objek', 'asc')->get();

    // GROUPING DATA
    $laporan_kewangan = [
        '10000' => ['tajuk' => 'EMOLUMEN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
        '20000' => ['tajuk' => 'PERKHIDMATAN & BEKALAN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
        '30000' => ['tajuk' => 'ASET', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
        '40000' => ['tajuk' => 'PEMBERIAN & KENAAN BAYARAN TETAP', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
        '50000' => ['tajuk' => 'PERBELANJAAN LAIN', 'items' => [], 'total_peruntukan' => 0, 'total_belanja' => 0],
    ];

    foreach ($data as $item) {
        // Kira suku tahun secara automatik
        $item->belanja_s1 = ($item->belanja_jan ?? 0) + ($item->belanja_feb ?? 0) + ($item->belanja_mac ?? 0);
        $item->belanja_s2 = ($item->belanja_apr ?? 0) + ($item->belanja_mei ?? 0) + ($item->belanja_jun ?? 0);
        $item->belanja_s3 = ($item->belanja_jul ?? 0) + ($item->belanja_ogos ?? 0) + ($item->belanja_sep ?? 0);
        $item->belanja_s4 = ($item->belanja_okt ?? 0) + ($item->belanja_nov ?? 0) + ($item->belanja_dis ?? 0);

        $kod = (string)$item->kod_utama;
        if (isset($laporan_kewangan[$kod])) {
            $laporan_kewangan[$kod]['items'][] = $item;
            $laporan_kewangan[$kod]['total_peruntukan'] += $item->peruntukan;
            $laporan_kewangan[$kod]['total_belanja'] += $item->belanja;
        }
    }

    // Grand total
    $grand_total_peruntukan = $data->sum('peruntukan');
    $grand_total_belanja = $data->sum('belanja');
    $grand_total_baki = $grand_total_peruntukan - $grand_total_belanja;
    $grand_peratus = $grand_total_peruntukan > 0 ? ($grand_total_belanja / $grand_total_peruntukan) * 100 : 0;

    return [
        'data' => $data,
        'tahun_dipilih' => $tahun_dipilih,
        'laporan_kewangan' => $laporan_kewangan,
        'grand_total_peruntukan' => $grand_total_peruntukan,
        'grand_total_belanja' => $grand_total_belanja,
        'grand_total_baki' => $grand_total_baki,
        'grand_peratus' => $grand_peratus,
    ];
}


    // --- 1. INDEX ---
public function index(Request $request)
{
    $viewData = $this->getLaporanKewangan($request);

    // --- Pengiraan Emolumen ---
    $emoData = $viewData['laporan_kewangan']['10000'] ?? ['total_peruntukan'=>0, 'total_belanja'=>0];
    $emoSiling = $emoData['total_peruntukan'] ?? 0;
    $emoBelanja = $emoData['total_belanja'] ?? 0;
    $emoBaki = $emoSiling - $emoBelanja;

    $viewData['emoSiling'] = $emoSiling;
    $viewData['emoBelanja'] = $emoBelanja;
    $viewData['emoBaki'] = $emoBaki;

    return view('kewangan.index', $viewData);
}

// --- 2. CREATE ---
    public function create()
    {
        return view('kewangan.create');
    }

    // --- 3. STORE ---
    public function store(Request $request)
{
    // 1. Ambil semua input dari form
    $data = $request->all();

    // 2. Senarai semua column belanja yang bermasalah (Not Null)
    $months = [
        'belanja_jan', 'belanja_feb', 'belanja_mac', 'belanja_apr', 
        'belanja_mei', 'belanja_jun', 'belanja_jul', 'belanja_ogos', 
        'belanja_sep', 'belanja_okt', 'belanja_nov', 'belanja_dis'
    ];

    // 3. Paksa tukar NULL kepada 0 supaya database tak marah
    foreach ($months as $month) {
        // Jika input kosong atau null, kita set jadi 0
        $data[$month] = $request->input($month) ?? 0;
    }

    // 4. Kira jumlah keseluruhan (Total Belanja)
    $totalBelanja = collect($months)->map(fn($m) => $data[$m])->sum();

    // 5. Gabungkan data tambahan (User, Negeri, Tahun)
    $finalData = array_merge($data, [
        'belanja' => $totalBelanja,
        'tahun' => $request->input('tahun', date('Y')),
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'negeri' => \Illuminate\Support\Facades\Auth::user()->negeri,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    try {
        // 6. Simpan ke database
        \App\Models\Kewangan::create($finalData);

        return redirect()->back()->with('success', 'Rekod kewangan berjaya disimpan!');
    } catch (\Exception $e) {
        // Jika ada error lain, dia akan tunjuk kat sini
        return redirect()->back()->with('error', 'Gagal simpan: ' . $e->getMessage());
    }
}
    // --- 4. EDIT ---
    public function edit($id)
    {
        $record = Kewangan::findOrFail($id);
        return view('kewangan.edit', compact('record'));
    }

    // --- 5. UPDATE ---
    public function update(Request $request, $id)
    {
        $record = Kewangan::findOrFail($id);

        $data = $request->validate([
            'kod_utama' => 'required',
            'kod_objek' => 'required',
            'butiran' => 'required',
            'peruntukan' => 'required|numeric',
            'belanja_jan' => 'nullable|numeric',
            'belanja_feb' => 'nullable|numeric',
            'belanja_mac' => 'nullable|numeric',
            'belanja_apr' => 'nullable|numeric',
            'belanja_mei' => 'nullable|numeric',
            'belanja_jun' => 'nullable|numeric',
            'belanja_jul' => 'nullable|numeric',
            'belanja_ogos' => 'nullable|numeric',
            'belanja_sep' => 'nullable|numeric',
            'belanja_okt' => 'nullable|numeric',
            'belanja_nov' => 'nullable|numeric',
            'belanja_dis' => 'nullable|numeric',
        ]);

        $totalBelanja = collect([
            $request->belanja_jan, $request->belanja_feb, $request->belanja_mac,
            $request->belanja_apr, $request->belanja_mei, $request->belanja_jun,
            $request->belanja_jul, $request->belanja_ogos, $request->belanja_sep,
            $request->belanja_okt, $request->belanja_nov, $request->belanja_dis
        ])->sum();

        $record->update(array_merge($data, ['belanja' => $totalBelanja]));

        return redirect()->route('kewangan.index')->with('success', 'Rekod kewangan berjaya dikemaskini.');
    }

    // --- 6. DESTROY ---
    public function destroy($id)
    {
        $record = Kewangan::findOrFail($id);
        $record->delete();
        return redirect()->route('kewangan.index')->with('success', 'Rekod berjaya dipadam.');
    }

// --- 7. PDF BULANAN (FIXED: POSTGRESQL COMPATIBLE) ---
    public function cetakPdfBulanan(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $tahun = $request->input('tahun', date('Y'));

        // 1. FILTER DATA
        $query = Kewangan::query();

        // Filter Negeri
        if ($user->negeri) {
            $query->where('negeri', 'ILIKE', '%' . $user->negeri . '%');
        }

        // Filter Tahun (FIXED ERROR 22P02)
        $query->where(function($q) use ($tahun) {
            $q->whereRaw("CAST(tahun AS TEXT) LIKE ?", ["%{$tahun}%"])
              ->orWhereNull('tahun'); 
        });

        $rekod_db = $query->orderBy('kod_objek', 'asc')->get();

        // 2. INITIALIZE VARIABLE
        $laporan_kewangan = [];
        $grand_total_peruntukan = 0;
        $grand_total_belanja = 0;

        // Variable untuk simpan total setiap bulan
        $total_bulanan = [
            'jan' => 0, 'feb' => 0, 'mac' => 0, 'apr' => 0, 'mei' => 0, 'jun' => 0,
            'jul' => 0, 'ogos' => 0, 'sep' => 0, 'okt' => 0, 'nov' => 0, 'dis' => 0
        ];

        $tajuk_kod = [
            '10000' => 'EMOLUMEN',
            '20000' => 'PERKHIDMATAN & BEKALAN',
            '30000' => 'ASET',
            '40000' => 'PEMBERIAN & KENAAN BAYARAN TETAP',
            '50000' => 'PERBELANJAAN LAIN-LAIN'
        ];

        // 3. LOOPING & PENGIRAAN
        foreach ($rekod_db as $item) {
            $kod_utama = $item->kod_utama;
            
            // Setup Struktur Group
            if (!isset($laporan_kewangan[$kod_utama])) {
                $laporan_kewangan[$kod_utama] = [
                    'tajuk' => $tajuk_kod[$kod_utama] ?? 'LAIN',
                    'total_peruntukan' => 0,
                    'total_belanja' => 0,
                    'items' => []
                ];
            }

            // Masukkan Item
            $laporan_kewangan[$kod_utama]['items'][] = $item;
            
            // Campur Total Group
            $laporan_kewangan[$kod_utama]['total_peruntukan'] += $item->peruntukan;
            $laporan_kewangan[$kod_utama]['total_belanja'] += $item->belanja;

            // Campur Grand Total
            $grand_total_peruntukan += $item->peruntukan;
            $grand_total_belanja += $item->belanja;

            // Campur Total Setip Bulan
            $total_bulanan['jan']  += $item->belanja_jan;
            $total_bulanan['feb']  += $item->belanja_feb;
            $total_bulanan['mac']  += $item->belanja_mac;
            $total_bulanan['apr']  += $item->belanja_apr;
            $total_bulanan['mei']  += $item->belanja_mei;
            $total_bulanan['jun']  += $item->belanja_jun;
            $total_bulanan['jul']  += $item->belanja_jul;
            $total_bulanan['ogos'] += $item->belanja_ogos;
            $total_bulanan['sep']  += $item->belanja_sep;
            $total_bulanan['okt']  += $item->belanja_okt;
            $total_bulanan['nov']  += $item->belanja_nov;
            $total_bulanan['dis']  += $item->belanja_dis;
        }

        ksort($laporan_kewangan);

        $viewData = [
            'title' => 'Laporan Kewangan Bulanan Tahun ' . $tahun,
            'tahun' => $tahun,
            'laporan_kewangan' => $laporan_kewangan,
            'grand_total_peruntukan' => $grand_total_peruntukan,
            'grand_total_belanja' => $grand_total_belanja,
            'total_bulanan' => $total_bulanan,
        ];

        $pdf = PDF::loadView('kewangan.pdf_bulanan', $viewData);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Kewangan_Bulanan_' . $tahun . '.pdf');
    }

    // --- 8. PDF SUKU TAHUN ---
    public function cetakPdfSuku(Request $request)
    {
        $viewData = $this->getLaporanKewangan($request);

        $viewData['title'] = 'Laporan Prestasi Suku Tahun ' . $viewData['tahun_dipilih'];

        $pdf = PDF::loadView('kewangan.pdf_suku_tahun', $viewData);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Prestasi_Suku_' . $viewData['tahun_dipilih'] . '.pdf');
    }

    // --- 9. SUKU TAHUN ---
    public function sukuTahun(Request $request)
    {
        $viewData = $this->getLaporanKewangan($request);
        return view('kewangan.suku_tahun', $viewData);
    }

// --- 10. PERBANDINGAN TAHUNAN ---
    public function perbandingan(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // 1. Tentukan 3 Tahun Terkini
        $tahunSemasa = $request->input('tahun', date('Y'));
        $tahunLepas = $tahunSemasa - 1;
        $tahun2Lepas = $tahunSemasa - 2;

        // 2. Tarik Data
        $query = Kewangan::query();
        
        if ($user->negeri) {
            $query->where('negeri', 'ILIKE', '%' . $user->negeri . '%');
        }

        $query->where(function($q) use ($tahunSemasa, $tahunLepas, $tahun2Lepas) {
            $q->whereRaw("CAST(tahun AS TEXT) LIKE ?", ["%{$tahunSemasa}%"])
              ->orWhereRaw("CAST(tahun AS TEXT) LIKE ?", ["%{$tahunLepas}%"])
              ->orWhereRaw("CAST(tahun AS TEXT) LIKE ?", ["%{$tahun2Lepas}%"])
              ->orWhereNull('tahun'); 
        });

        $semuaData = $query->get();

        // 3. Setup Struktur Data
        $laporan = [
            '10000' => ['tajuk' => 'EMOLUMEN', 'items' => []],
            '20000' => ['tajuk' => 'PERKHIDMATAN & BEKALAN', 'items' => []],
            '30000' => ['tajuk' => 'ASET', 'items' => []],
            '40000' => ['tajuk' => 'PEMBERIAN & KENAAN BAYARAN TETAP', 'items' => []],
            '50000' => ['tajuk' => 'PERBELANJAAN LAIN-LAIN', 'items' => []],
        ];

        // 4. Looping & Agihkan Data Ke Tahun Masing-masing
        foreach ($semuaData as $item) {
            $tahunDB = trim((string)$item->tahun);
            
            if ($tahunDB == "" || $tahunDB == null) {
                $tahunDB = (string)$tahunSemasa;
            }

            $kodGroup = (string)$item->kod_utama;
            $kodObjek = (string)$item->kod_objek;

            if (!isset($laporan[$kodGroup])) continue;

            if (!isset($laporan[$kodGroup]['items'][$kodObjek])) {
                $laporan[$kodGroup]['items'][$kodObjek] = [
                    'kod_objek' => $kodObjek,
                    'butiran' => $item->butiran,
                    'belanja_semasa' => 0,      
                    'belanja_lepas' => 0,       
                    'belanja_2_lepas' => 0,     
                ];
            }

            if ($tahunDB == $tahunSemasa) {
                $laporan[$kodGroup]['items'][$kodObjek]['belanja_semasa'] += $item->belanja;
            } 
            elseif ($tahunDB == $tahunLepas) {
                $laporan[$kodGroup]['items'][$kodObjek]['belanja_lepas'] += $item->belanja;
            } 
            elseif ($tahunDB == $tahun2Lepas) {
                $laporan[$kodGroup]['items'][$kodObjek]['belanja_2_lepas'] += $item->belanja;
            }
        }

        // 5. Susun Ikut Kod Objek
        foreach ($laporan as $key => $val) {
            ksort($laporan[$key]['items']);
        }

        return view('kewangan.perbandingan', [
            'laporan' => $laporan,
            'tahun_semasa' => $tahunSemasa,
            'tahun_lepas' => $tahunLepas,
            'tahun_2_lepas' => $tahun2Lepas,
            'tahun' => $tahunSemasa
        ]);
    }
}
