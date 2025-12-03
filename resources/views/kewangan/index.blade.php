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

    .card-dashboard {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }
    .card-dashboard:hover { transform: translateY(-5px); }

    .accordion-button:not(.collapsed) {
        background-color: #e0f2fe;
        color: #0369a1;
        font-weight: bold;
    }
    
    /* Button Icon */
    .btn-action {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 6px; transition: 0.2s;
    }
    .btn-action:hover { transform: scale(1.1); }
</style>

<div class="container-fluid px-4 py-4">

    {{-- 1. HEADER & FILTER TAHUN --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Laporan Kewangan</h3>
            <p class="text-muted small mb-0">
                Anggaran Perbelanjaan Mengurus Tahun <span class="fw-bold text-primary">{{ $tahun_dipilih }}</span>
            </p>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            {{-- Filter Tahun --}}
            <form action="{{ route('kewangan.index') }}" method="GET" class="d-flex align-items-center">
                <select name="tahun" class="form-select form-select-sm fw-bold border-secondary bg-white" 
                        onchange="this.form.submit()" style="width: 100px; cursor: pointer;">
                    @php
                        $currentYear = date('Y');
                        $startYear = $currentYear + 1; 
                        $endYear = 2020;
                    @endphp
                    @for($y = $startYear; $y >= $endYear; $y--)
                        <option value="{{ $y }}" {{ $tahun_dipilih == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </form>

            {{-- Butang Rekod Baru --}}
            {{-- Tambah sekatan: Hanya EO, CC, PTK1 boleh tambah rekod baru --}}
            @php $canEdit = in_array(auth()->user()->role, ['eo', 'cc', 'ptk1']); @endphp
            @if($canEdit)
            <a href="{{ route('kewangan.create') }}" class="btn btn-primary shadow-sm btn-sm px-3 py-2 fw-bold">
                <i class="fas fa-plus-circle me-2"></i> Rekod Baru
            </a>
            @endif
            
            {{-- Butang PDF --}}
            <a href="{{ route('kewangan.cetak_pdf_bulanan', ['tahun' => $tahun_dipilih]) }}" target="_blank" class="btn btn-danger shadow-sm btn-sm px-3 py-2 fw-bold">
                <i class="fas fa-file-pdf me-2"></i> PDF Bulanan
            </a>
        </div>
    </div>

    {{-- 2. DASHBOARD CARD (Grand Total) --}}
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard border-start border-4 border-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Peruntukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">RM {{ number_format($grand_total_peruntukan, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-money-bill-wave fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard border-start border-4 border-danger h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Jumlah Belanja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">RM {{ number_format($grand_total_belanja, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-cart fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard border-start border-4 border-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Baki Semasa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">RM {{ number_format($grand_total_baki, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            @php $statusColor = $grand_peratus > 90 ? 'danger' : ($grand_peratus > 70 ? 'warning' : 'info'); @endphp
            <div class="card card-dashboard border-start border-4 border-{{ $statusColor }} h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $statusColor }} text-uppercase mb-1">Prestasi</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($grand_peratus, 1) }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-{{ $statusColor }}" role="progressbar" style="width: {{ $grand_peratus }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. GRAF EMOLUMEN (Optional - kalau nak hidden boleh remove) --}}
    @include('dashboard.partials.graf_emolumen')

    {{-- 4. SENARAI REKOD (ACCORDION) --}}
    @if(!Route::is('dashboard.kewangan'))
        <div class="accordion mt-4" id="accordionKewangan">
            @foreach($laporan_kewangan as $kod_utama => $data)
                @php
                    $baki_utama = $data['total_peruntukan'] - $data['total_belanja'];
                    $peratus_utama = $data['total_peruntukan'] > 0 ? ($data['total_belanja'] / $data['total_peruntukan']) * 100 : 0;
                    $collapseId = "collapse" . $kod_utama;
                    
                    // Warna Header Accordion ikut peratus belanja
                    $headerColor = 'bg-white';
                    if($peratus_utama > 90) $headerColor = 'bg-danger bg-opacity-10';
                @endphp

                <div class="card shadow-sm mb-3 border-0">
                    <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between {{ $headerColor }}"
                        id="heading{{ $kod_utama }}"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $collapseId }}"
                        aria-expanded="true"
                        aria-controls="{{ $collapseId }}"
                        style="cursor: pointer;">
                        
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div>
                                <h6 class="m-0 font-weight-bold text-primary">{{ $kod_utama }} - {{ $data['tajuk'] }}</h6>
                                <small class="text-muted">{{ count($data['items']) }} Rekod</small>
                            </div>
                        </div>

                        <div class="d-none d-md-flex gap-2 mt-2 mt-md-0">
                            <span class="badge bg-light text-dark border px-3 py-2">Siling: RM {{ number_format($data['total_peruntukan'], 0) }}</span>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2">Belanja: RM {{ number_format($data['total_belanja'], 0) }}</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">Baki: RM {{ number_format($baki_utama, 0) }}</span>
                        </div>
                    </div>

                    <div id="{{ $collapseId }}" class="collapse show" aria-labelledby="heading{{ $kod_utama }}">
                        <div class="card-body p-0">
                            @if(count($data['items']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light text-center text-uppercase small">
                                            <tr>
                                                <th width="10%">Kod Objek</th>
                                                <th width="35%" class="text-start">Butiran</th>
                                                <th width="15%">Peruntukan</th>
                                                <th width="15%">Belanja</th>
                                                <th width="15%">Baki</th>
                                                <th width="10%">Tindakan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['items'] as $item)
                                                @php
                                                    $baki_item = $item->peruntukan - $item->belanja;
                                                    $peratus_item = $item->peruntukan > 0 ? ($item->belanja / $item->peruntukan) * 100 : 0;
                                                    $progress_color = $peratus_item > 90 ? 'bg-danger' : ($peratus_item > 70 ? 'bg-warning' : 'bg-success');
                                                    
                                                    // 🔥 LOGIK AKSES BARU 🔥
                                                    $currentRole = auth()->user()->role;
                                                    $canModify = in_array($currentRole, ['eo', 'cc', 'ptk1']);
                                                @endphp
                                                <tr>
                                                    <td class="text-center fw-bold">{{ $item->kod_objek }}</td>
                                                    <td class="text-start">
                                                        <div class="fw-semibold text-dark">{{ $item->butiran }}</div>
                                                        <div class="progress mt-1" style="height: 4px;">
                                                            <div class="progress-bar {{ $progress_color }}" role="progressbar" style="width: {{ $peratus_item }}%"></div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center text-primary fw-bold">{{ number_format($item->peruntukan, 2) }}</td>
                                                    <td class="text-center text-danger fw-bold">{{ number_format($item->belanja, 2) }}</td>
                                                    <td class="text-center text-success fw-bold">{{ number_format($baki_item, 2) }}</td>
                                                    <td class="text-center">
                                                        {{-- 🔥 KAWALAN AKSES KE ATAS RUANGAN TINDAKAN 🔥 --}}
                                                        @if($canModify)
                                                        <div class="d-flex justify-content-center gap-1">
                                                            {{-- Edit --}}
                                                            <a href="{{ route('kewangan.edit', $item->id) }}" class="btn btn-outline-primary btn-action btn-sm" title="Kemaskini">
                                                                <i class="fas fa-pen"></i>
                                                            </a>
                                                            
                                                            {{-- Delete --}}
                                                            <form action="{{ route('kewangan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Padam rekod ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-action btn-sm" title="Padam">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                        @else
                                                        {{-- Paparkan ikon kunci atau ruang kosong jika akses disekat --}}
                                                        <i class="fas fa-lock text-muted small" title="Akses terhad"></i>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Tiada rekod untuk kategori ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection