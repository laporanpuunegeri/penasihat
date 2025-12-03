<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPandanganUndang;
use App\Models\Agensi; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Penting untuk fungsi fail

class LaporanPandanganUndangController extends Controller
{
    /**
     * 1. INDEX: Senarai Laporan
     */
    public function index(Request $request)
    {
        $bulanPilihan = $request->input('bulan', date('n'));
        $tahunSemasa = date('Y');

        $query = LaporanPandanganUndang::query();

        if ($bulanPilihan !== 'all') {
            $query->whereMonth('tarikh_terima', $bulanPilihan)
                  ->whereYear('tarikh_terima', $tahunSemasa);
        }

        $data = $query->latest()->get();

        return view('laporanpandanganundang.index', compact('data'));
    }

    /**
     * 2. CREATE: Borang Daftar
     */
    public function create()
    {
        $user = Auth::user();

        $agensiList = Agensi::where('negeri', $user->negeri)
                            ->orWhere('negeri', 'Persekutuan')
                            ->orderBy('nama_agensi', 'ASC')
                            ->pluck('nama_agensi');

<<<<<<< HEAD
        return view('laporanpandanganundang.create', compact('agensiList'));
    }
=======
    // Senarai agensi tetap
    $agensiTetap = [
        "AKCC (Ayer Keroh Country Club)",
        "Bahagian Teknologi Maklumat Dan Komunikasi (BTMK)", 
        "BADSA (Bhg. Audit Dalam & Siasatan Awam)",
        "BKP, JKMM (Bhg. Khidmat Pengurusan / Pengurusan Aset)",
        "BKSA (Badan Kawal Selia Air)",
        "BPSM, JKMM (Bhg. Pengurusan Sumber Manusia)",
        "CMI (Pejabat Ketua Menteri)",
        "CUCKOO INTERNATIONAL (MAL) BERHAD",
        "DUN / Unit Dewan",
        "FR Fariza Enterprise",
        "Hospital Putra",
        "ITPS Marketing S/B",
        "Invest Melaka Berhad",
        "Jabatan Kebajikan Masyarakat Negeri Melaka (JKM)", 
        "Jabatan Kewangan & Perbendaharaan Negeri Melaka",
        "Jabatan Mufti Melaka",
        "Jabatan Pendakwaan Syariah",
        "Jabatan Pertanian",
        "Jabatan Perkhidmatan Veterinar Negeri Melaka",
        "Jabatan Pengairan dan Saliran Negeri Melaka",
        "Jabatan Perhutanan Negeri Melaka",
        "JAIM (Jabatan Agama Islam Melaka)",
        "JKR",
        "JPBD (Jab. Perancangan Bandar & Desa)",
        "Kertas Jemaah Pengampunan",
        "KMB (Kumpulan Melaka Berhad)",
        "Kompleks Falak Al-Khawarizmi",
        "LPM (Lembaga Perumahan Melaka)",
        "LTAM (Lembaga Tabung Amanah Melaka)",
        "Melaka International College of Science and Texhnology (MiCoST)",
        "Melaka Bekal Sdn Bhd",
        "Majlis Mesyuarat Kerajaan Negeri Melaka",
        "Mahkamah Syariah Melaka",
        "MAIM (Majlis Agama Islam Melaka)",
        "MCORP",
        "MITC",
        "MITCH (Melaka ICT Holding)",
        "M-WEZ (Melaka Waterfront Economic Zone)",
        "Panorama Melaka",
        "PBT (MBMB)",
        "PBT (MPAG)",
        "PBT (MPJ)",
        "PBT (MPTHJ)",
        "Pejabat T.Y.T",
        "Perbadanan Biokteknologi Melaka",
        "PERTAM (Perbadanan Kemajuan Tanah Adat Melaka)",
        "PERZIM (Perbadanan Muzium Melaka)",
        "PPSPM (Perbadanan Pembangunan Sungai & Pantai Melaka)",
        "PTD A/GAJAH",
        "PTD JASIN",
        "PTD M/TGH",
        "PTG Negeri Melaka",
        "PTHM (Perbadanan Teknologi Hijau Melaka)",
        "PUTARAN SEMASA SDN BHD",
        "SAMB (Syarikat Air Melaka)",
        "Setiausaha Kerajaan Negeri (suk)",
        "TAPEM (Tabung Amanah Pendidikan Melaka)",
        "UKT, JKMM (Unit Kerajaan Tempatan)",
        "UNIMEL / KUIM (Kolej Universiti Islam Melaka)",
        "Unit Integriti",
        "UTC Melaka",
        "UPEN (Unit Perancangan Ekonomi)",
        "Yayasan Melaka",
    ];
>>>>>>> a0c472fb933f28f5e7ce2499cc289ee4f1126ec1

