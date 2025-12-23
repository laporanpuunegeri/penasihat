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
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        font-family: "Times New Roman", Times, serif;
        line-height: 1.6;
        color: black;
        font-size: 12pt;
        position: relative;
    }
    
    .text-center { text-align: center; }
    .uppercase { text-transform: uppercase; }
    .bold { font-weight: bold; }
    .italic { font-style: italic; }
    
    /* Style Jadual */
    .table-schedule { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 15px; 
        border: 1px solid black;
    }
    .table-schedule th, .table-schedule td { 
        border: 1px solid black; 
        padding: 10px; 
        text-align: center; 
        vertical-align: middle;
        font-size: 11pt;
    }

    .float-end { float: right !important; }

    /* Table Footer (Tandatangan) tanpa border */
    .table-footer {
        width: 100%;
        margin-top: 50px;
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
        
        body * { visibility: hidden; }
        .no-print, .sidebar, nav, header, footer, .btn { display: none !important; }

        .paper-preview, .paper-preview * { visibility: visible; }
        .paper-preview {
            position: absolute; left: 0; top: 0;
            width: 210mm; height: 297mm;
            margin: 0; padding: 25mm 20mm;
            box-shadow: none; background-color: white;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-3 no-print">
        
        {{-- LOGIK BUTANG KEMBALI --}}
        @if(Auth::guard('agensi')->check())
            <a href="{{ route('permohonan.seksyen62') }}" class="btn btn-secondary shadow-sm">
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
                <i class="fas fa-print"></i> Cetak ke PDF
            </button>
        @endif
    </div>

    {{-- KANDUNGAN SURAT --}}
    <div class="paper-preview shadow-lg">
        
        {{-- TAJUK --}}
        <div class="text-center bold">
            KANUN TANAH NEGARA [Akta 828]<br>
            <span class="italic">NATIONAL LAND CODE [Act 828]</span><br>
            [Subseksyen 62(1)] / <span class="italic">[Subsection 62(1)]</span><br><br>
            PERIZABAN TANAH BAGI MAKSUD AWAM<br>
            <span class="italic">RESERVATION OF LAND FOR A PUBLIC PURPOSE</span>
        </div>

        {{-- PERENGGAN BM --}}
        <div class="mt-4" style="text-align: justify;">
            Pada menjalankan kuasa yang diberikan di bawah subseksyen 62(1) Kanun Tanah Negara [Akta 828], 
            Pihak Berkuasa Negeri telah merizabkan tanah yang diperihalkan dalam Jadual bagi maksud awam, 
            iaitu, suatu tapak untuk tujuan <strong>{{ $data->tujuan_bm }}</strong>, 
            dan telah menetapkan bahawa <strong>{{ $data->kawalan_bm }}</strong> hendaklah mempunyai kawalan terhadap tanah rizab itu 
            dan bahawa tanah rizab itu hendaklah diselenggarakan oleh <strong>{{ $data->selenggara_bm }}</strong>.
        </div>

        {{-- PERENGGAN BI --}}
        <div class="mt-3 italic" style="text-align: justify;">
            In exercising the powers conferred under subsection 62(1) of the National Land Code [Act 828], 
            the State Authority has reserved the land described in the Schedule for a public purpose, 
            that is, a site for the purpose of <strong>{{ $data->tujuan_bi }}</strong>, 
            and has stipulated that the <strong>{{ $data->kawalan_bi }}</strong> shall have control over the reserved land 
            and that the reserved land shall be maintained by the <strong>{{ $data->selenggara_bi }}</strong>.
        </div>

        {{-- JADUAL --}}
        <div class="mt-5 text-center bold">
            JADUAL / <span class="italic">SCHEDULE</span>
        </div>

        <table class="table-schedule">
            <thead>
                <tr>
                    <th>(1)<br>Daerah / <span class="italic">District</span></th>
                    <th>(2)<br>Mukim / <span class="italic">Mukim</span></th>
                    <th>(3)<br>No. Lot / <span class="italic">Lot No.</span></th>
                    <th>(4)<br>No. Pelan Akui / <span class="italic">Certified Plan No.</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data->daerah }}</td>
                    <td>{{ $data->mukim }}</td>
                    <td>{{ $data->no_lot }}</td>
                    <td>{{ $data->no_pa }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: left; border-top: 2px solid black;">
                        <strong>Keluasan / <span class="italic">Area</span>:</strong> 
                        {{ number_format($data->luas, 4) }} meter persegi / <span class="italic">square metres</span>. 
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- NO FAIL & TARIKH & TANDATANGAN --}}
        <div class="mt-3">
            [No. fail : {{ $data->no_fail }}] 
        </div>

        <table class="table-footer">
            <tr>
                <td width="50%" align="left" valign="bottom">
                    @php \Carbon\Carbon::setLocale('ms'); @endphp
                    <div style="margin-bottom: 60px;">
                        <div>Bertarikh : {{ \Carbon\Carbon::parse($data->tarikh_tt)->translatedFormat('d F Y') }}<br>
                        <span class="italic">Dated : {{ \Carbon\Carbon::parse($data->tarikh_tt)->format('d F Y') }}</span>
                    </div>
                </td>

                <td width="50%" align="center" valign="bottom">
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