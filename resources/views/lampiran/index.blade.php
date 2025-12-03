@extends('layouts.app')

@section('content')

{{-- CSS Khas untuk Jadual Input --}}
<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #0f172a; /* Dark Navy Accent */
    }

    .filter-card {
        background: #fff;
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        margin-bottom: 20px;
    }

    .table-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }

    .table thead th {
        background-color: #1e293b;
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border: 1px solid #334155;
        vertical-align: middle;
        padding: 10px 5px;
    }

    /* Input dalam jadual supaya nampak macam Excel */
    .table-input {
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 5px;
        font-size: 0.9rem;
        text-align: center;
        width: 100%;
        transition: all 0.2s;
    }
    
    .table-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        outline: none;
        background-color: #f8fafc;
    }

    .table-input-text {
        text-align: left; /* Untuk status */
    }

    .category-cell {
        background-color: #f8fafc;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.85rem;
        text-align: left;
        padding-left: 15px;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- 1. HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Lampiran Kes Mahkamah (Lampiran II)</h3>
            <p class="text-muted small mb-0">Sila isi statistik kes mahkamah mengikut kategori bagi bulan berkenaan.</p>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 border-start border-5 border-success mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 2. FILTER BULAN & TAHUN --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('lampiran.index') }}" id="filterForm" class="row align-items-center g-3">
            <div class="col-auto">
                <label class="fw-bold text-secondary small text-uppercase"><i class="fas fa-calendar-alt me-2"></i> Tetapan:</label>
            </div>
            <div class="col-auto">
                <select name="bulan" class="form-select form-select-sm bg-light fw-bold" onchange="document.getElementById('filterForm').submit();" style="width: 150px;">
                    @foreach (range(1, 12) as $b)
                        <option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm bg-light fw-bold" onchange="document.getElementById('filterForm').submit();" style="width: 100px;">
                    @foreach (range(now()->year, now()->year - 5) as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col ms-auto text-end">
                <small class="text-muted fst-italic">Data akan disimpan untuk: <strong>{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</strong></small>
            </div>
        </form>
    </div>

    {{-- 3. FORM INPUT DATA --}}
    <form method="POST" action="{{ route('lampiran.store') }}" id="lampiranForm">
        @csrf
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle text-center">
                    <thead>
                        <tr>
                            <th style="width: 20%; text-align: left; padding-left: 15px;">Kategori</th>
                            <th style="width: 10%;">Bil. Aktif</th>
                            <th style="width: 10%;">Majistret</th>
                            <th style="width: 10%;">Sesyen</th>
                            <th style="width: 10%;">Tinggi</th>
                            <th style="width: 10%;">Rayuan</th>
                            <th style="width: 10%;">Persk.</th>
                            <th style="width: 20%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategori_list as $kategori)
                            @php $item = $lampiran[$kategori] ?? null; @endphp
                            <tr>
                                {{-- Nama Kategori --}}
                                <td class="category-cell">
                                    {{ $kategori }}
                                </td>

                                {{-- Input Nombor --}}
                                <td>
                                    <input type="number" name="data[{{ $kategori }}][bil_aktif]" 
                                           value="{{ $item->bil_aktif ?? 0 }}" 
                                           class="table-input bg-warning bg-opacity-10 fw-bold" title="Bilangan Aktif">
                                </td>
                                <td>
                                    <input type="number" name="data[{{ $kategori }}][majistret]" 
                                           value="{{ $item->majistret ?? 0 }}" class="table-input">
                                </td>
                                <td>
                                    <input type="number" name="data[{{ $kategori }}][sesi]" 
                                           value="{{ $item->sesi ?? 0 }}" class="table-input">
                                </td>
                                <td>
                                    <input type="number" name="data[{{ $kategori }}][tinggi]" 
                                           value="{{ $item->tinggi ?? 0 }}" class="table-input">
                                </td>
                                <td>
                                    <input type="number" name="data[{{ $kategori }}][rayuan]" 
                                           value="{{ $item->rayuan ?? 0 }}" class="table-input">
                                </td>
                                <td>
                                    <input type="number" name="data[{{ $kategori }}][persk]" 
                                           value="{{ $item->persk ?? 0 }}" class="table-input">
                                </td>

                                {{-- Input Status (Teks) --}}
                                <td>
                                    <input type="text" name="data[{{ $kategori }}][status]" 
                                           value="{{ $item->status ?? '-' }}" 
                                           class="table-input table-input-text" 
                                           placeholder="Catatan...">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- BUTANG SIMPAN (Floating / Sticky Bottom Effect) --}}
        <div class="d-flex justify-content-end mt-4 mb-5">
            <button type="submit" id="simpanBtn" class="btn btn-success px-5 py-2 shadow fw-bold">
                <i class="fas fa-save me-2"></i> Simpan Data Lampiran II
            </button>
        </div>

    </form>
</div>

{{-- Script Kecil untuk UX (Highlight Row bila edit) --}}
<script>
    document.querySelectorAll('.table-input').forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('tr').style.backgroundColor = '#f0f9ff'; // Biru cair bila fokus
        });
        input.addEventListener('blur', function() {
            this.closest('tr').style.backgroundColor = ''; // Hilang warna bila blur
        });
        // Auto-select value bila klik (senang nak tukar 0)
        input.addEventListener('click', function() {
            this.select();
        });
    });
</script>

@endsection