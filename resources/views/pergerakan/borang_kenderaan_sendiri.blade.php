<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Permohonan Kenderaan Sendiri</title>
    <style>
        /* Tetapan Halaman Utama */
        @page { margin: 40px 50px; } 
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt; 
            line-height: 1.3; 
            color: #000;
        }

        /* Gaya Logo */
        .logo-container {
            text-align: center; 
            margin-bottom: 10px; 
        }
        .logo {
            max-width: 100px; 
            height: auto;
            display: inline-block; 
        }

        /* Header */
        .header {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px; 
            line-height: 1.2;
            font-size: 11pt;
        }

        /* Content Utama */
        .content {
            text-align: justify;
            margin-bottom: 10px;
        }

        /* Seksyen Umum */
        .section {
            margin-top: 10px; 
            margin-bottom: 10px;
        }

        /* Gaya Input Data */
        .input-data {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px dotted #000;
            padding: 0 5px;
        }

        /* Jadual Susun Atur */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        td {
            vertical-align: top;
            padding: 2px 0; 
        }
        .label-col { width: 120px; }
        .colon-col { width: 20px; text-align: center; }

        /* Garis Pemisah */
        hr.divider {
            border: 0;
            border-top: 1px solid #000;
            margin: 15px 0; 
        }

        /* Lain-lain */
        .strikethrough { text-decoration: line-through; }
        .spacer { height: 10px; } 
        .no-border { border: none !important; }

        /* Gaya Tandatangan */
        .signature-img {
            height: 50px; 
            width: auto;
            margin-bottom: -5px;
            margin-top: -10px;
        }
    </style>
