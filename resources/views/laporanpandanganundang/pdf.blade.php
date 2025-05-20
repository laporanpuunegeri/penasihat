<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pandangan Undang-Undang - PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h3>Laporan Pandangan Undang-Undang</h3>
    <table>
        <thead>
            <tr>
                <th>Bil</th>
                <th>Tarikh Terima</th>
                <th>Fakta Ringkasan</th>
                <th>Isu</th>
                <th>Ringkasan Pandangan</th>
                <th>Lisan</th>
                <th>Bertulis</th>
                <th>Status / Tarikh Selesai</th>
            </tr>
        </thead>
        <tbody>
            @php $bil = 1; @endphp
            @foreach($kategori as $kategoriIsu => $senarai)
            <tr>
                <td colspan="8"><strong>{{ $kategoriIsu }}</strong></td>
            </tr>
                @foreach($senarai as $laporan)
                <tr>
                    <td>{{ $bil++ }}</td>
                    <td>{{ $laporan->tarikh_terima }}</td>
                    <td>{{ $laporan->fakta_ringkasan }}</td>
                    <td>{{ $laporan->isu }}</td>
                    <td>{{ $laporan->ringkasan_pandangan }}</td>
                    <td>{{ strpos($laporan->jenis_pandangan, 'Lisan') !== false ? '✓' : '' }}</td>
                    <td>{{ strpos($laporan->jenis_pandangan, 'Bertulis') !== false ? '✓' : '' }}</td>
                    <td>{{ $laporan->status }}<br>{{ $laporan->tarikh_selesai ?? '-' }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
