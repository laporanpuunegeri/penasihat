@extends('layouts.app')

@section('content')
<div class="container-fluid px-5">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="text-start">
            <h4 class="text-uppercase fw-bold mb-0">Laporan Aktiviti Bulanan</h4>
            <h5 class="text-uppercase mb-1">Penasihat Undang-Undang Negeri {{ strtoupper(auth()->user()->negeri ?? '-') }}</h5>
            <h6 class="text-uppercase text-muted">Bulan {{ strtoupper(now()->translatedFormat('F')) }} {{ now()->year }}</h6>
        </div>
        <div class="text-end">
            <strong>LAMPIRAN B</strong>
            <br>
            <a href="{{ route('laporan.pdf') }}" class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                <i class="bi bi-printer"></i> Cetak PDF
            </a>
        </div>
    </div>

    <div class="ms-2 mb-4" style="line-height: 2">
        <p><strong>Nama Pegawai</strong> : {{ strtoupper(auth()->user()->name ?? '-') }}</p>
        <p><strong>Jawatan</strong> : PENASIHAT UNDANG-UNDANG NEGERI</p>
        <p><strong>Negeri</strong> : {{ strtoupper(auth()->user()->negeri ?? '-') }}</p>
    </div>

    <h5 class="fw-bold mb-3">PANDANGAN UNDANG-UNDANG 
        <small class="fw-normal">(Laporan lengkap adalah seperti di <strong>LAMPIRAN I</strong>)</small>
    </h5>

    @php
        $data = collect($kategori_list)->map(function ($kategori) use ($laporan) {
            $laporanKategori = $laporan->where('kategori', $kategori);
            return [
                'kategori' => $kategori,
                'bilangan' => $laporanKategori->count(),
                'jpn' => $laporanKategori->where('dirujuk_jpn', true)->count(),
                'negeri' => $laporanKategori->where('dirujuk_jpn', false)->count(),
                'laporan' => $laporanKategori,
            ];
        });
        $jumlah = $data->sum('bilangan');
        $jumlah_jpn = $data->sum('jpn');
        $jumlah_negeri = $data->sum('negeri');
    @endphp

    <table class="table table-bordered text-center align-middle mb-5">
        <thead class="table-secondary">
            <tr>
                <th rowspan="2" class="align-middle">Pembahagian Pandangan Undang-Undang mengikut isu</th>
                <th rowspan="2" class="align-middle">Bilangan</th>
                <th colspan="2">Bilangan</th>
                <th rowspan="2" class="align-middle">Status</th>
            </tr>
            <tr>
                <th>yang dirujuk ke JPN<br>(Ibu Pejabat)</th>
                <th>diputuskan di peringkat Negeri</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $kategori)
                <tr>
                    <td class="text-start">
                        <strong>{{ ['i.','ii.','iii.','iv.','v.','vi.'][$index] }}</strong> {{ $kategori['kategori'] }}
                    </td>
                    <td>{{ $kategori['bilangan'] }}</td>
                    <td>{{ $kategori['jpn'] }}</td>
                    <td>{{ $kategori['negeri'] }}</td>
                    <td>-</td>
                </tr>
            @endforeach
            <tr>
                <th class="text-end">JUMLAH KESELURUHAN</th>
                <th>{{ $jumlah }}</th>
                <th>{{ $jumlah_jpn }}</th>
                <th>{{ $jumlah_negeri }}</th>
                <th>-</th>
            </tr>
        </tbody>
    </table>

    <h5 class="fw-bold mb-3">1. SENARAI PANDANGAN UNDANG-UNDANG TERPERINCI</h5>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>BIL</th>
                <th>Tarikh</th>
                <th>Kategori</th>
                <th>Fakta Ringkasan</th>
                <th>Isu</th>
                <th>Ringkasan Pandangan</th>
                <th>Jenis Pandangan</th>
                <th>Status / Tarikh Selesai</th>
            </tr>
        </thead>
        <tbody>
            @php $bil = 1; @endphp

            @foreach ($data as $index => $kategori)
                <tr class="table-secondary">
                    <td colspan="8" class="fw-bold">
                        ({{ $index + 1 }}) {{ strtoupper($kategori['kategori']) }}
                    </td>
                </tr>

                @forelse ($kategori['laporan'] as $item)
                    <tr>
                        <td>{{ $bil++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tarikh_terima)->format('d/m/Y') }}</td>
                        <td>{{ $kategori['kategori'] }}</td>
                        <td>{{ $item->fakta_ringkasan }}</td>
                        <td>{{ $item->isu }}</td>
                        <td>{{ $item->ringkasan_pandangan }}</td>
                        <td class="text-center">
                            @if ($item->jenis_pandangan === 'Lisan')
                                &#10004;
                            @else
                                Bertulis
                            @endif
                        </td>
                        <td>
                            {{ $item->status }}
                            @if ($item->tarikh_selesai)
                                <br><small class="text-muted">Selesai: {{ \Carbon\Carbon::parse($item->tarikh_selesai)->format('d/m/Y') }}</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted fst-italic">Tiada laporan ditemui untuk kategori ini.</td>
                    </tr>
                @endforelse
            @endforeach
        </tbody>
    </table>
</div>
@endsection
