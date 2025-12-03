<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktiviti Bulanan</title>
    <style>
        /* --- GENERAL STYLES --- */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.3; /* Rapatkan sikit line height */
            color: #000;
        }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .fw-normal { font-weight: normal; }
        .mb-3 { margin-bottom: 12px; }
        .mt-4 { margin-top: 20px; }
        .mt-5 { margin-top: 30px; }
        .fst-italic { font-style: italic; }
        .text-muted { color: #555; }
        
        /* --- HEADER SECTION --- */
        .header {
            margin-bottom: 25px;
            text-align: center;
        }
        .header img {
            width: 90px;
            display: block;
            margin: 0 auto 10px auto;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .sub-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        /* --- TABLES (THE FIX) --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto; /* Benarkan table putus */
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            vertical-align: middle;
            text-align: center;
        }

        /* INI PENTING: Paksa baris jadual untuk membenarkan pemotongan */
        tr {
            page-break-inside: auto; 
            page-break-after: auto;
        }
        
        /* Kadang-kadang 'thead' menyebabkan isu gap jika row terlalu panjang */
        /* Kita set row-group supaya dia layan header macam row biasa jika perlu */
        thead {
            display: table-header-group; 
        }

        /* Info Pegawai Table */
        .info-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        /* --- LISTS --- */
        ul {
            margin: 5px 0 15px 0;
            padding-left: 20px;
        }
        li {
            margin-bottom: 5px;
            text-align: justify;
        }

        /* --- SPACER --- */
        .section-spacer {
            margin-top: 30px;
            display: block;
            width: 100%;
            height: 1px; /* Bagi height sikit */
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
        <td style="width: 25%; font-weight:bold;">NAMA PEGAWAI</td>
        <td style="width: 5%; text-align:center;">:</td>
        <td style="width: 70%;">{{ strtoupper($user->name ?? '-') }}</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">JAWATAN</td>
        <td style="text-align:center;">:</td>
        <td>{{ strtoupper($user->nama_jawatan ?? '-') }}</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">BULAN</td>
        <td style="text-align:center;">:</td>
        <td>{{ $bulan }}/{{ $tahun }}</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">NEGERI</td>
        <td style="text-align:center;">:</td>
        <td>{{ strtoupper($user->negeri ?? '-') }}</td>
    </tr>
</table>

<h5 class="fw-bold mb-3">1. PANDANGAN UNDANG-UNDANG 
    <small class="fw-normal" style="font-size: 10px;">(Laporan lengkap adalah seperti di <strong>LAMPIRAN I</strong>)</small>
</h5>

@php
    $data = collect($kategori_list)->map(function ($kategori) use ($laporan) {
        $laporanKategori = $laporan->where('kategori', $kategori);
        return [
            'kategori' => $kategori,
            'bilangan' => $laporanKategori->count(),
            'jpn' => $laporanKategori->where('dirujuk_jpn', true)->count(),
            'negeri' => $laporanKategori->where('dirujuk_jpn', false)->count(),
        ];
    });
    $jumlah = $data->sum('bilangan');
    $jumlah_jpn = $data->sum('jpn');
    $jumlah_negeri = $data->sum('negeri');
@endphp

<table style="text-align: center;">
    <thead>
        <tr>
            <th rowspan="2" style="width: 40%;">Pembahagian Pandangan Mengikut Isu</th>
            <th rowspan="2" style="width: 15%;">Bilangan Keseluruhan</th>
            <th colspan="2">Pecahan Bilangan</th>
            <th rowspan="2" style="width: 15%;">Status</th>
        </tr>
        <tr>
            <th>Dirujuk ke AGC (HQ)</th>
            <th>Peringkat Negeri</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
        <tr>
            <td class="text-start">{{ $item['kategori'] }}</td>
            <td>{{ $item['bilangan'] }}</td>
            <td>{{ $item['jpn'] }}</td>
            <td>{{ $item['negeri'] }}</td>
            <td>-</td>
        </tr>
        @endforeach
        <tr style="background-color: #f9f9f9; font-weight: bold;">
            <td class="text-end">JUMLAH KESELURUHAN</td>
            <td>{{ $jumlah }}</td>
            <td>{{ $jumlah_jpn }}</td>
            <td>{{ $jumlah_negeri }}</td>
            <td>-</td>
        </tr>
    </tbody>
</table>

<h5 class="fw-bold mb-3 mt-4">2. KES MAHKAMAH 
    <small class="fw-normal" style="font-size: 10px;">(Laporan lengkap adalah seperti di <strong>LAMPIRAN II</strong>)</small>
</h5>

@php $jumlah = ['bil_aktif' => 0, 'majistret' => 0, 'sesi' => 0, 'tinggi' => 0, 'rayuan' => 0, 'persk' => 0]; @endphp

<table style="text-align: center;">
    <thead>
        <tr>
            <th rowspan="2" style="width: 25%;">Kategori Kes</th>
            <th rowspan="2" style="width: 12%;">Bilangan Masih Aktif</th>
            <th colspan="5">Peringkat Mahkamah</th>
            <th rowspan="2" style="width: 10%;">Status</th>
        </tr>
        <tr>
            <th>Maj.</th>
            <th>Sesi.</th>
            <th>Tinggi</th>
            <th>Rayuan</th>
            <th>Persk.</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($kategori_kes as $kategori)
        @php
            $key = $kategori['key'];
            $label = $kategori['label'];
            $rekod = $lampiran_kesmahkamah[$key] ?? [
                'bil_aktif' => 0, 'majistret' => 0, 'sesi' => 0, 'tinggi' => 0, 'rayuan' => 0, 'persk' => 0, 'status' => '-'
            ];
            $jumlah['bil_aktif'] += $rekod['bil_aktif'];
            $jumlah['majistret'] += $rekod['majistret'];
            $jumlah['sesi'] += $rekod['sesi'];
            $jumlah['tinggi'] += $rekod['tinggi'];
            $jumlah['rayuan'] += $rekod['rayuan'];
            $jumlah['persk'] += $rekod['persk'];
        @endphp
        <tr>
            <td class="text-start">{{ $label }}</td>
            <td>{{ $rekod['bil_aktif'] }}</td>
            <td>{{ $rekod['majistret'] }}</td>
            <td>{{ $rekod['sesi'] }}</td>
            <td>{{ $rekod['tinggi'] }}</td>
            <td>{{ $rekod['rayuan'] }}</td>
            <td>{{ $rekod['persk'] }}</td>
            <td>{{ $rekod['status'] ?? '-' }}</td>
        </tr>
    @endforeach
        <tr style="background-color: #f9f9f9; font-weight: bold;">
            <td class="text-end">JUMLAH KESELURUHAN</td>
            <td>{{ $jumlah['bil_aktif'] }}</td>
            <td>{{ $jumlah['majistret'] }}</td>
            <td>{{ $jumlah['sesi'] }}</td>
            <td>{{ $jumlah['tinggi'] }}</td>
            <td>{{ $jumlah['rayuan'] }}</td>
            <td>{{ $jumlah['persk'] }}</td>
            <td>-</td>
        </tr>
    </tbody>
</table>

<h5 class="fw-bold mb-3 mt-4">3. PERUNDANGAN SUBSIDIARI SUBSTANTIF</h5>
<ul>
    <li>Rang Undang-Undang / Perundangan Subsidiari Substantif yang digubal (Laporan lengkap di <strong>LAMPIRAN III</strong>)</li>
    <li>Rang Undang-Undang / Perundangan Subsidiari Substantif yang dipinda (Laporan lengkap di <strong>LAMPIRAN IV</strong>)</li>
    <li>Rang Undang-Undang / Perundangan Subsidiari Substantif yang disemak di bawah Akta Penyelenggaraan Undang-Undang 1968 <strong>[Akta 1]</strong> (Laporan lengkap di <strong>LAMPIRAN V</strong>)</li>
</ul>

<h5 class="fw-bold mb-3 mt-4">4. MESYUARAT YANG DIHADIRI</h5>
<ul>
    <li>Mesyuarat yang dihadiri (Laporan lengkap di <strong>LAMPIRAN VI</strong>)</li>
</ul>

<h5 class="fw-bold mb-3 mt-4">5. KES TATATERTIB</h5>
<ul><li>Laporan lengkap adalah seperti di <strong>LAMPIRAN VII</strong></li></ul>

<h5 class="fw-bold mb-3 mt-4">6. LAIN-LAIN TUGASAN</h5>
<ul><li>Laporan lengkap adalah seperti di <strong>LAMPIRAN VIII</strong></li></ul>


<div class="section-spacer"></div>

<h5 class="fw-bold mb-3">LAMPIRAN I: SENARAI PANDANGAN UNDANG-UNDANG TERPERINCI</h5>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">Bil</th>
            <th style="width: 10%;">Tarikh</th>
            <th style="width: 15%;">Kategori</th>
            <th style="width: 20%;">Fakta Ringkas</th>
            <th style="width: 15%;">Isu</th>
            <th style="width: 15%;">Ringkasan Pandangan</th>
            <th style="width: 10%;">Jenis</th>
            <th style="width: 10%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @php $bil = 1; @endphp
        @foreach ($kategori_list as $index => $kategori)
            @php $laporanKategori = $laporan->where('kategori', $kategori); @endphp
            <tr style="background-color: #f9f9f9; page-break-inside: auto;">
                <td colspan="8" class="fw-bold text-start">({{ $index + 1 }}) {{ strtoupper($kategori) }}</td>
            </tr>
            @forelse ($laporanKategori as $item)
                <tr>
                    <td class="text-center">{{ $bil++ }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tarikh_terima)->format('d/m/Y') }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->fakta_ringkasan }}</td>
                    <td>{{ $item->isu }}</td>
                    <td>{{ $item->ringkasan_pandangan }}</td>
                    <td class="text-center">{{ $item->jenis_pandangan === 'Lisan' ? 'Lisan' : 'Bertulis' }}</td>
                    <td class="text-center">
                        {{ $item->status }}
                        @if ($item->tarikh_selesai)
                            <br><br><span class="text-muted" style="font-size: 9px;">Selesai:<br>{{ \Carbon\Carbon::parse($item->tarikh_selesai)->format('d/m/Y') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted fst-italic">Tiada laporan.</td></tr>
            @endforelse
        @endforeach
    </tbody>
</table>

<div class="section-spacer"></div>

<h5 class="fw-bold mb-3">LAMPIRAN II: LAPORAN KES MAHKAMAH TERPERINCI</h5> 
<table>
    <thead>
        <tr>
            <th style="width: 5%;">Bil</th>
            <th style="width: 10%;">Tarikh Daftar</th>
            <th style="width: 15%;">Jenis Kes / Pihak</th>
            <th style="width: 10%;">Sebutan / Bicara</th>
            <th style="width: 20%;">Fakta / Isu</th>
            <th style="width: 10%;">Skop Tugas</th>
            <th style="width: 20%;">Ringkasan Hujahan</th>
            <th style="width: 10%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_kesmahkamah as $index => $laporan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($laporan->tarikh_daftar)->format('d/m/Y') }}</td>
                <td>{{ $laporan->jenis_kes ?? '-' }}</td>
                <td class="text-center">{{ $laporan->tarikh_sebutan ? \Carbon\Carbon::parse($laporan->tarikh_sebutan)->format('d/m/Y') : '-' }}</td>
                <td>
                    <strong>Fakta:</strong> {{ $laporan->fakta_ringkas ?? '-' }}<br>
                    <strong>Isu:</strong> {{ $laporan->isu ?? '-' }}
                </td>
                <td>{{ $laporan->skop_tugas ?? '-' }}</td>
                <td>{{ $laporan->ringkasan_hujahan ?? '-' }}</td>
                <td class="text-center">
                    {{ $laporan->status ?? '-' }}
                    @if (!empty($laporan->tarikh_selesai))
                        <br><br><span class="text-muted" style="font-size: 9px;">Selesai:<br>{{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted fst-italic">Tiada rekod.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-spacer"></div>

<h5 class="fw-bold mb-3">LAMPIRAN III: PENGGUBALAN RANG UNDANG-UNDANG</h5>
<table>
    <thead>
        <tr>
            <th style="width: 5%;">Bil</th>
            <th style="width: 45%;">Tajuk RUU / Perundangan Subsidiari</th>
            <th style="width: 30%;">Tindakan</th>
            <th style="width: 20%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_gubalan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->tajuk }}</td>
                <td>{{ $item->tindakan }}</td>
                <td class="text-center">{{ $item->status }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted fst-italic">Tiada rekod.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-spacer"></div>

<h5 class="fw-bold mb-3">LAMPIRAN IV: PINDAAN RANG UNDANG-UNDANG</h5>
<table>
    <thead>
        <tr>
            <th style="width: 5%;">Bil</th>
            <th style="width: 45%;">Tajuk RUU / Perundangan Subsidiari</th>
            <th style="width: 30%;">Tindakan</th>
            <th style="width: 20%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_pindaan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->tajuk }}</td>
                <td>{{ $item->tindakan }}</td>
                <td class="text-center">{{ $item->status }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted fst-italic">Tiada rekod.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-spacer"></div>

<h5 class="fw-bold mb-3">LAMPIRAN V: SEMAKAN RANG UNDANG-UNDANG</h5>
<table>
    <thead>
        <tr>
            <th style="width: 5%;">Bil</th>
            <th style="width: 45%;">Tajuk RUU / Perundangan Subsidiari</th>
            <th style="width: 30%;">Tindakan</th>
            <th style="width: 20%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_semakan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->tajuk }}</td>
                <td>{{ $item->tindakan }}</td>
                <td class="text-center">{{ $item->status }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted fst-italic">Tiada rekod.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-spacer"></div>

<h5 class="fw-bold mb-3">LAMPIRAN VI: LAPORAN MESYUARAT</h5>
<p class="fst-italic mb-3" style="font-size: 10px;">(*Sila nyatakan rujukan jika berkaitan dengan Lampiran I)</p>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">Bil</th>
            <th style="width: 25%;">Mesyuarat</th>
            <th style="width: 25%;">Isu</th>
            <th style="width: 15%;">Tarikh</th>
            <th style="width: 15%;">Status</th>
            <th style="width: 15%;">Pandangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_mesyuarat as $index => $laporan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $laporan->mesyuarat }}</td>
                <td>{{ $laporan->isu }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($laporan->tarikh_mesyuarat)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $laporan->status }}</td>
                <td class="text-center">{{ $laporan->pandangan }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted fst-italic">Tiada rekod.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-spacer"></div>

<h5 class="fw-bold mb-3">LAMPIRAN VII: KES TATATERTIB</h5>
<table>
    <thead>
        <tr>
            <th style="width: 5%">Bil</th>
            <th style="width: 10%">Tarikh</th>
            <th style="width: 25%">Fakta Ringkasan</th>
            <th style="width: 15%">Isu</th>
            <th style="width: 25%">Ringkasan Pandangan</th>
            <th style="width: 20%">Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $kategori_tatatertib = [
                'PRIMA FACIE' => 'PRIMA FACIE / KERTAS PERTUDUHAN',
                'SURCAJ' => 'KES SURCAJ / LAPORAN SIASATAN',
                'PENAMATAN' => 'PENAMATAN DEMI KEPENTINGAN AWAM',
            ];
            $bil = 1;
        @endphp
        @foreach ($kategori_tatatertib as $key => $tajuk)
            @php $laporanKategori = $laporan_tatatertib->where('kategori', $key); @endphp
            <tr style="background-color: #f9f9f9;">
                <td colspan="6" class="fw-bold text-start">{{ $tajuk }}</td>
            </tr>
            @forelse ($laporanKategori as $laporan)
                <tr>
                    <td class="text-center">{{ $bil++ }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($laporan->tarikh_terima)->format('d/m/Y') }}</td>
                    <td>{{ $laporan->fakta_ringkasan }}</td>
                    <td>{{ $laporan->isu }}</td>
                    <td>{{ $laporan->ringkasan_pandangan }}</td>
                    <td class="text-center">
                        {{ $laporan->status }}
                        @if ($laporan->tarikh_selesai)
                            <br><span class="text-muted" style="font-size: 9px;">{{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted fst-italic">Tiada rekod.</td></tr>
            @endforelse
        @endforeach
    </tbody>
</table>

<div class="section-spacer"></div>

<h5 class="fw-bold mb-3">LAMPIRAN VIII: LAIN-LAIN TUGASAN</h5>
<table>
    <thead>
        <tr>
            <th style="width: 5%;">Bil</th>
            <th style="width: 50%;">Perihal Tugasan</th>
            <th style="width: 20%;">Tarikh</th>
            <th style="width: 25%;">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_lainlain as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->perihal }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tarikh_daftar)->format('d/m/Y') }}</td>
                <td>{{ $item->tindakan }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted fst-italic">Tiada rekod.</td></tr>
        @endforelse
    </tbody>
</table>

</body>
</html>