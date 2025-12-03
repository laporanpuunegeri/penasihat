<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Prestasi Suku Tahun</title>
    <style>
        /* CSS KHAS UNTUK DOMPDF */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }

        /* WRAPPER UTAMA: Untuk Centering dan Had Lebar */
        .main-wrapper {
            width: 95%; /* Tetapkan lebar dokumen */
            margin: 0 auto; /* 🔥 INI YANG PENTING UNTUK CENTERING BLOK 🔥 */
            padding: 20px 0;
        }

        /* HEADER */
        .header { margin-bottom: 10px; }
        .header img { 
            width: 60px; 
            display: block; /* Wajib jadikan block untuk margin auto */
            margin: 0 auto; /* Center the image itself */
            margin-bottom: 5px;
        }
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
            padding: 3px 4px; 
            vertical-align: middle;
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
            font-size: 9px;
            padding: 6px 4px;
            text-align: left !important;
        }

        /* Footer Total */
        .sub-total td {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
            border-top: 1px solid #000;
        }
        .total-row td {
            background-color: #d8d8d8; /* Warna Kelabu Gelap untuk Total Utama */
            font-weight: bold;
            font-size: 9px;
            border-top: 2px solid #000;
        }
    </style>
</head>
<body>
    <div class="main-wrapper"> {{-- WRAPPER UTAMA --}}
        
        <div class="header text-center">
            {{-- Gantikan 'images/logo.png' dengan path Jata Negara yang betul --}}
            {{-- Saya guna kod lama untuk elak error jika file Jata belum ada --}}
            <img src="{{ public_path('images/logo.png') }}" alt="Jata Negara">
            <h2 class="title">LAPORAN PRESTASI SUKU TAHUN {{ $tahun ?? date('Y') }}</h2>
            <p style="font-size: 9px; margin-top: 5px;">Negeri {{ Auth::user()->negeri ?? 'IBU PEJABAT' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2" width="5%">KOD</th>
                    <th rowspan="2" width="30%">BUTIRAN</th>
                    <th rowspan="2" width="11%">PERUNTUKAN</th>
                    <th colspan="5">PRESTASI PERBELANJAAN (RM)</th>
                </tr>
                <tr class="sub-header">
                    <th>SUKU 1</th>
                    <th>SUKU 2</th>
                    <th>SUKU 3</th>
                    <th>SUKU 4</th>
                    <th>JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    // Inisialisasi Grand Totals
                    $grand_total_peruntukan = 0;
                    $grand_s1=0; $grand_s2=0; $grand_s3=0; $grand_s4=0;
                    $grand_total_belanja = 0;
                    
                    $last_kod_utama = null;
                @endphp

                @foreach($laporan_kewangan as $kod_utama => $data)
                    @php
                        // Inisialisasi Sub-Totals
                        $sub_s1=0; $sub_s2=0; $sub_s3=0; $sub_s4=0;
                        $sub_peruntukan = 0;
                        $sub_total_belanja = 0;

                        // Kira Sub-Totals untuk setiap KOE Utama
                        foreach($data['items'] as $item) {
                            $sub_s1 += $item->belanja_s1; $sub_s2 += $item->belanja_s2;
                            $sub_s3 += $item->belanja_s3; $sub_s4 += $item->belanja_s4;
                            $sub_peruntukan += $item->peruntukan;
                            $sub_total_belanja += $item->belanja;
                        }
                        
                        // Tambah kepada Grand Totals
                        $grand_total_peruntukan += $sub_peruntukan;
                        $grand_s1 += $sub_s1; $grand_s2 += $sub_s2; $grand_s3 += $sub_s3; $grand_s4 += $sub_s4;
                        $grand_total_belanja += $sub_total_belanja;
                    @endphp

                    {{-- HEADER KATEGORI UTAMA (cth: EMOLUMEN) --}}
                    <tr class="kod-utama-row">
                        <td class="text-center">{{ $kod_utama }}</td>
                        <td colspan="7">{{ $data['tajuk'] }}</td>
                    </tr>

                    @if(count($data['items']) > 0)
                        @foreach($data['items'] as $item)
                            <tr>
                                <td class="text-center">{{ $item->kod_objek }}</td>
                                <td class="text-left">{{ $item->butiran }}</td>
                                <td class="text-right">{{ number_format($item->peruntukan, 2) }}</td>
                                <td class="text-right">{{ number_format($item->belanja_s1, 2) }}</td>
                                <td class="text-right">{{ number_format($item->belanja_s2, 2) }}</td>
                                <td class="text-right">{{ number_format($item->belanja_s3, 2) }}</td>
                                <td class="text-right">{{ number_format($item->belanja_s4, 2) }}</td>
                                <td class="text-right">{{ number_format($item->belanja, 2) }}</td>
                            </tr>
                        @endforeach

                        {{-- SUB-TOTAL KATEGORI --}}
                        <tr class="sub-total">
                            <td colspan="2" class="text-right">JUMLAH {{ $kod_utama }}:</td>
                            <td class="text-right">{{ number_format($sub_peruntukan, 2) }}</td>
                            <td class="text-right">{{ number_format($sub_s1, 2) }}</td>
                            <td class="text-right">{{ number_format($sub_s2, 2) }}</td>
                            <td class="text-right">{{ number_format($sub_s3, 2) }}</td>
                            <td class="text-right">{{ number_format($sub_s4, 2) }}</td>
                            <td class="text-right">{{ number_format($sub_total_belanja, 2) }}</td>
                        </tr>
                    @endif
                @endforeach

                {{-- GRAND TOTAL --}}
                <tr class="total-row">
                    <td colspan="2" class="text-right">JUMLAH BESAR KESELURUHAN:</td>
                    <td class="text-right">{{ number_format($grand_total_peruntukan, 2) }}</td>
                    <td class="text-right">{{ number_format($grand_s1, 2) }}</td>
                    <td class="text-right">{{ number_format($grand_s2, 2) }}</td>
                    <td class="text-right">{{ number_format($grand_s3, 2) }}</td>
                    <td class="text-right">{{ number_format($grand_s4, 2) }}</td>
                    <td class="text-right">{{ number_format($grand_total_belanja, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="font-size: 8pt; font-style: italic; margin-top: 15px;">
           Dicetak pada: {{ \Carbon\Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i') }} | Sistem Laporan PUU
        </div>
        
    </div> {{-- END main-wrapper --}}

</body>
</html>