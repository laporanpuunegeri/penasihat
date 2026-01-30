<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuamanCase;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; // Tambah DB Facade

class GuamanController extends Controller
{
    private function getGuamanCategories()
    {
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

    private function getKendalianByList()
    {
        return [
            'SFC PUAN NORAFIAH BINTI SAINI (PGN)',
            'FC IZZATUL NAWWARAH BINTI IDRUS',
            'FC MARYAM SAKINAH BINTI MOHD NOR',
        ];
    }

    public function index(Request $request)
    {
        $query = GuamanCase::query(); 

        // 1. Tentukan Bulan & Tahun (Kalau tak pilih, ambil SEMASA)
        $bulanDipilih = $request->input('bulan', date('m')); // Default: Bulan ini
        $tahunDipilih = $request->input('tahun', date('Y')); // Default: Tahun ini

        // 2. Terapkan Filter
        // Filter Kod Perkara
        if ($request->filled('kod')) {
            $query->where('kod_perkara', $request->kod);
        }

        // Filter WAJIB: Bulan & Tahun
        $query->whereMonth('tarikh_buka', $bulanDipilih);
        $query->whereYear('tarikh_buka', $tahunDipilih);

        // 3. Dapatkan Data
        $cases = $query->latest('tarikh_buka')->paginate(10); 
        
        // 4. Data Sokongan View
        $categories = $this->getGuamanCategories();
        
        // Tarik Senarai Tahun Unik (Support PostgreSQL & MySQL)
        $senaraiTahun = GuamanCase::selectRaw('EXTRACT(YEAR FROM tarikh_buka) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Kalau senarai tahun kosong (sebab database baru), letak tahun semasa
        if ($senaraiTahun->isEmpty()) {
            $senaraiTahun = [date('Y')];
        }

        $kodFilter = $request->kod;

        return view('guaman.index', compact('cases', 'categories', 'senaraiTahun', 'kodFilter', 'bulanDipilih', 'tahunDipilih'));
    }

    public function create()
    {
        $categories = $this->getGuamanCategories();
        $kendalianList = $this->getKendalianByList();
        
        return view('guaman.create', compact('categories', 'kendalianList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kod_perkara' => 'required|string|max:50',
            'kendalian_oleh' => 'required|string|max:255',
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
    
    public function edit(GuamanCase $guaman_case)
    {
        $categories = $this->getGuamanCategories();
        $kendalianList = $this->getKendalianByList();
        return view('guaman.create', compact('categories', 'kendalianList', 'guaman_case'));
    }

    public function update(Request $request, GuamanCase $guaman_case)
    {
        $request->validate([
            'kod_perkara' => 'required|string|max:50',
            'kendalian_oleh' => 'required|string|max:255',
            'kategori_kes' => 'required|string|max:255',
            'pihak_berlawanan' => 'required|string',
            'tarikh_buka' => 'nullable|date',
        ]);

        $guaman_case->update($request->all());

        return redirect()->route('guaman.index')->with('success', 'Kes guaman berjaya dikemaskini.');
    }

    public function cetakLaporanPdf(Request $request)
    {
        // Cetak ikut bulan/tahun yang sedang dipaparkan di skrin (jika user filter)
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $cases = GuamanCase::whereMonth('tarikh_buka', $bulan)
                            ->whereYear('tarikh_buka', $tahun)
                            ->orderBy('tarikh_buka', 'desc')
                            ->get();
        
        $groupedCases = $cases->groupBy('kod_perkara');
        $categories = $this->getGuamanCategories();
        
        // Nama bulan bahasa Melayu
        $bulanName = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
        $title = "Laporan Kes Guaman - $bulanName $tahun"; 
        $currentDate = now()->format('d F Y');

        $pdf = Pdf::loadView('guaman.pdf.laporan', compact('groupedCases', 'categories', 'title', 'currentDate'))
                    ->setPaper('a4', 'landscape'); 
        
        return $pdf->stream('Laporan_Guaman_' . $tahun . $bulan . '.pdf');
    }
}