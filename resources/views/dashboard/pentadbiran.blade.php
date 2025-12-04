@extends('layouts.app')

@section('content')
<style>
    .waran-table thead th {
        background-color: #343a40;
        color: white;
        vertical-align: middle;
        text-align: center;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        padding: 12px;
    }
    .waran-table tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    .summary-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        padding: 20px;
        border: 1px solid #e9ecef;
    }
</style>

<div class="container-fluid py-4">

    {{-- HEADER & BUTTONS --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Waran Perjawatan</h3>
            <p class="text-muted small mb-0">1.3.4 PEJABAT PENASIHAT UNDANG-UNDANG NEGERI MELAKA</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-secondary shadow-sm btn-sm px-3">
                <i class="fas fa-print me-2"></i> Cetak
            </button>
            <a href="{{ route('pentadbiran.waran.edit') }}" class="btn btn-primary shadow-sm btn-sm px-3">
                <i class="fas fa-edit me-2"></i> Kemaskini Data
            </a>
        </div>
    </div>

    {{-- ALERT MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center">
            <i class="fas fa-check-circle me-2 fs-5"></i> 
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- FILTER TAHUN --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px;">
        <div class="card-body py-2 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <label class="fw-bold text-secondary small text-uppercase me-3 mb-0">
                    <i class="far fa-calendar-alt me-1"></i> Tahun:
                </label>
                <select class="form-select form-select-sm fw-bold border-primary text-primary" style="width: 120px;">
                    <option value="2025" selected>2025</option>
                </select>
            </div>
            <span class="text-muted small fst-italic">
                <i class="fas fa-sync-alt me-1"></i> Data terkini dari Pangkalan Data
            </span>
        </div>
    </div>
    
    {{-- VISUAL DATA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            
            {{-- RINGKASAN ATAS --}}
            <div class="summary-card mb-4">
                <div class="row text-center">
                    <div class="col-md-4 border-end">
                        <h6 class="text-muted small fw-bold mb-1 text-uppercase">Jumlah Waran</h6>
                        <h3 class="text-dark fw-bold mb-0">{{ $metadata['totalWaran'] ?? 0 }}</h3>
                    </div>
                    <div class="col-md-4 border-end">
                        <h6 class="text-muted small fw-bold mb-1 text-uppercase">Diisi</h6>
                        <h3 class="text-success fw-bold mb-0">{{ $metadata['totalIsi'] ?? 0 }}</h3>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted small fw-bold mb-1 text-uppercase">Kekosongan</h6>
                        <h3 class="text-danger fw-bold mb-0">{{ $metadata['totalKosong'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            {{-- JADUAL UTAMA --}}
            <div class="table-responsive">
                <div class="mb-3 text-center">
                    <h5 class="fw-bold text-uppercase">Butiran Waran Perjawatan</h5>
                </div>

                <table class="table table-bordered table-hover waran-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">BIL.</th>
                            <th style="width: 45%; text-align: left; padding-left: 15px;">JAWATAN / GRED</th>
                            <th style="width: 10%;">BIL (Waran)</th>
                            <th style="width: 10%;">ISI</th>
                            <th style="width: 10%;">KOSONG</th>
                            <th style="width: 20%;">CATATAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($waranData as $index => $waran)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-start ps-3 fw-bold text-dark">{{ $waran->jawatan }}</td>
                            <td class="text-center">{{ $waran->bil }}</td>
                            <td class="text-center text-primary fw-bold">{{ $waran->isi }}</td>
                            <td class="text-center @if($waran->kosong > 0) text-danger fw-bold bg-light @endif">
                                {{ $waran->kosong }}
                            </td>
                            <td class="text-center small text-secondary">
                                {{ $waran->nota ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-3 text-warning"></i><br>
                                Tiada data dijumpai. Sila jalankan Seeder.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold text-uppercase py-3">Jumlah Keseluruhan</td>
                            <td class="text-center fw-bold py-3">{{ $metadata['totalWaran'] ?? 0 }}</td>
                            <td class="text-center fw-bold py-3 text-primary">{{ $metadata['totalIsi'] ?? 0 }}</td>
                            <td class="text-center fw-bold py-3 text-danger">{{ $metadata['totalKosong'] ?? 0 }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection