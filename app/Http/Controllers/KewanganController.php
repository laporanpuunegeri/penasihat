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

    // --- 7. PDF BULANAN ---
    public function cetakPdfBulanan(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $rekod_db = Kewangan::where('tahun', $tahun)->orderBy('kod_objek')->get();

        $laporan_kewangan = [];
        $grand_total_peruntukan = 0;
        $grand_total_belanja = 0;

        $tajuk_kod = [
            '10000' => 'EMOLUMEN',
            '20000' => 'PERKHIDMATAN & BEKALAN',
            '30000' => 'ASET',
            '40000' => 'PEMBERIAN & KENAAN BAYARAN TETAP',
            '50000' => 'PERBELANJAAN LAIN-LAIN'
        ];

        foreach ($rekod_db as $item) {
            $kod_utama = $item->kod_utama;
            if (!isset($laporan_kewangan[$kod_utama])) {
                $laporan_kewangan[$kod_utama] = [
                    'tajuk' => $tajuk_kod[$kod_utama] ?? 'LAIN',
                    'total_peruntukan' => 0,
                    'total_belanja' => 0,
                    'items' => []
                ];
            }
            $laporan_kewangan[$kod_utama]['items'][] = $item;
            $laporan_kewangan[$kod_utama]['total_peruntukan'] += $item->peruntukan;
            $laporan_kewangan[$kod_utama]['total_belanja'] += $item->belanja;

            $grand_total_peruntukan += $item->peruntukan;
            $grand_total_belanja += $item->belanja;
        }

        ksort($laporan_kewangan);

        $viewData = [
            'title' => 'Laporan Kewangan Bulanan Tahun ' . $tahun,
            'tahun' => $tahun,
            'laporan_kewangan' => $laporan_kewangan,
            'grand_total_peruntukan' => $grand_total_peruntukan,
            'grand_total_belanja' => $grand_total_belanja,
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

    // --- 10. PERBANDINGAN ---
    public function perbandingan(Request $request)
    {
        $viewData = $this->getLaporanKewangan($request);

        $viewData['tahun_semasa'] = $request->input('tahun', date('Y'));
        $viewData['tahun_lepas'] = $viewData['tahun_semasa'] - 1;
        $viewData['tahun_2_lepas'] = $viewData['tahun_semasa'] - 2;

        $laporan = [];
        foreach ($viewData['laporan_kewangan'] as $kod => $group) {
            $items = [];
            foreach ($group['items'] as $item) {
                $items[] = [
                    'kod_objek' => $item->kod_objek,
                    'butiran' => $item->butiran,
                    'belanja_semasa' => $item->belanja,
                    'belanja_lepas' => $item->belanja,
                    'belanja_2_lepas' => $item->belanja,
                ];
            }
            $laporan[$kod] = [
                'tajuk' => $group['tajuk'],
                'items' => $items,
            ];
        }

        $viewData['laporan'] = $laporan;

        return view('kewangan.perbandingan', $viewData);
    }
}