</head>
<body>

    {{-- 1. HEADER --}}
    <div class="logo-container">
        <img src="{{ public_path('images/logo.png') }}" alt="Jata Negara" class="logo">
    </div>
    <div class="header">
        PERMOHONAN MENGGUNAKAN KENDERAAN SENDIRI<br>
        BERKAITAN TUGAS RASMI
    </div>

    {{-- 2. KANDUNGAN UTAMA --}}
    <div class="content">
        <p style="margin-bottom: 5px;">Kepada Ketua Bahagian :</p>

        <p>
            Adalah dengan hormatnya saya memohon untuk menggunakan kenderaan sendiri bagi menjalankan tugas rasmi iaitu
            ke <span class="input-data">{{ $pergerakan->tujuan_penggunaan }}</span>
            @if($pergerakan->destinasi)
            di <span class="input-data">{{ $pergerakan->destinasi }}</span>
            @endif
        </p>

        <p>
            mulai pada 
            <span class="input-data">{{ \Carbon\Carbon::parse($pergerakan->tarikh_mula)->format('d/m/Y') }}</span>
            hingga 
            <span class="input-data">{{ \Carbon\Carbon::parse($pergerakan->tarikh_akhir)->format('d/m/Y') }}</span>.
        </p>

        <p>Sekian, terima kasih.</p>
    </div>

    {{-- 3. TANDATANGAN PEMOHON --}}
    <div class="section">
        <div class="spacer"></div>
        
        <table style="width: 100%; border: none; margin-bottom: 5px;">
            <tr>
                {{-- Label Kiri --}}
                <td class="no-border" style="width: 160px; vertical-align: bottom; padding-bottom: 5px;">
                    Tandatangan Pegawai :
                </td>
                
                {{-- Ruang Gambar Kanan --}}
                <td class="no-border" style="vertical-align: bottom;">
                    @if(isset($sig_applicant))
                        <img src="{{ $sig_applicant }}" class="signature-img" alt="Sain">
                    @else
                        <div style="height: 60px;"></div> 
                    @endif
                </td>
            </tr>
        </table>
        
        {{-- Butiran Pegawai --}}
        <table>
            <tr>
                <td class="label-col">Nama</td>
                <td class="colon-col">:</td>
                <td><span class="input-data">{{ $pergerakan->user->name }}</span></td>
            </tr>
            <tr>
                <td class="label-col">Bahagian</td>
                <td class="colon-col">:</td>
                <td><span class="input-data">{{ $pergerakan->user->bahagian ?? '-' }}</span></td>
            </tr>
            <tr>
                <td class="label-col">Tarikh</td>
                <td class="colon-col">:</td>
                <td><span class="input-data">{{ \Carbon\Carbon::parse($pergerakan->created_at)->format('d/m/Y') }}</span></td>
            </tr>
        </table>
    </div>

    {{-- GARIS PEMISAH --}}
    <hr class="divider">

    {{-- 4. PENGESAHAN (CC) --}}
    <div class="section">
        <strong>Disahkan oleh :</strong>
        
        <p style="margin-top: 5px; margin-bottom: 15px;">
            Adalah saya menegaskan bahawa penggunaan kenderaan sendiri
            
            @if($pergerakan->status_cc == 'Sokong')
                <strong>BOLEH</strong> / <span class="strikethrough">TIDAK BOLEH</span>
            @else
                <span class="strikethrough">BOLEH</span> / <strong>TIDAK BOLEH</strong>
            @endif

            digunakan kerana peruntukan TNT mencukupi.
            
            <span class="input-data">
                @if($pergerakan->status_cc == 'Sokong')
                    {{ $pergerakan->catatan_cc ?? 'BOLEH MENGGUNAKAN KENDERAAN SENDIRI' }}
                @elseif($pergerakan->status_cc == 'Tolak')
                    {{ $pergerakan->catatan_cc ?? 'KEKURANGAN KEWANGAN UNTUK TNT' }}
                @else
                    ........................................................................
                @endif
            </span>
        </p>
        
        {{-- TANDATANGAN CC (GAMBAR) --}}
        <div style="height: 80px; margin-top: 10px;">
            @if(isset($sig_cc))
                 <img src="{{ $sig_cc }}" style="height: 70px; width: auto; display: block;" alt="Sain CC">
            @else
                 <div style="height: 60px;"></div>
            @endif
        </div>
        
        <p style="line-height: 1.2;">
            <strong>{{ $cc_name ?? '......................................................................' }}</strong><br>
            <strong>{{ $cc_jawatan ?? 'Ketua Penolong Pengarah' }}</strong><br>
            Unit Pentadbiran Am
        </p>
    </div>

    {{-- GARIS PEMISAH --}}
    <hr class="divider">

    {{-- 5. KEPUTUSAN (YB) --}}
    <div class="section">
        <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 10px;">
            KEPUTUSAN
        </div>

        <p style="margin-bottom: 15px;">
            Saya 
            @if($pergerakan->status_yb == 'Lulus')
                <strong>BERSETUJU</strong> / <span class="strikethrough">TIDAK BERSETUJU</span>
            @elseif($pergerakan->status_yb == 'Tolak' || $pergerakan->status_yb == 'Ditolak Automatik (Oleh CC)')
                <span class="strikethrough">BERSETUJU</span> / <strong>TIDAK BERSETUJU</strong>
            @else
                BERSETUJU / TIDAK BERSETUJU
            @endif
            membenarkan penggunaan kenderaan sendiri bagi tujuan tugas rasmi di atas.
        </p>

        {{-- TANDATANGAN YB (GAMBAR) --}}
        <div style="height: 80px; margin-top: 10px;">
            {{-- Menggunakan $sig_yb untuk imej tandatangan YB/Dato' --}}
            @if(isset($sig_yb))
                 <img src="{{ $sig_yb }}" class="signature-img" style="height: 70px; width: auto; display: block;" alt="Sain YB">
            @else
                 <div style="height: 60px;"></div>
            @endif
        </div>

        <p>Tandatangan Ketua Bahagian : </p>
        
        <table>
            <tr>
                <td class="label-col">Nama</td>
                <td class="colon-col">:</td>
                <td>
                    {{-- ✅ KEMAS KINI NAMA: Menggunakan $namaYB yang dihantar dari Controller --}}
                    <span class="input-data">{{ strtoupper($namaYB ?? 'NAMA YB TIDAK DIJUMPAI') }}</span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Bahagian</td>
                <td class="colon-col">:</td>
                <td>
                    {{-- ✅ KEMAS KINI BAHAGIAN: Menggunakan $bahagianYB yang dihantar dari Controller --}}
                    <span class="input-data">{{ strtoupper($bahagianYB ?? 'BAHAGIAN YB TIDAK DIJUMPAI') }}</span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Tarikh</td>
                <td class="colon-col">:</td>
                <td>
                    @if($pergerakan->status_yb == 'Lulus')
                        <span class="input-data">{{ \Carbon\Carbon::parse($pergerakan->updated_at)->format('d/m/Y') }}</span>
                    @else
                        ......................................................................
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>
</html>