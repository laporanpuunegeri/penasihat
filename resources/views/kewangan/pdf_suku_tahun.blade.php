<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 0; font-size: 12pt; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; }
        
        /* Design Biru Putih Versi Cetak */
        thead { background-color: #4e73df; color: white; }
        .sub-header { background-color: #2e59d9; color: white; }
        .category-header { background-color: #eaecf4; font-weight: bold; }
        .total-row { background-color: #2e59d9; color: white; font-weight: bold; }
        .sub-total { background-color: #f8f9fc; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-uppercase { text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $title }}</h2>
        <p>{{ Auth::user()->negeri ?? 'IBU PEJABAT' }}</p>
        <hr>
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
            @php $grand_s1=0; $grand_s2=0; $grand_s3=0; $grand_s4=0; @endphp

            @foreach($laporan_kewangan as $kod_utama => $data)
                @php
                    $sub_s1=0; $sub_s2=0; $sub_s3=0; $sub_s4=0;
                    foreach($data['items'] as $item) {
                        $sub_s1 += $item->belanja_s1; $sub_s2 += $item->belanja_s2;
                        $sub_s3 += $item->belanja_s3; $sub_s4 += $item->belanja_s4;
                    }
                    $grand_s1 += $sub_s1; $grand_s2 += $sub_s2; $grand_s3 += $sub_s3; $grand_s4 += $sub_s4;
                @endphp

                <tr class="category-header">
                    <td class="text-center">{{ $kod_utama }}</td>
                    <td colspan="7">{{ $data['tajuk'] }}</td>
                </tr>

                @if(count($data['items']) > 0)
                    @foreach($data['items'] as $item)
                        <tr>
                            <td class="text-center">{{ $item->kod_objek }}</td>
                            <td>{{ $item->butiran }}</td>
                            <td class="text-right">{{ number_format($item->peruntukan, 2) }}</td>
                            <td class="text-right">{{ number_format($item->belanja_s1, 2) }}</td>
                            <td class="text-right">{{ number_format($item->belanja_s2, 2) }}</td>
                            <td class="text-right">{{ number_format($item->belanja_s3, 2) }}</td>
                            <td class="text-right">{{ number_format($item->belanja_s4, 2) }}</td>
                            <td class="text-right">{{ number_format($item->belanja, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="sub-total">
                        <td colspan="2" class="text-right">JUMLAH {{ $kod_utama }}:</td>
                        <td class="text-right">{{ number_format($data['total_peruntukan'], 2) }}</td>
                        <td class="text-right">{{ number_format($sub_s1, 2) }}</td>
                        <td class="text-right">{{ number_format($sub_s2, 2) }}</td>
                        <td class="text-right">{{ number_format($sub_s3, 2) }}</td>
                        <td class="text-right">{{ number_format($sub_s4, 2) }}</td>
                        <td class="text-right">{{ number_format($data['total_belanja'], 2) }}</td>
                    </tr>
                @endif
            @endforeach

            <tr class="total-row">
                <td colspan="2" class="text-right">JUMLAH BESAR:</td>
                <td class="text-right">{{ number_format($grand_total_peruntukan, 2) }}</td>
                <td class="text-right">{{ number_format($grand_s1, 2) }}</td>
                <td class="text-right">{{ number_format($grand_s2, 2) }}</td>
                <td class="text-right">{{ number_format($grand_s3, 2) }}</td>
                <td class="text-right">{{ number_format($grand_s4, 2) }}</td>
                <td class="text-right">{{ number_format($grand_total_belanja, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 8pt; font-style: italic;">
        Dicetak pada: {{ date('d-m-Y H:i:s') }} | Sistem Laporan PUU
    </div>

</body>
</html>