@extends('layouts.app')

@section('content')

{{-- Custom Style untuk Halaman Ini --}}
<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #0f172a; /* Dark Navy Accent */
    }
    
    .table-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }

    .table thead th {
        background-color: #1e293b; /* Dark Navy */
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border: none;
        vertical-align: middle;
        padding: 12px;
    }

    /* Badge Status */
    .status-badge {
        font-size: 0.7rem;
        padding: 5px 10px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
    }

    /* Action Buttons */
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

    {{-- 1. HEADER & TOOLBAR --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Laporan Kes Mahkamah</h3>
            <p class="text-muted small mb-0">Senarai kes-kes sivil, jenayah, dan lain-lain di mahkamah.</p>
        </div>
        
        <div class="d-flex gap-2">
            {{-- Butang Daftar Baru --}}
            <a href="{{ route('laporankesmahkamah.create') }}" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-plus-circle me-2"></i> Daftar Kes Baru
            </a>
        </div>
    </div>

    {{-- 2. FILTER CARD --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4">
            <form method="GET" class="row align-items-center g-3">
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
                    <a href="{{ route('laporankesmahkamah.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- 3. JADUAL DATA --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th width="5%">Bil</th>
                        <th width="12%">Tarikh</th>
                        <th width="15%">Jenis Kes</th>
                        <th width="20%" class="text-start">Fakta & Isu</th>
                        <th width="20%" class="text-start">Skop & Hujahan</th>
                        <th width="10%">Status</th>
                        <th width="10%">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentUser = auth()->user(); @endphp
                    
                    @forelse ($data as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            
                            {{-- Kolum Tarikh (Bertingkat) --}}
                            <td>
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="badge bg-light text-dark border" title="Tarikh Daftar">
                                        D: {{ $item->created_at->format('d/m/Y') }}
                                    </span>
                                    
                                    @if($item->tarikh_sebutan)
                                        <small class="text-primary fw-bold" style="font-size: 0.7rem;" title="Tarikh Sebutan">
                                            <i class="fas fa-gavel me-1"></i> S: {{ \Carbon\Carbon::parse($item->tarikh_sebutan)->format('d/m/Y') }}
                                        </small>
                                    @else
                                        <small class="text-muted fst-italic" style="font-size: 0.65rem;">- Tiada Sebutan -</small>
                                    @endif
                                </div>
                            </td>

                            {{-- Jenis Kes --}}
                            <td>
                                <div class="fw-bold text-dark">{{ Str::limit($item->jenis_kes, 30) }}</div>
                                {{-- Jika ada agensi (bila dah tambah column nanti), boleh letak sini --}}
                            </td>

                            {{-- Fakta & Isu (Gabung supaya jimat ruang) --}}
                            <td class="text-start">
                                <div class="mb-2">
                                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem;">Fakta:</span>
                                    <div class="small text-dark">{{ Str::limit($item->fakta_ringkas, 60) }}</div>
                                </div>
                                <div>
                                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem;">Isu:</span>
                                    <div class="small text-muted">{{ Str::limit($item->isu, 60) }}</div>
                                </div>
                            </td>

                            {{-- Skop & Hujahan --}}
                            <td class="text-start">
                                <div class="mb-2">
                                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem;">Skop Tugas:</span>
                                    <div class="small text-dark">{{ Str::limit($item->skop_tugas, 50) }}</div>
                                </div>
                                <div>
                                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem;">Hujahan:</span>
                                    <div class="small text-muted">{{ Str::limit($item->ringkasan_hujahan, 50) }}</div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if(Str::contains(strtolower($item->status), 'selesai'))
                                    <span class="badge bg-success status-badge">
                                        <i class="fas fa-check-circle me-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark status-badge">
                                        <i class="fas fa-clock me-1"></i> {{ Str::limit($item->status, 15) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Tindakan --}}
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('laporankesmahkamah.edit', $item->id) }}" 
                                       class="btn btn-outline-primary btn-icon btn-sm" 
                                       title="Kemaskini">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    {{-- Delete --}}
                                    @if ($currentUser->id === $item->user_id || $currentUser->role === 'admin')
                                        <form action="{{ route('laporankesmahkamah.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-icon btn-sm" 
                                                    onclick="return confirm('Adakah anda pasti mahu memadam rekod ini?')"
                                                    title="Padam">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                <div class="mt-2 text-muted" style="font-size: 0.6rem;">
                                    Oleh: {{ Str::limit(optional($item->user)->name ?? '?', 10) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted bg-light">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0 fw-bold">Tiada Laporan Kes Direkodkan.</p>
                                <small>Sila klik butang "Daftar Kes Baru" untuk mula.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection