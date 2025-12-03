<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $metadata['tajuk'] }} - {{ $metadata['tahun'] }}</title>

    <style>
        /* ============================================================
           GLOBAL STYLE
        ============================================================ */
        @page { size: A4 landscape; margin: 15mm; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        /* ============================================================
           HEADER
        ============================================================ */
        .header-laporan {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-laporan img {
            width: 80px;
            margin-bottom: 5px;
        }

        .header-laporan p {
            margin: 0;
            font-weight: bold;
            font-size: 10px;
        }

        .header-laporan h4 {
            margin: 5px 0 0 0;
            font-size: 15px;
            font-weight: bold;
        }

        /* ============================================================
           TABLE
        ============================================================ */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid black;
            padding: 4px;
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            background: #e6e6e6;
            font-weight: bold;
            height: 32px;
        }

        .text-left { text-align: left; }

        .outcome-row td {
            background: #f3f7fa;
            font-weight: bold;
            padding: 6px;
            font-size: 10px;
            text-align: left !important;
        }

        /* ============================================================
           SIGNATURES
        ============================================================ */
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }

        .sig-col {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            font-size: 11px;
        }

        .sig-line {
            margin-top: 35px;
            width: 85%;
            border-bottom: 1px solid #000;
            height: 1px;
        }

        .sig-label {
            margin-top: 5px;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <!-- ============================================================
         HEADER
    ============================================================ -->
    <div class="header-laporan">
        <img src="{{ public_path('images/logo.png') }}" alt="Jata Negara">
        <p>LAMPIRAN A | MAKLUM BALAS</p>
        <h4>PRESTASI KERANGKA KEBERHASILAN PROGRAM PENGURUSAN (PPUUN)<br>
            SEHINGGA 31 DISEMBER {{ $metadata['tahun'] }}</h4>
    </div>

    <!-- ============================================================
         TABLE
    ============================================================ -->
    <table>
        <thead>
            <tr>
                <th colspan="2" rowspan="2" style="width: 40%;">OUTCOME & KPI</th>
                <th rowspan="2" style="width: 8%;"></th>
                <th colspan="4">Pencapaian Suku Tahun</th>
                <th rowspan="2" style="width: 24%;">Catatan</th>
            </tr>
            <tr>
                <th style="width: 6%;">1 (25%)</th>
                <th style="width: 6%;">2 (50%)</th>
                <th style="width: 6%;">3 (75%)</th>
                <th style="width: 6%;">4 (100%)</th>
            </tr>

            <tr>
                <th>Bil.</th>
                <th class="text-left">Penerangan / Peratusan</th>
                <th>{{ $metadata['sasaran'] ?? 'Sasaran Tahunan' }}</th>
                <th colspan="4">Nilai Pencapaian</th>
                <th class="text-left">Perincian Catatan</th>
            </tr>
        </thead>

        <tbody>

        @forelse($data as $outcomeId => $records)
            @php
                $record = $records[0];
                $outcomeNum = explode(' ', $outcomeId)[1];
                $sasaran = $record->sasaran_tahunan;
            @endphp

            <!-- OUTCOME ROW -->
            <tr class="outcome-row">
                <td colspan="8">
                    **{{ $outcomeId }}**: {{ $record->kpi_desc }}
                </td>
            </tr>

            <!-- KPI ROW -->
            <tr>
                <td>KPI {{ $outcomeNum }}</td>
                <td class="text-left">{{ $record->kpi_desc }}</td>
                <td>{{ $sasaran }}%</td>

                <td>{{ $record->suku_1 }}</td>
                <td>{{ $record->suku_2 }}</td>
                <td>{{ $record->suku_3 }}</td>
                <td>{{ $record->suku_4 }}</td>

                <td class="text-left" rowspan="2">
                    @if(is_array($record->catatan_data))
                        @foreach($record->catatan_data as $key => $value)
                            ({{ $loop->iteration }}): {{ $key }} - {{ $value }}<br>
                        @endforeach
                    @else
                        - Tiada catatan -
                    @endif
                </td>
            </tr>

            <!-- PERATUSAN ROW -->
            <tr>
                <td></td>
                <td class="text-left">Peratusan Diselesaikan Dalam Tempoh</td>
                <td></td>

                <td>({{ number_format($record->suku_1 / $sasaran * 100, 0) }}%)</td>
                <td>({{ number_format($record->suku_2 / $sasaran * 100, 0) }}%)</td>
                <td>({{ number_format($record->suku_3 / $sasaran * 100, 0) }}%)</td>
                <td>({{ number_format($record->suku_4 / $sasaran * 100, 0) }}%)</td>
            </tr>

        @empty
            <tr>
                <td colspan="8" style="height: 60px; text-align:center;">
                    Tiada rekod PPUUN untuk tahun {{ $metadata['tahun'] }}.
                </td>
            </tr>
        @endforelse

        </tbody>
    </table>

    <!-- ============================================================
         SIGNATURE SECTION
    ============================================================ -->
    <div class="signature-section">

        <div class="sig-col">
            <p>Disediakan oleh:</p>
            <div class="sig-line"></div>
            <p class="sig-label">Urusetia Program: MOHD SAUFI BIN YUSOP</p>
            <p>Tarikh: Januari {{ $metadata['tahun'] }}</p>
        </div>

        <div class="sig-col">
            <p>Disahkan oleh:</p>
            <div class="sig-line"></div>
            <p class="sig-label">Ketua Program: YB SAIFULRIJAL BIN AZHARI</p>
            <p>Tarikh: Januari {{ $metadata['tahun'] }}</p>
        </div>

    </div>

</body>
</html>
