@extends('layouts.app')

@push('styles')
<style>
    /* Kad General */
    .card-finance {
        border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        background: #fff; transition: transform 0.3s; height: 100%;
    }
    .card-finance:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    
    /* Header Warna-Warni ikut Kod */
    .finance-header {
        padding: 15px; border-radius: 15px 15px 0 0; color: white;
        font-weight: bold; letter-spacing: 0.5px;
    }
    .header-10000 { background: linear-gradient(45deg, #1565c0, #42a5f5); } /* Biru */
    .header-20000 { background: linear-gradient(45deg, #2e7d32, #66bb6a); } /* Hijau */
    .header-30000 { background: linear-gradient(45deg, #ef6c00, #ffa726); } /* Oren */
    .header-40000 { background: linear-gradient(45deg, #c62828, #ef5350); } /* Merah */
    .header-50000 { background: linear-gradient(45deg, #6a1b9a, #ab47bc); } /* Ungu */

    .stat-value { font-size: 1.1rem; font-weight: 800; color: #333; }
    .stat-label { font-size: 0.7rem; color: #777; text-transform: uppercase; letter-spacing: 1px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold"><i class="fas fa-coins me-2"></i> Dashboard Kewangan</h1>
            <p class="text-muted mb-0">Prestasi perbelanjaan keseluruhan tahun {{ $tahun }}</p>
        </div>
        
        <form action="{{ route('dashboard.kewangan') }}" method="GET" class="d-flex align-items-center bg-white p-2 rounded shadow-sm">
            <label for="tahunSelect" class="mb-0 mr-2 font-weight-bold text-secondary small text-uppercase">Tahun:</label>
            <select name="tahun" id="tahunSelect" class="form-select form-select-sm border-0 fw-bold text-primary" onchange="this.form.submit()" style="width: auto; cursor: pointer;">
                @for($y = 2026; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <div class="row g-4">
        @if(isset($data_graf) && count($data_graf) > 0)
            @foreach($data_graf as $kod => $data)
                {{-- Hanya papar kad jika ada peruntukan --}}
                @if($data['peruntukan'] > 0) 
                <div class="col-xl-6 col-lg-12">
                    <div class="card card-finance">
                        <div class="finance-header header-{{ $kod }} d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-folder-open me-2"></i> {{ $kod }} - {{ $data['tajuk'] }}</span>
                            <span class="badge bg-white text-dark">{{ number_format($data['peratus'], 1) }}%</span>
                        </div>
                        
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-sm-4 text-center mb-3 mb-sm-0">
                                    <div style="height: 150px; position: relative;">
                                        <canvas id="chart{{ $kod }}"></canvas>
                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                            <small class="text-muted" style="font-size: 10px;">Guna</small>
                                            <div class="fw-bold {{ $data['peratus'] > 90 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($data['peratus'], 0) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-8">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                                <span class="stat-label">Peruntukan</span>
                                                <span class="stat-value text-dark">RM {{ number_format($data['peruntukan'], 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 pt-2">
                                                <span class="stat-label text-danger">Belanja</span>
                                                <span class="stat-value text-danger">RM {{ number_format($data['belanja'], 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center pt-2">
                                                <span class="stat-label text-success">Baki</span>
                                                <span class="stat-value text-success">RM {{ number_format($data['baki'], 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mt-3" style="height: 6px;">
                                        <div class="progress-bar {{ $data['peratus'] > 90 ? 'bg-danger' : 'bg-success' }}" 
                                             role="progressbar" 
                                             style="width: {{ $data['peratus'] }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        @else
            <div class="col-12 text-center py-5">
                <div class="text-muted">Data tidak dijumpai.</div>
            </div>
        @endif
    </div>

    {{-- Mesej jika semua peruntukan 0 --}}
    @if(isset($data_graf) && collect($data_graf)->sum('peruntukan') == 0)
        <div class="text-center py-5 text-muted">
            <i class="fas fa-file-invoice-dollar fa-4x mb-3 opacity-25"></i>
            <h4>Tiada Data Kewangan Tahun {{ $tahun }}</h4>
            <p>Sila tambah rekod kewangan untuk tahun ini.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Loop semua data dari controller untuk generate graf secara automatik
    @if(isset($data_graf))
        @foreach($data_graf as $kod => $data)
            @if($data['peruntukan'] > 0)
                new Chart(document.getElementById('chart{{ $kod }}').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Belanja', 'Baki'],
                        datasets: [{
                            data: [{{ $data['belanja'] }}, {{ $data['baki'] }}],
                            backgroundColor: ['#dc3545', '#198754'], // Merah (Belanja), Hijau (Baki)
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%', // Saiz lubang tengah
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let value = context.raw;
                                        return ' RM ' + value.toLocaleString('en-US', {minimumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        @endforeach
    @endif
</script>
@endpush