<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKesMahkamah;
use App\Models\LampiranKesMahkamah;
use App\Models\Agensi; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporankesmahkamahController extends Controller
{
    /**
     * 1. INDEX: Senarai Laporan (Default Bulan Semasa)
     */
    public function index(Request $request)
    {
        $query = LaporanKesMahkamah::query();
        $user = Auth::user();

        // 1. Tetapkan Default: Bulan Semasa (date('n'))
        // Jika user pilih 'all', set variable jadi 'all'
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = date('Y');

        // 2. Logic Filter Bulan (Ikut Tarikh Sebutan)
        if ($bulanPilihan !== 'all') {
            $query->whereMonth('tarikh_sebutan', $bulanPilihan)
                  ->whereYear('tarikh_sebutan', $tahunSemasa);
        }

        // 3. Logic Filter Role
        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } elseif ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        $data = $query->orderBy('tarikh_sebutan', 'desc')->get();

        return view('laporankesmahkamah.index', compact('data'));
    }

    /**
     * 2. CREATE: Borang Daftar
     */
    public function create()
    {
        $user = Auth::user();

        // Tarik Agensi untuk Dropdown
        $agensiList = Agensi::where('negeri', $user->negeri)
                            ->orWhere('negeri', 'Persekutuan')
                            ->orderBy('nama_agensi', 'ASC')
                            ->pluck('nama_agensi');

        return view('laporankesmahkamah.create', compact('agensiList'));
    }

    /**
     * 3. STORE: Simpan Data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_kes' => 'required|string',
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required|string',
            'isu' => 'required|string',
            'skop_tugas' => 'required|string',
            'ringkasan_hujahan' => 'required|string',
            'status' => 'required|string',
        ]);

        $user = Auth::user();

        LaporanKesMahkamah::create(array_merge($validated, [
            'user_id' => $user->id,
            'negeri' => $user->negeri,
            'perkara' => $request->perkara,
            'tarikh_daftar' => $request->tarikh_daftar ?? now(),
            'hantar_kepada_boss' => $request->has('hantar_kepada_boss'),
        ]));

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya disimpan.');
    }

    /**
     * 4. EDIT: Paparan Kemaskini
     */
    public function edit($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);
        $user = Auth::user();

        $agensiList = Agensi::where('negeri', $user->negeri)
                            ->orWhere('negeri', 'Persekutuan')
                            ->orderBy('nama_agensi', 'ASC')
                            ->pluck('nama_agensi');

        return view('laporankesmahkamah.edit', compact('laporan', 'agensiList'));
    }

    /**
     * 5. UPDATE: Simpan Kemaskini
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_kes' => 'required|string',
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required|string',
            'isu' => 'required|string',
            'skop_tugas' => 'required|string',
            'ringkasan_hujahan' => 'required|string',
            'status' => 'required|string',
        ]);

        $laporan = LaporanKesMahkamah::findOrFail($id);
        
        $laporan->perkara = $request->perkara;
        $laporan->tarikh_daftar = $request->tarikh_daftar;
        $laporan->hantar_kepada_boss = $request->has('hantar_kepada_boss');
        
        $laporan->update($validated);

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya dikemaskini.');
    }

    /**
     * 6. DESTROY: Padam Data
     */
    public function destroy($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);
        $laporan->delete();

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya dipadam.');
    }

    // === 💾 LAMPIRAN KES MAHKAMAH (LAMPIRAN II) ===

    public function lampiran(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $lampiran = LampiranKesMahkamah::where('user_id', $user->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('kategori');

        $kategori_list = [
            'Perlembagaan', 'Tanah / PBT', 'Rujukan tanah',
            'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan',
            'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain',
        ];

        return view('lampiran.index', compact('lampiran', 'kategori_list', 'bulan', 'tahun'));
    }

    public function storeLampiran(Request $request)
    {
        $user = Auth::user();
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $data = $request->input('data', []);

        DB::transaction(function () use ($user, $bulan, $tahun, $data) {
            // Padam lama
            LampiranKesMahkamah::where('user_id', $user->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->delete();

            // Simpan baru
            foreach ($data as $kategori => $val) {
                LampiranKesMahkamah::create([
                    'user_id'   => $user->id,
                    'negeri'    => $user->negeri,
                    'kategori'  => $kategori,
                    'bil_aktif' => intval($val['bil_aktif'] ?? 0),
                    'majistret' => intval($val['majistret'] ?? 0),
                    'sesi'      => intval($val['sesi'] ?? 0),
                    'tinggi'    => intval($val['tinggi'] ?? 0),
                    'rayuan'    => intval($val['rayuan'] ?? 0),
                    'persk'     => intval($val['persk'] ?? 0),
                    'status'    => $val['status'] ?? '-',
                    'bulan'     => $bulan,
                    'tahun'     => $tahun,
                ]);
            }
        });

        return redirect()->route('lampiran.index', ['bulan' => $bulan, 'tahun' => $tahun])
                         ->with('success', 'Lampiran berjaya dikemaskini.');
    }

    // === 📊 PECAHAN BULAN (Data Grafik - STATUS GROUPING) ===

    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = Carbon::create()->month($bulan)->format('F');

        // Ambil semua string status untuk bulan tersebut
        $semuaStatus = LaporanKesMahkamah::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->pluck('status');

        // Inisialisasi Kaunter Kategori Ringkas
        $kategoriRingkas = [
            'Selesai / Ditutup' => 0,
            'Dalam Perbicaraan / Sebutan' => 0,
            'Tangguh / KIV' => 0,
            'Lain-lain / Dalam Tindakan' => 0
        ];

        // Loop dan kategorikan setiap status
        foreach ($semuaStatus as $status) {
            $statusKecil = strtolower($status); // Tukar jadi huruf kecil untuk check

            if (str_contains($statusKecil, 'selesai') || str_contains($statusKecil, 'tutup') || str_contains($statusKecil, 'siap') || str_contains($statusKecil, 'keputusan')) {
                $kategoriRingkas['Selesai / Ditutup']++;
            } 
            elseif (str_contains($statusKecil, 'bicara') || str_contains($statusKecil, 'sebutan') || str_contains($statusKecil, 'hujahan')) {
                $kategoriRingkas['Dalam Perbicaraan / Sebutan']++;
            } 
            elseif (str_contains($statusKecil, 'tangguh') || str_contains($statusKecil, 'kiv')) {
                $kategoriRingkas['Tangguh / KIV']++;
            } 
            else {
                $kategoriRingkas['Lain-lain / Dalam Tindakan']++;
            }
        }

        // Format data untuk Chart.js
        $dataPecahan = collect();
        $labels = [];
        $totals = [];

        foreach ($kategoriRingkas as $label => $total) {
            // Hanya masukkan jika ada data (supaya chart tak kosong)
            if ($total > 0) {
                $dataPecahan->push((object)['kategori' => $label, 'total' => $total]);
                $labels[] = $label;
                $totals[] = $total;
            }
        }

        // Data Agensi (Biarkan kosong jika belum ada table agensi)
        $dataAgensi = collect([]);
        $labelsAgensi = [];
        $totalsAgensi = [];

        return view('laporankesmahkamah.pecahan', compact(
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan',
            'labelsAgensi', 'totalsAgensi', 'dataAgensi'
        ));
    }
}