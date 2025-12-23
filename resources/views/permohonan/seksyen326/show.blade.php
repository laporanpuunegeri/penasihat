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
        padding: 25mm 25mm; /* Margin selamat */
        box-shadow: 0 0 15px rgba(0,0,0,0.15);
        font-family: 'Times New Roman', serif;
        line-height: 1.5;
        font-size: 11pt;
        color: black;
        box-sizing: border-box;
    }

    .text-justify { text-align: justify; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: bold; }
    .text-uppercase { text-transform: uppercase; }
    .small { font-size: 10pt; }

    /* Table Jadual */
    .table-jadual { 
        border-collapse: collapse; 
        border: 1.5px solid black; 
        width: 100%;
        margin-top: 10px;
        font-size: 10pt;
    }
    .table-jadual th {
        border: 1px solid black; 
        padding: 8px 5px;
        vertical-align: middle;
        font-weight: bold;
        background-color: #f8f9fa;
    }
    .table-jadual td { 
        border: 1px solid black; 
        padding: 8px 5px; 
        vertical-align: middle; 
    }

    /* Style untuk potong perkataan (Strikethrough) */
    s { text-decoration: line-through; color: #000; }

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
            height: 297mm; 
            margin: 0;
            padding: 25mm 25mm;
            box-shadow: none;
            border: none;
            page-break-after: always; 
            display: block; 
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
            <a href="{{ route('permohonan.seksyen326') }}" class="btn btn-secondary shadow-sm">
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
        
        {{-- ================= PAGE 1: BORANG 19C (BM) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0 text-uppercase">Kanun Tanah Negara</h5>
                <p class="mb-0">[Akta 828]</p>
                <p class="mb-0">Borang 19C</p>
                <p class="mb-0">(Seksyen 326)</p>
                <h5 class="fw-bold mt-4 text-uppercase">NOTIS MENGENAI NIAT MEMOTONG KAVEAT</h5>
            </div>

            <div class="content-body text-justify">
                <p>Kepada <strong>{{ strtoupper($data->nama_penerima) }}</strong> NO. KP: <strong>{{ $data->ic_penerima }}</strong> yang beralamat di <strong>{{ $data->alamat_penerima }}</strong> dan atas permintaan siapa Kaveat Sendirian Berdaftar No. <strong>{{ $data->no_perserahan_kaveat }}</strong> adalah dimasukkan ke dalam hakmilik bagi tanah yang diperihalkan dalam jadual di bawah.</p>
                
                <p class="mt-3">Bahawasanya kaveat di atas bermaksud supaya mengikat tanah itu sendiri yang diperihalkan dalam jadual tersebut;</p>

                <p class="mt-3">Dan bahawasanya <strong>{{ strtoupper($data->nama_pemohon) }}</strong> telah membuat permohonan kepada saya supaya kaveat yang tersebut itu dipotong;</p>
                
                <p class="mt-3">Ambil perhatian bahawa, pada menjalankan kuasa-kuasa yang diberi oleh seksyen 326 Kanun Tanah Negara, saya akan, apabila habis tempoh dua bulan dari tarikh penyampaian notis, atau sesuatu tempoh yang lebih lanjut (jika ada) sebagaimana yang diperintahkan oleh Mahkamah, memotong kaveat itu.</p>
            </div>

            <div class="row mt-5">
                <div class="col-7">
                    <p>Bertarikh {{ \Carbon\Carbon::parse($data->tarikh_notis)->translatedFormat('d F Y') }}</p>
                    <p class="small text-muted">[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-5 text-center">
                    <br><br>
                    <p class="fw-bold mb-0 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0 small">Penolong Pentadbir Tanah Daerah</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="fw-bold text-center mb-2">JADUAL TANAH DAN KEPENTINGAN</h6>
                <table class="table-jadual w-100 text-center">
                    <thead>
                        <tr>
                            <th width="35%">
                                @if($data->jenis_kawasan == 'Bandar') Bandar @else <s>Bandar</s> @endif / 
                                @if($data->jenis_kawasan == 'Pekan') Pekan @else <s>Pekan</s> @endif / 
                                @if($data->jenis_kawasan == 'Mukim') Mukim @else <s>Mukim</s> @endif
                            </th>
                            <th width="30%">
                                No. 
                                @if($data->jenis_lot == 'Lot') Lot @else <s>Lot</s> @endif / 
                                @if($data->jenis_lot == 'Petak') Petak @else <s>Petak</s> @endif / 
                                @if($data->jenis_lot == 'PT') P.T @else <s>P.T</s> @endif /
                                @if($data->jenis_lot == 'Plot') Plot @else <s>Plot</s> @endif
                            </th>
                            <th width="35%">Perihal dan No. Hakmilik</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $data->nama_kawasan }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= PAGE 2: FORM 19C (BI) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0 text-uppercase">National Land Code</h5>
                <p class="mb-0">[Act 828]</p>
                <p class="mb-0">Form 19C</p>
                <p class="mb-0">(Section 326)</p>
                <h5 class="fw-bold mt-4 text-uppercase">NOTICE OF INTENDED REMOVAL OF CAVEAT</h5>
            </div>

            <div class="content-body text-justify">
                <p>To <strong>{{ strtoupper($data->nama_penerima) }}</strong> IC NO: <strong>{{ $data->ic_penerima }}</strong> of <strong>{{ $data->alamat_penerima }}</strong> at whose instance Private Caveat Registered No. <strong>{{ $data->no_perserahan_kaveat }}</strong> is entered upon the title to the land described in the schedule below.</p>
                
                <p class="mt-3">Whereas the above caveat is expressed to bind the whole of the land/the undivided share in the land/ the particular interest described in the said schedule;</p>

                <p class="mt-3">And whereas <strong>{{ strtoupper($data->nama_pemohon) }}</strong> has applied to me for the removal of the said caveat;</p>
                
                <p class="mt-3">Take notice that, in exercise of the powers conferred by section 326 of the National Land Code, I shall, at the expiry of the period of two months from the date of service of this notice, or of such further period (if any) as the Court may order, remove the caveat.</p>
            </div>

            <div class="row mt-5">
                <div class="col-7">
                    <p>Dated {{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d F Y') }}</p>
                    <p class="small text-muted">[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-5 text-center">
                    <br><br>
                    <p class="fw-bold mb-0 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0 small">Assistant District Land Administrator</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="fw-bold text-center mb-2">SCHEDULE OF LAND AND INTEREST</h6>
                <table class="table-jadual w-100 text-center">
                    <thead>
                        <tr>
                            <th width="35%">
                                @if($data->jenis_kawasan == 'Bandar') Town @else <s>Town</s> @endif / 
                                @if($data->jenis_kawasan == 'Pekan') Village @else <s>Village</s> @endif / 
                                @if($data->jenis_kawasan == 'Mukim') Mukim @else <s>Mukim</s> @endif
                            </th>
                            <th width="30%">
                                No. 
                                @if($data->jenis_lot == 'Lot') Lot @else <s>Lot</s> @endif / 
                                @if($data->jenis_lot == 'Plot') Plot @else <s>Plot</s> @endif / 
                                @if($data->jenis_lot == 'Petak') Parcel @else <s>Parcel</s> @endif / 
                                @if($data->jenis_lot == 'PT') L.O.No @else <s>L.O.No</s> @endif
                            </th>
                            <th width="35%">Description and No. of Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $data->nama_kawasan }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</td>
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