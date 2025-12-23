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
        padding: 20mm 15mm; /* Margin selamat */
        box-shadow: 0 0 15px rgba(0,0,0,0.15);
        font-family: 'Times New Roman', serif;
        line-height: 1.4;
        font-size: 11pt;
        color: black;
        position: relative;
    }

    .text-justify { text-align: justify; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: bold; }
    .text-uppercase { text-transform: uppercase; }
    .indent { text-indent: 50px; }
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

    /* Style Garis Palang (Diagonal) */
    .row-palang-penuh td { 
        padding: 0 !important; 
        height: 40px; 
        position: relative; 
    }
    .diagonal-box { 
        position: relative; 
        width: 100%; 
        height: 100%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }
    .diagonal-line { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        pointer-events: none; 
        z-index: 1; 
    }
    .text-label { 
        position: relative; 
        z-index: 2; 
        font-weight: bold; 
        font-style: italic;
        background-color: rgba(255,255,255,0.7); /* Background putih sikit supaya tulisan jelas */
        padding: 2px 5px;
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
            padding: 20mm 15mm; 
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
            <a href="{{ route('permohonan.seksyen261') }}" class="btn btn-secondary shadow-sm">
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
        
        {{-- ================= PAGE 1: BORANG 16G (BM) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">Kanun Tanah Negara</h5>
                <p class="mb-0">[Akta 828]</p>
                <p class="mb-0">(Seksyen 261)</p>
                <h5 class="fw-bold mt-3 text-uppercase">SAMAN KEPADA PENGGADAI SUPAYA HADIR DALAM SIASATAN</h5>
            </div>

            <div class="content-body text-justify">
                <p class="mb-0">Kepada: <strong>{{ strtoupper($data->nama_penggadai) }}</strong></p>
                <p>yang beralamat di {{ $data->alamat_penggadai }}, Penggadai dalam gadaian yang disebut di dalam Jadual bagi tanah yang tersebut di dalamnya.</p>
                
                <p class="indent mt-4">Bahawasanya saya telah menerima daripada pemegang gadai dalam gadaian tersebut, suatu permohonan supaya perintah jual dikeluarkan di bawah subseksyen 260(2) Kanun Tanah Negara.</p>

                <p class="indent">Dan bahawasanya saya bercadang hendak mengadakan suatu siasatan berkenaan dengan permohonan itu di <strong>{{ $data->tempat_siasatan }}</strong> pada <strong>{{ \Carbon\Carbon::parse($data->tarikh_siasatan)->translatedFormat('d F Y') }}</strong> jam <strong>{{ \Carbon\Carbon::parse($data->masa_siasatan)->format('h:i') }} pagi</strong>.</p>

                <p class="indent">Oleh itu, menurut perenggan 261(1)(c) Kanun Tanah Negara, tuan/puan adalah dengan ini dikehendaki hadir dalam siasatan tersebut dan menunjukkan sebab mengapa perintah tersebut tidak patut dibuat.</p>
            </div>

            <div class="row mt-5">
                <div class="col-7">
                    <p class="mb-0 text-uppercase">Bertarikh: {{ \Carbon\Carbon::parse($data->tarikh_notis)->translatedFormat('d F Y') }}</p>
                    <p class="mb-0">{{ $data->no_fail }}</p>
                </div>
                <div class="col-5 text-center">
                    <div style="height: 60px; border-bottom: 1px dotted black; width: 80%; margin: 0 auto;"></div>
                    <p class="fw-bold mb-0 mt-2 text-uppercase">({{ $data->nama_pentadbir }})</p>
                    <p class="mb-0 small">Penolong Pentadbir Tanah Daerah {{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="fw-bold text-center mb-3 text-uppercase">JADUAL TANAH DAN KEPENTINGAN</h6>
                <table class="table-jadual text-center">
                    <thead>
                        <tr>
                            <th width="15%">*Bandar/Pekan Mukim</th>
                            <th width="15%">No. *Lot/Petak/PT</th>
                            <th width="20%">Jenis dan No. Hakmilik</th>
                            <th width="15%">Bahagian tanah (jika ada)</th>
                            <th width="15%">No. Berdaftar *pajakan (jika ada)</th>
                            <th width="20%">No. Berdaftar Gadaian (jika ada)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $data->mukim }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</td>
                            <td>{{ $data->bahagian_tanah }}</td>
                            <td>-</td>
                            <td>{{ $data->no_daftar_gadaian }}</td>
                        </tr>
                        {{-- Garis Palang "1 Hakmilik Sahaja" --}}
                        <tr class="row-palang-penuh">
                            <td colspan="6">
                                <div class="diagonal-box">
                                    <span class="text-label">( 1 hakmilik sahaja )</span>
                                    <svg class="diagonal-line" preserveAspectRatio="none" viewBox="0 0 100 100">
                                        <line x1="0" y1="100" x2="100" y2="0" stroke="black" stroke-width="0.7" />
                                    </svg>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-2">
                    <p class="mb-0 small">Bertarikh: {{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d.m.Y') }}</p>
                    <p class="mb-0 small">{{ $data->no_fail }}</p>
                </div>
            </div>
        </div>

        {{-- ================= PAGE 2: FORM 16G (BI) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">National Land Code</h5>
                <p class="mb-0">[Act 828]</p>
                <p class="mb-0">(Section 261)</p>
                <h5 class="fw-bold mt-3 text-uppercase">SUMMON TO CHARGOR TO ATTEND AN ENQUIRY</h5>
            </div>

            <div class="content-body text-justify">
                <p class="mb-0">To: <strong>{{ strtoupper($data->nama_penggadai) }}</strong></p>
                <p>of {{ $data->alamat_penggadai }}, the Chargor of the charge stated in the Schedule over the land stated therein.</p>
                
                <p class="indent mt-4">Where as I have received from the chargee of said charge an application for an order for sale to be issued under subsection 260(2) of the National Land Code.</p>

                <p class="indent">And where as I intend to hold an enquiry with regards to the said application at <strong>{{ $data->tempat_siasatan }}</strong> on <strong>{{ \Carbon\Carbon::parse($data->tarikh_siasatan)->format('j F Y') }}</strong> at <strong>{{ \Carbon\Carbon::parse($data->masa_siasatan)->format('h:i') }} am</strong>.</p>

                <p class="indent">Therefore, pursuant to paragraph 261(1)(c) of National Land Code, you are required to attend the said enquiry and show cause why the said order ought not to be made.</p>
            </div>

            <div class="row mt-5">
                <div class="col-7">
                    <p class="mb-0">Dated: {{ \Carbon\Carbon::parse($data->tarikh_notis)->format('j F Y') }}</p>
                    <p class="mb-0">{{ $data->no_fail }}</p>
                </div>
                <div class="col-5 text-center">
                    <div style="height: 60px; border-bottom: 1px dotted black; width: 80%; margin: 0 auto;"></div>
                    <p class="fw-bold mb-0 mt-2 text-uppercase">({{ $data->nama_pentadbir }})</p>
                    <p class="mb-0 small">Assistant District Land Administrator</p>
                    <p class="fw-bold text-uppercase">{{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="fw-bold text-center mb-3 text-uppercase">SCHEDULE OF THE LAND AND INTEREST</h6>
                <table class="table-jadual text-center">
                    <thead>
                        <tr>
                            <th width="15%">*Town/Village/ Mukim</th>
                            <th width="15%">*Lot/Parce/L.O. No./No</th>
                            <th width="20%">Description and No. of Title</th>
                            <th width="15%">Share of Land (if any)</th>
                            <th width="15%">Registered No. * lease (if any)</th>
                            <th width="20%">Registered No. of Charge (if any)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $data->mukim }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</td>
                            <td>{{ $data->bahagian_tanah }}</td>
                            <td>-</td>
                            <td>{{ $data->no_daftar_gadaian }}</td>
                        </tr>
                        {{-- Garis Palang "1 title only" --}}
                        <tr class="row-palang-penuh">
                            <td colspan="6">
                                <div class="diagonal-box">
                                    <span class="text-label">( 1 title only )</span>
                                    <svg class="diagonal-line" preserveAspectRatio="none" viewBox="0 0 100 100">
                                        <line x1="0" y1="100" x2="100" y2="0" stroke="black" stroke-width="0.7" />
                                    </svg>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-2">
                    <p class="mb-0 small">Dated: {{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d.m.Y') }}</p>
                    <p class="mb-0 small">{{ $data->no_fail }}</p>
                </div>
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