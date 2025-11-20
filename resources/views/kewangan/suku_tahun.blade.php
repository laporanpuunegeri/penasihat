@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- HEADER & FILTER -->
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
            
            {{-- =========================================================== --}}
            {{-- BUTANG BARU: CETAK PDF (MERAH) --}}
            {{-- Saya tambah parameter ['tahun' => $tahun_dipilih] supaya PDF ikut tahun yang dipilih --}}
            {{-- =========================================================== --}}
            <a href="{{ route('kewangan.cetak_pdf', ['tahun' => $tahun_dipilih]) }}" class="btn btn-danger shadow-sm mr-2" target="_blank">
                <i class="fas fa-file-pdf fa-sm text-white-50 mr-1"></i> PDF
            </a>

            <a href="{{ route('kewangan.create') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Rekod
            </a>
        </div>
    </div>

    <!-- TAJUK -->
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

    <!-- JADUAL DATA -->
    <div class="card shadow mb-5 border-top-primary">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-dark" width="100%" cellspacing="0">
                    
                    <!-- HEADER TABLE (BIRU) -->
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2" style="vertical-align: middle; background-color: #4e73df; color: white; border: 1px solid #fff;">KOD</th>
                            <th rowspan="2" style="vertical-align: middle; background-color: #4e73df; color: white; border: 1px solid #fff; width: 30%;">BUTIRAN</th>
                            <th rowspan="2" style="vertical-align: middle; background-color: #4e73df; color: white; border: 1px solid #fff;">PERUNTUKAN<br>(RM)</th>
                            <th colspan="5" class="text-uppercase font-weight-bold" style="background-color: #4e73df; color: white; border: 1px solid #fff;">Prestasi Perbelanjaan (RM)</th>
                            <th rowspan="2" style="vertical-align: middle; background-color: #4e73df; color: white; border: 1px solid #fff;">EDIT</th>
                        </tr>
                        <tr class="text-center">
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff;">SUKU 1</th>
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff;">SUKU 2</th>
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff;">SUKU 3</th>
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff;">SUKU 4</th>
                            <th style="background-color: #f6c23e; color: #000; border: 1px solid #fff;">JUMLAH</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- VARIABLE GRAND TOTAL GLOBAL -->
                        @php 
                            $g_s1 = 0; $g_s2 = 0; $g_s3 = 0; $g_s4 = 0; 
                            $g_peruntukan = 0; $g_belanja = 0;
                        @endphp
                        
                        @foreach($laporan_kewangan as $kod_utama => $data)
                            
                            <!-- KIRA SUB-TOTAL (MANUAL LOOP) -->
                            @php
                                $sub_s1 = 0; $sub_s2 = 0; $sub_s3 = 0; $sub_s4 = 0;
                                $sub_peruntukan = 0; $sub_belanja = 0;

                                foreach($data['items'] as $itm) {
                                    $sub_s1 += $itm->belanja_s1;
                                    $sub_s2 += $itm->belanja_s2;
                                    $sub_s3 += $itm->belanja_s3;
                                    $sub_s4 += $itm->belanja_s4;
                                    $sub_peruntukan += $itm->peruntukan;
                                    $sub_belanja += $itm->belanja;
                                }

                                // Masukkan ke Grand Total
                                $g_s1 += $sub_s1; $g_s2 += $sub_s2; $g_s3 += $sub_s3; $g_s4 += $sub_s4;
                                $g_peruntukan += $sub_peruntukan; $g_belanja += $sub_belanja;
                            @endphp

                            <!-- ROW KATEGORI UTAMA -->
                            <tr style="background-color: #eaecf4; border-top: 2px solid #d1d3e2;">
                                <td class="text-center font-weight-bold text-dark">{{ $kod_utama }}</td>
                                <td colspan="8" class="font-weight-bold text-dark text-uppercase">{{ $data['tajuk'] }}</td>
                            </tr>

                            @if(count($data['items']) > 0)
                                @foreach($data['items'] as $item)
                                    <!-- DATA ROW -->
                                    <tr class="bg-white">
                                        <td class="text-center font-weight-bold text-secondary">{{ $item->kod_objek }}</td>
                                        <td>{{ $item->butiran }}</td>
                                        <td class="text-right font-weight-bold text-dark">{{ number_format($item->peruntukan, 2) }}</td>
                                        
                                        <!-- DATA SUKU TAHUN (PASTIKAN KELUAR) -->
                                        <td class="text-right text-secondary">{{ number_format($item->belanja_s1, 2) }}</td>
                                        <td class="text-right text-secondary">{{ number_format($item->belanja_s2, 2) }}</td>
                                        <td class="text-right text-secondary">{{ number_format($item->belanja_s3, 2) }}</td>
                                        <td class="text-right text-secondary">{{ number_format($item->belanja_s4, 2) }}</td>

                                        <!-- JUMLAH ROW -->
                                        <td class="text-right font-weight-bold text-dark" style="background-color: #fffcf0; border-left: 1px solid #ddd;">
                                            {{ number_format($item->belanja, 2) }}
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ route('kewangan.edit', $item->id) }}" class="text-warning" title="Edit">
                                                <i class="fas fa-pen-square fa-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                <!-- SUB-TOTAL ROW -->
                                <tr style="background-color: #f8f9fc; font-size: 0.9rem; border-bottom: 2px solid #e3e6f0;">
                                    <td colspan="2" class="text-right font-weight-bold text-secondary">JUMLAH {{ $kod_utama }}:</td>
                                    <td class="text-right font-weight-bold text-dark">{{ number_format($sub_peruntukan, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_s1, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_s2, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_s3, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_s4, 2) }}</td>
                                    <td class="text-right font-weight-bold text-primary">{{ number_format($sub_belanja, 2) }}</td>
                                    <td></td>
                                </tr>

                            @else
                                <tr><td colspan="9" class="text-center text-muted small py-3">Tiada rekod.</td></tr>
                            @endif
                        @endforeach

                        <!-- GRAND TOTAL ROW -->
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