<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fas fa-chart-pie mr-2"></i> Prestasi Peruntukan
                </h5>
                
                <div class="row align-items-center">
                    <!-- BAHAGIAN GRAF -->
                    <div class="col-md-5">
                        <div style="height: 250px;">
                            <canvas id="emolumenChart"></canvas>
                        </div>
                    </div>

                    <!-- BAHAGIAN INFO TEKS -->
                    <div class="col-md-7">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-secondary p-2" style="min-width: 100px;">Peruntukan</span></td>
                                        <td class="fw-bold text-end">RM {{ number_format($emoSiling ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-danger p-2" style="min-width: 100px;">Belanja</span></td>
                                        <td class="fw-bold text-danger text-end">RM {{ number_format($emoBelanja ?? 0, 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><span class="badge bg-success p-2" style="min-width: 100px;">Baki</span></td>
                                        <td class="fw-bold text-success text-end">RM {{ number_format($emoBaki ?? 0, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Progress Bar Visual -->
                        <div class="mt-3">
                            <small class="text-muted text-uppercase fw-bold">Peratus Penggunaan</small>
                            <div class="progress mt-1" style="height: 20px; border-radius: 10px;">
                                @php
                                    $peratus = ($emoSiling > 0) ? ($emoBelanja / $emoSiling) * 100 : 0;
                                    $warna = $peratus > 90 ? 'bg-danger' : ($peratus > 75 ? 'bg-warning' : 'bg-info');
                                @endphp
                                <div class="progress-bar {{ $warna }} progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: {{ $peratus }}%">
                                     {{ number_format($peratus, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT CHART.JS -->
<!-- Pastikan library Chart.js dah load di layout utama. Kalau belum, uncomment line bawah: -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('emolumenChart').getContext('2d');
    
    // Data dari Controller (Blade to JS)
    var dataSiling = {{ $emoSiling ?? 0 }};
    var dataBelanja = {{ $emoBelanja ?? 0 }};
    var dataBaki = {{ $emoBaki ?? 0 }};

    var myChart = new Chart(ctx, {
        type: 'doughnut', // Boleh tukar 'bar' atau 'pie' kalau nak
        data: {
            labels: ['Belanja', 'Baki'],
            datasets: [{
                data: [dataBelanja, dataBaki],
                backgroundColor: [
                    '#dc3545', // Merah (Belanja)
                    '#198754'  // Hijau (Baki)
                ],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            // Format duit dalam tooltip
                            let value = context.raw;
                            label += 'RM ' + value.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            return label;
                        }
                    }
                }
            },
            cutout: '65%', // Lubang tengah (untuk nampak moden)
        }
    });
});
</script>