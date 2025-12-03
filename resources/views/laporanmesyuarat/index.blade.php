@extends('layouts.app')

@section('content')

<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #0f172a;
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
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border: none;
        vertical-align: middle;
        padding: 12px;
    }
    .status-badge {
        font-size: 0.7rem;
        padding: 5px 10px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .btn-icon:hover { transform: translateY(-2px); }
</style>

<div class="container-fluid px-4 py-4">

    {{-- 1. HEADER --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Laporan Mesyuarat</h3>
            <p class="text-muted small mb-0">
                Rekod kehadiran mesyuarat dan pandangan undang-undang yang diberikan.
                <span class="d-none d-md-inline">(*Sila rujuk Lampiran I jika ada)</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('/laporanmesyuarat/create') }}" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-plus-circle me-2"></i> Daftar Mesyuarat
            </a>
        </div>
    </div>

    {{-- 2. FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4">
            <form method="GET" action="{{ url('/laporanmesyuarat') }}" class="row align-items-center g-3">
                <div class="col-auto">
                    <label for="bulan" class="col-form-label fw-bold text-secondary small text-uppercase">
                        <i class="fas fa-filter me-1"></i> Tapis Bulan Mesyuarat:
                    </label>
                </div>
                <div class="col-auto">
                    <select name="bulan" id="bulan" class="form-select form-select-sm fw-bold border-primary" style="min-width: 200px;" onchange="this.form.submit()">
                        <option value="">-- Semua Bulan --</option>
                        @foreach ([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April', 5 => 'Mei', 6 => 'Jun',
                            7 => 'Julai', 8 => 'Ogos', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
                        ] as $num => $nama)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('bulan'))
                    <div class="col-auto">
                        <a href="{{ url('/laporanmesyuarat') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- 3. TABLE --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th width="5%">Bil</th>
                        <th width="12%">Tarikh Mesyuarat</th>
                        <th width="25%" class="text-start">Nama Mesyuarat</th>
                        <th width="25%" class="text-start">Isu Berbangkit</th>
                        <th width="5%">Lisan</th>
                        <th width="5%">Bertulis</th>
                        <th width="10%">Status</th>
                        <th width="13%">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentUser = auth()->user(); @endphp

                    @forelse ($laporanMesyuarat as $index => $laporan)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ optional($laporan->tarikh_mesyuarat)->format('d/m/Y') }}</div>
                                <small class="text-muted" style="font-size: 0.65rem;">Daftar: {{ $laporan->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-start">{{ Str::limit($laporan->mesyuarat, 60) }}</td>
                            <td class="text-start">{{ Str::limit($laporan->isu, 80) }}</td>
                            <td>@if($laporan->pandangan === 'Lisan') <i class="fas fa-check-circle text-success"></i> @endif</td>
                            <td>@if($laporan->pandangan === 'Bertulis') <i class="fas fa-check-circle text-primary"></i> @endif</td>
                            <td>
                                <span class="badge bg-light text-dark border status-badge">{{ Str::limit($laporan->status, 15) }}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ url('/laporanmesyuarat/' . $laporan->id . '/edit') }}" 
                                       class="btn btn-outline-primary btn-icon btn-sm" 
                                       title="Kemaskini">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    {{-- Delete --}}
                                    @if ($currentUser->id === $laporan->user_id || $currentUser->role === 'admin')
                                        <form action="{{ url('/laporanmesyuarat/' . $laporan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda pasti untuk padam rekod ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" title="Padam">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="mt-2 text-muted" style="font-size: 0.6rem;">
                                    Oleh: {{ Str::limit(optional($laporan->user)->name ?? '?', 10) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted bg-light">
                                <i class="fas fa-handshake fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0 fw-bold">Tiada rekod mesyuarat ditemui.</p>
                                <small>Sila klik butang "Daftar Mesyuarat" untuk mula.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
