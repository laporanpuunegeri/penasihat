<!DOCTYPE html>
<html>
<head>
    <title>Kalendar Pergerakan Keseluruhan ({{ $bulan_text ?? 'Tiada Bulan' }})</title>
    <style>
        /* Tetapan asas untuk A4 Landscape (297mm x 210mm) */
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 8pt; 
            /* Menggunakan margin yang sangat kecil untuk memaksimumkan ruang cetakan */
            margin: 5mm; 
            padding: 0; 
            width: 287mm; /* (297 - 10mm margin) */
        }
        
        /* Mengelakkan pemotongan halaman dalam blok tandatangan */
        .signature-container {
            page-break-inside: avoid;
        }

        /* HEADER */
        .header { text-align: center; margin-bottom: 5px; } /* Kurangkan margin */
        .header h1 { margin: 0; font-size: 12pt; }
        .header h2 { margin-top: 1mm; font-size: 10pt; font-weight: normal; color: #555; }
        .header h3 { font-size: 9pt; margin-top: 1mm; }
        
        /* KALENDAR GRID */
        .calendar-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            margin-bottom: 10px; 
        }
        .calendar-table th { 
            border: 1px solid #aaa; 
            padding: 2px; 
            text-align: center; 
            font-size: 8pt;
            background-color: #0f172a; 
            color: white; 
        }
        .calendar-table td { 
            border: 1px solid #aaa; 
            padding: 2px; 
            vertical-align: top; 
            height: 65px; /* Kurangkan ketinggian sel secara drastik */
            font-size: 7pt; 
            line-height: 1.1;
        }
        
        .day-label { 
            font-size: 8pt; 
            font-weight: bold; 
            color: #333;
            background-color: #f1f1f1;
            padding: 1px 3px;
            margin-bottom: 2px; /* Kurangkan margin */
            display: block;
        }
        
        .event { 
            margin-bottom: 1px; /* Kurangkan margin */
            padding: 0 2px; /* Kurangkan padding */
            border-radius: 1px; 
            border-left: 2px solid #06b6d4; 
            background-color: #e0f7fa; 
            overflow: hidden; 
        }
        .event-title { font-weight: bold; font-size: 6.5pt; line-height: 1.1; color: #007bff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .event-details { font-size: 6pt; color: #555; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* TANDATANGAN */
        .signature-container {
            width: 100%;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #aaa;
            display: block; 
            text-align: right; 
        }
        
        .signature-box-right {
            width: 45%; /* Pastikan ia muat dalam skrin */
            margin-left: auto; 
            text-align: center;
            padding: 5px 0;
            display: inline-block;
        }
        
        .line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 3mm auto 1mm auto; /* Kurangkan margin */
        }
        .clear { clear: both; }
        .print-date { text-align: right; margin-top: 5px; font-size: 7pt; }
        
        .signature-img-container {
            height: 40px; /* Kurangkan ketinggian ruang tandatangan */
            line-height: 40px;
            vertical-align: middle;
        }
        .signature-box-right span {
            line-height: 1.1; /* Kurangkan jarak baris */
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KALENDAR PERGERAKAN PEGAWAI (KESELURUHAN)</h1>
        <h2>Jabatan Peguam Negara</h2>
        <h3 style="font-size: 9pt; margin-top: 5px;">Bulan: {{ $bulan_text ?? 'TIADA DATA BULAN' }}</h3>
    </div>

    @php
        use Carbon\Carbon;
        // Dapatkan tarikh mula dan akhir bulan untuk menjana grid kalendar
        $current = $start_of_month->copy()->startOfWeek(Carbon::SUNDAY); 
        $end = $end_of_month->copy()->endOfWeek(Carbon::SATURDAY);
    @endphp

    {{-- Jadual Kalendar (Grid View) --}}
    <table class="calendar-table">
        <thead>
            <tr>
                <th style="width: 14.28%;">Ahad</th>
                <th style="width: 14.28%;">Isnin</th>
                <th style="width: 14.28%;">Selasa</th>
                <th style="width: 14.28%;">Rabu</th>
                <th style="width: 14.28%;">Khamis</th>
                <th style="width: 14.28%;">Jumaat</th>
                <th style="width: 14.28%;">Sabtu</th>
            </tr>
        </thead>
        <tbody>
            @while ($current->lte($end))
                @if ($current->dayOfWeek === Carbon::SUNDAY)
                    <tr>
                @endif
                
                @php
                    $isCurrentMonth = $current->month == $start_of_month->month;
                    $dateKey = $current->format('Y-m-d');
                    $daily_events = $pergerakan_by_day[$dateKey] ?? [];
                @endphp
                
                <td style="{{ $isCurrentMonth ? '' : 'background-color: #f9f9f9; color: #ccc;' }}">
                    <span class="day-label">{{ $current->format('j') }}</span>
                    
                    @foreach ($daily_events as $event)
                        <div class="event">
                            <div class="event-title">{{ $event->user->name ?? 'Pengguna' }}</div>
                            <div class="event-details">{{ $event->tujuan_penggunaan }}</div>
                            <div class="event-details">({{ $event->masa_mula ?? 'Sepanjang Hari' }})</div>
                        </div>
                    @endforeach
                </td>

                @if ($current->dayOfWeek === Carbon::SATURDAY)
                    </tr>
                @endif
                
                @php $current->addDay(); @endphp
            @endwhile
        </tbody>
    </table>

    {{-- RUANG TANDATANGAN PENGESAHAN (HANYA YB) --}}
    <div class="signature-container">
        
        <div class="signature-box-right">
            <span style="font-weight: bold; font-size: 9pt;">Disahkan Oleh YB:</span>
            
            <div class="signature-img-container">
                @if(isset($sig_yb)) 
                    <img src="{{ $sig_yb }}" style="height: 40px; max-width: 200px; object-fit: contain;">
                @else
                    <div style="height: 40px;"></div> 
                @endif
            </div>

            <div class="line"></div>
            
            <span style="font-weight: bold; font-size: 8.5pt;">{{ $namaYB ?? 'YANG BERHORMAT' }}</span><br>
            <span style="font-size: 8pt;">{{ $jawatanYB ?? 'Jawatan' }}</span><br>
            <span style="font-size: 7.5pt;">Pejabat Penasihat Undang-Undang Negeri</span>
        </div>
        
        <div class="clear"></div>
    </div>
    
    <div class="print-date">
        Dicetak pada: {{ $tarikh_cetak ?? \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
    </div>

    {{-- Senarai Pergerakan Penuh (Dikeluarkan sepenuhnya atau diletakkan di hujung jika data tidak penting) --}}
    @if (false && $pergerakan_by_day->count() > 0)
        <!-- Kod senarai diletakkan sebagai false untuk memastikan ia tidak menolak tandatangan ke halaman kedua -->
        ...
    @endif

</body>
</html>