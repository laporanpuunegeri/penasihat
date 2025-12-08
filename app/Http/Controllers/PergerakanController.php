<?php

namespace App\Http\Controllers;

use App\Models\Pergerakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage; 
use PDF;
use Carbon\Carbon;

class PergerakanController extends Controller
{
    /**
     * Paparkan Kalendar Pergerakan (index).
     */
    public function index(Request $request)
    {
        $query = Pergerakan::query();
        $userRole = strtolower(Auth::user()->role);
        $senarai_pegawai = User::whereIn('role', ['user', 'cc', 'boss', 'yb'])->orderBy('name')->get();

        if (in_array($userRole, ['cc', 'boss', 'yb'])) {
            if ($request->filled('pegawai_id')) { $query->where('user_id', $request->pegawai_id); }
            if ($request->input('status_filter') === 'cc_pending' && ($userRole === 'cc' || $userRole === 'boss')) {
                $query->where('status_cc', 'Pending');
            } elseif ($request->input('status_filter') === 'yb_pending' && ($userRole === 'yb' || $userRole === 'boss')) {
                $query->where('status_yb', 'Pending');
            }
        } else {
            $query->where('user_id', Auth::id());
        }

        $pergerakan = $query->get()->map(function ($event) {
            $userName = $event->user->name ?? 'Pengguna Dipadam (ID: ' . $event->user_id . ')';
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required',
            'tarikh_mula' => 'required|date',
            'tarikh_akhir' => 'required|date',
            'kenderaan' => 'required',
            'tujuan_penggunaan' => 'required',
            'destinasi' => 'required',
            'lampiran' => 'required|file|mimes:jpg,jpeg,png|max:5120', 
        ], [
            'lampiran.required' => 'Sila muat naik dokumen lampiran/surat arahan.',
            'lampiran.mimes' => 'Harap maaf, hanya format GAMBAR (JPG, JPEG, PNG) sahaja dibenarkan.',
            'lampiran.max' => 'Saiz fail terlalu besar (Maksimum 5MB).',
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $data['status_cc'] = 'Pending';
            $data['status_yb'] = 'Pending';

            if ($request->hasFile('lampiran')) {
                $path = $request->file('lampiran')->store('lampiran_pergerakan', 'public');
                $data['lampiran'] = $path;
            }

            Pergerakan::create($data);

            return redirect()->route('pergerakan.index')->with('success', 'Permohonan berjaya dihantar.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    // 🔥🔥🔥 FUNGSI UTAMA YANG DIKEMASKINI 🔥🔥🔥
    public function cc_review(Request $request, Pergerakan $pergerakan)
    {
        if (!Gate::allows('review-cc', $pergerakan)) { abort(403); }

        $actionType = $request->input('action_type');
        $catatan_cc = $request->input('catatan_cc');

        // KES 1: SOKONG KENDERAAN PEJABAT (Ada kenderaan & pemandu)
        if ($actionType === 'support') {
            $validated = $request->validate([
                'no_kenderaan' => 'required|string|max:50', 
                'nama_pemandu' => 'required|string|max:255',
            ]);
            
            $pergerakan->status_cc = 'Sokong'; 
            $pergerakan->catatan_cc = $catatan_cc; 
            $pergerakan->cc_id = Auth::id(); // Simpan ID CC
            $pergerakan->no_kenderaan = $validated['no_kenderaan']; 
            $pergerakan->nama_pemandu = $validated['nama_pemandu'];
            
            $pergerakan->save();
            $message = 'Permohonan disokong dan kenderaan berjaya ditugaskan.';

        // KES 2: SOKONG KENDERAAN SENDIRI (Hanya Catatan) - MODAL KHAS
        } elseif ($actionType === 'support_sendiri') {
            $validated = $request->validate([
                'catatan_cc' => 'required|string|max:500' // Wajibkan catatan
            ]);

            $pergerakan->status_cc = 'Sokong';
            $pergerakan->cc_id = Auth::id(); // Simpan ID CC
            $pergerakan->catatan_cc = $validated['catatan_cc']; // Simpan catatan
            
            // Kosongkan kenderaan rasmi
            $pergerakan->no_kenderaan = null;
            $pergerakan->nama_pemandu = null;

            $pergerakan->save();
            $message = 'Permohonan (Kenderaan Sendiri) berjaya disokong dengan catatan.';

        // KES 3: TOLAK
        } elseif ($actionType === 'reject') {
            $validated = $request->validate(['catatan_cc' => 'required|string|max:500']);
            
            $pergerakan->status_cc = 'Tolak'; 
            $pergerakan->status_yb = 'Tolak';
            $pergerakan->cc_id = Auth::id(); // Simpan ID CC
            $pergerakan->catatan_cc = $validated['catatan_cc'];
            
            $pergerakan->save();
            $message = 'Permohonan pergerakan telah ditolak.';
        }

        return redirect()->route('pergerakan.index')->with('success', $message);
    }
    
    // --- FUNGSI RINGKAS (MUNGKIN TAK DIGUNAKAN LAGI TAPI SIMPAN UNTUK BACKUP) ---
    public function lulusCc($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        if (!Gate::allows('review-cc', $pergerakan)) { abort(403); }
        $pergerakan->status_cc = 'Sokong';
        $pergerakan->cc_id = Auth::id();
        $pergerakan->catatan_cc = 'Disokong tanpa penetapan kenderaan (Kenderaan Sendiri)';
        $pergerakan->save();
        return redirect()->route('pergerakan.index')->with('success', 'Permohonan berjaya disokong.');
    }

    public function tolakCc($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        if (!Gate::allows('review-cc', $pergerakan)) { abort(403); }
        $pergerakan->status_cc = 'Tolak'; $pergerakan->status_yb = 'Tolak';
        $pergerakan->cc_id = Auth::id();
        $pergerakan->catatan_cc = 'Ditolak oleh CC/Boss melalui pautan segera.';
        $pergerakan->save();
        return redirect()->route('pergerakan.index')->with('success', 'Permohonan telah ditolak.');
    }

    // --- Tindakan YB ---
    public function lulusYb($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        if (!Gate::allows('review-yb', $pergerakan)) { abort(403); }
        $pergerakan->status_yb = 'Lulus';
        $pergerakan->yb_id = Auth::id();
        $pergerakan->save();
        return redirect()->route('pergerakan.index')->with('success', 'Permohonan pergerakan berjaya DILULUSKAN.');
    }

    public function tolakYb($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        if (!Gate::allows('review-yb', $pergerakan)) { abort(403); }
        $pergerakan->status_yb = 'Tolak';
        $pergerakan->yb_id = Auth::id();
        $pergerakan->save();
        return redirect()->route('pergerakan.index')->with('success', 'Permohonan pergerakan telah DITOLAK oleh YB.');
    }

    // --- CRUD dan Cetak Borang ---
    public function destroy($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        $user = Auth::user();
        $userRole = strtolower($user->role);
        $canForceDelete = in_array($userRole, ['super_admin', 'boss']); 
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

    public function cetakBorang($id)
    {
        // 1. Muatkan relation 'cc' supaya kita tahu siapa yang sokong
        $pergerakan = Pergerakan::with(['user', 'cc'])->findOrFail($id);
        
        if ($pergerakan->status_yb !== 'Lulus') {
            abort(403, 'Hanya borang yang DILULUSKAN sahaja boleh dicetak.');
        }
        
        // Tentukan View
        $viewName = str_contains($pergerakan->kenderaan, 'Pejabat') 
                    ? 'pergerakan.borang_kenderaan_pejabat' 
                    : 'pergerakan.borang_kenderaan_sendiri';
        
        // --- OPERASI CARI SIAPA CC SEBENAR ---
        $cc_user = $pergerakan->cc; 
        
        if (!$cc_user) {
            $bahagianPemohon = $pergerakan->user->bahagian;
            $cc_user = User::where('role', 'boss')->where('bahagian', $bahagianPemohon)->first();
            if (!$cc_user) {
                $cc_user = User::whereIn('role', ['super_admin', 'admin'])->where('bahagian', $bahagianPemohon)->first();
            }
            if (!$cc_user) { $cc_user = Auth::user(); }
        }

        $penasihat = User::where('role', 'yb')->first();
        $sig_yb = $penasihat ? $this->getSignatureBase64($penasihat) : null;
        $sig_applicant = $this->getSignatureBase64($pergerakan->user);
        $sig_cc = $cc_user ? $this->getSignatureBase64($cc_user) : null;

        $data = [
            'pergerakan' => $pergerakan,
            'namaYB' => $penasihat->name ?? 'TIADA MAKLUMAT YB', 
            'bahagianYB' => $penasihat->bahagian ?? '', 
            'jawatanYB' => $penasihat->nama_jawatan ?? 'Penasihat Undang-Undang Negeri', 
            'sig_yb' => $sig_yb,
            'sig_applicant' => $sig_applicant, 
            'sig_cc' => $sig_cc, 
            'cc_name' => $cc_user->name ?? 'TIADA NAMA PENYOKONG',
            'cc_jawatan' => $cc_user->nama_jawatan ?? 'Ketua Bahagian', 
            'pemandu_ditugaskan' => $pergerakan->nama_pemandu,
            'no_kenderaan_rasmi' => $pergerakan->no_kenderaan,
        ];
        
        $pdf = PDF::loadView($viewName, $data);
        return $pdf->stream('Borang_Pergerakan_' . $pergerakan->id . '.pdf');
    }

    protected function getSignatureBase64(User $user)
    {
        $signature_url = null;
        if ($user->signature_file) {
            try {
                $path = 'public/signatures/' . $user->signature_file; 
                if (Storage::exists($path)) {
                    $contents = Storage::get($path);
                    $type = Storage::mimeType($path);
                    $signature_url = 'data:' . $type . ';base64,' . base64_encode($contents);
                }
            } catch (\Exception $e) { }
        }
        return $signature_url;
    }

    public function cetakKalendarKeseluruhan(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        if (!$month || !$year) {
            $currentDate = Carbon::now();
            $month = $currentDate->month;
            $year = $currentDate->year;
        }
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
            'sig_yb' => $penasihat ? $this->getSignatureBase64($penasihat) : null,
        ];
        
        $pdf = PDF::loadView('pergerakan.kalendar_keseluruhan_pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('Kalendar_Pergerakan_' . $startOfMonth->format('Ym') . '.pdf');
    }
}