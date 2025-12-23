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
        line-height: 1.6;
        font-size: 11.5pt;
        color: black;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
    }

    .content-body {
        flex: 1; /* Tolak footer ke bawah jika ada ruang */
    }

    .text-justify { text-align: justify; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .fw-bold { font-weight: bold; }
    .text-uppercase { text-transform: uppercase; }
    
    .perenggan-indent { padding-left: 25px; }
    .sub-indent { padding-left: 50px; }
    .small { font-size: 10pt; }

    /* Table Jadual */
    .table-jadual { 
        border-collapse: collapse; 
        border: 1.5px solid black; 
        width: 100%;
        margin-top: 10px;
        font-size: 11pt;
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

    /* Style Garis Palang (Diagonal) */
    .row-palang td { 
        padding: 0 !important; 
        height: 60px; 
        position: relative; 
    }
    .diagonal-container { 
        position: relative; 
        width: 100%; 
        height: 100%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }
    .line-svg { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
    }
    .diagonal-text { 
        z-index: 5; 
        font-weight: bold; 
        background: white; 
        padding: 0 10px;
        font-style: italic;
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
            height: 297mm; /* Paksa tinggi A4 penuh */
            margin: 0;
            padding: 25mm 25mm;
            box-shadow: none;
            border: none;
            page-break-after: always; 
            display: block; /* Reset display block */
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
            <a href="{{ route('permohonan.seksyen263') }}" class="btn btn-secondary shadow-sm">
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
                <i class="fas fa-print"></i> Cetak (4 Page)
            </button>
        @endif
    </div>

    {{-- CONTAINER KERTAS --}}
    <div class="print-container">
        
        {{-- ================= PAGE 1: PERINTAH JUALAN (BM) ================= --}}
        <div class="page">
            <div class="text-center mb-5">
                <h5 class="fw-bold mb-0 text-uppercase">Kanun Tanah Negara</h5>
                <p class="mb-0">Borang 16H</p>
                <p class="mb-0">Akta 828</p>
                <p class="mb-0">(Seksyen 263)</p>
                <h5 class="fw-bold mt-4 text-uppercase">Perintah Jualan Atas Permintaan Pemegang Gadaian</h5>
            </div>

            <div class="content-body text-justify">
                <p>Saya, <strong>{{ $data->nama_pentadbir_tanah }}</strong>, Penolong Pentadbir Tanah <strong>{{ $data->daerah }}</strong> pada menjalankan kuasa yang diberi oleh Seksyen 263 Kanun Tanah Negara dengan ini memerintahkan supaya dijual tanah yang diperihalkan dalam jadual di bawah ini :</p>
                
                <p class="mt-3">Dan saya selanjutnya memerintahkan-</p>
                <div class="perenggan-indent">
                    <p>(a) bahawa jualan itu hendaklah secara lelongan awam, yang akan diadakan pada <strong>{{ \Carbon\Carbon::parse($data->tarikh_lelongan)->translatedFormat('d F Y') }} ({{ $data->hari_lelongan }}) jam {{ \Carbon\Carbon::parse($data->masa_lelongan)->format('h.i') }} pagi/petang</strong> di <strong>{{ $data->tempat_lelongan }}</strong>; dan</p>
                    <p>(b) bahawa harga rizab bagi maksud jualan itu hendaklah <strong>RM{{ number_format($data->harga_rizab, 2) }}</strong>.</p>
                </div>

                <p class="mt-3">2. Saya dapati bahawa amaun yang kena dibayar kepada pemegang gadaian pada tarikh ini ialah <strong>RM{{ number_format($data->amaun_hutang, 2) }}</strong>. Nama pemegang gadaian ialah <strong>{{ $data->nama_pemegang_gadai }}</strong>.</p>
                
                <p class="mt-3">3. Jualan ini hendaklah tertakluk kepada syarat-syarat berikut:</p>
                <div class="perenggan-indent">
                    <p>(a) penawar memiliki, jumlah yang bersamaan dengan sepuluh peratus (10%) daripada harga rizab yang dinyatakan di bawah perenggan 1 (b) di atas: <strong>RM{{ number_format($data->deposit_sepuluh_peratus, 2) }}</strong>;</p>
                    <p>(b) amaun penuh harga jualan boleh dibayar serta-merta selepas tukul dijatuhkan oleh penawar yang berjaya kepada pemegang gadaian;</p>
                    <p>(c) di mana amaun penuh harga jualan tidak dibayar selepas tukul dijatuhkan oleh penawar yang berjaya-</p>
                    <div class="sub-indent">
                        <p>(i) maka jumlah wang dinyatakan dalam perenggan 3(a) hendaklah dibayar kepada pemegang gadaian dan ia hendaklah dipegang sebagai deposit sehingga keseluruhan harga belian telah dibayar; dan</p>
                        <p>(ii) sebelum baki harga belian diselesaikan, jumlah wang yang dinyatakan dalam subperenggan (i) hendaklah dikreditkan ke dalam akaun penggadai untuk tujuan mengurangkan jumlah wang yang kena dibayar kepada pemegang gadaian;</p>
                    </div>
                    <p>(d) baki harga belian hendaklah diselesaikan dalam masa tidak lebih daripada satu ratus dua puluh (120) hari dari tarikh jualan, iaitu, tidak lewat dari tarikh <strong>{{ \Carbon\Carbon::parse($data->tarikh_akhir_bayaran)->translatedFormat('d F Y') }}</strong> dan bahawa tiada apa-apa lanjutan masa bagi tempoh yang telah dinyatakan; dan</p>
                    <p>(e) di mana harga belian sepenuhnya tidak diselesaikan sebelum atau pada tarikh yang ditentukan dalam perenggan (d), jumlah wang yang dibayar sebagai deposit di bawah perenggan (c) kepada pemegang gadaian hendaklah dilucuthak dan dilupuskan dengan cara yang dinyatakan di bawah seksyen 267.</p>
                </div>
            </div>
            
            <div class="mt-auto text-right">
                <p class="small text-muted font-italic">(Sambungan di Halaman Sebelah...)</p>
            </div>
        </div>

        {{-- ================= PAGE 2: JADUAL (BM) ================= --}}
        <div class="page">
            <div class="content-body" style="padding-top: 20px;">
                <div class="row mt-5">
                    <div class="col-7 pt-4">
                        <p>Bertarikh : {{ \Carbon\Carbon::parse($data->tarikh_perintah)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="col-5 text-center">
                        <br><br>
                        <p>......................................................</p>
                        <p class="fw-bold mb-0">({{ strtoupper($data->nama_pentadbir_tanah) }})</p>
                        <p class="mb-0 small">Penolong Pentadbir Tanah Daerah</p>
                    </div>
                </div>

                <div class="mt-5">
                    <h6 class="fw-bold text-center mb-3 text-uppercase">Jadual Tanah * dan Kepentingan</h6>
                    <table class="table-jadual text-center">
                        <thead>
                            <tr>
                                <th>*Bandar/Pekan Mukim</th>
                                <th>No. *Lot/ Petak/PT</th>
                                <th>Perihal dan No. Hakmilik</th>
                                <th>Bahagian Tanah (Jika ada)</th>
                                <th>No. Berdaftar *pajakan/ pajakan kecil (jika ada)</th>
                                <th>No. Berdaftar Gadaian (jika ada)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $data->mukim }}</td>
                                <td>{{ $data->no_lot }}</td>
                                <td>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</td>
                                <td>{{ $data->bahagian_tanah }}</td>
                                <td>{{ $data->pajakan ?? '-' }}</td>
                                <td>{{ $data->no_daftar_gadaian }}</td>
                            </tr>
                            <tr class="row-palang">
                                <td colspan="6">
                                    <div class="diagonal-container">
                                        <span class="diagonal-text">( 1 hakmilik sahaja )</span>
                                        <svg class="line-svg" preserveAspectRatio="none" viewBox="0 0 100 100">
                                            <line x1="0" y1="100" x2="100" y2="0" stroke="black" stroke-width="0.5"/>
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <p class="mb-0 small">No Fail : {{ $data->no_fail }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= PAGE 3: ORDER FOR SALE (BI) ================= --}}
        <div class="page">
            <div class="text-center mb-5">
                <h5 class="fw-bold mb-0 text-uppercase">National Land Code</h5>
                <p class="mb-0">Form 16H</p>
                <p class="mb-0">Act 828</p>
                <p class="mb-0">(Section 263)</p>
                <h5 class="fw-bold mt-4 text-uppercase">Order For Sale At Instance Of Chargee</h5>
            </div>

            <div class="content-body text-justify">
                <p>I, <strong>{{ $data->nama_pentadbir_tanah }}</strong>, Assistant Land Administrator of <strong>{{ $data->daerah }}</strong> in exercise of the powers conferred by section 263 of the National Land Code, hereby order the sale of the land described in the schedule below ;</p>
                
                <p class="mt-3">And i further order-</p>
                <div class="perenggan-indent">
                    <p>(a) that the sale shall be by public auction, to be held on the <strong>{{ \Carbon\Carbon::parse($data->tarikh_lelongan)->format('j F Y') }} ({{ $data->hari_lelongan }}) at {{ \Carbon\Carbon::parse($data->masa_lelongan)->format('h.i A') }}</strong> in the premises of <strong>{{ $data->tempat_lelongan }}</strong>; and</p>
                    <p>(b) that the reserve price for the purpose of the sale shall be <strong>RM{{ number_format($data->harga_rizab, 2) }}</strong>.</p>
                </div>

                <p class="mt-3">2. I find that the amount due to the chargee at this date is <strong>RM{{ number_format($data->amaun_hutang, 2) }}</strong>. Name of chargee is <strong>{{ $data->nama_pemegang_gadai }}</strong>.</p>
                
                <p class="mt-3">3. The sale shall be subject to the following conditions:</p>
                <div class="perenggan-indent">
                    <p>(a) the bidder possesses, the sum equivalent to ten per centum (10%) of the reserve price specified under paragraph 1(b) above: <strong>RM{{ number_format($data->deposit_sepuluh_peratus, 2) }}</strong>;</p>
                    <p>(b) the full amount of the purchase price may be paid immediately after the fall of the hammer by the successful bidder to the chargee;</p>
                    <p>(c) where the full amount of the purchase price is not paid after the fall of the hammer by the successful bidder-</p>
                    <div class="sub-indent">
                        <p>(i) then, the sum specified in paragraph 3(a) shall be paid to the chargee and shall be retained as a deposit until the full purchase price has been paid;</p>
                        <p>(ii) and pending the settlement of the balance of the purchase price, the sum specified in subparagraph (i) shall be credited into the account of the chargor for the purpose of reducing the amount due to the chargee;</p>
                    </div>
                    <p>(d) the balance of the purchase price shall be settled within a date not later than one hundred and twenty (120) days from the date of the sale, that is, not later than the <strong>{{ \Carbon\Carbon::parse($data->tarikh_akhir_bayaran)->format('j F Y') }}</strong> and there shall be no extension of the period so specified;</p>
                    <p>(e) and where the full purchase price is not settled on or by the date specified in paragraph (d), the sum paid as deposit under paragraph (c) to the chargee shall be forfeited and disposed of in manner specified under section 267.</p>
                </div>
            </div>
            
            <div class="mt-auto text-right">
                <p class="small text-muted font-italic">(Continued on next page...)</p>
            </div>
        </div>

        {{-- ================= PAGE 4: SCHEDULE (BI) ================= --}}
        <div class="page">
            <div class="content-body" style="padding-top: 20px;">
                <div class="row mt-5">
                    <div class="col-7 pt-4">
                        <p>Dated : {{ \Carbon\Carbon::parse($data->tarikh_perintah)->format('j F Y') }}</p>
                    </div>
                    <div class="col-5 text-center">
                        <br><br>
                        <p>......................................................</p>
                        <p class="fw-bold mb-0">({{ strtoupper($data->nama_pentadbir_tanah) }})</p>
                        <p class="mb-0 small">Assistant District Land Administrator</p>
                    </div>
                </div>

                <div class="mt-5">
                    <h6 class="fw-bold text-center mb-3 text-uppercase">Schedule of Land * and Interest</h6>
                    <table class="table-jadual text-center">
                        <thead>
                            <tr>
                                <th>*Town/Village/ Mukim</th>
                                <th>No. *Lot/ Parcel/ L.O. No</th>
                                <th>Description and No.of Title</th>
                                <th>Share of Land (if any)</th>
                                <th>Registered No. * lease / sub-lease (if any)</th>
                                <th>Registered No. of Charge (if any)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $data->mukim }}</td>
                                <td>{{ $data->no_lot }}</td>
                                <td>{{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}</td>
                                <td>{{ $data->bahagian_tanah }}</td>
                                <td>{{ $data->pajakan_en ?? '-' }}</td>
                                <td>{{ $data->no_daftar_gadaian }}</td>
                            </tr>
                            <tr class="row-palang">
                                <td colspan="6">
                                    <div class="diagonal-container">
                                        <span class="diagonal-text">( 1 title only )</span>
                                        <svg class="line-svg" preserveAspectRatio="none" viewBox="0 0 100 100">
                                            <line x1="0" y1="100" x2="100" y2="0" stroke="black" stroke-width="0.5"/>
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <p class="mb-0 small">File No : {{ $data->no_fail }}</p>
                    </div>
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