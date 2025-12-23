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
    // Fungsi bantuan untuk memastikan data sentiasa ada format asas Chart.js
    const prepareData = (data) => {
        if (!data || !data.labels) {
            return {
                labels: ['Tiada Data'],
                datasets: [{ label: 'N/A', data: [0], backgroundColor: '#eeeeee' }]
            };
        }
        return data;
    };

    const configBar = (ctxId, rawData) => {
        const el = document.getElementById(ctxId);
        if(!el) return;
        new Chart(el, {
            type: 'bar',
            data: prepareData(rawData),
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    };

    const configPie = (ctxId, rawData) => {
        const el = document.getElementById(ctxId);
        if(!el) return;
        new Chart(el, {
            type: 'pie',
            data: prepareData(rawData),
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    };

    // Panggil fungsi dengan data dari Controller
    configBar('grafPandanganUndang', {!! json_encode($dataPandanganUndang ?? null) !!});
    configBar('grafKesMahkamah', {!! json_encode($dataKesMahkamah ?? null) !!});
    configPie('grafGubalanUndang', {!! json_encode($dataGubalan ?? null) !!});
    configPie('grafPindaanUndang', {!! json_encode($dataPindaan ?? null) !!});
    configBar('grafSemakanUndang', {!! json_encode($dataSemakan ?? null) !!});
    configPie('grafMesyuarat', {!! json_encode($dataMesyuarat ?? null) !!});
    configBar('grafTataterib', {!! json_encode($dataTataterib ?? null) !!});
    configPie('grafTugasan', {!! json_encode($dataTugasan ?? null) !!});
</script>
@endsection