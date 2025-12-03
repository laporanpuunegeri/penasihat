@extends('layouts.app')

@section('content')
<style>
    .card-filter { border-radius: 12px; }
    .status-badge { font-size: 0.75rem; padding: 5px 10px; }
    .action-icons a { margin: 0 3px; }
</style>

<div class="container-fluid py-4">

    {{-- HEADER & BUTTONS --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Pengurusan Laporan Kerangka PPUUN</h3>
            <p class="text-muted small">Senarai Laporan Prestasi mengikut tahun.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pentadbiran.laporan_prestasi.cetak', ['tahun' => $metadata['tahun'] ?? date('Y')]) }}" class="btn btn-success shadow-sm" target="_blank">
                <i class="fas fa-print me-2"></i> Cetak Laporan Penuh
            </a>
            <a href="{{ route('pentadbiran.laporan_prestasi.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-2"></i> Daftar Data Baru
            </a>
        </div>
    </div>

    {{-- FILTER TAHUN --}}
    <div class="card card-filter border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4">
            <form method="GET" class="row align-items-center g-3">
                <div class="col-auto">
                    <label for="tahun" class="col-form-label fw-bold text-secondary small text-uppercase">
                        <i class="far fa-calendar-alt me-1"></i> Tahun Laporan:
                    </label>
                </div>
                <div class="col-auto">
                    {{-- Guna select untuk pilih tahun --}}
                    <select name="tahun" id="tahun" class="form-select form-select-sm fw-bold border-primary" onchange="this.form.submit()" style="min-width: 120px;">
                        @for ($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ ($metadata['tahun'] ?? date('Y')) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    {{-- LISTING DATA --}}
    <div class="card shadow border-0">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3">Rekod Tersedia:</h6>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th style="width: 10%;">Outcome ID</th>
                            <th style="width: 50%;">Keterangan Outcome</th>
                            <th style="width: 15%;" class="text-center">Sasaran Tahunan</th>
                            <th style="width: 15%;" class="text-center">Status</th>
                            <th style="width: 10%;" class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $outcomeId => $records)
                            @php $record = $records[0]; @endphp
                            <tr>
                                <td><span class="badge bg-primary">{{ $outcomeId }}</span></td>
                                <td>{{ Str::limit($records[0]->tajuk ?? $record->kpi_desc, 70) }}</td>
                                <td class="text-center">{{ $record->sasaran_tahunan }}%</td>
                                <td class="text-center">
                                    <span class="status-badge bg-warning text-dark">DRAF</span>
                                </td>
                                <td class="text-center action-icons">
                                    {{-- Edit/View Detail --}}
                                    <a href="{{ route('pentadbiran.laporan_prestasi.create') }}" class="btn btn-outline-warning btn-sm btn-icon" title="Kemaskini Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- View Full Report (PDF) --}}
                                    <a href="{{ route('pentadbiran.laporan_prestasi.cetak', ['tahun' => $metadata['tahun']]) }}" class="btn btn-outline-info btn-sm btn-icon" title="Lihat Laporan Penuh" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Tiada data PPUUN direkodkan untuk tahun {{ $metadata['tahun'] ?? date('Y') }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection