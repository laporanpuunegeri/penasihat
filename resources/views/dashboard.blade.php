@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold text-uppercase mb-4 text-center">Dashboard Laporan</h3>

    {{-- Statistik Ringkasan --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-1">📅 Jumlah laporan bulan ini</h5>
                    <p class="fs-4 fw-bold mb-0">{{ $bulanIni }} laporan direkodkan</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-1">📝 Laporan belum selesai</h5>
                    <p class="fs-4 fw-bold mb-0">{{ $belumSelesai }} laporan masih "Dalam Proses"</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-start border-danger border-4">
                <div class="card-body text-center">
                    <h5 class="text-danger mb-1">⚠️ Tindakan segera</h5>
                    <p class="fs-4 fw-bold mb-0">{{ $melepasiTarikh }} laporan melepasi tarikh selesai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kad-kad Chart --}}
    <div class="row row-cols-1 row-cols-md-4 g-4">
        <x-dashboard-card title="Undang-Undang" chartId="chartUndang" />
        <x-dashboard-card title="Tatatertib" chartId="chartTatatertib" />
        <x-dashboard-card title="Mesyuarat" chartId="chartMesyuarat" />
        <x-dashboard-card title="Lain-lain" chartId="chartLain" />
        <x-dashboard-card title="Kes Mahkamah" chartId="chartMahkamah" />
        <x-dashboard-card title="Gubalan Undang" chartId="chartGubalan" />
        <x-dashboard-card title="Pindaan Undang" chartId="chartPindaan" />
        <x-dashboard-card title="Semakan Undang" chartId="chartSemakan" />
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const config = (id, data, color) => new Chart(document.getElementById(id), {
        type: 'bar',
        data: {
            labels: ['Suku 1', 'Suku 2', 'Suku 3', 'Suku 4'],
            datasets: [{
                label: 'Jumlah Laporan',
                data: data,
                backgroundColor: color,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    config('chartUndang', {!! json_encode($undang) !!}, '#007bff');
    config('chartTatatertib', {!! json_encode($tatatertib) !!}, '#dc3545');
    config('chartMesyuarat', {!! json_encode($mesyuarat) !!}, '#ffc107');
    config('chartLain', {!! json_encode($lain) !!}, '#28a745');
    config('chartMahkamah', {!! json_encode($kesmahkamah) !!}, '#6f42c1');
    config('chartGubalan', {!! json_encode($gubalan) !!}, '#17a2b8');
    config('chartPindaan', {!! json_encode($pindaan) !!}, '#fd7e14');
    config('chartSemakan', {!! json_encode($semakan) !!}, '#6610f2');
});
</script>
@endsection
