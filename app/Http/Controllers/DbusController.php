<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dbus;
use App\Models\DbusPegawai; 
use App\Models\DbusPecahanOs14; // 🔥 MODEL BARU DIIMPORT 🔥
use Illuminate\Support\Facades\DB;

class DbusController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', 2026);
        $dbData = Dbus::where('tahun', $tahun)->pluck('jumlah', 'kod_objek')->toArray();
        $structure = $this->getDbusStructure();
        $grandTotal = 0;

        foreach ($structure as $oaKey => &$oa) {
            $oaTotal = 0;
            foreach ($oa['items'] as $osKey => &$os) {
                $osTotal = 0;
                foreach ($os['items'] as $olKey => &$ol) {
                    $amount = $dbData[$olKey] ?? 0;
                    $ol['jumlah'] = $amount;
                    $osTotal += $amount;
                }
                $os['jumlah'] = ($dbData[$osKey] ?? 0) > 0 ? $dbData[$osKey] : $osTotal;
                $oaTotal += $os['jumlah'];
            }
            $oa['jumlah'] = ($dbData[$oaKey] ?? 0) > 0 ? $dbData[$oaKey] : $oaTotal;
            $grandTotal += $oa['jumlah'];
        }

        return view('pentadbiran.dbus.index', compact('structure', 'tahun', 'grandTotal'));
    }

    public function create()
    {
        $tahun = 2026; 
        $structure = $this->getDbusStructure();
        $existingData = []; 
        return view('pentadbiran.dbus.create', compact('tahun', 'structure', 'existingData'));
    }

    public function edit(Request $request)
    {
        $tahun = $request->tahun;
        $kategori = $request->kategori; 
        $structure = $this->getDbusStructure();
        $filteredStructure = isset($structure[$kategori]) ? [$kategori => $structure[$kategori]] : [];
        $existingData = Dbus::where('tahun', $tahun)->pluck('jumlah', 'kod_objek')->toArray();

        return view('pentadbiran.dbus.create', [
            'tahun' => $tahun,
            'structure' => $filteredStructure,
            'existingData' => $existingData,
            'isEdit' => true
        ]);
    }

    public function store(Request $request)
    {
        $tahun = $request->tahun;
        $request->validate(['tahun' => 'required|integer', 'data' => 'array']);

        DB::transaction(function () use ($request, $tahun) {
            if ($request->has('data')) {
                foreach ($request->data as $kod => $jumlah) {
                    $info = $this->findObjekInfo($kod);
                    $cleanJumlah = (float) str_replace(',', '', $jumlah ?? '0');

                    Dbus::updateOrCreate(
                        ['kod_objek' => $kod, 'tahun' => $tahun],
                        ['perkara' => $info['perkara'] ?? 'ITEM', 'jenis' => $info['jenis'] ?? 'OL', 'jumlah' => $cleanJumlah]
                    );
                }
            }
        });
        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', 'Data berjaya disimpan.');
    }

    /**
     * Cari dan paparkan borang edit untuk item OL14101 (Bayaran Lebih Masa).
     */
    public function editOl14101(Request $request, $kod)
    {
        $tahun = $request->tahun ?? date('Y');
        
        $dbusData = Dbus::where('kod_objek', $kod)
                        ->where('tahun', $tahun)
                        ->first();

        if (!$dbusData) {
            $info = $this->findObjekInfo($kod);
            $dbusData = Dbus::create([
                'kod_objek' => $kod, 
                'tahun' => $tahun, 
                'perkara' => $info['perkara'] ?? 'Bayaran Lebih Masa Kakitangan Awam', 
                'jenis' => 'OL', 
                'jumlah' => 0.00
            ]);
        }

        // 🔥 AMBIL DATA PECAHAN DARI MODEL BARU 🔥
        $pecahanData = DbusPecahanOs14::where('tahun', $tahun)->get();

        // Memuatkan view pecahan_OS14000
        return view('pentadbiran.dbus.pecahan_OS14000', compact('dbusData', 'tahun', 'pecahanData'));
    }

    /**
     * Kendalikan kemaskini data untuk item OL14101 dan pecahannya.
     */
    public function updateOl14101(Request $request)
    {
        $clean = fn($v) => (float) str_replace(',', '', $v ?? '0');

        $validatedData = $request->validate([
            'id' => 'required|exists:dbus,id', 

            'master_grand_total' => 'required|numeric|min:0',
            'pecahan' => 'required|array',
            'pecahan.*.gred' => 'required|string',
            'pecahan.*.anggaran' => 'required|numeric|min:0',
            'pecahan.*.orang' => 'required|integer|min:0',
            'pecahan.*.bulan' => 'required|integer|min:0',
            'pecahan.*.jumlah' => 'required|numeric|min:0',
            'pecahan.*.catatan' => 'nullable|string|max:255',
        ]);
        
        $dbus = Dbus::find($validatedData['id']);
        $tahun = $dbus->tahun;
        $masterTotal = $validatedData['master_grand_total']; 

        DB::transaction(function () use ($dbus, $masterTotal, $tahun, $validatedData) {
            
            // 1. PADAM SEMUA REKOD PECAHAN OT LAMA DARI JADUAL BARU
            DbusPecahanOs14::where('tahun', $tahun)->delete();
            
            // 2. SIMPAN DATA PECAHAN KE JADUAL BARU
            foreach ($validatedData['pecahan'] as $pecahanData) {
                // Hanya simpan jika ada anggaran atau bilangan pegawai/jumlah > 0
                if ($pecahanData['anggaran'] > 0 || $pecahanData['orang'] > 0 || $pecahanData['jumlah'] > 0) {
                    DbusPecahanOs14::create([
                        'tahun' => $tahun,
                        'gred' => $pecahanData['gred'],
                        'anggaran' => $pecahanData['anggaran'], 
                        'bil_orang' => $pecahanData['orang'],
                        'bil_bulan' => $pecahanData['bulan'],
                        'jumlah_pecahan' => $pecahanData['jumlah'],
                        'catatan' => $pecahanData['catatan'],
                    ]);
                }
            }

            // 3. UPDATE REKOD MASTER (OL14101)
            $dbus->update(['jumlah' => $masterTotal]);
            
            // 4. UPDATE REKOD OS INDUK (OS14000)
            Dbus::updateOrCreate(
                ['kod_objek' => 'OS14000', 'tahun' => $tahun],
                ['perkara' => 'BAYARAN LEBIH MASA', 'jenis' => 'OS', 'jumlah' => $masterTotal]
            );

        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', "Rekod {$dbus->kod_objek} dan pecahannya berjaya dikemaskini.");
    }


    public function editPecahan($kod, $tahun)
    {
        $pegawai = DbusPegawai::where('tahun', $tahun)->orderBy('id')->get();

        if ($kod == 'OS11000') return view('pentadbiran.dbus.pecahan_gaji', compact('pegawai', 'tahun', 'kod'));
        if ($kod == 'OS12000') return view('pentadbiran.dbus.pecahan_elaun', compact('pegawai', 'tahun', 'kod'));
        
        if ($kod == 'OS13000') {
            
            foreach ($pegawai as $p) {
                $gajiAsas = $p->gaji_2026 > 0 ? $p->gaji_2026 : $p->gaji_2025; 
                $elaunSebulan = $p->jumlah_elaun_sebulan;

                $p->auto_gaji_elaun_semasa = $gajiAsas + $elaunSebulan;
                $p->auto_gaji_elaun_baru = $p->auto_gaji_elaun_semasa; 
            }
            
            return view('pentadbiran.dbus.pecahan_kwsp', compact('pegawai', 'tahun', 'kod'));
        }

        return redirect()->back()->with('error', 'Fungsi pecahan belum tersedia.');
    }

    public function storePecahan(Request $request)
    {
        $tahun = $request->tahun;
        $kod = $request->kod_transaksi; 
        
        // ... Logik storePecahan sedia ada (tidak diubah)
        DB::transaction(function () use ($request, $tahun, $kod) {
            
            if ($request->has('pegawai')) {
                foreach ($request->pegawai as $data) {
                    if(empty($data['nama'])) continue;

                    $clean = fn($v) => (float) str_replace(',', '', $v ?? '0');

                    $updateData = [
                        'tahun' => $tahun,
                        'nama_pegawai' => $data['nama'],
                        'gred' => $data['gred'],
                        'catatan' => $data['catatan'] ?? null,
                    ];

                    if ($kod == 'OS11000') {
                        $updateData += [
                            'gaji_2025' => $clean($data['gaji_2025']),
                            'gaji_2026' => $clean($data['gaji_2026']),
                            'jumlah_keseluruhan' => $clean($data['jumlah_keseluruhan']),
                            'kenaikan_peratus' => $clean($data['kenaikan_peratus']),
                            'kenaikan_gaji' => $clean($data['kenaikan_gaji']),
                            'bulan_pergerakan' => $data['bulan'] ?? null, 
                        ];
                    }
                    
                    if ($kod == 'OS12000') {
                        $updateData += [
                            'itka' => $clean($data['itka']),
                            'itp' => $clean($data['itp']),
                            'el_keraian' => $clean($data['el_keraian']), 
                            'itju' => $clean($data['itju']),
                            'bipk' => $clean($data['bipk']), 
                            'bikppk' => $clean($data['bikppk']), 
                            'bitk' => $clean($data['bitk']),
                            'bsh' => $clean($data['bsh']), 
                            'biw' => $clean($data['biw']),
                            'el_lain' => $clean($data['el_lain']),
                            'jumlah_elaun_sebulan' => $clean($data['jumlah_elaun_sebulan']),
                            'jumlah_elaun_setahun' => $clean($data['jumlah_elaun_setahun']),
                        ];
                    }

                    if ($kod == 'OS13000') {
                        $updateData += [
                            'kwsp_gaji_semasa'    => $clean($data['kwsp_gaji_semasa']),
                            'kwsp_peratus_semasa' => (int) $data['kwsp_peratus_semasa'],
                            'kwsp_bulan_semasa'   => (int) $data['kwsp_bulan_semasa'],
                            'kwsp_gaji_baru'      => $clean($data['kwsp_gaji_baru']),
                            'kwsp_peratus_baru'   => (int) $data['kwsp_peratus_baru'],
                            'kwsp_bulan_baru'     => (int) $data['kwsp_bulan_baru'],
                            'kwsp_total'          => $clean($data['kwsp_total']),
                        ];
                    }

                    DbusPegawai::updateOrCreate(['id' => $data['id'] ?? null], $updateData);
                }
            }

            if ($kod == 'OS11000') {
                $total = DbusPegawai::where('tahun', $tahun)->sum('jumlah_keseluruhan');
                Dbus::updateOrCreate(['kod_objek' => 'OL11101', 'tahun' => $tahun], ['perkara' => 'Gaji Biasa Kakitangan Awam', 'jenis' => 'OL', 'jumlah' => $total]);
                Dbus::updateOrCreate(['kod_objek' => 'OS11000', 'tahun' => $tahun], ['perkara' => 'GAJI DAN UPAHAN', 'jenis' => 'OS', 'jumlah' => $total]);
            
            } elseif ($kod == 'OS12000') {
                $sums = DbusPegawai::where('tahun', $tahun)
                    ->selectRaw('SUM(itka) as itka, SUM(itp) as itp, SUM(el_keraian) as itk, SUM(itju) as itju, SUM(bipk) as bipk, SUM(bikppk) as bikppk, SUM(bitk) as bitk, SUM(bsh) as bsh, SUM(biw) as biw, SUM(el_lain) as lain, SUM(jumlah_elaun_setahun) as total')
                    ->first();

                Dbus::updateOrCreate(['kod_objek' => 'OL12101', 'tahun' => $tahun], ['perkara' => 'Elaun Khidmat Awam', 'jenis' => 'OL', 'jumlah' => $sums->itka * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12102', 'tahun' => $tahun], ['perkara' => 'Elaun Bantuan Sewa Rumah', 'jenis' => 'OL', 'jumlah' => $sums->itp * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12103', 'tahun' => $tahun], ['perkara' => 'Elaun Keraian', 'jenis' => 'OL', 'jumlah' => $sums->itk * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12106', 'tahun' => $tahun], ['perkara' => 'Imbuhan Tetap Jawatan Utama dan Gred Khas', 'jenis' => 'OL', 'jumlah' => $sums->itju * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12107', 'tahun' => $tahun], ['perkara' => 'Bayaran Insentif Perkhidmatan Kritikal', 'jenis' => 'OL', 'jumlah' => $sums->bipk * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12108', 'tahun' => $tahun], ['perkara' => 'Bayaran Insentif Khas Pegawai Profesional', 'jenis' => 'OL', 'jumlah' => $sums->bikppk * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12109', 'tahun' => $tahun], ['perkara' => 'Bayaran Insentif Tugas Kewangan', 'jenis' => 'OL', 'jumlah' => $sums->bitk * 12]); 
                
                $totalLain = ($sums->bsh + $sums->biw + $sums->lain) * 12;
                Dbus::updateOrCreate(['kod_objek' => 'OL12199', 'tahun' => $tahun], ['perkara' => 'Elaun Tetap Lain', 'jenis' => 'OL', 'jumlah' => $totalLain]); 

                Dbus::updateOrCreate(['kod_objek' => 'OS12000', 'tahun' => $tahun], ['perkara' => 'ELAUN DAN IMBUHAN TETAP', 'jenis' => 'OS', 'jumlah' => $sums->total]);

            } elseif ($kod == 'OS13000') {
                $total = DbusPegawai::where('tahun', $tahun)->sum('kwsp_total');
                Dbus::updateOrCreate(['kod_objek' => 'OL13101', 'tahun' => $tahun], ['perkara' => 'Sumbangan Berkanun Untuk Kakitangan Awam - KWSP', 'jenis' => 'OL', 'jumlah' => $total]);
                Dbus::updateOrCreate(['kod_objek' => 'OS13000', 'tahun' => $tahun], ['perkara' => 'SUMBANGAN BERKANUN UNTUK KAKITANGAN', 'jenis' => 'OS', 'jumlah' => $total]);
            }

        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', 'Data berjaya dikemaskini!');
    }

    private function findObjekInfo($searchKod) {
        $structure = $this->getDbusStructure();
        foreach ($structure as $oaKey => $oa) {
            if($oaKey == $searchKod) return $oa;
            foreach ($oa['items'] as $osKey => $os) {
                if($osKey == $searchKod) return $os;
                foreach ($os['items'] as $olKey => $ol) {
                    if($olKey == $searchKod) return $ol;
                }
            }
        }
        return null;
    }

    private function getDbusStructure()
    {
        return [
            'OA10000' => [
                'perkara' => 'EMOLUMEN', 'jenis' => 'OA',
                'items' => [
                    'OS11000' => [
                        'perkara' => 'GAJI DAN UPAHAN', 'jenis' => 'OS',
                        'items' => [
                            'OL11101' => ['perkara' => 'Gaji Biasa Kakitangan Awam', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS12000' => [
                        'perkara' => 'ELAUN DAN IMBUHAN TETAP', 'jenis' => 'OS',
                        'items' => [
                            'OL12101' => ['perkara' => 'Elaun Khidmat Awam', 'jenis' => 'OL'],
                            'OL12102' => ['perkara' => 'Elaun Bantuan Sewa Rumah', 'jenis' => 'OL'],
                            'OL12103' => ['perkara' => 'Elaun Keraian', 'jenis' => 'OL'],
                            'OL12106' => ['perkara' => 'Imbuhan Tetap Jawatan Utama dan Gred Khas', 'jenis' => 'OL'],
                            'OL12107' => ['perkara' => 'Bayaran Insentif Perkhidmatan Kritikal', 'jenis' => 'OL'],
                            'OL12108' => ['perkara' => 'Bayaran Insentif Khas Pegawai Profesional', 'jenis' => 'OL'],
                            'OL12109' => ['perkara' => 'Bayaran Insentif Tugas Kewangan', 'jenis' => 'OL'],
                            'OL12199' => ['perkara' => 'Elaun Tetap Lain', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS13000' => [
                        'perkara' => 'SUMBANGAN BERKANUN UNTUK KAKITANGAN', 'jenis' => 'OS',
                        'items' => [
                            'OL13101' => ['perkara' => 'Sumbangan Berkanun (KWSP)', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS14000' => [
                        'perkara' => 'BAYARAN LEBIH MASA', 'jenis' => 'OS',
                        'items' => [
                            'OL14101' => ['perkara' => 'Bayaran Lebih Masa Kakitangan Awam', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS15000' => [
                        'perkara' => 'FAEDAH-FAEDAH KEWANGAN YANG LAIN', 'jenis' => 'OS',
                        'items' => [
                            'OL15101' => ['perkara' => 'Bayaran dan Bayaran Balik Utiliti', 'jenis' => 'OL'],
                            'OL15102' => ['perkara' => 'Bayaran Balik Pasport/Lesen/Yuran', 'jenis' => 'OL'],
                            'OL15110' => ['perkara' => 'Pemberian Alat Komunikasi', 'jenis' => 'OL'],
                            'OL15111' => ['perkara' => 'Bayaran Kemudahan Perubatan', 'jenis' => 'OL'],
                            'OL15112' => ['perkara' => 'Pelbagai Elaun Pakaian', 'jenis' => 'OL'],
                            'OL15113' => ['perkara' => 'Pemberian Anugerah Perkhidmatan Cemerlang (APC)', 'jenis' => 'OL'],
                            'OL15114' => ['perkara' => 'Pelbagai Kemudahan Tambang Pengangkutan', 'jenis' => 'OL'],
                            'OL15119' => ['perkara' => 'Faedah Kewangan Lain / Elaun Perkakasan', 'jenis' => 'OL'],
                        ]
                    ],
                ]
            ],
            'OA20000' => [
                'perkara' => 'PERKHIDMATAN & BEKALAN', 'jenis' => 'OA',
                'items' => [
                    'OS21000' => [
                        'perkara' => 'PERBELANJAAN PERJALANAN & SARA HIDUP', 'jenis' => 'OS',
                        'items' => [
                            'OL21101' => ['perkara' => 'Makanan & Minuman', 'jenis' => 'OL'],
                            'OL21102' => ['perkara' => 'Penginapan', 'jenis' => 'OL'],
                            'OL21104' => ['perkara' => 'Elaun Perjalanan, Tambang Bas & Teksi', 'jenis' => 'OL'],
                            'OL21106' => ['perkara' => 'Bayaran Kapal Terbang', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS22000' => [
                        'perkara' => 'PENGANGKUTAN BARANG-BARANG', 'jenis' => 'OS',
                        'items' => [
                            'OL22155' => ['perkara' => 'Pengangkutan Barang / Elaun Perpindahan', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS23000' => [
                        'perkara' => 'PERHUBUNGAN DAN UTILITI', 'jenis' => 'OS',
                        'items' => [
                            'OL23101' => ['perkara' => 'Bayaran Pos Biasa, Mel Udara & Ekspress', 'jenis' => 'OL'],
                            'OL23102' => ['perkara' => 'Telefon, Sewaan Dan Pemasangan', 'jenis' => 'OL'],
                            'OL23103' => ['perkara' => 'Telex, Telegraf, Kabel, Wireless', 'jenis' => 'OL'],
                            'OL23199' => ['perkara' => 'Perkhidmatan Perhubungan Lain', 'jenis' => 'OL'],
                            'OL23201' => ['perkara' => 'Utiliti - Elektrik', 'jenis' => 'OL'],
                            'OL23202' => ['perkara' => 'Utiliti - Air', 'jenis' => 'OL'],
                            'OL23204' => ['perkara' => 'Utiliti - Pembentungan', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS24000' => [
                        'perkara' => 'SEWAAN', 'jenis' => 'OS',
                        'items' => [
                            'OL24201' => ['perkara' => 'Sewa Bangunan Kediaman', 'jenis' => 'OL'],
                            'OL24202' => ['perkara' => 'Sewa Bangunan Pejabat', 'jenis' => 'OL'],
                            'OL24299' => ['perkara' => 'Sewa Bangunan Lain', 'jenis' => 'OL'],
                            'OL24301' => ['perkara' => 'Sewa Kenderaan Penumpang', 'jenis' => 'OL'],
                            'OL24305' => ['perkara' => 'Sewa Kenderaan Konsesi Spanco', 'jenis' => 'OL'],
                            'OL24399' => ['perkara' => 'Sewa Kenderaan Lain', 'jenis' => 'OL'],
                            'OL24501' => ['perkara' => 'Sewa Alat Kelengkapan Pejabat', 'jenis' => 'OL'],
                            'OL24502' => ['perkara' => 'Sewa Perabot & Lengkapan', 'jenis' => 'OL'],
                            'OL24699' => ['perkara' => 'Sewa Alat Kelengkapan Elektronik Lain', 'jenis' => 'OL'],
                            'OL24799' => ['perkara' => 'Sewa Alat Kelengkapan Elektrik Lain', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS25000' => [
                        'perkara' => 'BAHAN MAKANAN DAN MINUMAN', 'jenis' => 'OS',
                        'items' => [
                            'OL25499' => ['perkara' => 'Makanan-makanan Yang Lain', 'jenis' => 'OL'],
                            'OL25601' => ['perkara' => 'Minuman Tidak Berkabonat', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS26000' => [
                        'perkara' => 'BEKALAN BAHAN MENTAH & PENYELENGGARAAN', 'jenis' => 'OS',
                        'items' => [
                            'OL26121' => ['perkara' => 'Alat Ganti Kelengkapan Pejabat', 'jenis' => 'OL'],
                            'OL26126' => ['perkara' => 'Alat Ganti Kelengkapan Elektronik', 'jenis' => 'OL'],
                            'OL26131' => ['perkara' => 'Alat Ganti Kelengkapan Elektrik', 'jenis' => 'OL'],
                            'OL26201' => ['perkara' => 'Minyak Petrol', 'jenis' => 'OL'],
                            'OL26202' => ['perkara' => 'Minyak Diesel', 'jenis' => 'OL'],
                            'OL26206' => ['perkara' => 'Pelincir & Grease', 'jenis' => 'OL'],
                            'OL26299' => ['perkara' => 'Bahan Api Lain', 'jenis' => 'OL'],
                            'OL26701' => ['perkara' => 'Bahan Pengecat (Cat, Barnish)', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS27000' => [
                        'perkara' => 'BEKALAN DAN BAHAN LAIN', 'jenis' => 'OS',
                        'items' => [
                            'OL27101' => ['perkara' => 'Surat Khabar, Majalah, Warta', 'jenis' => 'OL'],
                            'OL27102' => ['perkara' => 'Alat Tulis Pejabat', 'jenis' => 'OL'],
                            'OL27103' => ['perkara' => 'Alat Tulis Komputer', 'jenis' => 'OL'],
                            'OL27199' => ['perkara' => 'Bekalan Pejabat Lain', 'jenis' => 'OL'],
                            'OL27299' => ['perkara' => 'Bekalan Am Lain', 'jenis' => 'OL'],
                            'OL27401' => ['perkara' => 'Ubat dan Dadah', 'jenis' => 'OL'],
                            'OL27499' => ['perkara' => 'Bekalan Perubatan Lain', 'jenis' => 'OL'],
                            'OL27605' => ['perkara' => 'Pakaian Seragam', 'jenis' => 'OL'],
                            'OL27699' => ['perkara' => 'Pakaian (Kasut, Jam, Tali Leher)', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS28000' => [
                        'perkara' => 'PENYELENGGARAAN & PEMBAIKAN', 'jenis' => 'OS',
                        'items' => [
                            'OL28102' => ['perkara' => 'Penyelenggaraan Bangunan Pejabat', 'jenis' => 'OL'],
                            'OL28199' => ['perkara' => 'Penyelenggaraan Bangunan Lain', 'jenis' => 'OL'],
                            'OL28301' => ['perkara' => 'Penyelenggaraan Kenderaan Penumpang', 'jenis' => 'OL'],
                            'OL28307' => ['perkara' => 'Kenderaan Konsesi SPANCO', 'jenis' => 'OL'],
                            'OL28501' => ['perkara' => 'Penyelenggaraan Alat Pejabat', 'jenis' => 'OL'],
                            'OL28599' => ['perkara' => 'Penyelenggaraan Perabot', 'jenis' => 'OL'],
                            'OL28601' => ['perkara' => 'Penyelenggaraan Komputer', 'jenis' => 'OL'],
                            'OL28699' => ['perkara' => 'Penyelenggaraan Elektronik Lain', 'jenis' => 'OL'],
                            'OL28701' => ['perkara' => 'Penyelenggaraan Hawa Dingin', 'jenis' => 'OL'],
                            'OL28799' => ['perkara' => 'Penyelenggaraan Elektrik Lain', 'jenis' => 'OL'],
                            'OL28801' => ['perkara' => 'Penyelenggaraan Telefon/Telex', 'jenis' => 'OL'],
                            'OL28899' => ['perkara' => 'Penyelenggaraan Perhubungan Lain', 'jenis' => 'OL'],
                            'OL28911' => ['perkara' => 'Perkhidmatan Pembersihan', 'jenis' => 'OL'],
                        ]
                    ],
                    'OS29000' => [
                        'perkara' => 'PERKHIDMATAN IKTISAS', 'jenis' => 'OS',
                        'items' => [
                            'OL29107' => ['perkara' => 'Perkhidmatan Latihan/Pensyarah', 'jenis' => 'OL'],
                            'OL29126' => ['perkara' => 'Perkhidmatan Persediaan Makanan', 'jenis' => 'OL'],
                            'OL29199' => ['perkara' => 'Perkhidmatan Lain (Meter Reading)', 'jenis' => 'OL'],
                            'OL29201' => ['perkara' => 'Penerbitan Kerajaan', 'jenis' => 'OL'],
                            'OL29202' => ['perkara' => 'Pencetakan Borang/Kad', 'jenis' => 'OL'],
                            'OL29299' => ['perkara' => 'Perkhidmatan Percetakan Lain', 'jenis' => 'OL'],
                            'OL29301' => ['perkara' => 'Gaji/Upahan (Pekerja Sambilan)', 'jenis' => 'OL'],
                            'OL29303' => ['perkara' => 'Sumbangan KWSP (Pekerja Sambilan)', 'jenis' => 'OL'],
                            'OL29401' => ['perkara' => 'Keraian - Makanan & Minuman', 'jenis' => 'OL'],
                            'OL29402' => ['perkara' => 'Keraian - Penginapan', 'jenis' => 'OL'],
                            'OL29411' => ['perkara' => 'Keraian Pejabat (Mesyuarat)', 'jenis' => 'OL'],
                            'OL29499' => ['perkara' => 'Bayaran Lain (Tol/Tip)', 'jenis' => 'OL'],
                        ]
                    ],
                ]
            ],
        ];
    }
}