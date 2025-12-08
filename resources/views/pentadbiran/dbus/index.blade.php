@extends('layouts.app')

@section('content')

<style>
    /* FORMAT JADUAL UTAMA */
    .table-dbus { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #dee2e6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 0.9rem; background-color: #fff; }
    .table-dbus th, .table-dbus td { padding: 8px 12px; vertical-align: middle; border-bottom: 1px solid #dee2e6; border-right: 1px solid #dee2e6; }
    .table-dbus thead th { background-color: #2c3e50; color: #ffffff; text-align: center; font-weight: 600; text-transform: uppercase; border: 1px solid #2c3e50; }
    .row-oa { background-color: #34495e; color: #fff; font-weight: 700; font-size: 1rem; }
    .row-os { background-color: #f8f9fa; color: #0d6efd; font-weight: 600; }
    .pl-os { padding-left: 20px !important; border-left: 4px solid #0d6efd !important; }
    .row-group td { background-color: #e6fffa; color: #006644; font-weight: 600; font-style: italic; padding-left: 40px !important; text-transform: uppercase; border-bottom: 2px solid #a3d9c9; }
    .row-ol td { background-color: #ffffff; color: #495057; }
    .pl-ol { padding-left: 60px !important; }
    .nav-tabs .nav-link { font-weight: bold; color: #495057; background-color: #e9ecef; border: 1px solid #dee2e6; margin-right: 4px; padding: 10px 20px; }
    .nav-tabs .nav-link.active { background-color: #fff; color: #0d6efd; border-bottom-color: transparent; border-top: 3px solid #0d6efd; }
    .text-right { text-align: right; font-family: 'Consolas', monospace; }
    .text-center { text-align: center; }
    .editable-oa-value { cursor: pointer; border-bottom: 1px dashed #adb5bd; }
    .editable-input { width: 100%; text-align: right; color: #000; }
</style>

<div class="container-fluid py-4">
    
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="fas fa-calculator me-2 text-primary"></i>D'BUS (OBB)</h3>
            <p class="text-muted small mb-0">PENASIHAT UNDANG-UNDANG NEGERI MELAKA</p>
        </div>
    </div>
    
    {{-- FILTER TAHUN & GRAND TOTAL --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-left: 5px solid #0d6efd;">
        <div class="card-body py-3 d-flex justify-content-between align-items-center">
            
            <div class="d-flex align-items-center gap-3">
                <form action="{{ route('pentadbiran.dbus.index') }}" method="GET" class="d-flex align-items-center">
                    <label class="me-2 fw-bold text-secondary">TAHUN:</label>
                    <select name="tahun" id="tahunSelector" class="form-select form-select-sm w-auto fw-bold text-primary border-primary" onchange="this.form.submit()">
                        @for($y = 2027; $y <= 2030; $y++) <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option> @endfor
                    </select>
                </form>

                {{-- BUTANG CETAK DISINI --}}
                <a href="{{ route('pentadbiran.dbus.cetak_pdf', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-danger btn-sm shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> CETAK PDF
                </a>
            </div>

            <div class="text-end">
                <span class="text-muted small text-uppercase fw-bold">Jumlah Keseluruhan (OA10 + OA20)</span>
                <h4 class="mb-0 text-success fw-bold" id="grandTotalDisplay">RM {{ number_format($grandTotal, 2) }}</h4>
            </div>
        </div>
    </div>

    {{-- 🔥 TAB NAVIGATION (OA10 vs OA20) 🔥 --}}
    <ul class="nav nav-tabs mb-0" id="dbusTabs" role="tablist">
        @foreach($structure as $oaKey => $oa)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $oaKey }}" data-bs-toggle="tab" data-bs-target="#content-{{ $oaKey }}" type="button" role="tab">
                    <i class="fas fa-folder-open me-2"></i> {{ $oaKey }} - {{ $oa['perkara'] }}
                </button>
            </li>
        @endforeach
    </ul>

    {{-- KAD JADUAL (ISI KANDUNGAN TAB) --}}
    <div class="card border-0 shadow-sm border-top-0 rounded-0 rounded-bottom">
        <div class="card-body p-3">
            <div class="tab-content" id="dbusTabsContent">
                
                @foreach($structure as $oaKey => $oa)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content-{{ $oaKey }}" role="tabpanel">
                        
                        <div class="table-responsive">
                            <table class="table-dbus">
                                <thead>
                                    <tr>
                                        <th style="width: 50%">BUTIRAN PERBELANJAAN</th>
                                        <th style="width: 12%">OL (RM)</th>
                                        <th style="width: 12%">OS (RM)</th>
                                        <th style="width: 12%">OA (RM)</th>
                                        <th style="width: 14%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- BARIS JUMLAH BESAR OA INI --}}
                                    <tr class="row-oa">
                                        <td>JUMLAH {{ $oaKey }} ({{ $oa['perkara'] }})</td>
                                        <td></td><td></td>
                                        <td class="text-right">
                                            <div class="editable-oa-value" id="oaDisplay-{{ $oaKey }}" data-oa-kod="{{ $oaKey }}" data-value="{{ $oa['jumlah'] ?? 0 }}" onclick="enableEdit(this)">
                                                {{ number_format($oa['jumlah'] ?? 0, 2) }}
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>

                                    {{-- LOOP OS --}}
                                    @if(isset($oa['items']) && is_array($oa['items']))
                                        @foreach($oa['items'] as $osKey => $os)
                                            <tr class="row-os">
                                                <td class="pl-os">{{ $osKey }} {{ $os['perkara'] }}</td>
                                                <td></td>
                                                <td class="text-right text-primary">{{ number_format($os['jumlah'] ?? 0, 2) }}</td>
                                                <td></td>
                                                <td class="text-center">
                                                    {{-- LOGIK BUTANG KEMASKINI --}}
                                                    @php
                                                        $route = 'pentadbiran.dbus.pecahan'; 
                                                        $btnClass = 'btn-primary'; // Warna Default: Biru
                                                        
                                                        // Senarai Modul yang SUDAH SIAP dan ada Route Khas
                                                        $siap = [
                                                            'OS14000' => 'pentadbiran.dbus.edit_ol14101',
                                                            'OS15000' => 'pentadbiran.dbus.edit_os15000',
                                                            'OS21000' => 'pentadbiran.dbus.edit_os21000',
                                                            'OS22000' => 'pentadbiran.dbus.edit_os22000',
                                                            'OS23000' => 'pentadbiran.dbus.edit_os23000',
                                                            'OS24000' => 'pentadbiran.dbus.edit_os24000',
                                                            'OS25000' => 'pentadbiran.dbus.edit_os25000',
                                                            'OS26000' => 'pentadbiran.dbus.edit_os26000',
                                                            'OS27000' => 'pentadbiran.dbus.edit_os27000',
                                                            'OS28000' => 'pentadbiran.dbus.edit_os28000',
                                                            'OS29000' => 'pentadbiran.dbus.edit_os29000',
                                                        ];

                                                        if (array_key_exists($osKey, $siap)) {
                                                            $route = $siap[$osKey];
                                                        } 
                                                    @endphp

                                                    <a href="{{ route($route, ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm {{ $btnClass }} py-0 px-2 shadow-sm" title="Kemaskini">
                                                        <i class="fas fa-edit me-1"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>

                                            {{-- LOOP GROUP (HIJAU) --}}
                                            @if(isset($os['items']) && is_array($os['items']))
                                                @foreach($os['items'] as $groupKey => $group)
                                                    
                                                    @if(is_array($group) && isset($group['perkara']))
                                                        <tr class="row-group">
                                                            <td colspan="5">
                                                                <i class="fas fa-caret-right me-2"></i> {{ $groupKey }} {{ $group['perkara'] }}
                                                            </td>
                                                        </tr>

                                                        {{-- LOOP OL (DATA PUTIH) --}}
                                                        @if(isset($group['items']) && is_array($group['items']))
                                                            @foreach($group['items'] as $olKey => $ol)
                                                                <tr class="row-ol">
                                                                    <td class="pl-ol text-muted small">
                                                                        <span class="badge bg-light text-dark border me-1">{{ $olKey }}</span> 
                                                                        {{ $ol['perkara'] ?? '' }}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {{ isset($ol['jumlah']) && $ol['jumlah'] > 0 ? number_format($ol['jumlah'], 2) : '-' }}
                                                                    </td>
                                                                    <td></td><td></td><td></td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                            
                                            {{-- Spacer antara OS --}}
                                            <tr><td colspan="5" style="border:none; height: 10px;"></td></tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div> {{-- End Table Responsive --}}
                    </div> {{-- End Tab Pane --}}
                @endforeach

            </div> {{-- End Tab Content --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function formatCurrency(num) { return parseFloat(num).toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    
    function enableEdit(element) {
        if (element.querySelector('input')) return;
        let currentVal = parseFloat(element.getAttribute('data-value'));
        let oaKod = element.getAttribute('data-oa-kod');
        element.innerHTML = `<input type="number" class="form-control form-control-sm text-end border-warning shadow" value="${currentVal.toFixed(2)}" id="input-${oaKod}" onblur="saveEdit(this, '${oaKod}')" onkeypress="handleEnter(event, this, '${oaKod}')">`;
        setTimeout(() => document.getElementById(`input-${oaKod}`).focus(), 100);
    }

    function handleEnter(e, input, oaKod) { if (e.key === 'Enter') input.blur(); }

    function saveEdit(input, oaKod) {
        let newVal = parseFloat(input.value);
        let parentDiv = document.getElementById(`oaDisplay-${oaKod}`);
        let oldVal = parseFloat(parentDiv.getAttribute('data-value'));
        
        if (isNaN(newVal) || newVal === oldVal) { parentDiv.innerHTML = formatCurrency(oldVal); return; }
        
        let tahun = document.getElementById('tahunSelector').value;
        parentDiv.innerHTML = formatCurrency(newVal);
        parentDiv.setAttribute('data-value', newVal);
        updateGrandTotal(); 

        fetch("{{ route('pentadbiran.dbus.updateOaAm') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ tahun: tahun, oa_kod: oaKod, oa_am_value: newVal })
        }).catch(err => { alert('Gagal simpan.'); parentDiv.innerHTML = formatCurrency(oldVal); });
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.editable-oa-value').forEach(el => total += parseFloat(el.getAttribute('data-value')));
        document.getElementById('grandTotalDisplay').innerText = 'RM ' + formatCurrency(total);
    }
</script>
@endpush