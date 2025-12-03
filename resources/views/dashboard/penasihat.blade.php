@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="fw-bold text-uppercase mb-4">Dashboard Laporan Undang-Undang</h3>

    <div class="row">
        <div class="col-md-6 mb-4">
            <canvas id="grafPandanganUndang"></canvas>
        </div>
        <div class="col-md-6 mb-4">
            <canvas id="grafKesMahkamah"></canvas>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <canvas id="grafGubalanUndang"></canvas>
        </div>
        <div class="col-md-6 mb-4">
            <canvas id="grafPindaanUndang"></canvas>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <canvas id="grafSemakanUndang"></canvas>
        </div>
        <div class="col-md-6 mb-4">
            <canvas id="grafMesyuarat"></canvas>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <canvas id="grafTataterib"></canvas>
        </div>
        <div class="col-md-6 mb-4">
            <canvas id="grafTugasan"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const configBar = (ctxId, data) => {
        new Chart(document.getElementById(ctxId), {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    };

    const configPie = (ctxId, data) => {
        new Chart(document.getElementById(ctxId), {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    };

    configBar('grafPandanganUndang', {!! json_encode($dataPandanganUndang) !!});
    configBar('grafKesMahkamah', {!! json_encode($dataKesMahkamah) !!});
    configPie('grafGubalanUndang', {!! json_encode($dataGubalan) !!});
    configPie('grafPindaanUndang', {!! json_encode($dataPindaan) !!});
    configBar('grafSemakanUndang', {!! json_encode($dataSemakan) !!});
    configPie('grafMesyuarat', {!! json_encode($dataMesyuarat) !!});
    configBar('grafTataterib', {!! json_encode($dataTataterib) !!});
    configPie('grafTugasan', {!! json_encode($dataTugasan) !!});
</script>
@endsection
