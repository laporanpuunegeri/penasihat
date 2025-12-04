<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dbus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class DbusController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', 2027);
        
        // Pastikan guna model yang betul (Dbus::class)
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
        $tahun = 2027; 
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

    // Helper functions
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
        // Struktur penuh dikekalkan (saya pendekkan di sini utk jimat ruang, tapi pastikan ia ada dalam file anda)
        return [
            'OA10000' => [
                'perkara' => 'EMOLUMEN', 'jenis' => 'OA',
                'items' => [
                    'OS11000' => ['perkara' => 'GAJI DAN UPAHAN', 'jenis' => 'OS', 'items' => ['OL11101' => ['perkara' => 'Gaji Biasa Kakitangan Awam', 'jenis' => 'OL']]],
                    'OS12000' => ['perkara' => 'ELAUN DAN IMBUHAN TETAP', 'jenis' => 'OS', 'items' => [
                            'OL12101' => ['perkara' => 'Elaun Khidmat Awam', 'jenis' => 'OL'],
                            'OL12102' => ['perkara' => 'Elaun Bantuan Sewa Rumah', 'jenis' => 'OL'],
                            'OL12103' => ['perkara' => 'Elaun Keraian', 'jenis' => 'OL'],
                            'OL12106' => ['perkara' => 'Imbuhan Tetap Jawatan Utama dan Gred Khas', 'jenis' => 'OL'],
                            'OL12107' => ['perkara' => 'Bayaran Insentif Perkhidmatan Kritikal', 'jenis' => 'OL'],
                            'OL12108' => ['perkara' => 'Bayaran Insentif Khas Pegawai Profesional', 'jenis' => 'OL'],
                            'OL12109' => ['perkara' => 'Bayaran Insentif Tugas Kewangan', 'jenis' => 'OL'],
                            'OL12199' => ['perkara' => 'Elaun Tetap Lain', 'jenis' => 'OL'],
                    ]],
                    'OS13000' => ['perkara' => 'SUMBANGAN BERKANUN UNTUK KAKITANGAN', 'jenis' => 'OS', 'items' => ['OL13101' => ['perkara' => 'Sumbangan Berkanun (KWSP)', 'jenis' => 'OL']]],
                    'OS14000' => ['perkara' => 'BAYARAN LEBIH MASA', 'jenis' => 'OS', 'items' => ['OL14101' => ['perkara' => 'Bayaran Lebih Masa Kakitangan Awam', 'jenis' => 'OL']]],
                    'OS15000' => ['perkara' => 'FAEDAH-FAEDAH KEWANGAN YANG LAIN', 'jenis' => 'OS', 'items' => [
                            'OL15101' => ['perkara' => 'Bayaran dan Bayaran Balik Utiliti', 'jenis' => 'OL'],
                            'OL15102' => ['perkara' => 'Bayaran Balik Pasport/Lesen/Yuran', 'jenis' => 'OL'],
                            'OL15110' => ['perkara' => 'Pemberian Alat Komunikasi', 'jenis' => 'OL'],
                            'OL15111' => ['perkara' => 'Bayaran Kemudahan Perubatan', 'jenis' => 'OL'],
                            'OL15112' => ['perkara' => 'Pelbagai Elaun Pakaian', 'jenis' => 'OL'],
                            'OL15113' => ['perkara' => 'Pemberian Anugerah Perkhidmatan Cemerlang (APC)', 'jenis' => 'OL'],
                            'OL15114' => ['perkara' => 'Pelbagai Kemudahan Tambang Pengangkutan', 'jenis' => 'OL'],
                            'OL15119' => ['perkara' => 'Faedah Kewangan Lain / Elaun Perkakasan', 'jenis' => 'OL'],
                    ]],
                ]
            ],
            'OA20000' => [
                'perkara' => 'PERKHIDMATAN & BEKALAN', 'jenis' => 'OA',
                'items' => [
                    'OS21000' => ['perkara' => 'PERBELANJAAN PERJALANAN & SARA HIDUP', 'jenis' => 'OS', 'items' => ['OL21101' => ['perkara' => 'Makanan & Minuman', 'jenis' => 'OL'], 'OL21102' => ['perkara' => 'Penginapan', 'jenis' => 'OL'], 'OL21104' => ['perkara' => 'Elaun Perjalanan', 'jenis' => 'OL'], 'OL21106' => ['perkara' => 'Bayaran Kapal Terbang', 'jenis' => 'OL']]],
                    'OS22000' => ['perkara' => 'PENGANGKUTAN BARANG-BARANG', 'jenis' => 'OS', 'items' => ['OL22155' => ['perkara' => 'Pengangkutan Barang', 'jenis' => 'OL']]],
                    'OS23000' => ['perkara' => 'PERHUBUNGAN DAN UTILITI', 'jenis' => 'OS', 'items' => ['OL23101' => ['perkara' => 'Bayaran Pos', 'jenis' => 'OL'], 'OL23102' => ['perkara' => 'Telefon', 'jenis' => 'OL'], 'OL23103' => ['perkara' => 'Telex', 'jenis' => 'OL'], 'OL23199' => ['perkara' => 'Perkhidmatan Lain', 'jenis' => 'OL'], 'OL23201' => ['perkara' => 'Elektrik', 'jenis' => 'OL'], 'OL23202' => ['perkara' => 'Air', 'jenis' => 'OL'], 'OL23204' => ['perkara' => 'Pembentungan', 'jenis' => 'OL']]],
                    'OS24000' => ['perkara' => 'SEWAAN', 'jenis' => 'OS', 'items' => ['OL24201' => ['perkara' => 'Sewa Bangunan Kediaman', 'jenis' => 'OL'], 'OL24202' => ['perkara' => 'Sewa Bangunan Pejabat', 'jenis' => 'OL'], 'OL24299' => ['perkara' => 'Sewa Bangunan Lain', 'jenis' => 'OL'], 'OL24301' => ['perkara' => 'Sewa Kenderaan Penumpang', 'jenis' => 'OL'], 'OL24305' => ['perkara' => 'Sewa Kenderaan Konsesi', 'jenis' => 'OL'], 'OL24399' => ['perkara' => 'Sewa Kenderaan Lain', 'jenis' => 'OL'], 'OL24501' => ['perkara' => 'Sewa Alat Pejabat', 'jenis' => 'OL'], 'OL24502' => ['perkara' => 'Sewa Perabot', 'jenis' => 'OL'], 'OL24699' => ['perkara' => 'Sewa Elektronik Lain', 'jenis' => 'OL'], 'OL24799' => ['perkara' => 'Sewa Elektrik Lain', 'jenis' => 'OL']]],
                    'OS25000' => ['perkara' => 'BAHAN MAKANAN DAN MINUMAN', 'jenis' => 'OS', 'items' => ['OL25499' => ['perkara' => 'Makanan Lain', 'jenis' => 'OL'], 'OL25601' => ['perkara' => 'Minuman Tidak Berkabonat', 'jenis' => 'OL']]],
                    'OS26000' => ['perkara' => 'BEKALAN BAHAN MENTAH', 'jenis' => 'OS', 'items' => ['OL26121' => ['perkara' => 'Alat Ganti Pejabat', 'jenis' => 'OL'], 'OL26126' => ['perkara' => 'Alat Ganti Elektronik', 'jenis' => 'OL'], 'OL26131' => ['perkara' => 'Alat Ganti Elektrik', 'jenis' => 'OL'], 'OL26201' => ['perkara' => 'Petrol', 'jenis' => 'OL'], 'OL26202' => ['perkara' => 'Diesel', 'jenis' => 'OL'], 'OL26206' => ['perkara' => 'Pelincir', 'jenis' => 'OL'], 'OL26299' => ['perkara' => 'Bahan Api Lain', 'jenis' => 'OL'], 'OL26701' => ['perkara' => 'Bahan Pengecat', 'jenis' => 'OL']]],
                    'OS27000' => ['perkara' => 'BEKALAN DAN BAHAN LAIN', 'jenis' => 'OS', 'items' => ['OL27101' => ['perkara' => 'Surat Khabar', 'jenis' => 'OL'], 'OL27102' => ['perkara' => 'Alat Tulis Pejabat', 'jenis' => 'OL'], 'OL27103' => ['perkara' => 'Alat Tulis Komputer', 'jenis' => 'OL'], 'OL27199' => ['perkara' => 'Bekalan Pejabat Lain', 'jenis' => 'OL'], 'OL27299' => ['perkara' => 'Bekalan Am Lain', 'jenis' => 'OL'], 'OL27401' => ['perkara' => 'Ubat dan Dadah', 'jenis' => 'OL'], 'OL27499' => ['perkara' => 'Bekalan Perubatan Lain', 'jenis' => 'OL'], 'OL27605' => ['perkara' => 'Pakaian Seragam', 'jenis' => 'OL'], 'OL27699' => ['perkara' => 'Pakaian Lain', 'jenis' => 'OL']]],
                    'OS28000' => ['perkara' => 'PENYELENGGARAAN & PEMBAIKAN', 'jenis' => 'OS', 'items' => ['OL28102' => ['perkara' => 'Bangunan Pejabat', 'jenis' => 'OL'], 'OL28199' => ['perkara' => 'Bangunan Lain', 'jenis' => 'OL'], 'OL28301' => ['perkara' => 'Kenderaan Penumpang', 'jenis' => 'OL'], 'OL28307' => ['perkara' => 'Kenderaan Konsesi', 'jenis' => 'OL'], 'OL28501' => ['perkara' => 'Alat Pejabat', 'jenis' => 'OL'], 'OL28599' => ['perkara' => 'Perabot', 'jenis' => 'OL'], 'OL28601' => ['perkara' => 'Komputer', 'jenis' => 'OL'], 'OL28699' => ['perkara' => 'Elektronik Lain', 'jenis' => 'OL'], 'OL28701' => ['perkara' => 'Hawa Dingin', 'jenis' => 'OL'], 'OL28799' => ['perkara' => 'Elektrik Lain', 'jenis' => 'OL'], 'OL28801' => ['perkara' => 'Telefon/Telex', 'jenis' => 'OL'], 'OL28899' => ['perkara' => 'Perhubungan Lain', 'jenis' => 'OL'], 'OL28911' => ['perkara' => 'Pembersihan', 'jenis' => 'OL']]],
                    'OS29000' => ['perkara' => 'PERKHIDMATAN IKTISAS', 'jenis' => 'OS', 'items' => ['OL29107' => ['perkara' => 'Latihan/Pensyarah', 'jenis' => 'OL'], 'OL29126' => ['perkara' => 'Persediaan Makanan', 'jenis' => 'OL'], 'OL29199' => ['perkara' => 'Perkhidmatan Lain', 'jenis' => 'OL'], 'OL29201' => ['perkara' => 'Penerbitan', 'jenis' => 'OL'], 'OL29202' => ['perkara' => 'Pencetakan', 'jenis' => 'OL'], 'OL29299' => ['perkara' => 'Percetakan Lain', 'jenis' => 'OL'], 'OL29301' => ['perkara' => 'Gaji Sambilan', 'jenis' => 'OL'], 'OL29303' => ['perkara' => 'KWSP Sambilan', 'jenis' => 'OL'], 'OL29401' => ['perkara' => 'Keraian Makanan', 'jenis' => 'OL'], 'OL29402' => ['perkara' => 'Keraian Penginapan', 'jenis' => 'OL'], 'OL29411' => ['perkara' => 'Keraian Pejabat', 'jenis' => 'OL'], 'OL29499' => ['perkara' => 'Bayaran Lain', 'jenis' => 'OL']]],
                ]
            ],
        ];
    }
}