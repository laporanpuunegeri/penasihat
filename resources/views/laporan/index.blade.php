@extends('layouts.app')
<style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
    }

    th, td {
        border: 1px solid #000;
        padding: 4px;
        vertical-align: top;
    }

    th {
        background-color: #eee;
        text-align: center;
    }

    .text-center { text-align: center; }
    .text-start { text-align: left; }
    .text-end { text-align: right; }

    .fw-bold { font-weight: bold; }
    .fst-italic { font-style: italic; }

    h5, h6 { margin: 0; padding: 0; }
</style>

@section('content')
<div class="container-fluid px-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="text-start">
            <h4 class="text-uppercase fw-bold mb-0">Laporan Aktiviti Bulanan</h4>
            <h5 class="text-uppercase mb-1">Penasihat Undang-Undang Negeri {{ strtoupper(auth()->user()->negeri ?? '-') }}</h5>

            {{-- Dropdown Bulan --}}
            <form method="GET" action="{{ route('laporan.index') }}" class="d-flex align-items-center gap-2 mt-2">
                <label for="bulan" class="mb-0">Bulan:</label>
                <select name="bulan" id="bulan" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan', now()->month) == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <span class="text-muted ms-2">{{ now()->year }}</span>
            </form>
        </div>

<div class="text-end">
    <strong>LAMPIRAN B</strong><br>
    <a href="{{ route('laporan.pdf', ['bulan' => request('bulan', now()->month)]) }}" class="btn btn-sm btn-outline-primary mt-2" target="_blank">
        <i class="bi bi-printer"></i> Cetak PDF
    </a>
    <a href="mailto:?subject=Laporan Bulanan PUU Negeri {{ auth()->user()->negeri ?? '' }}&body=Sila rujuk laporan di pautan ini: {{ url()->current() }}" 
       class="btn btn-sm btn-outline-secondary mt-2 ms-2">
        <i class="bi bi-envelope"></i> Email
    </a>
