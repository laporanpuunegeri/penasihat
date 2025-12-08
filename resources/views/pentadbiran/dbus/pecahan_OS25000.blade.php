@extends('layouts.app')

@section('content')

{{-- STYLE --}}
<style>
    /* Tab Styling */
    .nav-tabs .nav-link { background-color: #f8f9fa; color: #495057; border: 1px solid #e9ecef; font-weight: 600; }
    .nav-tabs .nav-link.active { background-color: #3b82f6 !important; color: white !important; border-color: #3b82f6 !important; }
    
    /* Table Styling ikut PDF */
    .table-pdf thead th { 
        background-color: #e9ecef; 
        color: #000; 
        text-align: center; 
        vertical-align: middle; 
        border: 1px solid #000; 
        font-size: 0.85rem;
    }
    .table-pdf tbody td { 
        border: 1px solid #000; 
        vertical-align: middle; 
        font-size: 0.9rem;
        padding: 4px;
    }
    .input-pdf { border: 1px solid #ccc; text-align: right; width: 100%; padding: 4px; font-size: 0.9rem; }
    .input-catatan { border: 1px solid #ccc; width: 100%; padding: 4px; font-size: 0.85rem; }
    .input-nama { border: 1px solid #ccc; width: 100%; padding: 4px; font-weight: bold; }
    .bg-yellow { background-color: #fff3cd; } 
    .header-subgroup { background-color: #d1e7dd; font-weight: bold; font-style: italic; text-transform: uppercase; padding-left: 10px; }
    .total-row td { background-color: #e2e3e5; font-weight: bold; }
    .input-disabled { background-color: transparent; border: none; font-weight: bold; width: 100%; color: #000; }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">OBJEK AM/OBJEK SEBAGAI: OS{{ $kod }} (BAHAN MAKANAN & MINUMAN)</h3>
            <p class="text-muted small">ANGGARAN PERUNTUKAN BAGI TAHUN {{ $tahun }}</p>
        </div>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun' => $tahun]) }}" class="btn btn-secondary btn-sm shadow-sm"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    </div>

    <form id="pecahanOs25Form" method="POST" action="{{ route('pentadbiran.dbus.update_os25000') }}">
        @csrf
        <input type="hidden" name="master_id" value="{{ $dbusData->id }}">
        <input type="hidden" name="master_kod" value="{{ $kod }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">

                {{-- TABS --}}
                <ul class="nav nav-tabs" id="pecahanTab" role="tablist">
                    @foreach($items as $key => $group)
                        <li class="nav-item">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $key }}" data-bs-toggle="tab" data-bs-target="#panel-{{ $key }}" type="button">
                                {{ $group['title'] }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content border border-top-0 p-3 bg-white">
                    @foreach($items as $key => $group)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="panel-{{ $key }}" role="tabpanel">
                            
                            <div class="table-responsive">
                                <table class="table table-sm table-pdf mb-0">
                                    <thead>
                                        <tr>
                                            <th width="5%">BIL</th>
                                            <th width="30%">PERKARA</th>
                                            <th width="10%">KUANTITI<br>(unit)</th>
                                            <th width="10%">BULAN<br>PEMBELIAN</th>
                                            <th width="15%">ANGGARAN KOS<br>(Sebulan) (RM)</th>
                                            <th width="15%">JUMLAH<br>(RM)</th>
                                            <th width="15%">CATATAN</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-{{ $key }}">
                                        
                                        <tr>
                                            <td colspan="7" class="header-subgroup">{{ $group['title'] }}</td>
                                        </tr>

                                        @foreach($group['data'] as $index => $item)
                                            @php
                                                $sub = $item['sub'];
                                                $saved = $pecahanMap[$sub] ?? null;
                                                
                                                // Ambil nilai dari DB jika ada, jika tidak guna default dari Controller
                                                $qty = $saved['kuantiti'] ?? $item['q'] ?? 0;
                                                $bln = $saved['bil_bulan'] ?? $item['b'] ?? 0;
                                                $ang = $saved['anggaran_sebulan'] ?? $item['a'] ?? 0.00;
                                                $catatan = $saved['catatan'] ?? ''; 
                                                $butiranVal = $saved['butiran'] ?? $item['butiran'];
                                                
                                                $jum = $qty * $bln * $ang;
                                                $edit = $item['editable'] ?? true; // Tambah default true
                                            @endphp
                                            <tr data-group="{{ $key }}">
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    @if($edit) 
                                                        <input type="text" name="data[{{ $sub }}][butiran]" class="input-nama" value="{{ $butiranVal }}" placeholder="Sila Nyatakan..."> 
                                                    @else 
                                                        <input type="text" class="input-disabled" value="{{ $butiranVal }}" readonly>
                                                        <input type="hidden" name="data[{{ $sub }}][butiran]" value="{{ $butiranVal }}"> 
                                                    @endif
                                                    <input type="hidden" name="data[{{ $sub }}][kod_ol]" value="{{ $item['kod_ol'] }}">
                                                </td>
                                                <td><input type="number" name="data[{{ $sub }}][kuantiti]" class="input-clean input-qty {{ $edit?'bg-yellow':'' }}" value="{{ $qty }}" {{ $edit?'':'readonly' }} min="0"></td>
                                                <td><input type="number" name="data[{{ $sub }}][bulan]" class="input-clean input-bln {{ $edit?'bg-yellow':'' }}" value="{{ $bln }}" {{ $edit?'':'readonly' }} min="0" max="12"></td>
                                                <td><input type="number" name="data[{{ $sub }}][anggaran]" class="input-clean input-ang {{ $edit?'bg-yellow':'' }}" value="{{ number_format($ang, 2, '.', '') }}" step="0.01" min="0"></td>
                                                <td class="text-end total-row">
                                                    <input type="hidden" class="input-jum-hidden" value="{{ $jum }}">
                                                    <span class="span-jum">{{ number_format($jum, 2) }}</span>
                                                </td>
                                                <td><input type="text" name="data[{{ $sub }}][catatan]" class="input-catatan" value="{{ $catatan }}" placeholder="Catatan Tambahan"></td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="5" class="text-end fw-bold">JUMLAH KESELURUHAN ({{ $group['title'] }})</td>
                                            <td class="text-end" id="subtotal-{{ $key }}">RM0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- GRAND TOTAL BAWAH --}}
                <div class="d-flex justify-content-between p-3 mt-2 bg-dark text-white align-items-center rounded-bottom">
                    <h5 class="mb-0">JUMLAH KESELURUHAN (OS25000)</h5>
                    <h3 class="mb-0" id="final-total">RM0.00</h3>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i> SIMPAN KEMASKINI</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        calculateAll();
        
        // Event Listener: Kira bila user ubah nombor
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                updateRow(this.closest('tr'));
            });
        });
    });

    function updateRow(row) {
        let q = parseFloat(row.querySelector('.input-qty').value) || 0;
        let b = parseFloat(row.querySelector('.input-bln').value) || 0;
        let a = parseFloat(row.querySelector('.input-ang').value) || 0;
        
        let total = q * b * a;
        
        row.querySelector('.input-jum-hidden').value = total.toFixed(2);
        row.querySelector('.span-jum').innerText = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        let group = row.getAttribute('data-group');
        updateGroupTotal(group);
    }

    function updateGroupTotal(group) {
        let sum = 0;
        let container = document.getElementById('tbody-' + group);
        
        if(container) {
            container.querySelectorAll('.input-jum-hidden').forEach(el => sum += parseFloat(el.value) || 0);
            document.getElementById('subtotal-' + group).textContent = 'RM' + sum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        updateFinalTotal();
    }

    function updateFinalTotal() {
        let sum = 0;
        document.querySelectorAll('.input-jum-hidden').forEach(el => sum += parseFloat(el.value) || 0);
        document.getElementById('final-total').textContent = 'RM' + sum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function calculateAll() {
        // Panggil fungsi pengiraan awal untuk semua tab yang mungkin ada
        @foreach(array_keys($items) as $k)
            updateGroupTotal('{{ $k }}');
        @endforeach
    }
</script>
@endpush