<?php

namespace App\Http\Controllers;

use App\Models\Pergerakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PDF; 
use Carbon\Carbon;
use Carbon\CarbonPeriod; 

class PergerakanController extends Controller
{
    // =========================================================================
    // 1. INDEX & PAPARAN (PA DITAMBAH DI SINI)
    // =========================================================================
    public function index(Request $request)
    {
        $query = Pergerakan::query();
        $userRole = strtolower(Auth::user()->role);

        // 🔥 1. Tambah 'pa' dalam senarai pegawai untuk filter dropdown
        $senarai_pegawai = User::whereIn('role', ['user', 'cc', 'super_admin', 'yb', 'pa'])->orderBy('name')->get();

        // 🔥 2. Benarkan PA akses data (sama level macam CC/YB/Super Admin)
        if (in_array($userRole, ['cc', 'super_admin', 'yb', 'pa'])) {
            
            // Filter ikut nama pegawai
            if ($request->filled('pegawai_id')) { 
                $query->where('user_id', $request->pegawai_id); 
            }

            // Filter Khas CC (Hanya nampak kenderaan tertentu jika perlu)
            if ($userRole === 'cc') {
                $query->whereIn('kenderaan', ['Kenderaan Pejabat', 'Kenderaan Sendiri']);
            }

            // Filter Status Button
            if ($request->input('status_filter') === 'cc_pending' && ($userRole === 'cc' || $userRole === 'super_admin')) {
                $query->where('status_cc', 'Pending');
            } 
            // 🔥 3. Benarkan PA nampak list 'Belum Disahkan YB'
            elseif ($request->input('status_filter') === 'yb_pending' && ($userRole === 'yb' || $userRole === 'super_admin' || $userRole === 'pa')) {
                $query->where('status_yb', 'Pending');
            }

        } else {
            // Kalau user biasa, nampak diri sendiri je
            $query->where('user_id', Auth::id());
        }

        $pergerakan = $query->get()->map(function ($event) {
            $userName = $event->user->name ?? 'Pengguna Dipadam';
            $title = $userName . ' - ' . $event->tujuan_penggunaan;
            
            $color = '#6c757d'; 
            if ($event->status_yb === 'Lulus') { $color = '#28a745'; } 
            elseif ($event->status_yb === 'Tolak' || $event->status_cc === 'Tolak') { $color = '#dc3545'; } 
            elseif ($event->status_cc === 'Pending') { $color = '#ffc107'; }

            return [
                'id' => $event->id,
                'title' => $title,
                'start' => $event->tarikh_mula,
                'end' => Carbon::parse($event->tarikh_akhir)->addDay()->toDateString(), 
                'color' => $color,
                'extendedProps' => [
                    'kenderaan' => $event->kenderaan, 'tujuan_penggunaan' => $event->tujuan_penggunaan,
                    'destinasi' => $event->destinasi, 'masa_mula' => $event->masa_mula,
                    'masa_akhir' => $event->masa_akhir, 'nama_pemandu' => $event->nama_pemandu,
                    'no_kenderaan' => $event->no_kenderaan, 'catatan' => $event->catatan,
                    'status_cc' => $event->status_cc, 'catatan_cc' => $event->catatan_cc,
                    'status_yb' => $event->status_yb,
                    'owner_id' => $event->user_id,
                    'lampiran' => $event->lampiran, 
                ]
            ];
        });
        
        $currentUserId = Auth::id();

        return view('pergerakan.index', compact('pergerakan', 'senarai_pegawai', 'currentUserId'));
    }

    public function create()
    {
        return view('pergerakan.create');
    }

    // =========================================================================
    // 2. SIMPAN DATA (STORE) - KEKAL BASE64
    // =========================================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required',
            'tarikh_mula' => 'required|date',
            'tarikh_akhir' => 'required|date|after_or_equal:tarikh_mula',
            'kenderaan' => 'required',
            'tujuan_penggunaan' => 'required',
            'destinasi' => 'required',
            // Validation longgar sikit supaya tak isu JPG/jpg/JPEG
            'lampiran' => 'nullable|file|max:10240', 
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $data['status_cc'] = 'Pending';
            $data['status_yb'] = 'Pending';

            // Convert Lampiran ke Base64 (Untuk atasi masalah Server Reset)
            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                
                // Baca fail dan tukar jadi base64 string
                $fileContent = file_get_contents($file->getRealPath());
                $base64 = base64_encode($fileContent);
                $mimeType = $file->getMimeType();
                
