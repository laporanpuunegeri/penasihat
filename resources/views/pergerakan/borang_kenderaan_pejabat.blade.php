<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>BORANG PERMOHONAN KENDERAAN JABATAN</title>
    <style>
        /* 1. MARGIN KEKAL KECIL SUPAYA MUAT 1 PAGE */
        @page { margin: 30px 40px; } 
        
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 10pt; 
            line-height: 1.1; 
            text-transform: uppercase; 
        }

        .logo-container { text-align: center; margin-bottom: 5px; }
        .logo { max-width: 80px; height: auto; display: inline-block; } 
        .header { text-align: center; font-weight: bold; margin-bottom: 10px; font-size: 11pt; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        
        /* Padding cell "Slim" */
        th, td { border: 1px solid black; padding: 3px; vertical-align: top; }
        
        .no-border { border: none !important; }
        .section-title { background-color: #f0f0f0; font-weight: bold; font-size: 10pt; }
        .label { width: 130px; font-weight: bold; }
        .label-pemandu { width: 70px; font-weight: bold; }
        
        /* 2. BESARKAN RUANG SAIN SUPAYA TAK SEMPIT */
        .signature-box { 
            height: 75px; 
            vertical-align: bottom; 
            text-align: center; 
            font-size: 9pt; 
            padding-bottom: 5px;
        }
        
        .signature-img { height: 55px; width: auto; margin-bottom: 5px; }
        
        .text-center { text-align: center; }
        .strike { text-decoration: line-through; color: #999; }

        /* Style khas untuk Bahagian C & D */
        .ketua-jabatan-signature { 
            padding-top: 25px; /* Jarakkan dari teks atas */
            padding-bottom: 10px; 
            text-align: center;
            font-size: 9pt; 
        }

        .kelulusan-status { margin: 10px 0; font-weight: bold; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="logo-container">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
    </div>
    <div class="header">
        BORANG PENGGUNAAN KENDERAAN JABATAN<br>
        PEJABAT PENASIHAT UNDANG-UNDANG<br>
        NEGERI {{ $pergerakan->user->negeri ?? '_________________' }}
    </div>

    {{-- A. BUTIRAN PEMOHON --}}
    <table>
        <tr><td colspan="2" class="section-title">A. BUTIRAN PEMOHON</td></tr>
        <tr><td class="label">1. NAMA PEMOHON</td><td>{{ $pergerakan->user->name ?? '-' }}</td></tr>
        <tr><td class="label">2. JAWATAN</td><td>{{ $pergerakan->user->nama_jawatan ?? '-' }}</td></tr>
        <tr><td class="label">3. TARIKH</td><td>{{ \Carbon\Carbon::parse($pergerakan->created_at)->format('d/m/Y') }}</td></tr>
    </table>
<br>
    {{-- B. BUTIRAN PERMOHONAN --}}
    <table>
        <tr><td colspan="4" class="section-title">B. BUTIRAN PERMOHONAN</td></tr>
        <tr><td class="label">4. TARIKH MOHON</td><td colspan="3">{{ \Carbon\Carbon::parse($pergerakan->created_at)->format('d/m/Y') }}</td></tr>
        <tr>
            <td class="label">5. NO. KENDERAAN</td><td width="30%">{{ $pergerakan->no_kenderaan ?? '__________' }}</td>
            <td class="label-pemandu">6. PEMANDU</td><td>{{ $pergerakan->nama_pemandu ?? '__________' }}</td>
        </tr>
        <tr><td class="label">7. TUJUAN</td><td colspan="3">{{ $pergerakan->tujuan_penggunaan ?? '-' }}</td></tr>
        <tr><td class="label">8. DESTINASI</td><td colspan="3">{{ $pergerakan->destinasi ?? '-' }}</td></tr>
        <tr>
            <td class="label">9. MULA</td><td>{{ \Carbon\Carbon::parse($pergerakan->tarikh_mula)->format('d/m/Y') }}</td>
            <td class="label">MASA</td><td>{{ $pergerakan->masa_mula ?? '__________' }}</td>
        </tr>
        <tr>
            <td class="label">10. TAMAT</td><td>{{ \Carbon\Carbon::parse($pergerakan->tarikh_akhir)->format('d/m/Y') }}</td>
            <td class="label">MASA</td><td>{{ $pergerakan->masa_akhir ?? '__________' }}</td>
        </tr>
        <tr>
            <td colspan="4" class="signature-box"><br><br><br><br>
                @if(isset($sig_applicant)) <img src="{{ $sig_applicant }}" class="signature-img" alt="Sain Pemohon"> @endif
                <br>_______________________________________<br>
                {{ $pergerakan->user->name ?? 'NAMA PEMOHON' }}<br>(TANDATANGAN PEMOHON)
            </td>
        </tr>
    </table>
<br>
    {{-- C. ULASAN PENTADBIRAN (Guna Logic status_cc) --}}
    <table>
        <tr><td class="section-title">C. ULASAN BAHAGIAN PENTADBIRAN</td></tr>
        <tr>
            <td style="padding: 5px;">
                PERMOHONAN TUAN/PUAN UNTUK TUJUAN DI ATAS:
                <div class="text-center kelulusan-status">
                     {{-- 🔥 WORDING: SOKONG / TIDAK DISOKONG --}}
                     @if($pergerakan->status_cc == 'Sokong') SOKONG / <span class="strike">TIDAK DISOKONG</span>
                     @elseif($pergerakan->status_cc == 'Tolak') <span class="strike">SOKONG</span> / TIDAK DISOKONG
                     @else SOKONG / TIDAK DISOKONG @endif
                </div>
                
                <table class="no-border" style="width: 100%; margin: 0;">
                    <tr>
                        <td class="no-border" width="80px"><strong>CATATAN:</strong></td>
                        <td class="no-border" style="border-bottom: 1px dotted black !important;">{{ $pergerakan->catatan_cc ?? '' }}</td>
                    </tr>
                </table>
                
                <div class="ketua-jabatan-signature">
                    @if(isset($sig_cc)) <img src="{{ $sig_cc }}" class="signature-img" alt="Sain CC"> @endif
                    <br>_______________________________________<br>
                    <strong>{{ $cc_name ?? 'PEGAWAI PENTADBIRAN' }}</strong><br>
                    (TANDATANGAN PENTADBIRAN)
                </div>
            </td>
        </tr>
    </table>
<br>
    {{-- D. KEPUTUSAN YB (Guna Logic status_yb) --}}
    <table>
        <tr><td class="section-title">D. KEPUTUSAN KETUA JABATAN / YB</td></tr>
        <tr>
            <td style="padding: 5px;">
                PERMOHONAN TUAN/PUAN UNTUK TUJUAN DI ATAS:
                <div class="text-center kelulusan-status">
                    {{-- 🔥 WORDING: DILULUSKAN / TIDAK DILULUSKAN --}}
                    @if($pergerakan->status_yb === 'Lulus') DILULUSKAN / <span class="strike">TIDAK DILULUSKAN</span>
                    @elseif($pergerakan->status_yb === 'Tolak') <span class="strike">DILULUSKAN</span> / TIDAK DILULUSKAN
                    @else DILULUSKAN / TIDAK DILULUSKAN @endif
                </div>

                <div class="ketua-jabatan-signature">
                    @if(isset($sig_yb)) <img src="{{ $sig_yb }}" class="signature-img" alt="Sain YB"> @endif
                    <br>_______________________________________<br>
                    <strong>{{ $namaYB ?? 'YANG BERHORMAT' }}</strong><br>
                    (TANDATANGAN YB PENASIHAT)
                </div>
            </td>
        </tr>
    </table>

    {{-- 🔥 LAMPIRAN (VERSI HYBRID: BASE64 & FIZIKAL) 🔥 --}}
    @if(!empty($pergerakan->lampiran))
        <div style="page-break-before: always;"></div>
        <div style="text-align: center; margin-top: 10px;">
            <h4 style="text-decoration: underline;">LAMPIRAN</h4>
            
            @if(str_contains($pergerakan->lampiran, 'data:image'))
                {{-- KES 1: DATA BARU (Base64 terus dari Database) --}}
                <img src="{{ $pergerakan->lampiran }}" style="max-width: 95%; max-height: 900px; border: 1px solid #000;">
            
            @else
                {{-- KES 2: DATA LAMA (Cari fail fizikal - kalau ada) --}}
                @php
                    $pathSebenar = storage_path('app/public/' . $pergerakan->lampiran);
                @endphp
                
                @if(file_exists($pathSebenar))
                    @php
                        $type = pathinfo($pathSebenar, PATHINFO_EXTENSION);
                        $data = file_get_contents($pathSebenar);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    @endphp
                    <img src="{{ $base64 }}" style="max-width: 95%; max-height: 900px; border: 1px solid #000;">
                @endif
            @endif
        </div>
    @endif

</body>
</html>