<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPPUUN;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPPUUNController extends Controller
{

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

    // 1. INDEX (DASHBOARD - IKUT NEGERI)
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $userNegeri = Auth::user()->negeri; 

        $data = LaporanPPUUN::where('tahun', $tahun)
                            ->where('negeri', $userNegeri) 
                            ->orderBy('outcome_id')
                            ->get()
                            ->groupBy('outcome_id');
        
        // Statistik Ringkas
        $totalPencapaian = 0;
        $count = 0;
        foreach($data as $group) {
            $rec = $group->first();
            if($rec->sasaran_tahunan > 0) {
                $totalVal = $rec->suku_1 + $rec->suku_2 + $rec->suku_3 + $rec->suku_4;
                $perc = ($totalVal / $rec->sasaran_tahunan) * 100;
                $totalPencapaian += $perc;
            }
            $count++;
        }
        $avgPencapaian = $count > 0 ? round($totalPencapaian / $count, 1) : 0;

        $stats = [
            'avg_pencapaian' => $avgPencapaian,
        ];

        $metadata = [
            'tahun' => $tahun,
            'negeri' => $userNegeri, 
            'tajuk' => 'PRESTASI KERANGKA KEBERHASILAN PROGRAM PENGURUSAN (PPUUN)',
        ];

        $outcomesList = $this->outcomes;

        return view('pentadbiran.laporan_prestasi.index', compact('data', 'metadata', 'stats', 'outcomesList'));
    }
    
    // 2. CREATE (BORANG)
    public function create(Request $request)
    {
        $outcome_id = $request->outcome_id;
        $tahun = $request->tahun ?? date('Y');
        $userNegeri = Auth::user()->negeri;

        // Cari rekod lama (Mesti match Tahun + Outcome + Negeri)
        $rekod_lama = null;
        if($outcome_id) {
            $rekod_lama = LaporanPPUUN::where('tahun', $tahun)
                                      ->where('negeri', $userNegeri)
                                      ->where('outcome_id', $outcome_id)
                                      ->first();
        }

        return view('pentadbiran.laporan_prestasi.create', [
            'outcomes' => $this->outcomes,
            'tahun' => $tahun,
            'selected_outcome' => $outcome_id,
            'rekod' => $rekod_lama,
            'userNegeri' => $userNegeri 
        ]);
    }

// 3. STORE (KEMASKINI: SIMPAN DATA BEBAN KES)
    public function store(Request $request)
    {
        $request->validate([
            'outcome_id' => 'required|string',
            'sasaran' => 'required|numeric',
            'kpi_desc' => 'required|string',
            'suku.*' => 'required|numeric',
            'masuk.*' => 'nullable|numeric',
            'selesai.*' => 'nullable|numeric',
        ]);

        $user = Auth::user();

        // 1. Proses Catatan
        $catatanArray = [];
        $catatanLabels = $this->outcomes[$request->outcome_id]['cat_labels'] ?? [];
        $catatanInput = $request->catatan_val;
        foreach ($catatanLabels as $index => $label) {
            if (isset($catatanInput[$index])) {
                 $catatanArray[$label] = $catatanInput[$index];
            }
        }
        
        // 2. Proses Beban Kes (Masuk & Selesai)

        $bebanKesData = [
            'masuk' => $request->masuk ?? [0,0,0,0],
            'selesai' => $request->selesai ?? [0,0,0,0]
        ];

        // 3. Simpan ke DB
        LaporanPPUUN::updateOrCreate(
            [
                'tahun' => $request->tahun,
                'outcome_id' => $request->outcome_id,
                'negeri' => $user->negeri, 
            ],
            [
                'kpi_desc' => $request->kpi_desc,
                'sasaran_tahunan' => $request->sasaran,
                'suku_1' => $request->suku[0] ?? 0,
                'suku_2' => $request->suku[1] ?? 0,
                'suku_3' => $request->suku[2] ?? 0,
                'suku_4' => $request->suku[3] ?? 0,
                'catatan_data' => $catatanArray,
                'beban_kes' => $bebanKesData, 
                'user_id' => $user->id,
                'status' => 'Disimpan',
            ]
        );

        return redirect()->route('pentadbiran.laporan_prestasi.index', ['tahun' => $request->tahun])
                         ->with('success', 'Laporan berjaya disimpan!');
    }

    // 4. CETAK PDF (FILTER NEGERI JUGA)
    public function cetak(Request $request)
    {
        $user = Auth::user(); 
        $tahun = $request->tahun ?? date('Y');
        
        // Ambil Data Laporan (Hanya Negeri User)
        $data = LaporanPPUUN::where('tahun', $tahun)
                            ->where('negeri', $user->negeri) // Filter Negeri
                            ->orderBy('outcome_id')
                            ->get()
                            ->groupBy('outcome_id');
        
        $metadata = [
            'tahun' => $tahun,
            'negeri' => strtoupper($user->negeri),
            'tajuk' => 'PRESTASI KERANGKA KEBERHASILAN PROGRAM PENGURUSAN (PPUUN)',
        ];

        $eo = User::where('negeri', $user->negeri) 
                ->where(function($q) {
                    $q->where('role', 'ILIKE', '%EO%')->orWhere('role', 'ILIKE', '%Urusetia%');
                })->first();

        $yb = User::where('negeri', $user->negeri) 
                ->where(function($q) {
                    $q->where('role', 'ILIKE', '%Penasihat%')->orWhere('role', 'ILIKE', '%YB%')->orWhere('role', 'ILIKE', '%Ketua%');
                })->first();

        // Logic Sain (Hybrid Base64/Path)
        $prosesSain = function($rawData) {
            if (empty($rawData)) return null;
            if (str_contains($rawData, 'data:image')) return $rawData;

            $paths = [storage_path('app/public/' . $rawData), public_path('storage/' . $rawData), public_path($rawData)];
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $content = file_get_contents($path);
                    return 'data:image/' . $type . ';base64,' . base64_encode($content);
                }
            }
            return null;
        };

        $sainEo = ($eo && $eo->signature_file) ? $prosesSain($eo->signature_file) : null;
        $sainYb = ($yb && $yb->signature_file) ? $prosesSain($yb->signature_file) : null;

        $pdf = Pdf::loadView('pentadbiran.laporan_prestasi.cetak', compact(
            'data', 'metadata', 'eo', 'yb', 'sainEo', 'sainYb'
        ));

        return $pdf->stream('Laporan_PPUUN_' . $user->negeri . '_' . $tahun . '.pdf');
    }
}