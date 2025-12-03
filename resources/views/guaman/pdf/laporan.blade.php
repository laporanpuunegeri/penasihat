<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        /* CSS ringkas untuk DomPDF */
        body { font-family: Arial, sans-serif; font-size: 9pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3 { margin: 0; }
        .table-container { page-break-inside: auto; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
        .category-header { 
            font-weight: bold; 
            background-color: #f0f0f0; 
            padding: 6px; 
            margin-top: 10px; 
            border: 1px solid #000;
        }
        .text-bold { font-weight: bold; }
        .small-text { font-size: 8pt; line-height: 1.2; }
    </style>
</head>
<body>
    <div class="header">
        <h3 class="text-bold">STATUS KES YANG DIKENDALIKAN OLEH UNIT GUAMAN MELAKA</h3>
        <p>Dicetak pada: {{ $currentDate }}</p>
    </div>

    @foreach ($groupedCases as $kod => $cases)
        @php
            // Dapatkan tajuk panjang berdasarkan kod (cth: KOD 01)
            $categoryInfo = $categories[$kod] ?? ['title' => 'Kategori Tidak Dikenali', 'route_kategori' => ''];
        @endphp

        <div class="category-header">
            KOD {{ $kod }} - {{ $categoryInfo['title'] }} 
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        {{-- MENGASINGKAN KATEGORI KES DAN STATUS --}}
                        <th style="width: 5%;">NO.</th>
                        <th style="width: 30%;">RUJUKAN FAIL</th>
                        <th style="width: 30%;">RUJUKAN MAHKAMAH / PIHAK BERLAWANAN</th>
                        <th style="width: 15%;">KATEGORI KES</th>  {{-- LAJUR BARU --}}
                        <th style="width: 20%;">STATUS</th>          {{-- LAJUR BARU --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cases as $index => $case)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            
                            {{-- LAJUR RUJUKAN FAIL (30%) --}}
                           <td>
                                <span class="text-bold">{{ $case->rujukan_fail }}</span>
                                
                                {{-- Menggunakan DIV dengan margin bawah untuk jarak --}}
                                <div style="margin-top: 8px;"> 
                                    @if($case->tarikh_buka)
                                        <div class="small-text">Buka pada: {{ \Carbon\Carbon::parse($case->tarikh_buka)->format('d.m.Y') }}</div>
                                    @endif
                                </div>
                                
                                <div style="margin-top: 8px;"> 
                                @if($case->kendalian_oleh)
                                    <div class="small-text">Kendalian: {{ $case->kendalian_oleh }}</div>
                                @endif
                            </td>
                            
                            {{-- LAJUR RUJUKAN MAHKAMAH / PIHAK BERLAWANAN (30%) --}}
                            <td>
                                {{-- Rujukan Mahkamah --}}
                                <span class="text-bold">{{ $case->mahkamah }}</span>
                                @if($case->rujukan_mahkamah)
                                    <br><span class="small-text">Ruj: {{ $case->rujukan_mahkamah }}</span>
                                @endif
                                
                                <br><br>
                                {{-- Pihak Berlawanan (Di bawah Rujukan Mahkamah) --}}
                                <span class="text-bold">Pihak Berlawanan:</span>
                                <br><span class="small-text">{{ $case->pihak_berlawanan }}</span>
                            </td>

                            {{-- LAJUR KATEGORI KES (15%) --}}
                            <td>
                                {{ $case->kategori_kes }}
                            </td>

                            {{-- LAJUR STATUS (20%) --}}
                            <td>
                                {{ $case->status_kes }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

</body>
</html>