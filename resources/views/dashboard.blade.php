@extends('layouts.app')

@push('styles')
<style>
    /* --- Styles Premium & Compact --- */
    .dashboard-container { padding: 1.5rem; }
    
    .card-metric {
        background: #fff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
        border-top: 4px solid transparent; 
        overflow: hidden;
    }
    .card-metric:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }

    /* Header */
    .card-metric-header {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex; justify-content: space-between; align-items: center;
        background: rgba(248, 249, 250, 0.6);
    }
    .metric-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #555; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px;}
    .link-arrow { font-size: 0.7rem; color: #999; text-decoration: none; transition:0.2s; }
    .link-arrow:hover { color: #333; }

    /* Chart Area */
    .chart-wrapper { position: relative; height: 180px; padding: 10px; }

    /* Colors */
    .border-blue { border-color: #2563eb !important; }
    .border-red { border-color: #e11d48 !important; }
    .border-green { border-color: #059669 !important; }
    .border-orange { border-color: #d97706 !important; }
    .border-cyan { border-color: #0891b2 !important; }
    .border-purple { border-color: #7c3aed !important; }
    .border-gray { border-color: #475569 !important; }
    
    .icon-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="row align-items-end mb-4">
        <div class="col">
            <h3 class="fw-bold text-dark mb-0">Dashboard Prestasi</h3>
            <p class="text-muted small mb-0">Ringkasan Laporan Tahun {{ now()->year }}</p>
        </div>
    </div>

    {{-- BARIS 1 --}}
    <div class="row g-3 mb-3">
        {{-- Pandangan --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card-metric border-blue">
                <div class="card-metric-header">
                    <div class="metric-title"><span class="icon-dot bg-primary"></span> Pandangan</div>
                    <a href="{{ route('laporanpandanganundang.index') }}" class="link-arrow">Lihat &rarr;</a>
                </div>
                <div class="chart-wrapper"><canvas id="grafPandanganUndang"></canvas></div>
            </div>
        </div>
        {{-- Mahkamah --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card-metric border-red">
                <div class="card-metric-header">
                    <div class="metric-title"><span class="icon-dot bg-danger"></span> Mahkamah</div>
                    <a href="{{ route('laporankesmahkamah.index') }}" class="link-arrow">Lihat &rarr;</a>
                </div>
                <div class="chart-wrapper"><canvas id="grafKesMahkamah"></canvas></div>
            </div>
        </div>
        {{-- Gubalan --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card-metric border-green">
                <div class="card-metric-header">
                    <div class="metric-title"><span class="icon-dot bg-success"></span> Gubalan</div>
                    <a href="{{ route('laporangubalanundang.index') }}" class="link-arrow">Lihat &rarr;</a>
                </div>
                <div class="chart-wrapper"><canvas id="grafGubalanUndang"></canvas></div>
            </div>
        </div>
        {{-- Pindaan --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card-metric border-orange">
                <div class="card-metric-header">
                    <div class="metric-title"><span class="icon-dot bg-warning"></span> Pindaan</div>
                    <a href="{{ route('laporanpindaanundang.index') }}" class="link-arrow">Lihat &rarr;</a>
                </div>
                <div class="chart-wrapper"><canvas id="grafPindaanUndang"></canvas></div>
            </div>
        </div>
    </div>

    {{-- BARIS 2 --}}
    <div class="row g-3">
        {{-- Semakan --}}
        <div class="col-xl-4 col-lg-4 col-md-6">
            <div class="card-metric border-cyan">
                <div class="card-metric-header">
                    <div class="metric-title"><span class="icon-dot bg-info"></span> Semakan</div>
                    <a href="{{ route('laporansemakanundang.index') }}" class="link-arrow">Lihat &rarr;</a>
                </div>
                <div class="chart-wrapper"><canvas id="grafSemakanUndang"></canvas></div>
            </div>
        </div>
        {{-- Mesyuarat --}}
        <div class="col-xl-4 col-lg-4 col-md-6">
            <div class="card-metric border-purple">
                <div class="card-metric-header">
                    <div class="metric-title"><span class="icon-dot" style="background:#7c3aed;"></span> Mesyuarat</div>
                    <a href="{{ route('laporanmesyuarat.index') }}" class="link-arrow">Lihat &rarr;</a>
                </div>
                <div class="chart-wrapper"><canvas id="grafMesyuarat"></canvas></div>
            </div>
        </div>
        {{-- Tatatertib --}}
        <div class="col-xl-4 col-lg-4 col-md-6">
            <div class="card-metric border-gray">
                <div class="card-metric-header">
                    <div class="metric-title"><span class="icon-dot bg-secondary"></span> Tatatertib</div>
                    <a href="{{ route('kestatatertib.index') }}" class="link-arrow">Lihat &rarr;</a>
                </div>
                <div class="chart-wrapper"><canvas id="grafTatatertib"></canvas></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#94a3b8';

        // Fungsi Render Chart (Dengan Drill Down)
        function renderChart(canvasId, controllerData, routeUrl, colorHex) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            // 1. Ambil Data dari Controller
            let chartData = controllerData;

            // 2. Override Warna Dataset supaya ikut tema kad & buat Gradient
            if (chartData && chartData.datasets && chartData.datasets[0]) {
                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, colorHex); 
                gradient.addColorStop(1, 'rgba(255, 255, 255, 0.1)');

                chartData.datasets[0].backgroundColor = gradient;
                chartData.datasets[0].borderColor = colorHex;
                chartData.datasets[0].borderWidth = 1;
                chartData.datasets[0].borderRadius = 4;
                chartData.datasets[0].barPercentage = 0.6;
            }

            // 3. Render Chart
            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 6 }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 2], color: '#f1f5f9' },
                            ticks: { display: false } 
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 9 }, autoSkip: true }
                        }
                    },
                    // 🔥🔥 FUNGSI DRILL DOWN (KLIK BAR) 🔥🔥
                    onClick: (evt, elements) => {
                        if (elements.length > 0) {
                            // Ambil index bar yang diklik (0=Jan, 1=Feb...)
                            const index = elements[0].index;
                            
                            // Bulan = index + 1 (Sebab index mula 0)
                            const bulan = index + 1;
                            
                            // Tahun Semasa (Dari PHP)
                            const tahun = {{ now()->year }};
                            
                            // Redirect ke page pecahan dengan parameter
                            window.location.href = `${routeUrl}?bulan=${bulan}&tahun=${tahun}`;
                        }
                    },
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    }
                }
            });
        }

        // --- PANGGIL DATA (Variable match Controller Asal) ---
        // Saya kembalikan 'route(...pecahan)' seperti kod asal supaya drill down jumpa jalan
        
        renderChart('grafPandanganUndang', {!! json_encode($dataPandanganUndang) !!}, "{{ route('laporanpandanganundang.pecahan') }}", '#2563eb');
        renderChart('grafKesMahkamah', {!! json_encode($dataKesMahkamah) !!}, "{{ route('laporankesmahkamah.pecahan') }}", '#e11d48');
        renderChart('grafGubalanUndang', {!! json_encode($dataGubalan) !!}, "{{ route('laporangubalanundang.pecahan') }}", '#059669');
        renderChart('grafPindaanUndang', {!! json_encode($dataPindaan) !!}, "{{ route('laporanpindaanundang.pecahan') }}", '#d97706');
        renderChart('grafSemakanUndang', {!! json_encode($dataSemakan) !!}, "{{ route('laporansemakanundang.pecahan') }}", '#0891b2');
        renderChart('grafMesyuarat', {!! json_encode($dataMesyuarat) !!}, "{{ route('laporanmesyuarat.pecahan') }}", '#7c3aed');
        renderChart('grafTatatertib', {!! json_encode($dataTataterib) !!}, "{{ route('kestatatertib.pecahan') }}", '#475569');

    });
</script>
@endpush