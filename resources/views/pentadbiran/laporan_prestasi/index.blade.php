@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard Prestasi PPUUN</h1>
            <p class="text-muted small">Paparan terperinci pencapaian mengikut Outcome.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pentadbiran.laporan_prestasi.cetak', ['tahun' => request('tahun', date('Y'))]) }}" 
               class="btn btn-success shadow-sm" target="_blank">
                <i class="fas fa-print me-2"></i> Cetak Laporan
            </a>
        </div>
    </div>

    {{-- ROW 1: FILTER & PURATA PENCAPAIAN --}}
    <div class="row mb-4">
        {{-- Filter Tahun --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <label class="fw-bold me-3 text-secondary mb-0"><i class="far fa-calendar-alt me-1"></i> TAHUN LAPORAN:</label>
                    <select name="tahun" class="form-select w-auto shadow-sm border-primary" onchange="window.location.href='{{ route('pentadbiran.laporan_prestasi.index') }}?tahun='+this.value">
                        @for($i = date('Y'); $i >= 2023; $i--)
                            <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        {{-- Kad Purata Pencapaian --}}
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2" style="border-left: 5px solid #1cc88a;">
                <div class="card-body py-2 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Purata Pencapaian Keseluruhan</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['avg_pencapaian'] }}%</div>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: PECAHAN OUTCOME 1, 2, 3 --}}
    <div class="row">
        @foreach($outcomesList as $key => $details)
            @php
                $record = $data->has($key) ? $data[$key]->first() : null;
                $sasaran = $record->sasaran_tahunan ?? 90;
            @endphp

            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card shadow h-100">
                    
                    {{-- HEADER KAD: TAJUK PENUH --}}
                    <div class="card-header py-3 d-flex flex-row align-items-start justify-content-between {{ $record ? 'bg-primary text-white' : 'bg-light' }}">
                        <h6 class="m-0 fw-bold" style="font-size: 0.8rem; line-height: 1.4;">
                            {{ $key }}: {{ strtoupper($details['tajuk']) }}
                        </h6>

                        @if($record)
                            <a href="{{ route('pentadbiran.laporan_prestasi.create', ['outcome_id' => $key, 'tahun' => request('tahun', date('Y'))]) }}" 
                               class="btn btn-sm btn-light text-primary py-0 ms-2" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                        @else
                            <span class="badge bg-secondary ms-2">Belum Diisi</span>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        @if($record)
                            {{-- Penerangan KPI --}}
                            <div class="mb-3">
                                <span class="badge bg-secondary mb-1">KPI:</span>
                                <p class="small text-muted mb-0" style="line-height: 1.3;">
                                    {{ $record->kpi_desc }}
                                </p>
                            </div>

                            <hr class="my-2">

                            {{-- 1. PECAHAN SUKU TAHUN --}}
                            <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-2">Pencapaian Suku Tahun</h6>
                            <div class="mb-3">
                                @for($i = 1; $i <= 4; $i++)
                                    @php 
                                        $col = 'suku_'.$i; 
                                        $val = $record->$col;
                                        $width = ($sasaran > 0) ? ($val / $sasaran) * 100 : 0;
                                        $color = $width > 0 ? 'bg-info' : 'bg-secondary';
                                    @endphp
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="small fw-bold me-2" style="width: 50px;">Suku {{ $i }}:</span>
                                        <div class="progress flex-grow-1" style="height: 15px;">
                                            <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $width }}%" 
                                                 aria-valuenow="{{ $width }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $val }}
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <hr class="my-3">

                            {{-- 2. DATA CATATAN (DIPERBAIKI: TIADA SCROLLBAR) --}}
                            <div class="flex-grow-1">
                                <h6 class="text-xs font-weight-bold text-success text-uppercase mb-2">Data Catatan</h6>
                                {{-- 
                                     🔥 FIX: Buang 'height' dan 'overflow-y'. 
                                     Ganti dengan 'h-100' supaya dia memenuhi ruang kad secara natural.
                                --}}
                                <div class="bg-light p-3 rounded h-100" style="font-size: 0.85rem; border: 1px solid #e3e6f0;">
                                    @if(!empty($record->catatan_data))
                                        <ul class="ps-3 mb-0">
                                            @foreach($record->catatan_data as $catLabel => $catVal)
                                                <li class="mb-2">
                                                    <strong class="text-dark d-block" style="font-size: 0.75rem;">{{ $catLabel }}</strong>
                                                    <span class="text-primary">{{ $catVal }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <em class="text-muted">- Tiada catatan -</em>
                                    @endif
                                </div>
                            </div>

                        @else
                            {{-- KALAU TIADA DATA --}}
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted small">Tiada rekod untuk Outcome ini.</p>
                                <a href="{{ route('pentadbiran.laporan_prestasi.create', ['outcome_id' => $key, 'tahun' => request('tahun', date('Y'))]) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus"></i> Isi Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection