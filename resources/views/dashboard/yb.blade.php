@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="fw-bold text-uppercase mb-4 text-center">Dashboard YB Penasihat Undang-Undang</h3>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const config = (id, data, color) => new Chart(document.getElementById(id), {
        type: 'bar',
        data: {
            labels: ['Suku 1', 'Suku 2', 'Suku 3', 'Suku 4'],
            datasets: [{
                label: 'Jumlah Laporan',
                data: data,
                backgroundColor: color,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { enabled: true } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
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
</script>
@endsection