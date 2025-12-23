{{-- LOGIK: Kalau Agensi login, guna layout Agensi. Kalau Admin, guna layout Admin (app) --}}
@extends(Auth::guard('agensi')->check() ? 'layouts.agensi' : 'layouts.app')

@section('content')
<style>
    /* =========================================
       STYLE PAPARAN SKRIN (PREVIEW MODE)
       ========================================= */
    .paper-preview {
        background-color: white;
        width: 210mm;       /* Lebar A4 */
        min-height: 297mm;  /* Tinggi A4 */
        padding: 25mm 20mm; /* Margin standard */
        margin: auto;
        margin-bottom: 30px; /* Jarak antara page 1 dan 2 di skrin */
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        font-family: "Times New Roman", Times, serif;
        line-height: 1.5;
        color: black;
        font-size: 11pt;
        position: relative;
    }
    
    .text-center { text-align: center; }
    .bold { font-weight: bold; }
    .italic { font-style: italic; }
    .uppercase { text-transform: uppercase; }
    
    /* Table Jadual */
    .table-schedule { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 20px; 
        border: 1px solid black;
    }
    .table-schedule td { 
        border: 1px solid black; 
        padding: 10px; 
        vertical-align: top;
    }

    /* Table Footer (Tandatangan) */
    .table-footer {
        width: 100%;
        margin-top: 40px;
        border: none;
    }
    .table-footer td {
        border: none;
        vertical-align: top;
        padding: 0;
    }

    /* =========================================
       STYLE KHAS UNTUK PRINT / PDF
       ========================================= */
    @media print {
        @page { size: A4 portrait; margin: 0; }
        
        /* Sembunyikan elemen UI */
        body * { visibility: hidden; }
        .no-print, .sidebar, nav, header, footer, .btn { display: none !important; }

        /* Tunjuk Kertas Sahaja */
        .paper-preview, .paper-preview * { visibility: visible; }
        
        .paper-preview {
            position: relative;
            width: 210mm; 
            min-height: 297mm;
            margin: 0; 
            padding: 25mm 20mm;
            box-shadow: none; 
            background: white;
            page-break-after: always; /* Paksa page baru selepas setiap kertas */
        }

        /* Jangan buat page kosong lepas page terakhir */
        .paper-preview:last-child {
            page-break-after: auto;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-3 no-print">
        
        {{-- LOGIK BUTANG KEMBALI --}}
        @if(Auth::guard('agensi')->check())
            <a href="{{ route('permohonan.seksyen64') }}" class="btn btn-secondary shadow-sm">
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
                <i class="fas fa-print"></i> Cetak ke PDF (2 Page)
            </button>
        @endif
    </div>

    {{-- ========================================================= --}}
    {{-- MUKA SURAT 1: TAJUK & TEKS KANDUNGAN                      --}}
    {{-- ========================================================= --}}
    <div class="paper-preview shadow-lg">
        @php \Carbon\Carbon::setLocale('ms'); @endphp
        
        <div class="text-center bold">
            KANUN TANAH NEGARA [Akta 828]<br>
            <span class="italic">NATIONAL LAND CODE [Act 828]</span><br>
            [Seksyen 64]<br><br>
            PEMBATALAN PERIZABAN TANAH<br>
            <span class="italic">REVOCATION OF RESERVATION OF LAND</span>
        </div>

        <div class="mt-4" style="text-align: justify;">
            <p>
                Bahawasanya, melalui Pemberitahuan No. <strong>{{ $data->no_warta_asal }}</strong> yang disiarkan dalam Warta Kerajaan Negeri Melaka pada 
                <strong>{{ \Carbon\Carbon::parse($data->tarikh_warta_asal)->translatedFormat('j F Y') }}</strong>, pada menjalankan kuasa yang diberikan oleh subseksyen 62(1) Kanun Tanah Negara [Akta 828], 
                Pihak Berkuasa Negeri telah mengisytiharkan bahawa <strong>Lot {{ $data->no_lot }}</strong> yang terletak di <strong>Mukim {{ $data->mukim }}</strong>, 
                <strong>Daerah {{ $data->daerah }}</strong> seluas <strong>{{ number_format($data->luas) }} meter persegi</strong> dan ditandakan di dalam Pelan Akui No. 
                <strong>{{ $data->no_pa }}</strong> yang tersimpan di Pejabat Pengarah Ukur dan Pemetaan, Melaka sebagai rizab bagi maksud awam, iaitu sebagai 
                <strong>{{ $data->tujuan_bm }}</strong> dan menetapkan bahawa <strong>{{ $data->kawalan_bm }}</strong> hendaklah mempunyai kawalan terhadap tanah rizab itu.
            </p>
            <p>
                Dan bahawasanya, didapati suaimanfaat sekarang oleh Pihak Berkuasa Negeri supaya perizaban yang bagi Lot {{ $data->no_lot }} disebut terdahulu itu patut dibatalkan;
            </p>
            <p>
                Dan bahawasanya, tiada apa-apa bantahan diterima terhadap cadangan Pihak Berkuasa Negeri sebagaimana yang disebut terdahulu;
            </p>
            <p>
                Maka oleh yang demikian, pada menjalankan kuasa yang diberikan oleh subseksyen 64(1) Kanun Tanah Negara, Pihak Berkuasa Negeri membatalkan perizaban yang disebut terdahulu.
            </p>
        </div>

        <div class="mt-3 italic" style="text-align: justify;">
            <p>
                Whereas, by Notification No. <strong>{{ $data->no_warta_asal }}</strong> published in the State of Malacca Government Gazette on 
                <strong>{{ \Carbon\Carbon::parse($data->tarikh_warta_asal)->format('j F Y') }}</strong>, in exercise of the powers conferred by subsection 62(1) of the National Land Code [Act 828], 
                the State Authority had proclaimed that <strong>Lot {{ $data->no_lot }}</strong>, situated in <strong>Mukim {{ $data->mukim }}</strong>, 
                <strong>District of {{ $data->daerah }}</strong>, having an area of <strong>{{ number_format($data->luas) }} square metres</strong> and delineated upon Certified Plan No. 
                <strong>{{ $data->no_pa }}</strong> which is deposited in the office of the Director of Survey and Mapping of Malacca to be a reserve for a public purpose, that is a site for a 
                <strong>{{ $data->tujuan_bi }}</strong> and stipulates that the <strong>{{ $data->kawalan_bi }}</strong> shall have control over the reserve land.
            </p>
            <p>
                And whereas, it now appears expedient to the State Authority that the aforesaid reservation of Lot {{ $data->no_lot }} should be revoked;
            </p>
            <p>
                And whereas, no objection to the proposal of the State Authority as aforesaid was received;
            </p>
            <p>
                Hence therefore, in exercise of the powers conferred by subsection 64(1) of the National Land Code, the State Authority revokes of the aforesaid reservation.
            </p>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MUKA SURAT 2: JADUAL & TANDATANGAN                        --}}
    {{-- ========================================================= --}}
    <div class="paper-preview shadow-lg">
        
        <div class="text-center bold">JADUAL / <span class="italic">SCHEDULE</span></div>
        
        <table class="table-schedule">
            <tr>
                <td width="50%">
                    Daerah / <span class="italic">District</span> : {{ $data->daerah }}<br>
                    Mukim / <span class="italic">Mukim</span> : {{ $data->mukim }}<br>
                    No. Lot / <span class="italic">Lot No.</span> : {{ $data->no_lot }}
                </td>
                <td width="50%">
                    No. Pelan Akui / <span class="italic">Certified Plan No.</span> : {{ $data->no_pa }}<br>
                    Keluasan / <span class="italic">Area</span> : {{ number_format($data->luas) }} meter persegi / <span class="italic">square metres</span>.
                </td>
            </tr>
        </table>

        <div class="mt-4">
            [No. fail : {{ $data->no_fail }}]
        </div>

        <table class="table-footer">
            <tr>
                <td width="50%" align="left" valign="bottom">
                    <div style="margin-bottom: 20px;">
                        <div>Bertarikh : {{ \Carbon\Carbon::parse($data->tarikh_tt)->translatedFormat('j F Y') }}<br>
                        <span class="italic">Dated : {{ \Carbon\Carbon::parse($data->tarikh_tt)->format('j F Y') }}</span>
                    </div>
                </td>

                <td width="50%" align="center" valign="bottom">
                    {{-- Ruang kosong untuk tandatangan --}}
                    <div style="height: 60px;"></div> 

                    <div class="bold uppercase">DATUK HAELMY BIN MOHD HANIFAH</div>
                    <div class="mt-1">Setiausaha</div>
                    <div>Majlis Mesyuarat Kerajaan Negeri Melaka /</div>
                    <div class="italic">Secretary State Executive Council Malacca</div>
                </td>
            </tr>
        </table>
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

    .container-fluid {
        padding-top: 20px;
        padding-bottom: 50px;
    }
</style>
@endif

@endsection