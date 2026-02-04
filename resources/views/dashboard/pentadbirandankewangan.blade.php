@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard Pentadbiran & Kewangan</h1>
            <p class="text-muted mb-0">Tahun: {{ $tahun }}</p>
        </div>
        <form action="{{ route('dashboard.kewangan') }}" method="GET" class="bg-white p-2 rounded shadow-sm">
            <select name="tahun" onchange="this.form.submit()" class="form-select border-0 fw-bold text-primary">
                @for($y = 2026; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    {{-- WARAN --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <h6 class="text-secondary small text-uppercase">Jumlah Perjawatan</h6>
                    <h2 class="fw-bold text-primary">{{ $metadata['totalWaran'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <h6 class="text-secondary small text-uppercase">Diisi</h6>
                    <h2 class="fw-bold text-success">{{ $metadata['totalIsi'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-danger">
                <div class="card-body">
                    <h6 class="text-secondary small text-uppercase">Kosong</h6>
                    <h2 class="fw-bold text-danger">{{ $metadata['totalKosong'] }}</h2>
                </div>
            </div>
        </div>
    </div>

{{-- KEWANGAN OVERVIEW --}}
    <div class="row mb-5">
        {{-- KAD KIRI: CARTA DONUT (WOW VERSION) --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-pie me-2"></i> Pecahan Perbelanjaan</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center h-100">
                        {{-- 1. BAHAGIAN CHART --}}
                        <div class="col-md-6 position-relative mb-3 mb-md-0">
                            <div style="height: 200px; position: relative;">
                                <canvas id="mainPieChart"></canvas>
                                {{-- Teks di Tengah Donut --}}
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Total Belanja</small>
                                    <div class="fw-bold text-dark" style="font-size: 14px; line-height: 1.2;">
                                        RM {{ number_format($totalBelanja / 1000000, 2) }}M
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. BAHAGIAN LEGEND (SENARAI TEPI) --}}
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush small">
                                @php 
                                    $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']; 
                                    $i = 0; 
                                @endphp
                                @foreach($data_graf as $kod => $data)
                                    @if($data['belanja'] > 0)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 py-1">
                                        <div class="d-flex align-items-center" style="max-width: 65%;">
                                            <span style="min-width: 10px; height: 10px; background-color: {{ $colors[$i] ?? '#ccc' }}; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                            <div class="text-truncate" title="{{ $data['tajuk'] }}">
                                                <span class="fw-bold text-dark" style="font-size: 11px;">{{ $kod }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-dark" style="font-size: 11px;">{{ number_format($data['peratus'], 0) }}%</span>
                                        </div>
                                    </li>
                                    @php $i++; @endphp
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KAD KANAN: PRESTASI KESELURUHAN --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100 bg-primary text-white">
                <div class="card-body d-flex flex-column justify-content-center p-5">
                    <h5 class="text-white-50 text-uppercase">Prestasi Keseluruhan</h5>
                    <h1 class="display-3 fw-bold">{{ number_format($prestasi, 1) }}%</h1>
                    <div class="row mt-4 text-center">
                        <div class="col-4 border-end">
                            <small class="text-white-50">Peruntukan</small>
                            <h4>RM {{ number_format($totalPeruntukan, 2) }}</h4>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-white-50">Belanja</small>
                            <h4 class="text-warning">RM {{ number_format($totalBelanja, 2) }}</h4>
                        </div>
                        <div class="col-4">
                            <small class="text-white-50">Baki</small>
                            {{-- Style Custom: Hijau Neon + Shadow --}}
                            <h4 class="fw-bold" style="color: #ccff90; text-shadow: 0px 0px 10px rgba(0,0,0,0.25);">
                                RM {{ number_format($totalPeruntukan - $totalBelanja, 2) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL KOD OBJEK --}}
    <h5 class="fw-bold text-dark mb-3">Perincian Mengikut Kod Objek</h5>
    <div class="row g-4 mb-5">
        @foreach($data_graf as $kod => $data)
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" 
                     style="background: linear-gradient(45deg, #4e73df, #224abe); color: white;">
                    <span class="fw-bold">{{ $kod }}</span>
                    <span class="badge bg-white text-dark">{{ number_format($data['peratus'], 0) }}%</span>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ $data['tajuk'] }}</h6>
                    <div class="progress mb-3" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: {{ $data['peratus'] }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Peruntukan:</span>
                        <span class="fw-bold">RM {{ number_format($data['peruntukan'], 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Belanja:</span>
                        <span class="text-danger fw-bold">RM {{ number_format($data['belanja'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    var labels = [];
    var dataValues = [];
    var colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];

    @foreach($data_graf as $d)
        @if($d['peruntukan'] > 0)
            labels.push("{{ $d['tajuk'] }}");
            dataValues.push({{ $d['belanja'] }});
        @endif
    @endforeach

    if(document.getElementById("mainPieChart")) {
        var ctx = document.getElementById("mainPieChart");
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            },
        });
    }
</script>
@endpush