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
     * 1. INDEX: Filter Paling Inclusive (Terima OR Update OR Selesai)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanPandanganUndang::query();

        // 1. Ambil rekod terkini sahaja (elak rekod sejarah berulang)
        $query->where('is_current', true);

        // 🔥 2. Filter Role (VISIBILITY LOGIC) 🔥
        $role = strtolower($user->role);
        
        if ($role === 'super_admin') {
            // Super Admin melihat semua data (Global)
        } elseif (in_array($role, ['yb', 'pa', 'puun', 'admin'])) {
            // YB/PA/PUUN/Admin melihat semua data di Negeri mereka
            $query->where('negeri', $user->negeri);
        } else {
            // User biasa hanya melihat data yang mereka masukkan
            $query->where('user_id', $user->id);
        }

        // 3. LOGIC FILTER TARIKH (TRIPLE THREAT)
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan', 'all');

        $query->where(function($q) use ($tahun, $bulan) {
            
            if ($tahun != 'all') {
                // A: Filter ikut Tarikh Terima (Surat masuk bulan ni)
                $q->where(function($sub) use ($tahun, $bulan) {
                    $sub->whereYear('tarikh_terima', $tahun);
                    if ($bulan != 'all') $sub->whereMonth('tarikh_terima', $bulan);
                })
                // B: ATAU Filter ikut Tarikh Kemaskini (Ada tindakan/status baru bulan ni)
                ->orWhere(function($sub) use ($tahun, $bulan) {
                    $sub->whereYear('updated_at', $tahun);
                    if ($bulan != 'all') $sub->whereMonth('updated_at', $bulan);
                })
                // C: ATAU Filter ikut Tarikh Selesai (Kes selesai bulan ni)
                ->orWhere(function($sub) use ($tahun, $bulan) {
                    $sub->whereYear('tarikh_selesai', $tahun);
                    if ($bulan != 'all') $sub->whereMonth('tarikh_selesai', $bulan);
                });
            }
        });

        // Susun ikut yang paling baru dikemaskini/tindakan
        $senaraiLaporan = $query->orderBy('updated_at', 'desc')->get();

        return view('laporanpandanganundang.index', compact('senaraiLaporan', 'tahun'));
    }

    /**
     * 2. CREATE: Borang Daftar
     */
    public function create() 
    { 
        $user = Auth::user();
        // Dapatkan senarai agensi ikut negeri user
        $agensiList = Agensi::where('negeri', $user->negeri)
                            ->orWhere('negeri', 'Persekutuan')
                            ->orderBy('nama_agensi', 'ASC')
                            ->pluck('nama_agensi');
                            
        return view('laporanpandanganundang.create', compact('agensiList')); 
    }

    /**
     * 3. STORE: Simpan Rekod Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string', 
            'status' => 'required|string', 
            'tarikh_terima' => 'required|date', 
            'agensi' => 'required|string', 
            'isu' => 'required|string', 
            'fakta_ringkasan' => 'required|string', 
            'isu_detail' => 'required|string',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);
        
        $dokumenPath = null;
        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')->store('dokumen_pandangan', 'public');
        }

        $user = Auth::user();
        $bossId = (strtolower($user->role) === 'pa' || $request->has('hantar_kepada_boss')) ? $user->boss_id : null;

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
            'user_id' => $user->id,
            'boss_id' => $bossId,
            'negeri' => $user->negeri,
            'dokumen_path' => $dokumenPath,
            'is_current' => true, 
            'previous_id' => null,
            'tarikh_daftar' => now() 
        ]);

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya disimpan.');
    }

    /**
     * 4. EDIT: Borang Kemaskini (DENGAN KAWALAN AKSES)
     */
    public function edit($id) 
    {
        $laporan = LaporanPandanganUndang::findOrFail($id);
        
        // 🔥 KAWALAN AKSES 🔥
        if (!$this->authorizeAction($laporan)) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk mengemaskini laporan ini.');
        }

        $user = Auth::user();
        
        $agensiList = Agensi::where('negeri', $user->negeri)
                            ->orWhere('negeri', 'Persekutuan')
                            ->orderBy('nama_agensi', 'ASC')
                            ->pluck('nama_agensi');
                            
        return view('laporanpandanganundang.edit', compact('laporan', 'agensiList'));
    }

    /**
     * 5. UPDATE: Logic Log Status Baru (DENGAN KAWALAN AKSES)
     */
    public function update(Request $request, $id)
    {
        $laporan = LaporanPandanganUndang::findOrFail($id);
        
        // 🔥 KAWALAN AKSES 🔥
        if (!$this->authorizeAction($laporan)) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk mengemaskini laporan ini.');
        }

        $status_text = $request->input('status');
        $status_index = (int)substr($status_text, 0, 1);
        
        $is_status_2_to_7 = ($status_index >= 2 && $status_index <= 7);
        $is_status_8 = ($status_index == 8);
        $needs_new_log = ($is_status_2_to_7 || $is_status_8);

        // Validation Rules
        $rules = [
            'status' => 'required|string',
            'ringkasan_pandangan' => 'nullable|string',
            'jenis_pandangan' => 'nullable|string', 
        ];

        // Validation ikut status
        if ($is_status_2_to_7) {
            $rules['tarikh_status_baru'] = 'required|date';
        }
        if ($is_status_8) {
            $rules['tarikh_selesai'] = 'required|date';
        }

        $validated = $request->validate($rules);

        // SCENARIO A: Update Biasa (Tanpa Log Baru - cth: Status 1)
        if (!$needs_new_log) {
            $laporan->update([
                'status' => $validated['status'],
                'ringkasan_pandangan' => $validated['ringkasan_pandangan'],
                'jenis_pandangan' => $request->jenis_pandangan, // KEKALKAN update jenis pandangan jika ada
            ]);
            return redirect()->route('laporanpandanganundang.index')->with('success', 'Kemaskini berjaya.');
        }

        // SCENARIO B: Workflow Baru (Status 2-8)
        DB::transaction(function () use ($laporan, $request, $validated, $is_status_2_to_7, $is_status_8) {
            
            // 1. Archive rekod lama
            $laporan->is_current = false;
            $laporan->save();

            // 2. Duplicate data
            $rekod_baru = $laporan->replicate();

            // 3. Update info baru
            $rekod_baru->status = $validated['status'];
            $rekod_baru->ringkasan_pandangan = $validated['ringkasan_pandangan'];
            $rekod_baru->jenis_pandangan = $request->jenis_pandangan; // KEKALKAN nilai
            
            // Handle Tarikh Selesai & Tarikh Tindakan
            if ($is_status_8) {
                // Status 8: Set tarikh selesai
                $rekod_baru->tarikh_selesai = $validated['tarikh_selesai'];
                $rekod_baru->belum_selesai = false;
                $tarikhTindakan = $validated['tarikh_selesai']; 
            } else {
                // Status 2-7: Belum selesai
                $rekod_baru->tarikh_selesai = null; 
                $rekod_baru->belum_selesai = true;
                $tarikhTindakan = $validated['tarikh_status_baru'];
            }
            
            // Set 'dirujuk_jpn' jika ada input (optional)
            $rekod_baru->dirujuk_jpn = $request->has('dirujuk_jpn');

            // LOGIC TARIKH SUPAYA MUNCUL DALAM FILTER BULAN TINDAKAN
            $tarikhBaru = Carbon::parse($tarikhTindakan)->setTimeFrom(now());
            $rekod_baru->created_at = $tarikhBaru; 
            $rekod_baru->updated_at = $tarikhBaru; 
            
            // Link Audit Trail
            $rekod_baru->previous_id = $laporan->id;
            $rekod_baru->is_current = true;

            // Handle Fail (Jika ada fail baru diupload)
            if ($request->hasFile('dokumen')) {
                $rekod_baru->dokumen_path = $request->file('dokumen')->store('dokumen_pandangan', 'public');
            }

            $rekod_baru->save();
        });

        return redirect()->route('laporanpandanganundang.index')->with('success', 'Status baru berjaya direkodkan.');
    }

    /**
     * 6. DESTROY: Padam Rekod (DENGAN KAWALAN AKSES)
     */
    public function destroy($id) 
    { 
        $laporan = LaporanPandanganUndang::findOrFail($id); 
        
        // 🔥 KAWALAN AKSES 🔥
        if (!$this->authorizeAction($laporan)) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk memadam laporan ini.');
        }

        // Padam dokumen (jika ada)
        if ($laporan->dokumen_path) { 
            Storage::disk('public')->delete($laporan->dokumen_path); 
        } 
        
        $laporan->delete(); 
        
        return redirect()->route('laporanpandanganundang.index')->with('success', 'Laporan berjaya dipadam.'); 
    }
    
    /**
     * 7. PECAHAN BULAN (Drill-down Statistik)
     */
    public function pecahanBulan(Request $request) 
    {
        $bulan = $request->bulan; 
        $tahun = $request->tahun; 
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');
        
        $dataPecahan = LaporanPandanganUndang::select('kategori', DB::raw('count(*) as total'))
            ->where('is_current', true)
            // Guna logic OR yang sama (Terima OR Update OR Selesai)
             ->where(function($q) use ($tahun, $bulan) {
                $q->where(function($sub) use ($tahun, $bulan) { 
                    $sub->whereYear('tarikh_terima', $tahun)->whereMonth('tarikh_terima', $bulan); 
                })
                ->orWhere(function($sub) use ($tahun, $bulan) { 
                    $sub->whereYear('updated_at', $tahun)->whereMonth('updated_at', $bulan); 
                })
                ->orWhere(function($sub) use ($tahun, $bulan) { 
                    $sub->whereYear('tarikh_selesai', $tahun)->whereMonth('tarikh_selesai', $bulan); 
                });
            })
            ->groupBy('kategori')
            ->orderBy('total', 'desc')
            ->get();

        $labels = $dataPecahan->pluck('kategori'); 
        $totals = $dataPecahan->pluck('total');
        
        // Placeholder data untuk elak error view
        $dataAgensi = collect(); $labelsAgensi = []; $totalsAgensi = [];
        
        return view('laporanpandanganundang.pecahan', compact('bulan', 'tahun', 'namaBulan', 'labels', 'totals', 'dataPecahan', 'dataAgensi', 'labelsAgensi', 'totalsAgensi'));
    }

    /**
     * 🔥 HELPER FUNCTION BARU: authorizeAction (Check Edit/Delete Access) 🔥
     * Rule: PA, YB, super_admin boleh delete/edit (skop negeri/global)
     */
    protected function authorizeAction(LaporanPandanganUndang $laporan)
    {
        $user = Auth::user();
        $role = strtolower($user->role);
        
        // 1. Super Admin boleh buat semua (Global)
        if ($role === 'super_admin') {
            return true;
        }
        
        // 2. PA, YB, PUUN, Admin boleh buat semua dalam Negeri yang sama
        $stateRoles = ['pa', 'yb', 'puun', 'admin'];
        if (in_array($role, $stateRoles) && $user->negeri === $laporan->negeri) {
            return true;
        }
        
        // 3. User asal boleh edit/padam rekod sendiri
        if ($user->id === $laporan->user_id) {
            return true;
        }
        
        return false;
    }
}