                // Simpan format: "data:image/png;base64,....."
                $data['lampiran'] = 'data:' . $mimeType . ';base64,' . $base64;
            }

            if ($request->has('is_multiday')) {
                $start = Carbon::parse($request->tarikh_mula);
                $end = Carbon::parse($request->tarikh_akhir);
                $period = CarbonPeriod::create($start, $end);
                $dayCount = 1;
                foreach ($period as $date) {
                    $dailyData = $data;
                    $dailyData['tarikh_mula'] = $date->format('Y-m-d');
                    $dailyData['tarikh_akhir'] = $date->format('Y-m-d');
                    $dailyData['tujuan_penggunaan'] = $data['tujuan_penggunaan'] . ' (Hari ke-' . $dayCount++ . ')';
                    Pergerakan::create($dailyData);
                }
                $message = 'Permohonan bersiri berjaya dihantar.';
            } else {
                Pergerakan::create($data);
                $message = 'Permohonan berjaya dihantar.';
            }

            return redirect()->route('pergerakan.index')->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    // =========================================================================
    // 3. SEMAKAN & KELULUSAN
    // =========================================================================
    public function cc_review(Request $request, Pergerakan $pergerakan)
    {
        if (!Gate::allows('review-cc', $pergerakan)) { abort(403); }

        $actionType = $request->input('action_type');
        $catatan_cc = $request->input('catatan_cc');

        if ($actionType === 'support') {
            $validated = $request->validate([
                'no_kenderaan' => 'required|string|max:50', 
                'nama_pemandu' => 'required|string|max:255',
            ]);
            $pergerakan->status_cc = 'Sokong'; 
            $pergerakan->catatan_cc = $catatan_cc; 
            $pergerakan->cc_id = Auth::id(); 
            $pergerakan->no_kenderaan = $validated['no_kenderaan']; 
            $pergerakan->nama_pemandu = $validated['nama_pemandu'];
            $pergerakan->save();
            $message = 'Permohonan disokong (Kenderaan Pejabat).';

        } elseif ($actionType === 'support_sendiri') {
            $validated = $request->validate(['catatan_cc' => 'required|string|max:500']);
            $pergerakan->status_cc = 'Sokong';
            $pergerakan->cc_id = Auth::id();
            $pergerakan->catatan_cc = $validated['catatan_cc'];
            $pergerakan->no_kenderaan = null;
            $pergerakan->nama_pemandu = null;
            $pergerakan->save();
            $message = 'Permohonan disokong (Kenderaan Sendiri).';

        } elseif ($actionType === 'reject') {
            $validated = $request->validate(['catatan_cc' => 'required|string|max:500']);
            $pergerakan->status_cc = 'Tolak'; 
            $pergerakan->status_yb = 'Tolak';
            $pergerakan->cc_id = Auth::id(); 
            $pergerakan->catatan_cc = $validated['catatan_cc'];
            $pergerakan->save();
            $message = 'Permohonan ditolak.';
        }

        return redirect()->route('pergerakan.index')->with('success', $message);
    }
    
    // 🔥 4. LULUS YB (PA JUGA BOLEH LULUSKAN)
    public function lulusYb($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        
        // Kita cek manual: Kalau YB atau PA atau Super Admin -> BOLEH
        if (!in_array(Auth::user()->role, ['yb', 'pa', 'super_admin'])) { 
            abort(403, 'Anda tiada kuasa YB/PA.'); 
        }

        $pergerakan->status_yb = 'Lulus';
        $pergerakan->yb_id = Auth::id();
        $pergerakan->save();
        
        return redirect()->route('pergerakan.index')->with('success', 'Permohonan DILULUSKAN (Tindakan YB/PA).');
    }

    // 🔥 5. TOLAK YB (PA JUGA BOLEH TOLAK)
    public function tolakYb($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        
        // Kita cek manual: Kalau YB atau PA atau Super Admin -> BOLEH
        if (!in_array(Auth::user()->role, ['yb', 'pa', 'super_admin'])) { 
             abort(403, 'Anda tiada kuasa YB/PA.'); 
        }

        $pergerakan->status_yb = 'Tolak';
        $pergerakan->yb_id = Auth::id();
        $pergerakan->save();
        
        return redirect()->route('pergerakan.index')->with('success', 'Permohonan DITOLAK (Tindakan YB/PA).');
    }

    public function destroy($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        $user = Auth::user();
        $userRole = strtolower($user->role);
        $canForceDelete = in_array($userRole, ['super_admin']); 
        $isApplicant = ($user->id === $pergerakan->user_id);
        
        try {
            if (!$isApplicant && !$canForceDelete) { throw new \Exception('Tiada kebenaran padam.', 403); }
            if ($pergerakan->status_yb === 'Lulus' && !$canForceDelete) { throw new \Exception('Rekod LULUS tidak boleh dipadam.', 403); }
            if ($canForceDelete) { $pergerakan->forceDelete(); } else { $pergerakan->delete(); }
            return response()->json(['status' => 'success', 'message' => 'Rekod berjaya dipadam.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // 4. CETAK BORANG (READ BASE64 PDF)
    // =========================================================================
    public function cetakBorang($id)
    {
        $pergerakan = Pergerakan::with(['user', 'cc'])->findOrFail($id);
        
        if ($pergerakan->status_yb !== 'Lulus') {
            abort(403, 'Hanya borang yang DILULUSKAN sahaja boleh dicetak.');
        }
        
        $viewName = str_contains($pergerakan->kenderaan, 'Pejabat') 
                    ? 'pergerakan.borang_kenderaan_pejabat' 
                    : 'pergerakan.borang_kenderaan_sendiri';
        
        // Cari CC
        $cc_user = $pergerakan->cc; 
        if (!$cc_user) {
            $negeri = $pergerakan->user->negeri;
            $cc_user = User::where('role', 'cc')->where('negeri', $negeri)->first();
            if (!$cc_user) {
                $cc_user = User::where('role', 'super_admin')->first() ?? Auth::user();
            }
        }
        
        // Cari YB (Untuk dapatkan sain YB sebenar walaupun PA yang approve)
        $penasihat = User::where('role', 'yb')->first();

        // Ambil Tandatangan
        $sig_yb = $penasihat ? $penasihat->signature_file : null;
        $sig_applicant = $pergerakan->user->signature_file ?? null;
        $sig_cc = $cc_user ? $cc_user->signature_file : null;

        $data = [
            'pergerakan' => $pergerakan,
            'namaYB' => $penasihat->name ?? 'TIADA',
            'bahagianYB' => $penasihat->bahagian ?? '',
            'jawatanYB' => $penasihat->nama_jawatan ?? '',
            'sig_yb' => $sig_yb,
            'sig_applicant' => $sig_applicant,
            'sig_cc' => $sig_cc,
            'cc_name' => $cc_user->name ?? '',
            'cc_jawatan' => $cc_user->nama_jawatan ?? '',
            'pemandu_ditugaskan' => $pergerakan->nama_pemandu,
            'no_kenderaan_rasmi' => $pergerakan->no_kenderaan,
        ];
        
        $pdf = PDF::loadView($viewName, $data);
        $pdf->setOptions(['isRemoteEnabled' => true, 'chroot' => [base_path()]]); 
        
        return $pdf->stream('Borang_Pergerakan_' . $pergerakan->id . '.pdf');
    }

    public function cetakKalendarKeseluruhan(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $penasihat = User::where('role', 'yb')->first();
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $pergerakan_data = Pergerakan::with('user')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tarikh_mula', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('tarikh_akhir', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($query) use ($startOfMonth, $endOfMonth) {
                          $query->where('tarikh_mula', '<', $startOfMonth)->where('tarikh_akhir', '>', $endOfMonth);
                      });
            })
            ->where('status_yb', 'Lulus') 
            ->where('kenderaan', 'Kenderaan Sendiri') 
            ->orderBy('tarikh_mula')
            ->get()
            ->groupBy(function($date) { return Carbon::parse($date->tarikh_mula)->format('Y-m-d'); });

        $data = [
            'bulan_text' => $startOfMonth->translatedFormat('F Y'),
            'pergerakan_by_day' => $pergerakan_data,
            'start_of_month' => $startOfMonth,
            'end_of_month' => $endOfMonth,
            'tarikh_cetak' => Carbon::now()->format('d/m/Y H:i:s'),
            'namaYB' => $penasihat->name ?? 'YANG BERHORMAT',
            'jawatanYB' => $penasihat->nama_jawatan ?? 'Jawatan',
            'sig_yb' => $penasihat ? $penasihat->signature_file : null,
        ];
        
        $pdf = PDF::loadView('pergerakan.kalendar_keseluruhan_pdf', $data)->setPaper('a4', 'landscape');
        $pdf->setOptions(['isRemoteEnabled' => true, 'chroot' => [base_path()]]); 
        
        return $pdf->stream('Kalendar_Pergerakan_' . $startOfMonth->format('Ym') . '.pdf');
    }
}