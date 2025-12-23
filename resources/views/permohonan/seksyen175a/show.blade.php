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
        /* SAYA KURANGKAN PADDING SUPAYA LEBIH MUAT */
        padding: 15mm 20mm; 
        box-shadow: 0 0 15px rgba(0,0,0,0.15);
        font-family: 'Times New Roman', serif;
        line-height: 1.3; /* Rapatkan sikit baris */
        font-size: 11.5pt; /* Kecilkan sikit font standard */
        color: black;
        position: relative;
    }

    .text-justify { text-align: justify; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: bold; }
    .text-uppercase { text-transform: uppercase; }
    .indent { text-indent: 40px; }
    .small { font-size: 10pt; }

    /* Table Jadual - Fix supaya tak lari */
    .table-jadual { 
        border-collapse: collapse; 
        border: 1.5px solid black; 
        width: 100%;
        margin-top: 5px;
        page-break-inside: avoid; /* Jangan bagi putus tengah jalan */
    }
    .table-jadual td { 
        border: 1px solid black; 
        padding: 6px 10px; /* Kurangkan padding dalam cell */
        vertical-align: top; 
    }

    /* Content Body Spacing */
    .content-body p {
        margin-bottom: 10px; /* Kurangkan jarak antar perenggan */
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
            padding: 15mm 20mm; /* Sama macam preview */
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
            <a href="{{ route('permohonan.seksyen175a') }}" class="btn btn-secondary shadow-sm">
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
        
        {{-- ================= PAGE 1: BORANG 10E (BM) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">Kanun Tanah Negara</h5>
                <p class="mb-0 small">[Akta 828]</p>
                <h5 class="fw-bold mb-0">Borang 10E</h5>
                <p class="mb-0 small">(Seksyen 175A)</p>
                <h5 class="fw-bold mt-3 text-uppercase">NOTIS BERHUBUNGAN DENGAN PENYEDIAAN SUATU DOKUMEN HAKMILIK DAFTARAN SEMENTARA</h5>
            </div>

            <div class="content-body text-justify">
                <p class="indent">Pada menjalankan kuasa-kuasa yang diberi oleh Bab 4 Bahagian Sepuluh Kanun Tanah Negara, Notis adalah dengan ini diberi bahawa adalah dicadangkan untuk menyediakan suatu dokumen hakmilik daftaran sementara berhubungan dengan tanah yang diperihalkan dalam Jadual di bawah ini kerana sebab-sebab berikut:-</p>
                
                <p class="text-center fw-bold my-3 fst-italic">"{{ $data->sebab_penyediaan }}"</p>

                <p class="indent">Selepas penyiaran notis ini dalam Warta, tiada Pendaftar atau Pentadbir Tanah boleh menerima untuk pendaftaran apa-apa instrumen urus niaga yang menyentuh tanah itu, atau memasukkan apa-apa kaveat persendirian di bawah seksyen 322 atau apa-apa kaveat pemegang lien di bawah seksyen 330 berkenaan dengannya, sehingga pemasukan itu dalam dokumen hakmilik daftaran sementara telah disahkan di bawah seksyen 175F.</p>

                <p class="indent">Mana-mana orang atau badan yang mempunyai kepentingan dalam tanah tersebut boleh memohon dalam tempoh tiga bulan penyiaran notis ini dalam Warta kepada Pentadbir Tanah dalam Borang 10F bahawa nama *tuan punya/mana-mana orang yang mempunyai kepentingan berdaftar atau kepentingan yang boleh didaftarkan dimasukkan dalam dokumen hakmilik daftaran sementara.</p>

                <p class="indent">Mana-mana orang atau badan yang ada memiliki dokumen hakmilik keluaran hendaklah, dalam tempoh tiga bulan tersebut, menghantarnya kepada Pentadbir Tanah.</p>
            </div>

            {{-- RUANGAN TANDATANGAN --}}
            <div class="row mt-4">
                <div class="col-7">
                    <p class="mb-0">Bertarikh: {{ \Carbon\Carbon::parse($data->tarikh_notis)->translatedFormat('d F Y') }}</p>
                    <p class="small">[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-5 text-center">
                    <div style="height: 60px; border-bottom: 1px dotted black; width: 90%; margin: 0 auto;"></div>
                    <p class="fw-bold mb-0 mt-2 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0 small">Penolong Pentadbir Tanah Daerah</p>
                    <p class="fw-bold small">{{ $data->daerah }}</p>
                </div>
            </div>

            {{-- JADUAL (Pastikan 'page-break-inside: avoid' dalam CSS berfungsi) --}}
            <div class="mt-4">
                <h6 class="fw-bold text-center mb-2 text-uppercase">JADUAL</h6>
                <table class="table-jadual">
                    <tr>
                        <td colspan="2">Daerah - <strong>{{ $data->daerah }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">Bandar/ Pekan/ Mukim - <strong>{{ $data->mukim }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">Perihal dan No. Hakmilik - <strong>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</strong></td>
                    </tr>
                    <tr>
                        <td width="50%">No. Lot - <strong>{{ $data->no_lot }}</strong></td>
                        <td width="50%">Luas - <strong>{{ $data->luas }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ================= PAGE 2: FORM 10E (BI) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">National Land Code</h5>
                <p class="mb-0 small">[Act 828]</p>
                <h5 class="fw-bold mb-0">Form 10E</h5>
                <p class="mb-0 small">(Section 175A)</p>
                <h5 class="fw-bold mt-3 text-uppercase">NOTICE RELATING TO THE PREPARATION OF A PROVISIONAL REGISTER DOCUMENT OF TITLE</h5>
            </div>

            <div class="content-body text-justify">
                <p class="indent">In exercise of the powers conferred by Chapter 4 of Part Ten of the National Land Code, Notice is hereby given that it is proposed to prepare a provisional register document of title relating to the land described in the Schedule below for the following reason-</p>
                
                {{-- LOGIK TRANSLATE SEBAB PENYEDIAAN --}}
                <p class="text-center fw-bold my-3 fst-italic">
                    @if(str_contains(strtolower($data->sebab_penyediaan), 'hilang'))
                        "The original document registered title has been lost."
                    @elseif(str_contains(strtolower($data->sebab_penyediaan), 'rosak'))
                        "The original document registered title has been damaged."
                    @elseif(str_contains(strtolower($data->sebab_penyediaan), 'musnah'))
                         "The original document registered title has been partially destroyed."
                    @else
                        "{{ $data->sebab_penyediaan }}"
                    @endif
                </p>

                <p class="indent">Upon publication in the Gazette of this notice, no Registrar or Land Administrator shall accept for registration any instrument of dealing affecting the land, or enter any private caveat under section 322 or any lienholders caveat under section 330 in respect thereof, until the entry in the provisional register document of title has been authenticated under section 175F.</p>

                <p class="indent">Any person or body having interest in the said land may apply within three months of the publication in the Gazette of this notice to the Land Administrator in Form 10F that the name of the proprietor/any person having registered or registrable interest be entered in the provisional register document of title.</p>

                <p class="indent">Any person or body who is possession of the issue document of title thereto shall within the said three months, deliver it to the Land Administrator.</p>
            </div>

            <div class="row mt-4">
                <div class="col-7">
                    <p class="mb-0">Dated : {{ \Carbon\Carbon::parse($data->tarikh_notis)->format('j F Y') }}</p>
                    <p class="small">[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-5 text-center">
                    <div style="height: 60px; border-bottom: 1px dotted black; width: 90%; margin: 0 auto;"></div>
                    <p class="fw-bold mb-0 mt-2 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0 small">Assistant District Land Administrator</p>
                    <p class="fw-bold small">{{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-4">
                <h6 class="fw-bold text-center mb-2 text-uppercase">SCHEDULE</h6>
                <table class="table-jadual">
                    <tr>
                        <td colspan="2">District - <strong>{{ $data->daerah }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">Town/Village/Mukim - <strong>{{ $data->mukim }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">Description and No. of Title - <strong>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</strong></td>
                    </tr>
                    <tr>
                        <td width="50%">Lot No. - <strong>{{ $data->no_lot }}</strong></td>
                        <td width="50%">Area - <strong>{{ $data->luas }}</strong></td>
                    </tr>
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