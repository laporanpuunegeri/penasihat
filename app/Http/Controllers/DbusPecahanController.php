<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dbus; 
use App\Models\DbusPegawai; 
use App\Models\DbusPecahanOs14; 
use App\Models\DbusPecahanOs15;
use App\Models\DbusPecahanOS21;
use App\Models\DbusPecahanOS22;
use App\Models\DbusPecahanOS23; 
use App\Models\DbusPecahanOS24;
use App\Models\DbusPecahanOS25;
use App\Models\DbusPecahanOS26;
use App\Models\DbusPecahanOS27;
use App\Models\DbusPecahanOS28;
use App\Models\DbusPecahanOS29;
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

    // --- 6. PERHUBUNGAN DAN UTILITI (OS23000) - GABUNGAN 3 PDF ---

    public function editOs23000($kod, $tahun)
    {
        // 1. Setup Induk
        $dbusData = Dbus::where('kod_objek', $kod)->where('tahun', $tahun)->first();
        if (!$dbusData) {
            $info = $this->findObjekInfo($kod);
            $dbusData = Dbus::create([
                'kod_objek' => $kod,
                'tahun' => $tahun,
                'perkara' => $info['perkara'] ?? 'PERHUBUNGAN DAN UTILITI',
                'jenis' => 'OS',
                'jumlah' => 0.00
            ]);
        }

        // 2. Ambil data sedia ada
        $pecahanData = DbusPecahanOS23::where('dbus_id', $dbusData->id)->get();
        $pecahanMap = $pecahanData->keyBy('sub_kod')->toArray();

        // 3. Definisi Item Mengikut PDF

        // A. POS (OS23101) - Rujuk PDF OS23101
       // A. POS (OS23101) - Rujuk PDF OS23101 Penuh
        $itemsPos = [
            'POS BIASA' => [
                ['sub' => 'OL23101_POS_L_M',  'butiran' => 'Sampul (Flyer) Pos Laju bersaiz M', 'unit' => 'pack', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_POS_L_L',  'butiran' => 'Sampul (Flyer) Pos Laju bersaiz L', 'unit' => 'pack', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_POS_L_XL', 'butiran' => 'Sampul (Flyer) Pos Laju bersaiz XL', 'unit' => 'pack', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_STM_100',  'butiran' => 'Setem RM1.00', 'unit' => 'keping', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_STM_060',  'butiran' => 'Setem RM0.60', 'unit' => 'keping', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_STM_050',  'butiran' => 'Setem RM0.50', 'unit' => 'keping', 'kod_ol' => 'OL23101'], // Item Baru
            ],
            'MEL UDARA' => [
                ['sub' => 'OL23101_MEL_UD',   'butiran' => 'RM5.00 per gram', 'unit' => 'keping', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_MEL_LAIN', 'butiran' => 'Lain-Lain (Mel Udara)', 'unit' => 'keping', 'kod_ol' => 'OL23101'], // Item Baru
            ],
            'MEL BERDAFTAR & EKSPRESS' => [
                ['sub' => 'OL23101_MEL_D_AR', 'butiran' => 'Mel Berdaftar (Kad AR)', 'unit' => 'keping', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_POS_05',   'butiran' => 'Mel Berdaftar Pos Laju <0.50kg', 'unit' => 'keping', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_POS_075',  'butiran' => 'Mel Berdaftar Pos Laju <0.75kg', 'unit' => 'keping', 'kod_ol' => 'OL23101'], // Item Baru
                ['sub' => 'OL23101_POS_10',   'butiran' => 'Mel Berdaftar Pos Laju <1.0kg', 'unit' => 'keping', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_POS_125',  'butiran' => 'Mel Berdaftar Pos Laju <1.25kg', 'unit' => 'keping', 'kod_ol' => 'OL23101'], // Item Baru
                ['sub' => 'OL23101_POS_15',   'butiran' => 'Mel Berdaftar Pos Laju <1.5kg', 'unit' => 'keping', 'kod_ol' => 'OL23101'], // Item Baru
                ['sub' => 'OL23101_POS_175',  'butiran' => 'Mel Berdaftar Pos Laju <1.75kg', 'unit' => 'keping', 'kod_ol' => 'OL23101'], // Item Baru
                ['sub' => 'OL23101_POS_20_DOC', 'butiran' => 'Mel Berdaftar Pos Laju <2.0kg', 'unit' => 'keping', 'kod_ol' => 'OL23101'], // Item Baru
                ['sub' => 'OL23101_POS_30',   'butiran' => 'Mel Berdaftar Pos Laju <3.0kg', 'unit' => 'keping', 'kod_ol' => 'OL23101'], // Item Baru
            ],
            'POS BUNGKUSAN' => [
                ['sub' => 'OL23101_KOTAK_2',  'butiran' => 'Pos Laju 2.0kg (Kotak Oren)', 'unit' => 'unit', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_KOTAK_5',  'butiran' => 'Pos Laju 5.0kg (Kotak Oren)', 'unit' => 'unit', 'kod_ol' => 'OL23101'],
                ['sub' => 'OL23101_KOTAK_10', 'butiran' => 'Pos Laju 10.0kg (Kotak Oren)', 'unit' => 'unit', 'kod_ol' => 'OL23101'], // Item Baru
            ]
        ];

        // B. KOMUNIKASI (OS23102 & OS23103) - Rujuk PDF OS23102 Penuh
        $itemsKom = [
            'TELEFON (OL23102) - Sewaan & Pemasangan' => [
                ['sub' => 'OL23102_TM_PEJ',   'butiran' => 'Bil TM Pejabat', 'unit' => 'Unit', 'kod_ol' => 'OL23102'],
                ['sub' => 'OL23102_KB',       'butiran' => 'Ketua Bahagian', 'unit' => 'orang', 'kod_ol' => 'OL23102'],
                ['sub' => 'OL23102_TKB',      'butiran' => 'Timbalan Ketua Bahagian', 'unit' => 'orang', 'kod_ol' => 'OL23102'], // Item Baru
                ['sub' => 'OL23102_KU',       'butiran' => 'Ketua Unit', 'unit' => 'orang', 'kod_ol' => 'OL23102'], // Item Baru
            ],
            'TELEX, TELEGRAF, WIRELESS (OL23103)' => [
                ['sub' => 'OL23103_KB',       'butiran' => 'Ketua Bahagian', 'unit' => 'orang', 'kod_ol' => 'OL23103'],
                ['sub' => 'OL23103_TKB',      'butiran' => 'Timbalan Ketua Bahagian', 'unit' => 'orang', 'kod_ol' => 'OL23103'], // Item Baru
                ['sub' => 'OL23103_KU',       'butiran' => 'Ketua Unit', 'unit' => 'orang', 'kod_ol' => 'OL23103'], // Item Baru
                ['sub' => 'OL23103_PKA',      'butiran' => 'Pem. Khidmat Am (H1)', 'unit' => 'orang', 'kod_ol' => 'OL23103'],
            ],
            'PERKHIDMATAN LAIN (OL23199)' => [
                ['sub' => 'OL23199_LAIN_1',   'butiran' => 'Lain-lain 1', 'unit' => 'orang', 'kod_ol' => 'OL23199'],
                ['sub' => 'OL23199_LAIN_2',   'butiran' => 'Lain-lain 2', 'unit' => 'orang', 'kod_ol' => 'OL23199'], // Item Baru
            ]
        ];

        // C. UTILITI (OS23200) - Rujuk PDF OS23200 Penuh
        $itemsUtil = [
            'ELEKTRIK (OL23201)' => [
                ['sub' => 'OL23201_1', 'butiran' => 'Sila Nyatakan 1', 'kod_ol' => 'OL23201'],
                ['sub' => 'OL23201_2', 'butiran' => 'Sila Nyatakan 2', 'kod_ol' => 'OL23201'],
                ['sub' => 'OL23201_3', 'butiran' => 'Sila Nyatakan 3', 'kod_ol' => 'OL23201'],
            ],
            'AIR (OL23202)' => [
                ['sub' => 'OL23202_1', 'butiran' => 'Sila Nyatakan 1', 'kod_ol' => 'OL23202'],
                ['sub' => 'OL23202_2', 'butiran' => 'Sila Nyatakan 2', 'kod_ol' => 'OL23202'],
                ['sub' => 'OL23202_3', 'butiran' => 'Sila Nyatakan 3', 'kod_ol' => 'OL23202'],
            ],
            'PEMBENTUNGAN (OL23204)' => [
                ['sub' => 'OL23204_1', 'butiran' => 'Sila Nyatakan 1', 'kod_ol' => 'OL23204'],
                ['sub' => 'OL23204_2', 'butiran' => 'Sila Nyatakan 2', 'kod_ol' => 'OL23204'],
            ],
            'PERKHIDMATAN UTILITI LAIN (OL23299)' => [
                ['sub' => 'OL23299_1', 'butiran' => 'Sila Nyatakan 1', 'kod_ol' => 'OL23299'],
                ['sub' => 'OL23299_2', 'butiran' => 'Sila Nyatakan 2', 'kod_ol' => 'OL23299'],
                ['sub' => 'OL23299_3', 'butiran' => 'Sila Nyatakan 3', 'kod_ol' => 'OL23299'],
                ['sub' => 'OL23299_4', 'butiran' => 'Sila Nyatakan 4', 'kod_ol' => 'OL23299'],
                ['sub' => 'OL23299_5', 'butiran' => 'Sila Nyatakan 5', 'kod_ol' => 'OL23299'],
            ]
        ];
        return view('pentadbiran.dbus.pecahan_OS23000', compact(
            'dbusData', 'pecahanMap', 'kod', 'tahun', 
            'itemsPos', 'itemsKom', 'itemsUtil'
        ));
    }

    public function updateOs23000(Request $request)
    {
        $masterId = $request->input('master_id');
        $dbusMaster = Dbus::findOrFail($masterId);
        $tahun = $dbusMaster->tahun;
        $pecahanInput = $request->input('data'); 
        $grandTotal = 0;

        DB::transaction(function () use ($dbusMaster, $pecahanInput, &$grandTotal, $tahun) {
            
            DbusPecahanOS23::where('dbus_id', $dbusMaster->id)->delete();

            if ($pecahanInput) {
                foreach ($pecahanInput as $subKod => $data) {
                    
                    // Logic pengiraan: Kuantiti x Bulan x Kadar(Anggaran)
                    // Jika Utiliti (tiada kuantiti), kita set default 1
                    $kuantiti = isset($data['kuantiti']) ? (int)$data['kuantiti'] : 1;
                    $anggaran = isset($data['anggaran']) ? (float)$data['anggaran'] : 0;
                    $bilBulan = isset($data['bulan']) ? (int)$data['bulan'] : 12;
                    
                    $unit = $data['unit'] ?? null;
                    $noAkaun = $data['akaun'] ?? null;
                    $kodOl = $data['kod_ol'] ?? substr($subKod, 0, 7);
                    $butiran = $data['butiran'] ?? '';

                    $jumlah = $anggaran * $bilBulan * $kuantiti;

                    if ($jumlah > 0 || $anggaran > 0) {
                        DbusPecahanOS23::create([
                            'dbus_id' => $dbusMaster->id,
                            'kod_ol' => $kodOl,
                            'sub_kod' => $subKod,
                            'butiran' => $butiran,
                            'no_akaun' => $noAkaun,
                            'kuantiti' => $kuantiti,
                            'unit' => $unit,
                            'anggaran_sebulan' => $anggaran, // Ini adalah Kadar
                            'bil_bulan' => $bilBulan,
                            'tahun' => $tahun,
                            'jumlah' => $jumlah
                        ]);
                        $grandTotal += $jumlah;
                    }
                }
            }

            // Update Master & OL Sums
            $dbusMaster->jumlah = $grandTotal;
            $dbusMaster->save();

            $olSums = DbusPecahanOS23::where('dbus_id', $dbusMaster->id)
                        ->select('kod_ol', DB::raw('SUM(jumlah) as total'))
                        ->groupBy('kod_ol')
                        ->get();

            foreach($olSums as $sum) {
                // Mapping nama OL ikut PDF
                $perkara = 'Lain-lain';
                if($sum->kod_ol == 'OL23101') $perkara = 'Pos Biasa, Mel Udara & Berdaftar';
                if($sum->kod_ol == 'OL23102') $perkara = 'Telefon & Kos Pemasangan';
                if($sum->kod_ol == 'OL23103') $perkara = 'Telex, Telegraf & Wireless';
                if($sum->kod_ol == 'OL23199') $perkara = 'Perkhidmatan Perhubungan Lain';
                if($sum->kod_ol == 'OL23201') $perkara = 'Elektrik';
                if($sum->kod_ol == 'OL23202') $perkara = 'Air';
                if($sum->kod_ol == 'OL23204') $perkara = 'Pembentungan';
                if($sum->kod_ol == 'OL23299') $perkara = 'Utiliti Lain';

                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    ['perkara' => $perkara, 'jenis' => 'OL', 'jumlah' => $sum->total]
                );
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])
                         ->with('success', 'OS23000 berjaya dikemaskini. Jumlah: RM' . number_format($grandTotal, 2));
    }

    // --- 7. SEWAAN (OS24000) ---

public function editOs24000($kod, $tahun)
    {
        // 1. Setup Induk
        $dbusData = Dbus::where('kod_objek', $kod)->where('tahun', $tahun)->first();

        if (!$dbusData) {
            $info = $this->findObjekInfo($kod);
            $dbusData = Dbus::create([
                'kod_objek' => $kod,
                'tahun' => $tahun,
                'perkara' => $info['perkara'] ?? 'SEWAAN',
                'jenis' => 'OS',
                'jumlah' => 0.00
            ]);
        }

        // 2. Ambil data sedia ada
        $pecahanData = DbusPecahanOS24::where('dbus_id', $dbusData->id)->get();
        $pecahanMap = $pecahanData->keyBy('sub_kod')->toArray();

        // 3. DEFINISI ITEM (STRUKTUR PDF YANG TEPAT)
        $items = [
            // --- TAB 1: SEWAAN BANGUNAN (OS24200) - ADA SUB-GROUP ---
            '24200' => [
                'title' => 'SEWAAN BANGUNAN (24200)',
                'has_subgroups' => true, // Flag untuk Blade tahu ini ada sub-header
                'subgroups' => [
                    [
                        'title' => 'SEWAAN BANGUNAN KEDIAMAN (OL24201)',
                        'data' => [
                            ['sub' => 'OL24201_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24201', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24201_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24201', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24201_3', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24201', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24201_4', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24201', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ],
                    [
                        'title' => 'SEWAAN BANGUNAN PEJABAT (OL24202)',
                        'data' => [
                            ['sub' => 'OL24202_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24202', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24202_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24202', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24202_3', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24202', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24202_4', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24202', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ],
                    [
                        'title' => 'SEWAAN BANGUNAN (LAIN-LAIN) (OL24299)',
                        'data' => [
                            ['sub' => 'OL24299_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24299', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24299_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24299', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24299_3', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24299', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24299_4', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24299', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ]
                ]
            ],

            // --- TAB 2: KENDERAAN (OS24300) - ADA SUB-GROUP JUGA (IKUT PDF) ---
            '24300' => [
                'title' => 'SEWAAN KENDERAAN (24300)',
                'has_subgroups' => true,
                'subgroups' => [
                    [
                        'title' => 'SEWAAN PENUMPANG (OL24301)',
                        'data' => [
                            ['sub' => 'OL24301_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24301', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24301_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24301', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ],
                    [
                        'title' => 'SEWAAN KENDERAAN KONSESI SPANCO (OL24305)',
                        'data' => [
                            ['sub' => 'OL24305_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24305', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24305_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24305', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ],
                    [
                        'title' => 'SEWAAN KENDERAAN KENDERAAN LAIN (OL24399)',
                        'data' => [
                            ['sub' => 'OL24399_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24399', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24399_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24399', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ]
                ]
            ],

            // --- TAB 3: ALAT PEJABAT (OS24500) - ADA SUB-GROUP ---
            '24500' => [
                'title' => 'ALAT PEJABAT (24500)',
                'has_subgroups' => true,
                'subgroups' => [
                    [
                        'title' => 'SEWA ALAT KELENGKAPAN PEJABAT (OL24501)',
                        'data' => [
                            ['sub' => 'OL24501_PETI', 'butiran' => 'Peti Surat - Pos Malaysia', 'unit' => 'unit', 'kod_ol' => 'OL24501', 'editable' => false, 'q'=>1, 'b'=>1, 'a'=>250.00],
                            ['sub' => 'OL24501_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24501', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24501_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24501', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24501_3', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24501', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ],
                    [
                        'title' => 'SEWA PERABOT DAN LENGKAPAN (OL24502)',
                        'data' => [
                            ['sub' => 'OL24502_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24502', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24502_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24502', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24502_3', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24502', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ]
                ]
            ],

            // --- TAB 4: ELEKTRONIK (OS24600) - SATU GROUP SAHAJA (OL24699) ---
            '24600' => [
                'title' => 'ELEKTRONIK (24600)',
                'has_subgroups' => true,
                'subgroups' => [
                    [
                        'title' => 'SEWA ALAT KELENGKAPAN ELEKTRONIK YANG LAIN (OL24699)',
                        'data' => [
                            ['sub' => 'OL24699_FOTO', 'butiran' => 'Mesin Fotostat (Pejabat PPUUN Melaka Seri Negeri)', 'unit' => 'unit', 'kod_ol' => 'OL24699', 'editable' => false, 'q'=>1, 'b'=>12, 'a'=>1100.00],
                            ['sub' => 'OL24699_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24699', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24699_2', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24699', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                            ['sub' => 'OL24699_3', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24699', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ]
                ]
            ],

            // --- TAB 5: ELEKTRIK (OS24700) - SATU GROUP SAHAJA (OL24799) ---
            '24700' => [
                'title' => 'ELEKTRIK (24700)',
                'has_subgroups' => true,
                'subgroups' => [
                    [
                        'title' => 'SEWA ALAT KELENGKAPAN ELEKTRIK YANG LAIN (OL24799)',
                        'data' => [
                            ['sub' => 'OL24799_AIR', 'butiran' => 'Mesin Penapis Air', 'unit' => 'unit', 'kod_ol' => 'OL24799', 'editable' => false, 'q'=>3, 'b'=>12, 'a'=>120.00],
                            ['sub' => 'OL24799_1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL24799', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0],
                        ]
                    ]
                ]
            ],
        ];

        return view('pentadbiran.dbus.pecahan_OS24000', compact('dbusData', 'pecahanMap', 'kod', 'tahun', 'items'));
    }

public function updateOs24000(Request $request)
    {
        $masterId = $request->input('master_id');
        $dbusMaster = Dbus::findOrFail($masterId);
        $tahun = $dbusMaster->tahun;
        $pecahanInput = $request->input('data');
        $grandTotal = 0;

        DB::transaction(function () use ($dbusMaster, $pecahanInput, &$grandTotal, $tahun) {
            
            // 1. Padam data pecahan lama
            DbusPecahanOS24::where('dbus_id', $dbusMaster->id)->delete();

            // 2. Simpan data baru
            if ($pecahanInput) {
                foreach ($pecahanInput as $subKod => $data) {
                    $qty = isset($data['kuantiti']) ? (float)$data['kuantiti'] : 0;
                    $bulan = isset($data['bulan']) ? (float)$data['bulan'] : 0;
                    $anggaran = isset($data['anggaran']) ? (float)$data['anggaran'] : 0;
                    $butiran = $data['butiran'] ?? '';
                    $kodOl = $data['kod_ol'] ?? substr($subKod, 0, 7);
                    
                    // AMBIL DATA CATATAN
                    $catatan = $data['catatan'] ?? '';

                    $jumlah = $qty * $bulan * $anggaran;

                    // Simpan pecahan ke database
                    if ($jumlah > 0 || $qty > 0 || $anggaran > 0) {
                        DbusPecahanOS24::create([
                            'dbus_id' => $dbusMaster->id,
                            'kod_ol' => $kodOl,
                            'sub_kod' => $subKod,
                            'butiran' => $butiran,
                            'anggaran_sebulan' => $anggaran,
                            'bil_bulan' => $bulan,
                            'kuantiti' => $qty, // Kolum kuantiti
                            'tahun' => $tahun,
                            'jumlah' => $jumlah,
                            'catatan' => $catatan // ✅ SEKARANG BOLEH DISIMPAN
                        ]);
                        $grandTotal += $jumlah;
                    }
                }
            }

            // 3. Update Master Table
            $dbusMaster->jumlah = $grandTotal;
            $dbusMaster->save();

            Dbus::updateOrCreate(
                ['kod_objek' => 'OS24000', 'tahun' => $tahun],
                ['perkara' => 'SEWAAN', 'jenis' => 'OS', 'jumlah' => $grandTotal]
            );

            // 4. Reset OL
            $senaraiOL = ['OL24201', 'OL24202', 'OL24299', 'OL24301', 'OL24305', 'OL24399', 'OL24501', 'OL24502', 'OL24699', 'OL24799'];
            Dbus::whereIn('kod_objek', $senaraiOL)->where('tahun', $tahun)->update(['jumlah' => 0]);

            // 5. Update OL
            $olSums = DbusPecahanOS24::where('dbus_id', $dbusMaster->id)
                        ->select('kod_ol', DB::raw('SUM(jumlah) as total'))
                        ->groupBy('kod_ol')
                        ->get();

            foreach($olSums as $sum) {
                $info = $this->findObjekInfo($sum->kod_ol); 
                $perkara = $info['perkara'] ?? 'Sewaan';

                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    ['perkara' => $perkara, 'jenis' => 'OL', 'jumlah' => $sum->total]
                );
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])
                         ->with('success', 'OS24000 berjaya dikemaskini.');
    }

    // --- 8. BAHAN MAKANAN DAN MINUMAN (OS25000) ---

    public function editOs25000($kod, $tahun)
    {
        // 1. Setup Induk
        $dbusData = Dbus::where('kod_objek', $kod)->where('tahun', $tahun)->first();

        if (!$dbusData) {
            $info = $this->findObjekInfo($kod);
            $dbusData = Dbus::create([
                'kod_objek' => $kod,
                'tahun' => $tahun,
                'perkara' => $info['perkara'] ?? 'BAHAN MAKANAN DAN MINUMAN',
                'jenis' => 'OS',
                'jumlah' => 0.00
            ]);
        }

        // 2. Ambil data sedia ada
        // Menganggap anda mempunyai Model DbusPecahanOS25
        $pecahanData = DbusPecahanOS25::where('dbus_id', $dbusData->id)->get();
        $pecahanMap = $pecahanData->keyBy('sub_kod')->toArray();

        // 3. DEFINISI ITEM (Contoh Struktur Makanan & Minuman)
        $items = [
            '25400_MAKANAN' => [
                'title' => 'BAHAN MAKANAN LAIN (OL25499)',
                'data' => [
                    // Ini perlu diisi berdasarkan keperluan PDF anda
                    ['sub' => 'OL25499_1', 'butiran' => 'Sila Nyatakan (Makanan 1)', 'unit' => 'unit', 'kod_ol' => 'OL25499', 'editable' => true, 'q'=>0, 'b'=>12, 'a'=>0],
                    ['sub' => 'OL25499_2', 'butiran' => 'Sila Nyatakan (Makanan 2)', 'unit' => 'unit', 'kod_ol' => 'OL25499', 'editable' => true, 'q'=>0, 'b'=>12, 'a'=>0],
                ]
            ],
            '25600_MINUMAN' => [
                'title' => 'MINUMAN TIDAK BERKABONAT (OL25601)',
                'data' => [
                    ['sub' => 'OL25601_1', 'butiran' => 'Sila Nyatakan (Minuman 1)', 'unit' => 'unit', 'kod_ol' => 'OL25601', 'editable' => true, 'q'=>0, 'b'=>12, 'a'=>0],
                ]
            ],
        ];

        return view('pentadbiran.dbus.pecahan_OS25000', compact('dbusData', 'pecahanMap', 'kod', 'tahun', 'items'));
    }
    
public function updateOs25000(Request $request)
    {
        // 1. Pengesahan Data
        $validated = $request->validate([
            // ✅ PEMBETULAN UTAMA: Guna 'dbuses' (plural) untuk pengesahan table
            'master_id' => 'required|exists:dbuses,id', 
            
            'master_kod' => 'required|string',
            'tahun' => 'required|integer',
            'data' => 'required|array',
            'data.*.butiran' => 'nullable|string|max:255',
            'data.*.kod_ol' => 'required|string',
            'data.*.kuantiti' => 'nullable|numeric|min:0',
            'data.*.bulan' => 'nullable|integer|min:0|max:12',
            'data.*.anggaran' => 'nullable|numeric|min:0',
            'data.*.catatan' => 'nullable|string|max:255',
        ]);
        
        $masterKod = $validated['master_kod'];
        $tahun = $validated['tahun'];
        
        // Guna $validated['master_id']
        $dbusMaster = Dbus::findOrFail($validated['master_id']); 
        $grandTotal = 0;

        DB::transaction(function () use ($dbusMaster, $validated, &$grandTotal, $tahun) {
            
            // 1. Padam data lama
            DbusPecahanOS25::where('dbus_id', $dbusMaster->id)->delete(); 

            // 2. Simpan data baru & Kira Grand Total
            if (isset($validated['data'])) {
                foreach ($validated['data'] as $kod_pecahan_sub => $data) {
                    $qty = (float)($data['kuantiti'] ?? 0);
                    $bulan = (float)($data['bulan'] ?? 0);
                    $anggaran = (float)($data['anggaran'] ?? 0);
                    $butiran = $data['butiran'] ?? '';
                    $kodOl = $data['kod_ol'] ?? substr($kod_pecahan_sub, 0, 7);
                    $catatan = $data['catatan'] ?? '';

                    $jumlah = $qty * $bulan * $anggaran;

                    if ($jumlah > 0 || $qty > 0 || $anggaran > 0) {
                        DbusPecahanOS25::create([
                            'dbus_id' => $dbusMaster->id,
                            'kod_ol' => $kodOl,
                            'kod_pecahan_sub' => $kod_pecahan_sub, 
                            'butiran' => $butiran,
                            'anggaran_sebulan' => $anggaran,
                            'bil_bulan' => $bulan,
                            'kuantiti' => $qty,
                            'tahun' => $tahun,
                            'jumlah' => $jumlah,
                            'catatan' => $catatan
                        ]);
                        $grandTotal += $jumlah;
                    }
                }
            }
            
            // 3. Reset dan Update Rekod Dbus Utama
            $dbusMaster->update(['jumlah' => $grandTotal]);

            Dbus::updateOrCreate(
                ['kod_objek' => 'OS25000', 'tahun' => $tahun],
                ['perkara' => 'BAHAN MAKANAN DAN MINUMAN', 'jenis' => 'OS', 'jumlah' => $grandTotal]
            );

            // 4. Update OL Sums
            $senaraiOL = ['OL25499', 'OL25601'];
            Dbus::whereIn('kod_objek', $senaraiOL)->where('tahun', $tahun)->update(['jumlah' => 0]);
            
            $olSums = DbusPecahanOS25::where('dbus_id', $dbusMaster->id)
                        ->select('kod_ol', DB::raw('SUM(jumlah) as total'))
                        ->groupBy('kod_ol')
                        ->get();

            foreach($olSums as $sum) {
                $info = $this->findObjekInfo($sum->kod_ol); 
                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    ['perkara' => $info['perkara'] ?? 'Makanan/Minuman', 'jenis' => 'OL', 'jumlah' => $sum->total]
                );
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])
                         ->with('success', 'OS25000 berjaya dikemaskini.');
    }

    // --- 9. BEKALAN BAHAN MENTAH (OS26000) ---
    
    // DbusPecahanController.php (Gantikan method editOs26000)

    public function editOs26000($kod, $tahun)
    {
        $dbusData = Dbus::where('kod_objek', $kod)->where('tahun', $tahun)->first();
        if (!$dbusData) {
            $info = $this->findObjekInfo($kod);
            $dbusData = Dbus::create([
                'kod_objek' => $kod,
                'tahun' => $tahun,
                'perkara' => $info['perkara'] ?? 'BEKALAN BAHAN MENTAH',
                'jenis' => 'OS',
                'jumlah' => 0.00
            ]);
        }

        $pecahanData = DbusPecahanOS26::where('dbus_id', $dbusData->id)->get();
        $pecahanMap = $pecahanData->keyBy('kod_pecahan_sub')->toArray();

        // 3. DEFINISI ITEM (DATA MENGIKUT PDF OS26100 & OS26200)
        $items = [
            // --- TAB 1: BAHAN BAKAR KENDERAAN (OS26200) ---
            '26200_BAKAR' => [
                'title' => 'BAHAN API DAN PELINCIR (OS26200)',
                'has_subgroups' => true,
                'subgroups' => [
                    // MINYAK PETROL KENDERAAN JABATAN/RASMI (OL26201)
                    [
                        'title' => 'MINYAK PETROL KENDERAAN JABATAN/RASMI (OL26201)',
                        'data' => [
                            ['sub' => 'OL26201_X70P', 'butiran' => 'Proton X70 Pej Penasihat', 'unit' => 'L', 'kod_ol' => 'OL26201', 'editable' => false, 'q'=>1, 'b'=>12, 'a'=>500.00], // Dari PDF
                            ['sub' => 'OL26201_X70N', 'butiran' => 'Proton X70 PPN', 'unit' => 'L', 'kod_ol' => 'OL26201', 'editable' => false, 'q'=>1, 'b'=>12, 'a'=>250.00], // Dari PDF
                            ['sub' => 'OL26201_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'L', 'kod_ol' => 'OL26201', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                        ]
                    ],
                    // MINYAK DISEL KENDERAAN JABATAN/RASMI (OL26202)
                    [
                        'title' => 'MINYAK DISEL KENDERAAN JABATAN/RASMI (OL26202)',
                        'data' => [
                            ['sub' => 'OL26202_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'L', 'kod_ol' => 'OL26202', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                        ]
                    ],
                    // MINYAK PELINCIR DAN GREASE (OL26206)
                    [
                        'title' => 'MINYAK PELINCIR DAN GREASE (OL26206)',
                        'data' => [
                            ['sub' => 'OL26206_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'Unit', 'kod_ol' => 'OL26206', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                        ]
                    ],
                    // KELUARAN PETROLEUM DAN BAKAR YANG LAIN (OL26299)
                    [
                        'title' => 'KELUARAN PETROLEUM DAN BAKAR YANG LAIN (OL26299)',
                        'data' => [
                            ['sub' => 'OL26299_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'Unit', 'kod_ol' => 'OL26299', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                        ]
                    ]
                ]
            ],

            // --- TAB 2: ALAT GANTI PEJABAT (OS26100) ---
            '26100_GANTI' => [
                'title' => 'ALAT GANTI KELENGKAPAN (OS26100)',
                'has_subgroups' => true,
                'subgroups' => [
                    // ALAT GANTI KELENGKAPAN PEJABAT, PERABUT DAN LENGKAPAN (OS26121)
                    [
                        'title' => 'ALAT GANTI PEJABAT, PERABOT DAN LENGKAPAN (OL26121)',
                        'data' => [
                            ['sub' => 'OL26121_WAKTU', 'butiran' => 'Alat Ganti Mesin Perakam Waktu', 'unit' => 'Unit', 'kod_ol' => 'OL26121', 'editable' => false, 'q'=>2, 'b'=>1, 'a'=>300.00],
                            ['sub' => 'OL26121_BIND', 'butiran' => 'Alat Ganti Mesin Binding', 'unit' => 'Unit', 'kod_ol' => 'OL26121', 'editable' => false, 'q'=>2, 'b'=>2, 'a'=>200.00],
                            ['sub' => 'OL26121_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'Unit', 'kod_ol' => 'OL26121', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                        ]
                    ],
                    // ALAT GANTI KELENGKAPAN ELEKTRONIK (OS26126)
                    [
                        'title' => 'ALAT GANTI KELENGKAPAN ELEKTRONIK (OL26126)',
                        'data' => [
                            ['sub' => 'OL26126_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'Unit', 'kod_ol' => 'OL26126', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                        ]
                    ],
                    // ALAT GANTI KELENGKAPAN ELEKTRIK (OS26131)
                    [
                        'title' => 'ALAT GANTI KELENGKAPAN ELEKTRIK (OL26131)',
                        'data' => [
                            ['sub' => 'OL26131_PENL', 'butiran' => 'Alat Ganti Mesin Penebuk Lubang', 'unit' => 'Unit', 'kod_ol' => 'OL26131', 'editable' => false, 'q'=>2, 'b'=>2, 'a'=>110.00],
                            ['sub' => 'OL26131_BIND', 'butiran' => 'Alat Ganti Mesin Binding', 'unit' => 'Unit', 'kod_ol' => 'OL26131', 'editable' => false, 'q'=>3, 'b'=>1, 'a'=>110.00],
                        ]
                    ],
                ]
            ],
        ];

        return view('pentadbiran.dbus.pecahan_OS26000', compact('dbusData', 'pecahanMap', 'kod', 'tahun', 'items'));
    }

    public function updateOs26000(Request $request)
    {
        $validated = $request->validate([
            'master_id' => 'required|exists:dbuses,id',
            'master_kod' => 'required|string',
            'tahun' => 'required|integer',
            'data' => 'required|array',
            'data.*.butiran' => 'nullable|string|max:255',
            'data.*.kod_ol' => 'required|string', 
            'data.*.kuantiti' => 'nullable|numeric|min:0',
            'data.*.bulan' => 'nullable|integer|min:0|max:12',
            'data.*.anggaran' => 'nullable|numeric|min:0',
            'data.*.catatan' => 'nullable|string|max:255',
        ]);
        
        $masterKod = $validated['master_kod'];
        $tahun = $validated['tahun'];
        $dbusMaster = Dbus::findOrFail($validated['master_id']);
        $grandTotal = 0;

        DB::transaction(function () use ($dbusMaster, $validated, &$grandTotal, $tahun) {
            
            DbusPecahanOS26::where('dbus_id', $dbusMaster->id)->delete();

            if (isset($validated['data'])) {
                foreach ($validated['data'] as $kod_pecahan_sub => $item) {
                    $qty = (float)($item['kuantiti'] ?? 0);
                    $bulan = (float)($item['bulan'] ?? 0);
                    $anggaran = (float)($item['anggaran'] ?? 0);
                    $jumlah = $qty * $bulan * $anggaran;

                    if ($jumlah > 0 || $qty > 0 || $anggaran > 0) {
                        DbusPecahanOS26::create([
                            'dbus_id' => $dbusMaster->id,
                            'kod_ol' => $item['kod_ol'],
                            'kod_pecahan_sub' => $kod_pecahan_sub,
                            'butiran' => $item['butiran'],
                            'anggaran_sebulan' => $anggaran,
                            'bil_bulan' => $bulan,
                            'kuantiti' => $qty,
                            'tahun' => $tahun,
                            'jumlah' => $jumlah,
                            'catatan' => $item['catatan']
                        ]);
                        $grandTotal += $jumlah;
                    }
                }
            }
            
            // Update Master OS26000
            $dbusMaster->update(['jumlah' => $grandTotal]);
            Dbus::updateOrCreate(['kod_objek' => 'OS26000', 'tahun' => $tahun], ['perkara' => 'BEKALAN BAHAN MENTAH', 'jenis' => 'OS', 'jumlah' => $grandTotal]);

            // Update OL Sums (Reset dan Sum)
            $senaraiOL = ['OL26121', 'OL26201', 'OL26202', 'OL26206', 'OL26299', 'OL26701'];
            Dbus::whereIn('kod_objek', $senaraiOL)->where('tahun', $tahun)->update(['jumlah' => 0]);
            
            $olSums = DbusPecahanOS26::where('dbus_id', $dbusMaster->id)
                        ->select('kod_ol', DB::raw('SUM(jumlah) as total'))
                        ->groupBy('kod_ol')
                        ->get();

            foreach($olSums as $sum) {
                $info = $this->findObjekInfo($sum->kod_ol); 
                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    ['perkara' => $info['perkara'] ?? 'Bahan Mentah', 'jenis' => 'OL', 'jumlah' => $sum->total]
                );
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])
                         ->with('success', 'OS26000 berjaya dikemaskini.');
    }
    
// --- 10. BEKALAN DAN BAHAN LAIN (OS27000) ---

    public function editOs27000($kod, $tahun)
    {
        $dbusData = Dbus::where('kod_objek', $kod)->where('tahun', $tahun)->first();
        if (!$dbusData) {
            $info = $this->findObjekInfo($kod);
            $dbusData = Dbus::create([
                'kod_objek' => $kod,
                'tahun' => $tahun,
                'perkara' => $info['perkara'] ?? 'BEKALAN DAN BAHAN LAIN',
                'jenis' => 'OS',
                'jumlah' => 0.00
            ]);
        }

        $pecahanData = DbusPecahanOS27::where('dbus_id', $dbusData->id)->get();
        $pecahanMap = $pecahanData->keyBy('kod_pecahan_sub')->toArray();

        // 3. DEFINISI ITEM (LENGKAP DARI SEMUA PDF)
        $items = [
            // --- TAB 1: BEKALAN PEJABAT (OS27100) ---
            '27100_PEJABAT' => [
                'title' => 'BEKALAN PEJABAT (27100)',
                'has_subgroups' => true,
                'subgroups' => [
                    [
                        'title' => 'SURAT KHABAR & BAHAN BERCETAK (OL27101)',
                        'data' => [
                            ['sub' => 'OL27101_PEJ', 'butiran' => 'Pejabat Penasihat', 'unit' => 'unit', 'kod_ol' => 'OL27101', 'editable' => false, 'q'=>11, 'b'=>1, 'a'=>96.00],
                            ['sub' => 'OL27101_PEND', 'butiran' => 'Unit Pendakwaan', 'unit' => 'unit', 'kod_ol' => 'OL27101', 'editable' => false, 'q'=>11, 'b'=>1, 'a'=>150.00],
                        ]
                    ],
                    [
                        'title' => 'ALAT TULIS PEJABAT (OL27102)',
                        'data' => [
                            ['sub' => 'OL27102_LUMP', 'butiran' => 'Pembelian Alat Tulis (Rujuk Lampiran)', 'unit' => 'lumpsum', 'kod_ol' => 'OL27102', 'editable' => false, 'q'=>1, 'b'=>1, 'a'=>28968.30],
                        ]
                    ],
                    [
                        'title' => 'ALAT TULIS KOMPUTER (OL27103)',
                        'data' => [
                            ['sub' => 'OL27103_PEN', 'butiran' => 'Pen drive', 'unit' => 'unit', 'kod_ol' => 'OL27103', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>35.00],
                            ['sub' => 'OL27103_T1', 'butiran' => 'Toner Laserjet HP 416A Magenta', 'unit' => 'unit', 'kod_ol' => 'OL27103', 'editable' => false, 'q'=>2, 'b'=>1, 'a'=>484.00],
                            ['sub' => 'OL27103_T2', 'butiran' => 'Toner Laserjet HP 416A Yellow', 'unit' => 'unit', 'kod_ol' => 'OL27103', 'editable' => false, 'q'=>2, 'b'=>1, 'a'=>484.00],
                            ['sub' => 'OL27103_T3', 'butiran' => 'Toner Laserjet HP 416A Black', 'unit' => 'unit', 'kod_ol' => 'OL27103', 'editable' => false, 'q'=>4, 'b'=>1, 'a'=>374.00],
                            ['sub' => 'OL27103_T4', 'butiran' => 'Toner HP CF226A', 'unit' => 'unit', 'kod_ol' => 'OL27103', 'editable' => false, 'q'=>12, 'b'=>1, 'a'=>579.00],
                            ['sub' => 'OL27103_K1', 'butiran' => 'Katrij HP Laserjet 136A', 'unit' => 'unit', 'kod_ol' => 'OL27103', 'editable' => false, 'q'=>4, 'b'=>1, 'a'=>218.00],
                            ['sub' => 'OL27103_K2', 'butiran' => 'Katrij HP Laserjet 955 XL Magenta', 'unit' => 'unit', 'kod_ol' => 'OL27103', 'editable' => false, 'q'=>4, 'b'=>1, 'a'=>148.00],
                            ['sub' => 'OL27103_K3', 'butiran' => 'Katrij HP Laserjet 955 XL Cyan', 'unit' => 'unit', 'kod_ol' => 'OL27103', 'editable' => false, 'q'=>4, 'b'=>1, 'a'=>148.00],
                        ]
                    ],
                    [
                        'title' => 'BEKALAN PEJABAT LAIN (OL27199)',
                        'data' => [
                            ['sub' => 'OL27199_BINGKAI', 'butiran' => 'Bingkai Gambar', 'unit' => 'unit', 'kod_ol' => 'OL27199', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>10.00],
                            ['sub' => 'OL27199_PERABOT', 'butiran' => 'Peralatan/Alat Ganti Perabot', 'unit' => 'unit', 'kod_ol' => 'OL27199', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>400.00],
                            ['sub' => 'OL27199_TANDA', 'butiran' => 'Papan Tanda Pejabat', 'unit' => 'unit', 'kod_ol' => 'OL27199', 'editable' => false, 'q'=>3, 'b'=>1, 'a'=>2500.00],
                            ['sub' => 'OL27199_CAP', 'butiran' => 'Cap Pegawai/Signage', 'unit' => 'unit', 'kod_ol' => 'OL27199', 'editable' => false, 'q'=>55, 'b'=>1, 'a'=>60.00],
                            ['sub' => 'OL27199_BEG', 'butiran' => 'Beg Beroda', 'unit' => 'unit', 'kod_ol' => 'OL27199', 'editable' => false, 'q'=>6, 'b'=>1, 'a'=>520.00],
                        ]
                    ]
                ]
            ],
            
            // --- TAB 2: BEKALAN AM (OS27200) ---
            '27200_AM' => [
                'title' => 'BEKALAN AM (27200)',
                'has_subgroups' => true,
                'subgroups' => [
                    [
                        'title' => 'BEKALAN AM YANG LAIN (OL27299)',
                        'data' => [
                            ['sub' => 'OL27299_TNG1', 'butiran' => 'Top Up Touch N Go PPN', 'unit' => 'unit', 'kod_ol' => 'OL27299', 'editable' => false, 'q'=>1, 'b'=>1, 'a'=>3000.00],
                            ['sub' => 'OL27299_TNG2', 'butiran' => 'Top Up Touch N Go Pej. Penasihat', 'unit' => 'unit', 'kod_ol' => 'OL27299', 'editable' => false, 'q'=>5, 'b'=>1, 'a'=>500.00],
                        ]
                    ]
                ]
            ],

            // --- TAB 3: PERUBATAN (OS27400) ---
            '27400_UBAT' => [
                'title' => 'BEKALAN PERUBATAN (27400)',
                'has_subgroups' => true,
                'subgroups' => [
                    [
                        'title' => 'UBAT DAN DADAH (OL27401)',
                        'data' => [
                            ['sub' => 'OL27401_PETI', 'butiran' => 'Refill ubatan di dalam peti kecemasan', 'unit' => 'unit', 'kod_ol' => 'OL27401', 'editable' => false, 'q'=>30, 'b'=>1, 'a'=>30.00],
                            ['sub' => 'OL27401_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'unit', 'kod_ol' => 'OL27401', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>0.00],
                        ]
                    ],
                    [
                        'title' => 'BEKALAN PERUBATAN LAIN (OL27499)',
                        'data' => [
                            ['sub' => 'OL27499_MASK', 'butiran' => 'Face Mask', 'unit' => 'unit', 'kod_ol' => 'OL27499', 'editable' => false, 'q'=>20, 'b'=>1, 'a'=>20.00],
                            ['sub' => 'OL27499_SAN', 'butiran' => 'Hand Sanitizer 500ml', 'unit' => 'unit', 'kod_ol' => 'OL27499', 'editable' => false, 'q'=>20, 'b'=>1, 'a'=>20.00],
                        ]
                    ]
                ]
            ],

            // --- TAB 4: PAKAIAN (OS27600) ---
            '27600_PAKAIAN' => [
                'title' => 'PAKAIAN (27600)',
                'has_subgroups' => true,
                'subgroups' => [
                    [
                        'title' => 'PAKAIAN SERAGAM (OL27605)',
                        'data' => [
                            ['sub' => 'OL27605_UNIFORM', 'butiran' => 'Pakaian Seragam', 'unit' => 'pasang', 'kod_ol' => 'OL27605', 'editable' => false, 'q'=>2, 'b'=>7, 'a'=>230.00],
                            ['sub' => 'OL27605_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'pasang', 'kod_ol' => 'OL27605', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                            ['sub' => 'OL27605_L2', 'butiran' => 'Sila Nyatakan', 'unit' => 'pasang', 'kod_ol' => 'OL27605', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                        ]
                    ],
                    [
                        'title' => 'PAKAIAN LAIN (OL27699)',
                        'data' => [
                            ['sub' => 'OL27699_KASUT', 'butiran' => 'Kasut', 'unit' => 'pasang', 'kod_ol' => 'OL27699', 'editable' => false, 'q'=>2, 'b'=>7, 'a'=>150.00],
                            ['sub' => 'OL27699_L1', 'butiran' => 'Sila Nyatakan', 'unit' => 'pasang', 'kod_ol' => 'OL27699', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                            ['sub' => 'OL27699_L2', 'butiran' => 'Sila Nyatakan', 'unit' => 'pasang', 'kod_ol' => 'OL27699', 'editable' => true, 'q'=>0, 'b'=>0, 'a'=>0.00],
                        ]
                    ]
                ]
            ],

            // --- TAB 5: LAMPIRAN (DETAIL ALAT TULIS) ---
            // TAB 5: LAMPIRAN LENGKAP (OL27102)
            // =================================================
            '27102_LAMPIRAN' => [
                'title' => 'LAMPIRAN (ALAT TULIS - OL27102)',
                'has_subgroups' => true,
                'subgroups' => [
                    ['title' => '1-2. KERTAS', 'data' => [
                        ['sub' => 'LAMP_1', 'butiran' => '1. Kertas A4', 'unit' => 'rim', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>650, 'b'=>1, 'a'=>13.80],
                        ['sub' => 'LAMP_2', 'butiran' => '2. Kertas A3', 'unit' => 'rim', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>19.00],
                    ]],
                    ['title' => '3. AIR FRESHENER', 'data' => [
                        ['sub' => 'LAMP_3_1', 'butiran' => '3.1 Air Freshener - 1 Set', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>36.00],
                        ['sub' => 'LAMP_3_2', 'butiran' => '3.2 Air Freshener - Casing', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>6, 'b'=>1, 'a'=>32.00],
                        ['sub' => 'LAMP_3_3', 'butiran' => '3.3 Air Freshener - Refill 280ml', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>36, 'b'=>1, 'a'=>16.00],
                    ]],
                    ['title' => '4. BAKUL SAMPAH', 'data' => [
                        ['sub' => 'LAMP_4', 'butiran' => '4. Bakul Sampah (Medium)', 'unit' => 'buah', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>12.00],
                    ]],
                    ['title' => '5. BATERI', 'data' => [
                        ['sub' => 'LAMP_5_1', 'butiran' => '5.1 Saiz D (1 Pek: 2 Biji)', 'unit' => 'pek', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>7.80],
                        ['sub' => 'LAMP_5_2', 'butiran' => '5.2 Saiz AA (1 Pek: 4 Biji)', 'unit' => 'pek', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>5.10],
                        ['sub' => 'LAMP_5_3', 'butiran' => '5.3 Saiz AAA (1 Pek: 4 Biji)', 'unit' => 'pek', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>15, 'b'=>1, 'a'=>3.80],
                    ]],
                    ['title' => '6. BINDING TAPE', 'data' => [
                        ['sub' => 'LAMP_6_1', 'butiran' => '6.1 Binding Tape 1"', 'unit' => 'gulung', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>5.00],
                        ['sub' => 'LAMP_6_2', 'butiran' => '6.2 Binding Tape 2"', 'unit' => 'gulung', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>5.00],
                        ['sub' => 'LAMP_6_3', 'butiran' => '6.3 Binding Tape 3"', 'unit' => 'gulung', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>5.00],
                    ]],
                    ['title' => '7-10. BUKU & DAFTAR', 'data' => [
                        ['sub' => 'LAMP_7_1', 'butiran' => '7.1 Buku Kulit Keras - Foolscap F4', 'unit' => 'dozen', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>12.90],
                        ['sub' => 'LAMP_7_2', 'butiran' => '7.2 Buku Kulit Keras - Berindeks', 'unit' => 'dozen', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>13.90],
                        ['sub' => 'LAMP_7_3', 'butiran' => '7.3 Buku Kulit Keras - Quarto', 'unit' => 'dozen', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>1, 'b'=>1, 'a'=>4.90],
                        ['sub' => 'LAMP_8', 'butiran' => '8. Buku Daftar Suratan Rahsia Rasmi', 'unit' => 'buah', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>0],
                        ['sub' => 'LAMP_9', 'butiran' => '9. Buku Daftar Surat Penghantaran', 'unit' => 'buah', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>0],
                        ['sub' => 'LAMP_10', 'butiran' => '10. Buku Daftar Stok', 'unit' => 'buah', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>6.00],
                        ['sub' => 'LAMP_11', 'butiran' => '11. Buku Despatch', 'unit' => 'buah', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>4.50],
                        ['sub' => 'LAMP_12', 'butiran' => '12. Buku Rekod Daftar Surat', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>8.00],
                        ['sub' => 'LAMP_13', 'butiran' => '13. Buku Log Kenderaan', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>8.00],
                        ['sub' => 'LAMP_14', 'butiran' => '14. Buku Rekod Servis', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>120, 'b'=>1, 'a'=>5.50],
                    ]],
                    ['title' => '15. COMB BINDING STRIP', 'data' => [
                        ['sub' => 'LAMP_15_1', 'butiran' => '15.1 Saiz 6mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>2.90],
                        ['sub' => 'LAMP_15_2', 'butiran' => '15.2 Saiz 8mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>2.90],
                        ['sub' => 'LAMP_15_3', 'butiran' => '15.3 Saiz 10mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>3.40],
                        ['sub' => 'LAMP_15_4', 'butiran' => '15.4 Saiz 11mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>3.60],
                        ['sub' => 'LAMP_15_5', 'butiran' => '15.5 Saiz 12mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>3.90],
                        ['sub' => 'LAMP_15_6', 'butiran' => '15.6 Saiz 14mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>4.40],
                        ['sub' => 'LAMP_15_7', 'butiran' => '15.7 Saiz 16mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>4.90],
                        ['sub' => 'LAMP_15_8', 'butiran' => '15.8 Saiz 18mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>6.40],
                        ['sub' => 'LAMP_15_9', 'butiran' => '15.9 Saiz 20mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>7.90],
                        ['sub' => 'LAMP_15_10', 'butiran' => '15.10 Saiz 22mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>8.60],
                        ['sub' => 'LAMP_15_11', 'butiran' => '15.11 Saiz 25mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>9.90],
                        ['sub' => 'LAMP_15_12', 'butiran' => '15.12 Saiz 28mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>10.60],
                        ['sub' => 'LAMP_15_13', 'butiran' => '15.13 Saiz 38mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>11.90],
                        ['sub' => 'LAMP_15_14', 'butiran' => '15.14 Saiz 45mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>12.60],
                        ['sub' => 'LAMP_15_15', 'butiran' => '15.15 Saiz 50mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>13.60],
                    ]],
                    ['title' => '16. DAWAI KOKOT', 'data' => [
                        ['sub' => 'LAMP_16_1', 'butiran' => '16.1 No.10 (1000/box)', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>120, 'b'=>1, 'a'=>1.50],
                        ['sub' => 'LAMP_16_2', 'butiran' => '16.2 No.03 (1000/box)', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>25, 'b'=>1, 'a'=>2.00],
                        ['sub' => 'LAMP_16_3', 'butiran' => '16.3 No.23/10', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>2.00],
                        ['sub' => 'LAMP_16_4', 'butiran' => '16.4 No.23/15', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>4.00],
                        ['sub' => 'LAMP_16_5', 'butiran' => '16.5 No.23/17', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>4.00],
                    ]],
                    ['title' => '17-18. DAKWAT & GAM', 'data' => [
                        ['sub' => 'LAMP_17', 'butiran' => '17. Dakwat Stamp Pad', 'unit' => 'botol', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>4.50],
                        ['sub' => 'LAMP_18_1', 'butiran' => '18.1 Glue Stick 25g', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>1.80],
                        ['sub' => 'LAMP_18_2', 'butiran' => '18.2 Jenis Polytron 230g', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>4.50],
                        ['sub' => 'LAMP_18_3', 'butiran' => '18.3 Jenis Biasa 230g', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>3.50],
                        ['sub' => 'LAMP_18_4', 'butiran' => '18.4 Jenis Biasa 50ml', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>1.80],
                        ['sub' => 'LAMP_18_5', 'butiran' => '18.5 Electric Glue Gun', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>6, 'b'=>1, 'a'=>9.90],
                        ['sub' => 'LAMP_18_6', 'butiran' => '18.6 Refill Pistol Gum', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>0.60],
                    ]],
                    ['title' => '19-24. PELBAGAI ALAT', 'data' => [
                        ['sub' => 'LAMP_19', 'butiran' => '19. Gelang Getah Tebal', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>4.80],
                        ['sub' => 'LAMP_20', 'butiran' => '20. Gunting Kertas', 'unit' => 'bilah', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>40, 'b'=>1, 'a'=>4.50],
                        ['sub' => 'LAMP_21', 'butiran' => '21. Kad Perakam Waktu', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>1, 'b'=>1, 'a'=>20.00],
                        ['sub' => 'LAMP_22_1', 'butiran' => '22.1 Kad Pergerakan Fail (Merah)', 'unit' => 'keping', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>0.25],
                        ['sub' => 'LAMP_22_2', 'butiran' => '22.2 Kad Pergerakan Fail (Hijau)', 'unit' => 'keping', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>0.25],
                        ['sub' => 'LAMP_23_1', 'butiran' => '23.1 Kalkulator Canon LS-88Hi', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>7.90],
                        ['sub' => 'LAMP_23_2', 'butiran' => '23.2 Kalkulator Canon LS-120Hi', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>6, 'b'=>1, 'a'=>38.80],
                        ['sub' => 'LAMP_24', 'butiran' => '24. Kertas Minit', 'unit' => 'rim', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>24.00],
                    ]],
                    ['title' => '25-29. KERTAS LAIN', 'data' => [
                        ['sub' => 'LAMP_25_1', 'butiran' => '25.1 Kertas Karbon Hitam', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>1, 'b'=>1, 'a'=>34.80],
                        ['sub' => 'LAMP_25_2', 'butiran' => '25.2 Kertas Karbon Biru', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>1, 'b'=>1, 'a'=>34.80],
                        ['sub' => 'LAMP_26', 'butiran' => '26. Letterhead A4', 'unit' => 'rim', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>17.50],
                        ['sub' => 'LAMP_27_1', 'butiran' => '27.1 A4 90gsm (Berwarna)', 'unit' => 'rim', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>18.00],
                        ['sub' => 'LAMP_27_2', 'butiran' => '27.2 Index Divider', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>160, 'b'=>1, 'a'=>6.90],
                        ['sub' => 'LAMP_27_3', 'butiran' => '27.3 Kertas Warna 80gsm', 'unit' => 'rim', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>16.00],
                        ['sub' => 'LAMP_28', 'butiran' => '28. Kertas Mahjung', 'unit' => 'gulung', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>10.90],
                        ['sub' => 'LAMP_29', 'butiran' => '29. Kertas Pembalut Coklat', 'unit' => 'gulung', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>24.90],
                    ]],
                    ['title' => '30-31. KLIP', 'data' => [
                        ['sub' => 'LAMP_30', 'butiran' => '30. Bulldog Clip', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>7.50],
                        ['sub' => 'LAMP_31_1', 'butiran' => '31.1 Klip Hitam 9mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>1.50],
                        ['sub' => 'LAMP_31_2', 'butiran' => '31.2 Klip Hitam 19mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>1.80],
                        ['sub' => 'LAMP_31_3', 'butiran' => '31.3 Klip Hitam 31mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>3.80],
                        ['sub' => 'LAMP_31_4', 'butiran' => '31.4 Klip Hitam 51mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>40, 'b'=>1, 'a'=>5.80],
                        ['sub' => 'LAMP_31_5', 'butiran' => '31.5 Bujur Besar', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>2.00],
                        ['sub' => 'LAMP_31_6', 'butiran' => '31.6 Segitiga Kecil', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>120, 'b'=>1, 'a'=>1.00],
                    ]],
                    ['title' => '32-33. FAIL', 'data' => [
                        ['sub' => 'LAMP_32', 'butiran' => '32. Kotak Fail', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>4.90],
                        ['sub' => 'LAMP_33', 'butiran' => '33. Kulit Fail Putih', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>25.00],
                        ['sub' => 'LAMP_33_2', 'butiran' => '33.2 Fail Rahsia (Merah)', 'unit' => 'keping', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>100, 'b'=>1, 'a'=>0.60],
                        ['sub' => 'LAMP_33_3', 'butiran' => '33.3 Fail Sulit (Hijau)', 'unit' => 'keping', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>0.60],
                    ]],
                    ['title' => '34-40. ALAT TULIS & PELEKAT', 'data' => [
                        ['sub' => 'LAMP_34_2', 'butiran' => '34.2 Marker Pen WB Hitam', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>4, 'b'=>1, 'a'=>40.00],
                        ['sub' => 'LAMP_34_3', 'butiran' => '34.3 Marker Pen WB Biru', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>4, 'b'=>1, 'a'=>40.00],
                        ['sub' => 'LAMP_34_4', 'butiran' => '34.4 Marker Pen WB Merah', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>4, 'b'=>1, 'a'=>40.00],
                        ['sub' => 'LAMP_34_6', 'butiran' => '34.6 Marker Artline 500 Hitam', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>40.00],
                        ['sub' => 'LAMP_34_7', 'butiran' => '34.7 Marker Artline 500 Biru', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>40.00],
                        ['sub' => 'LAMP_34_8', 'butiran' => '34.8 Marker Artline 500 Merah', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>40.00],
                        ['sub' => 'LAMP_34_10', 'butiran' => '34.10 Marker Artline 200 Hitam', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>36.00],
                        ['sub' => 'LAMP_34_11', 'butiran' => '34.11 Marker Artline 200 Biru', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>36.00],
                        ['sub' => 'LAMP_34_12', 'butiran' => '34.12 Marker Artline 200 Merah', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>36.00],
                        ['sub' => 'LAMP_35', 'butiran' => '35. Memo Pad', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>0, 'b'=>1, 'a'=>3.00],
                        ['sub' => 'LAMP_36_1', 'butiran' => '36.1 Memo PUUP', 'unit' => 'pad', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>3.00],
                        ['sub' => 'LAMP_38_1', 'butiran' => '38.1 Pelekat SEGERA', 'unit' => 'pek', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>3.00],
                        ['sub' => 'LAMP_38_2', 'butiran' => '38.2 Pelekat TINDAKAN MERTA', 'unit' => 'pek', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>3.00],
                        ['sub' => 'LAMP_38_3', 'butiran' => '38.3 Pelekat SIGN HERE', 'unit' => 'pek', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>120, 'b'=>1, 'a'=>3.00],
                        ['sub' => 'LAMP_39', 'butiran' => '39. Pemadam Pensil', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>18.80],
                        ['sub' => 'LAMP_40_1', 'butiran' => '40.1 Corrector (Cecair)', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>5.90],
                        ['sub' => 'LAMP_40_2', 'butiran' => '40.2 Correction Tape', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>5.90],
                    ]],
                    ['title' => '41-49. ALATAN LAIN', 'data' => [
                        ['sub' => 'LAMP_41', 'butiran' => '41. Pemadam White Board', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>6, 'b'=>1, 'a'=>2.80],
                        ['sub' => 'LAMP_42_1', 'butiran' => '42.1 Pembaris 15cm', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>0.80],
                        ['sub' => 'LAMP_42_2', 'butiran' => '42.2 Pembaris 30cm', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>1.60],
                        ['sub' => 'LAMP_43_1', 'butiran' => '43.1 Pen Hitam', 'unit' => 'batang', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>8.50],
                        ['sub' => 'LAMP_43_2', 'butiran' => '43.2 Pen Biru', 'unit' => 'batang', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>8.50],
                        ['sub' => 'LAMP_43_3', 'butiran' => '43.3 Pen Merah', 'unit' => 'batang', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>8.50],
                        ['sub' => 'LAMP_43_5', 'butiran' => '43.5 Pilot Ball Liner Hitam', 'unit' => 'batang', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>200, 'b'=>1, 'a'=>4.80],
                        ['sub' => 'LAMP_43_6', 'butiran' => '43.6 Pilot Ball Liner Biru', 'unit' => 'batang', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>200, 'b'=>1, 'a'=>4.80],
                        ['sub' => 'LAMP_43_7', 'butiran' => '43.7 Pilot Ball Liner Merah', 'unit' => 'batang', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>200, 'b'=>1, 'a'=>4.80],
                        ['sub' => 'LAMP_45_1', 'butiran' => '45.1 Penebuk 1 Lubang', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>3.50],
                        ['sub' => 'LAMP_45_2', 'butiran' => '45.2 Penebuk 2 Lubang', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>6.50],
                        ['sub' => 'LAMP_46', 'butiran' => '46. Pengasah Pensil Meja', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>1, 'b'=>1, 'a'=>16.90],
                        ['sub' => 'LAMP_47', 'butiran' => '47. Pensil 2B', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>20.00],
                        ['sub' => 'LAMP_48_1', 'butiran' => '48.1 Pisau Cutter', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>2.10],
                        ['sub' => 'LAMP_48_2', 'butiran' => '48.2 Mata Pisau', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>12, 'b'=>1, 'a'=>2.10],
                        ['sub' => 'LAMP_49', 'butiran' => '49. Binding Cover A4', 'unit' => 'rim', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>24.00],
                    ]],
                    ['title' => '50-67. LAIN-LAIN & FINAL', 'data' => [
                        ['sub' => 'LAMP_50', 'butiran' => '50. Plastik Laminat', 'unit' => 'rim', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>19.00],
                        ['sub' => 'LAMP_51', 'butiran' => '51. Post-It Notes', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>6.80],
                        ['sub' => 'LAMP_52_1', 'butiran' => '52.1 Refill Pilot G2 (0.5)', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>42.80],
                        ['sub' => 'LAMP_52_2', 'butiran' => '52.2 Refill Pilot G2 (0.7)', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>42.80],
                        ['sub' => 'LAMP_52_3', 'butiran' => '52.3 Refill Pilot G2 (1.0)', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>42.80],
                        ['sub' => 'LAMP_53', 'butiran' => '53. Ribbon Perakam Waktu', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>16.00],
                        ['sub' => 'LAMP_55_1', 'butiran' => '55.1 2 Ring Folder 25mm', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>20, 'b'=>1, 'a'=>5.90],
                        ['sub' => 'LAMP_55_2', 'butiran' => '55.2 2 Ring Folder 50mm', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>20, 'b'=>1, 'a'=>7.90],
                        ['sub' => 'LAMP_55_3', 'butiran' => '55.3 3 Ring Folder 85mm', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>20, 'b'=>1, 'a'=>10.50],
                        ['sub' => 'LAMP_55_4', 'butiran' => '55.4 Arch File', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>20, 'b'=>1, 'a'=>9.00],
                        ['sub' => 'LAMP_56', 'butiran' => '56. Sampul Kecil', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>4, 'b'=>1, 'a'=>36.80],
                        ['sub' => 'LAMP_57_1', 'butiran' => '57.1 Sampul DL', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>60.00],
                        ['sub' => 'LAMP_57_2', 'butiran' => '57.2 Sampul C5', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>2, 'b'=>1, 'a'=>70.00],
                        ['sub' => 'LAMP_57_3', 'butiran' => '57.3 Sampul C4', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>4, 'b'=>1, 'a'=>80.00],
                        ['sub' => 'LAMP_57_4', 'butiran' => '57.4 Sampul 114x162', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>4, 'b'=>1, 'a'=>90.00],
                        ['sub' => 'LAMP_57_5', 'butiran' => '57.5 Sampul Besar X2', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>4, 'b'=>1, 'a'=>95.00],
                        ['sub' => 'LAMP_58_2', 'butiran' => '58.2 Stamp Pad 8.5x12', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>12.80],
                        ['sub' => 'LAMP_59_1', 'butiran' => '59.1 Stapler HD10', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>2.80],
                        ['sub' => 'LAMP_59_2', 'butiran' => '59.2 Stapler HD10D', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>14.60],
                        ['sub' => 'LAMP_60_1', 'butiran' => '60.1 Sticky Note 15x76', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>120, 'b'=>1, 'a'=>2.40],
                        ['sub' => 'LAMP_60_2', 'butiran' => '60.2 Sticky Note 75x75', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>120, 'b'=>1, 'a'=>2.80],
                        ['sub' => 'LAMP_61_3', 'butiran' => '61.3 Binding Tape 3"', 'unit' => 'gulung', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>5.00],
                        ['sub' => 'LAMP_61_4', 'butiran' => '61.4 Cellotape 18mm', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>60, 'b'=>1, 'a'=>1.50],
                        ['sub' => 'LAMP_61_6', 'butiran' => '61.6 Cellotape 60mm', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>4.00],
                        ['sub' => 'LAMP_61_7', 'butiran' => '61.7 Packaging Tape 60mm', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>15, 'b'=>1, 'a'=>4.80],
                        ['sub' => 'LAMP_61_10', 'butiran' => '61.10 Paper Gummed Tape', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>15, 'b'=>1, 'a'=>7.50],
                        ['sub' => 'LAMP_62_1', 'butiran' => '62.1 Tali Fail 72mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>6.80],
                        ['sub' => 'LAMP_62_2', 'butiran' => '62.2 Tali Fail 150mm', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>10, 'b'=>1, 'a'=>13.80],
                        ['sub' => 'LAMP_63', 'butiran' => '63. Tali Putih Cotton', 'unit' => 'pek', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>18.00],
                        ['sub' => 'LAMP_65_1', 'butiran' => '65.1 Tracing Paper A4', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>6.80],
                        ['sub' => 'LAMP_65_2', 'butiran' => '65.2 Tracing Paper A3', 'unit' => 'unit', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>5, 'b'=>1, 'a'=>10.80],
                        ['sub' => 'LAMP_67', 'butiran' => '67. Thumb/Push Pin', 'unit' => 'kotak', 'kod_ol' => 'OL27102', 'editable' => true, 'q'=>30, 'b'=>1, 'a'=>2.10],
                    ]]
                ]
            ],
        ];

        return view('pentadbiran.dbus.pecahan_OS27000', compact('dbusData', 'pecahanMap', 'kod', 'tahun', 'items'));
    }
    
    public function updateOs27000(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'master_id' => 'required|exists:dbuses,id',
            'tahun' => 'required|integer',
            'data' => 'required|array',
        ]);
        
        $dbusMaster = Dbus::findOrFail($validated['master_id']);
        $tahun = $validated['tahun'];
        $grandTotal = 0;

        DB::transaction(function () use ($dbusMaster, $request, &$grandTotal, $tahun) {
            
            // 2. Padam Data Lama (Reset Pecahan OS27000)
            // Pastikan Model DbusPecahanOS27 diimport di atas: use App\Models\DbusPecahanOS27;
            DbusPecahanOS27::where('dbus_id', $dbusMaster->id)->delete();

            // 3. Loop Simpan Data Baru
            foreach ($request->input('data') as $kod_pecahan_sub => $item) {
                // Sanitasi Nilai (Pastikan format nombor)
                $qty = (float)($item['kuantiti'] ?? 0);
                // Untuk Pakaian guna bil_bulan sebagai 'orang', lain-lain default 1
                $bilangan = (float)($item['bil_bulan'] ?? 1); 
                $anggaran = (float)($item['anggaran'] ?? 0);
                
                $jumlah = $qty * $bilangan * $anggaran;

                // Simpan hanya jika ada nilai atau ia item wajib
                if ($jumlah > 0 || $qty > 0 || $anggaran > 0) {
                    DbusPecahanOS27::create([
                        'dbus_id' => $dbusMaster->id,
                        'kod_ol' => $item['kod_ol'], // PENTING: Ini menentukan grouping (cth: OL27102)
                        'kod_pecahan_sub' => $kod_pecahan_sub,
                        'butiran' => $item['butiran'] ?? '',
                        'anggaran_sebulan' => $anggaran,
                        'bil_bulan' => $bilangan,
                        'kuantiti' => $qty,
                        'tahun' => $tahun,
                        'jumlah' => $jumlah,
                        'catatan' => $item['catatan'] ?? ''
                    ]);
                    $grandTotal += $jumlah;
                }
            }
            
            // 4. Update Jumlah Induk OS27000 di Table Utama
            $dbusMaster->update(['jumlah' => $grandTotal]);
            Dbus::updateOrCreate(
                ['kod_objek' => 'OS27000', 'tahun' => $tahun], 
                ['perkara' => 'BEKALAN DAN BAHAN LAIN', 'jenis' => 'OS', 'jumlah' => $grandTotal]
            );

            // 5. Update Pecahan OL (Supaya paparan Index betul)
            // Senarai semua OL yang wujud dalam OS27000
            $senaraiOL = [
                'OL27101', // Surat Khabar
                'OL27102', // Alat Tulis (Termasuk Lampiran)
                'OL27103', // Komputer
                'OL27199', // Pejabat Lain
                'OL27299', // Bekalan Am
                'OL27401', // Ubat
                'OL27499', // Perubatan Lain
                'OL27605', // Pakaian Seragam
                'OL27699'  // Pakaian Lain
            ];

            // Reset semua OL ke 0 dulu
            Dbus::whereIn('kod_objek', $senaraiOL)->where('tahun', $tahun)->update(['jumlah' => 0]);
            
            // Kira jumlah setiap OL berdasarkan data pecahan baru
            $olSums = DbusPecahanOS27::where('dbus_id', $dbusMaster->id)
                        ->select('kod_ol', DB::raw('SUM(jumlah) as total'))
                        ->groupBy('kod_ol')
                        ->get();

            // Update DBUS table dengan jumlah baru setiap OL
            foreach($olSums as $sum) {
                $info = $this->findObjekInfo($sum->kod_ol); 
                
                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    [
                        'perkara' => $info['perkara'] ?? 'Perkara', 
                        'jenis' => 'OL', 
                        'jumlah' => $sum->total
                    ]
                );
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])
                         ->with('success', 'OS27000 berjaya dikemaskini. Jumlah: RM' . number_format($grandTotal, 2));
    }

   // --- 11. PENYELENGGARAAN & PEMBAIKAN (OS28000) ---

    public function editOs28000($kod, $tahun)
    {
        // 1. Dapatkan/Cipta Rekod Induk
        $dbusData = Dbus::firstOrCreate(
            ['kod_objek' => $kod, 'tahun' => $tahun],
            ['perkara' => 'PENYELENGGARAAN & PEMBAIKAN', 'jenis' => 'OS', 'jumlah' => 0.00]
        );

        // 2. Ambil Data Sedia Ada
        $pecahanData = DbusPecahanOS28::where('dbus_id', $dbusData->id)->get();
        $pecahanMap = $pecahanData->keyBy('kod_pecahan_sub')->toArray();

        // 3. STRUKTUR DATA IKUT PDF (Tab -> Sub-Group -> Item)
        $items = [
            // TAB 1: BANGUNAN (28100)
            '28100_BANGUNAN' => [
                'title' => '28100 PENYELENGGARAAN BANGUNAN',
                'groups' => [
                    ['title' => 'BANGUNAN PEJABAT (OL28102)', 'items' => [
                        ['sub' => 'OL28102_1', 'butiran' => 'Sila Nyatakan', 'q'=>0, 's'=>0, 'k'=>0],
                    ]],
                    ['title' => 'BANGUNAN-BANGUNAN LAIN (OL28199)', 'items' => [
                        ['sub' => 'OL28199_1', 'butiran' => 'Sila Nyatakan', 'q'=>0, 's'=>0, 'k'=>0],
                    ]]
                ]
            ],
            // TAB 2: KENDERAAN (28300)
            '28300_KENDERAAN' => [
                'title' => '28300 PENYELENGGARAAN KENDERAAN',
                'groups' => [
                    ['title' => 'KENDERAAN PENUMPANG (OL28301)', 'items' => [
                        ['sub' => 'OL28301_1', 'butiran' => 'Sila Nyatakan', 'q'=>0, 's'=>0, 'k'=>0],
                    ]],
                    ['title' => 'KENDERAAN KONSESI SPANCO (OL28307)', 'items' => [
                        ['sub' => 'OL28307_1', 'butiran' => 'Sila Nyatakan', 'q'=>0, 's'=>0, 'k'=>0],
                    ]]
                ]
            ],
            // TAB 3: ALAT PEJABAT & PERABOT (28500)
            '28500_PEJABAT' => [
                'title' => '28500 ALAT PEJABAT & PERABOT',
                'groups' => [
                    ['title' => 'ALAT KELENGKAPAN PEJABAT (OL28501)', 'items' => [
                        ['sub' => 'OL28501_1', 'butiran' => 'Selenggara Build-in Cabinet loker bercermin', 'q'=>5, 's'=>1, 'k'=>2000.00],
                    ]],
                    ['title' => 'PERABOT DAN LENGKAPAN LAIN (OL28599)', 'items' => [
                        ['sub' => 'OL28599_1', 'butiran' => 'Caj Pembaikan Mesin Perakam Waktu', 'q'=>3, 's'=>1, 'k'=>200.00],
                        ['sub' => 'OL28599_2', 'butiran' => 'Caj Pembaikan Mesin Penebuk Lubang Manual', 'q'=>1, 's'=>3, 'k'=>120.00],
                    ]]
                ]
            ],
            // TAB 4: ALAT ELEKTRONIK (28600)
            '28600_ELEKTRONIK' => [
                'title' => '28600 ALAT ELEKTRONIK',
                'groups' => [
                    ['title' => 'KOMPUTER & PROSES DATA (OL28601)', 'items' => [
                        ['sub' => 'OL28601_1', 'butiran' => 'Reformat Komputer dan notebook', 'q'=>8, 's'=>1, 'k'=>500.00],
                        ['sub' => 'OL28601_2', 'butiran' => 'Baiki Komputer dan notebook', 'q'=>5, 's'=>1, 'k'=>1100.00],
                        ['sub' => 'OL28601_3', 'butiran' => 'Pembaikan mesin pencetak', 'q'=>2, 's'=>1, 'k'=>260.00],
                    ]],
                    ['title' => 'ALAT KELENGKAPAN ELEKTRONIK LAIN (OL28699)', 'items' => [
                        ['sub' => 'OL28699_1', 'butiran' => 'Maintenance Terminal Touch \'N Go Jusa C', 'q'=>1, 's'=>1, 'k'=>390.00],
                        ['sub' => 'OL28699_2', 'butiran' => 'Selenggara mikrofon Bilik Mesyuarat Pendakwaan', 'q'=>20, 's'=>1, 'k'=>100.00],
                    ]]
                ]
            ],
            // TAB 5: ALAT ELEKTRIK (28700)
            '28700_ELEKTRIK' => [
                'title' => '28700 ALAT ELEKTRIK',
                'groups' => [
                    ['title' => 'ALAT HAWA DINGIN (OL28701)', 'items' => [
                        ['sub' => 'OL28701_1', 'butiran' => 'Penyelenggaraan Berkala 6 unit Split Air-Cond', 'q'=>4, 's'=>1, 'k'=>1000.00],
                        ['sub' => 'OL28701_2', 'butiran' => 'Pembaikan kerosakan Air-Cond Bilik Mesyuarat', 'q'=>1, 's'=>1, 'k'=>2000.00],
                    ]],
                    ['title' => 'ALAT ELEKTRIK LAIN (OL28799)', 'items' => [
                        ['sub' => 'OL28799_1', 'butiran' => 'Mesin Perincih', 'q'=>4, 's'=>1, 'k'=>90.00],
                        ['sub' => 'OL28799_2', 'butiran' => 'Mesin Binding Elektrik', 'q'=>2, 's'=>1, 'k'=>90.00],
                        ['sub' => 'OL28799_3', 'butiran' => 'Mesin Penebuk Lubang Elektrik', 'q'=>1, 's'=>2, 'k'=>360.00],
                    ]]
                ]
            ],
            // TAB 6: PERHUBUNGAN (28800)
            '28800_PERHUBUNGAN' => [
                'title' => '28800 ALAT PERHUBUNGAN',
                'groups' => [
                    ['title' => 'TELEFON, TELEX DAN TELEGRAF (OL28801)', 'items' => [
                        ['sub' => 'OL28801_1', 'butiran' => 'Telefon di Unit Pendakwaan Melaka', 'q'=>1, 's'=>0, 'k'=>2400.00], // PDF kata RM0 jumlah, tapi ada kos unit?
                        ['sub' => 'OL28801_2', 'butiran' => 'Mesin Faks', 'q'=>1, 's'=>1, 'k'=>300.00],
                    ]],
                    ['title' => 'ALAT PERHUBUNGAN LAIN (OL28899)', 'items' => [
                        ['sub' => 'OL28899_1', 'butiran' => 'Sila Nyatakan', 'q'=>0, 's'=>0, 'k'=>0],
                    ]]
                ]
            ],
            // TAB 7: ASET LAIN (28900)
            '28900_LAIN' => [
                'title' => '28900 ASET-ASET LAIN',
                'groups' => [
                    ['title' => 'PERKHIDMATAN PEMBERSIHAN (OL28911)', 'items' => [
                        ['sub' => 'OL28911_1', 'butiran' => 'Sila Nyatakan', 'q'=>0, 's'=>0, 'k'=>0],
                    ]]
                ]
            ],
        ];

        return view('pentadbiran.dbus.pecahan_OS28000', compact('dbusData', 'pecahanMap', 'kod', 'tahun', 'items'));
    }

    public function updateOs28000(Request $request)
    {
        $validated = $request->validate([
            'master_id' => 'required|exists:dbuses,id', 'tahun' => 'required', 'data' => 'required|array'
        ]);
        
        $dbusMaster = Dbus::findOrFail($validated['master_id']);
        $tahun = $validated['tahun'];
        $grandTotal = 0;

        DB::transaction(function () use ($dbusMaster, $request, &$grandTotal, $tahun) {
            DbusPecahanOS28::where('dbus_id', $dbusMaster->id)->delete();

            foreach ($request->input('data') as $subKod => $item) {
                $qty = (float)($item['kuantiti'] ?? 0);
                $servis = (float)($item['bil_servis'] ?? 0);
                $kos = (float)($item['anggaran_kos'] ?? 0);
                $jumlah = $qty * $servis * $kos;

                if ($jumlah > 0 || $qty > 0 || $kos > 0) {
                    // Extract Kod OL dari Sub Kod (Contoh: OL28102_1 -> OL28102)
                    $kodOl = substr($subKod, 0, 7); 

                    DbusPecahanOS28::create([
                        'dbus_id' => $dbusMaster->id,
                        'kod_ol' => $kodOl,
                        'kod_pecahan_sub' => $subKod,
                        'butiran' => $item['butiran'] ?? '',
                        'kuantiti' => $qty,
                        'bil_servis' => $servis,
                        'anggaran_kos' => $kos,
                        'jumlah' => $jumlah,
                        'tahun' => $tahun,
                        'catatan' => $item['catatan'] ?? ''
                    ]);
                    $grandTotal += $jumlah;
                }
            }

            // Update Master
            $dbusMaster->update(['jumlah' => $grandTotal]);
            Dbus::updateOrCreate(['kod_objek' => 'OS28000', 'tahun' => $tahun], ['jumlah' => $grandTotal]);

            // Update OL Sums
            $olSums = DbusPecahanOS28::where('dbus_id', $dbusMaster->id)
                        ->select('kod_ol', DB::raw('SUM(jumlah) as total'))
                        ->groupBy('kod_ol')->get();
            
            // Reset dulu semua OL berkaitan
            $allOLs = ['OL28102','OL28199','OL28301','OL28307','OL28501','OL28599','OL28601','OL28699','OL28701','OL28799','OL28801','OL28899','OL28911'];
            Dbus::whereIn('kod_objek', $allOLs)->where('tahun', $tahun)->update(['jumlah' => 0]);

            foreach($olSums as $sum) {
                $info = $this->findObjekInfo($sum->kod_ol);
                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    ['perkara' => $info['perkara'] ?? 'Penyelenggaraan', 'jenis' => 'OL', 'jumlah' => $sum->total]
                );
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', 'OS28000 berjaya dikemaskini.');
    }

// --- 12. PERKHIDMATAN IKTISAS (OS29000) ---

    public function editOs29000($kod, $tahun)
    {
        // 1. Dapatkan/Cipta Rekod Induk
        $dbusData = Dbus::firstOrCreate(
            ['kod_objek' => $kod, 'tahun' => $tahun],
            ['perkara' => 'PERKHIDMATAN IKTISAS', 'jenis' => 'OS', 'jumlah' => 0.00]
        );

        // 2. Ambil Data Sedia Ada
        $pecahanData = DbusPecahanOS29::where('dbus_id', $dbusData->id)->get();
        $pecahanMap = $pecahanData->keyBy('kod_pecahan_sub')->toArray();

        // 3. DATA TERPERINCI SEBIJIK PDF (2 PERKARA UTAMA x 3 PECAHAN)
        $items = [
            // ============================================================
            // TAB 1: PERKHIDMATAN DIBELI (OS29100)
            // ============================================================
            '29100' => [
                'title' => '29100 PERKHIDMATAN DIBELI',
                'groups' => [
                    
                    // ---------------------------------------------------------
                    // BIL 1: YURAN KURSUS / TEAM BUILDING
                    // ---------------------------------------------------------
                    [
                        'title' => '1. YURAN KURSUS - (a) Perkhidmatan Latihan & Pensyarah (OL29107)',
                        'items' => [
                            ['sub' => 'B1_29107_PEN', 'butiran' => 'Bayaran Pensyarah (RM480 x Hari)', 'q'=>0, 'f'=>2, 'k'=>480.00],
                            ['sub' => 'B1_29107_JUSA', 'butiran' => 'Gred Utama/Khas A, B, C (RM460)', 'q'=>0, 'f'=>2, 'k'=>460.00],
                            ['sub' => 'B1_29107_G54', 'butiran' => 'Gred 53-54 (RM300)', 'q'=>0, 'f'=>2, 'k'=>300.00],
                            ['sub' => 'B1_29107_G52', 'butiran' => 'Gred 45-52 (RM200)', 'q'=>0, 'f'=>2, 'k'=>200.00],
                            ['sub' => 'B1_29107_G44', 'butiran' => 'Gred 41-44 (RM100)', 'q'=>0, 'f'=>2, 'k'=>100.00],
                            ['sub' => 'B1_29107_G40', 'butiran' => 'Gred 17-40 (RM50)', 'q'=>0, 'f'=>2, 'k'=>50.00],
                            ['sub' => 'B1_29107_G16', 'butiran' => 'Gred 1-16 (RM50)', 'q'=>0, 'f'=>2, 'k'=>50.00],
                        ]
                    ],
                    [
                        'title' => '1. YURAN KURSUS - (b) Perkhidmatan Persediaan Makanan (OL29126)',
                        'items' => [
                            ['sub' => 'B1_29126_PEN', 'butiran' => 'Bayaran Pensyarah', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B1_29126_JUSA', 'butiran' => 'Gred Utama/Khas A, B, C', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B1_29126_G54', 'butiran' => 'Gred 53-54', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B1_29126_G52', 'butiran' => 'Gred 45-52', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B1_29126_G44', 'butiran' => 'Gred 41-44', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B1_29126_G40', 'butiran' => 'Gred 17-40', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B1_29126_G16', 'butiran' => 'Gred 1-16', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ],
                    [
                        'title' => '1. YURAN KURSUS - (c) Perkhidmatan Yang Lain (OL29199)',
                        'items' => [
                            ['sub' => 'B1_29199_TOL', 'butiran' => 'Bayaran Tol (RM40)', 'q'=>5, 'f'=>2, 'k'=>40.00],
                            ['sub' => 'B1_29199_JUSA', 'butiran' => 'Pakej Persidangan (Jusa C)', 'q'=>1, 'f'=>2, 'k'=>200.00],
                            ['sub' => 'B1_29199_G52', 'butiran' => 'Pakej Persidangan (Gred 45-52)', 'q'=>5, 'f'=>2, 'k'=>200.00],
                            ['sub' => 'B1_29199_G44', 'butiran' => 'Pakej Persidangan (Gred 41-44)', 'q'=>13, 'f'=>2, 'k'=>200.00],
                            ['sub' => 'B1_29199_G40', 'butiran' => 'Pakej Persidangan (Gred 17-40)', 'q'=>19, 'f'=>2, 'k'=>200.00],
                            ['sub' => 'B1_29199_G16', 'butiran' => 'Pakej Persidangan (Gred 1-16)', 'q'=>7, 'f'=>2, 'k'=>200.00],
                            ['sub' => 'B1_29199_YURAN', 'butiran' => 'Yuran Kursus', 'q'=>6, 'f'=>2, 'k'=>50.00],
                        ]
                    ],

                    // ---------------------------------------------------------
                    // BIL 2: METER READING MESIN FOTOSTAT
                    // ---------------------------------------------------------
                    [
                        'title' => '2. METER READING - (a) Perkhidmatan Latihan (OL29107)',
                        'items' => [
                            ['sub' => 'B2_29107_PEN', 'butiran' => 'Bayaran Pensyarah', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29107_JUSA', 'butiran' => 'Gred Utama/Khas A, B, C', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29107_G54', 'butiran' => 'Gred 53-54', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29107_G52', 'butiran' => 'Gred 45-52', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29107_G44', 'butiran' => 'Gred 41-44', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29107_G40', 'butiran' => 'Gred 17-40', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29107_G16', 'butiran' => 'Gred 1-16', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ],
                    [
                        'title' => '2. METER READING - (b) Perkhidmatan Persediaan Makanan (OL29126)',
                        'items' => [
                            ['sub' => 'B2_29126_PEN', 'butiran' => 'Bayaran Pensyarah', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29126_JUSA', 'butiran' => 'Gred Utama/Khas A, B, C', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29126_G54', 'butiran' => 'Gred 53-54', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29126_G52', 'butiran' => 'Gred 45-52', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29126_G44', 'butiran' => 'Gred 41-44', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29126_G40', 'butiran' => 'Gred 17-40', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29126_G16', 'butiran' => 'Gred 1-16', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ],
                    [
                        'title' => '2. METER READING - (c) Perkhidmatan Yang Lain (OL29199)',
                        'items' => [
                            ['sub' => 'B2_29199_WARNA', 'butiran' => 'Meter Reading Warna (2500 keping x RM0.40)', 'q'=>1, 'f'=>12, 'k'=>832.00],
                            ['sub' => 'B2_29199_BW', 'butiran' => 'Meter Reading Hitam/Putih (12000 keping x RM0.05)', 'q'=>1, 'f'=>12, 'k'=>540.00],
                            ['sub' => 'B2_29199_L1', 'butiran' => 'Sila Nyatakan', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29199_L2', 'butiran' => 'Sila Nyatakan', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29199_L3', 'butiran' => 'Sila Nyatakan', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'B2_29199_L4', 'butiran' => 'Sila Nyatakan', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ]
                ]
            ],

            // ============================================================
            // TAB 2: PERCETAKAN (OS29200) - Rujuk PDF OS29200
            // ============================================================
            '29200' => [
                'title' => '29200 PERCETAKAN',
                'groups' => [
                    [
                        'title' => 'PENERBITAN KERAJAAN (OL29201)',
                        'items' => [
                            ['sub' => 'OL29201_NOTA', 'butiran' => 'Nota Transkrip Unit Guaman', 'q'=>1, 'f'=>9, 'k'=>2800.00],
                            ['sub' => 'OL29201_LAIN', 'butiran' => 'Sila Nyatakan', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ],
                    [
                        'title' => 'BORANG & KEPALA SURAT (OL29202)',
                        'items' => [
                            ['sub' => 'OL29202_BORANG', 'butiran' => 'Mencetak Borang, Kad, Kulit Fail & Buku', 'q'=>2000, 'f'=>1, 'k'=>0.70],
                            ['sub' => 'OL29202_SURAT', 'butiran' => 'Mencetak Kepala Surat', 'q'=>5000, 'f'=>1, 'k'=>0.60],
                            ['sub' => 'OL29202_FAIL', 'butiran' => 'Mencetak Fail Perbicaraan Jenayah', 'q'=>1000, 'f'=>1, 'k'=>2.33],
                        ]
                    ],
                    [
                        'title' => 'PERCETAKAN LAIN (OL29299)',
                        'items' => [
                            ['sub' => 'OL29299_MINIT', 'butiran' => 'Kertas Minit', 'q'=>2000, 'f'=>1, 'k'=>0.25],
                            ['sub' => 'OL29299_LAIN', 'butiran' => 'Sila Nyatakan', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ]
                ]
            ],

            // ============================================================
            // TAB 3: SAMBILAN (OS29300) - Rujuk PDF OS29300
            // ============================================================
            '29300' => [
                'title' => '29300 KAKITANGAN KONTRAK',
                'groups' => [
                    [
                        'title' => 'GAJI & UPAH (OL29301)',
                        'items' => [
                            ['sub' => 'OL29301_MARYAM', 'butiran' => 'MARYAM SAKINAH BINTI MOHD NOR (L9)', 'q'=>1, 'f'=>12, 'k'=>2580.00],
                            ['sub' => 'OL29301_SYAZA', 'butiran' => 'SYAZA NUR BINTI SHARIF (L9)', 'q'=>1, 'f'=>12, 'k'=>2580.00],
                        ]
                    ],
                    [
                        'title' => 'ELAUN LEBIH MASA & LAIN (OL29302)',
                        'items' => [
                            ['sub' => 'OL29302_MARYAM', 'butiran' => 'Elaun - Maryam Sakinah', 'q'=>1, 'f'=>12, 'k'=>1015.00],
                            ['sub' => 'OL29302_SYAZA', 'butiran' => 'Elaun - Syaza Nur', 'q'=>1, 'f'=>12, 'k'=>1015.00],
                        ]
                    ],
                    [
                        'title' => 'SUMBANGAN KWSP (OL29303)',
                        'items' => [
                            ['sub' => 'OL29303_MARYAM', 'butiran' => 'KWSP - Maryam Sakinah (11%+12%)', 'q'=>1, 'f'=>12, 'k'=>375.65],
                            ['sub' => 'OL29303_SYAZA', 'butiran' => 'KWSP - Syaza Nur (11%+12%)', 'q'=>1, 'f'=>12, 'k'=>375.65],
                        ]
                    ]
                ]
            ],

            // ============================================================
            // TAB 4: KERAIAN (OS29400) - Rujuk PDF OS29400
            // ============================================================
            '29400' => [
                'title' => '29400 KERAIAN & HOSPITALITI',
                'groups' => [
                    [
                        'title' => 'MAKAN & MINUM JEMPUTAN LUAR (OL29401)',
                        'items' => [
                            ['sub' => 'OL29401_JUSA', 'butiran' => 'Gred Utama/Khas A, B, C', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29401_G54', 'butiran' => 'Gred 53-54', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29401_G52', 'butiran' => 'Gred 45-52', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29401_G44', 'butiran' => 'Gred 41-44', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29401_G40', 'butiran' => 'Gred 17-40', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29401_G16', 'butiran' => 'Gred 1-16', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ],
                    [
                        'title' => 'ELAUN PENGINAPAN (OL29402)',
                        'items' => [
                            ['sub' => 'OL29402_JUSA', 'butiran' => 'Gred Utama/Khas A, B, C', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29402_G54', 'butiran' => 'Gred 53-54', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29402_G52', 'butiran' => 'Gred 45-52', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29402_G44', 'butiran' => 'Gred 41-44', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29402_G40', 'butiran' => 'Gred 17-40', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29402_G16', 'butiran' => 'Gred 1-16', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ],
                    [
                        'title' => 'KERAIAN PEJABAT - MESYUARAT DALAMAN (OL29411)',
                        'items' => [
                            ['sub' => 'OL29411_JUSA', 'butiran' => 'Gred Utama/Khas A', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29411_JUSA_BC', 'butiran' => 'Gred Utama/Khas B dan C (RM10)', 'q'=>2, 'f'=>2, 'k'=>10.00],
                            ['sub' => 'OL29411_G14', 'butiran' => 'Gred 14-15', 'q'=>1, 'f'=>2, 'k'=>10.00],
                            ['sub' => 'OL29411_G11', 'butiran' => 'Gred 11-13', 'q'=>4, 'f'=>2, 'k'=>10.00],
                            ['sub' => 'OL29411_G9', 'butiran' => 'Gred 9-10', 'q'=>14, 'f'=>2, 'k'=>10.00],
                            ['sub' => 'OL29411_G5', 'butiran' => 'Gred 5-8', 'q'=>7, 'f'=>2, 'k'=>10.00],
                            ['sub' => 'OL29411_G1', 'butiran' => 'Gred 1-4', 'q'=>24, 'f'=>2, 'k'=>10.00],
                        ]
                    ],
                    [
                        'title' => 'BAYARAN LAIN - TIP/TOL (OL29499)',
                        'items' => [
                            ['sub' => 'OL29499_JUSA', 'butiran' => 'Gred Utama/Khas A, B, C', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29499_G54', 'butiran' => 'Gred 53-54', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29499_G52', 'butiran' => 'Gred 45-52', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29499_G44', 'butiran' => 'Gred 41-44', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29499_G40', 'butiran' => 'Gred 17-40', 'q'=>0, 'f'=>0, 'k'=>0],
                            ['sub' => 'OL29499_G16', 'butiran' => 'Gred 1-16', 'q'=>0, 'f'=>0, 'k'=>0],
                        ]
                    ]
                ]
            ]
        ];

        return view('pentadbiran.dbus.pecahan_OS29000', compact('dbusData', 'pecahanMap', 'kod', 'tahun', 'items'));
    }

  public function updateOs29000(Request $request)
    {
        $validated = $request->validate([
            'master_id' => 'required|exists:dbuses,id', 'tahun' => 'required', 'data' => 'required|array'
        ]);
        
        $dbusMaster = Dbus::findOrFail($validated['master_id']);
        $tahun = $validated['tahun'];
        $grandTotal = 0;

        DB::transaction(function () use ($dbusMaster, $request, &$grandTotal, $tahun) {
            DbusPecahanOS29::where('dbus_id', $dbusMaster->id)->delete();

            foreach ($request->input('data') as $subKod => $item) {
                $qty = (float)($item['kuantiti'] ?? 0);
                $freq = (float)($item['kekerapan'] ?? 0);
                $kos = (float)($item['anggaran_kos'] ?? 0);
                $jumlah = $qty * $freq * $kos;

                if ($jumlah > 0 || $qty > 0 || $kos > 0) {
                    
                    // --- LOGIK PENTING: TUKAR SUB-KOD KEPADA KOD OL STANDARD ---
                    $kodOl = ''; 
                    
                    // Cek corak dalam string sub-kod
                    if (str_contains($subKod, '29107')) $kodOl = 'OL29107';
                    elseif (str_contains($subKod, '29126')) $kodOl = 'OL29126';
                    elseif (str_contains($subKod, '29199')) $kodOl = 'OL29199';
                    elseif (str_contains($subKod, '29201')) $kodOl = 'OL29201';
                    elseif (str_contains($subKod, '29202')) $kodOl = 'OL29202';
                    elseif (str_contains($subKod, '29299')) $kodOl = 'OL29299';
                    elseif (str_contains($subKod, '29301')) $kodOl = 'OL29301';
                    elseif (str_contains($subKod, '29302')) $kodOl = 'OL29302';
                    elseif (str_contains($subKod, '29303')) $kodOl = 'OL29303';
                    elseif (str_contains($subKod, '29401')) $kodOl = 'OL29401';
                    elseif (str_contains($subKod, '29402')) $kodOl = 'OL29402';
                    elseif (str_contains($subKod, '29411')) $kodOl = 'OL29411';
                    elseif (str_contains($subKod, '29499')) $kodOl = 'OL29499';
                    else $kodOl = substr($subKod, 0, 7); // Fallback

                    DbusPecahanOS29::create([
                        'dbus_id' => $dbusMaster->id,
                        'kod_ol' => $kodOl, // Simpan sebagai kod standard (OL29107)
                        'kod_pecahan_sub' => $subKod, // Simpan kod unik (BIL1_...)
                        'butiran' => $item['butiran'] ?? '',
                        'kuantiti' => $qty,
                        'kekerapan' => $freq,
                        'anggaran_kos' => $kos,
                        'jumlah' => $jumlah,
                        'tahun' => $tahun,
                        'catatan' => $item['catatan'] ?? ''
                    ]);
                    $grandTotal += $jumlah;
                }
            }

            // Update Master
            $dbusMaster->update(['jumlah' => $grandTotal]);
            Dbus::updateOrCreate(['kod_objek' => 'OS29000', 'tahun' => $tahun], ['jumlah' => $grandTotal]);

            // Reset OL Sums
            $allOLs = ['OL29107','OL29126','OL29199','OL29201','OL29202','OL29299','OL29301','OL29303','OL29401','OL29402','OL29411','OL29499'];
            Dbus::whereIn('kod_objek', $allOLs)->where('tahun', $tahun)->update(['jumlah' => 0]);

            // Recalculate OL Sums & Update DBUS Table
            $olSums = DbusPecahanOS29::where('dbus_id', $dbusMaster->id)
                        ->select('kod_ol', DB::raw('SUM(jumlah) as total'))
                        ->groupBy('kod_ol')->get();
            
            foreach($olSums as $sum) {
                $info = $this->findObjekInfo($sum->kod_ol);
                Dbus::updateOrCreate(
                    ['kod_objek' => $sum->kod_ol, 'tahun' => $tahun],
                    ['perkara' => $info['perkara'] ?? 'Perkhidmatan', 'jenis' => 'OL', 'jumlah' => $sum->total]
                );
            }
        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', 'OS29000 berjaya dikemaskini.');
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