@extends('layouts.app')

@section('content')
<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-primary text-uppercase" style="letter-spacing: 1px;">
                Statistik Terperinci: {{ $namaBulan }} {{ $tahun }}
            </h2>
            <p class="text-muted mb-0">Analisis pecahan data mengikut Kategori dan Agensi.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row mb-5">
        <div class="col-12 mb-3">
            <h4 class="fw-bold text-dark border-start border-5 border-primary ps-3">1. Pecahan Kategori</h4>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="card-title text-primary mb-0 fw-bold">
                        <i class="fas fa-chart-bar me-2"></i> Carta Kategori
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 400px;">
                        <canvas id="chartPecahan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="card-title text-primary mb-0 fw-bold">
                        <i class="fas fa-list me-2"></i> Perincian Kategori
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4 py-3">Kategori</th>
                                    <th class="text-center py-3">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataPecahan as $item)
                                <tr>
                                    <td class="ps-4">{{ $item->kategori ?? 'Tiada Kategori' }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $item->total }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center py-4 text-muted">Tiada data.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td class="ps-4">JUMLAH KESELURUHAN</td>
                                    <td class="text-center text-primary">{{ $totals->sum() }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5 text-muted" style="opacity: 0.1;">

    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h4 class="fw-bold text-dark border-start border-5 border-info ps-3">2. Statistik Agensi (Top 10)</h4>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="card-title text-info mb-0 fw-bold"><i class="fas fa-chart-bar me-2"></i> Carta Agensi Tertinggi</h5>
                </div>
                <div class="card-body">
                    <div style="height: 400px;">
                        <canvas id="chartAgensi"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="card-title text-info mb-0 fw-bold"><i class="fas fa-building me-2"></i> Senarai Agensi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4 py-3">Nama Agensi</th>
                                    <th class="text-center py-3">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataAgensi as $agency)
                                <tr>
                                    <td class="ps-4 small">{{Str::limit($agency->agensi, 30) ?? 'Tiada Nama' }}</td>
                                    <td class="text-center fw-bold text-info">{{ $agency->total }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center py-4 text-muted">Tiada data agensi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- CONFIG UMUM ---
    Chart.defaults.font.family = "'Segoe UI', sans-serif";
    Chart.defaults.color = '#666';
    
    // Palet Biru untuk Bar Chart
    const bluePalette = ['#0d47a1', '#1565c0', '#1976d2', '#2196f3', '#42a5f5', '#64b5f6', '#90caf9'];

    // --- 1. GRAF KATEGORI (TUKAR KE BAR CHART) ---
    const ctxCat = document.getElementById('chartPecahan').getContext('2d');
    new Chart(ctxCat, {
        type: 'bar', // <--- Jenis Bar
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Jumlah Kes',
                data: {!! json_encode($totals) !!},
                backgroundColor: '#1565c0', // Biru Solid (Lebih kemas untuk Bar)
                borderRadius: 6,
                barPercentage: 0.6,
                categoryPercentage: 0.8
            }]
        },
        options: {
            indexAxis: 'y', // <--- PENTING: Bar Melintang (Horizontal) supaya nama panjang tak putus
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }, // Tak perlu legend sebab label dah ada di tepi
                tooltip: { backgroundColor: 'rgba(13, 71, 161, 0.9)' }
            },
            scales: {
                x: { 
                    grid: { color: '#f0f0f0' },
                    ticks: { precision: 0 } // Nombor bulat sahaja
                },
                y: { 
                    grid: { display: false } 
                }
            }
        }
    });

    // --- 2. GRAF AGENSI (KEKAL SAMA) ---
    const ctxAgency = document.getElementById('chartAgensi').getContext('2d');
    new Chart(ctxAgency, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labelsAgensi) !!},
            datasets: [{
                label: 'Jumlah Permohonan',
                data: {!! json_encode($totalsAgensi) !!},
                backgroundColor: '#17a2b8', // Warna Info (Cyan/Teal)
                borderRadius: 6,
                barPercentage: 0.6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'rgba(23, 162, 184, 0.9)' }
            },
            scales: {
                x: { grid: { color: '#f0f0f0' }, ticks: { stepSize: 1 } },
                y: { grid: { display: false } }
            }
        }
    });
</script>
@endsection