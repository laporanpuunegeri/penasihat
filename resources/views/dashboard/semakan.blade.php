@extends('layouts.app')

@push('styles')
<style>
    /* --- Dashboard Styles (Tema Semakan - Hijau Teal & Putih) --- */
    /* Kita pakai warna sikit berbeza dari Penasihat supaya user tahu dia kat mana */
    .page-title { color: #00695c; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; } /* Hijau Teal */
    .page-subtitle { color: #546e7a; font-size: 0.9rem; }
    
    /* Card Style */
    .card-custom {
        border: none; border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        background-color: #fff;
    }
    .card-custom:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 105, 92, 0.15); }
    
    /* Card Header */
    .card-header-custom {
        background: #fff; border-bottom: 2px solid #b2dfdb; /* Garis hijau cair */
        padding: 1rem 1.2rem; display: flex; align-items: center;
        border-radius: 12px 12px 0 0;
    }
    .card-title-custom {
        font-size: 0.95rem; font-weight: 700; color: #00695c; margin: 0; text-transform: uppercase; display: flex; align-items: center;
    }
    .card-title-custom i { color: #26a69a; margin-right: 10px; width: 20px; text-align: center; font-size: 1.1rem; }
    
    /* Card Body */
    .card-body-custom { padding: 1rem; position: relative; height: 320px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            {{-- TAJUK KHAS UNTUK SEMAKAN --}}
            <h2 class="page-title"><i class="fas fa-search-plus me-2"></i> Dashboard Bahagian Semakan</h2>
            <p class="page-subtitle">Ringkasan statistik dan prestasi tahun {{ now()->year }}</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- 1. PANDANGAN UNDANG-UNDANG --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-comment-dots"></i> Pandangan Undang-Undang</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafPandanganUndang"></canvas></div>
            </div>
        </div>

        {{-- 2. KES MAHKAMAH --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-gavel"></i> Kes Mahkamah</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafKesMahkamah"></canvas></div>
            </div>
        </div>

        {{-- 3. GUBALAN --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-file-contract"></i> Gubalan Undang-Undang</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafGubalanUndang"></canvas></div>
            </div>
        </div>

        {{-- 4. PINDAAN --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-edit"></i> Pindaan Undang-Undang</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafPindaanUndang"></canvas></div>
            </div>
        </div>

        {{-- 5. SEMAKAN --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-search"></i> Semakan Undang-Undang</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafSemakanUndang"></canvas></div>
            </div>
        </div>

        {{-- 6. MESYUARAT --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-users"></i> Mesyuarat</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafMesyuarat"></canvas></div>
            </div>
        </div>

        {{-- 7. TATATERTIB --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-exclamation-triangle"></i> Tatatertib</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafTataterib"></canvas></div>
            </div>
        </div>

        {{-- 8. LAIN-LAIN TUGASAN --}}
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-tasks"></i> Lain-lain Tugasan</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafTugasan"></canvas></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // --- Konfigurasi Warna Tema Semakan (Hijau Teal) ---
    const themeColors = {
        primary: '#00897b', // Hijau Teal (Main Bar Color) - Beza sikit dari Penasihat
        light: '#80cbc4',   
    };

    // Setup Default Chart.js
    Chart.defaults.font.family = "'Segoe UI', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.color = '#666';
    
    // Setting Common
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                position: 'bottom', 
                labels: { usePointStyle: true, padding: 20, boxWidth: 10 } 
            },
            tooltip: { backgroundColor: 'rgba(0, 105, 92, 0.9)', padding: 12, cornerRadius: 8 }
        },
        scales: {
            x: { grid: { display: false } }, 
            y: { 
                beginAtZero: true, 
                border: { display: false }, 
                grid: { color: '#f0f0f0' },
                ticks: { precision: 0 } 
            }
        },
        onHover: (event, chartElement) => {
            event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
        }
    };

    // --- FUNGSI RENDER GRAF ---
    const renderChart = (canvasId, type, data, routeUrl) => {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        // Paksa Styling Bar (Warna Hijau Teal)
        if (data.datasets) {
            data.datasets.forEach((dataset) => {
                dataset.backgroundColor = themeColors.primary;
                dataset.borderColor = themeColors.primary;
                dataset.borderRadius = 5; 
                dataset.barPercentage = 0.6; 
                dataset.categoryPercentage = 0.8;
            });
        }

        new Chart(ctx, {
            type: 'bar', 
            data: data,
            options: {
                ...commonOptions,
                onClick: (evt, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index; 
                        const bulan = index + 1; 
                        const tahun = {{ now()->year }};
                        
                        // Redirect ke halaman pecahan
                        window.location.href = `${routeUrl}?bulan=${bulan}&tahun=${tahun}`;
                    }
                }
            }
        });
    };

    // --- INISIALISASI GRAF (DATA DARI CONTROLLER) ---

    renderChart('grafPandanganUndang', 'bar', {!! json_encode($dataPandanganUndang) !!}, 
        "{{ route('laporanpandanganundang.pecahan') }}");

    renderChart('grafKesMahkamah', 'bar', {!! json_encode($dataKesMahkamah) !!}, 
        "{{ route('laporankesmahkamah.pecahan') }}");

    renderChart('grafGubalanUndang', 'bar', {!! json_encode($dataGubalan) !!}, 
        "{{ route('laporangubalanundang.pecahan') }}");

    renderChart('grafPindaanUndang', 'bar', {!! json_encode($dataPindaan) !!}, 
        "{{ route('laporanpindaanundang.pecahan') }}");

    renderChart('grafSemakanUndang', 'bar', {!! json_encode($dataSemakan) !!}, 
        "{{ route('laporansemakanundang.pecahan') }}");

    renderChart('grafMesyuarat', 'bar', {!! json_encode($dataMesyuarat) !!}, 
        "{{ route('laporanmesyuarat.pecahan') }}");

    renderChart('grafTataterib', 'bar', {!! json_encode($dataTataterib) !!}, 
        "{{ route('kestatatertib.pecahan') }}");

    renderChart('grafTugasan', 'bar', {!! json_encode($dataTugasan) !!}, 
        "{{ route('lainlaintugasan.pecahan') }}");

</script>
@endpush