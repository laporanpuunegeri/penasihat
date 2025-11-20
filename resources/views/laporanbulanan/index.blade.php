@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="text-center fw-bold text-uppercase mb-4">Laporan Aktiviti Bulanan</h4>
    <p><strong>Bulan:</strong> {{ $bulan }}/{{ $tahun }}</p>
    <p><strong>Negeri:</strong> NEGERI KEDAH DARUL AMAN</p>

    <hr>
    <h5>1. PANDANGAN UNDANG-UNDANG</h5>
    <table class="table table-bordered table-sm">
        <thead><tr>
            <th>Tarikh Terima</th><th>Isu</th><th>Fakta Ringkas</th><th>Status</th>
        </tr></thead>
        <tbody>
        @foreach ($pandangan as $item)
            <tr>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>{{ $item->isu }}</td>
                <td>{{ $item->fakta_ringkas }}</td>
                <td>{{ $item->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h5>2. KES MAHKAMAH</h5>
    <table class="table table-bordered table-sm">
        <thead><tr>
            <th>Jenis Kes</th><th>Tarikh</th><th>Isu</th><th>Status</th>
        </tr></thead>
        <tbody>
        @foreach ($kesmahkamah as $item)
            <tr>
                <td>{{ $item->jenis_kes }}</td>
                <td>{{ $item->tarikh_sebutan }}</td>
                <td>{{ $item->isu }}</td>
                <td>{{ $item->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h5>3. GUBALAN / PINDAAN / SEMAKAN</h5>
    <ul>
        <li>Gubalan: {{ $gubalan->count() }} laporan</li>
        <li>Pindaan: {{ $pindaan->count() }} laporan</li>
        <li>Semakan: {{ $semakan->count() }} laporan</li>
    </ul>

    <h5>4. MESYUARAT YANG DIHADIRI</h5>
    <ul>
        @foreach ($mesyuarat as $item)
            <li>{{ $item->mesyuarat }} - {{ $item->tarikh ?? '-' }}</li>
        @endforeach
    </ul>

    <h5>5. KES TATATERTIB</h5>
    <ul>
        @foreach ($tatatertib as $item)
            <li>{{ $item->isu }} - {{ $item->status }}</li>
        @endforeach
    </ul>

    <h5>6. LAIN-LAIN TUGASAN</h5>
    <ul>
        @foreach ($lain as $item)
            <li>{{ $item->perihal }} ({{ $item->tarikh }})</li>
        @endforeach
    </ul>
</div>
@endsection