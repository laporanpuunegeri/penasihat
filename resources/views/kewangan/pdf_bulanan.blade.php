<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kewangan {{ $tahun }}</title>
    <style>
        /* CSS KHAS UNTUK DOMPDF */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px; /* Dikecilkan sedikit untuk muatkan 12 kolum */
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }

        /* HEADER */
        .header { margin-bottom: 10px; }
        .header img { width: 60px; display: block; margin: 0 auto; }
        .title { font-size: 14px; font-weight: bold; text-transform: uppercase; margin-top: 5px; }

        /* TABLE STYLING */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 3px 4px; /* Padding dikurangkan */
            vertical-align: top;
            word-wrap: break-word;
        }
        thead th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }
        
        .kod-utama-row td {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
            padding: 6px 4px;
        }

        /* Footer Total */
        .footer-total td {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
            border-top: 2px solid #000;
        }

        /* Monthly Cell */
        .monthly-cell {
            width: 4%; /* 12 kolum x 4% = 48% */
            font-size: 7.5px;
            padding: 3px 2px;
        }

    </style>
</head>
<body>

    <div class="header text-center">
        {{-- Gantikan 'images/logo.png' dengan path yang betul --}}
        <img src="{{ public_path('images/logo.png') }}" alt="Jata Negara">
        <div class="title">{{ $title }}</div>
        <div style="font-size: 10px;">MAKSUD BEKALAN: [B.08 - JABATAN PEGUAM NEGARA, MALAYSIA]</div>
        <p style="font-size: 9px; margin-top: 5px;">Negeri {{ Auth::user()->negeri ?? 'IBU PEJABAT' }}</p>
    </div>

    {{-- Jadual Utama Laporan --}}
    <table>
        <thead class="text-center">
            <tr>
                <th style="width: 6%" rowspan="2">KOD OBJEK</th>
                <th style="width: 21%" rowspan="2">BUTIRAN / JENIS PERBELANJAAN</th>
                <th style="width: 9%" rowspan="2">PERUNTUKAN (RM)</th>
                <th style="width: 9%" rowspan="2">TOTAL BELANJA (RM)</th>
                <th style="width: 9%" rowspan="2">BAKI (RM)</th>
                <th colspan="12" style="width: 51%;">PERBELANJAAN BULANAN (RM)</th>
            </tr>
            <tr>
                <th class="monthly-cell">JAN</th>
                <th class="monthly-cell">FEB</th>
                <th class="monthly-cell">MAC</th>
                <th class="monthly-cell">APR</th>
                <th class="monthly-cell">MEI</th>
                <th class="monthly-cell">JUN</th>
                <th class="monthly-cell">JUL</th>
                <th class="monthly-cell">OGOS</th>
                <th class="monthly-cell">SEP</th>
                <th class="monthly-cell">OKT</th>
                <th class="monthly-cell">NOV</th>
                <th class="monthly-cell">DIS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan_kewangan as $kod_utama => $group)
                @php
                    $baki_utama = $group['total_peruntukan'] - $group['total_belanja'];
                @endphp
                
                {{-- ROW KATEGORI UTAMA (ACCORDION HEADER) --}}
                <tr class="kod-utama-row">
                    <td colspan="5" class="text-left fw-bold">{{ $kod_utama }} - {{ $group['tajuk'] }}</td>
                    <td colspan="12" class="text-left fw-bold">Peratus Penggunaan: {{ number_format($group['total_peruntukan'] > 0 ? ($group['total_belanja'] / $group['total_peruntukan']) * 100 : 0, 1) }}%</td>
                </tr>

                {{-- LOOP ITEMS --}}
                @foreach($group['items'] as $item)
                    @php
                        $baki_item = $item->peruntukan - $item->belanja;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $item->kod_objek }}</td>
                        <td>{{ $item->butiran }}</td>
                        <td class="text-right">{{ number_format($item->peruntukan, 2) }}</td>
                        <td class="text-right fw-bold">{{ number_format($item->belanja, 2) }}</td>
                        <td class="text-right fw-bold">{{ number_format($baki_item, 2) }}</td>
                        
                        {{-- PERBELANJAAN BULANAN (BARU) --}}
                        <td class="text-right">{{ number_format($item->belanja_jan, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_feb, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_mac, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_apr, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_mei, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_jun, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_jul, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_ogos, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_sep, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_okt, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_nov, 2) }}</td>
                        <td class="text-right">{{ number_format($item->belanja_dis, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        
        {{-- FOOTER GRAND TOTAL --}}
        <tfoot>
            <tr class="footer-total">
                <td colspan="2" class="text-right fw-bold">JUMLAH BESAR KESELURUHAN:</td>
                <td class="text-right">{{ number_format($grand_total_peruntukan, 2) }}</td>
                <td class="text-right">{{ number_format($grand_total_belanja, 2) }}</td>
                <td class="text-right">{{ number_format($grand_total_peruntukan - $grand_total_belanja, 2) }}</td>
                <td colspan="12"></td>
            </tr>
        </tfoot>
    </table>
    
    {{-- Tambah Tarikh Cetak di Bawah --}}

        <div style="font-size: 8pt; font-style: italic; margin-top: 15px;">
           Dicetak pada: {{ \Carbon\Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i') }} | Sistem Laporan PUU
        </div>

</body>
</html>