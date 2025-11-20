<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPandanganUndang;
use Illuminate\Support\Facades\Auth;

class LaporanPandanganUndangController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = LaporanPandanganUndang::query();

        if ($user->role === 'yb' || $user->role === 'pa') {
            $query->where('negeri', $user->negeri);
        } elseif ($user->role === 'user') {
            $query->where('created_by', $user->id);
        }

if ($request->filled('bulan')) {
    $query->whereMonth('tarikh_terima', $request->bulan)
          ->whereYear('tarikh_terima', now()->year);
}

        $data = $query->latest()->get();

        return view('laporanpandanganundang.index', compact('data', 'user'));
    }

   public function create()
{
    $user = auth()->user();

    // Ambil senarai agensi dari laporan terdahulu oleh user
    $agensiFromDb = LaporanPandanganUndang::where('created_by', $user->id)
        ->whereNotNull('agensi')
        ->pluck('agensi')
        ->toArray();

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

    // Gabung agensi dari DB + tetap, buang yang duplikat
    $agensiList = array_unique(array_merge($agensiFromDb, $agensiTetap));
    sort($agensiList); // Susun ikut abjad

    return view('laporanpandanganundang.create', compact('agensiList'));
}

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
    ]);

    $user = Auth::user();
    $bossId = ($user->role === 'pa' || $request->has('hantar_kepada_boss')) ? $user->boss_id : null;

    LaporanPandanganUndang::create([
        'kategori' => $request->kategori,
        'isu' => $request->isu,
        'tarikh_terima' => $request->tarikh_terima,
        'fakta_ringkasan' => $request->fakta_ringkasan,
        'isu_detail' => $request->isu_detail,
        'ringkasan_pandangan' => $request->input('ringkasan_pandangan', '-'), // <== FIX DI SINI
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
    ]);

    return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya disimpan.');
}

    public function edit($id)
    {
        $laporan = LaporanPandanganUndang::findOrFail($id);
        $user = auth()->user();

        $agensiFromDb = LaporanPandanganUndang::where('created_by', $user->id)
                            ->whereNotNull('agensi')
                            ->pluck('agensi')
                            ->toArray();

        $agensiTetap = [
            "JAIM", "PBT", "JPBD", "PTG", "TAPEM", "SAMB",
            "Hospital Putra", "UPEN", "JAWHAR"
        ];

        $agensiList = array_unique(array_merge($agensiFromDb, $agensiTetap));

        return view('laporanpandanganundang.edit', compact('laporan', 'agensiList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'isu' => 'required|string',
            'tarikh_terima' => 'required|date',
            'fakta_ringkasan' => 'required|string',
            'isu_detail' => 'required|string',
            'ringkasan_pandangan' => 'required|string|max:1000',
            'jenis_pandangan' => 'nullable|string',
            'status' => 'required|string',
            'tarikh_selesai' => 'nullable|date',
            'belum_selesai' => 'nullable|boolean',
            'dirujuk_jpn' => 'nullable|boolean',
            'agensi' => 'required|string'
        ]);

        $laporan = LaporanPandanganUndang::findOrFail($id);
        $user = Auth::user();
        $bossId = ($user->role === 'pa' || $request->has('hantar_kepada_boss')) ? $user->boss_id : $laporan->boss_id;

        $laporan->update([
            'isu' => $request->isu,
            'tarikh_terima' => $request->tarikh_terima,
            'fakta_ringkasan' => $request->fakta_ringkasan,
            'isu_detail' => $request->isu_detail,
            'ringkasan_pandangan' => $request->ringkasan_pandangan,
            'jenis_pandangan' => $request->jenis_pandangan,
            'status' => $request->status,
            'tarikh_selesai' => $request->belum_selesai ? null : $request->tarikh_selesai,
            'belum_selesai' => $request->has('belum_selesai'),
            'dirujuk_jpn' => $request->has('dirujuk_jpn'),
            'agensi' => $request->agensi,
            'boss_id' => $bossId,
        ]);

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanPandanganUndang::findOrFail($id);
        $laporan->delete();

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya dipadam.');
    }
}
