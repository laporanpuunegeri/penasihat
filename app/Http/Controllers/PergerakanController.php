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

        // Logik Penapisan
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
            // Ubah logik pewarnaan untuk menyokong 'Tolak' sahaja, bukan (CC/YB)
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
                ]
            ];
        });
        
        $currentUserId = Auth::id();

        return view('pergerakan.index', compact('pergerakan', 'senarai_pegawai', 'currentUserId'));
    }

    /**
     * Paparkan borang pendaftaran pergerakan baru.
     */
    public function create()
    {
        return view('pergerakan.create');
    }

    /**
     * Simpan rekod pergerakan baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tarikh_mula' => 'required|date',
            'tarikh_akhir' => 'required|date|after_or_equal:tarikh_mula',
            'masa_mula' => 'nullable', 'masa_akhir' => 'nullable',
            'jenis' => 'required|string|max:100', 
            'kenderaan' => 'required|string', 'tujuan_penggunaan' => 'required|string|max:255',
            'destinasi' => 'required|string|max:255', 'catatan' => 'nullable|string|max:500',
        ]);

        $pergerakan = new Pergerakan($validatedData);
        $pergerakan->user_id = Auth::id();
        $pergerakan->status_cc = 'Pending'; $pergerakan->status_yb = 'Pending';
        $pergerakan->save();

        return redirect()->route('pergerakan.index')->with('success', 'Permohonan pergerakan berjaya dihantar.');
    }


    /**
     * Handle CC/Boss review (Sokong/Tolak dengan Penetapan Kenderaan).
     */
    public function cc_review(Request $request, Pergerakan $pergerakan)
    {
        if (!Gate::allows('review-cc', $pergerakan)) { abort(403); }

        $actionType = $request->input('action_type');
        $catatan_cc = $request->input('catatan_cc');

        if ($actionType === 'support') {
            $validated = $request->validate(['no_kenderaan' => 'required|string|max:50', 'nama_pemandu' => 'required|string|max:255',]);
            $pergerakan->status_cc = 'Sokong'; $pergerakan->catatan_cc = $catatan_cc;
            $pergerakan->no_kenderaan = $validated['no_kenderaan']; $pergerakan->nama_pemandu = $validated['nama_pemandu'];
            $pergerakan->save();
            $message = 'Permohonan pergerakan telah berjaya disokong dan kenderaan ditugaskan.';
        } elseif ($actionType === 'reject') {
            $validated = $request->validate(['catatan_cc' => 'required|string|max:500']);
            $pergerakan->status_cc = 'Tolak'; $pergerakan->status_yb = 'Tolak'; // Tukar ke 'Tolak' untuk konsistensi
            $pergerakan->catatan_cc = $validated['catatan_cc'];
            $pergerakan->save();
            $message = 'Permohonan pergerakan telah ditolak.';
        }

        return redirect()->route('pergerakan.index')->with('success', $message);
    }
    
    // --- Tindakan Ringkas CC (Untuk Kenderaan Sendiri) ---

    public function lulusCc($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        
        if (!Gate::allows('review-cc', $pergerakan)) { abort(403, 'Anda tiada kebenaran untuk menyokong pergerakan ini.'); }
        
        if ($pergerakan->kenderaan !== 'Kenderaan Sendiri') {
            abort(403, 'Rekod ini memerlukan penetapan kenderaan/pemandu melalui Modal Ulasan CC.'); 
        }

        $pergerakan->status_cc = 'Sokong';
        $pergerakan->catatan_cc = 'Disokong tanpa penetapan kenderaan (Kenderaan Sendiri)';
        $pergerakan->save();

        return redirect()->route('pergerakan.index')->with('success', 'Permohonan pergerakan (Kenderaan Sendiri) berjaya disokong.');
    }

    public function tolakCc($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        if (!Gate::allows('review-cc', $pergerakan)) { abort(403); }

        $pergerakan->status_cc = 'Tolak'; $pergerakan->status_yb = 'Tolak'; // Tukar ke 'Tolak'
        $pergerakan->catatan_cc = 'Ditolak oleh CC/Boss melalui pautan segera.';
        $pergerakan->save();

        return redirect()->route('pergerakan.index')->with('success', 'Permohonan pergerakan telah ditolak oleh CC/Boss.');
    }

    // --- Tindakan YB ---

    public function lulusYb($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        if (!Gate::allows('review-yb', $pergerakan)) { abort(403); }

        $pergerakan->status_yb = 'Lulus';
        $pergerakan->save();

        return redirect()->route('pergerakan.index')->with('success', 'Permohonan pergerakan berjaya DILULUSKAN.');
    }

    public function tolakYb($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        if (!Gate::allows('review-yb', $pergerakan)) { abort(403); }

        $pergerakan->status_yb = 'Tolak'; // Tukar ke 'Tolak'
        $pergerakan->save();

        return redirect()->route('pergerakan.index')->with('success', 'Permohonan pergerakan telah DITOLAK oleh YB.');
    }

    // --- CRUD dan Cetak Borang ---

    /**
     * Memadam rekod pergerakan.
     */
    public function destroy($id)
    {
        $pergerakan = Pergerakan::findOrFail($id);
        $user = Auth::user();
        $userRole = strtolower($user->role);
        
        $canForceDelete = in_array($userRole, ['super_admin', 'boss']); 
        $isApplicant = ($user->id === $pergerakan->user_id);
        
        try {
            if (!$isApplicant && !$canForceDelete) {
                throw new \Exception('Anda tiada kebenaran untuk memadam rekod ini (Bukan Pemilik/Admin).', 403);
            }

            if ($pergerakan->status_yb === 'Lulus' && !$canForceDelete) {
                throw new \Exception('Rekod yang telah DILULUSKAN oleh YB tidak boleh dipadam.', 403);
            }
            
            // Menggunakan forceDelete() jika model guna SoftDeletes, 
            // memastikan rekod Lulus dipadam sepenuhnya oleh admin
            if ($canForceDelete) {
                 $pergerakan->forceDelete();
            } else {
                 $pergerakan->delete();
            }
            
            return response()->json(['status' => 'success', 'message' => 'Rekod berjaya dipadam.']);
            
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 403 ? 403 : 500;
            $message = $e->getMessage() ?: 'Gagal memadam rekod.';
            
            return response()->json(['status' => 'error', 'message' => $message], $statusCode);
        }
    }

    /**
     * Menjana PDF Borang Pergerakan individu.
     */
    public function cetakBorang($id)
    {
        $pergerakan = Pergerakan::with('user')->findOrFail($id);
        
        if ($pergerakan->status_yb !== 'Lulus') {
            abort(403, 'Hanya borang yang DILULUSKAN sahaja boleh dicetak.');
        }
        
        if (str_contains($pergerakan->kenderaan, 'Pejabat')) {
            $viewName = 'pergerakan.borang_kenderaan_pejabat';
        } else {
            $viewName = 'pergerakan.borang_kenderaan_sendiri';
        }
        
        $penasihat = User::where('role', 'yb')->first();
        $cc_user = $penasihat ?? Auth::user(); 
        
        $sig_yb = $penasihat ? $this->getSignatureBase64($penasihat) : null;
        $sig_applicant = $this->getSignatureBase64($pergerakan->user);
        $sig_cc = $cc_user ? $this->getSignatureBase64($cc_user) : null;

        $data = [
            'pergerakan' => $pergerakan,
            'namaYB' => $penasihat->name ?? 'NAMA YB TIADA', 'bahagianYB' => $penasihat->bahagian ?? 'BAHAGIAN YB TIADA', 
            'jawatanYB' => $penasihat->nama_jawatan ?? 'JAWATAN YB TIADA', 'sig_yb' => $sig_yb,
            'sig_applicant' => $sig_applicant, 
            'sig_cc' => $sig_cc, 'cc_name' => $cc_user->name ?? 'CC TIADA',
            'cc_jawatan' => $cc_user->nama_jawatan ?? 'Ketua Bahagian', 
            'pemandu_ditugaskan' => $pergerakan->nama_pemandu,
            'no_kenderaan_rasmi' => $pergerakan->no_kenderaan,
        ];
        
        $pdf = PDF::loadView($viewName, $data);
        return $pdf->stream('Borang_Pergerakan_' . $pergerakan->id . '.pdf');
    }

    /**
     * Mendapatkan butiran tandatangan dalam format Base64.
     */
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
            } catch (\Exception $e) {
            }
        }
        return $signature_url;
    }

    /**
     * Menjana PDF Kalendar Pergerakan Keseluruhan.
     */
    public function cetakKalendarKeseluruhan(Request $request)
    {
        // 1. Ambil input Bulan dan Tahun dari URL (FullCalendar)
        $month = $request->input('month');
        $year = $request->input('year');

        // Jika bulan/tahun tiada, gunakan bulan/tahun semasa
        if (!$month || !$year) {
            $currentDate = Carbon::now();
            $month = $currentDate->month;
            $year = $currentDate->year;
        }

        // 2. Dapatkan data Pengesah (YB)
        $penasihat = User::where('role', 'yb')->first();
        
        // 3. Tentukan tarikh mula dan akhir bulan tersebut
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // 4. Ambil data pergerakan LULUS untuk bulan tersebut
        $pergerakan_data = Pergerakan::with('user')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tarikh_mula', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('tarikh_akhir', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($query) use ($startOfMonth, $endOfMonth) {
                          $query->where('tarikh_mula', '<', $startOfMonth)
                                ->where('tarikh_akhir', '>', $endOfMonth);
                      });
            })
            ->where('status_yb', 'Lulus') // Hanya sertakan rekod yang LULUS oleh YB
            ->orderBy('tarikh_mula')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->tarikh_mula)->format('Y-m-d'); // Kumpulkan mengikut hari mula
            });

        // 5. Sediakan data untuk View PDF
        $data = [
            // Data Kalendar
            'bulan_text' => $startOfMonth->translatedFormat('F Y'), // Contoh: 'Disember 2025'
            'pergerakan_by_day' => $pergerakan_data,
            'start_of_month' => $startOfMonth,
            'end_of_month' => $endOfMonth,
            'tarikh_cetak' => Carbon::now()->format('d/m/Y H:i:s'),

            // Data Pengesah (YB)
            'namaYB' => $penasihat->name ?? 'YANG BERHORMAT',
            'jawatanYB' => $penasihat->nama_jawatan ?? 'Jawatan',
            'sig_yb' => $penasihat ? $this->getSignatureBase64($penasihat) : null,
        ];
        
        // 6. Muatkan View dan Jana PDF (Guna format Landskap untuk kalendar)
        $pdf = PDF::loadView('pergerakan.kalendar_keseluruhan_pdf', $data)
                  ->setPaper('a4', 'landscape');

        $filename = 'Kalendar_Pergerakan_' . $startOfMonth->format('Ym') . '.pdf';

        return $pdf->stream($filename);
    }
    
    public function cetakKalendarIndividuPDF(Request $request)
    {
        // Fungsi ini mungkin tidak digunakan
    }
}