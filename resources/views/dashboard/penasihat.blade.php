@extends('layouts.app')

@push('styles')
<style>
    /* --- Dashboard Styles (Biru & Putih) --- */
    .page-title { color: #0d47a1; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
    .page-subtitle { color: #546e7a; font-size: 0.9rem; }
    
    /* Card Style */
    .card-custom {
        border: none; border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        background-color: #fff;
    }
    .card-custom:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(21, 101, 192, 0.15); }
    
    /* Card Header */
    .card-header-custom {
        background: #fff; border-bottom: 2px solid #e3f2fd;
        padding: 1rem 1.2rem; display: flex; align-items: center;
        border-radius: 12px 12px 0 0;
    }
    .card-title-custom {
        font-size: 0.95rem; font-weight: 700; color: #1565c0; margin: 0; text-transform: uppercase; display: flex; align-items: center;
    }
    .card-title-custom i { color: #42a5f5; margin-right: 10px; width: 20px; text-align: center; font-size: 1.1rem; }
    
    /* Card Body */
    .card-body-custom { padding: 1rem; position: relative; height: 320px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h2 class="page-title"><i class="fas fa-chart-bar me-2"></i> Dashboard Penasihat</h2>
            <p class="page-subtitle">Ringkasan statistik dan prestasi tahun {{ now()->year }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-comment-dots"></i> Pandangan Undang-Undang</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafPandanganUndang"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-gavel"></i> Kes Mahkamah</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafKesMahkamah"></canvas></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-file-contract"></i> Gubalan Undang-Undang</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafGubalanUndang"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-edit"></i> Pindaan Undang-Undang</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafPindaanUndang"></canvas></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-search"></i> Semakan Undang-Undang</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafSemakanUndang"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-users"></i> Mesyuarat</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafMesyuarat"></canvas></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom"><h5 class="card-title-custom"><i class="fas fa-exclamation-triangle"></i> Tatatertib</h5></div>
                <div class="card-body card-body-custom"><canvas id="grafTataterib"></canvas></div>
            </div>
        </div>
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
    // --- Konfigurasi Warna Tema Biru ---
    const themeColors = {
        primary: '#1565c0', // Biru Kuat (Main Bar Color)
        light: '#64b5f6',   // Biru Cair
        palette: ['#0d47a1', '#1565c0', '#1976d2', '#2196f3', '#42a5f5', '#90caf9'] 
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
            tooltip: { backgroundColor: 'rgba(13, 71, 161, 0.9)', padding: 12, cornerRadius: 8 }
        },
        scales: {
            x: { 
                grid: { display: false } // Hilangkan grid melintang
            }, 
            y: { 
                beginAtZero: true, 
                border: { display: false }, 
                grid: { color: '#f0f0f0' },
                ticks: { precision: 0 } // Nombor bulat sahaja
            }
        },
        // Tambah Cursor Pointer
        onHover: (event, chartElement) => {
            event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
        }
    };

    // --- FUNGSI RENDER GRAF (SEMUA JADI BAR) ---
    const renderChart = (canvasId, type, data, routeUrl) => {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        // Paksa Styling Bar untuk Semua
        if (data.datasets) {
            data.datasets.forEach((dataset) => {
                // Semua guna warna biru solid
                dataset.backgroundColor = themeColors.primary;
                dataset.borderRadius = 5; // Bar bucu bulat
                dataset.barPercentage = 0.6; // Lebar bar
                dataset.categoryPercentage = 0.8;
            });
        }

        new Chart(ctx, {
            type: 'bar', // <--- PAKSA JADI BAR CHART
            data: data,
            options: {
                ...commonOptions,
                // Logic Klik (Drill-down)
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

    // --- INISIALISASI SEMUA GRAF SEBAGAI BAR ---
    // Pastikan Controller hantar semua variable data ini!

    // 1. Pandangan Undang-Undang
    renderChart('grafPandanganUndang', 'bar', {!! json_encode($dataPandanganUndang) !!}, 
        "{{ route('laporanpandanganundang.pecahan') }}");

    // 2. Kes Mahkamah
    renderChart('grafKesMahkamah', 'bar', {!! json_encode($dataKesMahkamah) !!}, 
        "{{ route('laporankesmahkamah.pecahan') }}");

    // 3. Gubalan Undang-Undang
    renderChart('grafGubalanUndang', 'bar', {!! json_encode($dataGubalan) !!}, 
        "{{ route('laporangubalanundang.pecahan') }}");

    // 4. Pindaan Undang-Undang
    renderChart('grafPindaanUndang', 'bar', {!! json_encode($dataPindaan) !!}, 
        "{{ route('laporanpindaanundang.pecahan') }}");

    // 5. Semakan Undang-Undang
    renderChart('grafSemakanUndang', 'bar', {!! json_encode($dataSemakan) !!}, 
        "{{ route('laporansemakanundang.pecahan') }}");

    // 6. Mesyuarat
    renderChart('grafMesyuarat', 'bar', {!! json_encode($dataMesyuarat) !!}, 
        "{{ route('laporanmesyuarat.pecahan') }}");

    // 7. Tatatertib
    renderChart('grafTataterib', 'bar', {!! json_encode($dataTataterib) !!}, 
        "{{ route('kestatatertib.pecahan') }}");

    // 8. Lain-lain Tugasan
    renderChart('grafTugasan', 'bar', {!! json_encode($dataTugasan) !!}, 
        "{{ route('lainlaintugasan.pecahan') }}");

</script>
@endpush