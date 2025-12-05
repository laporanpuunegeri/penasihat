@extends('layouts.app')

@section('content')

{{-- Custom CSS untuk Styling Moden --}}
<style>
    /* 1. KONTENA UTAMA */
    .dbus-card {
        border: none; /* Hilangkan border pada card utama */
        border-radius: 12px; /* Sudut membulat */
        overflow: hidden; /* Penting untuk jadual di dalamnya */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    /* 2. TAB NAVIGATION */
    .nav-tabs .nav-link {
        background-color: #f8f9fa; /* Lebih cerah */
        color: #495057;
        border: 1px solid #e9ecef;
        margin-right: 2px;
        font-weight: 600;
        border-radius: 8px 8px 0 0;
        padding: 10px 18px;
        transition: all 0.3s;
    }
    .nav-tabs .nav-link.active {
        /* Warna Korporat Gelap */
        background-color: #1e293b !important; 
        color: #ffffff !important;
        border-color: #1e293b !important;
        border-top: 3px solid #3b82f6 !important; /* Blue accent */
        border-bottom-color: transparent !important; /* Sambungkan ke konten */
    }

    /* 3. JADUAL (Table) */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-dark th {
        background-color: #343a40; /* Kekalkan gelap */
        color: #f8f9fa;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table-bordered th, .table-bordered td {
        border-color: #dee2e6; /* Border lebih lembut */
    }
    .table-hover tbody tr:hover {
        background-color: #f5f5f5; /* Efek hover yang lebih lembut */
    }

    /* 4. BARIS UTAMA (OA) */
    .oa-row, .table-secondary {
        background-color: #e9ecef !important; /* Sedikit lebih terang */
        border-top: 2px solid #3b82f6; /* Border tebal biru untuk pemisah */
        font-size: 1rem !important;
    }
    
    /* 5. BARIS SUB-INDUK (OS) */
    .fw-bold.text-primary.bg-light {
        background-color: #f0f8ff !important; /* Biru sangat muda */
        color: #007bff !important;
        font-size: 0.95rem;
    }

    /* 6. GRAND TOTAL FOOTER */
    .grand-total-footer {
        background-color: #343a40 !important; /* Kekalkan warna gelap yang profesional */
        color: #ffffff;
        border-radius: 0 0 12px 12px; /* Sudut membulat di bawah */
    }
    .grand-total-footer h4 {
        color: #28a745; /* Hijau untuk nilai total */
    }
</style>

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">D'BUS (OBB) - Anggaran Belanja</h3>
            <p class="text-muted small">BAHAGIAN/PUUN : PENASIHAT UNDANG-UNDANG NEGERI MELAKA</p>
        </div>
    </div>

    {{-- FILTER TAHUN --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <label class="fw-bold text-secondary me-3 mb-0">TAHUN ANGGARAN:</label>
                <form action="{{ route('pentadbiran.dbus.index') }}" method="GET">
                    <select name="tahun" class="form-select form-select-sm fw-bold border-primary text-primary" style="width: 150px;" onchange="this.form.submit()">
                        @for ($y = 2027; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div class="text-muted small">
                Jumlah Keseluruhan: <span class="fw-bold text-success">RM {{ number_format($grandTotal, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Logik untuk mendapatkan kunci OA --}}
    @php
        $oaKeys = array_keys($structure);
        $oaTitles = [
            'OA10000' => 'EMOLUMEN',
            'OA20000' => 'PERKHIDMATAN & BEKALAN'
        ];
    @endphp

    {{-- TAB NAVIGATION --}}
    <ul class="nav nav-tabs" id="oaTab" role="tablist">
        @foreach($oaKeys as $oaKey)
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold {{ $loop->first ? 'active' : '' }}" 
                        id="{{ $oaKey }}-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#panel-{{ $oaKey }}" 
                        type="button" role="tab">
                    {{ $oaKey }} - {{ $oaTitles[$oaKey] ?? 'LAIN-LAIN' }}
                </button>
            </li>
        @endforeach
    </ul>

    {{-- TAB CONTENT CONTAINER --}}
    <div class="tab-content dbus-card p-0 rounded-0 rounded-bottom" id="oaTabContent">

        {{-- Loop Structure untuk create PANELS berasingan --}}
        @foreach($structure as $oaKey => $oa)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="panel-{{ $oaKey }}" role="tabpanel">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" style="font-size: 0.9rem;">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th style="width: 50%;">BUTIRAN PERBELANJAAN</th>
                                    <th style="width: 15%;">OL (Lanjut)</th>
                                    <th style="width: 15%;">OS (Sebagai)</th>
                                    <th style="width: 15%;">OA (Am)</th>
                                    <th style="width: 5%;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- BARIS OA (INDUK) --}}
                                <tr class="oa-row border-bottom-2 border-dark">
                                    <td class="fw-bold text-dark">{{ $oaKey }} {{ $oa['perkara'] }}</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end fw-bold">{{ number_format($oa['jumlah'], 2) }}</td>
                                    <td class="text-center">
                                        @php
                                            $oaColor = ($oaKey == 'OA10000') ? 'btn-primary' : 'btn-warning';
                                        @endphp
                                        <a href="{{ route('pentadbiran.dbus.edit', ['tahun'=>$tahun, 'kategori'=>$oaKey]) }}" class="btn btn-sm {{ $oaColor }} py-0 px-2" title="Kemaskini">
                                            <i class="fas fa-edit small"></i>
                                        </a>
                                    </td>
                                </tr>

                                @foreach($oa['items'] as $osKey => $os)
                                    {{-- BARIS OS (SUB-INDUK) --}}
                                    <tr class="fw-bold text-primary bg-light">
                                        <td class="ps-4">{{ $osKey }} {{ $os['perkara'] }}</td>
                                        <td></td>
                                        <td class="text-end">{{ number_format($os['jumlah'], 2) }}</td>
                                        <td></td>
                                        <td class="text-center">
                                            
                                            {{-- LOGIK AKSI (DIKEKALKAN SEPERTI ASAL) --}}
                                            
                                            @if(in_array($osKey, ['OS11000', 'OS12000', 'OS13000']))
                                                <a href="{{ route('pentadbiran.dbus.pecahan', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini Pecahan">
                                                    <i class="fas fa-user small"></i>
                                                </a>
                                            
                                            @elseif($osKey == 'OS14000')
                                                <a href="{{ route('pentadbiran.dbus.edit_ol14101', ['kod'=>'OL14101', 'tahun'=>$tahun]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini OT">
                                                    <i class="fas fa-edit small"></i>
                                                </a>

                                            @elseif($osKey == 'OS15000')
                                                <a href="{{ route('pentadbiran.dbus.edit_os15000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini Faedah">
                                                    <i class="fas fa-edit small"></i>
                                                </a>
                                                
                                            @elseif($osKey == 'OS21000')
                                                <a href="{{ route('pentadbiran.dbus.edit_os21000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-info py-0 px-2" title="Kemaskini Pecahan Perjalanan">
                                                    <i class="fas fa-edit small"></i>
                                                </a>
                                                
                                            @elseif($osKey == 'OS22000')
                                                <a href="{{ route('pentadbiran.dbus.edit_os22000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-info py-0 px-2" title="Kemaskini Pengangkutan">
                                                    <i class="fas fa-truck small"></i>
                                                </a>
                                            
                                            @elseif($osKey == 'OS23000')
                                                <a href="{{ route('pentadbiran.dbus.edit_os23000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-info py-0 px-2" title="Kemaskini Utiliti">
                                                    <i class="fas fa-lightbulb small"></i>
                                                </a>
                                                
                                            @elseif($osKey == 'OS24000')
                                                <a href="{{ route('pentadbiran.dbus.edit_os24000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-info py-0 px-2" title="Kemaskini Sewaan">
                                                    <i class="fas fa-building small"></i>
                                                </a>

                                            @else
                                                <a href="{{ route('pentadbiran.dbus.edit', ['tahun'=>$tahun, 'kategori'=>$osKey]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini">
                                                    <i class="fas fa-edit small"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>

                                    @foreach($os['items'] as $olKey => $ol)
                                        {{-- BARIS OL (DETAIL) --}}
                                        <tr>
                                            <td class="ps-5 text-muted">{{ $olKey }} {{ $ol['perkara'] }}</td>
                                            <td class="text-end">{{ number_format($ol['jumlah'], 2) }}</td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center">
                                                {{-- Tiada butang di sini --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr><td colspan="5" class="bg-white border-0 py-2"></td></tr>
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr class="border-top border-dark">
                                    <td class="text-end text-uppercase">Jumlah Kecil ({{ $oaKey }})</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end text-success fs-5">{{ number_format($oa['jumlah'], 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- GRAND TOTAL KESELURUHAN --}}
    <div class="card mt-3 grand-total-footer">
        <div class="card-body py-2 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">JUMLAH KESELURUHAN ANGGARAN BELANJA</h5>
            <h4 class="mb-0 fw-bold">RM {{ number_format($grandTotal, 2) }}</h4>
        </div>
    </div>
</div>
@endsection