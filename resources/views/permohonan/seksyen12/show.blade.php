{{-- LOGIK: Kalau Agensi login, guna layout Agensi. Kalau bukan (Admin), guna layout Admin (app) --}}
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
        padding: 25mm 25mm; /* Margin dalam kertas (Atas/Bawah Kiri/Kanan) */
        margin: auto;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        font-family: "Times New Roman", Times, serif;
        line-height: 1.5;
        color: black;
        font-size: 11pt;
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
        padding: 8px; 
        text-align: left; 
        vertical-align: top;
        font-size: 11pt;
    }

    .float-end { float: right !important; }

    /* =========================================
       STYLE KHAS UNTUK PRINT / PDF (PENTING!)
       ========================================= */
    @media print {
        /* 1. Paksa Saiz Kertas A4 & Buang Header/Footer Browser */
        @page {
            size: A4 portrait;
            margin: 0; /* Kosongkan margin browser supaya kita boleh control sendiri */
        }

        /* 2. Sembunyikan semua benda kecuali kertas surat */
        body * {
            visibility: hidden;
        }
        
        /* 3. Tunjuk Kertas Surat Sahaja */
        .paper-preview, .paper-preview * {
            visibility: visible;
        }

        /* 4. Posisikan kertas ngam-ngam kat bucu */
        .paper-preview {
            position: absolute;
            left: 0;
            top: 0;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 25mm 25mm; /* Margin Dokumen Rasmi */
            box-shadow: none;   /* Buang bayang */
            background-color: white;
        }

        /* 5. Hilangkan butang/sidebar jika ada yg degil */
        .no-print, .sidebar, nav, header, footer, .btn { 
            display: none !important; 
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-3 no-print">
        
        {{-- LOGIK BUTANG KEMBALI --}}
        @if(Auth::guard('agensi')->check())
            <a href="{{ route('permohonan.seksyen12') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Senarai
            </a>
        @else
            <a href="{{ route('admin.warta.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Semakan
            </a>
        @endif

        {{-- Butang Cetak (Hanya Admin) --}}
        @if(!Auth::guard('agensi')->check())
            <button onclick="window.print()" class="btn btn-danger shadow-sm font-weight-bold">
                <i class="fas fa-file-pdf"></i> Cetak / Simpan PDF
            </button>
        @endif
    </div>

    {{-- KANDUNGAN SURAT --}}
    <div class="paper-preview">
        <div class="text-center bold">
            KANUN TANAH NEGARA [Akta 828]<br>
            <span class="italic">NATIONAL LAND CODE [Act 828]</span><br><br>
            PELANTIKAN DI BAWAH PERENGGAN 12(1)(b)<br>
            <span class="italic">APPOINTMENT UNDER PARAGRAPH 12(1)(b)</span>
        </div>

        <div class="mt-4" style="text-align: justify;">
            Pada menjalankan kuasa yang diberikan oleh perenggan 12(1)(b) Kanun Tanah Negara [Akta 828], 
            Pihak Berkuasa Negeri telah melantik orang yang dinamakan dalam ruang (1) Jadual bagi jawatan 
            yang dinyatakan dalam ruang (2) mulai dari tarikh yang ditunjukkan bersetentangan dalam ruang (3).
        </div>

        <div class="mt-2" style="text-align: justify;">
            Pelantikan ini adalah sah dan berkuatkuasa selagimana orang yang dinamakan dalam ruang (1) 
            Jadual bertugas di Daerah atau Negeri Melaka dan menjalankan tugas mengikut jawatan yang 
            telah dilantik di ruang (2) Jadual.
        </div>

        <div class="mt-3 italic" style="text-align: justify;">
            In exercise of the powers conferred by paragraph 12(1)(b) of the National Land Code [Act 828], 
            the State Authority has appointed the person named in column (1) of the Schedule to the office 
            specified in column (2) with effect from the dates shown against the respective name in column (3).
        </div>

        <div class="mt-2 italic" style="text-align: justify;">
            These appointments are valid and have effect so long as the persons named in column (1) of the 
            Schedule assigned in the District or State of Malacca and exercise duties as appointed in 
            column (2) of the Schedule.
        </div>

        <div class="mt-5 text-center bold">
            JADUAL / <span class="italic">SCHEDULE</span>
        </div>

        <table class="table-schedule">
            <thead>
                <tr class="text-center">
                    <th width="40%">(1)<br>Nama / <span class="italic">Name</span></th>
                    <th width="40%">(2)<br>Pelantikan / <span class="italic">Appointment</span></th>
                    <th width="20%">(3)<br>Tarikh / <span class="italic">Date</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="bold">{{ strtoupper($data->nama) }}</div>
                        <div>No. K/P / NRIC. {{ $data->no_kp }}</div>
                        <div class="mt-1">
                            {{ $data->jawatan }} /<br>
                            <span class="italic">{{ $data->position }}</span>
                        </div>
                    </td>
                    <td>
                        {{ $data->pelantikan_bm }} /<br>
                        <span class="italic">{{ $data->pelantikan_bi }}</span>
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($data->tarikh_lantikan)->format('d.m.Y') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4">
            [No. fail : {{ $data->no_fail }}]
        </div>

        <div class="mt-4">
            @php \Carbon\Carbon::setLocale('ms'); @endphp
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 100px;">Bertarikh :</td>
                    <td>{{ \Carbon\Carbon::parse($data->tarikh_tt)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr class="italic">
                    <td>Dated :</td>
                    <td>{{ \Carbon\Carbon::parse($data->tarikh_tt)->format('d F Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="mt-5 text-center float-end" style="width: 380px;">
            <div class="bold">DATUK HAELMY BIN MOHD HANIFAH</div>
            <div class="mt-2">Setiausaha</div>
            <div>Majlis Mesyuarat Kerajaan Negeri Melaka /</div>
            <div class="italic">Secretary State Executive Council Malacca</div>
        </div>
        <div style="clear: both;"></div>
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