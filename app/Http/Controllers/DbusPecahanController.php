<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dbus; 
use App\Models\DbusPegawai; 
use App\Models\DbusPecahanOS21;
use App\Models\DbusPecahanOs14; 
use App\Models\DbusPecahanOs15;
use App\Models\DbusPecahanOS22; 
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DbusPecahanController extends Controller
{
    // --- 1. PECAHAN PEGAWAI (OS11, OS12, OS13) ---
    public function editPegawai($kod, $tahun)
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

    public function storePegawai(Request $request)
    {
        $tahun = $request->tahun;
        $kod = $request->kod_transaksi; 
        
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
                    } elseif ($kod == 'OS12000') {
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
                    } elseif ($kod == 'OS13000') {
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
                $sums = DbusPegawai::where('tahun', $tahun)->selectRaw('SUM(itka) as itka, SUM(itp) as itp, SUM(el_keraian) as itk, SUM(itju) as itju, SUM(bipk) as bipk, SUM(bikppk) as bikppk, SUM(bitk) as bitk, SUM(bsh) as bsh, SUM(biw) as biw, SUM(el_lain) as lain, SUM(jumlah_elaun_setahun) as total')->first();
                Dbus::updateOrCreate(['kod_objek' => 'OL12101', 'tahun' => $tahun], ['perkara' => 'Elaun Khidmat Awam', 'jenis' => 'OL', 'jumlah' => $sums->itka * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12102', 'tahun' => $tahun], ['perkara' => 'Elaun Bantuan Sewa Rumah', 'jenis' => 'OL', 'jumlah' => $sums->itp * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12103', 'tahun' => $tahun], ['perkara' => 'Elaun Keraian', 'jenis' => 'OL', 'jumlah' => $sums->itk * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12106', 'tahun' => $tahun], ['perkara' => 'Imbuhan Tetap Jawatan Utama dan Gred Khas', 'jenis' => 'OL', 'jumlah' => $sums->itju * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12107', 'tahun' => $tahun], ['perkara' => 'Bayaran Insentif Perkhidmatan Kritikal', 'jenis' => 'OL', 'jumlah' => $sums->bipk * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12108', 'tahun' => $tahun], ['perkara' => 'Bayaran Insentif Khas Pegawai Profesional', 'jenis' => 'OL', 'jumlah' => $sums->bikppk * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12109', 'tahun' => $tahun], ['perkara' => 'Bayaran Insentif Tugas Kewangan', 'jenis' => 'OL', 'jumlah' => $sums->bitk * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OL12199', 'tahun' => $tahun], ['perkara' => 'Elaun Tetap Lain', 'jenis' => 'OL', 'jumlah' => ($sums->bsh + $sums->biw + $sums->lain) * 12]); 
                Dbus::updateOrCreate(['kod_objek' => 'OS12000', 'tahun' => $tahun], ['perkara' => 'ELAUN DAN IMBUHAN TETAP', 'jenis' => 'OS', 'jumlah' => $sums->total]);
            } elseif ($kod == 'OS13000') {
                $total = DbusPegawai::where('tahun', $tahun)->sum('kwsp_total');
                Dbus::updateOrCreate(['kod_objek' => 'OL13101', 'tahun' => $tahun], ['perkara' => 'Sumbangan Berkanun Untuk Kakitangan Awam - KWSP', 'jenis' => 'OL', 'jumlah' => $total]);
                Dbus::updateOrCreate(['kod_objek' => 'OS13000', 'tahun' => $tahun], ['perkara' => 'SUMBANGAN BERKANUN UNTUK KAKITANGAN', 'jenis' => 'OS', 'jumlah' => $total]);
            }
        });
        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', 'Data berjaya dikemaskini!');
    }

    // --- 2. PECAHAN OT (OS14000) ---
    public function editOt(Request $request, $kod)
    {
        $tahun = $request->tahun ?? date('Y');
        $dbusData = Dbus::where('kod_objek', $kod)->where('tahun', $tahun)->first();

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
        $pecahanData = DbusPecahanOs14::where('tahun', $tahun)->get();
        return view('pentadbiran.dbus.pecahan_OS14000', compact('dbusData', 'tahun', 'pecahanData'));
    }

    public function updateOt(Request $request)
    {
        $clean = fn($v) => (float) str_replace(',', '', $v ?? '0');

        $validatedData = $request->validate([
            'id' => ['required', Rule::exists('dbuses', 'id')],
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
            DbusPecahanOs14::where('tahun', $tahun)->delete();
            foreach ($validatedData['pecahan'] as $pecahanData) {
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
            $dbus->update(['jumlah' => $masterTotal]);
            Dbus::updateOrCreate(
                ['kod_objek' => 'OS14000', 'tahun' => $tahun],
                ['perkara' => 'BAYARAN LEBIH MASA', 'jenis' => 'OS', 'jumlah' => $masterTotal]
            );
        });
        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', "Rekod OT dikemaskini.");
    }

    // --- 3. PECAHAN OS15 (OS15000) ---
    public function editOs15(Request $request, $kod)
    {
        $tahun = $request->tahun ?? date('Y');
        $ol_kod = 'OL15119'; 
        $dbusData = Dbus::where('kod_objek', $ol_kod)->where('tahun', $tahun)->first();

        if (!$dbusData) {
             $info = $this->findObjekInfo($ol_kod);
             $dbusData = Dbus::create([
                 'kod_objek' => $ol_kod, 
                 'tahun' => $tahun, 
                 'perkara' => $info['perkara'] ?? 'Faedah Kewangan Lain', 
                 'jenis' => 'OL', 
                 'jumlah' => 0.00
             ]);
        }
        $pecahanData = DbusPecahanOs15::where('tahun', $tahun)->get();
        return view('pentadbiran.dbus.pecahan_OS15000', compact('dbusData', 'tahun', 'pecahanData', 'kod'));
    }
    
    public function updateOs15(Request $request)
    {
        $validatedData = $request->validate([
            'master_id' => ['required', Rule::exists('dbuses', 'id')], 
            'master_grand_total' => 'required|numeric|min:0', 
            'pecahan' => 'required|array',
            'pecahan.*.kod_ol' => 'required|string',
            'pecahan.*.sub_kod' => 'nullable|string',
            'pecahan.*.butiran' => 'nullable|string|max:255',
            'pecahan.*.anggaran' => 'nullable|numeric|min:0',
            'pecahan.*.bil_unit' => 'nullable|integer|min:0',
            'pecahan.*.catatan' => 'nullable|string|max:255',
        ]);
        
        $master_dbus = Dbus::find($validatedData['master_id']);
        $tahun = $master_dbus->tahun;

        DB::transaction(function () use ($master_dbus, $tahun, $validatedData) {
            
            DbusPecahanOs15::where('tahun', $tahun)->delete();
            
            foreach ($validatedData['pecahan'] as $pecahanData) {
                $anggaran = $pecahanData['anggaran'] ?? 0;
                $bil_unit = $pecahanData['bil_unit'] ?? 0;
                $jumlah_pecahan = $anggaran * $bil_unit;

                DbusPecahanOs15::create([
                    'tahun' => $tahun,
                    'kod_ol' => $pecahanData['kod_ol'],
                    'sub_kod' => $pecahanData['sub_kod'],
                    'butiran' => $pecahanData['butiran'],
                    'anggaran' => $anggaran,
                    'bil_unit' => $bil_unit,
                    'jumlah_pecahan' => $jumlah_pecahan,
                    'catatan' => $pecahanData['catatan'],
                ]);
            }

            $olSums = DbusPecahanOs15::where('tahun', $tahun)
                        ->select('kod_ol', DB::raw('SUM(jumlah_pecahan) as total'))
                        ->groupBy('kod_ol')
                        ->get();
            
            $grandTotal = 0;

            foreach ($olSums as $sum) {
                $grandTotal += $sum->total;
                $perkara = Dbus::where('kod_objek', $sum->kod_ol)->where('tahun', $tahun)->value('perkara') ?? 'Butiran ' . $sum->kod_ol;

                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    [
                        'perkara' => $perkara, 
                        'jenis' => 'OL', 
                        'jumlah' => $sum->total
                    ]
                );
            }
            
            Dbus::updateOrCreate(
                ['kod_objek' => 'OS15000', 'tahun' => $tahun],
                ['perkara' => 'FAEDAH-FAEDAH KEWANGAN YANG LAIN', 'jenis' => 'OS', 'jumlah' => $grandTotal]
            );

            $master_dbus->update(['jumlah' => $grandTotal]); 
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', "Rekod OS15 berjaya dikemaskini.");
    }
    
    // --- 4. PECAHAN PERJALANAN (OS21000) ---

    public function editOs21000($kod, $tahun)
    {
        $dbusData = Dbus::where('kod_objek', $kod)->where('tahun', $tahun)->first();

        if (!$dbusData) {
            $info = $this->findObjekInfo($kod);
            $dbusData = Dbus::create([
                'kod_objek' => $kod,
                'tahun' => $tahun,
                'perkara' => $info['perkara'] ?? 'PERBELANJAAN PERJALANAN & SARA HIDUP',
                'jenis' => 'OS',
                'jumlah' => 0.00
            ]);
        }

        $pecahanData = DbusPecahanOS21::where('dbus_id', $dbusData->id)->get();
        
        $pecahanMap = $pecahanData->keyBy('sub_kod')->toArray();

        return view('pentadbiran.dbus.pecahan_OS21000', compact('dbusData', 'pecahanMap', 'kod', 'tahun'));
    }

    public function updateOs21000(Request $request)
    {
        $masterId = $request->input('master_id');
        $dbusMaster = Dbus::findOrFail($masterId);
        $tahun = $dbusMaster->tahun;
        $pecahanInput = $request->input('data');
        $grandTotal = 0;
        
        // Kadar Elaun (OL21101 & OL21102)
        $kadar = [
            // --- KADAR SEMINAR (_S) ---
            'OL21101_GUG_S' => 115.00, 'OL21101_GUB_S' => 100.00, 'OL21101_G14_S' => 85.00, 'OL21101_G11_S' => 60.00, 'OL21101_G09_S' => 45.00, 'OL21101_G05_S' => 40.00, 'OL21101_G01_S' => 40.00,
            'OL21102_GUG_S' => 400.00, 'OL21102_GUB_S' => 400.00, 'OL21102_G14_S' => 350.00, 'OL21102_G11_S' => 145.00, 'OL21102_G09_S' => 130.00, 'OL21102_G05_S' => 80.00, 'OL21102_G01_S' => 65.00,
            'OL21106_GUB_S' => 650.00, 'OL21106_G14_S' => 650.00, 'OL21106_G11_S' => 650.00, 'OL21106_G09_S' => 650.00, 'OL21106_G01_S' => 650.00, // Flight Rates
            'OL21104_TRANS_S' => 1.00, // Transport (Use input as cost)

            // --- KADAR TUGAS RASMI (_R) ---
            'OL21101_GUG_R' => 115.00, 'OL21101_GUB_R' => 100.00, 'OL21101_G14_R' => 85.00, 'OL21101_G11_R' => 60.00, 'OL21101_G09_R' => 45.00, 'OL21101_G05_R' => 40.00, 'OL21101_G01_R' => 40.00,
            'OL21102_GUG_R' => 400.00, 'OL21102_GUB_R' => 400.00, 'OL21102_G14_R' => 350.00, 'OL21102_G11_R' => 145.00, 'OL21102_G09_R' => 130.00, 'OL21102_G05_R' => 80.00, 'OL21102_G01_R' => 65.00,
            'OL21106_GUB_R' => 650.00, 'OL21106_G14_R' => 650.00, 'OL21106_G11_R' => 650.00, 'OL21106_G09_R' => 650.00, 'OL21106_G01_R' => 650.00, // Flight Rates
            'OL21104_TRANS_R' => 1.00, // Transport (Use input as cost)
        ];

        DB::transaction(function () use ($dbusMaster, $pecahanInput, $kadar, &$grandTotal, $tahun) {
            
            DbusPecahanOS21::where('dbus_id', $dbusMaster->id)->delete(); 

            if ($pecahanInput) {
                foreach ($pecahanInput as $subKod => $data) {
                    $customKos = isset($data['kos']) ? (float)$data['kos'] : 0; 
                    
                    $bilOrang = isset($data['orang']) ? (int)$data['orang'] : 0;
                    $bilHari = isset($data['hari']) ? (int)$data['hari'] : 0; 
                    
                    $olKod = substr($subKod, 0, 7); 
                    $kadarSemasa = $kadar[$subKod] ?? 0; 
                    
                    $jumlahPerbelanjaan = 0;
                    $bilHariSimpan = $bilHari;
                    $anggaranSimpan = 0;

                    if (str_contains($subKod, 'OL21106')) { // KAPAL TERBANG
                        $kosUnit = ($customKos > 0) ? $customKos : $kadarSemasa;
                        $jumlahPerbelanjaan = $kosUnit * $bilOrang * $bilHari; 
                        $anggaranSimpan = $kosUnit;

                    } elseif (str_contains($subKod, 'TRANS')) { // PENGANGKUTAN
                        $kosTotal = (float)$data['orang']; 
                        $kekerapan = (float)$data['hari'];
                        
                        $jumlahPerbelanjaan = $kosTotal * $kekerapan;
                        $bilOrang = 0; 
                        $bilHariSimpan = $kekerapan; 
                        $anggaranSimpan = $kosTotal;

                    } else { // MAKAN & HOTEL
                        $jumlahPerbelanjaan = $bilOrang * $bilHari * $kadarSemasa;
                        $anggaranSimpan = $kadarSemasa;
                    }
                    
                    if ($jumlahPerbelanjaan > 0) {
                        DbusPecahanOS21::create([
                            'dbus_id' => $dbusMaster->id,
                            'kod_ol' => $olKod,
                            'sub_kod' => $subKod, 
                            'butiran' => $subKod,
                            'bil_orang' => $bilOrang,
                            'bil_hari' => $bilHariSimpan, 
                            'anggaran' => $anggaranSimpan, 
                            'jumlah' => $jumlahPerbelanjaan,
                        ]);
                        $grandTotal += $jumlahPerbelanjaan;
                    }
                }
            }
            
            $dbusMaster->jumlah = $grandTotal; 
            $dbusMaster->save();

            Dbus::updateOrCreate(['kod_objek' => 'OS21000', 'tahun' => $tahun], ['perkara' => 'PERBELANJAAN PERJALANAN & SARA HIDUP', 'jenis' => 'OS', 'jumlah' => $grandTotal]);
            
            $olSums = DbusPecahanOS21::where('dbus_id', $dbusMaster->id)->select('kod_ol', DB::raw('SUM(jumlah) as total'))->groupBy('kod_ol')->get();
            foreach($olSums as $sum) {
                $info = $this->findObjekInfo($sum->kod_ol);
                Dbus::updateOrCreate(['kod_objek' => $sum->kod_ol, 'tahun' => $tahun], ['perkara' => $info['perkara'], 'jenis' => 'OL', 'jumlah' => $sum->total]);
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])
                         ->with('success', 'Anggaran OS21000 berjaya dikemaskini. Jumlah: RM' . number_format($grandTotal, 2));
    }

    // --- 5. PENGANGKUTAN BARANG (OS22000) ---

    public function editOs22000($kod, $tahun)
    {
        // 1. Cari atau Cipta Data Induk (OS22000)
        $dbusData = Dbus::where('kod_objek', $kod)->where('tahun', $tahun)->first();

        if (!$dbusData) {
            $info = $this->findObjekInfo($kod);
            $dbusData = Dbus::create([
                'kod_objek' => $kod,
                'tahun' => $tahun,
                'perkara' => $info['perkara'] ?? 'PENGANGKUTAN BARANG-BARANG',
                'jenis' => 'OS',
                'jumlah' => 0.00
            ]);
        }

        // 2. Ambil data pecahan
        $pecahanData = DbusPecahanOS22::where('dbus_id', $dbusData->id)->get();
        $pecahanMap = $pecahanData->keyBy('sub_kod')->toArray();

        // 3. Hantar ke View
        return view('pentadbiran.dbus.pecahan_OS22000', compact('dbusData', 'pecahanMap', 'kod', 'tahun'));
    }

    public function updateOs22000(Request $request)
    {
        $masterId = $request->input('master_id');
        $dbusMaster = Dbus::findOrFail($masterId);
        $tahun = $dbusMaster->tahun;
        $pecahanInput = $request->input('data');
        $grandTotal = 0;

        // Senarai Kadar (Hardcoded ikut Blade View)
        $kadarMap = [
            // Gred Utama / Khas A
            'OL22155_GUA_B' => 600, 'OL22155_GUA_R' => 1200, 'OL22155_GUA_BS' => 800, 'OL22155_GUA_RS' => 1600,
            // Gred Utama / Khas B/C
            'OL22155_GUB_B' => 500, 'OL22155_GUB_R' => 1000, 'OL22155_GUB_BS' => 700, 'OL22155_GUB_RS' => 1600,
            // Gred 53 dan 54
            'OL22155_G53_B' => 500, 'OL22155_G53_R' => 1000, 'OL22155_G53_BS' => 700, 'OL22155_G53_RS' => 1600,
            // Gred 45 hingga 52
            'OL22155_G45_B' => 450, 'OL22155_G45_R' => 900, 'OL22155_G45_BS' => 650, 'OL22155_G45_RS' => 1300,
            // Gred 43 dan 44
            'OL22155_G43_B' => 400, 'OL22155_G43_R' => 800, 'OL22155_G43_BS' => 600, 'OL22155_G43_RS' => 1200,
            // Gred 41 dan 42
            'OL22155_G41_B' => 350, 'OL22155_G41_R' => 700, 'OL22155_G41_BS' => 550, 'OL22155_G41_RS' => 1100,
            // Gred 31 hingga 40
            'OL22155_G31_B' => 300, 'OL22155_G31_R' => 600, 'OL22155_G31_BS' => 400, 'OL22155_G31_RS' => 800,
            // Gred 27 hingga 30
            'OL22155_G27_B' => 250, 'OL22155_G27_R' => 500, 'OL22155_G27_BS' => 350, 'OL22155_G27_RS' => 700,
            // Gred 21 hingga 26
            'OL22155_G21_B' => 200, 'OL22155_G21_R' => 400, 'OL22155_G21_BS' => 300, 'OL22155_G21_RS' => 600,
            // Gred 17 hingga 20
            'OL22155_G17_B' => 180, 'OL22155_G17_R' => 360, 'OL22155_G17_BS' => 250, 'OL22155_G17_RS' => 500,
            // Gred 13 hingga 16
            'OL22155_G13_B' => 150, 'OL22155_G13_R' => 300, 'OL22155_G13_BS' => 200, 'OL22155_G13_RS' => 400,
            // Gred 1 hingga 12
            'OL22155_G01_B' => 100, 'OL22155_G01_R' => 200, 'OL22155_G01_BS' => 150, 'OL22155_G01_RS' => 300,
        ];

        DB::transaction(function () use ($dbusMaster, $pecahanInput, $kadarMap, &$grandTotal, $tahun) {
            
            // 1. Padam data lama
            DbusPecahanOS22::where('dbus_id', $dbusMaster->id)->delete();

            // 2. Simpan data baru
            if ($pecahanInput) {
                foreach ($pecahanInput as $subKod => $data) {
                    
                    // Input daripada form Blade adalah 'orang'
                    $bilOrang = isset($data['orang']) ? (int)$data['orang'] : 0;
                    
                    // Kadar diambil daripada map controller
                    $anggaran = $kadarMap[$subKod] ?? 0;
                    
                    $jumlahPerbelanjaan = $anggaran * $bilOrang;
                    
                    // Ambil Kod OL (OL22155) dari subkod
                    $olKod = substr($subKod, 0, 7); 

                    if ($jumlahPerbelanjaan > 0) {
                        DbusPecahanOS22::create([
                            'dbus_id' => $dbusMaster->id,
                            'kod_ol' => $olKod,
                            'sub_kod' => $subKod,
                            'butiran' => $subKod, // Boleh tukar nama cantik jika perlu
                            'anggaran' => $anggaran,
                            'bil_unit' => $bilOrang, // Simpan sebagai bil_unit dalam DB
                            'jumlah' => $jumlahPerbelanjaan
                        ]);
                        $grandTotal += $jumlahPerbelanjaan;
                    }
                }
            }

            // 3. Update Master Table (DBUSES)
            $dbusMaster->jumlah = $grandTotal;
            $dbusMaster->save();

            // Update Induk OS22000
            Dbus::updateOrCreate(
                ['kod_objek' => 'OS22000', 'tahun' => $tahun],
                ['perkara' => 'PENGANGKUTAN BARANG-BARANG', 'jenis' => 'OS', 'jumlah' => $grandTotal]
            );

            // Update OL (Group Sums)
            $olSums = DbusPecahanOS22::where('dbus_id', $dbusMaster->id)
                        ->select('kod_ol', DB::raw('SUM(jumlah) as total'))
                        ->groupBy('kod_ol')
                        ->get();

            foreach($olSums as $sum) {
                $info = $this->findObjekInfo($sum->kod_ol);
                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    ['perkara' => $info['perkara'] ?? 'Pecahan OL', 'jenis' => 'OL', 'jumlah' => $sum->total]
                );
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])
                         ->with('success', 'Anggaran OS22000 berjaya dikemaskini. Jumlah: RM' . number_format($grandTotal, 2));
    }

    // --- HELPER FUNCTIONS ---
    
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
        // Struktur OS D'BUS anda (Diringkaskan)
        return [
            'OA10000' => [
                'perkara' => 'EMOLUMEN', 'jenis' => 'OA',
                'items' => [/* ... OS11000 - OS15000 ... */]
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
                    // ... OS22000 - OS29000 ...
                ]
            ],
        ];
    }
}