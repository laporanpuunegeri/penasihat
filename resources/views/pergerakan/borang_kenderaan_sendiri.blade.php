<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Permohonan Kenderaan Sendiri</title>
    <style>
        @page { margin: 40px 50px; } 
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.3; color: #000; }
        .logo-container { text-align: center; margin-bottom: 10px; }
        .logo { max-width: 100px; height: auto; display: inline-block; }
        .header { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; line-height: 1.2; font-size: 11pt; }
        .content { text-align: justify; margin-bottom: 10px; }
        .section { margin-top: 10px; margin-bottom: 10px; }
        .input-data { font-weight: bold; text-transform: uppercase; border-bottom: 1px dotted #000; padding: 0 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 2px; }
        td { vertical-align: top; padding: 2px 0; }
        .label-col { width: 120px; }
        .colon-col { width: 20px; text-align: center; }
        hr.divider { border: 0; border-top: 1px solid #000; margin: 15px 0; }
        .strikethrough { text-decoration: line-through; }
        .spacer { height: 10px; } 
        .no-border { border: none !important; }
        .signature-img { height: 50px; width: auto; margin-bottom: -5px; margin-top: -10px; }
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

    {{-- 2. KANDUNGAN --}}
    <div class="content">
        <p style="margin-bottom: 5px;">Kepada Ketua Bahagian :</p>
        <p>
            Adalah dengan hormatnya saya memohon untuk menggunakan kenderaan sendiri bagi menjalankan tugas rasmi iaitu
            ke <span class="input-data">{{ $pergerakan->tujuan_penggunaan }}</span>
            @if($pergerakan->destinasi) di <span class="input-data">{{ $pergerakan->destinasi }}</span> @endif mulai pada <span class="input-data">{{ \Carbon\Carbon::parse($pergerakan->tarikh_mula)->format('d/m/Y') }}</span>
            hingga <span class="input-data">{{ \Carbon\Carbon::parse($pergerakan->tarikh_akhir)->format('d/m/Y') }}</span>.
        </p>
        <p>Sekian, terima kasih.</p>
    </div>

    {{-- 3. TANDATANGAN PEMOHON --}}
    <div class="section">
        <div class="spacer"></div>
        <table style="width: 100%; border: none; margin-bottom: 5px;">
            <tr>
                <td class="no-border" style="width: 160px; vertical-align: bottom; padding-bottom: 5px;">Tandatangan Pegawai :</td>
                <td class="no-border" style="vertical-align: bottom;">
                    @if(isset($sig_applicant)) <img src="{{ $sig_applicant }}" class="signature-img" alt="Sain"> @else <div style="height: 60px;"></div> @endif
                </td>
            </tr>
        </table>
        <table>
            <tr><td class="label-col">Nama</td><td class="colon-col">:</td><td><span class="input-data">{{ $pergerakan->user->name }}</span></td></tr>
            <tr><td class="label-col">Bahagian</td><td class="colon-col">:</td><td><span class="input-data">{{ $pergerakan->user->bahagian ?? '-' }}</span></td></tr>
            <tr><td class="label-col">Tarikh</td><td class="colon-col">:</td><td><span class="input-data">{{ \Carbon\Carbon::parse($pergerakan->created_at)->format('d/m/Y') }}</span></td></tr>
        </table>
    </div>

    <hr class="divider">

    {{-- 4. PENGESAHAN CC --}}

    <div class="section">
        <strong>Disahkan oleh :</strong>
        <p style="margin-top: 5px; margin-bottom: 15px; text-align: justify;">
            Adalah saya menegaskan bahawa penggunaan kenderaan pejabat tidak dapat disediakan pada tarikh seperti di atas kerana kenderaan digunakan untuk urusan rasmi yang lain.
        </p>
        <table style="width: 100%; border: none;">
            <tr>
                {{-- KIRI: Tandatangan CC --}}
                <td class="no-border" style="width: 60%; vertical-align: bottom;">
                    <div style="height: 70px;">
                        @if(isset($sig_cc)) 
                            {{-- Gambar Sain --}}
                            <img src="{{ $sig_cc }}" style="height: 60px; width: auto;" alt="Sain CC"> 
                        @endif
                    </div>
                    
                    <div style="line-height: 1.2;">
                        <strong>{{ strtoupper($cc_name ?? 'NAMA PENYOKONG TIADA') }}</strong><br>
                        {{ strtoupper($cc_jawatan ?? 'JAWATAN PENYOKONG TIADA') }}<br>
                        Unit Pentadbiran Am
                    </div>
                </td>

                <td class="no-border" style="width: 40%; vertical-align: bottom; padding-bottom: 25px;">
                    <div style="font-weight: bold; margin-bottom: 5px;">CATATAN :</div>
                    
                    {{-- Saya dah buang 'border-bottom' dan titik-titik --}}
                    <div style="min-height: 20px;">
                        {{ $pergerakan->catatan_cc ?? '' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <hr class="divider">

    {{-- 5. KEPUTUSAN YB --}}
    <div class="section">
        <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 10px;">KEPUTUSAN</div>
        <p style="margin-bottom: 15px;">
            Saya 
            @if($pergerakan->status_yb == 'Lulus') <strong>BERSETUJU</strong> / <span class="strikethrough">TIDAK BERSETUJU</span>
            @elseif($pergerakan->status_yb == 'Tolak') <span class="strikethrough">BERSETUJU</span> / <strong>TIDAK BERSETUJU</strong>
            @else BERSETUJU / TIDAK BERSETUJU @endif
            membenarkan penggunaan kenderaan sendiri bagi tujuan tugas rasmi di atas.
        </p>
        
        {{-- Gambar Tandatangan YB --}}
        <div style="height: 80px; margin-top: 10px;">
            @if(isset($sig_yb)) <img src="{{ $sig_yb }}" class="signature-img" style="height: 70px; width: auto; display: block;" alt="Sain YB"> @else <div style="height: 60px;"></div> @endif
        </div>
        
        <p>Tandatangan Ketua Bahagian : </p>
        <table>
            <tr><td class="label-col">Nama</td><td class="colon-col">:</td><td><span class="input-data">{{ strtoupper($namaYB ?? 'NAMA YB TIADA') }}</span></td></tr>
            <tr><td class="label-col">Bahagian</td><td class="colon-col">:</td><td><span class="input-data">{{ strtoupper($bahagianYB ?? 'BAHAGIAN YB TIADA') }}</span></td></tr>
            <tr><td class="label-col">Tarikh</td><td class="colon-col">:</td><td>
                @if($pergerakan->status_yb == 'Lulus') <span class="input-data">{{ \Carbon\Carbon::parse($pergerakan->updated_at)->format('d/m/Y') }}</span>
                @else ...................................................................... @endif
            </td></tr>
        </table>
    </div>

    {{-- 🔥 PAGE 2: LAMPIRAN (FIXED MARGIN) 🔥 --}}
    @if(!empty($pergerakan->lampiran))
        
        {{-- Paksa buka Page 2 --}}
        <div style="page-break-before: always;"></div>

        {{-- Container Gambar (Kurangkan margin atas supaya tak lari ke page 3) --}}
        <div style="text-align: center; margin-top: 10px;">
            <h3 style="text-decoration: underline; margin-bottom: 20px;">LAMPIRAN DOKUMEN SOKONGAN</h3>
            
            @php
                $pathSebenar = storage_path('app/public/' . $pergerakan->lampiran);
            @endphp

            @if(file_exists($pathSebenar))
                @php
                    $type = pathinfo($pathSebenar, PATHINFO_EXTENSION);
                    $data = file_get_contents($pathSebenar);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                @endphp
                
                {{-- Set max-height supaya tak melimpah ke page 3 --}}
                <img src="{{ $base64 }}" style="max-width: 95%; max-height: 850px; border: 1px solid #000; display: inline-block;">
            @else
                <p style="color:red;">RALAT: Fail gambar tidak dijumpai.</p>
            @endif
        </div>
    @endif

</body>
</html>