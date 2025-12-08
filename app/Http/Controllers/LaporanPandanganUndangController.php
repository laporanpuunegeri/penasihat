<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPandanganUndang;
use App\Models\Agensi; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; 

class LaporanPandanganUndangController extends Controller
{
    /**
     * 1. INDEX: Senarai Laporan
     */
public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanPandanganUndang::query();

        // 1. Filter Role (Hanya Admin/YB nampak semua)
        $allowedRoles = ['super_admin', 'YB', 'PUUN', 'admin']; 
        if (!in_array($user->role, $allowedRoles)) {
            $query->where('user_id', $user->id);
        }

        // 2. FILTER TAHUN (Wajib ada supaya data tak bercampur aduk)
        // Ambil tahun dari request, atau guna tahun semasa sebagai default
        $tahun = $request->input('tahun', date('Y'));
        
        if ($tahun != 'all') {
            $query->whereYear('tarikh_terima', $tahun);
        }

        // 3. FILTER BULAN
        if ($request->has('bulan') && $request->bulan != 'all') {
            $query->whereMonth('tarikh_terima', $request->bulan);
        }

        // 4. DAPATKAN DATA
        // PENTING: Guna get() bukan paginate(10). 
        // Ini memastikan semua data dalam bulan/tahun tersebut dipaparkan untuk disusun ikut kategori.
        $senaraiLaporan = $query->orderBy('tarikh_terima', 'desc')->get();

        return view('laporanpandanganundang.index', compact('senaraiLaporan', 'tahun'));
    }

    /**
     * 2. CREATE: Borang Daftar
     */
    public function create()
    {
        $user = Auth::user();

        // Kita guna version Database (HEAD) sebab ia lebih dinamik
        $agensiList = Agensi::where('negeri', $user->negeri)
                            ->orWhere('negeri', 'Persekutuan')
                            ->orderBy('nama_agensi', 'ASC')
                            ->pluck('nama_agensi');

        return view('laporanpandanganundang.create', compact('agensiList'));
    }

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
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', 
        ]);

        $user = Auth::user();
        $bossId = ($user->role === 'pa' || $request->has('hantar_kepada_boss')) ? $user->boss_id : null;

        // Proses Fail
        $dokumenPath = null;
        if ($request->hasFile('dokumen')) {
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
            'dokumen_path' => $dokumenPath, 
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
            'ringkasan_pandangan' => 'nullable|string', 
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