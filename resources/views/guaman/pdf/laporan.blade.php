<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        @page { margin: 2cm 1cm 2cm 1cm; }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 9pt; 
        }

        /* Header Atas */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            text-transform: uppercase;
        }

        /* Footer Bawah (Untuk Tarikh) */
        .footer {
            position: fixed; 
            bottom: -30px; 
            left: 0px; 
            right: 0px;
            height: 30px; 
            text-align: center;
            font-size: 8pt;
            font-style: italic;
            color: #555;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        th {
            background-color: #f0f0f0;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
        }
        
        .kod-header {
            background-color: #e0e0e0;
            padding: 5px;
            font-weight: bold;
            border: 1px solid #000;
            margin-top: 10px;
            font-size: 9pt;
        }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    {{-- FOOTER: Tarikh letak di sini (Akan muncul di bawah setiap page) --}}
    <div class="footer">
        Dicetak pada: {{ $currentDate }}
    </div>

    {{-- HEADER --}}
    <div class="header">
        <h3>STATUS KES YANG DIKENDALIKAN OLEH UNIT GUAMAN MELAKA</h3>
    </div>

    {{-- KANDUNGAN --}}
    @foreach ($groupedCases as $kod => $cases)
        
        {{-- Header Kod Perkara --}}
        @php 
            $catTitle = $categories[$kod]['title'] ?? 'KATEGORI TIDAK DIKETAHUI';
        @endphp
        
        <div class="kod-header">
            KOD {{ $kod }} - {{ $catTitle }}
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%">NO.</th>
                    <th style="width: 25%">RUJUKAN FAIL</th>
                    <th style="width: 35%">RUJUKAN MAHKAMAH / PIHAK BERLAWANAN</th>
                    <th style="width: 20%">KATEGORI KES</th>
                    <th style="width: 15%">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cases as $index => $case)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $case->rujukan_fail }}</strong><br>
                            <span style="font-size: 8pt;">Buka pada: {{ \Carbon\Carbon::parse($case->tarikh_buka)->format('d.m.Y') }}</span><br><br>
                            <span style="font-size: 8pt;">Kendalian: {{ $case->kendalian_oleh }}</span>
                        </td>
                        <td>
                            <strong>{{ $case->mahkamah }}</strong><br>
                            Ruj: {{ $case->rujukan_mahkamah ?? '-' }}<br><br>
                            <strong>Pihak Berlawanan:</strong><br>
                            {{ $case->pihak_berlawanan }}
                        </td>
                        <td>{{ $case->kategori_kes }}</td>
                        <td>{{ $case->status_kes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endforeach

</body>
</html>