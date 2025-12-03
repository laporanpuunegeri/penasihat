<?php

namespace App\Http\Controllers;

use App\Models\Pergerakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Wajib untuk membaca fail
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Validation\Rules\Password; // Wajib

class PergerakanController extends Controller
{
    // ... (Fungsi index, formatEventData, create, store KEKAL SAMA) ...

    // Helper untuk tukar path file ke Base64 (Untuk DOMPDF)
    private function base64ImageHelper($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            $type = pathinfo(storage_path('app/public/' . $filePath), PATHINFO_EXTENSION);
            $data = Storage::disk('public')->get($filePath);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
    
    public function cetakBorang($id)
    {
        // 1. Muatkan relasi termasuk CC dan YB (yang tandatangan)
        $data = Pergerakan::with(['user', 'cc', 'yb'])->findOrFail($id);

        // 2. Tentukan Siapa YB yang akan dipaparkan butirannya
        // Kita guna ID yang disimpan di transaction, kalau tak ada, guna YB utama
        $userToSignYB = $data->yb ?? User::where('role', 'yb')->orWhere('role', 'BOSS')->first();

        // 3. Jana Base64 Signatures
        $signatureApplicant = $this->base64ImageHelper($data->user->signature_file ?? null);
        $signatureCC        = $this->base64ImageHelper($data->cc->signature_file ?? null);
        $signatureYB        = $this->base64ImageHelper($userToSignYB->signature_file ?? null);
        
        $namaYB = $userToSignYB ? $userToSignYB->name : 'YB DATUK KHAIRUL AZREEM'; 
        $bahagianYB = $userToSignYB ? $userToSignYB->bahagian : 'BAHAGIAN PENASIHAT';


        if ($data->status_yb !== 'Lulus') {
            return back()->with('error', 'Permohonan belum selesai proses kelulusan.');
        }

        $pdfData = [
            'data' => $data,
            'namaYB' => $namaYB,
            'bahagianYB' => $bahagianYB,
            'negeri' => $data->user->negeri ?? 'NEGERI TIDAK DIKENALI',
            
            // 🔥 HANTAR BASE64 STRINGS KE VIEW
            'sig_applicant' => $signatureApplicant,
            'sig_cc'        => $signatureCC,
            'sig_yb'        => $signatureYB, 
        ];

        if ($data->kenderaan == 'Kenderaan Sendiri') {
            $pdf = Pdf::loadView('pergerakan.borang_kenderaan_sendiri', $pdfData);
            return $pdf->stream('Permohonan_Kenderaan_Sendiri_' . $data->id . '.pdf');
        } elseif ($data->kenderaan == 'Kenderaan Pejabat') {
            $pdf = Pdf::loadView('pergerakan.borang_kenderaan_jabatan', $pdfData);
            return $pdf->stream('Borang_Kenderaan_Pejabat_' . $data->id . '.pdf');
        }
        return back()->with('error', 'Jenis kenderaan tidak sah.');
    }
    
    /**
     * Menjana PDF laporan kalendar pergerakan keseluruhan (index)
     */
    public function cetakKalendarPDF()
    {
        // Mendapatkan data pergerakan yang telah Lulus untuk tahun semasa
        $pergerakan = Pergerakan::with('user') // Pastikan ada relasi 'user' untuk mendapatkan nama pegawai
                            ->where('status_yb', 'Lulus') 
                            ->whereYear('tarikh_mula', date('Y')) // Hanya rekod tahun semasa
                            ->orderBy('tarikh_mula', 'asc')
                            ->get(); 

        $data = [
            'pergerakan' => $pergerakan,
            'tahun' => date('Y'),
            'tarikh_cetak' => now()->format('d/m/Y H:i:s')
        ];

        // Gantikan 'pdf.kalendar_pergerakan_pdf' dengan nama view blade PDF anda
        // Pastikan anda telah mencipta fail blade ini di resources/views/pdf/
        $pdf = Pdf::loadView('pdf.kalendar_pergerakan_pdf', $data)
                    ->setPaper('A4', 'landscape'); // Tetapkan orientasi melintang

        // Hantar fail untuk dipaparkan terus di browser atau dimuat turun
        return $pdf->stream('kalendar-pergerakan-' . date('Y') . '.pdf');
    }
    
// ... (sambungan fungsi lain seperti showBorang, destroy) ...

}