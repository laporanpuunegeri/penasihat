<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktiviti Bulanan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }

        .text-center { text-align: center; }
        .text-start { text-align: left; }

        .header {
            margin-bottom: 20px;
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
            margin-bottom: 5px;
        }

        .sub-title {
            font-weight: bold;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
            word-break: break-word;
            white-space: normal;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
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
        <td style="width: 25%;">NAMA PEGAWAI</td>
        <td style="width: 5%;">:</td>
        <td style="width: 70%;">{{ strtoupper($user->name ?? '-') }}</td>
    </tr>
    <tr>
        <td>JAWATAN</td>
        <td>:</td>
        <td>{{ strtoupper($user->nama_jawatan ?? '-') }}</td>
    </tr>
    <tr>
        <td>BULAN</td>
        <td>:</td>
        <td>{{ $bulan }}/{{ $tahun }}</td>
    </tr>
    <tr>
        <td>NEGERI</td>
        <td>:</td>
        <td>{{ strtoupper($user->negeri ?? '-') }}</td>
    </tr>
</table>
{{-- Bahagian 1 --}}
<h5 class="fw-bold mb-3">1. PANDANGAN UNDANG-UNDANG 
    <small class="fw-normal">(Laporan lengkap adalah seperti di <strong>LAMPIRAN I</strong>)</small>
</h5>

@php
    function toRoman($number) {
        $map = ['M'=>1000,'CM'=>900,'D'=>500,'CD'=>400,'C'=>100,'XC'=>90,'L'=>50,'XL'=>40,'X'=>10,'IX'=>9,'V'=>5,'IV'=>4,'I'=>1];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return strtolower($returnValue) . '.';
    }

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

<table class="table table-bordered text-center align-middle mb-5" style="table-layout: fixed;">
    <thead class="table-secondary">
        <tr>
            <th rowspan="2" style="width: 30%;">Pembahagian Pandangan Undang-Undang mengikut isu</th>
            <th rowspan="2" style="width: 10%;">Bilangan</th>
            <th colspan="2" style="width: 30%;">Bilangan</th>
            <th rowspan="2" style="width: 10%;">Status</th>
        </tr>
        <tr>
            <th style="width: 15%;">yang dirujuk ke JPN<br>(Ibu Pejabat)</th>
            <th style="width: 15%;">diputuskan di peringkat Negeri</th>
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
        <tr class="fw-bold">
            <td class="text-end">JUMLAH KESELURUHAN</td>
            <td>{{ $jumlah }}</td>
            <td>{{ $jumlah_jpn }}</td>
            <td>{{ $jumlah_negeri }}</td>
            <td>-</td>
        </tr>
    </tbody>
</table>
{{-- Bahagian 2 --}}
<h5 class="fw-bold mb-3">2. KES MAHKAMAH 
    <small class="fw-normal">(Laporan lengkap adalah seperti di <strong>LAMPIRAN II</strong>)</small>
</h5>

@php
    use Illuminate\Support\Str;
    $jumlah = ['bil_aktif' => 0, 'majistret' => 0, 'sesi' => 0, 'tinggi' => 0, 'rayuan' => 0, 'persk' => 0];
@endphp

<table class="table table-bordered text-center align-middle mb-5" style="table-layout: fixed;">
    <thead class="table-dark">
        <tr>
            <th rowspan="2" style="width: 25%;">Pembahagian kes mengikut isu</th>
            <th rowspan="2" style="width: 10%;">Bilangan Masih Aktif</th>
            <th colspan="5" style="width: 45%;">Mahkamah</th>
            <th rowspan="2" style="width: 10%;">Status</th>
        </tr>
        <tr>
            <th style="width: 9%;">Maj.</th>
            <th style="width: 9%;">Sesi.</th>
            <th style="width: 9%;">Tinggi</th>
            <th style="width: 9%;">Rayuan</th>
            <th style="width: 9%;">Persk.</th>
        </tr>
    </thead>
    <tbody>
@foreach ($kategori_kes as $kategori)
    @php
        $key = $kategori['key'];
        $label = $kategori['label'];
        $rekod = $lampiran_kesmahkamah[$key] ?? [
            'bil_aktif' => 0,
            'majistret' => 0,
            'sesi'      => 0,
            'tinggi'    => 0,
            'rayuan'    => 0,
            'persk'     => 0,
            'status'    => '-',
        ];

        // Kemaskini jumlah keseluruhan
        $jumlah['bil_aktif'] += $rekod['bil_aktif'];
        $jumlah['majistret'] += $rekod['majistret'];
        $jumlah['sesi']      += $rekod['sesi'];
        $jumlah['tinggi']    += $rekod['tinggi'];
        $jumlah['rayuan']    += $rekod['rayuan'];
        $jumlah['persk']     += $rekod['persk'];
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
    <tr class="fw-bold">
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

    <h5 class="fw-bold mb-3 mt-5">3. PERUNDANGAN SUBSIDIARI SUBSTANTIF</h5>

<ul style="list-style-type: none; padding-left: 0;">
    <li>
        [*] Rang Undang-Undang / Perundangan Subsidiari Substantif yang digubal 
        (Laporan lengkap adalah seperti di <strong>LAMPIRAN III</strong>)
    </li>
    <li>
        [*] Rang Undang-Undang / Perundangan Subsidiari Substantif yang dipinda 
        (Laporan lengkap adalah seperti di <strong>LAMPIRAN IV</strong>)
    </li>
    <li>
        [*] Rang Undang-Undang / Perundangan Subsidiari Substantif yang disemak 
        di bawah Akta Penyelenggaraan Undang-Undang 1968 <strong>[Akta 1]</strong> 
        (Laporan lengkap adalah seperti di <strong>LAMPIRAN V</strong>)
    </li>
</ul>

<h5 class="fw-bold mb-3 mt-4">4. MESYUARAT YANG DIHADIRI</h5>

<ul style="list-style-type: none; padding-left: 0;">
    <li>
        [*] Mesyuarat yang dihadiri 
        (Laporan lengkap adalah seperti di <strong>LAMPIRAN VI</strong>)
    </li>
</ul>

<h5 class="fw-bold mb-3 mt-4">5. KES TATATERTIB</h5>
<p>
    (Laporan lengkap adalah seperti di <strong>LAMPIRAN VII</strong>)
</p>

<h5 class="fw-bold mb-3 mt-4">6. LAIN-LAIN TUGASAN</h5>
<p>
    (Laporan lengkap adalah seperti di <strong>LAMPIRAN VIII</strong>)
</p>
    
{{-- Bahagian 2 --}}
<h5 class="fw-bold mb-3">1. SENARAI PANDANGAN UNDANG-UNDANG TERPERINCI</h5>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr>
            <th style="width: 5%;">BIL</th>
            <th style="width: 8%;">Tarikh</th>
            <th style="width: 12%;">Kategori</th>
            <th style="width: 19%;">Fakta Ringkasan</th>
            <th style="width: 14%;">Isu</th>
            <th style="width: 18%;">Ringkasan Pandangan</th>
            <th style="width: 12%;">Jenis Pandangan</th>
            <th style="width: 12%;">Status / Tarikh Selesai</th>
        </tr>
    </thead>
    <tbody>
        @php $bil = 1; @endphp
        @foreach ($kategori_list as $index => $kategori)
            @php
                $laporanKategori = $laporan->where('kategori', $kategori);
            @endphp
            <tr class="table-secondary">
                <td colspan="8" class="fw-bold">({{ $index + 1 }}) {{ strtoupper($kategori) }}</td>
            </tr>

            @forelse ($laporanKategori as $item)
                <tr>
                    <td>{{ $bil++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tarikh_terima)->format('d/m/Y') }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->fakta_ringkasan }}</td>
                    <td>{{ $item->isu }}</td>
                    <td>{{ $item->ringkasan_pandangan }}</td>
                    <td class="text-center">
                        {{ $item->jenis_pandangan === 'Lisan' ? '✔' : 'Bertulis' }}
                    </td>
                    <td>
                        {{ $item->status }}
                        @if ($item->tarikh_selesai)
                            <br><small class="text-muted">Selesai: {{ \Carbon\Carbon::parse($item->tarikh_selesai)->format('d/m/Y') }}</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted fst-italic">
                        Tiada laporan ditemui untuk kategori ini.
                    </td>
                </tr>
            @endforelse
        @endforeach
    </tbody>
