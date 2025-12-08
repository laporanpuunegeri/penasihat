@extends('layouts.app')

@section('content')

{{-- CSS Tambahan --}}
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
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border: none;
        vertical-align: middle;
    }

    .category-row {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
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
    
    .status-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 50px;
        font-weight: 600;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER & BUTTON --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Laporan Pandangan Undang-Undang</h3>
            <p class="text-muted small mb-0">Senarai rekod pandangan undang-undang mengikut kategori.</p>
        </div>
        
        <div class="d-flex gap-2">
            @if(auth()->user()->role == 'pa' || auth()->user()->role == 'admin')
                <a href="{{ route('agensi.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-building-cog me-2"></i> Urus Agensi
                </a>
            @endif

            <a href="{{ route('laporanpandanganundang.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-2"></i> Daftar Baharu
            </a>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4">
            <form method="GET" class="row align-items-center g-3">
                <div class="col-auto">
                    <label class="col-form-label fw-bold text-secondary small text-uppercase">
                        <i class="fas fa-filter me-1"></i> Tapisan:
                    </label>
                </div>
                
                {{-- PILIHAN BULAN --}}
                <div class="col-auto">
                    <select name="bulan" class="form-select form-select-sm fw-bold border-primary" style="min-width: 150px;" onchange="this.form.submit()">
                        <option value="all" {{ request('bulan') == 'all' ? 'selected' : '' }}>-- Semua Bulan --</option>
                        @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April', 5 => 'Mei', 6 => 'Jun',
                                   7 => 'Julai', 8 => 'Ogos', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember']
                                   as $num => $nama)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @php
        $kategori_list = [
            'Perlembagaan', 'Tanah / PBT', 'Undang-Undang Pentadbiran / Perkhidmatan',
            'Perjanjian / MOU', 'Penswastaan', 'Lain-lain'
        ];
        $currentUser = auth()->user();
    @endphp

    {{-- TABLE CONTENT --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th width="10%">Tarikh Terima</th>
                        <th width="12%">Agensi</th>
                        <th width="20%">Fakta / Isu</th>
                        <th width="20%">Ringkasan Pandangan</th>
                        <th width="5%">Lisan</th>
                        <th width="5%">Bertulis</th>
                        <th width="12%">Status</th>
                        <th width="6%">Dokumen</th> 
                        <th width="10%">Tindakan</th>
                    </tr>
                </thead>

                @foreach ($kategori_list as $kategori)
                    {{-- FILTER DATA MENGIKUT KATEGORI --}}
                    @php 
                        $filtered = $senaraiLaporan->where('kategori', $kategori); 
                    @endphp

                    <tbody>
                        {{-- Row Kategori --}}
                        <tr class="category-row">
                            <td colspan="9" class="text-start ps-4 border-top border-bottom">
                                <i class="fas fa-folder-open me-2 text-primary"></i> {{ $loop->iteration }}. {{ strtoupper($kategori) }}
                            </td>
                        </tr>

                        @forelse ($filtered as $item)
                            <tr>
                                {{-- Tarikh --}}
                                <td>
                                    <div class="fw-bold text-dark">{{ optional($item->tarikh_terima)->format('d/m/Y') }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Daftar: {{ $item->created_at->format('d/m/Y') }}</small>
                                </td>

                                {{-- Agensi --}}
                                <td><span class="badge bg-light text-dark border">{{ Str::limit($item->agensi, 20) }}</span></td>

                                {{-- Fakta & Isu --}}
                                <td class="text-start">
                                    <div class="small text-dark mb-1"><strong>Fakta:</strong> {{ Str::limit($item->fakta_ringkasan, 60) }}</div>
                                    <div class="small text-secondary"><strong>Isu:</strong> {{ Str::limit($item->isu_detail, 60) }}</div>
                                </td>

                                {{-- Pandangan --}}
                                <td class="text-start small text-muted">
                                    {{ Str::limit($item->ringkasan_pandangan, 80) }}
                                </td>

                                {{-- Checklist Jenis --}}
                                <td>
                                    @if($item->jenis_pandangan === 'Lisan') 
                                        <i class="fas fa-check-circle text-success fs-5"></i> 
                                    @else 
                                        <span class="text-muted">-</span> 
                                    @endif
                                </td>
                                <td>
                                    @if($item->jenis_pandangan === 'Bertulis') 
                                        <i class="fas fa-check-circle text-primary fs-5"></i> 
                                    @else 
                                        <span class="text-muted">-</span> 
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($item->tarikh_selesai)
                                        <span class="badge bg-success status-badge">Selesai</span>
                                        <div class="small text-muted mt-1" style="font-size: 0.65rem;">
                                            {{ \Carbon\Carbon::parse($item->tarikh_selesai)->format('d/m/Y') }}
                                        </div>
                                    @else
                                        <span class="badge bg-warning text-dark status-badge">Dalam Tindakan</span>
                                        <div class="small text-muted mt-1 fst-italic" style="font-size: 0.65rem;">
                                            {{ Str::limit($item->status ?? 'Aktif', 20) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Dokumen --}}
                                <td>
                                    @if($item->dokumen_path)
                                        <a href="{{ asset('storage/' . $item->dokumen_path) }}" target="_blank" class="btn btn-sm btn-outline-info btn-icon" title="Lihat Dokumen">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                {{-- Tindakan --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('laporanpandanganundang.edit', $item->id) }}" 
                                           class="btn btn-outline-warning btn-icon btn-sm" 
                                           title="Kemaskini">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        {{-- Benarkan delete hanya jika user adalah pemilik atau Super Admin --}}
                                        @if ($currentUser->id === $item->user_id || in_array($currentUser->role, ['super_admin', 'admin']))
                                            <form action="{{ route('laporanpandanganundang.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-icon btn-sm" 
                                                        onclick="return confirm('Adakah anda pasti mahu memadam laporan ini?')"
                                                        title="Padam">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-1 text-muted" style="font-size: 0.65rem;">
                                        Oleh: {{ Str::limit(optional($item->creator)->name ?? '?', 10) }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-3 text-muted fst-italic bg-light">
                                    Tiada rekod.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                @endforeach
            </table>
        </div>
    </div>
    
    {{-- 
        PAGINATION DIBUANG SEBAB KITA GUNA GET() DI CONTROLLER 
        SUPAYA SEMUA DATA KATEGORI KELUAR 
    --}}
    
</div>
@endsection