</div>



    {{-- Bahagian 1 --}}
    <h5 class="fw-bold mb-3">PANDANGAN UNDANG-UNDANG 
        <small class="fw-normal">(Laporan lengkap adalah seperti di <strong>LAMPIRAN I</strong>)</small>
    </h5>

    @php
        $data = collect($kategori_list)->map(function ($kategori) use ($laporan) {
            $laporanKategori = $laporan->where('kategori', $kategori);
            return [
                'kategori' => $kategori,
                'bilangan' => $laporanKategori->count(),
                'jpn' => $laporanKategori->where('dirujuk_jpn', true)->count(),
                'negeri' => $laporanKategori->where('dirujuk_jpn', false)->count(),
                'laporan' => $laporanKategori,
            ];
        });
        $jumlah = $data->sum('bilangan');
        $jumlah_jpn = $data->sum('jpn');
        $jumlah_negeri = $data->sum('negeri');
    @endphp

    <table class="table table-bordered text-center align-middle mb-5">
        <thead class="table-secondary">
            <tr>
                <th rowspan="2" class="align-middle">Pembahagian Pandangan Undang-Undang mengikut isu</th>
                <th rowspan="2" class="align-middle">Bilangan</th>
                <th colspan="2">Bilangan</th>
                <th rowspan="2" class="align-middle">Status</th>
            </tr>
            <tr>
                <th>yang dirujuk ke JPN<br>(Ibu Pejabat)</th>
                <th>diputuskan di peringkat Negeri</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $kategori)
                <tr>
                    <td class="text-start">
                        <strong>{{ ['i.','ii.','iii.','iv.','v.','vi.'][$index] }}</strong> {{ $kategori['kategori'] }}
                    </td>
                    <td>{{ $kategori['bilangan'] }}</td>
                    <td>{{ $kategori['jpn'] }}</td>
                    <td>{{ $kategori['negeri'] }}</td>
                    <td>-</td>
                </tr>
            @endforeach
            <tr>
                <th class="text-end">JUMLAH KESELURUHAN</th>
                <th>{{ $jumlah }}</th>
                <th>{{ $jumlah_jpn }}</th>
                <th>{{ $jumlah_negeri }}</th>
                <th>-</th>
            </tr>
        </tbody>
    </table>

    {{-- Bahagian 2 --}}
    <h5 class="fw-bold mb-3">1. SENARAI PANDANGAN UNDANG-UNDANG TERPERINCI</h5>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>BIL</th>
                <th>Tarikh</th>
                <th>Kategori</th>
                <th>Fakta Ringkasan</th>
                <th>Isu</th>
                <th>Ringkasan Pandangan</th>
                <th>Jenis Pandangan</th>
                <th>Status / Tarikh Selesai</th>
            </tr>
        </thead>
        <tbody>
            @php $bil = 1; @endphp
            @foreach ($data as $index => $kategori)
                <tr class="table-secondary">
                    <td colspan="8" class="fw-bold">
                        ({{ $index + 1 }}) {{ strtoupper($kategori['kategori']) }}
                    </td>
                </tr>
                @forelse ($kategori['laporan'] as $item)
                    <tr>
                        <td>{{ $bil++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tarikh_terima)->format('d/m/Y') }}</td>
                        <td>{{ $kategori['kategori'] }}</td>
                        <td>{{ $item->fakta_ringkasan }}</td>
                        <td>{{ $item->isu }}</td>
                        <td>{{ $item->ringkasan_pandangan }}</td>
                        <td class="text-center">
                            @if ($item->jenis_pandangan === 'Lisan')
                                &#10004;
                            @else
                                Bertulis
                            @endif
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
                        <td colspan="8" class="text-center text-muted fst-italic">Tiada laporan ditemui untuk kategori ini.</td>
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
                <th>BIL</th>
                <th>TARIKH DAFTAR</th>
                <th>JENIS KES / PIHAK-PIHAK</th>
                <th>TARIKH SEBUTAN / BICARA</th>
                <th>FAKTA RINGKAS</th>
                <th>ISU</th>
                <th>** SKOP TUGAS</th>
                <th>RINGKASAN HUJAHAN</th>
                <th>STATUS</th>
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
    <h5 class="fw-bold mb-3">3. LAPORAN PENGGUBALAN RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF YANG DIJALANKAN</h5>

    <table class="table table-bordered align-middle text-center">
        <thead class="table-dark text-white">
            <tr>
                <th style="width: 5%">BIL</th>
                <th>TAJUK RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF</th>
                <th style="width: 30%">TINDAKAN</th>
                <th style="width: 25%">STATUS</th>
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
                    <td colspan="4" class="text-muted fst-italic">Tiada laporan penggubalan direkodkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Bahagian 5 --}}
    <h5 class="fw-bold mb-3 mt-5">4. LAPORAN PINDAAN RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF YANG DIPINDA</h5>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-dark text-white">
            <tr>
                <th style="width: 5%">BIL</th>
                <th>TAJUK RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF</th>
                <th style="width: 30%">TINDAKAN</th>
                <th style="width: 25%">STATUS</th>
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
                    <td colspan="4" class="text-muted fst-italic">Tiada laporan pindaan direkodkan.</td>
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
                <th style="width: 5%">BIL</th>
                <th>TAJUK RANG UNDANG-UNDANG / PERUNDANGAN SUBSIDIARI SUBSTANTIF</th>
                <th style="width: 30%">TINDAKAN</th>
                <th style="width: 25%">STATUS</th>
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
                    <td colspan="4" class="text-muted fst-italic">Tiada laporan semakan direkodkan.</td>
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
                <th rowspan="2" class="align-middle">BIL</th>
                <th rowspan="2" class="align-middle">MESYUARAT</th>
                <th rowspan="2" class="align-middle">ISU</th>
                <th rowspan="2" class="align-middle">TARIKH MESYUARAT</th>
                <th rowspan="2" class="align-middle">STATUS</th>
                <th colspan="2">PANDANGAN</th>
            </tr>
            <tr>
                <th>LISAN</th>
                <th>BERTULIS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan_mesyuarat as $index => $laporan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-start">{{ $laporan->mesyuarat }}</td>
                    <td class="text-start">{{ $laporan->isu }}</td>
                    <td>{{ \Carbon\Carbon::parse($laporan->tarikh_mesyuarat)->format('d/m/Y') }}</td>
                    <td>{{ $laporan->status }}</td>
                    <td>
                        {{ $laporan->pandangan === 'Lisan' ? '✔' : '' }}
                    </td>
                    <td>
                        {{ $laporan->pandangan === 'Bertulis' ? '✔' : '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted fst-italic">Tiada mesyuarat direkodkan untuk bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Bahagian 8 --}}
    <h5 class="fw-bold mb-3 mt-5">7. KES TATATERTIB</h5>

    <table class="table table-bordered align-middle text-center">
        <thead class="table-dark text-white text-center">
            <tr>
                <th style="width: 5%">BIL</th>
                <th style="width: 10%">TARIKH TERIMA</th>
                <th style="width: 20%">FAKTA RINGKASAN</th>
                <th style="width: 15%">ISU</th>
                <th style="width: 30%">RINGKASAN PANDANGAN</th>
                <th style="width: 20%">STATUS / TARIKH SELESAI</th>
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
                        <td>
                            {{ $laporan->status }}
                            @if ($laporan->tarikh_selesai)
                                <br><small class="text-muted">Selesai: {{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted fst-italic">Tiada laporan untuk kategori ini.</td>
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
                <th style="width: 5%">BIL</th>
                <th style="width: 50%">PERIHAL TUGASAN</th>
                <th style="width: 20%">TARIKH</th>
                <th style="width: 25%">TINDAKAN</th>
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
                    <td colspan="4" class="text-muted text-center fst-italic">Tiada laporan tugasan lain-lain direkodkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
