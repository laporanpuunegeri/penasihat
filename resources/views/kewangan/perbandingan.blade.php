@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Laporan Perbandingan Tahunan</h1>
        
        <form action="{{ route('kewangan.perbandingan') }}" method="GET" class="d-flex align-items-center">
            <label for="tahun" class="font-weight-bold mr-2 text-gray-700">Tahun Semasa:</label>
            <select name="tahun" class="form-control form-control-sm font-weight-bold text-primary shadow-sm" onchange="this.form.submit()" style="width: auto;">
                @php $startYear = 2026; $endYear = 2020; @endphp
                @for($y = $startYear; $y >= $endYear; $y--)
                    <option value="{{ $y }}" {{ $tahun_semasa == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <div class="row justify-content-center mb-4">
        <div class="col-12 text-center">
            <h5 class="font-weight-bold text-dark text-uppercase">
                PERBANDINGAN PRESTASI PERBELANJAAN 3 TAHUN TERKINI
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
                            <th rowspan="2" style="vertical-align: middle; background-color: #4e73df; color: white; border: 1px solid #fff; width: 30%;">BUTIRAN PERBELANJAAN</th>
                            
                            <th colspan="3" class="text-uppercase font-weight-bold" style="background-color: #4e73df; color: white; border: 1px solid #fff;">JUMLAH BELANJA (RM)</th>
                            
                            <th colspan="2" class="text-uppercase font-weight-bold" style="background-color: #224abe; color: white; border: 1px solid #fff;">BEZA ({{ $tahun_semasa }} vs {{ $tahun_lepas }})</th>
                        </tr>
                        <tr class="text-center">
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff; width: 12%;">{{ $tahun_2_lepas }}</th>
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff; width: 12%;">{{ $tahun_lepas }}</th>
                            <th style="background-color: #2e59d9; color: white; border: 1px solid #fff; width: 12%;">{{ $tahun_semasa }}</th>
                            
                            <th style="background-color: #f6c23e; color: #000; border: 1px solid #fff; width: 12%;">RM</th>
                            <th style="background-color: #f6c23e; color: #000; border: 1px solid #fff; width: 8%;">%</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $grand_semasa  = 0;
                            $grand_lepas   = 0;
                            $grand_2_lepas = 0;
                        @endphp

                        @foreach($laporan as $kod_utama => $data)
                            <tr style="background-color: #eaecf4; border-top: 2px solid #d1d3e2;">
                                <td class="text-center font-weight-bold text-dark">{{ $kod_utama }}</td>
                                <td colspan="7" class="font-weight-bold text-dark text-uppercase">{{ $data['tajuk'] }}</td>
                            </tr>

                            @if(count($data['items']) > 0)
                                @foreach($data['items'] as $item)
                                    @php
                                        $val_semasa  = (float)$item['belanja_semasa'];
                                        $val_lepas   = (float)$item['belanja_lepas'];
                                        $val_2_lepas = (float)$item['belanja_2_lepas'];
                                        
                                        // Kira Beza
                                        $diff_rm = $val_semasa - $val_lepas;
                                        $diff_percent = $val_lepas > 0 ? ($diff_rm / $val_lepas) * 100 : ($val_semasa > 0 ? 100 : 0);

                                        // Grand Total
                                        $grand_semasa  += $val_semasa;
                                        $grand_lepas   += $val_lepas;
                                        $grand_2_lepas += $val_2_lepas;

                                        // Warna Indikator
                                        $text_color = $diff_rm > 0 ? 'text-success' : ($diff_rm < 0 ? 'text-danger' : 'text-secondary');
                                        $icon = $diff_rm > 0 ? 'fa-arrow-up' : ($diff_rm < 0 ? 'fa-arrow-down' : 'fa-minus');
                                    @endphp

                                    <tr class="bg-white">
                                        <td class="text-center font-weight-bold text-secondary">{{ $item['kod_objek'] }}</td>
                                        <td>{{ $item['butiran'] }}</td>
                                        
                                        <td class="text-right text-secondary">{{ number_format($val_2_lepas, 2) }}</td>
                                        <td class="text-right text-secondary">{{ number_format($val_lepas, 2) }}</td>
                                        <td class="text-right font-weight-bold text-dark">{{ number_format($val_semasa, 2) }}</td>
                                        
                                        <td class="text-right font-weight-bold {{ $text_color }}" style="background-color: #fffcf0; border-left: 1px solid #ddd;">
                                            {{ $diff_rm > 0 ? '+' : '' }}{{ number_format($diff_rm, 2) }}
                                        </td>
                                        <td class="text-center font-weight-bold {{ $text_color }}" style="background-color: #fffcf0;">
                                            <i class="fas {{ $icon }} text-xs mr-1"></i> {{ number_format(abs($diff_percent), 1) }}%
                                        </td>
                                    </tr>
                                @endforeach

                                @php
                                    $sub_semasa  = collect($data['items'])->sum('belanja_semasa');
                                    $sub_lepas   = collect($data['items'])->sum('belanja_lepas');
                                    $sub_2_lepas = collect($data['items'])->sum('belanja_2_lepas');
                                    $sub_diff    = $sub_semasa - $sub_lepas;
                                    $sub_percent = $sub_lepas > 0 ? ($sub_diff / $sub_lepas) * 100 : ($sub_semasa > 0 ? 100 : 0);
                                    $sub_color   = $sub_diff > 0 ? 'text-success' : ($sub_diff < 0 ? 'text-danger' : 'text-secondary');
                                @endphp
                                <tr style="background-color: #f8f9fc; font-size: 0.9rem; border-bottom: 2px solid #e3e6f0;">
                                    <td colspan="2" class="text-right font-weight-bold text-secondary">JUMLAH {{ $kod_utama }}:</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_2_lepas, 2) }}</td>
                                    <td class="text-right font-weight-bold text-gray-600">{{ number_format($sub_lepas, 2) }}</td>
                                    <td class="text-right font-weight-bold text-primary">{{ number_format($sub_semasa, 2) }}</td>
                                    <td class="text-right font-weight-bold {{ $sub_color }}">{{ number_format($sub_diff, 2) }}</td>
                                    <td class="text-center font-weight-bold {{ $sub_color }}">{{ number_format(abs($sub_percent), 1) }}%</td>
                                </tr>

                            @else
                                <tr><td colspan="7" class="text-center text-muted small py-3">Tiada rekod.</td></tr>
                            @endif
                        @endforeach

                        @php
                            $grand_diff = $grand_semasa - $grand_lepas;
                            $grand_percent = $grand_lepas > 0 ? ($grand_diff / $grand_lepas) * 100 : ($grand_semasa > 0 ? 100 : 0);
                        @endphp
                        <tr style="background-color: #2e59d9; color: white; border-top: 3px solid #ffffff;">
                            <td colspan="2" class="text-right font-weight-bold text-uppercase py-3" style="border: 1px solid #fff;">JUMLAH BESAR:</td>
                            
                            <td class="text-right font-weight-bold py-3" style="background-color: #4e73df; border: 1px solid #fff; color: #ffffff;">
    {{ number_format($grand_2_lepas, 2) }}
                            </td>
                            <td class="text-right font-weight-bold py-3" style="background-color: #4e73df; border: 1px solid #fff; color: #ffffff;">
{{ number_format($grand_lepas, 2) }}
                            </td>

                            <td class="text-right font-weight-bold py-3" style="background-color: #4e73df; border: 1px solid #fff; color: #ffffff;">
                                {{ number_format($grand_semasa, 2) }}
                            </td>

                            <td class="text-right font-weight-bold py-3" style="background-color: #f6c23e; color: black; border: 1px solid #fff;">
                                {{ $grand_diff > 0 ? '+' : '' }}{{ number_format($grand_diff, 2) }}
                            </td>
                            <td class="text-center font-weight-bold py-3" style="background-color: #f6c23e; color: black; border: 1px solid #fff;">
                                {{ number_format($grand_percent, 1) }}%
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection