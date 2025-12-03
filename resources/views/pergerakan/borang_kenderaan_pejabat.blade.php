<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>BORANG PERMOHONAN KENDERAAN JABATAN</title>
    <style>
        /* Tetapan Asas dan Global Uppercase */
        @page { margin: 40px 50px; } 
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.2; 
            text-transform: uppercase; 
        }
        /* Style untuk Kontainer Logo */
        .logo-container {
            text-align: center; 
            margin-bottom: 5px;
        }
        .logo {
            max-width: 100px; 
            height: auto;
            display: inline-block; 
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px; 
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px; 
        }
        th, td {
            border: 1px solid black;
            padding: 4px; 
            vertical-align: top;
        }
        .no-border {
            border: none !important;
        }
        .section-title {
            background-color: #f0f0f0; 
            font-weight: bold;
        }
        .label {
            width: 120px; 
            font-weight: bold;
        }
        .label-pemandu {
            width: 70px; 
            font-weight: bold;
        }
        .signature-box {
            height: 70px; 
            vertical-align: bottom;
            text-align: center;
            font-size: 10pt;
        }
        /* Gaya Tandatangan Imej */
        .signature-img {
            height: 50px; 
            width: auto;
            margin-top: 5px;
        }
        .text-center {
            text-align: center;
        }
        .strike {
            text-decoration: line-through;
            color: #999;
        }
        .ketua-jabatan-signature {
            padding-right: 5px;
            padding-top: 15px;
            font-size: 10pt;
        }
        .kelulusan-status {
             margin: 10px 0; 
             font-size: 12pt; 
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="logo-container">
        <img src="{{ public_path('images/logo.png') }}" alt="Jata Negara" class="logo">
    </div>
    
    <div class="header">
        BORANG PENGGUNAAN KENDERAAN JABATAN<br>
        PEJABAT PENASIHAT UNDANG-UNDANG<br>
        NEGERI {{ $pergerakan->user->negeri ?? '_________________' }} 
    </div>

    {{-- SEKSYEN A: BUTIRAN PEMOHON --}}
    <table>
        <tr>
            <td colspan="2" class="section-title">A. BUTIRAN PEMOHON</td>
        </tr>
        <tr>
            <td class="label">1. NAMA PEMOHON</td>
            <td>{{ $pergerakan->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">2. JAWATAN</td>
            <td>{{ $pergerakan->user->nama_jawatan ?? '-' }}</td> 
        </tr>
        <tr>
            <td class="label">3. TARIKH</td>
            <td>{{ \Carbon\Carbon::parse($pergerakan->created_at)->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- SEKSYEN B: BUTIRAN PERMOHONAN --}}
    <table>
        <tr>
            <td colspan="4" class="section-title">B. BUTIRAN PERMOHONAN</td>
        </tr>
        
        {{-- Baris: Tarikh Permohonan --}}
        <tr>
            <td class="label">4. TARIKH PERMOHONAN</td>
            <td colspan="3">{{ \Carbon\Carbon::parse($pergerakan->created_at)->format('d/m/Y') }}</td>
        </tr>

        {{-- Baris: Kenderaan & Pemandu (Diisi oleh CC) --}}
        <tr>
            <td class="label">5. NO. KENDERAAN</td>
            <td width="30%">{{ $pergerakan->no_kenderaan ?? '_________________' }}</td>
            <td class="label-pemandu">6. PEMANDU</td> 
            <td>{{ $pergerakan->nama_pemandu ?? '_________________' }}</td>
        </tr>

        {{-- Baris: Tujuan --}}
        <tr>
            <td class="label">7. TUJUAN PENGGUNAAN</td>
            <td colspan="3">{{ $pergerakan->tujuan_penggunaan ?? '-' }}</td>
        </tr>

        {{-- Baris: Destinasi --}}
        <tr>
            <td class="label">8. DESTINASI</td>
            <td colspan="3">{{ $pergerakan->destinasi ?? '-' }}</td>
        </tr>

        {{-- Baris: Tarikh Mula Guna & MASA --}}
        <tr>
            <td class="label">9. TARIKH MULA GUNA</td>
            <td>{{ \Carbon\Carbon::parse($pergerakan->tarikh_mula)->format('d/m/Y') }}</td>
            <td class="label">MASA</td>
            <td>{{ $pergerakan->masa_mula ?? '_________________' }}</td> 
        </tr>

        {{-- Baris: Tarikh Akhir Guna & MASA --}}
        <tr>
            <td class="label">10. TARIKH AKHIR GUNA</td>
            <td>{{ \Carbon\Carbon::parse($pergerakan->tarikh_akhir)->format('d/m/Y') }}</td>
            <td class="label">MASA</td>
            <td>{{ $pergerakan->masa_akhir ?? '_________________' }}</td>
        </tr>

        {{-- Tandatangan Pemohon --}}
        <tr>
            <td colspan="4" class="signature-box">
                {{-- 🔥 Tandatangan Pemohon (Menggunakan $sig_applicant dari Controller) --}}
                @if(isset($sig_applicant))
                    <img src="{{ $sig_applicant }}" class="signature-img" alt="Tandatangan Pemohon">
                @endif
                <br>
                _______________________________________<br>
                {{ $pergerakan->user->name ?? 'NAMA PEMOHON' }}<br>
                (TANDATANGAN PEMOHON)
            </td>
        </tr>
    </table>

    {{-- SEKSYEN C: ULASAN BAHAGIAN PENTADBIRAN --}}
    <table>
        <tr>
            <td class="section-title">C. ULASAN BAHAGIAN PENTADBIRAN DAN KEWANGAN</td>
        </tr>
        <tr>
            <td style="padding: 10px 20px;">
                <p>PERMOHONAN TUAN/PUAN UNTUK TUJUAN DI ATAS:</p>
                
                {{-- Logik potong perkataan berdasarkan status YB --}}
                <div class="text-center kelulusan-status">
                    @if($pergerakan->status_yb === 'Lulus')
                        DILULUSKAN / <span class="strike">TIDAK DILULUSKAN</span>
                    @elseif($pergerakan->status_yb === 'Tolak' || $pergerakan->status_yb === 'Ditolak Automatik (Oleh CC)')
                        <span class="strike">DILULUSKAN</span> / TIDAK DILULUSKAN
                    @else
                        DILULUSKAN / TIDAK DILULUSKAN
                    @endif
                </div>

                <br>
                
                <table class="no-border" style="width: 100%">
                    <tr>
                        <td class="no-border" width="100px"><strong>CATATAN :</td>
                        <td class="no-border" style="border-bottom: 1px dotted black !important;">
                             {{ $pergerakan->catatan_yb ?? ($pergerakan->catatan_cc ?? 'TIADA CATATAN') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="no-border"><strong>TARIKH :</td>
                        <td class="no-border" style="border-bottom: 1px dotted black !important;">
                            {{ $pergerakan->updated_at ? \Carbon\Carbon::parse($pergerakan->updated_at)->format('d/m/Y') : date('d/m/Y') }}
                        </td>
                    </tr>
                </table>
                
                <div class="text-center ketua-jabatan-signature">
                    
                    {{-- Ruang Tandatangan CC/Pentadbiran (Menggunakan $sig_cc dari Controller) --}}
                    @if(isset($sig_cc))
                         <img src="{{ $sig_cc }}" class="signature-img" alt="Tandatangan CC">
                    @endif

                    <br>
                    _______________________________________<br>
                    <strong>{{ $cc_name ?? 'NAMA CC' }}</strong><br>
                    (TANDATANGAN BAHAGIAN PENTADBIRAN)<br>
                    {{ $cc_jawatan ?? 'JAWATAN CC' }}
                </div>
            </td>
        </tr>
    </table>
    
</body>
</html>