<!DOCTYPE html>
<html>
<head>
    <title>Anggaran Belanja {{ $tahun }}</title>
    <style>
        @page { margin: 20px 25px; }
        body { font-family: sans-serif; font-size: 10px; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px 8px; vertical-align: top; }
        
        /* Kolum */
        .col-desc { width: 55%; }
        .col-money { width: 15%; text-align: right; }
        
        /* Warna & Font Baris */
        .bg-oa { background-color: #333; color: #fff; font-weight: bold; font-size: 11px; }
        .bg-os { background-color: #ddd; font-weight: bold; }
        .bg-group { background-color: #f0f0f0; font-style: italic; font-weight: bold; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        
        /* Indentasi */
        .pl-os { padding-left: 15px; }
        .pl-group { padding-left: 30px; }
        .pl-ol { padding-left: 50px; }

        /* Page Break */
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

    <div class="header">
        <h2>D'BUS (OBB) - ANGGARAN BELANJA TAHUN {{ $tahun }}</h2>
        <p>PENASIHAT UNDANG-UNDANG NEGERI MELAKA</p>
    </div>

    <table>
        <thead>
            <tr style="background-color: #ccc;">
                <th class="col-desc">BUTIRAN PERBELANJAAN</th>
                <th class="col-money">OL (RM)</th>
                <th class="col-money">OS (RM)</th>
                <th class="col-money">OA (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($structure as $oaKey => $oa)
                {{-- BARIS OA --}}
                <tr class="bg-oa">
                    <td>{{ $oaKey }} {{ $oa['perkara'] }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{ number_format($oa['jumlah'], 2) }}</td>
                </tr>

                @if(isset($oa['items']) && is_array($oa['items']))
                    @foreach($oa['items'] as $osKey => $os)
                        {{-- BARIS OS --}}
                        <tr class="bg-os">
                            <td class="pl-os">{{ $osKey }} {{ $os['perkara'] }}</td>
                            <td></td>
                            <td class="text-right">{{ number_format($os['jumlah'], 2) }}</td>
                            <td></td>
                        </tr>

                        @if(isset($os['items']) && is_array($os['items']))
                            @foreach($os['items'] as $groupKey => $group)
                                {{-- BARIS GROUP --}}
                                @if(is_array($group) && isset($group['perkara']))
                                    <tr class="bg-group">
                                        <td class="pl-group" colspan="4">
                                            {{ $groupKey }} {{ $group['perkara'] }}
                                        </td>
                                    </tr>

                                    {{-- BARIS OL --}}
                                    @if(isset($group['items']) && is_array($group['items']))
                                        @foreach($group['items'] as $olKey => $ol)
                                            <tr>
                                                <td class="pl-ol">
                                                    <span class="bold">{{ $olKey }}</span> {{ $ol['perkara'] ?? '' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ isset($ol['jumlah']) && $ol['jumlah'] > 0 ? number_format($ol['jumlah'], 2) : '-' }}
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #333; color: #fff;">
                <td class="text-right bold">JUMLAH KESELURUHAN</td>
                <td></td>
                <td></td>
                <td class="text-right bold">RM {{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="font-size: 9px; margin-top: 20px;">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}
    </div>

</body>
</html>