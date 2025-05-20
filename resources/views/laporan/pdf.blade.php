<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktiviti Bulanan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .header {
            margin-bottom: 20px;
            text-align: center;
        }
        .header img {
            width: 80px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .sub-title {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px;
            vertical-align: top;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
        }
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
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/logo.png') }}" alt="Jata Negara">
    <div class="title">LAPORAN AKTIVITI BULANAN</div>
    <div class="sub-title">
        PENASIHAT UNDANG-UNDANG NEGERI {{ strtoupper($user->negeri ?? '-') }}
    </div>
</div>

<table class="info-table">
    <tr>
        <td style="width: 25%;">NAMA PEGAWAI</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ strtoupper($user->name ?? '-') }}</td>
    </tr>
    <tr>
        <td>JAWATAN</td>
        <td>:</td>
        <td>{{ strtoupper($user->nama_jawatan ?? '-') }}</td>
    </tr>
    <tr>
        <td>BULAN</td>
        <td>:</td>
        <td>{{ $bulan }}/{{ $tahun }}</td>
    </tr>
    <tr>
        <td>NEGERI</td>
        <td>:</td>
        <td>{{ strtoupper($user->negeri ?? '-') }}</td>
    </tr>
</table>

{{-- 1. PANDANGAN UNDANG-UNDANG --}}
<h5 class="section-title">1. PANDANGAN UNDANG-UNDANG</h5>
<table>
    <thead>
        <tr>
            <th>BIL</th>
            <th>TARIKH TERIMA</th>
            <th>FAKTA RINGKASAN</th>
            <th>ISU</th>
            <th>RINGKASAN PANDANGAN</th>
            <th>*LISAN</th>
            <th>*BERTULIS</th>
            <th>STATUS / TARIKH SELESAI</th>
        </tr>
    </thead>
    <tbody>
        @php
            $kategori_list = [
                'Perlembagaan',
                'Tanah / PBT',
                'Undang-Undang Pentadbiran / Perkhidmatan',
                'Perjanjian / MOU',
                'Penswastaan',
                'Lain-lain',
            ];
            $label_abjad = ['(i)', '(ii)', '(iii)', '(iv)', '(v)', '(vi)'];
        @endphp

        @foreach ($kategori_list as $index => $kategori)
            <tr>
                <td colspan="8" style="font-weight: bold;">
                    {{ $label_abjad[$index] }} {{ strtoupper($kategori) }}
                </td>
            </tr>

            @php
                $laporanKategori = $laporan->where('kategori', $kategori);
                $bil = 1;
            @endphp

            @forelse ($laporanKategori as $lap)
                <tr>
                    <td>{{ $bil++ }}</td>
                    <td>{{ optional($lap->tarikh_terima)->format('d/m/Y') }}</td>
                    <td>{{ $lap->fakta_ringkasan }}</td>
                    <td>{{ $lap->isu }}</td>
                    <td>{{ $lap->ringkasan_pandangan }}</td>
                    <td class="text-center">{{ $lap->jenis_pandangan === 'Lisan' ? '✔' : '' }}</td>
                    <td class="text-center">{{ $lap->jenis_pandangan === 'Bertulis' ? '✔' : '' }}</td>
                    <td>
                        {{ $lap->status }}
                        @if ($lap->tarikh_selesai)
                            <br><small>Selesai: {{ optional($lap->tarikh_selesai)->format('d/m/Y') }}</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-muted fst-italic">Tiada laporan bagi kategori ini.</td>
                </tr>
            @endforelse
        @endforeach
    </tbody>
</table>

