@extends('layouts.app')

@push('styles')
{{-- Import Google Font moden --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0d47a1 0%, #42a5f5 100%);
        --success-gradient: linear-gradient(135deg, #1b5e20 0%, #66bb6a 100%);
        --warning-gradient: linear-gradient(135deg, #f57f17 0%, #ffca28 100%);
        --info-gradient: linear-gradient(135deg, #006064 0%, #26c6da 100%);
        --card-bg: #ffffff;
        --bg-body: #f4f7fa;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-body);
    }

    /* --- Summary Cards (Top Row) --- */
    .kpi-card {
        border: none;
        border-radius: 20px;
        color: white;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        height: 140px;
    }
    
    .kpi-card:hover { transform: translateY(-5px); }
    
    .kpi-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        z-index: 2;
        position: relative;
    }

    .kpi-icon {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 8rem;
        opacity: 0.15;
        transform: rotate(-15deg);
        z-index: 1;
    }

    .kpi-title { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; margin-bottom: 5px; font-weight: 600; }
    .kpi-value { font-size: 2.5rem; font-weight: 700; line-height: 1; }

    /* --- Background Gradients (WARNA LAMA + WARNA BARU) --- */
    .bg-gradient-blue   { background: var(--primary-gradient); }
    .bg-gradient-green  { background: var(--success-gradient); }
    .bg-gradient-orange { background: var(--warning-gradient); }
    .bg-gradient-cyan   { background: var(--info-gradient); }
    
    /* 🔥 INI YANG SAYA TAMBAH (UNTUK 8 KAD) 🔥 */
    .bg-gradient-purple { background: linear-gradient(135deg, #7b1fa2 0%, #ab47bc 100%); }
    .bg-gradient-red    { background: linear-gradient(135deg, #c62828 0%, #ef5350 100%); }
    .bg-gradient-indigo { background: linear-gradient(135deg, #283593 0%, #5c6bc0 100%); }
    .bg-gradient-teal   { background: linear-gradient(135deg, #00695c 0%, #26a69a 100%); }

    /* --- Chart Cards --- */
    .chart-card {
        border: none;
        border-radius: 20px;
        background: var(--card-bg);
        box-shadow: 0 5px 25px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        transition: all 0.3s;
    }

    .chart-card:hover {
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    }

    .chart-header {
        padding: 1.5rem 1.5rem 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #37474f;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .chart-title i {
        background: #e3f2fd;
        color: #1565c0;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-right: 12px;
        font-size: 1rem;
    }

    .chart-body {
        padding: 1rem 1.5rem 1.5rem;
        position: relative;
        height: 350px; 
    }

    .page-header { margin-bottom: 30px; }
    .page-title { font-weight: 800; color: #1a237e; font-size: 1.8rem; }
    .text-muted-custom { color: #78909c; }

</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    {{-- HEADER SECTION --}}
    <div class="row align-items-end page-header">
        <div class="col-md-8">
            <p class="text-uppercase text-muted-custom mb-1 fw-bold fs-7">Dashboard Prestasi</p>
            <h2 class="page-title">Selamat Datang, Penasihat</h2>
            <p class="text-muted-custom mb-0">Analisis data dan statistik terkini bagi tahun {{ now()->year }}</p>
        </div>
        <div class="col-md-4 text-end d-none d-md-block">
            <span class="badge bg-white text-dark p-3 shadow-sm rounded-pill">
                <i class="far fa-calendar-alt me-2 text-primary"></i> {{ now()->format('d F Y') }}
            </span>
        </div>
    </div>

{{-- SECTION 1: TOP KPI CARDS (8 KATEGORI) --}}
    <div class="row g-4 mb-5">
        
        {{-- BARIS 1 --}}
        
        {{-- 1. Pandangan Undang-undang (Biru) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-gradient-blue">
                <div class="card-body">
                    <div class="kpi-title">Pandangan Undang-undang </div>
                    <div class="kpi-value">{{ array_sum($dataPandanganUndang['datasets'][0]['data'] ?? []) }}</div>
                </div>
                <i class="fas fa-comment-dots kpi-icon"></i>
            </div>
        </div>

        {{-- 2. Kes Mahkamah (Hijau) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-gradient-green">
                <div class="card-body">
                    <div class="kpi-title">Kes Mahkamah</div>
                    <div class="kpi-value">{{ array_sum($dataKesMahkamah['datasets'][0]['data'] ?? []) }}</div>
                </div>
                <i class="fas fa-gavel kpi-icon"></i>
            </div>
        </div>

        {{-- 3. Gubalan UU (Ungu - Baru) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-gradient-purple">
                <div class="card-body">
                    <div class="kpi-title">Gubalan Undang-undang</div>
                    <div class="kpi-value">{{ array_sum($dataGubalan['datasets'][0]['data'] ?? []) }}</div>
                </div>
                <i class="fas fa-pen-fancy kpi-icon"></i>
            </div>
        </div>

        {{-- 4. Pindaan Undang-undang (Oren - Baru) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-gradient-orange">
                <div class="card-body">
                    <div class="kpi-title">Pindaan Undang-undang </div>
                    <div class="kpi-value">{{ array_sum($dataPindaan['datasets'][0]['data'] ?? []) }}</div>
                </div>
                <i class="fas fa-edit kpi-icon"></i>
            </div>
        </div>

        {{-- BARIS 2 --}}

        {{-- 5. Semakan Undang-undang  (Teal - Asingkan dari Gubalan) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-gradient-teal">
                <div class="card-body">
                    <div class="kpi-title">Semakan Undang-undang </div>
                    <div class="kpi-value">{{ array_sum($dataSemakan['datasets'][0]['data'] ?? []) }}</div>
                </div>
                <i class="fas fa-search kpi-icon"></i>
            </div>
        </div>

        {{-- 6. Mesyuarat (Cyan) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-gradient-cyan">
                <div class="card-body">
                    <div class="kpi-title">Mesyuarat</div>
                    <div class="kpi-value">{{ array_sum($dataMesyuarat['datasets'][0]['data'] ?? []) }}</div>
                </div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
        </div>

        {{-- 7. Tatatertib (Merah - Baru) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-gradient-red">
                <div class="card-body">
                    <div class="kpi-title">Tatatertib</div>
                    {{-- Pastikan variable $dataTataterib ejaannya betul ikut controller --}}
                    <div class="kpi-value">{{ array_sum($dataTataterib['datasets'][0]['data'] ?? []) }}</div>
                </div>
                <i class="fas fa-exclamation-triangle kpi-icon"></i>
            </div>
        </div>

        {{-- 8. Lain-lain Tugasan (Indigo - Baru) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-gradient-indigo">
                <div class="card-body">
                    <div class="kpi-title">Lain-lain Tugasan</div>
                    <div class="kpi-value">{{ array_sum($dataTugasan['datasets'][0]['data'] ?? []) }}</div>
                </div>
                <i class="fas fa-tasks kpi-icon"></i>
            </div>
        </div>

    </div>

    {{-- SECTION 2: CHARTS GRID --}}
    <div class="row g-4">
        {{-- Row 1 Charts --}}
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="fas fa-comment-dots"></i> Pandangan Undang-Undang</h5>
                    <button class="btn btn-sm btn-light rounded-circle"><i class="fas fa-ellipsis-v"></i></button>
                </div>
                <div class="chart-body">
                    <canvas id="grafPandanganUndang"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="fas fa-gavel"></i> Kes Mahkamah</h5>
                    <button class="btn btn-sm btn-light rounded-circle"><i class="fas fa-ellipsis-v"></i></button>
                </div>
                <div class="chart-body">
                    <canvas id="grafKesMahkamah"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 2 Charts --}}
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="fas fa-file-contract"></i> Gubalan Undang-Undang</h5>
                </div>
                <div class="chart-body">
                    <canvas id="grafGubalanUndang"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="fas fa-edit"></i> Pindaan Undang-Undang</h5>
                </div>
                <div class="chart-body">
                    <canvas id="grafPindaanUndang"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 3 Charts --}}
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="fas fa-search"></i> Semakan Undang-Undang</h5>
                </div>
                <div class="chart-body">
                    <canvas id="grafSemakanUndang"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="fas fa-users"></i> Mesyuarat</h5>
                </div>
                <div class="chart-body">
                    <canvas id="grafMesyuarat"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 4 Charts --}}
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="fas fa-exclamation-triangle"></i> Tatatertib</h5>
                </div>
                <div class="chart-body">
                    <canvas id="grafTataterib"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="fas fa-tasks"></i> Lain-lain Tugasan</h5>
                </div>
                <div class="chart-body">
                    <canvas id="grafTugasan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // --- Setting Global Chart.js untuk Font & Look Modern ---
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#90a4ae';
    Chart.defaults.scale.grid.color = '#f0f4f8';
    
    // Fungsi untuk create Gradient pada Graf (Ini kunci WOW factor)
    function createGradient(ctx, colorStart, colorEnd) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, colorStart);
        gradient.addColorStop(1, colorEnd);
        return gradient;
    }

    // Palette Warna Modern
    const colors = {
        blue:   { start: '#448aff', end: '#2962ff' },
        purple: { start: '#ab47bc', end: '#7b1fa2' },
        green:  { start: '#66bb6a', end: '#2e7d32' },
        orange: { start: '#ffa726', end: '#ef6c00' },
        cyan:   { start: '#26c6da', end: '#00838f' },
        red:    { start: '#ef5350', end: '#c62828' }
    };

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }, // Sorok legend sebab tajuk dah ada
            tooltip: {
                backgroundColor: 'rgba(30, 41, 59, 0.9)',
                titleFont: { size: 13, family: 'Poppins' },
                bodyFont: { size: 14, weight: 'bold', family: 'Poppins' },
                padding: 12,
                cornerRadius: 8,
                displayColors: false
            }
        },
        scales: {
            x: { 
                grid: { display: false },
                ticks: { font: { weight: '600' } }
            }, 
            y: { 
                border: { display: false, dash: [5, 5] }, 
                grid: { color: '#eef2f5', borderDash: [5, 5] },
                ticks: { padding: 10 }
            }
        },
        elements: {
            bar: {
                borderRadius: 8, // Bar bulat yang modern
                borderSkipped: false
            }
        },
        onClick: (evt, elements, chart) => {
            // Logic redirect (sama seperti sebelum ini)
            if (elements.length > 0) {
                const index = elements[0].index;
                const bulan = index + 1;
                const tahun = {{ now()->year }};
                // Ambil URL dari canvas dataset (kita simpan dlm attribute nanti)
                const canvasId = chart.canvas.id;
                const routeUrl = document.getElementById(canvasId).dataset.url;
                if(routeUrl) window.location.href = `${routeUrl}?bulan=${bulan}&tahun=${tahun}`;
            }
        }
    };

    const renderChart = (canvasId, data, colorKey, routeUrl) => {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        // Simpan URL dalam dataset canvas untuk event onClick
        canvas.dataset.url = routeUrl;
        
        const ctx = canvas.getContext('2d');
        const gradient = createGradient(ctx, colors[colorKey].start, colors[colorKey].end);

        // Apply style pada dataset
        if (data.datasets) {
            data.datasets.forEach((dataset) => {
                dataset.backgroundColor = gradient;
                dataset.hoverBackgroundColor = colors[colorKey].start;
                dataset.barPercentage = 0.55; // Bar sedikit kurus & elegan
                dataset.categoryPercentage = 0.8;
            });
        }

        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: commonOptions
        });
    };

    // --- RENDER SEMUA GRAF DENGAN WARNA BERBEZA (Visual Variety) ---
    
    renderChart('grafPandanganUndang', {!! json_encode($dataPandanganUndang) !!}, 'blue', "{{ route('laporanpandanganundang.pecahan') }}");
    renderChart('grafKesMahkamah',     {!! json_encode($dataKesMahkamah) !!},     'red',  "{{ route('laporankesmahkamah.pecahan') }}");
    renderChart('grafGubalanUndang',   {!! json_encode($dataGubalan) !!},         'purple', "{{ route('laporangubalanundang.pecahan') }}");
    renderChart('grafPindaanUndang',   {!! json_encode($dataPindaan) !!},         'orange', "{{ route('laporanpindaanundang.pecahan') }}");
    renderChart('grafSemakanUndang',   {!! json_encode($dataSemakan) !!},         'green',  "{{ route('laporansemakanundang.pecahan') }}");
    renderChart('grafMesyuarat',       {!! json_encode($dataMesyuarat) !!},       'cyan',   "{{ route('laporanmesyuarat.pecahan') }}");
    renderChart('grafTataterib',       {!! json_encode($dataTataterib) !!},       'red',    "{{ route('kestatatertib.pecahan') }}");
    renderChart('grafTugasan',         {!! json_encode($dataTugasan) !!},         'blue',   "{{ route('lainlaintugasan.pecahan') }}");

</script>
@endpush