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

    /* Table Custom */
    .table-custom { 
        border-collapse: collapse; 
        border: 1.5px solid black; 
        font-size: 11pt; 
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
            <a href="{{ route('permohonan.seksyen130') }}" class="btn btn-secondary shadow-sm">
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
        
        {{-- ================= PAGE 1: BORANG 8A (BM) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">Kanun Tanah Negara</h5>
                <p class="mb-0 small">[Akta 828]</p>
                <h5 class="fw-bold mb-0">Borang 8A</h5>
                <p class="mb-0 small">(Seksyen 130)</p>
                <h5 class="fw-bold mt-3">NOTIS PERKEMBALIAN TANAH KEPADA KERAJAAN</h5>
            </div>

            <div class="text-justify mb-3 content-text">
                <p class="indent mb-2">Bahawasanya, menurut peruntukan seksyen 100 Kanun Tanah Negara, tanah yang dijadualkan di bawah ini telah dengan jalan perintah diisytiharkan dilucuthak kepada Pihak Berkuasa Negeri.</p>
                <p class="indent mb-2">Notis adalah dengan ini diberi bahawa pelucuthakan tersebut telah berkuatkuasa pada hari ini dan bahawa, oleh kerana tanah itu menjadi kepunyaan Pihak Berkuasa Negeri—</p>
                <ol type="a" class="ms-4 mb-0">
                    <li>apa-apa jua hakmilik atau kepentingan atas tanah itu yang dahulunya ada atau yang boleh timbul adalah dipadamkan; dan</li>
                    <li>dokumen hakmilik keluaran bagi tanah itu adalah tidak sah dan boleh dirampas oleh Kerajaan.</li>
                </ol>
            </div>

            <div class="row mt-5">
                <div class="col-6 pt-5">
                    <p class="mb-0">Bertarikh pada <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d') }}</strong> haribulan <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->translatedFormat('F, Y') }}</strong></p>
                    <p class="small">[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-6 text-center">
                    <div style="height: 60px;"></div>
                    <p class="fw-bold mb-0 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0">Pentadbir Tanah</p>
                    <p>Daerah {{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="text-center fw-bold mb-2 text-uppercase small">JADUAL TANAH YANG DILUCUTHAK</h6>
                <table class="table-custom text-center">
                    <thead>
                        <tr>
                            <th style="width: 25%">*Bandar/Pekan atau Mukim</th>
                            <th style="width: 20%">No. *Lot/P.T.</th>
                            <th style="width: 20%">Luas</th>
                            <th>Perihal dan No. Hakmilik</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 60px;">
                            <td>{{ $data->mukim }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->luas }}</td>
                            <td>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</td>
                        </tr>
                    </tbody>
                </table>
                <p class="small mt-1 text-start">*Potong sebagaimana yang sesuai</p>
            </div>
        </div>

        {{-- ================= PAGE 2: FORM 8A (BI) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">National Land Code</h5>
                <p class="mb-0 small">[Act 828]</p>
                <h5 class="fw-bold mb-0">Form 8A</h5>
                <p class="mb-0 small">(Section 130)</p>
                <h5 class="fw-bold mt-3 text-uppercase">NOTICE OF REVERSION TO THE STATE</h5>
            </div>

            <div class="text-justify mb-3 content-text">
                <p class="indent mb-2">Whereas pursuant to the provision of section 100 of the National Land Code, the land scheduled below has by order been declared forfeit to the State Authority.</p>
                <p class="indent mb-2">Notice is hereby given that such forfeiture has this day taken effect and that, in consequence of its vesting in the State Authority—</p>
                <ol type="a" class="ms-4 mb-0">
                    <li>any title or interest in the land heretofore substituting or capable of rising is extinguished; and</li>
                    <li>the issue document of title to the land is void and is impoundable by the State.</li>
                </ol>
            </div>

            <div class="row mt-5">
                <div class="col-6 pt-5">
                    <p class="mb-0">Dated on <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d') }}</strong> of <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('F, Y') }}</strong></p>
                    <p class="small">[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-6 text-center text-uppercase">
                    <div style="height: 60px;"></div>
                    <p class="fw-bold mb-0">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0">District Land Administrator</p>
                    <p>{{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="text-center fw-bold mb-2 text-uppercase small">SCHEDULE OF FORFEITED LAND</h6>
                <table class="table-custom text-center">
                    <thead>
                        <tr>
                            <th style="width: 25%">* Town/Village/Mukim</th>
                            <th style="width: 20%">No. *Lot/P.T.</th>
                            <th style="width: 20%">Area</th>
                            <th>Description and No. of Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 60px;">
                            <td>{{ $data->mukim }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->luas }}</td>
                            <td>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</td>
                        </tr>
                    </tbody>
                </table>
                <p class="small mt-1 text-start">*Cut as appropriate</p>
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