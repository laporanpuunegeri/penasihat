<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuamanCase;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GuamanController extends Controller
{
    /**
     * Helper untuk mendapatkan senarai kategori kes (Mapping KOD).
     */
    private function getGuamanCategories()
    {
        // Berdasarkan dokumen STATUS BULAN NOVEMBER 2025
        return [
            '01' => ['title' => 'PERKARA PERLEMBAGAAN / WARGANEGARA, AGAMA', 'route_kategori' => 'Kewarganegaraan'],
            '02' => ['title' => 'PERKHIDMATAN AWAM / TATATERTIB, GAJI', 'route_kategori' => 'Tatatertib'],
            '11' => ['title' => 'PELBAGAI PERLEMBAGAAN', 'route_kategori' => 'Perlembagaan'],
            '16' => ['title' => 'PELBAGAI TORT (JKR, Pencerobohan)', 'route_kategori' => 'Pelbagai Tort'],
            '17' => ['title' => 'SALAH TANGKAP, TAHANAN, KEKERASAN', 'route_kategori' => 'Salah Tangkap'],
            '18' => ['title' => 'KEMALANGAN JALANRAYA', 'route_kategori' => 'Kemalangan Jalanraya'],
            '20' => ['title' => 'KES FITNAH / DEFAMATION', 'route_kategori' => 'Fitnah'],
            '21' => ['title' => 'KECUAIAN PERUBATAN', 'route_kategori' => 'Kecuaian Perubatan'],
            '26' => ['title' => 'KONTRAK', 'route_kategori' => 'Kontrak'],
        ];
    }

    /**
     * Helper untuk mendapatkan senarai pegawai yang mengendalikan kes.
     */
    private function getKendalianByList()
    {
        return [
            'SFC PUAN NORAFIAH BINTI SAINI (PGN)',
            'FC IZZATUL NAWWARAH BINTI IDRUS',
            'FC MARYAM SAKINAH BINTI MOHD NOR',
        ];
    }

    /**
     * Paparan utama Modul Guaman (Senarai Kes) dengan fungsi filter.
     */
    public function index(Request $request)
    {
        $categories = $this->getGuamanCategories();
        $kodFilter = $request->input('kod');

        $query = GuamanCase::query();
        if ($kodFilter) {
             $query->where('kod_perkara', $kodFilter);
        }
                
        $cases = $query->orderBy('tarikh_buka', 'desc')->get();

        return view('guaman.index', compact(
            'categories', 
            'cases', 
            'kodFilter'
            // Pembolehubah statistik telah dibuang
        ));
    }
    /**
     * Paparkan Borang Daftar Kes Baru, hantar senarai pegawai kendalian.
     */
    public function create()
    {
        $categories = $this->getGuamanCategories();
        $kendalianList = $this->getKendalianByList();
        
        return view('guaman.create', compact('categories', 'kendalianList'));
    }

    /**
     * Simpan rekod kes baru ke dalam database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kod_perkara' => 'required|string|max:50',
            'rujukan_fail' => 'nullable|string|max:255',
            'kendalian_oleh' => 'required|string|max:255',
            'mahkamah' => 'nullable|string|max:255',
            'kategori_kes' => 'required|string|max:255',
            'pihak_berlawanan' => 'required|string',
            'tarikh_buka' => 'nullable|date',
        ]);

        GuamanCase::create([
            'kod_perkara' => $request->kod_perkara,
            'rujukan_fail' => $request->rujukan_fail,
            'kendalian_oleh' => $request->kendalian_oleh, 
            'mahkamah' => $request->mahkamah,
            'rujukan_mahkamah' => $request->rujukan_mahkamah,
            'kategori_kes' => $request->kategori_kes,
            'pihak_berlawanan' => $request->pihak_berlawanan,
            'status_kes' => $request->status_kes ?? 'Kendalian PGN',
            'tarikh_buka' => $request->tarikh_buka,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('guaman.index')->with('success', 'Kes guaman baru berjaya didaftarkan.');
    }
    
    /**
     * Paparkan Borang Sunting/Detail Kes sedia ada.
     */
    public function edit(GuamanCase $guaman_case)
    {
        $categories = $this->getGuamanCategories();
        $kendalianList = $this->getKendalianByList();
        
        // Hantar data kes yang dimuat turun ($guaman_case) ke view
        return view('guaman.create', compact('categories', 'kendalianList', 'guaman_case'));
    }

    /**
     * Kemaskini rekod kes sedia ada.
     */
    public function update(Request $request, GuamanCase $guaman_case)
    {
        // Logik Validasi yang sama seperti method store()
        $request->validate([
            'kod_perkara' => 'required|string|max:50',
            'rujukan_fail' => 'nullable|string|max:255',
            'kendalian_oleh' => 'required|string|max:255',
            'mahkamah' => 'nullable|string|max:255',
            'kategori_kes' => 'required|string|max:255',
            'pihak_berlawanan' => 'required|string',
            'tarikh_buka' => 'nullable|date',
        ]);

        // Kemaskini data
        $guaman_case->update($request->all());

        return redirect()->route('guaman.index')->with('success', 'Kes guaman berjaya dikemaskini.');
    }

    /**
     * Mendapatkan data dan menjana output PDF untuk preview.
     */
    public function cetakLaporanPdf(Request $request)
    {
        $cases = GuamanCase::orderBy('tarikh_buka', 'desc')->get();
        $groupedCases = $cases->groupBy('kod_perkara');
        $categories = $this->getGuamanCategories();
        $currentDate = now()->format('d F Y');
        $title = 'Laporan Kes Guaman'; 

        $pdf = Pdf::loadView('guaman.pdf.laporan', compact('groupedCases', 'categories', 'title', 'currentDate'))
                    ->setPaper('a4', 'landscape'); 

        // Menggunakan stream() untuk PREVIEW
        return $pdf->stream('Laporan_Guaman_' . date('Ymd') . '.pdf');
    }
}