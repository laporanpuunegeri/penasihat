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
        width: 210mm;       
        min-height: 297mm;  /* Di skrin, kita nak nampak dia panjang macam A4 */
        padding: 20mm 20mm; 
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

    /* Table Custom */
    .table-custom { 
        border-collapse: collapse; 
        border: 1.2px solid black; 
        font-size: 10pt; 
        width: 100%;
    }
    .table-custom th, .table-custom td { 
        border: 1px solid black; 
        padding: 5px; 
        vertical-align: middle;
    }
    .small-ref td { 
        font-size: 8.5pt; 
        height: 20px; 
    }

    /* =========================================
       STYLE KHAS UNTUK PRINT / PDF (YANG SAYA UBAH)
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
            /* PENTING: Tukar min-height jadi auto supaya tak ada page kosong terselit */
            height: auto; 
            min-height: 0; 
            
            margin: 0;
            padding: 20mm 20mm;
            box-shadow: none;
            
            /* Ini yang memaksa page baru setiap kali habis satu div .page */
            page-break-after: always; 
            break-after: page;
            
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
            <a href="{{ route('permohonan.seksyen97') }}" class="btn btn-secondary shadow-sm">
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
                <i class="fas fa-print"></i> Cetak Semua
            </button>
        @endif
    </div>

    {{-- CONTAINER KERTAS --}}
    <div class="print-container">

        {{-- ================= PAGE 1: BORANG 6A (BM) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">Kanun Tanah Negara</h5>
                <p class="mb-0">[Akta 828]</p>
                <h5 class="fw-bold mb-0">Borang 6A</h5>
                <p class="mb-0">[Seksyen 97 dan 98]</p>
                <h5 class="fw-bold mt-3">NOTIS TUNTUTAN: TUNGGAKAN SEWA</h5>
            </div>

            <div class="mb-4">
                <p class="mb-1">Kepada <strong>{{ $data->nama_pemilik }} (K/P: {{ $data->no_kp_pemilik }})</strong></p>
                <p class="mb-3">beralamat <strong>{{ $data->alamat_pemilik }}, {{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}, MUKIM {{ $data->mukim }}, DAERAH {{ $data->daerah }}</strong> tuan punya tanah yang diperihalkan dalam ruangan pertama dan kedua dalam Jadual di bawah ini.</p>
            </div>

            <div class="text-justify mb-4">
                <p class="indent">Bahawasanya sewa yang dikenakan bagi tanah tersebut dan yang kena dibayar bagi tahun ini masih belum dibayar dan, mulai dari 1hb Jun, adalah dalam tunggakan.</p>
                <p class="indent">Kamu adalah dengan ini dikehendaki, dalam tempoh tiga bulan dari tarikh notis ini disampaikan, supaya membayar di <strong>Pejabat Daerah dan Tanah {{ $data->daerah }}</strong> segala jumlah wang yang sekarang ini kena dibayar sebagaimana yang tercatat dalam ruangan ketiga hingga enam dalam Jadual itu dan yang dijumlahkan dalam ruangan akhirnya.</p>
                <p class="indent">Dan ambil perhatian bahawa, jika jumlah yang dinyatakan dalam ruangan akhir itu tidak dibayar dengan sepenuhnya dalam tempoh tiga bulan tersebut, maka saya yang bertandatangan di bawah ini, menurut kuasa-kuasa yang diberi oleh seksyen 100 Kanun Tanah Negara, akan mengisytiharkan dengan perintah tanah berkenaan dilucuthak kepada Pihak Berkuasa Negeri.</p>
            </div>

            <div class="row mt-5">
                <div class="col-7">
                    <p>Bertarikh pada <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d') }}</strong> haribulan <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->translatedFormat('F, Y') }}</strong></p>
                    <p>[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-5 text-center mt-3">
                    <p class="fw-bold mb-0 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0">Pentadbir Tanah</p>
                    <p>Daerah {{ $data->daerah }}</p>
                </div>
            </div>

            <div class="mt-4">
                <h6 class="text-center fw-bold mb-2 small text-uppercase">JADUAL TANAH DAN TUNGGAKAN</h6>
                <table class="table-custom text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:20%">Perihal dan No. Hakmilik</th>
                            <th rowspan="2" style="width:10%">No. Lot/ P.T.</th>
                            <th>Sewa tahun ini</th>
                            <th>Tunggakan dari tahun-tahun lepas</th>
                            <th colspan="2">Fi-fi, dll yang dikenakan sebagai sewa</th>
                            <th rowspan="2">Jumlah kena dibayar</th>
                        </tr>
                        <tr>
                            <th>RM</th>
                            <th>RM</th>
                            <th>Denda (RM)</th>
                            <th>Notis (RM)</th>
                        </tr>
                        <tr class="small-ref">
                            <td>(1)</td><td>(2)</td><td>(3)</td><td>(4)</td><td>(5)</td><td>(6)</td><td>(7)</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-3">{{ $data->jenis_hakmilik }}<br>{{ $data->no_hakmilik }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ number_format($data->sewa_tahun_semasa, 2) }}</td>
                            <td>{{ number_format($data->jumlah_tunggakan, 2) }}</td>
                            <td>{{ number_format($data->denda, 2) }}</td>
                            <td>{{ number_format($data->kos_notis, 2) }}</td>
                            <td class="fw-bold">{{ number_format($data->jumlah_besar, 2) }}</td>
                        </tr>
                        <tr><td colspan="7" class="fw-bold text-start ps-2">MUKIM {{ $data->mukim }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= PAGE 2: TAMBAHAN ================= --}}
        <div class="page">
            <h5 class="fw-bold text-uppercase text-center mb-4">TAMBAHAN</h5>
            <div class="mb-4">
                <p>Kepada <strong>{{ $data->nama_bank ?? '..........................................' }}</strong> yang beralamat di <strong>{{ $data->alamat_bank ?? '..........................................' }}</strong>. Pemegang gadaian.</p>
            </div>
            <div class="text-justify">
                <p class="indent">Sekiranya kamu ada sebab mempercayai bahawa tuanpunya bagi tanah yang dijadualkan di atas dalam mana kamu mempunyai atau menuntut sesuatu kepentingan akan mungkir daripada membayar wang-wang yang sekarang ini ditetapkan sebagai kena dibayar, maka kamu boleh mengelakkan perlucuthakan tanah tersebut dengan membayar sepenuhnya kepada Pentadbir Tanah, dalam tempoh yang dinyatakan, jumlah yang dinyatakan berkenaan dengan tanah itu.</p>
                <p class="indent">Dan ambil perhatian bahawa (tanpa menjejaskan apa-apa hak di bawah seksyen itu untuk mendakwa tuanpunya itu secara terus), hak-hak istimewa yang berikut untuk mendapat balik bayaran-bayaran wujud menurut kuasa peruntukan-peruntukan seksyen 98 Kanun Tanah Negara–</p>
                <div class="ps-4 mt-2">
                    <p>(a) apa-apa jumlah wang yang dibayar oleh pemegang gadaian hendaklah ditambahkan kepada bayaran yang pertama yang kemudiannya kena dibayar atas gadaian itu;</p>
                    <p>(b) apa-apa jumlah wang yang dibayar oleh pemajak, pemajak kecil ataupun tenan boleh didapatkan balik dengan memotong amaun itu daripada apa-apa sewa yang pada masa itu atau kemudiannya kena dibayar olehnya kepada tuanpunya tanah itu atau kepada sesiapa yang memegang tanah itu;</p>
                    <p>(c) mana-mana pemajak, pemajak kecil atau tenan yang menanggung apa-apa tanggungan tambahan atau dialami apa-apa potongan di bawah seksyen itu boleh mendapat balik amaun yang ditanggung atau dipotong itu dengan membuat potongan yang bersamaan daripada amaun sewa yang kena dibayar olehnya.</p>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-7 pt-4">
                    <p>Bertarikh pada <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d') }}</strong> haribulan <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->translatedFormat('F, Y') }}</strong></p>
                </div>
                <div class="col-5 text-center">
                    <p class="fw-bold mb-0 mt-4 text-uppercase">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0">Pentadbir Tanah</p>
                    <p>Daerah {{ $data->daerah }}</p>
                </div>
            </div>
        </div>

        {{-- ================= PAGE 3: FORM 6A (BI) ================= --}}
        <div class="page">
            <div class="text-center mb-4">
                <h5 class="fw-bold mb-0">National Land Code</h5>
                <p class="mb-0 small">[Act 828]</p>
                <h5 class="fw-bold mb-0">Form 6A</h5>
                <p class="mb-0 small">[Sections 97 and 98]</p>
                <h5 class="fw-bold mt-3">NOTICE OF DEMAND: ARREARS OF RENT</h5>
            </div>
            <div class="mb-4">
                <p>To <strong>{{ $data->nama_pemilik }} (I/C: {{ $data->no_kp_pemilik }})</strong>, in address <strong>{{ $data->alamat_pemilik }}, {{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }}, MUKIM {{ $data->mukim }}, DAERAH {{ $data->daerah }}</strong>, proprietor of the land described in the 1st and 2nd columns of the Schedule below.</p>
            </div>
            <div class="text-justify mb-4">
                <p class="indent">Whereas the rent reserved on the said land and due in respect of the current year is unpaid and, with effect from the 1 June, in arrear.</p>
                <p class="indent">You are hereby required, within three months of the date of the service of this notice, to pay at <strong>Pejabat Daerah dan Tanah {{ $data->daerah }}</strong> all the sums now due as entered in the 3rd and 6th columns of the Schedule and totalled in the final column thereof.</p>
                <p class="indent">And take notice that, if the total specified in the final column is not paid in full within the said period of three months, then I the undersigned, by virtue of the powers conferred by section 100 of the National Land Code, shall by order declare the land in question forfeit to the State Authority.</p>
            </div>
            <div class="row mt-4">
                <div class="col-7">
                    <p>Dated on <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('d') }}</strong> of <strong>{{ \Carbon\Carbon::parse($data->tarikh_notis)->format('F, Y') }}</strong></p>
                    <p>[{{ $data->no_fail }}]</p>
                </div>
                <div class="col-5 text-center mt-3 text-uppercase">
                    <p class="fw-bold mb-0">{{ $data->nama_pentadbir }}</p>
                    <p class="mb-0">Land Administrator</p>
                    <p>{{ $data->daerah }}</p>
                </div>
            </div>
            <div class="mt-4">
                <h6 class="text-center fw-bold mb-2 small text-uppercase">SCHEDULE OF LAND AND ARREARS</h6>
                <table class="table-custom text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:20%">Description No. of Title</th>
                            <th rowspan="2" style="width:10%">Lot/L.O No.</th>
                            <th>Current Year’s Rent</th>
                            <th>Arrears from previous years</th>
                            <th colspan="2">Fees, etc., chargeable as rent</th>
                            <th rowspan="2">Total due</th>
                        </tr>
                        <tr>
                            <th>RM</th>
                            <th>RM</th>
                            <th>Of Fine</th>
                            <th>Notice</th>
                        </tr>
                        <tr class="small-ref">
                            <td>(1)</td><td>(2)</td><td>(3)</td><td>(4)</td><td>(5)</td><td>(6)</td><td>(7)</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-3 text-uppercase">{{ $data->jenis_hakmilik }}<br>{{ $data->no_hakmilik }}</td>
                            <td>{{ $data->no_lot }}</td>
                            <td>{{ number_format($data->sewa_tahun_semasa, 2) }}</td>
                            <td>{{ number_format($data->jumlah_tunggakan, 2) }}</td>
                            <td>{{ number_format($data->denda, 2) }}</td>
                            <td>{{ number_format($data->kos_notis, 2) }}</td>
                            <td class="fw-bold">{{ number_format($data->jumlah_besar, 2) }}</td>
                        </tr>
                        <tr><td colspan="7" class="fw-bold text-start ps-2">MUKIM {{ $data->mukim }}</td></tr>
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

    /* Background gelap supaya fokus pada kertas (Preview Mode) */
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