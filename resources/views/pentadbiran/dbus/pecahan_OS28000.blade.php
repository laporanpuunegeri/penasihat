@extends('layouts.app')

@section('content')

<style>
    .nav-tabs .nav-link { background-color: #f8f9fa; color: #495057; border: 1px solid #dee2e6; font-weight: 600; margin-right: 2px; }
    .nav-tabs .nav-link.active { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .table-pdf thead th { background-color: #e9ecef; color: #000; text-align: center; vertical-align: middle; border: 1px solid #999; font-size: 0.85rem; }
    .table-pdf tbody td { border: 1px solid #999; vertical-align: middle; font-size: 0.9rem; padding: 4px; }
    .input-clean { border: 1px solid #ccc; text-align: right; width: 100%; padding: 4px; font-size: 0.9rem; }
    .input-catatan { border: 1px solid #ccc; width: 100%; padding: 4px; font-size: 0.85rem; }
    .input-nama { border: 1px solid #ccc; width: 100%; padding: 4px; font-weight: bold; }
    .header-subgroup { background-color: #d1e7dd; font-weight: bold; font-style: italic; text-transform: uppercase; padding-left: 10px; border: 1px solid #999; }
    .total-row td { background-color: #e2e3e5; font-weight: bold; border: 1px solid #999; }
</style>

@php
    $renderRow = function($item, $index, $key, $pecahanMap) {
        $sub = $item['sub'];
        $saved = $pecahanMap[$sub] ?? null;
        
        $qty = $saved['kuantiti'] ?? $item['q'] ?? 0;
        $servis = $saved['bil_servis'] ?? $item['s'] ?? 0;
        $kos = $saved['anggaran_kos'] ?? $item['k'] ?? 0;
        $catatan = $saved['catatan'] ?? '';
        $butiranVal = $saved['butiran'] ?? $item['butiran'];
        $jum = $qty * $servis * $kos;

        return '
        <tr>
            <td class="text-center">'.$index.'</td>
            <td><input type="text" name="data['.$sub.'][butiran]" class="input-nama" value="'.e($butiranVal).'"></td>
            <td><input type="number" name="data['.$sub.'][kuantiti]" class="input-clean input-qty" value="'.$qty.'" min="0"></td>
            <td><input type="number" name="data['.$sub.'][bil_servis]" class="input-clean input-servis" value="'.$servis.'" min="0"></td>
            <td><input type="number" name="data['.$sub.'][anggaran_kos]" class="input-clean input-kos" value="'.number_format($kos, 2, '.', '').'" step="0.01" min="0"></td>
            <td class="text-end total-cell">
                <input type="hidden" class="input-jum-hidden" value="'.$jum.'">
                <span class="span-jum">'.number_format($jum, 2).'</span>
            </td>
            <td><input type="text" name="data['.$sub.'][catatan]" class="input-catatan" value="'.e($catatan).'"></td>
        </tr>';
    };
@endphp

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">OBJEK AM/OBJEK SEBAGAI: OS{{ $kod }} (PENYELENGGARAAN)</h3>
            <p class="text-muted small">ANGGARAN PERUNTUKAN TAHUN {{ $tahun }}</p>
        </div>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun' => $tahun]) }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <form method="POST" action="{{ route('pentadbiran.dbus.update_os28000') }}">
        @csrf
        <input type="hidden" name="master_id" value="{{ $dbusData->id }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">
                
                {{-- TAB NAV --}}
                <ul class="nav nav-tabs mb-3" id="os28Tab" role="tablist">
                    @foreach($items as $key => $tab)
                        <li class="nav-item">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-btn-{{ $key }}" data-bs-toggle="tab" data-bs-target="#content-{{ $key }}" type="button">{{ $tab['title'] }}</button>
                        </li>
                    @endforeach
                </ul>

                {{-- TAB CONTENT --}}
                <div class="tab-content">
                    @foreach($items as $key => $tab)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content-{{ $key }}">
                            <div class="table-responsive">
                                <table class="table table-sm table-pdf mb-0">
                                    <thead>
                                        <tr>
                                            <th width="5%">BIL</th>
                                            <th width="35%">PERKARA</th>
                                            <th width="10%">KUANTITI<br>(unit)</th>
                                            <th width="10%">BILANGAN<br>SERVIS (KALI)</th>
                                            <th width="15%">ANGGARAN KOS<br>(RM)</th>
                                            <th width="15%">JUMLAH<br>(RM)</th>
                                            <th width="10%">CATATAN</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-{{ $key }}">
                                        @foreach($tab['groups'] as $group)
                                            <tr><td colspan="7" class="header-subgroup">{{ $group['title'] }}</td></tr>
                                            @foreach($group['items'] as $idx => $item)
                                                {!! $renderRow($item, $idx + 1, $key, $pecahanMap) !!}
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="5" class="text-end">JUMLAH ({{ $tab['title'] }})</td>
                                            <td class="text-end" id="total-{{ $key }}">RM0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between p-3 mt-3 bg-dark text-white rounded">
                    <h5 class="mb-0">JUMLAH KESELURUHAN (OS28000)</h5>
                    <h3 class="mb-0" id="final-grand-total">RM0.00</h3>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">SIMPAN KEMASKINI</button>
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
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() { updateRow(this.closest('tr')); });
        });
    });

    function updateRow(row) {
        let q = parseFloat(row.querySelector('.input-qty').value) || 0;
        let s = parseFloat(row.querySelector('.input-servis').value) || 0;
        let k = parseFloat(row.querySelector('.input-kos').value) || 0;
        let total = q * s * k;
        
        row.querySelector('.input-jum-hidden').value = total.toFixed(2);
        row.querySelector('.span-jum').innerText = total.toLocaleString('en-US', {minimumFractionDigits: 2});
        
        // Cari parent tbody ID untuk update total tab semasa
        let tbodyId = row.closest('tbody').id;
        let groupKey = tbodyId.replace('tbody-', '');
        updateGroupTotal(groupKey);
    }

    function updateGroupTotal(key) {
        let sum = 0;
        document.querySelectorAll(`#tbody-${key} .input-jum-hidden`).forEach(el => sum += parseFloat(el.value) || 0);
        document.getElementById(`total-${key}`).textContent = 'RM' + sum.toLocaleString('en-US', {minimumFractionDigits: 2});
        updateFinalTotal();
    }

    function updateFinalTotal() {
        let grand = 0;
        document.querySelectorAll('.input-jum-hidden').forEach(el => grand += parseFloat(el.value) || 0);
        document.getElementById('final-grand-total').textContent = 'RM' + grand.toLocaleString('en-US', {minimumFractionDigits: 2});
    }

    function calculateAll() {
        @foreach(array_keys($items) as $k)
            updateGroupTotal('{{ $k }}');
        @endforeach
    }
</script>
@endpush