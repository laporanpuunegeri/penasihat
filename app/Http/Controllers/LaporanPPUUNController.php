<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPPUUN;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPPUUNController extends Controller
{
    // Data statik OUTCOME dari dokumen anda (dikeluarkan dari function store/create untuk dipaparkan)
    private $outcomes = [
        'OUTCOME 1' => [
            'tajuk' => 'Khidmat Nasihat Perundangan Yang Cekap Dan Teratur Kepada Kerajaan Negeri',
            'cat_labels' => ['Nasihat Undang-Undang', 'Nasihat Syariah', 'Perjanjian'],
        ],
        'OUTCOME 2' => [
            'tajuk' => 'Pengendalian Kes Sivil Kerajaan Negeri Yang Cekap Dan Teratur',
            'cat_labels' => ['Kes Sivil', 'Kes Pengambilan Tanah', 'Kes Rayuan Sivil', 'Kes Rayuan Pengambilan Tanah'],
        ],
        'OUTCOME 3' => [
            'tajuk' => 'Penggubalan Semakan Dan Pencetakan Semula Enakmen Dan Rang Undang-Undang Subsidiari Yang Cekap Dan Teratur',
            'cat_labels' => ['Penggubalan Perundangan utama dan subsidiari', 'Semakan dan cetakan semula undang-undang'],
        ],
    ];

    // 1. INDEX: Papar Ringkasan Prestasi (Untuk dipadankan dengan borang anda)
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        
        // Ambil data mengikut OUTCOME, diurutkan
        $data = LaporanPPUUN::where('tahun', $tahun)
                            ->orderBy('outcome_id')
                            ->get()
                            ->groupBy('outcome_id'); // Group by Outcome 1, 2, 3
        
        // Data statik untuk Header/Footer Laporan
        $metadata = [
            'tahun' => $tahun,
            'tajuk' => 'PRESTASI KERANGKA KEBERHASILAN PROGRAM PENGURUSAN (PPUUN)',
        ];

        // 🔥 VIEW DIBETULKAN KEPADA LALUAN ASAL (Jika anda buat view di situ) 🔥
        return view('pentadbiran.laporan_prestasi.index', compact('data', 'metadata'));
    }
    
    // 2. CREATE: Borang Daftar
    public function create()
    {
        return view('pentadbiran.laporan_prestasi.create', [
            'outcomes' => $this->outcomes,
            'tahun' => date('Y')
        ]);
    }

    // 3. STORE: Simpan Data
    public function store(Request $request)
    {
        $request->validate([
            'outcome_id' => 'required|string',
            'sasaran' => 'required|numeric',
            'kpi_desc' => 'required|string',
            'suku.*' => 'required|numeric',
            'catatan_val.*' => 'nullable|string',
        ]);

        $catatanArray = [];
        $catatanLabels = $this->outcomes[$request->outcome_id]['cat_labels'] ?? [];
        $catatanInput = $request->catatan_val;

        foreach ($catatanLabels as $index => $label) {
            if (isset($catatanInput[$index])) {
                 $catatanArray[$label] = $catatanInput[$index];
            }
        }
        
        // Simpan ke Database
        LaporanPPUUN::create([
            'tahun' => $request->tahun,
            'outcome_id' => $request->outcome_id,
            'kpi_desc' => $request->kpi_desc,
            'sasaran_tahunan' => $request->sasaran,
            'suku_1' => $request->suku[0],
            'suku_2' => $request->suku[1],
            'suku_3' => $request->suku[2],
            'suku_4' => $request->suku[3],
            'catatan_data' => $catatanArray,
            'user_id' => Auth::id(),
            'status' => 'Sedia untuk disahkan',
        ]);

        return redirect()->route('pentadbiran.laporan_prestasi.index')->with('success', 'Laporan PPUUN berjaya disimpan.');
    }

    // 4. CETAK PDF
    public function cetak(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $data = LaporanPPUUN::where('tahun', $tahun)
                            ->orderBy('outcome_id')
                            ->get()
                            ->groupBy('outcome_id');
        $metadata = [
            'tahun' => $tahun,
            'tajuk' => 'PRESTASI KERANGKA KEBERHASILAN PROGRAM PENGURUSAN (PPUUN)',
        ];

        // 🔥 VIEW DIBETULKAN KEPADA NAMA FAIL CETAK YANG BETUL 🔥
        $pdf = Pdf::loadView('pentadbiran.laporan_prestasi.cetak', compact('data', 'metadata'));

        return $pdf->stream('Laporan_PPUUN_' . $tahun . '.pdf');
    }
}