@extends('layouts.app')

@section('content')

{{-- Style Khas --}}
<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #0f172a;
    }

    .card-header {
        cursor: pointer;
    }

    .table thead th {
        background-color: #1e293b;
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        border: none;
        vertical-align: middle;
        padding: 10px 5px;
    }
    
    /* Tambahan style dari body */
    .kod-utama-row td {
        background-color: #f0f0f0;
        font-weight: bold;
        font-size: 10px;
        padding: 6px 4px;
        border-top: 2px solid #ddd !important;
    }
</style>

<div class="container-fluid px-4 py-4">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Laporan Prestasi Suku Tahun</h1>
        
        <div class="d-flex align-items-center">
            <form action="{{ route('kewangan.suku_tahun') }}" method="GET" class="mr-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-white border-0 font-weight-bold">Tahun</span>
                    </div>
                    <select name="tahun" class="form-control border-left-0 bg-white font-weight-bold text-primary" onchange="this.form.submit()" style="cursor: pointer;">
                        @php $startYear = 2026; $endYear = 2020; @endphp
                        @for($y = $startYear; $y >= $endYear; $y--)
                            <option value="{{ $y }}" {{ $tahun_dipilih == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
            
@if(Auth::user()->role !== 'user' || Auth::user()->role == 'ptk1')
    <a href="{{ route('kewangan.create') }}" class="btn btn-success shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Rekod
    </a>
@endif

<a href="{{ route('kewangan.cetak_pdf_suku', ['tahun' => $tahun_dipilih]) }}" target="_blank" class="btn btn-danger shadow-sm btn-sm px-3 py-2 fw-bold">
    <i class="fas fa-file-pdf me-2"></i> PDF Suku Tahun
</a>
        </div>
    </div>

    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h5 class="font-weight-bold text-dark text-uppercase">
                LAPORAN PRESTASI PERBELANJAAN SUKU TAHUN {{ $tahun_dipilih }}
            </h5>
            <p class="text-secondary font-weight-bold text-uppercase text-xs mb-0">
                {{ Auth::user()->negeri ?? 'IBU PEJABAT' }}
            </p>
            <hr class="mt-2 mb-0 border-primary" style="width: 60%; opacity: 0.3;">
        </div>
    </div>

    <div class="card shadow mb-5 border-top-primary">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-dark" width="100%" cellspacing="0">
                    
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2" style="vertical-align: middle; background-color: #4e73df; color: white; border: 1px solid #fff; width: 5%;">KOD</th>
                            <th rowspan="2" style="vertical-align: middle; background-color: #4e73df; color: white; border: 1px solid #fff; width: 25%;">BUTIRAN</th>
                            <th rowspan="2" style="vertical-align: middle; background-color: #4e73df; color: white; border: 1px solid #fff; width: 10%;">PERUNTUKAN<br>(RM)</th>
                            <th colspan="5" class="text-uppercase font-weight-bold" style="background-color: #4e73df; color: white; border: 1px solid #fff;">Prestasi Perbelanjaan (RM)</th>
                        <tr class="text-center">
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff;">SUKU 1</th>
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff;">SUKU 2</th>
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff;">SUKU 3</th>
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff;">SUKU 4</th>
                            <th style="background-color: #f6c23e; color: #000; border: 1px solid #fff;">JUMLAH</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php 
                            $g_s1 = 0; $g_s2 = 0; $g_s3 = 0; $g_s4 = 0; 
                            $g_peruntukan = 0; $g_belanja = 0;
                        @endphp
                        
                        @foreach($laporan_kewangan as $kod_utama => $data)
                            
                            @php
                                $sub_s1 = 0; $sub_s2 = 0; $sub_s3 = 0; $sub_s4 = 0;
                                $sub_peruntukan = 0; $sub_belanja = 0;

                                foreach($data['items'] as $itm) {
                                    // LOGIC FIX: KIRA SEMULA SUKU TAHUN DARI 12 BULAN
                                    $s1_val = $itm->belanja_jan + $itm->belanja_feb + $itm->belanja_mac;
                                    $s2_val = $itm->belanja_apr + $itm->belanja_mei + $itm->belanja_jun;
                                    $s3_val = $itm->belanja_jul + $itm->belanja_ogos + $itm->belanja_sep;
                                    $s4_val = $itm->belanja_okt + $itm->belanja_nov + $itm->belanja_dis;
                                    
                                    $sub_s1 += $s1_val;
                                    $sub_s2 += $s2_val;
                                    $sub_s3 += $s3_val;
                                    $sub_s4 += $s4_val;

                                    $sub_peruntukan += $itm->peruntukan;
                                    $sub_belanja += $itm->belanja; // Guna total belanja dari DB (sudah dikira di Controller)
                                }

                                // Masukkan ke Grand Total
                                $g_s1 += $sub_s1; $g_s2 += $sub_s2; $g_s3 += $sub_s3; $g_s4 += $sub_s4;
                                $g_peruntukan += $sub_peruntukan; $g_belanja += $sub_belanja;
                            @endphp

                            <tr class="kod-utama-row">
                                <td class="text-center font-weight-bold text-dark">{{ $kod_utama }}</td>
                                <td colspan="8" class="font-weight-bold text-dark text-uppercase">{{ $data['tajuk'] }}</td>
                            </tr>

                            @if(count($data['items']) > 0)
                                @foreach($data['items'] as $item)
                                    @php
                                        $baki_item = $item->peruntukan - $item->belanja;
                                        // PENGIRAAN SUKU TAHUN UNTUK BARIS ITEM
                                        $item_s1 = $item->belanja_jan + $item->belanja_feb + $item->belanja_mac;
                                        $item_s2 = $item->belanja_apr + $item->belanja_mei + $item->belanja_jun;
                                        $item_s3 = $item->belanja_jul + $item->belanja_ogos + $item->belanja_sep;
                                        $item_s4 = $item->belanja_okt + $item->belanja_nov + $item->belanja_dis;
                                    @endphp
                                    
                                    <tr class="bg-white">
                                        <td class="text-center font-weight-bold text-secondary">{{ $item->kod_objek }}</td>
                                        <td>{{ $item->butiran }}</td>
                                        <td class="text-right font-weight-bold text-dark">{{ number_format($item->peruntukan, 2) }}</td>
                                        
                                        <td class="text-right text-secondary">{{ number_format($item_s1, 2) }}</td>
                                        <td class="text-right text-secondary">{{ number_format($item_s2, 2) }}</td>
                                        <td class="text-right text-secondary">{{ number_format($item_s3, 2) }}</td>
                                        <td class="text-right text-secondary">{{ number_format($item_s4, 2) }}</td>

                                        <td class="text-right font-weight-bold text-dark" style="background-color: #fffcf0; border-left: 1px solid #ddd;">
                                            {{ number_format($item->belanja, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                
                                <tr style="background-color: #f8f9fc; font-size: 0.9rem; border-bottom: 2px solid #e3e6f0;">
                                    <td colspan="2" class="text-right font-weight-bold text-secondary">SUB-JUMLAH {{ $kod_utama }}:</td>
                                    <td class="text-right font-weight-bold text-dark">{{ number_format($sub_peruntukan, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_s1, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_s2, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_s3, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_s4, 2) }}</td>
                                    <td class="text-right font-weight-bold text-primary">{{ number_format($sub_belanja, 2) }}</td>
                                </tr>

                            @else
                                <tr><td colspan="9" class="text-center text-muted small py-3">Tiada rekod.</td></tr>
                            @endif
                        @endforeach

                        <tr style="background-color: #2e59d9; color: white; border-top: 3px solid #224abe;">
                            <td colspan="2" class="text-right font-weight-bold text-uppercase py-3">JUMLAH BESAR:</td>
                            <td class="text-right font-weight-bold py-3">{{ number_format($g_peruntukan, 2) }}</td>
                            <td class="text-right font-weight-bold py-3">{{ number_format($g_s1, 2) }}</td>
                            <td class="text-right font-weight-bold py-3">{{ number_format($g_s2, 2) }}</td>
                            <td class="text-right font-weight-bold py-3">{{ number_format($g_s3, 2) }}</td>
                            <td class="text-right font-weight-bold py-3">{{ number_format($g_s4, 2) }}</td>
                            <td class="text-right font-weight-bold py-3" style="background-color: #f6c23e; color: black;">{{ number_format($g_belanja, 2) }}</td>
                            <td class="py-3"></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection