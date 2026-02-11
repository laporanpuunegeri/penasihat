@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
    :root { --card-bg: #ffffff; --bg-body: #f4f7fa; }
    body { font-family: 'Poppins', sans-serif; background-color: var(--bg-body); }
    .kpi-card { border: none; border-radius: 20px; color: white; overflow: hidden; position: relative; box-shadow: 0 10px 20px rgba(0,0,0,0.1); height: 140px; transition: transform 0.3s; }
    .kpi-card:hover { transform: translateY(-5px); }
    .kpi-title { font-size: 0.85rem; text-transform: uppercase; font-weight: 600; opacity: 0.9; }
    .kpi-value { font-size: 2.5rem; font-weight: 700; }
    .kpi-icon { position: absolute; right: -20px; bottom: -20px; font-size: 8rem; opacity: 0.15; transform: rotate(-15deg); }
    .section-title { font-weight: 800; color: #1a237e; border-bottom: 3px solid #1a237e; display: inline-block; margin-bottom: 20px; padding-bottom: 5px; margin-top: 40px; }
    .chart-card { border: none; border-radius: 20px; background: white; padding: 10px; height: 350px; box-shadow: 0 5px 25px rgba(0,0,0,0.03); }
    
    /* Warna Background Gradient */
    .bg-blue { background: linear-gradient(135deg, #0d47a1, #42a5f5); }
    .bg-green { background: linear-gradient(135deg, #1b5e20, #66bb6a); }
    .bg-purple { background: linear-gradient(135deg, #7b1fa2, #ab47bc); }
    .bg-orange { background: linear-gradient(135deg, #f57f17, #ffca28); }
    .bg-teal { background: linear-gradient(135deg, #006064, #26c6da); }
    .bg-red { background: linear-gradient(135deg, #c62828, #ef5350); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="font-weight-bolder text-dark">Dashboard Eksekutif (YB)</h2>
            <p class="text-muted">Ringkasan Prestasi Keseluruhan Jabatan Tahun {{ $tahun }}</p>
        </div>
    </div>

    {{-- SEKSYEN 1: BAHAGIAN PENASIHAT --}}
    <h4 class="section-title"><i class="fas fa-briefcase"></i> 1. BAHAGIAN PENASIHAT</h4>
    
    {{-- KPI CARDS --}}
    <div class="row g-4 mb-4">
        @php
            $penasihatItems = [
                ['title' => 'Pandangan UU', 'key' => 'pandangan', 'bg' => 'bg-blue', 'icon' => 'fa-comment-dots'],
                ['title' => 'Kes Mahkamah', 'key' => 'mahkamah', 'bg' => 'bg-green', 'icon' => 'fa-gavel'],
                ['title' => 'Gubalan', 'key' => 'gubalan', 'bg' => 'bg-purple', 'icon' => 'fa-pen-fancy'],
                ['title' => 'Pindaan', 'key' => 'pindaan', 'bg' => 'bg-orange', 'icon' => 'fa-edit'],
                ['title' => 'Semakan', 'key' => 'semakan', 'bg' => 'bg-teal', 'icon' => 'fa-search'],
                ['title' => 'Mesyuarat', 'key' => 'mesyuarat', 'bg' => 'bg-blue', 'icon' => 'fa-users'],
                ['title' => 'Tatatertib', 'key' => 'tatatertib', 'bg' => 'bg-red', 'icon' => 'fa-exclamation-triangle'],
                ['title' => 'Lain-lain', 'key' => 'tugasan', 'bg' => 'bg-purple', 'icon' => 'fa-tasks'],
            ];
        @endphp

        @foreach($penasihatItems as $item)
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card {{ $item['bg'] }}">
                <div class="card-body">
                    <div class="kpi-title">{{ $item['title'] }}</div>
                    <div class="kpi-value">{{ array_sum($dataPenasihat[$item['key']]['datasets'][0]['data']) }}</div>
                    <i class="fas {{ $item['icon'] }} kpi-icon"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- GRAF PIE PENASIHAT (SEMUA 8 SKOP ADA DI SINI) --}}
    <div class="row g-4">
        <div class="col-md-3"><div class="chart-card"><h6 class="text-center small font-weight-bold">Pandangan</h6><canvas id="chartPenasihat1"></canvas></div></div>
        <div class="col-md-3"><div class="chart-card"><h6 class="text-center small font-weight-bold">Kes Mahkamah</h6><canvas id="chartPenasihat2"></canvas></div></div>
        <div class="col-md-3"><div class="chart-card"><h6 class="text-center small font-weight-bold">Gubalan</h6><canvas id="chartPenasihat3"></canvas></div></div>
        <div class="col-md-3"><div class="chart-card"><h6 class="text-center small font-weight-bold">Pindaan</h6><canvas id="chartPenasihat4"></canvas></div></div>
        
        {{-- Sambungan 4 Graf yang hilang tadi --}}
        <div class="col-md-3"><div class="chart-card"><h6 class="text-center small font-weight-bold">Semakan</h6><canvas id="chartPenasihat5"></canvas></div></div>
        <div class="col-md-3"><div class="chart-card"><h6 class="text-center small font-weight-bold">Mesyuarat</h6><canvas id="chartPenasihat6"></canvas></div></div>
        <div class="col-md-3"><div class="chart-card"><h6 class="text-center small font-weight-bold">Tatatertib</h6><canvas id="chartPenasihat7"></canvas></div></div>
        <div class="col-md-3"><div class="chart-card"><h6 class="text-center small font-weight-bold">Lain-lain</h6><canvas id="chartPenasihat8"></canvas></div></div>
    </div>


    {{-- SEKSYEN 2: GUAMAN --}}
    <h4 class="section-title"><i class="fas fa-balance-scale"></i> 2. BAHAGIAN GUAMAN (LITIGASI)</h4>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card kpi-card bg-blue">
                <div class="card-body">
                    <div class="kpi-title">JUMLAH KES GUAMAN</div>
                    <div class="kpi-value">{{ array_sum($dataGuaman['datasets'][0]['data']) }}</div>
                    <i class="fas fa-university kpi-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="chart-card" style="height: 300px;">
                <h6 class="text-center small font-weight-bold">Pecahan Bulanan</h6>
                <canvas id="chartGuaman"></canvas>
            </div>
        </div>
    </div>


    {{-- SEKSYEN 3: KEWANGAN --}}
    <h4 class="section-title"><i class="fas fa-hand-holding-usd"></i> 3. KEWANGAN & PENTADBIRAN</h4>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card kpi-card bg-green">
                <div class="card-body">
                    <div class="kpi-title">PERUNTUKAN</div>
                    <div class="kpi-value">RM {{ number_format($totalPeruntukan, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card bg-red">
                <div class="card-body">
                    <div class="kpi-title">BELANJA</div>
                    <div class="kpi-value">RM {{ number_format($totalBelanja, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="chart-card" style="height: 300px;">
                <h6 class="text-center small font-weight-bold">Prestasi Belanja vs Baki</h6>
                <canvas id="chartKewangan"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- LOAD LIBRARY UTAMA --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- LOAD PLUGIN DATALABELS (Untuk text %) --}}
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    // Register Plugin
    Chart.register(ChartDataLabels);
    Chart.defaults.font.family = "'Poppins', sans-serif";

    // Fungsi Render Pie dengan Text %
    const renderPie = (id, data, showPercent = true) => {
        const ctx = document.getElementById(id);
        if(!ctx) return;

        // Kira total untuk %
        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogos','Sep','Okt','Nov','Dis'],
                datasets: [{
                    data: data.datasets[0].data,
                    backgroundColor: ['#42a5f5', '#66bb6a', '#ffa726', '#ab47bc', '#26c6da', '#ef5350', '#5c6bc0', '#8d6e63', '#78909c', '#ec407a', '#9ccc65', '#ffca28'],
                    borderWidth: 1,
                    borderColor: '#fff'
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { display: false }, // Sorok legend supaya kemas
                    datalabels: {
                        color: '#fff',
                        font: { weight: '900', size: 25 },
                        formatter: (value, ctx) => {
                            if(value > 0 && showPercent) {
                                let percentage = ((value * 100) / total).toFixed(0) + "%";
                                return percentage;
                            } else {
                                return null; // Tak tunjuk kalau 0
                            }
                        }
                    }
                } 
            }
        });
    };

    // --- RENDER SEMUA 8 PIE CHART ---
    renderPie('chartPenasihat1', {!! json_encode($dataPenasihat['pandangan']) !!});
    renderPie('chartPenasihat2', {!! json_encode($dataPenasihat['mahkamah']) !!});
    renderPie('chartPenasihat3', {!! json_encode($dataPenasihat['gubalan']) !!});
    renderPie('chartPenasihat4', {!! json_encode($dataPenasihat['pindaan']) !!}); // Pindaan tadi salah ID
    renderPie('chartPenasihat5', {!! json_encode($dataPenasihat['semakan']) !!});
    renderPie('chartPenasihat6', {!! json_encode($dataPenasihat['mesyuarat']) !!});
    renderPie('chartPenasihat7', {!! json_encode($dataPenasihat['tatatertib']) !!});
    renderPie('chartPenasihat8', {!! json_encode($dataPenasihat['tugasan']) !!});

    // --- GUAMAN ---
    renderPie('chartGuaman', {!! json_encode($dataGuaman) !!});

    // --- KEWANGAN (SPECIAL CONFIG) ---
    const dataKew = {!! json_encode($dataKewanganChart) !!};
    new Chart(document.getElementById('chartKewangan'), {
        type: 'pie',
        data: {
            labels: ['Belanja', 'Baki'],
            datasets: [{
                data: dataKew.datasets[0].data,
                backgroundColor: ['#ef5350', '#66bb6a']
            }]
        },
        options: { 
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                datalabels: {
                    color: '#fff',
                    font: { weight: '900', size: 25},
                    formatter: (value, ctx) => {
                        let total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        return ((value * 100) / total).toFixed(1) + "%";
                    }
                }
            }
        }
    });
</script>
@endpush