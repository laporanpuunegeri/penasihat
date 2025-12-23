{{-- LOGIK: Kalau Agensi login, guna layout Agensi. Kalau Admin, guna layout Admin (app) --}}
@extends(Auth::guard('agensi')->check() ? 'layouts.agensi' : 'layouts.app')

@section('content')
<style>
    /* =========================================
       STYLE PAPARAN SKRIN (PREVIEW MODE)
       ========================================= */
    .print-container {
        background-color: #f0f0f0; 
        padding: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 30px; 
    }

    .page {
        background-color: white;
        width: 210mm;       /* Lebar A4 */
        min-height: 297mm;  /* Tinggi A4 */
        padding: 20mm 15mm; /* Padding kiri/kanan dikurangkan sikit untuk jadual lebar */
        box-shadow: 0 0 15px rgba(0,0,0,0.15);
        font-family: 'Times New Roman', serif;
        line-height: 1.4;
        font-size: 11pt; /* Font kecil sikit untuk muatkan jadual */
        color: black;
        position: relative;
    }

    .text-justify { text-align: justify; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: bold; }
    .text-uppercase { text-transform: uppercase; }
    .indent { text-indent: 50px; }
    .small { font-size: 10pt; }

    /* Table Jadual Padat */
    .table-jadual { 
        border-collapse: collapse; 
        border: 1.5px solid black; 
        width: 100%;
        margin-top: 10px;
        font-size: 10pt; /* Kecilkan font dalam table */
    }
    .table-jadual th {
        background-color: #f8f9fa; 
        font-weight: bold;
        border: 1px solid black; 
        padding: 8px 4px;
        vertical-align: middle;
    }
    .table-jadual td { 
        border: 1px solid black; 
        padding: 8px 4px; 
        vertical-align: middle; 
    }

    /* =========================================
       STYLE KHAS UNTUK PRINT / PDF
       ========================================= */
    @media print {
        @page { size: A4 portrait; margin: 0; }
        
        body * { visibility: hidden; }
        .no-print, .sidebar, nav, header, footer, .btn, .d-print-none { display: none !important; }

        .print-container, .print-container * { 
            visibility: visible; 
            background: none !important;
        }
        
        .print-container {
            display: block;
            padding: 0;
            margin: 0;
        }

        .page {
            width: 210mm;
            height: auto; 
            min-height: 0;
            margin: 0;
            padding: 20mm 15mm; /* Sama macam preview */
            box-shadow: none;
            border: none;
            page-break-after: always; 
            position: relative;
            left: 0; top: 0;
        }

        .page:last-child {
            page-break-after: auto;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-3 no-print">
        
        {{-- LOGIK BUTANG KEMBALI --}}
        @if(Auth::guard('agensi')->check())
            <a href="{{ route('permohonan.seksyen175d') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Senarai
            </a>
        @else
            <a href="{{ route('admin.warta.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Semakan
            </a>
        @endif

        {{-- Butang Cetak (Hanya Admin) --}}
        @if(!Auth::guard('agensi')->check())
            <button onclick="window.print()" class="btn btn-success shadow-sm">
                <i class="fas fa-print"></i> Cetak 
            </button>
        @endif
    </div>

    {{-- CONTAINER KERTAS --}}
    <div class="print-container">
        
        {{-- ================= PAGE 1: BORANG 10H (BM) ================= --}}
        <div class="page">
            <div class="text-center mb-5">
                <h5 class="fw-bold mb-0">Kanun Tanah Negara</h5>
                <p class="mb-0">[Akta 828]</p>
                <h5 class="fw-bold mb-0">Borang 10H</h5>
                <p class="mb-0">(Seksyen 175 D)</p>
                <h5 class="fw-bold mt-4 text-uppercase">NOTIS BAHAWA DOKUMEN HAKMILIK DAFTARAN SEMENTARA ADALAH TERBUKA UNTUK DIPERIKSA</h5>
            </div>

            <div class="content-body text-justify">
                <p class="indent">Notis adalah dengan ini diberi bahawa dokumen hakmilik daftaran sementara berkenaan dengan tanah yang diperihalkan dalam Jadual di bawah ini adalah sekarang terbuka untuk diperiksa di Pejabat Tanah <strong>{{ $data->daerah }}</strong> dan boleh diperiksa tanpa bayaran semasa waktu pejabat biasa.</p>

                <p class="indent">Mana-mana orang atau badan yang mempunyai apa-apa kepentingan atas tanah tersebut boleh membantah terhadap apa-apa kemasukan di dalamnya atau apa-apa ketinggalan kemasukan itu selaras dengan seksyen 175E dalam tempoh tiga bulan dari tarikh penyiaran notis ini dalam Warta.</p>
            </div>

            <div class="row mt-5">
                <div class="col-7">
                    <p class="mb-0">Bertarikh : {{ \Carbon\Carbon::parse($data->tarikh_notis)->translatedFormat('j F Y') }}</p>
                    <div class="mt-4">
                        <p class="small">[{{ $data->no_fail }}]</p>
                    </div>
                </div>
                <div class="col-5 text-center">
                    <div style="height: 80px; border-bottom: 1px dotted black; width: 90%; margin: 0 auto;"></div>
                    <p class="fw-bold mb-0 mt-2 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0 small">Penolong Pentadbir Tanah Daerah</p>
                    <p class="fw-bold small text-uppercase">{{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="fw-bold text-center mb-3 text-uppercase">JADUAL</h6>
                <table class="table-jadual text-center">
                    <thead>
                        <tr>
                            <th width="10%">Daerah</th>
                            <th width="15%">Bandar / Pekan / Mukim</th>
                            <th width="15%">Perihal dan No. Hakmilik</th>
                            <th width="15%">No. Lot / Petak / P.T</th>
                            <th width="10%">Luas</th>
                            <th width="20%">Tuan punya Berdaftar</th>
                            <th width="15%">Butir-butir kepentingan (jika ada)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $data->daerah }}</td>
                            <td>{{ $data->mukim }}</td>
                            <td>{{ $data->jenis_hakmilik }}<br>{{ $data->no_hakmilik }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->luas }}</td>
                            <td>{{ $data->nama_pemilik }}<br><small>No. KP: {{ $data->no_kp_pemilik }}</small></td>
                            <td>{{ $data->bahagian_tanah }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= PAGE 2: FORM 10H (BI) ================= --}}
        <div class="page">
            <div class="text-center mb-5">
                <h5 class="fw-bold mb-0">National Land Code</h5>
                <p class="mb-0">[Act 828]</p>
                <h5 class="fw-bold mb-0">Form 10H</h5>
                <p class="mb-0">(Section 175D)</p>
                <h5 class="fw-bold mt-4 text-uppercase">NOTICE THAT THE PROVISIONAL REGISTER DOCUMENT OF TITLE IS OPEN FOR INSPECTION</h5>
            </div>

            <div class="content-body text-justify">
                <p class="indent">Notice is hereby given that the provisional register document of title in respect of lands described in the Schedule below is now open for inspection at the <strong>Pejabat Tanah {{ $data->daerah }}</strong> and can be inspected without payment during the normal office hours.</p>

                <p class="indent">Any person or body who has any interest in the said land may object to any entry therein or any omission thereof in accordance with section 175E within three months from the date of publication of this notice in the Gazette.</p>
            </div>

            <div class="row mt-5">
                <div class="col-7">
                    <p class="mb-0">Dated : {{ \Carbon\Carbon::parse($data->tarikh_notis)->format('j F Y') }}</p>
                    <div class="mt-4">
                        <p class="small">[{{ $data->no_fail }}]</p>
                    </div>
                </div>
                <div class="col-5 text-center">
                    <div style="height: 80px; border-bottom: 1px dotted black; width: 90%; margin: 0 auto;"></div>
                    <p class="fw-bold mb-0 mt-2 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0 small">Assistant District Land Administrator</p>
                    <p class="fw-bold small text-uppercase">{{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="fw-bold text-center mb-3 text-uppercase">SCHEDULE</h6>
                <table class="table-jadual text-center">
                    <thead>
                        <tr>
                            <th width="10%">District</th>
                            <th width="15%">Town / Village / Mukim</th>
                            <th width="15%">Description and No. of Title</th>
                            <th width="15%">Lot No. / Parcel / L.O. No.</th>
                            <th width="10%">Area</th>
                            <th width="20%">Registered proprietor</th>
                            <th width="15%">Particulars of interest (if any)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $data->daerah }}</td>
                            <td>{{ $data->mukim }}</td>
                            <td>{{ $data->jenis_hakmilik }}<br>{{ $data->no_hakmilik }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->luas }}</td>
                            <td>{{ $data->nama_pemilik }}<br><small>I/C: {{ $data->no_kp_pemilik }}</small></td>
                            <td>{{ $data->bahagian_tanah }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- CSS PENYOROK SIDEBAR (ADMIN PREVIEW MODE) --}}
@if(!Auth::guard('agensi')->check())
<style>
    /* Hilangkan sidebar & topbar bila Admin tengok di skrin */
    .sidebar, nav.navbar, .sticky-footer, .topbar, #accordionSidebar {
        display: none !important;
    }

    /* Background gelap supaya fokus pada kertas */
    body, #content-wrapper {
        background-color: #525659 !important; 
        margin: 0 !important;
        padding: 0 !important;
    }

    .print-container {
        background-color: #525659;
    }

    .container-fluid {
        padding-top: 20px;
        padding-bottom: 50px;
    }
</style>
@endif

@endsection