{{-- 2. KES MAHKAMAH --}}
<div style="page-break-before: always;"></div>
<h5 class="section-title">2. LAPORAN KES MAHKAMAH</h5>
<table>
    <thead>
        <tr>
            <th style="background-color: #f2f2f2; font-weight: bold;">BIL</th>
            <th style="background-color: #f2f2f2; font-weight: bold;">*JENIS KES / PIHAK-PIHAK</th>
            <th style="background-color: #f2f2f2; font-weight: bold;">TARIKH SEBUTAN / BICARA</th>
            <th style="background-color: #f2f2f2; font-weight: bold;">FAKTA RINGKAS</th>
            <th style="background-color: #f2f2f2; font-weight: bold;">ISU</th>
            <th style="background-color: #f2f2f2; font-weight: bold;">**SKOP TUGAS</th>
            <th style="background-color: #f2f2f2; font-weight: bold;">RINGKASAN HUJAHAN</th>
            <th style="background-color: #f2f2f2; font-weight: bold;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_kesmahkamah as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->jenis_kes ?? '-' }}</td>
                <td>{{ $item->tarikh_sebutan ? \Carbon\Carbon::parse($item->tarikh_sebutan)->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->fakta_ringkas ?? '-' }}</td>
                <td>{{ $item->isu ?? '-' }}</td>
                <td>{{ $item->skop_tugas ?? '-' }}</td>
                <td>{{ $item->ringkasan_hujahan ?? '-' }}</td>
                <td>{{ $item->status ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted fst-italic">Tiada data direkodkan.</td>
            </tr>
        @endforelse
    </tbody>
</table>


{{-- 3. GUBALAN --}}
<div style="page-break-before: always;"></div>
<h5 class="section-title">3. LAPORAN PENGGUBALAN RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF YANG DIGUBAL</h5>
<table>
    <thead>
        <tr>
            <th>BIL</th>
            <th>TAJUK</th>
            <th>TINDAKAN</th>
            <th>STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_gubalan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->tajuk }}</td>
                <td>{{ $item->tindakan }}</td>
                <td>{{ $item->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-muted text-center fst-italic">Tiada laporan penggubalan direkodkan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- 4. PINDAAN --}}
<div style="page-break-before: always;"></div>
<h5 class="section-title">4. LAPORAN PINDAAN RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF YANG DIPINDA</h5>
<table>
    <thead>
        <tr>
            <th>BIL</th>
            <th>TAJUK</th>
            <th>TINDAKAN</th>
            <th>STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_pindaan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->tajuk }}</td>
                <td>{{ $item->tindakan }}</td>
                <td>{{ $item->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-muted text-center fst-italic">Tiada laporan pindaan direkodkan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- 5. SEMAKAN --}}
<div style="page-break-before: always;"></div>
<h5 class="section-title">5. LAPORAN PENYEMAKAN RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF YANG DISEMAK</h5>
<table>
    <thead>
        <tr>
            <th>BIL</th>
            <th>TAJUK</th>
            <th>TINDAKAN</th>
            <th>STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_semakan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->tajuk }}</td>
                <td>{{ $item->tindakan }}</td>
                <td>{{ $item->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted fst-italic">Tiada laporan semakan direkodkan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- 6. MESYUARAT --}}
<div style="page-break-before: always;"></div>
<h5 class="section-title">6. LAPORAN MESYUARAT YANG DIHADIRI</h5>
<table>
    <thead>
        <tr>
            <th rowspan="2">BIL</th>
            <th rowspan="2">MESYUARAT</th>
            <th rowspan="2">ISU</th>
            <th rowspan="2">TARIKH</th>
            <th rowspan="2">STATUS</th>
            <th colspan="2">PANDANGAN</th>
        </tr>
        <tr>
            <th>LISAN</th>
            <th>BERTULIS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_mesyuarat as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->mesyuarat }}</td>
                <td>{{ $item->isu }}</td>
                <td>{{ $item->tarikh_mesyuarat ? \Carbon\Carbon::parse($item->tarikh_mesyuarat)->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->status }}</td>
                <td class="text-center">{{ $item->pandangan === 'Lisan' ? '✔' : '' }}</td>
                <td class="text-center">{{ $item->pandangan === 'Bertulis' ? '✔' : '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-muted text-center fst-italic">Tiada mesyuarat direkodkan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- 7. TATATERTIB --}}
<div style="page-break-before: always;"></div>
<h5 class="section-title">7. KES TATATERTIB</h5>
<table>
    <thead>
        <tr>
            <th>TARIKH TERIMA</th>
            <th>KATEGORI</th>
            <th>FAKTA RINGKASAN</th>
            <th>ISU</th>
            <th>RINGKASAN PANDANGAN</th>
            <th>STATUS / TARIKH SELESAI</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporan_tatatertib as $item)
            <tr>
                <td>{{ optional($item->tarikh_terima)->format('d/m/Y') }}</td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->fakta_ringkasan }}</td>
                <td>{{ $item->isu }}</td>
                <td>{{ $item->ringkasan_pandangan }}</td>
                <td>
                    {{ $item->status }}
                    @if($item->tarikh_selesai)
                        <br><small>Selesai: {{ optional($item->tarikh_selesai)->format('d/m/Y') }}</small>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{-- 8. LAIN-LAIN TUGASAN --}}
<div style="page-break-before: always;"></div>
<h5 class="section-title">8. LAIN-LAIN TUGASAN</h5>
<table>
    <thead>
        <tr>
            <th style="background-color:#f2f2f2; font-weight:bold;">BIL</th>
            <th style="background-color:#f2f2f2; font-weight:bold;">PERIHAL</th>
            <th style="background-color:#f2f2f2; font-weight:bold;">TARIKH</th>
            <th style="background-color:#f2f2f2; font-weight:bold;">TINDAKAN</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_lainlain as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->perihal }}</td>
               <td>{{ \Carbon\Carbon::parse($item->tarikh)->format('d/m/Y') }}</td>
                <td>{{ $item->tindakan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-muted text-center fst-italic">Tiada laporan tugasan lain-lain direkodkan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
