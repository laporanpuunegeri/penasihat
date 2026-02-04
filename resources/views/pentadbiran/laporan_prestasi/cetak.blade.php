<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $metadata['tajuk'] }} - {{ $metadata['tahun'] }}</title>
    <style>
        /* SETTING KERTAS & FONT MACAM WORD */
        @page { size: A4 landscape; margin: 20mm 15mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; color: #000; line-height: 1.2; }

        /* HEADER SECTION */
        .top-right-label { text-align: right; font-weight: bold; font-size: 10pt; margin-bottom: 5px; }
        .header-laporan { text-align: center; margin-bottom: 25px; }
        .header-laporan img { width: 90px; margin-bottom: 10px; }
        .header-laporan h4 { margin: 5px 0 0 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }

        /* TABLE STYLE */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 30px; }
        th, td { border: 1px solid black; padding: 8px; vertical-align: top; text-align: center; font-size: 10pt; word-wrap: break-word; }
        th { background-color: #d9d9d9; font-weight: bold; vertical-align: middle; }
        .text-left { text-align: left; }
        .text-bold { font-weight: bold; }

        /* Row Outcome */
        .outcome-row td { background-color: #f2f2f2; text-align: left !important; font-weight: bold; padding: 10px; text-transform: uppercase; }

        /* SIGNATURE SECTION */
        .signature-section { margin-top: 40px; width: 100%; page-break-inside: avoid; }
        .sig-table { width: 100%; border: none; }
        .sig-table td { border: none; text-align: left; padding: 5px; vertical-align: top; }
        .sig-box { height: 90px; vertical-align: bottom; }
        .sig-img { max-height: 80px; max-width: 250px; }
        .sig-line { border-bottom: 1px solid black; width: 95%; margin-top: 5px; margin-bottom: 5px; }
    </style>
</head>
<body>

    @php
        $outcomeTitles = [
            'OUTCOME 1' => 'KHIDMAT NASIHAT PERUNDANGAN YANG CEKAP DAN TERATUR KEPADA KERAJAAN NEGERI',
            'OUTCOME 2' => 'PENGENDALIAN KES SIVIL KERAJAAN NEGERI YANG CEKAP DAN TERATUR',
            'OUTCOME 3' => 'PENGGUBALAN SEMAKAN DAN PENCETAKAN SEMULA ENAKMEN DAN RANG UNDANG-UNDANG SUBSIDIARI YANG CEKAP DAN TERATUR'
        ];
    @endphp

    <div class="top-right-label">
        LAMPIRAN A<br>MAKLUM BALAS
    </div>

    <div class="header-laporan">
        <img src="{{ public_path('images/logo.png') }}" alt="Jata Negara">
        <h4>PRESTASI KERANGKA KEBERHASILAN PROGRAM PENGURUSAN (PPUUN)<br>
        SEHINGGA 31 DISEMBER {{ $metadata['tahun'] }}</h4>
    </div>

    {{-- BUKA TABLE PERTAMA --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" style="width: 40%;">OUTCOME & KPI</th>
                <th style="width: 10%;">Sasaran<br>Tahunan</th>
                <th colspan="4">Pencapaian Suku Tahun</th>
                <th style="width: 25%;">Catatan</th>
            </tr>
            <tr>
                <th style="width: 5%;">Bil.</th>
                <th>Penerangan / Peratusan</th>
                <th>(%)</th>
                <th style="width: 6%;">1<br>(25%)</th>
                <th style="width: 6%;">2<br>(50%)</th>
                <th style="width: 6%;">3<br>(75%)</th>
                <th style="width: 6%;">4<br>(100%)</th>
                <th>Perincian</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $outcomeId => $records)
            @php 
                $record = $records[0]; 
                $sasaran = $record->sasaran_tahunan; 
                $cleanId = trim(strtoupper($outcomeId));
                $tajukOutcome = $outcomeTitles[$cleanId] ?? $outcomeId;
            @endphp
            
            {{-- ========================================================== --}}
            {{-- 🔥 LOGIC PAGE BREAK KHAS UNTUK OUTCOME 3 🔥 --}}
            {{-- ========================================================== --}}
            @if($cleanId == 'OUTCOME 3')
                {{-- 1. Tutup Table Lama --}}
                </tbody>
                </table>

                {{-- 2. Paksa Masuk Page Baru --}}
                <div style="page-break-before: always;"></div>

                {{-- 3. Buka Table Baru & Ulang Header (Supaya Kemas) --}}
                <table>
                    <thead>
                        <tr>
                            <th colspan="2" style="width: 40%;">OUTCOME & KPI</th>
                            <th style="width: 10%;">Sasaran<br>Tahunan</th>
                            <th colspan="4">Pencapaian Suku Tahun</th>
                            <th style="width: 25%;">Catatan</th>
                        </tr>
                        <tr>
                            <th style="width: 5%;">Bil.</th>
                            <th>Penerangan / Peratusan</th>
                            <th>(%)</th>
                            <th style="width: 6%;">1<br>(25%)</th>
                            <th style="width: 6%;">2<br>(50%)</th>
                            <th style="width: 6%;">3<br>(75%)</th>
                            <th style="width: 6%;">4<br>(100%)</th>
                            <th>Perincian</th>
                        </tr>
                    </thead>
                    <tbody>
            @endif
            {{-- ========================================================== --}}

            <tr class="outcome-row">
                <td colspan="8">{{ $cleanId }}: {{ $tajukOutcome }}</td>
            </tr>
            
            <tr>
                <td>KPI</td>
                <td class="text-left">{{ $record->kpi_desc }}</td>
                <td>{{ $sasaran }}%</td>
                <td>{{ $record->suku_1 }}</td>
                <td>{{ $record->suku_2 }}</td>
                <td>{{ $record->suku_3 }}</td>
                <td>{{ $record->suku_4 }}</td>
                <td class="text-left">
                    @if(is_array($record->catatan_data))
                        <ul style="margin: 0; padding-left: 15px;">
                        @foreach($record->catatan_data as $key => $value) 
                            <li>{{ $key }}: <strong>{{ $value }}</strong></li> 
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
            </tr>

            <tr>
                <td></td>
                <td class="text-left text-bold">Pencapaian (%)</td>
                <td></td>
                <td>{{ $sasaran > 0 ? number_format($record->suku_1 / $sasaran * 100, 0) : 0 }}%</td>
                <td>{{ $sasaran > 0 ? number_format($record->suku_2 / $sasaran * 100, 0) : 0 }}%</td>
                <td>{{ $sasaran > 0 ? number_format($record->suku_3 / $sasaran * 100, 0) : 0 }}%</td>
                <td>{{ $sasaran > 0 ? number_format($record->suku_4 / $sasaran * 100, 0) : 0 }}%</td>
                <td style="background-color: #f2f2f2;"></td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="padding: 20px;">Tiada rekod PPUUN untuk tahun {{ $metadata['tahun'] }}.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <table class="sig-table">
            <tr>
                <td width="50%">
                    Disediakan oleh:<br><br>
                    <div class="sig-box">
                        @if(!empty($sainEo)) <img src="{{ $sainEo }}" class="sig-img" alt="Sain EO"> @endif
                    </div>
                    <div class="sig-line"></div>
                    <strong>Urusetia Program:</strong><br>
                    {{ strtoupper($eo->name ?? 'BELUM DITETAPKAN') }}<br><br>
                    <strong>Tarikh:</strong> {{ date('d F Y') }}
                </td>
                <td width="50%" style="padding-left: 30px;">
                    Disahkan oleh:<br><br>
                    <div class="sig-box">
                        @if(!empty($sainYb)) <img src="{{ $sainYb }}" class="sig-img" alt="Sain YB"> @endif
                    </div>
                    <div class="sig-line"></div>
                    <strong>Ketua Program:</strong><br>
                    {{ strtoupper($yb->name ?? 'BELUM DITETAPKAN') }}<br><br>
                    <strong>Tarikh:</strong> {{ date('d F Y') }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>