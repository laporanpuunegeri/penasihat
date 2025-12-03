@extends('layouts.app')

@section('content')

{{-- Custom CSS untuk halaman ini --}}
<style>
    .report-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 30px;
        overflow: hidden;
    }
    .report-header {
        background: #f8fafc;
        padding: 20px 25px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .report-title {
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table-custom thead th {
        background-color: #1e293b; /* Dark Navy */
        color: #ffffff;
        font-weight: 600;
        border: none;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 15px;
        vertical-align: middle;
    }
    .table-custom tbody td {
        padding: 15px;
        vertical-align: middle;
        font-size: 0.95rem;
        border-color: #f1f5f9;
        color: #475569;
    }
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .bg-soft-primary { background-color: #e0f2fe; color: #0284c7; }
    .bg-soft-success { background-color: #dcfce7; color: #166534; }
    .bg-soft-warning { background-color: #fef9c3; color: #854d0e; }
    .bg-soft-secondary { background-color: #f1f5f9; color: #475569; }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER UTAMA & FILTER --}}
    <div class="report-card p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="fw-bold text-dark mb-1">LAPORAN AKTIVITI BULANAN</h4>
                <h6 class="text-secondary text-uppercase mb-3">
                    Penasihat Undang-Undang Negeri {{ strtoupper(auth()->user()->negeri ?? '-') }}
                </h6>

                {{-- Dropdown Bulan --}}
                <form method="GET" action="{{ route('laporan.index') }}" class="d-flex align-items-center gap-3">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-calendar-alt"></i></span>
                        <select name="bulan" id="bulan" class="form-select border-start-0 ps-0" onchange="this.form.submit()" style="cursor: pointer;">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('bulan', now()->month) == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <span class="input-group-text bg-light text-dark fw-bold">{{ now()->year }}</span>
                    </div>
                </form>
            </div>

            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="p-3 bg-light rounded-3 d-inline-block text-center border">
                    <small class="d-block text-muted fw-bold mb-2">EKSPORT DOKUMEN</small>
                    <a href="{{ route('laporan.pdf', ['bulan' => request('bulan', now()->month)]) }}" 
                       class="btn btn-primary btn-sm px-4 shadow-sm" target="_blank">
                        <i class="fas fa-file-pdf me-2"></i> Cetak PDF (Lampiran B)
                    </a>
                </div>
            </div>
        </div>
    </div>


    {{-- BAHAGIAN 1: STATISTIK PANDANGAN UNDANG-UNDANG --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-chart-pie me-2 text-primary"></i> Ringkasan Pandangan Undang-Undang
            </h5>
            <small class="text-muted">Rujuk Lampiran I untuk perincian</small>
        </div>
        
        <div class="p-0">
            @php
                $kategori_list = ['Perlembagaan', 'Tanah / PBT', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Perjanjian / MOU', 'Penswastaan', 'Lain-lain'];

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

            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0 text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-start ps-4" style="width: 40%;">Isu / Kategori</th>
                            <th rowspan="2">Jumlah Keseluruhan</th>
                            <th colspan="2">Pecahan Bilangan</th>
                        </tr>
                        <tr>
                            <th class="bg-secondary bg-opacity-10 text-dark">Dirujuk ke AGC (HQ)</th>
                            <th class="bg-secondary bg-opacity-10 text-dark">Peringkat Negeri</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $index => $kategori)
                            <tr>
                                <td class="text-start ps-4 fw-bold text-dark">
                                    {{ $loop->iteration }}. {{ $kategori['kategori'] }}
                                </td>
                                <td>
                                    @if($kategori['bilangan'] > 0)
                                        <span class="badge bg-primary rounded-pill px-3">{{ $kategori['bilangan'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $kategori['jpn'] ?: '-' }}</td>
                                <td>{{ $kategori['negeri'] ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr class="fw-bold">
                            <td class="text-end pe-4">JUMLAH BESAR</td>
                            <td class="text-primary fs-5">{{ $jumlah }}</td>
                            <td>{{ $jumlah_jpn }}</td>
                            <td>{{ $jumlah_negeri }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


    {{-- BAHAGIAN 2: STATISTIK KES MAHKAMAH --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-balance-scale me-2 text-primary"></i> Ringkasan Kes Mahkamah
            </h5>
            <small class="text-muted">Rujuk Lampiran II untuk perincian</small>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-start ps-4">Kategori Kes</th>
                        <th rowspan="2">Masih Aktif</th>
                        <th colspan="5" class="bg-secondary bg-opacity-10 text-dark">Peringkat Mahkamah</th>
                    </tr>
                    <tr>
                        <th>Majistret</th>
                        <th>Sesyen</th>
                        <th>Tinggi</th>
                        <th>Rayuan</th>
                        <th>Persekutuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $jum = ['aktif' => 0, 'maj' => 0, 'sesi' => 0, 'tinggi' => 0, 'rayuan' => 0, 'persk' => 0]; @endphp

                    @forelse ($lampiran_kesmahkamah as $row)
                        <tr>
                            <td class="text-start ps-4 fw-semibold">{{ $row->kategori }}</td>
                            <td><span class="badge bg-warning text-dark rounded-pill">{{ $row->bil_aktif }}</span></td>
                            <td>{{ $row->majistret }}</td>
                            <td>{{ $row->sesi }}</td>
                            <td>{{ $row->tinggi }}</td>
                            <td>{{ $row->rayuan }}</td>
                            <td>{{ $row->persk }}</td>
                        </tr>
                        @php
                            $jum['aktif'] += $row->bil_aktif; $jum['maj'] += $row->majistret;
                            $jum['sesi'] += $row->sesi; $jum['tinggi'] += $row->tinggi;
                            $jum['rayuan'] += $row->rayuan; $jum['persk'] += $row->persk;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open mb-2 d-block fa-2x opacity-25"></i>
                                Tiada data ringkasan kes mahkamah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td class="text-end pe-4">JUMLAH BESAR</td>
                        <td class="text-primary">{{ $jum['aktif'] }}</td>
                        <td>{{ $jum['maj'] }}</td>
                        <td>{{ $jum['sesi'] }}</td>
                        <td>{{ $jum['tinggi'] }}</td>
                        <td>{{ $jum['rayuan'] }}</td>
                        <td>{{ $jum['persk'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


    {{-- BAHAGIAN 3: SENARAI TERPERINCI (Pandangan Undang-undang) --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-list-alt me-2 text-primary"></i> 1. Senarai Pandangan Undang-Undang Terperinci
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">Bil</th>
                        <th width="10%">Tarikh</th>
                        <th width="20%">Fakta Ringkas</th>
                        <th width="20%">Isu</th>
                        <th width="25%">Ringkasan Pandangan</th>
                        <th width="10%" class="text-center">Jenis</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $bil = 1; @endphp
                    @foreach ($data as $index => $kategori)
                        {{-- Kategori Header Row --}}
                        <tr class="table-light">
                            <td colspan="7" class="fw-bold ps-4 text-primary text-uppercase border-top border-bottom">
                                <i class="fas fa-folder me-2"></i> {{ $kategori['kategori'] }}
                            </td>
                        </tr>
                        
                        @forelse ($kategori['laporan'] as $item)
                            <tr>
                                <td class="text-center text-muted">{{ $bil++ }}</td>
                                <td>
                                    <span class="d-block fw-bold text-dark">{{ \Carbon\Carbon::parse($item->tarikh_terima)->format('d/m/Y') }}</span>
                                </td>
                                <td>{{ Str::limit($item->fakta_ringkasan, 100) }}</td>
                                <td>{{ Str::limit($item->isu, 100) }}</td>
                                <td>{{ Str::limit($item->ringkasan_pandangan, 100) }}</td>
                                <td class="text-center">
                                    @if ($item->jenis_pandangan === 'Lisan')
                                        <span class="badge bg-soft-secondary border">Lisan</span>
                                    @else
                                        <span class="badge bg-soft-primary border">Bertulis</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->tarikh_selesai)
                                        <span class="badge bg-soft-success mb-1">Selesai</span>
                                        <small class="d-block text-muted" style="font-size: 0.7rem;">
                                            {{ \Carbon\Carbon::parse($item->tarikh_selesai)->format('d/m/Y') }}
                                        </small>
                                    @else
                                        <span class="badge bg-soft-warning">Dalam Tindakan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted fst-italic py-3 small">
                                    Tiada rekod untuk kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    {{-- BAHAGIAN 4: LAPORAN KES MAHKAMAH (Terperinci) --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-gavel me-2 text-primary"></i> 2. Laporan Kes Mahkamah
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center">Bil</th>
                        <th>Tarikh Daftar</th>
                        <th>Jenis Kes / Pihak</th>
                        <th>Sebutan / Bicara</th>
                        <th>Fakta / Isu</th>
                        <th>Ringkasan Hujahan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan_kesmahkamah as $index => $laporan)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->tarikh_daftar)->format('d/m/Y') }}</td>
                            <td class="fw-bold">{{ $laporan->jenis_kes ?? '-' }}</td>
                            <td>{{ $laporan->tarikh_sebutan ? \Carbon\Carbon::parse($laporan->tarikh_sebutan)->format('d/m/Y') : '-' }}</td>
                            <td>
                                <small class="d-block fw-bold text-dark">Fakta:</small> {{ $laporan->fakta_ringkas ?? '-' }}
                                <div class="mt-2 border-top pt-1">
                                    <small class="d-block fw-bold text-dark">Isu:</small> {{ $laporan->isu ?? '-' }}
                                </div>
                            </td>
                            <td>{{ $laporan->ringkasan_hujahan ?? '-' }}</td>
                            <td>
                                @if(!empty($laporan->tarikh_selesai))
                                    <span class="badge bg-soft-success">Selesai</span>
                                    <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}</div>
                                @else
                                    <span class="badge bg-soft-warning">{{ $laporan->status ?? 'Aktif' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-3 d-block opacity-25"></i>
                                Tiada laporan kes mahkamah direkodkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- BAHAGIAN 5: PENGGUBALAN --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-pen-nib me-2 text-primary"></i> 3. Penggubalan
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">Bil</th>
                        <th>Tajuk Rang Undang-Undang / Perundangan</th>
                        <th width="30%">Tindakan</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan_gubalan as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $item->tajuk }}</td>
                            <td>{{ $item->tindakan }}</td>
                            <td><span class="badge bg-soft-secondary border text-dark">{{ $item->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted fst-italic">Tiada rekod penggubalan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- BAHAGIAN 6: PINDAAN --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-edit me-2 text-primary"></i> 4. Pindaan
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">Bil</th>
                        <th>Tajuk Rang Undang-Undang / Perundangan</th>
                        <th width="30%">Tindakan</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan_pindaan as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $item->tajuk }}</td>
                            <td>{{ $item->tindakan }}</td>
                            <td><span class="badge bg-soft-secondary border text-dark">{{ $item->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted fst-italic">Tiada rekod pindaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- BAHAGIAN 7: SEMAKAN --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-search me-2 text-primary"></i> 5. Semakan
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">Bil</th>
                        <th>Tajuk Rang Undang-Undang / Perundangan</th>
                        <th width="30%">Tindakan</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan_semakan as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $item->tajuk }}</td>
                            <td>{{ $item->tindakan }}</td>
                            <td><span class="badge bg-soft-secondary border text-dark">{{ $item->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted fst-italic">Tiada rekod semakan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- BAHAGIAN 8: MESYUARAT --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-users me-2 text-primary"></i> 6. Laporan Mesyuarat
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0 text-center">
                <thead>
                    <tr>
                        <th rowspan="2">Bil</th>
                        <th rowspan="2" class="text-start">Mesyuarat / Isu</th>
                        <th rowspan="2">Tarikh</th>
                        <th rowspan="2">Status</th>
                        <th colspan="2" class="bg-secondary bg-opacity-10 text-dark">Jenis Pandangan</th>
                    </tr>
                    <tr>
                        <th style="font-size: 0.75rem;">Lisan</th>
                        <th style="font-size: 0.75rem;">Bertulis</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan_mesyuarat as $index => $laporan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start">
                                <div class="fw-bold text-dark">{{ $laporan->mesyuarat }}</div>
                                <small class="text-muted">{{ $laporan->isu }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($laporan->tarikh_mesyuarat)->format('d/m/Y') }}</td>
                            <td>{{ $laporan->status }}</td>
                            <td>
                                @if($laporan->pandangan === 'Lisan') <i class="fas fa-check-circle text-success"></i> @endif
                            </td>
                            <td>
                                @if($laporan->pandangan === 'Bertulis') <i class="fas fa-check-circle text-success"></i> @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-3 text-muted fst-italic">Tiada mesyuarat direkodkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- BAHAGIAN 9: KES TATATERTIB --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-exclamation-triangle me-2 text-primary"></i> 7. Kes Tatatertib
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">Bil</th>
                        <th width="10%">Tarikh Terima</th>
                        <th width="30%">Fakta / Isu</th>
                        <th>Pandangan</th>
                        <th width="15%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $bil = 1; 
                         $kategori_tatatertib = [
                            'PRIMA FACIE' => 'Menyemak Penentuan Kes Prima Facie / Kertas Pertuduhan',
                            'SURCAJ' => 'Kes Surcaj / Laporan Jawatankuasa Siasatan',
                            'PENAMATAN' => 'Ulasan Penamatan Demi Kepentingan Awam'
                         ];
                    @endphp

                    @foreach ($kategori_tatatertib as $key => $tajuk)
                        <tr class="table-light">
                            <td colspan="5" class="fw-bold ps-4 text-dark border-top border-bottom text-uppercase" style="font-size: 0.85rem;">
                                <i class="fas fa-caret-right me-2 text-muted"></i> {{ $tajuk }}
                            </td>
                        </tr>
                        @php $laporanKategori = $laporan_tatatertib->where('kategori', $key); @endphp

                        @forelse ($laporanKategori as $laporan)
                            <tr>
                                <td class="text-center">{{ $bil++ }}</td>
                                <td>{{ \Carbon\Carbon::parse($laporan->tarikh_terima)->format('d/m/Y') }}</td>
                                <td>
                                    <small class="fw-bold">Fakta:</small> {{ $laporan->fakta_ringkasan }}<br>
                                    <small class="fw-bold">Isu:</small> {{ $laporan->isu }}
                                </td>
                                <td>{{ $laporan->ringkasan_pandangan }}</td>
                                <td>
                                    @if($laporan->tarikh_selesai)
                                        <span class="badge bg-soft-success">Selesai</span>
                                        <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}</div>
                                    @else
                                        <span class="badge bg-soft-warning">{{ $laporan->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted small py-2">Tiada rekod.</td></tr>
                        @endforelse
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    {{-- BAHAGIAN 10: LAIN-LAIN TUGASAN --}}
    <div class="report-card">
        <div class="report-header">
            <h5 class="report-title">
                <i class="fas fa-tasks me-2 text-primary"></i> 8. Lain-Lain Tugasan
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">Bil</th>
                        <th>Perihal Tugasan</th>
                        <th class="text-center" width="15%">Tarikh</th>
                        <th width="25%">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan_lainlain as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $item->perihal }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->tarikh)->format('d/m/Y') }}</td>
                            <td>{{ $item->tindakan }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted fst-italic">Tiada tugasan lain direkodkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Skrip Auto-Calculate untuk Input (Jika ada input form) --}}
<script>
    // Kekalkan skrip asal jika ada penggunaan input di masa hadapan
    document.addEventListener('DOMContentLoaded', function () {
        // Logic pengiraan boleh diletakkan di sini jika perlu
    });
</script>

@endsection