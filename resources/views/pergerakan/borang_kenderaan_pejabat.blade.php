<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>BORANG PERMOHONAN KENDERAAN JABATAN</title>
    <style>
        @page { margin: 40px 50px; } 
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.2; text-transform: uppercase; }
        .logo-container { text-align: center; margin-bottom: 5px; }
        .logo { max-width: 100px; height: auto; display: inline-block; }
        .header { text-align: center; font-weight: bold; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid black; padding: 4px; vertical-align: top; }
        .no-border { border: none !important; }
        .section-title { background-color: #f0f0f0; font-weight: bold; }
        .label { width: 120px; font-weight: bold; }
        .label-pemandu { width: 70px; font-weight: bold; }
        .signature-box { height: 70px; vertical-align: bottom; text-align: center; font-size: 10pt; }
        .signature-img { height: 50px; width: auto; margin-top: 5px; }
        .text-center { text-align: center; }
        .strike { text-decoration: line-through; color: #999; }
        .ketua-jabatan-signature { padding-right: 5px; padding-top: 15px; font-size: 10pt; }
        .kelulusan-status { margin: 10px 0; font-size: 12pt; }
    </style>
</head>
<body>

    <div class="logo-container"><img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo"></div>
    <div class="header">BORANG PENGGUNAAN KENDERAAN JABATAN<br>PEJABAT PENASIHAT UNDANG-UNDANG<br>NEGERI {{ $pergerakan->user->negeri ?? '_________________' }}</div>

    {{-- A. BUTIRAN PEMOHON --}}
    <table>
        <tr><td colspan="2" class="section-title">A. BUTIRAN PEMOHON</td></tr>
        <tr><td class="label">1. NAMA PEMOHON</td><td>{{ $pergerakan->user->name ?? '-' }}</td></tr>
        <tr><td class="label">2. JAWATAN</td><td>{{ $pergerakan->user->nama_jawatan ?? '-' }}</td></tr>
        <tr><td class="label">3. TARIKH</td><td>{{ \Carbon\Carbon::parse($pergerakan->created_at)->format('d/m/Y') }}</td></tr>
    </table>

    {{-- B. BUTIRAN PERMOHONAN --}}
    <table>
        <tr><td colspan="4" class="section-title">B. BUTIRAN PERMOHONAN</td></tr>
        <tr><td class="label">4. TARIKH PERMOHONAN</td><td colspan="3">{{ \Carbon\Carbon::parse($pergerakan->created_at)->format('d/m/Y') }}</td></tr>
        <tr><td class="label">5. NO. KENDERAAN</td><td width="30%">{{ $pergerakan->no_kenderaan ?? '_________________' }}</td><td class="label-pemandu">6. PEMANDU</td><td>{{ $pergerakan->nama_pemandu ?? '_________________' }}</td></tr>
        <tr><td class="label">7. TUJUAN PENGGUNAAN</td><td colspan="3">{{ $pergerakan->tujuan_penggunaan ?? '-' }}</td></tr>
        <tr><td class="label">8. DESTINASI</td><td colspan="3">{{ $pergerakan->destinasi ?? '-' }}</td></tr>
        <tr><td class="label">9. TARIKH MULA GUNA</td><td>{{ \Carbon\Carbon::parse($pergerakan->tarikh_mula)->format('d/m/Y') }}</td><td class="label">MASA</td><td>{{ $pergerakan->masa_mula ?? '_________________' }}</td></tr>
        <tr><td class="label">10. TARIKH AKHIR GUNA</td><td>{{ \Carbon\Carbon::parse($pergerakan->tarikh_akhir)->format('d/m/Y') }}</td><td class="label">MASA</td><td>{{ $pergerakan->masa_akhir ?? '_________________' }}</td></tr>
        <tr>
            <td colspan="4" class="signature-box">
                @if(isset($sig_applicant)) <img src="{{ $sig_applicant }}" class="signature-img" alt="Tandatangan Pemohon"> @endif
                <br>_______________________________________<br>{{ $pergerakan->user->name ?? 'NAMA PEMOHON' }}<br>(TANDATANGAN PEMOHON)
            </td>
        </tr>
    </table>

    {{-- C. ULASAN PENTADBIRAN --}}
    <table>
        <tr><td class="section-title">C. ULASAN BAHAGIAN PENTADBIRAN DAN KEWANGAN</td></tr>
        <tr>
            <td style="padding: 10px 20px;">
                <p>PERMOHONAN TUAN/PUAN UNTUK TUJUAN DI ATAS:</p>
                <div class="text-center kelulusan-status">
                    @if($pergerakan->status_yb === 'Lulus') DILULUSKAN / <span class="strike">TIDAK DILULUSKAN</span>
                    @elseif($pergerakan->status_yb === 'Tolak') <span class="strike">DILULUSKAN</span> / TIDAK DILULUSKAN
                    @else DILULUSKAN / TIDAK DILULUSKAN @endif
                </div>
                <br>
                <table class="no-border" style="width: 100%">
                    <tr><td class="no-border" width="100px"><strong>CATATAN :</td><td class="no-border" style="border-bottom: 1px dotted black !important;">{{ $pergerakan->catatan_yb ?? ($pergerakan->catatan_cc ?? 'TIADA CATATAN') }}</td></tr>
                    <tr><td class="no-border"><strong>TARIKH :</td><td class="no-border" style="border-bottom: 1px dotted black !important;">{{ $pergerakan->updated_at ? \Carbon\Carbon::parse($pergerakan->updated_at)->format('d/m/Y') : date('d/m/Y') }}</td></tr>
                </table>
                <div class="text-center ketua-jabatan-signature">
                    @if(isset($sig_cc)) <img src="{{ $sig_cc }}" class="signature-img" alt="Tandatangan CC"> @endif
                    <br>_______________________________________<br><strong>{{ $cc_name ?? 'NAMA CC' }}</strong><br>(TANDATANGAN BAHAGIAN PENTADBIRAN)<br>{{ $cc_jawatan ?? 'JAWATAN CC' }}
                </div>
            </td>
        </tr>
    </table>

  {{-- 🔥 PAGE 2: LAMPIRAN (FIXED: PAGE JUMP ISSUE) 🔥 --}}
    @if(!empty($pergerakan->lampiran))
        
        {{-- Paksa masuk page baru --}}
        <div style="page-break-before: always;"></div>

        {{-- 
           TRIK: Kita guna margin negatif sikit kat atas
           supaya dia naik tinggi dan tak tolak gambar ke page sebelah 
        --}}
        <div style="text-align: center; margin-top: -20px; padding-top: 0;">
            
            <h4 style="text-decoration: underline; margin-bottom: 10px;">LAMPIRAN DOKUMEN SOKONGAN</h4>
            
            @php
                $pathSebenar = storage_path('app/public/' . $pergerakan->lampiran);
            @endphp

            @if(file_exists($pathSebenar))
                @php
                    $type = pathinfo($pathSebenar, PATHINFO_EXTENSION);
                    $data = file_get_contents($pathSebenar);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                @endphp
                
                {{-- 
                   PENTING: Set max-height lebih kecil (contoh 800px atau 750px) 
                   supaya dia muat dalam satu page A4 (tolak header/footer) 
                --}}
                <img src="{{ $base64 }}" style="max-width: 98%; max-height: 750px; border: 1px solid #000; display: block; margin: 0 auto;">
            
            @else
                <p style="color:red;">RALAT: Fail gambar tidak dijumpai.</p>
            @endif
        </div>
    @endif

</body>
</html>