</table>

{{-- Bahagian 3 --}}
<h5 class="fw-bold mb-3">2. LAPORAN KES MAHKAMAH</h5> 

<table class="table table-bordered table-striped align-middle mb-5">
    <thead class="table-dark">
        <tr>
            <th style="width: 5%;">BIL</th>
            <th style="width: 10%;">TARIKH DAFTAR</th>
            <th style="width: 14%;">JENIS KES / PIHAK-PIHAK</th>
            <th style="width: 10%;">TARIKH SEBUTAN / BICARA</th>
            <th style="width: 17%;">FAKTA RINGKAS</th>
            <th style="width: 10%;">ISU</th>
            <th style="width: 12%;">** SKOP TUGAS</th>
            <th style="width: 13%;">RINGKASAN HUJAHAN</th>
            <th style="width: 9%;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_kesmahkamah as $index => $laporan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($laporan->tarikh_daftar)->format('d/m/Y H:i') }}</td>
                <td>{{ $laporan->jenis_kes ?? '-' }}</td>
                <td>{{ $laporan->tarikh_sebutan ? \Carbon\Carbon::parse($laporan->tarikh_sebutan)->format('d/m/Y') : '-' }}</td>
                <td class="text-start">{{ $laporan->fakta_ringkas ?? '-' }}</td>
                <td>{{ $laporan->isu ?? '-' }}</td>
                <td class="text-start">{{ $laporan->skop_tugas ?? '-' }}</td>
                <td class="text-start">{{ $laporan->ringkasan_hujahan ?? '-' }}</td>
                <td>
                    {{ $laporan->status ?? '-' }}
                    @if (!empty($laporan->tarikh_selesai))
                        <br><small class="text-muted">Selesai: {{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}</small>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted fst-italic">
                    Tiada laporan kes mahkamah direkodkan untuk bulan ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>


{{-- Bahagian 4 --}}
<h5 class="fw-bold mb-3">
    3. LAPORAN PENGGUBALAN RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF YANG DIJALANKAN
</h5>

<table class="table table-bordered align-middle text-center">
    <thead class="table-secondary">
        <tr>
            <th style="width: 5%;">BIL</th>
            <th style="width: 40%;">TAJUK RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF</th>
            <th style="width: 30%;">TINDAKAN</th>
            <th style="width: 25%;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_gubalan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-start">{{ $item->tajuk }}</td>
                <td class="text-start">{{ $item->tindakan }}</td>
                <td class="text-start">{{ $item->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-muted fst-italic text-center">
                    Tiada laporan penggubalan direkodkan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>


{{-- Bahagian 5 --}}
<h5 class="fw-bold mb-3 mt-5">
    4. LAPORAN PINDAAN RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF YANG DIPINDA
</h5>

<table class="table table-bordered text-center align-middle">
    <thead class="table-dark text-white">
        <tr>
            <th style="width: 5%;">BIL</th>
            <th style="width: 40%;">TAJUK RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF</th>
            <th style="width: 30%;">TINDAKAN</th>
            <th style="width: 25%;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_pindaan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-start">{{ $item->tajuk }}</td>
                <td class="text-start">{{ $item->tindakan }}</td>
                <td class="text-start">{{ $item->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-muted fst-italic text-center">
                    Tiada laporan pindaan direkodkan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Bahagian 6 --}}
<h5 class="fw-bold mb-3 mt-5">
    5. LAPORAN PENYEMAKAN RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF YANG DISEMAK 
    <small class="fw-normal d-block">(Termasuk cetakan semula dan pembaharuan undang-undang)</small>
</h5>

<table class="table table-bordered text-center align-middle">
    <thead class="table-dark text-white">
        <tr>
            <th style="width: 5%;">BIL</th>
            <th style="width: 40%;">TAJUK RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF</th>
            <th style="width: 30%;">TINDAKAN</th>
            <th style="width: 25%;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_semakan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-start">{{ $item->tajuk }}</td>
                <td class="text-start">{{ $item->tindakan }}</td>
                <td class="text-start">{{ $item->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-muted fst-italic text-center">
                    Tiada laporan semakan direkodkan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
{{-- Bahagian 7 --}}
<h5 class="fw-bold mb-3 mt-5">
    6. LAPORAN MESYUARAT YANG DIHADIRI
</h5>

<p class="fst-italic mb-1">
    (*Sila nyatakan dan rujukan butiran berkenaan sekiranya pandangan undang-undang telah diberikan dalam <strong>LAMPIRAN I</strong>)
</p>
<p class="fst-italic mb-3">
    (**Bagi Kes Tatatertib, sila nyatakan bilangan kes yang telah didengar)
</p>

<table class="table table-bordered align-middle text-center">
    <thead class="table-dark text-white">
        <tr>
            <th style="width: 5%;" rowspan="2" class="align-middle">BIL</th>
            <th style="width: 24%;" rowspan="2" class="align-middle text-start">MESYUARAT</th>
            <th style="width: 24%;" rowspan="2" class="align-middle text-start">ISU</th>
            <th style="width: 14%;" rowspan="2" class="align-middle">TARIKH</th>
            <th style="width: 17%;" rowspan="2" class="align-middle text-start">STATUS</th>
            <th colspan="2" class="align-middle">PANDANGAN</th>
        </tr>
        <tr>
            <th style="width: 8%;">LISAN</th>
            <th style="width: 8%;">BERTULIS</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_mesyuarat as $index => $laporan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-start">{{ $laporan->mesyuarat }}</td>
                <td class="text-start">{{ $laporan->isu }}</td>
                <td>{{ \Carbon\Carbon::parse($laporan->tarikh_mesyuarat)->format('d/m/Y') }}</td>
                <td class="text-start">{{ $laporan->status }}</td>
                <td>{{ $laporan->pandangan === 'Lisan' ? '✔' : '' }}</td>
                <td>{{ $laporan->pandangan === 'Bertulis' ? '✔' : '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted fst-italic">
                    Tiada mesyuarat direkodkan untuk bulan ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Bahagian 8 --}}
<h5 class="fw-bold mb-3 mt-5">7. KES TATATERTIB</h5>

<table class="table table-bordered align-middle text-center">
    <thead class="table-dark text-white">
        <tr>
            <th style="width: 5%">BIL</th>
            <th style="width: 10%">TARIKH</th>
            <th style="width: 25%" class="text-start">FAKTA RINGKASAN</th>
            <th style="width: 15%" class="text-start">ISU</th>
            <th style="width: 25%" class="text-start">RINGKASAN PANDANGAN</th>
            <th style="width: 20%" class="text-start">STATUS / TARIKH SELESAI</th>
        </tr>
    </thead>
    <tbody>
        @php
            $kategori_tatatertib = [
                'PRIMA FACIE' => '(i) MENYEMAK PENENTUAN KES PRIMA FACIE / KERTAS PERTUDUHAN / NOTIS TIDAK HADIR BERTUGAS',
                'SURCAJ' => '(ii) KES SURCAJ / MENELITI LAPORAN JAWATANKUASA SIASATAN',
                'PENAMATAN' => '(iii) PENYEDIAAN ULASAN BAGI KES PENAMATAN DEMI KEPENTINGAN AWAM',
            ];
            $bil = 1;
        @endphp

        @foreach ($kategori_tatatertib as $key => $tajuk)
            @php
                $laporanKategori = $laporan_tatatertib->where('kategori', $key);
            @endphp

            <tr class="table-secondary">
                <td colspan="6" class="fw-bold text-start">{{ $tajuk }}</td>
            </tr>

            @forelse ($laporanKategori as $laporan)
                <tr>
                    <td>{{ $bil++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($laporan->tarikh_terima)->format('d/m/Y') }}</td>
                    <td class="text-start">{{ $laporan->fakta_ringkasan }}</td>
                    <td class="text-start">{{ $laporan->isu }}</td>
                    <td class="text-start">{{ $laporan->ringkasan_pandangan }}</td>
                    <td class="text-start">
                        {{ $laporan->status }}
                        @if ($laporan->tarikh_selesai)
                            <br><small class="text-muted">Selesai: {{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted fst-italic">Tiada laporan untuk kategori ini.</td>
                </tr>
            @endforelse
        @endforeach
    </tbody>
</table>

{{-- Bahagian 9 --}}
<h5 class="fw-bold mb-3 mt-5">8. LAIN-LAIN TUGASAN</h5>

<table class="table table-bordered align-middle">
    <thead class="table-dark text-white text-center">
        <tr>
            <th style="width: 5%;">BIL</th>
            <th style="width: 50%;" class="text-start">PERIHAL TUGASAN</th>
            <th style="width: 20%;">TARIKH</th>
            <th style="width: 25%;" class="text-start">TINDAKAN</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporan_lainlain as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-start">{{ $item->perihal }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tarikh_daftar)->format('d/m/Y') }}</td>
                <td class="text-start">{{ $item->tindakan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-muted text-center fst-italic">
                    Tiada laporan tugasan lain-lain direkodkan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>
</body>
</html>
