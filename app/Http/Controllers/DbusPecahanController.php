<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dbus;
use App\Models\DbusPegawai; 
use App\Models\DbusPecahanOs14;
use App\Models\DbusPecahanOs15;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DbusPecahanController extends Controller
{
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

    public function editOt(Request $request, $kod)
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

        $pecahanData = DbusPecahanOs14::where('tahun', $tahun)->get();

        return view('pentadbiran.dbus.pecahan_OS14000', compact('dbusData', 'tahun', 'pecahanData'));
    }

    public function updateOt(Request $request)
    {
        $clean = fn($v) => (float) str_replace(',', '', $v ?? '0');

        $validatedData = $request->validate([
            // Menggunakan Dbus::class Rule untuk mengatasi ralat DB
            'id' => ['required', Rule::exists(Dbus::class, 'id')], 
            
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

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', "Rekod {$dbus->kod_objek} dan pecahannya berjaya dikemaskini.");
    }

    public function editOs15(Request $request, $kod)
    {
        $tahun = $request->tahun ?? date('Y');
        $ol_kod = 'OL15119'; 
        
        $dbusData = Dbus::where('kod_objek', $ol_kod)
                        ->where('tahun', $tahun)
                        ->first();

        if (!$dbusData) {
             $info = $this->findObjekInfo($ol_kod);
             $dbusData = Dbus::create([
                 'kod_objek' => $ol_kod, 
                 'tahun' => $tahun, 
                 'perkara' => $info['perkara'] ?? 'Faedah Kewangan Lain / Elaun Perkakasan', 
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
            'master_id' => ['required', Rule::exists(Dbus::class, 'id')],
            
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
        $masterTotal = $validatedData['master_grand_total']; 

        DB::transaction(function () use ($master_dbus, $masterTotal, $tahun, $validatedData) {
            
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
            
            $master_dbus->update(['jumlah' => $masterTotal]);

            Dbus::updateOrCreate(
                ['kod_objek' => 'OS15000', 'tahun' => $tahun],
                ['perkara' => 'FAEDAH-FAEDAH KEWANGAN YANG LAIN', 'jenis' => 'OS', 'jumlah' => $masterTotal]
            );

        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', "Rekod OS15000 dan pecahannya berjaya dikemaskini.");
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

    public function editOs15000(Request $request, $kod)
    {
        $tahun = $request->tahun ?? date('Y');
        $ol_kod = 'OL15119'; 
        
        $dbusData = Dbus::where('kod_objek', $ol_kod)
                        ->where('tahun', $tahun)
                        ->first();

        if (!$dbusData) {
             $info = $this->findObjekInfo($ol_kod);
             $dbusData = Dbus::create([
                 'kod_objek' => $ol_kod, 
                 'tahun' => $tahun, 
                 'perkara' => $info['perkara'] ?? 'Faedah Kewangan Lain / Elaun Perkakasan', 
                 'jenis' => 'OL', 
                 'jumlah' => 0.00
             ]);
        }
        
        $pecahanData = DbusPecahanOs15::where('tahun', $tahun)->get();

        return view('pentadbiran.dbus.pecahan_OS15000', compact('dbusData', 'tahun', 'pecahanData', 'kod'));
    }
    
    public function updateOs15000(Request $request)
    {
        $validatedData = $request->validate([
            'master_id' => ['required', Rule::exists(Dbus::class, 'id')],
            
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
        $masterTotal = $validatedData['master_grand_total']; 

        DB::transaction(function () use ($master_dbus, $masterTotal, $tahun, $validatedData) {
            
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
            
            $master_dbus->update(['jumlah' => $masterTotal]);

            Dbus::updateOrCreate(
                ['kod_objek' => 'OS15000', 'tahun' => $tahun],
                ['perkara' => 'FAEDAH-FAEDAH KEWANGAN YANG LAIN', 'jenis' => 'OS', 'jumlah' => $masterTotal]
            );

        });

        return redirect()->route('pentadbiran.dbus.index', ['tahun' => $tahun])->with('success', "Rekod OS15000 dan pecahannya berjaya dikemaskini.");
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

    public function editOs15000(Request $request, $kod)
    {
        $tahun = $request->tahun ?? date('Y');
        $ol_kod = 'OL15119'; 
        
        $dbusData = Dbus::where('kod_objek', $ol_kod)
                        ->where('tahun', $tahun)
                        ->first();

        if (!$dbusData) {
             $info = $this->findObjekInfo($ol_kod);
             $dbusData = Dbus::create([
                 'kod_objek' => $ol_kod, 
                 'tahun' => $tahun, 
                 'perkara' => $info['perkara'] ?? 'Faedah Kewangan Lain / Elaun Perkakasan', 
                 'jenis' => 'OL', 
                 'jumlah' => 0.00
             ]);
        }
        
        $pecahanData = DbusPecahanOs15::where('tahun', $tahun)->get();

        return view('pentadbiran.dbus.pecahan_OS15000', compact('dbusData', 'tahun', 'pecahanData', 'kod'));
    }
    
    public function updateOs15000(Request $request)
    {
        $validatedData = $request->validate([
            'master_id' => ['required', Rule::exists(Dbus::class, 'id')],
            
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
        $masterTotal = $validatedData['master_grand_total']; 

        DB::transaction(function () use ($master_dbus, $masterTotal, $tahun, $validatedData) {
            
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
            
            $master_dbus->update(['jumlah' => $masterTotal]);

            Dbus::updateOrCreate(
                ['kod_objek' => 'OS15000', 'tahun' => $tahun],
                ['perkara' => 'FAEDAH-FAW