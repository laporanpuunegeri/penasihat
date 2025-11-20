@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold text-uppercase mb-4 text-center">Dashboard Laporan</h3>

    {{-- Filter Bar --}}
    <form method="GET" class="row mb-4">
        <div class="col-md-3">
            <label>Sort Nama Pegawai</label>
            <select name="pegawai" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($senaraiPegawai as $pegawai)
                    <option value="{{ $pegawai->id }}" {{ request('pegawai') == $pegawai->id ? 'selected' : '' }}>
                        {{ $pegawai->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Bulan</label>
            <select name="bulan" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($senaraiBulan as $num => $nama)
                    <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Tahun</label>
            <select name="tahun" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($senaraiTahun as $t)
                    <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Agensi</label>
            <select name="agensi" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($senaraiAgensi as $a)
                    <option value="{{ $a }}" {{ request('agensi') == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- KPI Section --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold">📊 Jumlah Pandangan Undang-Undang</h6>
                    <canvas id="chartPiePandangan"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <h6 class="text-danger fw-bold">⚠️ Ringkasan belum dibuat (&gt;14 hari)</h6>
                    <p class="fs-3 fw-bold text-danger">{{ $melepasiTarikh }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold">🏢 Jumlah Agensi diberi pandangan</h6>
                    <canvas id="chartPieAgensi"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Carta Pegawai & Statistik Kes --}}
    <div class="row mb-4">
        <div class="col-md-9">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold">👤 Senarai Pegawai (Jumlah Semua Modul)</h6>
                    <canvas id="chartColumnPegawai"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold">📌 Jumlah Keseluruhan:</h6>
                    <ol class="mb-0">
                        <li>Pandangan Undang-undang: <strong>{{ $totalPandangan }}</strong></li>
                        <li>Kes Mahkamah: <strong>{{ $totalMahkamah }}</strong></li>
                        <li>Gubalan Undang-undang: <strong>{{ $totalGubalan }}</strong></li>
                        <li>Pindaan Undang-undang: <strong>{{ $totalPindaan }}</strong></li>
                        <li>Semakan Undang-undang: <strong>{{ $totalSemakan }}</strong></li>
                        <li>Mesyuarat: <strong>{{ $totalMesyuarat }}</strong></li>
                        <li>Tatatertib: <strong>{{ $totalTatatertib }}</strong></li>
                        <li>Lain-lain: <strong>{{ $totalLain }}</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pie Chart Pandangan
    new Chart(document.getElementById('chartPiePandangan'), {
        type: 'pie',
        data: {
            labels: ['Telah Diambil Tindakan', 'Belum Diambil Tindakan'],
            datasets: [{
                data: [{{ $sudahTindakan }}, {{ $belumTindakan }}],
                backgroundColor: ['#28a745', '#dc3545']
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Pie Chart Agensi
    new Chart(document.getElementById('chartPieAgensi'), {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($agensiData)) !!},
            datasets: [{
                data: {!! json_encode(array_values($agensiData)) !!},
                backgroundColor: ['#007bff', '#ffc107', '#6f42c1', '#17a2b8', '#dc3545', '#20c997']
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Column Chart Pegawai (dengan nombor & tooltip nama penuh)
    const pegawaiNama = {!! json_encode($pegawaiData->pluck('nama')) !!};
    const pegawaiJumlah = {!! json_encode($pegawaiData->pluck('jumlah')) !!};

    new Chart(document.getElementById('chartColumnPegawai'), {
        type: 'bar',
        data: {
            labels: pegawaiNama.map((_, i) => (i + 1).toString()), // Guna nombor sahaja
            datasets: [{
                label: 'Jumlah Modul Diisi',
                data: pegawaiJumlah,
                backgroundColor: '#17a2b8',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            const index = context[0].dataIndex;
                            return pegawaiNama[index];
                        }
                    }
                }
            },
scales: {
    y: {
        beginAtZero: true,
        suggestedMax: Math.max(...pegawaiJumlah, 5), // Minimum maksimum paksi Y ialah 5
        ticks: {
            stepSize: 1 // Papar dalam langkah 1
        },
        title: {
            display: true,
            text: 'Jumlah Modul'
        }
    },
    x: {
        title: {
            display: true,
            text: 'Pegawai (No)'
        }
    }
}
        }
    });
});
</script>
@endsection
