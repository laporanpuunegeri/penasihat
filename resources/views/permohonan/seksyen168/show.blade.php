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
        gap: 30px; /* Jarak antara page BM dan BI */
    }

    .page {
        background-color: white;
        width: 210mm;       /* Lebar A4 */
        min-height: 297mm;  /* Tinggi A4 */
        padding: 20mm 20mm; /* Margin selamat */
        box-shadow: 0 0 15px rgba(0,0,0,0.15);
        font-family: 'Times New Roman', serif;
        line-height: 1.4;
        font-size: 11.5pt;
        color: black;
        position: relative;
    }

    .text-justify { text-align: justify; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: bold; }
    .text-uppercase { text-transform: uppercase; }
    .indent { text-indent: 50px; }
    .small { font-size: 10pt; }
    .fst-italic { font-style: italic; }

    /* Table Custom */
    .table-custom { 
        border-collapse: collapse; 
        border: 1.5px solid black; 
        font-size: 10pt; 
        width: 100%;
    }
    .table-custom th, .table-custom td { 
        border: 1px solid black; 
        padding: 8px; 
        vertical-align: middle;
    }

    /* =========================================
       STYLE KHAS UNTUK PRINT / PDF
       ========================================= */
    @media print {
        @page { size: A4 portrait; margin: 0; }
        
        /* Sembunyikan UI */
        body * { visibility: hidden; }
        .no-print, .sidebar, nav, header, footer, .btn, .d-print-none { display: none !important; }

        /* Paparkan Kertas Sahaja */
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
            /* PENTING: height auto supaya tak ada page kosong */
            height: auto; 
            min-height: 0;
            
            margin: 0;
            padding: 20mm 20mm;
            box-shadow: none;
            border: none;
            
            /* Paksa page baru selepas setiap kertas */
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
            <a href="{{ route('permohonan.seksyen168') }}" class="btn btn-secondary shadow-sm">
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
        
        {{-- ================= PAGE 1: BORANG 10D (BM) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">Kanun Tanah Negara</h5>
                <p class="mb-0 small">[Akta 828]</p>
                <h5 class="fw-bold mb-0">Borang 10D</h5>
                <p class="mb-0 small">(Seksyen 168)</p>
                <h5 class="fw-bold mt-3 text-uppercase" style="max-width: 90%; margin: 0 auto;">NOTIS MENGENAI NIAT UNTUK MENGELUARKAN HAKMILIK SAMBUNGAN (ATAU DOKUMEN KELUARAN YANG BAHARU SEBAGAI GANTINYA)</h5>
            </div>

            <div class="text-justify mb-3 content-text">
                <p class="indent mb-2">Pada menjalankan kuasa yang diberikan oleh seksyen 168 Kanun Tanah Negara, notis adalah dengan ini diberi bahawa adalah dicadangkan untuk menggantikan dokumen hakmilik keluaran bagi tanah yang diperihalkan dalam Jadual di bawah ini kerana sebab yang berikut:</p>
                
                <p class="fw-bold text-center fst-italic my-4" style="font-size: 1.1em;">"{{ $data->sebab_permohonan }}"</p>
            </div>

            <div class="row mt-4">
                <div class="col-6 pt-5">
                    <p class="mb-0">Bertarikh pada <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d') }}</strong> haribulan <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->translatedFormat('F, Y') }}</strong></p>
                    <p class="small">[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-6 text-center">
                    <div style="height: 60px;"></div>
                    <p class="fw-bold mb-0 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0">Pentadbir Tanah / Pendaftar</p>
                    <p>Daerah {{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="text-center fw-bold mb-2 text-uppercase small">JADUAL</h6>
                <table class="table-custom w-100 text-center align-middle">
                    <thead>
                        <tr>
                            <th>Bil.</th>
                            <th>Bandar/Pekan/ Mukim</th>
                            <th>No. Lot/ Petak/P.T.</th>
                            <th>Jenis dan No. Hakmilik</th>
                            <th>Keluasan</th>
                            <th>No. Fail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 60px;">
                            <td>1.</td>
                            <td>{{ $data->mukim }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->jenis_hakmilik }}<br>{{ $data->no_hakmilik }}</td>
                            <td>{{ $data->luas }}</td>
                            <td>{{ $data->no_fail }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= PAGE 2: FORM 10D (BI) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">National Land Code</h5>
                <p class="mb-0 small">[Act 828]</p>
                <h5 class="fw-bold mb-0">Form 10D</h5>
                <p class="mb-0 small">(Section 168)</p>
                <h5 class="fw-bold mt-3 text-uppercase" style="max-width: 90%; margin: 0 auto;">NOTICE OF INTENTION TO ISSUE TITLE IN CONTINUATION (OR NEW ISSUE DOCUMENT IN LIEU THEREOF)</h5>
            </div>

            <div class="text-justify mb-3 content-text">
                <p class="indent mb-2">In exercise of the power conferred by section 168 of the National Land Code, notice is hereby given that it is proposed to replace the issue document of title to the land described in the Schedule below for the following reason:</p>
                
                {{-- LOGIK TRANSLATE SEBAB PERMOHONAN --}}
                <p class="fw-bold text-center fst-italic my-4" style="font-size: 1.1em;">
                    @if(str_contains(strtolower($data->sebab_permohonan), 'hilang'))
                        "The issue document of title has been lost."
                    @elseif(str_contains(strtolower($data->sebab_permohonan), 'rosak'))
                        "The issue document of title has been damaged."
                    @elseif(str_contains(strtolower($data->sebab_permohonan), 'musnah'))
                         "The issue document of title has been partially destroyed."
                    @else
                        "{{ $data->sebab_permohonan }}"
                    @endif
                </p>
            </div>

            <div class="row mt-4">
                <div class="col-6 pt-5">
                    <p class="mb-0">Dated on <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d') }}</strong> of <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('F, Y') }}</strong></p>
                    <p class="small">[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-6 text-center text-uppercase">
                    <div style="height: 60px;"></div>
                    <p class="fw-bold mb-0">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0">Land Administrator / Registrar</p>
                    <p>{{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="text-center fw-bold mb-2 text-uppercase small">SCHEDULE</h6>
                <table class="table-custom w-100 text-center align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Town/Village/ Mukim</th>
                            <th>Lot/Parcel/ L.O. No.</th>
                            <th>Description and No. of Title</th>
                            <th>Area</th>
                            <th>File No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 60px;">
                            <td>1.</td>
                            <td>{{ $data->mukim }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->jenis_hakmilik }}<br>{{ $data->no_hakmilik }}</td>
                            <td>{{ $data->luas }}</td>
                            <td>{{ $data->no_fail }}</td>
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