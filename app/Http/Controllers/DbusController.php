<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dbus;
use Illuminate\Support\Facades\DB;

class DbusController extends Controller
{
public function index(Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));
        $grandTotal = 0;

        // 1. Dapatkan Struktur
        $structure = $this->getDbusStructure();

        // 2. Dapatkan Data dari DB
        $dbusRecords = Dbus::where('tahun', $tahun)->get()->keyBy('kod_objek');

        // 3. MAPPING DATA (FORCE CALCULATION)
        foreach ($structure as $oaKey => &$oa) {
            $oaTotal = 0;

            if (isset($oa['items'])) {
                foreach ($oa['items'] as $osKey => &$os) {
                    $osTotal = 0;

                    if (isset($os['items'])) {
                        foreach ($os['items'] as $groupKey => &$group) {
                            $groupTotal = 0;

                            if (isset($group['items'])) {
                                foreach ($group['items'] as $olKey => &$ol) {
                                    // Ambil nilai terus dari rekod OL dalam DBUS table
                                    $amount = isset($dbusRecords[$olKey]) ? $dbusRecords[$olKey]->jumlah : 0;
                                    
                                    $ol['jumlah'] = $amount;
                                    $groupTotal += $amount;
                                }
                            }
                            // Update total Group
                            $group['jumlah'] = $groupTotal;
                            $osTotal += $groupTotal;
                        }
                    }
                    // Update total OS
                    $os['jumlah'] = $osTotal;
                    $oaTotal += $osTotal;
                }
            }
            // Update total OA
            $oa['jumlah'] = $oaTotal;
            $grandTotal += $oaTotal;
        }

        return view('pentadbiran.dbus.index', compact('structure', 'grandTotal', 'tahun'));
    }

    // --- FUNGSI UPDATE AJAX (INLINE EDITING) ---
    public function updateOaAm(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'oa_kod' => 'required|string', 
            'oa_am_value' => 'required|numeric|min:0',
        ]);

        $tahun = $request->input('tahun');
        $oaKod = $request->input('oa_kod');
        $newValue = $request->input('oa_am_value');
        
        $info = $this->findObjekInfo($oaKod);
        if (!$info) {
             return response()->json(['success' => false, 'message' => 'Kod tidak sah'], 404);
        }

        try {
            Dbus::updateOrCreate(
                ['kod_objek' => $oaKod, 'tahun' => $tahun],
                [
                    'perkara' => $info['perkara'], 
                    'jenis' => 'OA',
                    'jumlah' => $newValue
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => "Berjaya dikemaskini.",
                'new_value' => $newValue
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // --- HELPER: CARI INFO ---
    private function findObjekInfo($searchKod) {
        $structure = $this->getDbusStructure();
        if(isset($structure[$searchKod])) return $structure[$searchKod];

        foreach ($structure as $oa) {
            if(isset($oa['items'][$searchKod])) return $oa['items'][$searchKod];
            foreach ($oa['items'] as $os) {
                if(isset($os['items'][$searchKod])) return $os['items'][$searchKod];
            }
        }
        return null;
    }

    public function cetakPdf(Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));
        $grandTotal = 0;

        // 1. Dapatkan Struktur & Data (Sama seperti Index)
        $structure = $this->getDbusStructure();
        $dbusRecords = Dbus::where('tahun', $tahun)->get()->keyBy('kod_objek');

        // 2. MAPPING DATA (LOGIK SAMA MACAM INDEX)
        foreach ($structure as $oaKey => &$oa) {
            $oaTotal = 0;
            if (isset($oa['items'])) {
                foreach ($oa['items'] as $osKey => &$os) {
                    $osTotal = 0;
                    if (isset($os['items'])) {
                        foreach ($os['items'] as $groupKey => &$group) {
                            $groupTotal = 0;
                            if (isset($group['items'])) {
                                foreach ($group['items'] as $olKey => &$ol) {
                                    $amount = isset($dbusRecords[$olKey]) ? $dbusRecords[$olKey]->jumlah : 0;
                                    $ol['jumlah'] = $amount;
                                    $groupTotal += $amount;
                                }
                            }
                            $group['jumlah'] = $groupTotal;
                            $osTotal += $groupTotal;
                        }
                    }
                    $os['jumlah'] = $osTotal;
                    $oaTotal += $osTotal;
                }
            }
            $oa['jumlah'] = $oaTotal;
            $grandTotal += $oaTotal;
        }

        // 3. JANA PDF
        $pdf = \PDF::loadView('pentadbiran.dbus.cetak_pdf', compact('structure', 'grandTotal', 'tahun'));
        
        // Set Kertas A4 Landscape
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('Anggaran_Belanja_' . $tahun . '.pdf');
    }
private function getDbusStructure()
    {
        return [
            // ============================================================
            // 1. OA10000: EMOLUMEN
            // ============================================================
            'OA10000' => [
                'perkara' => 'EMOLUMEN', 'jenis' => 'OA', 'items' => [
                    'OS11000' => ['perkara' => 'GAJI DAN UPAHAN', 'jenis' => 'OS', 'items' => [
                        '11100' => ['perkara' => 'GAJI KAKITANGAN AWAM', 'jenis' => 'GRP', 'items' => [
                            'OL11101' => ['perkara' => 'Gaji Biasa Kakitangan Awam'],
                        ]]
                    ]],
                    'OS12000' => ['perkara' => 'ELAUN DAN IMBUHAN TETAP', 'jenis' => 'OS', 'items' => [
                        '12100' => ['perkara' => 'ELAUN TETAP KAKITANGAN AWAM', 'jenis' => 'GRP', 'items' => [
                            'OL12101' => ['perkara' => 'Elaun Khidmat Awam'],
                            'OL12102' => ['perkara' => 'Elaun Bantuan Sewa Rumah'],
                            'OL12103' => ['perkara' => 'Elaun Keraian'],
                            'OL12106' => ['perkara' => 'Imbuhan Tetap Jawatan Utama'],
                            'OL12107' => ['perkara' => 'Bayaran Insentif Perkhidmatan Kritikal'],
                            'OL12108' => ['perkara' => 'Bayaran Insentif Khas (0.00)'],
                            'OL12109' => ['perkara' => 'Bayaran Insentif Tugas Kewangan'],
                            'OL12199' => ['perkara' => 'Elaun Tetap Lain'],
                        ]]
                    ]],
                    'OS13000' => ['perkara' => 'SUMBANGAN BERKANUN', 'jenis' => 'OS', 'items' => [
                        '13100' => ['perkara' => 'SUMBANGAN BERKANUN', 'jenis' => 'GRP', 'items' => [
                            'OL13101' => ['perkara' => 'KWSP'],
                        ]]
                    ]],
                    'OS14000' => ['perkara' => 'BAYARAN LEBIH MASA', 'jenis' => 'OS', 'items' => [
                        '14100' => ['perkara' => 'BAYARAN LEBIH MASA', 'jenis' => 'GRP', 'items' => [
                            'OL14101' => ['perkara' => 'Bayaran Lebih Masa Kakitangan Awam'],
                        ]]
                    ]],
                    'OS15000' => ['perkara' => 'FAEDAH KEWANGAN LAIN', 'jenis' => 'OS', 'items' => [
                        '15100' => ['perkara' => 'FAEDAH KEWANGAN', 'jenis' => 'GRP', 'items' => [
                            'OL15101' => ['perkara' => 'Bayaran Utiliti (0.00)'],
                            'OL15102' => ['perkara' => 'Bayaran Balik Pasport/Lesen'],
                            'OL15110' => ['perkara' => 'Alat Komunikasi (0.00)'],
                            'OL15111' => ['perkara' => 'Kemudahan Perubatan'],
                            'OL15112' => ['perkara' => 'Elaun Pakaian'],
                            'OL15113' => ['perkara' => 'APC (0.00)'],
                            'OL15114' => ['perkara' => 'Tambang Pengangkutan'],
                            'OL15119' => ['perkara' => 'Faedah Kewangan Lain'],
                        ]]
                    ]],
                ]
            ],

            // ============================================================
            // 2. OA20000: PERKHIDMATAN & BEKALAN
            // ============================================================
            'OA20000' => [
                'perkara' => 'PERKHIDMATAN & BEKALAN', 'jenis' => 'OA', 'items' => [
                    'OS21000' => ['perkara' => 'PERJALANAN & SARA HIDUP', 'jenis' => 'OS', 'items' => [
                        '21100' => ['perkara' => 'PERJALANAN DALAM NEGERI', 'jenis' => 'GRP', 'items' => [
                            'OL21101' => ['perkara' => 'Makanan & Minuman'],
                            'OL21102' => ['perkara' => 'Penginapan'],
                            'OL21104' => ['perkara' => 'Tambang Bas/Teksi'],
                            'OL21106' => ['perkara' => 'Kapal Terbang'],
                        ]]
                    ]],
                    'OS22000' => ['perkara' => 'PENGANGKUTAN BARANG', 'jenis' => 'OS', 'items' => [
                        '22100' => ['perkara' => 'PENGANGKUTAN BARANG', 'jenis' => 'GRP', 'items' => [
                            'OL22155' => ['perkara' => 'Elaun Perpindahan'],
                        ]]
                    ]],
                    'OS23000' => ['perkara' => 'PERHUBUNGAN DAN UTILITI', 'jenis' => 'OS', 'items' => [
                        '23100' => ['perkara' => 'PERHUBUNGAN', 'jenis' => 'GRP', 'items' => [
                            'OL23101' => ['perkara' => 'Pos'],
                            'OL23102' => ['perkara' => 'Telefon'],
                            'OL23103' => ['perkara' => 'Internet/Telex'],
                            'OL23199' => ['perkara' => 'Perkhidmatan Lain'],
                        ]],
                        '23200' => ['perkara' => 'UTILITI', 'jenis' => 'GRP', 'items' => [
                            'OL23201' => ['perkara' => 'Elektrik'],
                            'OL23202' => ['perkara' => 'Air'],
                            'OL23204' => ['perkara' => 'Pembentungan'],
                        ]]
                    ]],
                    'OS24000' => ['perkara' => 'SEWAAN', 'jenis' => 'OS', 'items' => [
                        '24200' => ['perkara' => 'SEWAAN BANGUNAN', 'jenis' => 'GRP', 'items' => [
                            'OL24201' => ['perkara' => 'Sewa Bangunan Kediaman'],
                            'OL24202' => ['perkara' => 'Sewa Bangunan Pejabat'],
                            'OL24299' => ['perkara' => 'Sewa Bangunan Lain'],
                        ]],
                        '24300' => ['perkara' => 'SEWAAN KENDERAAN', 'jenis' => 'GRP', 'items' => [
                            'OL24301' => ['perkara' => 'Sewa Kenderaan Penumpang'],
                            'OL24305' => ['perkara' => 'Sewa Kenderaan Konsesi'],
                            'OL24399' => ['perkara' => 'Sewa Kenderaan Lain'],
                        ]],
                        '24500' => ['perkara' => 'SEWA ALAT PEJABAT', 'jenis' => 'GRP', 'items' => [
                            'OL24501' => ['perkara' => 'Sewa Alat Pejabat'],
                            'OL24502' => ['perkara' => 'Sewa Perabot'],
                        ]],
                        '24600' => ['perkara' => 'SEWA ALAT ELEKTRONIK', 'jenis' => 'GRP', 'items' => [
                            'OL24699' => ['perkara' => 'Sewa Elektronik Lain'],
                        ]],
                        '24700' => ['perkara' => 'SEWA ALAT ELEKTRIK', 'jenis' => 'GRP', 'items' => [
                            'OL24799' => ['perkara' => 'Sewa Elektrik Lain'],
                        ]],
                    ]],
                    'OS25000' => ['perkara' => 'MAKANAN DAN MINUMAN', 'jenis' => 'OS', 'items' => [
                        '25000' => ['perkara' => 'MAKANAN DAN MINUMAN', 'jenis' => 'GRP', 'items' => [
                            'OL25499' => ['perkara' => 'Makanan Lain'],
                            'OL25601' => ['perkara' => 'Minuman'],
                        ]]
                    ]],
                    'OS26000' => ['perkara' => 'BAHAN MENTAH & PENYELENGGARAAN', 'jenis' => 'OS', 'items' => [
                        '26100' => ['perkara' => 'ALAT-ALAT GANTI', 'jenis' => 'GRP', 'items' => [
                            'OL26121' => ['perkara' => 'Alat Ganti Pejabat'],
                            'OL26126' => ['perkara' => 'Alat Ganti Elektronik'],
                            'OL26131' => ['perkara' => 'Alat Ganti Elektrik'],
                        ]],
                        '26200' => ['perkara' => 'PETROLEUM & BAHAN API', 'jenis' => 'GRP', 'items' => [
                            'OL26201' => ['perkara' => 'Petrol'],
                            'OL26202' => ['perkara' => 'Diesel'],
                            'OL26206' => ['perkara' => 'Pelincir'],
                            'OL26299' => ['perkara' => 'Bahan Api Lain'],
                        ]],
                        '26700' => ['perkara' => 'KIMIA & BAHAN KIMIA', 'jenis' => 'GRP', 'items' => [
                            'OL26701' => ['perkara' => 'Bahan Pengecat'],
                        ]]
                    ]],
                    'OS27000' => ['perkara' => 'BEKALAN DAN BAHAN LAIN', 'jenis' => 'OS', 'items' => [
                        '27100' => ['perkara' => 'BEKALAN PEJABAT', 'jenis' => 'GRP', 'items' => [
                            'OL27101' => ['perkara' => 'Surat Khabar'],
                            'OL27102' => ['perkara' => 'Alat Tulis'],
                            'OL27103' => ['perkara' => 'Alat Tulis Komputer'],
                            'OL27199' => ['perkara' => 'Bekalan Pejabat Lain'],
                        ]],
                        '27200' => ['perkara' => 'BEKALAN AM', 'jenis' => 'GRP', 'items' => [
                            'OL27299' => ['perkara' => 'Bekalan Am'],
                        ]],
                        '27400' => ['perkara' => 'PERUBATAN', 'jenis' => 'GRP', 'items' => [
                            'OL27401' => ['perkara' => 'Ubat'],
                            'OL27499' => ['perkara' => 'Perubatan Lain'],
                        ]],
                        '27600' => ['perkara' => 'PAKAIAN', 'jenis' => 'GRP', 'items' => [
                            'OL27605' => ['perkara' => 'Pakaian Seragam'],
                            'OL27699' => ['perkara' => 'Pakaian Lain'],
                        ]],
                    ]],
                    'OS28000' => ['perkara' => 'PENYELENGGARAAN', 'jenis' => 'OS', 'items' => [
                        '28100' => ['perkara' => 'BANGUNAN', 'jenis' => 'GRP', 'items' => [
                            'OL28102' => ['perkara' => 'Bangunan Pejabat'],
                            'OL28199' => ['perkara' => 'Bangunan Lain'],
                        ]],
                        '28300' => ['perkara' => 'KENDERAAN', 'jenis' => 'GRP', 'items' => [
                            'OL28301' => ['perkara' => 'Kenderaan Penumpang'],
                            'OL28307' => ['perkara' => 'Kenderaan Konsesi'],
                        ]],
                        '28500' => ['perkara' => 'ALAT PEJABAT', 'jenis' => 'GRP', 'items' => [
                            'OL28501' => ['perkara' => 'Alat Pejabat'],
                            'OL28599' => ['perkara' => 'Perabot Lain'],
                        ]],
                        '28600' => ['perkara' => 'ALAT ELEKTRONIK', 'jenis' => 'GRP', 'items' => [
                            'OL28601' => ['perkara' => 'Komputer'],
                            'OL28699' => ['perkara' => 'Elektronik Lain'],
                        ]],
                        '28700' => ['perkara' => 'ALAT ELEKTRIK', 'jenis' => 'GRP', 'items' => [
                            'OL28701' => ['perkara' => 'Hawa Dingin'],
                            'OL28799' => ['perkara' => 'Elektrik Lain'],
                        ]],
                        '28800' => ['perkara' => 'ALAT PERHUBUNGAN', 'jenis' => 'GRP', 'items' => [
                            'OL28801' => ['perkara' => 'Telefon/Telex'],
                            'OL28899' => ['perkara' => 'Perhubungan Lain'],
                        ]],
                        '28900' => ['perkara' => 'ASET LAIN', 'jenis' => 'GRP', 'items' => [
                            'OL28911' => ['perkara' => 'Pembersihan'],
                        ]],
                    ]],
'OS29000' => [
                'perkara' => 'PERKHIDMATAN IKTISAS & LAIN-LAIN', 
                'jenis' => 'OA', 
                'jumlah' => 0, 
                'items' => [
                    '29100' => ['perkara' => 'PERKHIDMATAN DIBELI (Latihan, Makanan, dll)', 'jenis' => 'GRP', 'items' => [
                        'OL29107' => ['perkara' => 'Latihan & Bayaran Pensyarah'],
                        'OL29126' => ['perkara' => 'Persediaan Makanan'],
                        'OL29199' => ['perkara' => 'Perkhidmatan Lain (Tol/Meter Reading)'],
                    ]],
                    '29200' => ['perkara' => 'PERCETAKAN', 'jenis' => 'GRP', 'items' => [
                        'OL29201' => ['perkara' => 'Penerbitan Kerajaan'],
                        'OL29202' => ['perkara' => 'Borang & Kepala Surat'],
                        'OL29299' => ['perkara' => 'Percetakan Lain'],
                    ]],
                    '29300' => ['perkara' => 'KAKITANGAN KONTRAK (SAMBILAN)', 'jenis' => 'GRP', 'items' => [
                        'OL29301' => ['perkara' => 'Gaji & Upah Sambilan'],
                        'OL29302' => ['perkara' => 'Elaun Lebih Masa Sambilan'],
                        'OL29303' => ['perkara' => 'Sumbangan KWSP Sambilan'],
                    ]],
                    '29400' => ['perkara' => 'KERAIAN & HOSPITALITI', 'jenis' => 'GRP', 'items' => [
                        'OL29401' => ['perkara' => 'Makan & Minum (Jemputan Luar)'],
                        'OL29402' => ['perkara' => 'Elaun Penginapan'],
                        'OL29411' => ['perkara' => 'Keraian Pejabat (Mesyuarat)'],
                        'OL29499' => ['perkara' => 'Bayaran Lain (Tip/Tol)'],
                        ]],
                    ]],
                ]
            ],
        ];
    }
    
    // Fungsi dummy untuk elak error route jika ada
    public function create() { return view('pentadbiran.dbus.create'); }
    public function store(Request $request) { return redirect()->back(); }
    public function edit(Request $request) { return redirect()->back(); }
}