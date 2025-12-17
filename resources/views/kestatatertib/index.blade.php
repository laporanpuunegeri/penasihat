@extends('layouts.app')

@section('content')

{{-- Style Khas --}}
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
            <h3 class="mb-1 fw-bold text-dark">Senarai Kes Tatatertib</h3>
            <p class="text-muted small mb-0">
                Laporan kes tatatertib dan tindakan yang diambil.
            </p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ url('/kestatatertib/create') }}" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-plus-circle me-2"></i> Daftar Kes
            </a>
        </div>
    </div>

    {{-- 2. FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4">
            <form method="GET" action="{{ url('/kestatatertib') }}" class="row align-items-center g-3">
                <div class="col-auto">
                    <label for="bulan" class="col-form-label fw-bold text-secondary small text-uppercase">
                        <i class="fas fa-filter me-1"></i> Tapis Bulan Daftar:
                    </label>
                </div>
                <div class="col-auto">
                    <select name="bulan" id="bulan" class="form-select form-select-sm fw-bold border-primary" style="min-width: 200px;" onchange="this.form.submit()">
                        <option value="">-- Semua Bulan --</option>
                        @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April', 5 => 'Mei', 6 => 'Jun',
                                   7 => 'Julai', 8 => 'Ogos', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember']
                                   as $num => $nama)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                @if(request('bulan'))
                <div class="col-auto">
                    <a href="{{ url('/kestatatertib') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- 3. JADUAL --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th width="5%">Bil</th>
                        <th width="12%">Tarikh</th>
                        <th width="15%">Kategori</th>
                        <th width="25%" class="text-start">Fakta & Isu</th>
                        <th width="20%" class="text-start">Ringkasan Pandangan</th>
                        <th width="12%">Status</th>
                        <th width="10%">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentUser = auth()->user(); @endphp
                    
                    @forelse ($data as $index => $laporan)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            
                            {{-- Tarikh Bertingkat (Fixed with Backup Date) --}}
                            <td>
                                <div class="d-flex flex-column align-items-center gap-1">
                                    {{-- Tarikh Daftar: Kalau tak ada, guna created_at --}}
                                    <span class="badge bg-light text-dark border" title="Tarikh Daftar">
                                        D: 
                                        @if($laporan->tarikh_daftar)
                                            {{ \Carbon\Carbon::parse($laporan->tarikh_daftar)->format('d/m/Y') }}
                                        @elseif($laporan->created_at)
                                            {{ $laporan->created_at->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                    
                                    {{-- Tarikh Terima --}}
                                    <small class="text-muted fst-italic" style="font-size: 0.65rem;" title="Tarikh Terima">
                                        T: {{ $laporan->tarikh_terima ? \Carbon\Carbon::parse($laporan->tarikh_terima)->format('d/m/Y') : '-' }}
                                    </small>
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $laporan->kategori }}</div>
                            </td>

                            {{-- Fakta & Isu --}}
                            <td class="text-start">
                                <div class="mb-2">
                                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem;">Fakta:</span>
                                    <div class="small text-dark">{{ Str::limit($laporan->fakta_ringkasan, 60) }}</div>
                                </div>
                                <div>
                                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem;">Isu:</span>
                                    <div class="small text-muted">{{ Str::limit($laporan->isu, 60) }}</div>
                                </div>
                            </td>

                            {{-- Ringkasan Pandangan --}}
                            <td class="text-start">
                                <div class="small text-dark">{{ Str::limit($laporan->ringkasan_pandangan, 80) }}</div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($laporan->tarikh_selesai)
                                    <span class="badge bg-success status-badge mb-1">Selesai</span>
                                    <div class="small text-muted" style="font-size: 0.65rem;">
                                        {{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark status-badge">
                                        {{ Str::limit($laporan->status, 15) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Tindakan --}}
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ url('/kestatatertib/' . $laporan->id . '/edit') }}" 
                                       class="btn btn-outline-primary btn-icon btn-sm" 
                                       title="Kemaskini">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    {{-- Delete --}}
                                    @if ($laporan->user_id === $currentUser->id || $currentUser->role === 'admin')
                                        <form action="{{ url('/kestatatertib/' . $laporan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda pasti untuk padam rekod ini?');">
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
                            <td colspan="7" class="text-center py-5 text-muted bg-light">
                                <i class="fas fa-gavel fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0 fw-bold">Tiada kes tatatertib direkodkan.</p>
                                <small>Sila klik butang "Daftar Kes" untuk mula.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection