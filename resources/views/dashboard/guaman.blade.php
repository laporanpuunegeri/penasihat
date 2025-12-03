{{-- resources/views/dashboard/guaman.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h3 class="fw-bold text-dark mb-4">Dashboard Guaman</h3>
    <p class="text-muted">Selamat datang ke Dashboard Modul Guaman. Berikut adalah ringkasan aktiviti dan statistik kes mengikut kendalian, kod perkara dan mahkamah.</p>
    
    <hr>

    <div class="row">
        
        {{-- KAD RINGKASAN UTAMA (Jumlah Kes Berdaftar) --}}
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Jumlah Kes Berdaftar</h6>
                            <h2 class="display-5 fw-bold">{{ $totalCases ?? '0' }}</h2>
                        </div>
                        <i class="fas fa-gavel fa-3x op-3"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- RUANGAN GRAFIK BULANAN (BAR CHART) --}}
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header fw-bold bg-white">Kes Berdaftar Mengikut Bulan ({{ date('Y') }})</div>
                <div class="card-body">
                    @if(($totalCases ?? 0) > 0)
                        <canvas id="monthlyGuamanChart"></canvas>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-chart-bar fa-4x mb-3 text-light"></i>
                            <p>Tiada data kes untuk dijana mengikut bulan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    
    <hr>
    
    <div class="row">
        
        {{-- KAD 1: GRAF PIE PEGAWAI KENDALIAN --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header fw-bold bg-white">Pecahan Kes Mengikut Pegawai Kendalian</div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    @if(($totalCases ?? 0) > 0)
                        <canvas id="kendalianPieChart" style="max-height: 250px;"></canvas>
                    @else
                        <p class="text-muted text-center">Tiada data kendalian kes untuk dijana.</p>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- KAD 2: GRAF PIE KOD PERKARA --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header fw-bold bg-white">Pecahan Kes Mengikut Kod Perkara</div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    @if(($totalCases ?? 0) > 0)
                        <canvas id="kodPerkaraPieChart" style="max-height: 250px;"></canvas>
                    @else
                        <p class="text-muted text-center">Tiada data kod perkara untuk dijana.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- KAD 3: GRAF PIE MAHKAMAH --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header fw-bold bg-white">Pecahan Kes Mengikut Mahkamah</div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    @if(($totalCases ?? 0) > 0)
                        <canvas id="mahkamahPieChart" style="max-height: 250px;"></canvas>
                    @else
                        <p class="text-muted text-center">Tiada data mahkamah untuk dijana.</p>
                    @endif
                </div>
            </div>
        </div>
        
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalCases = {{ $totalCases ?? 0 }};
        if (totalCases > 0) {
            
            // --- 1. DATA KES DARI PHP ---
            const monthlyData = @json($dataMonthlyGraph);
            const kendalianData = @json($casesByKendalianGraph);
            const kodData = @json($casesByKodGraph);
            const mahkamahData = @json($casesByMahkamahGraph);
            
            // --- 2. WARNA (Palet Berbeza untuk setiap Pie Chart) ---
            const colorPalettes = {
                kendalian: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'], // Biru/Hijau/Cyan
                kod: ['#17a673', '#6f42c1', '#fd7e14', '#e83e8c', '#20c997', '#007bff'], // Hijau/Ungu/Oren
                mahkamah: ['#3098f0', '#ffc107', '#dc3545', '#17a2b8', '#6610f2', '#6c757d'] // Biru Muda/Kuning/Merah
            };

            // --- 3. FUNGUI PENJANA GRAF PIE ---
            function createPieChart(ctxId, chartData, colorPalette) {
                const ctx = document.getElementById(ctxId).getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            data: chartData.data,
                            backgroundColor: colorPalette.slice(0, chartData.data.length),
                            hoverBackgroundColor: colorPalette.map(c => c + 'cc'),
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        tooltips: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyFontColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: true,
                            caretPadding: 10,
                        },
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                    },
                });
            }
            
            // --- 4. BAR CHART BULANAN ---
            const monthlyCtx = document.getElementById('monthlyGuamanChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: monthlyData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision: 0,
                            title: {
                                display: true,
                                text: 'Jumlah Kes'
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: { display: false }
                    }
                }
            });


            // --- 5. JANA 3 GRAF PIE ---
            createPieChart('kendalianPieChart', kendalianData, colorPalettes.kendalian);
            createPieChart('kodPerkaraPieChart', kodData, colorPalettes.kod);
            createPieChart('mahkamahPieChart', mahkamahData, colorPalettes.mahkamah);
        }
    });
</script>
@endpush
@endsection