    /**
     * 3. STORE: Simpan Data & Fail
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'isu' => 'required|string',
            'tarikh_terima' => 'required|date',
            'fakta_ringkasan' => 'required|string',
            'isu_detail' => 'required|string',
            'jenis_pandangan' => 'nullable|string',
            'status' => 'required|string',
            'tarikh_selesai' => 'nullable|date',
            'belum_selesai' => 'nullable|boolean',
            'dirujuk_jpn' => 'nullable|boolean',
            'agensi' => 'required|string',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Validasi Fail
        ]);

        $user = Auth::user();
        $bossId = ($user->role === 'pa' || $request->has('hantar_kepada_boss')) ? $user->boss_id : null;

        // Proses Fail
        $dokumenPath = null;
        if ($request->hasFile('dokumen')) {
            // Simpan dalam storage/app/public/dokumen_pandangan
            $dokumenPath = $request->file('dokumen')->store('dokumen_pandangan', 'public');
        }

        LaporanPandanganUndang::create([
            'kategori' => $request->kategori,
            'isu' => $request->isu,
            'tarikh_terima' => $request->tarikh_terima,
            'fakta_ringkasan' => $request->fakta_ringkasan,
            'isu_detail' => $request->isu_detail,
            'ringkasan_pandangan' => $request->input('ringkasan_pandangan', '-'),
            'jenis_pandangan' => $request->jenis_pandangan,
            'status' => $request->status,
            'tarikh_selesai' => $request->belum_selesai ? null : $request->tarikh_selesai,
            'belum_selesai' => $request->has('belum_selesai'),
            'dirujuk_jpn' => $request->has('dirujuk_jpn'),
            'agensi' => $request->agensi,
            'created_by' => $user->id,
            'boss_id' => $bossId,
            'user_id' => $user->id,
            'negeri' => $user->negeri,
            'dokumen_path' => $dokumenPath, // Simpan path fail
        ]);

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya disimpan.');
    }

    /**
     * 4. EDIT: Borang Kemaskini
     */
    public function edit($id)
    {
        $laporan = LaporanPandanganUndang::findOrFail($id);
        $user = Auth::user();

        $agensiList = Agensi::where('negeri', $user->negeri)
                            ->orWhere('negeri', 'Persekutuan')
                            ->orderBy('nama_agensi', 'ASC')
                            ->pluck('nama_agensi');

        return view('laporanpandanganundang.edit', compact('laporan', 'agensiList'));
    }

    /**
     * 5. UPDATE: Simpan Perubahan & Fail Baru
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'isu' => 'required|string',
            'tarikh_terima' => 'required|date',
            'fakta_ringkasan' => 'required|string',
            'isu_detail' => 'required|string',
            'ringkasan_pandangan' => 'nullable|string', // Boleh nullable
            'jenis_pandangan' => 'nullable|string',
            'status' => 'required|string',
            'tarikh_selesai' => 'nullable|date',
            'belum_selesai' => 'nullable|boolean',
            'dirujuk_jpn' => 'nullable|boolean',
            'agensi' => 'required|string',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $laporan = LaporanPandanganUndang::findOrFail($id);
        $user = Auth::user();
        
        $bossId = ($user->role === 'pa' || $request->has('hantar_kepada_boss')) ? $user->boss_id : $laporan->boss_id;

        // Proses Fail Baru (Jika ada)
        if ($request->hasFile('dokumen')) {
            // Padam fail lama jika wujud
            if ($laporan->dokumen_path) {
                Storage::disk('public')->delete($laporan->dokumen_path);
            }
            // Simpan fail baru
            $laporan->dokumen_path = $request->file('dokumen')->store('dokumen_pandangan', 'public');
        }

        $laporan->update([
            'isu' => $request->isu,
            'tarikh_terima' => $request->tarikh_terima,
            'fakta_ringkasan' => $request->fakta_ringkasan,
            'isu_detail' => $request->isu_detail,
            'ringkasan_pandangan' => $request->ringkasan_pandangan ?? '-',
            'jenis_pandangan' => $request->jenis_pandangan,
            'status' => $request->status,
            'tarikh_selesai' => $request->belum_selesai ? null : $request->tarikh_selesai,
            'belum_selesai' => $request->has('belum_selesai'),
            'dirujuk_jpn' => $request->has('dirujuk_jpn'),
            'agensi' => $request->agensi,
            'boss_id' => $bossId,
            // dokumen_path sudah diupdate secara manual di atas jika ada fail baru
        ]);

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    /**
     * 6. DESTROY: Padam Rekod & Fail
     */
    public function destroy($id)
    {
        $laporan = LaporanPandanganUndang::findOrFail($id);

        // Padam fail dari storage jika wujud
        if ($laporan->dokumen_path) {
            Storage::disk('public')->delete($laporan->dokumen_path);
        }

        $laporan->delete();

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya dipadam.');
    }

    /**
     * 7. PECAHAN BULAN
     */
    public function pecahanBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

        $dataPecahan = LaporanPandanganUndang::select('kategori', DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('kategori')
            ->orderBy('total', 'desc')
            ->get();

        $labels = $dataPecahan->pluck('kategori');
        $totals = $dataPecahan->pluck('total');

        $dataAgensi = LaporanPandanganUndang::select('agensi', DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->whereNotNull('agensi')
            ->where('agensi', '!=', '')
            ->groupBy('agensi')
            ->orderBy('total', 'desc') 
            ->limit(10) 
            ->get();

        $labelsAgensi = $dataAgensi->pluck('agensi');
        $totalsAgensi = $dataAgensi->pluck('total');

        return view('laporanpandanganundang.pecahan', compact(
            'bulan', 'tahun', 'namaBulan', 
            'labels', 'totals', 'dataPecahan',
            'labelsAgensi', 'totalsAgensi', 'dataAgensi'
        ));
    }
}