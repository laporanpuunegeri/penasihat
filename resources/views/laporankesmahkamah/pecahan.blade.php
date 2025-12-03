@extends('layouts.app')

@section('content')
<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-primary text-uppercase" style="letter-spacing: 1px;">
                Kes Mahkamah: {{ $namaBulan }} {{ $tahun }}
            </h2>
            <p class="text-muted mb-0">Pecahan statistik kes mengikut peringkat mahkamah.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row">
        
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="card-title text-primary mb-0 fw-bold">
                        <i class="fas fa-balance-scale me-2"></i> Carta Kategori
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
                        <i class="fas fa-list me-2"></i> Perincian Data
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Peringkat Mahkamah</th>
                                    <th class="text-center py-3">Jumlah Kes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataPecahan as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $item->kategori }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->total > 0 ? 'bg-primary' : 'bg-secondary opacity-50' }} rounded-pill px-3">
                                            {{ $item->total }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted">Tiada rekod ditemui.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td class="ps-4">JUMLAH KESELURUHAN</td>
                                    <td class="text-center text-primary">{{ collect($dataPecahan)->sum('total') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Segoe UI', sans-serif";
    
    // Warna Biru (Sama macam modul Pandangan Undang-undang)
    const bluePalette = [
        '#0d47a1', // Biru Gelap (Mahkamah Tinggi/Persekutuan)
        '#1565c0', 
        '#1976d2', 
        '#2196f3', 
        '#42a5f5', 
        '#64b5f6', 
        '#90caf9'  // Biru Cair
    ];

    const ctxCat = document.getElementById('chartPecahan').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                data: {!! json_encode($totals) !!},
                backgroundColor: bluePalette,
                borderColor: '#ffffff', // Border putih supaya nampak clean
                borderWidth: 2
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom', 
                    labels: { usePointStyle: true, padding: 20 } 
                } 
            },
            cutout: '60%' // Saiz lubang tengah donut
        }
    });
</script>
@endsection