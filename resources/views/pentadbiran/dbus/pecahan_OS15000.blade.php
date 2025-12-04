@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Pecahan Faedah Kewangan Lain (OS15000) - Tahun {{ $dbusData->tahun }}</h3>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun' => $dbusData->tahun]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke D'BUS
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="alert alert-info border-0 shadow-sm">
        <i class="fas fa-info-circle me-2"></i> 
        Sila masukkan anggaran bagi setiap item. Gunakan butang <b>(+)</b> pada kategori yang berkaitan untuk menambah rekod.
    </div>
    
    <form action="{{ route('pentadbiran.dbus.update_os15000') }}" method="POST">
        @csrf
        <input type="hidden" name="master_id" value="{{ $dbusData->id }}"> 
        
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle" style="font-size: 0.85rem;">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 40%;">Butiran Faedah / Nama Pegawai</th>
                                <th style="width: 15%;">Anggaran Unit (RM)</th>
                                <th style="width: 10%;">Bil. Unit</th>
                                <th style="width: 20%;">Jumlah Pecahan (RM)</th>
                                <th style="width: 10%;">Catatan</th>
                                <th style="width: 5%;"></th> 
                            </tr>
                        </thead>
                        <tbody id="tbody-os15">
                            @php
                                // Ambil data dari DB
                                $dbMap = $pecahanData->keyBy(function($item) {
                                    return $item->kod_ol . '_' . $item->sub_kod;
                                });
                                $globalIndex = 0;

                                // 1. ITEM STANDARD (SELAIN OL15114)
                                $standardItems = [
                                    'OL15101' => ['tajuk' => '1. Bayaran dan Bayaran Balik Utiliti', 'dynamic' => true, 'template' => ['sub' => '1.', 'butiran' => 'Nama Pegawai', 'anggaran' => 0, 'unit' => 12]],
                                    'OL15102' => ['tajuk' => '2. Bayaran Balik Lain', 'dynamic' => false, 'items' => [
                                            ['sub' => '2.1', 'butiran' => 'Yuran Tahunan Badan Profesional', 'anggaran' => 350, 'unit' => 0],
                                            ['sub' => '2.2', 'butiran' => 'Bayaran Balik Lesen Memandu', 'anggaran' => 50, 'unit' => 1],
                                            ['sub' => '2.3', 'butiran' => 'Bayaran Balik Pasport', 'anggaran' => 300, 'unit' => 1],
                                    ]],
                                    'OL15110' => ['tajuk' => '3. Pemberian Alat Telekomunikasi', 'dynamic' => true, 'template' => ['sub' => '3.', 'butiran' => 'Nama Pegawai', 'anggaran' => 2000, 'unit' => 0]],
                                    'OL15111' => ['tajuk' => '4. Bayaran Kemudahan Perubatan', 'dynamic' => true, 'template' => ['sub' => '4.', 'butiran' => 'Nama Pesakit', 'anggaran' => 0, 'unit' => 1]],
                                    'OL15112' => ['tajuk' => '5. Pelbagai Elaun Pakaian', 'dynamic' => false, 'items' => [
                                            ['sub' => '5.1', 'butiran' => 'Elaun Pakaian Panas', 'anggaran' => 1500, 'unit' => 1],
                                            ['sub' => '5.2', 'butiran' => 'Elaun Jubah (Gown)', 'anggaran' => 750, 'unit' => 6],
                                            ['sub' => '5.3', 'butiran' => 'Elaun Baju Kot', 'anggaran' => 500, 'unit' => 8],
                                            ['sub' => '5.4', 'butiran' => 'Elaun Pakaian Istiadat', 'anggaran' => 1500, 'unit' => 2],
                                            ['sub' => '5.5', 'butiran' => 'Elaun Pakaian Istiadat Pengurniaan', 'anggaran' => 1500, 'unit' => 1],
                                    ]],
                                    'OL15113' => ['tajuk' => '6. Pemberian Anugerah Perkhidmatan Cemerlang', 'dynamic' => false, 'items' => [['sub' => '6.1', 'butiran' => 'Penerima APC', 'anggaran' => 0, 'unit' => 0]]],
                                ];
                            @endphp

                            {{-- RENDER STANDARD ITEMS --}}
                            @foreach($standardItems as $kod_ol => $group)
                                <tr class="table-active header-row" data-kod-ol="{{ $kod_ol }}">
                                    <td colspan="5" class="text-start fw-bold text-dark ps-3">{{ $group['tajuk'] }}</td>
                                    <td class="text-center">
                                        @if($group['dynamic'])
                                            <button type="button" class="btn btn-sm btn-info add-dynamic-btn" data-kod-ol="{{ $kod_ol }}" data-prefix="{{ $group['template']['sub'] }}" data-butiran="{{ $group['template']['butiran'] }}" title="Tambah Baris"><i class="fas fa-plus text-white"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $displayItems = $group['dynamic'] ? $pecahanData->where('kod_ol', $kod_ol) : collect($group['items']);
                                @endphp
                                @foreach($displayItems as $item)
                                    @php
                                        if ($item instanceof \Illuminate\Database\Eloquent\Model) {
                                            $sub = $item->sub_kod; $butiran = $item->butiran; $ang = $item->anggaran; $unit = $item->bil_unit; $cat = $item->catatan;
                                        } else {
                                            $sub = $item['sub']; $butiran = $item['butiran']; 
                                            $dbItem = $dbMap->get($kod_ol . '_' . $sub);
                                            $ang = $dbItem->anggaran ?? $item['anggaran']; $unit = $dbItem->bil_unit ?? $item['unit']; $cat = $dbItem->catatan ?? '';
                                        }
                                        $jum = $ang * $unit;
                                        $dynamicClass = $group['dynamic'] ? 'dynamic-item' : 'static-item';
                                    @endphp
                                    <tr class="row-os15-data {{ $dynamicClass }}" data-kod-ol="{{ $kod_ol }}">
                                        <td class="text-start ps-4 d-flex align-items-center">
                                            @if($group['dynamic'])
                                                <input type="text" name="pecahan[{{ $globalIndex }}][sub_kod]" class="form-control form-control-sm me-2 text-center sub-kod-display" style="width: 60px; background-color: #f8f9fa;" value="{{ $sub }}" readonly>
                                                <input type="text" name="pecahan[{{ $globalIndex }}][butiran]" class="form-control form-control-sm" value="{{ $butiran }}">
                                            @else
                                                {{ $sub }} - {{ $butiran }}
                                                <input type="hidden" name="pecahan[{{ $globalIndex }}][sub_kod]" value="{{ $sub }}">
                                                <input type="hidden" name="pecahan[{{ $globalIndex }}][butiran]" value="{{ $butiran }}">
                                            @endif
                                        </td>
                                        <td><input type="number" step="0.01" name="pecahan[{{ $globalIndex }}][anggaran]" class="form-control form-control-sm text-end input-anggaran" value="{{ $ang }}" min="0"></td>
                                        <td><input type="number" name="pecahan[{{ $globalIndex }}][bil_unit]" class="form-control form-control-sm text-center input-unit" value="{{ $unit }}" min="0"></td>
                                        <td class="text-end fw-bold text-success jumlah-row">RM <span class="jumlah-nilai">{{ number_format($jum, 2) }}</span><input type="hidden" name="pecahan[{{ $globalIndex }}][jumlah]" class="jumlah-input" value="{{ $jum }}"></td>
                                        <td><input type="text" name="pecahan[{{ $globalIndex }}][catatan]" class="form-control form-control-sm" value="{{ $cat }}"></td>
                                        <td class="text-center">
                                            @if($group['dynamic'])
                                                <button type="button" class="btn btn-danger btn-sm remove-dynamic-row"><i class="fas fa-trash"></i></button>
                                            @endif
                                        </td>
                                        <input type="hidden" name="pecahan[{{ $globalIndex }}][kod_ol]" value="{{ $kod_ol }}">
                                    </tr>
                                    @php $globalIndex++; @endphp
                                @endforeach
                            @endforeach

                            {{-- 2. OL15114: PELBAGAI KEMUDAHAN TAMBANG --}}
                            @php $kod14 = 'OL15114'; @endphp
                            <tr class="table-active"><td colspan="6" class="text-start fw-bold text-dark ps-3">7. Pelbagai Kemudahan Tambang Pengangkutan ({{ $kod14 }})</td></tr>
                            
                            <tr class="bg-light"><td colspan="6" class="text-start fw-bold ps-4">7.1 Tambang Mengunjungi Wilayah Asal</td></tr>
                            
                            {{-- 7.1a BUJANG --}}
                            <tr class="header-row" data-kod-ol="{{ $kod14 }}" data-group="bujang">
                                <td colspan="5" class="text-start ps-5 fst-italic">a) Bujang (Malaysian Airline Berhad)</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info add-dynamic-btn" data-kod-ol="{{ $kod14 }}" data-prefix="7.1a" data-butiran="Tambang Bujang" data-anggaran="2684.60" data-unit="1" data-insert-after="bujang" title="Tambah Bujang"><i class="fas fa-plus text-white"></i></button>
                                </td>
                            </tr>
                            @foreach($pecahanData->where('kod_ol', $kod14)->filter(fn($i) => str_starts_with($i->sub_kod, '7.1a')) as $item)
                                <tr class="row-os15-data dynamic-item" data-kod-ol="{{ $kod14 }}" data-group="bujang">
                                    <td class="text-start ps-4 d-flex align-items-center">
                                        <input type="text" name="pecahan[{{ $globalIndex }}][sub_kod]" class="form-control form-control-sm me-2 text-center sub-kod-display" style="width: 60px; background-color: #f8f9fa;" value="{{ $item->sub_kod }}" readonly>
                                        <input type="text" name="pecahan[{{ $globalIndex }}][butiran]" class="form-control form-control-sm" value="{{ $item->butiran }}">
                                    </td>
                                    <td><input type="number" step="0.01" name="pecahan[{{ $globalIndex }}][anggaran]" class="form-control form-control-sm text-end input-anggaran" value="{{ $item->anggaran }}" min="0"></td>
                                    <td><input type="number" name="pecahan[{{ $globalIndex }}][bil_unit]" class="form-control form-control-sm text-center input-unit" value="{{ $item->bil_unit }}" min="0"></td>
                                    <td class="text-end fw-bold text-success jumlah-row">RM <span class="jumlah-nilai">{{ number_format($item->anggaran * $item->bil_unit, 2) }}</span><input type="hidden" name="pecahan[{{ $globalIndex }}][jumlah]" class="jumlah-input" value="{{ $item->anggaran * $item->bil_unit }}"></td>
                                    <td><input type="text" name="pecahan[{{ $globalIndex }}][catatan]" class="form-control form-control-sm" value="{{ $item->catatan }}"></td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-dynamic-row"><i class="fas fa-trash"></i></button></td>
                                    <input type="hidden" name="pecahan[{{ $globalIndex }}][kod_ol]" value="{{ $kod14 }}">
                                </tr>
                                @php $globalIndex++; @endphp
                            @endforeach

                            {{-- 7.1b KELUARGA --}}
                            <tr class="header-row" data-kod-ol="{{ $kod14 }}" data-group="keluarga">
                                <td colspan="5" class="text-start ps-5 fst-italic">b) Bekeluarga (MAB)</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info add-dynamic-btn" data-kod-ol="{{ $kod14 }}" data-prefix="7.1b" data-butiran="Tambang Keluarga" data-anggaran="5000.00" data-unit="1" data-insert-after="keluarga" title="Tambah Keluarga"><i class="fas fa-plus text-white"></i></button>
                                </td>
                            </tr>
                            @foreach($pecahanData->where('kod_ol', $kod14)->filter(fn($i) => str_starts_with($i->sub_kod, '7.1b')) as $item)
                                <tr class="row-os15-data dynamic-item" data-kod-ol="{{ $kod14 }}" data-group="keluarga">
                                    <td class="text-start ps-4 d-flex align-items-center">
                                        <input type="text" name="pecahan[{{ $globalIndex }}][sub_kod]" class="form-control form-control-sm me-2 text-center sub-kod-display" style="width: 60px; background-color: #f8f9fa;" value="{{ $item->sub_kod }}" readonly>
                                        <input type="text" name="pecahan[{{ $globalIndex }}][butiran]" class="form-control form-control-sm" value="{{ $item->butiran }}">
                                    </td>
                                    <td><input type="number" step="0.01" name="pecahan[{{ $globalIndex }}][anggaran]" class="form-control form-control-sm text-end input-anggaran" value="{{ $item->anggaran }}" min="0"></td>
                                    <td><input type="number" name="pecahan[{{ $globalIndex }}][bil_unit]" class="form-control form-control-sm text-center input-unit" value="{{ $item->bil_unit }}" min="0"></td>
                                    <td class="text-end fw-bold text-success jumlah-row">RM <span class="jumlah-nilai">{{ number_format($item->anggaran * $item->bil_unit, 2) }}</span><input type="hidden" name="pecahan[{{ $globalIndex }}][jumlah]" class="jumlah-input" value="{{ $item->anggaran * $item->bil_unit }}"></td>
                                    <td><input type="text" name="pecahan[{{ $globalIndex }}][catatan]" class="form-control form-control-sm" value="{{ $item->catatan }}"></td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-dynamic-row"><i class="fas fa-trash"></i></button></td>
                                    <input type="hidden" name="pecahan[{{ $globalIndex }}][kod_ol]" value="{{ $kod14 }}">
                                </tr>
                                @php $globalIndex++; @endphp
                            @endforeach

                            {{-- 7.2 TPKLN --}}
                            <tr class="bg-light header-row" data-kod-ol="{{ $kod14 }}" data-group="tpkln">
                                <td colspan="5" class="text-start fw-bold ps-4">7.2 Tambang Percuma Ke Luar Negara (TPKLN)</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info add-dynamic-btn" data-kod-ol="{{ $kod14 }}" data-prefix="7.2" data-butiran="Nama Pegawai" data-anggaran="0.00" data-unit="1" data-insert-after="tpkln" title="Tambah TPKLN"><i class="fas fa-plus text-white"></i></button>
                                </td>
                            </tr>
                            @foreach($pecahanData->where('kod_ol', $kod14)->filter(fn($i) => str_starts_with($i->sub_kod, '7.2')) as $item)
                                <tr class="row-os15-data dynamic-item" data-kod-ol="{{ $kod14 }}" data-group="tpkln">
                                    <td class="text-start ps-4 d-flex align-items-center">
                                        <input type="text" name="pecahan[{{ $globalIndex }}][sub_kod]" class="form-control form-control-sm me-2 text-center sub-kod-display" style="width: 60px; background-color: #f8f9fa;" value="{{ $item->sub_kod }}" readonly>
                                        <input type="text" name="pecahan[{{ $globalIndex }}][butiran]" class="form-control form-control-sm" value="{{ $item->butiran }}">
                                    </td>
                                    <td><input type="number" step="0.01" name="pecahan[{{ $globalIndex }}][anggaran]" class="form-control form-control-sm text-end input-anggaran" value="{{ $item->anggaran }}" min="0"></td>
                                    <td><input type="number" name="pecahan[{{ $globalIndex }}][bil_unit]" class="form-control form-control-sm text-center input-unit" value="{{ $item->bil_unit }}" min="0"></td>
                                    <td class="text-end fw-bold text-success jumlah-row">RM <span class="jumlah-nilai">{{ number_format($item->anggaran * $item->bil_unit, 2) }}</span><input type="hidden" name="pecahan[{{ $globalIndex }}][jumlah]" class="jumlah-input" value="{{ $item->anggaran * $item->bil_unit }}"></td>
                                    <td><input type="text" name="pecahan[{{ $globalIndex }}][catatan]" class="form-control form-control-sm" value="{{ $item->catatan }}"></td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-dynamic-row"><i class="fas fa-trash"></i></button></td>
                                    <input type="hidden" name="pecahan[{{ $globalIndex }}][kod_ol]" value="{{ $kod14 }}">
                                </tr>
                                @php $globalIndex++; @endphp
                            @endforeach

                            {{-- 3. ITEM 8 (OL15119) --}}
                            @php $kodLast = 'OL15119'; $dbItemLast = $dbMap->get($kodLast . '_8.1'); @endphp
                            <tr class="table-active"><td colspan="6" class="text-start fw-bold text-dark ps-3">8. Faedah Kewangan Lain / Elaun Perkakasan</td></tr>
                            <tr class="row-os15-data static-item" data-kod-ol="{{ $kodLast }}">
                                <td class="text-start ps-4">8.1 - Insuran Perlindungan Persendirian <input type="hidden" name="pecahan[{{ $globalIndex }}][sub_kod]" value="8.1"><input type="hidden" name="pecahan[{{ $globalIndex }}][butiran]" value="Insuran Perlindungan Persendirian"></td>
                                <td><input type="number" step="0.01" name="pecahan[{{ $globalIndex }}][anggaran]" class="form-control form-control-sm text-end input-anggaran" value="{{ $dbItemLast->anggaran ?? 100 }}"></td>
                                <td><input type="number" name="pecahan[{{ $globalIndex }}][bil_unit]" class="form-control form-control-sm text-center input-unit" value="{{ $dbItemLast->bil_unit ?? 2 }}"></td>
                                <td class="text-end fw-bold text-success jumlah-row">RM <span class="jumlah-nilai">{{ number_format(($dbItemLast->anggaran ?? 100) * ($dbItemLast->bil_unit ?? 2), 2) }}</span><input type="hidden" name="pecahan[{{ $globalIndex }}][jumlah]" class="jumlah-input" value="{{ ($dbItemLast->anggaran ?? 100) * ($dbItemLast->bil_unit ?? 2) }}"></td>
                                <td><input type="text" name="pecahan[{{ $globalIndex }}][catatan]" class="form-control form-control-sm" value="{{ $dbItemLast->catatan ?? '' }}"></td>
                                <td class="text-center"></td>
                                <input type="hidden" name="pecahan[{{ $globalIndex }}][kod_ol]" value="{{ $kodLast }}">
                            </tr>

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">JUMLAH KESELURUHAN OS15000</td>
                                <td class="text-end fw-bold fs-5" id="grand-total">RM {{ number_format($dbusData->jumlah, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card-footer text-end bg-light">
                <button type="submit" class="btn btn-success shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Pecahan & Kemaskini D'BUS
                </button>
                <input type="hidden" name="master_grand_total" id="master-grand-total" value="0">
            </div>
        </form>
    </div>
</div>

{{-- TEMPLATE ROW (CLIENT SIDE) --}}
<script type="text/template" id="row-template">
    <tr class="row-os15-data dynamic-item" data-kod-ol="{kod}" data-group="{group}">
        <td class="text-start ps-4 d-flex align-items-center">
            <input type="hidden" name="pecahan[{idx}][sub_kod]" class="sub-kod-value" value="{sub}">
            <input type="text" class="form-control form-control-sm me-2 text-center sub-kod-display" style="width: 60px; background-color: #f8f9fa;" value="{sub}" readonly disabled>
            <input type="text" name="pecahan[{idx}][butiran]" class="form-control form-control-sm" value="{butiran}">
        </td>
        <td><input type="number" step="0.01" name="pecahan[{idx}][anggaran]" class="form-control form-control-sm text-end input-anggaran" value="{ang}"></td>
        <td><input type="number" name="pecahan[{idx}][bil_unit]" class="form-control form-control-sm text-center input-unit" value="{unit}"></td>
        <td class="text-end fw-bold text-success jumlah-row">RM <span class="jumlah-nilai">0.00</span><input type="hidden" name="pecahan[{idx}][jumlah]" class="jumlah-input" value="0"></td>
        <td><input type="text" name="pecahan[{idx}][catatan]" class="form-control form-control-sm"></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-dynamic-row"><i class="fas fa-trash"></i></button></td>
        <input type="hidden" name="pecahan[{idx}][kod_ol]" value="{kod}">
    </tr>
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('tbody-os15');
    const grandTotalElement = document.getElementById('grand-total');
    const masterTotalInput = document.getElementById('master-grand-total');

    function formatNumber(num) { return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
    function parseVal(str) { return parseFloat(str.toString().replace(/[^0-9.-]+/g,"")) || 0; }

    function calculateRow(row) {
        const ang = parseVal(row.querySelector('.input-anggaran').value);
        const unit = parseVal(row.querySelector('.input-unit').value);
        const total = ang * unit;
        row.querySelector('.jumlah-nilai').innerText = formatNumber(total);
        row.querySelector('.jumlah-input').value = total.toFixed(2);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.jumlah-input').forEach(inp => total += parseVal(inp.value));
        grandTotalElement.innerText = "RM " + formatNumber(total);
        masterTotalInput.value = total.toFixed(2);
    }

    function updateSequence(groupName, kodOl) {
        let rows;
        let basePrefix = "";

        if (groupName) {
            rows = document.querySelectorAll(`.row-os15-data[data-group="${groupName}"]`);
            if (groupName === 'bujang') basePrefix = "7.1a";
            if (groupName === 'keluarga') basePrefix = "7.1b";
            if (groupName === 'tpkln') basePrefix = "7.2";
        } else if (kodOl) {
            rows = document.querySelectorAll(`.row-os15-data[data-kod-ol="${kodOl}"]`);
            if(kodOl === 'OL15101') basePrefix = "1.";
            if(kodOl === 'OL15110') basePrefix = "3.";
            if(kodOl === 'OL15111') basePrefix = "4.";
        }

        if (rows && rows.length > 0) {
            rows.forEach((row, index) => {
                const displayInput = row.querySelector('.sub-kod-display');
                const valueInput = row.querySelector('.sub-kod-value'); 
                
                if (displayInput) {
                    let displayVal = basePrefix.endsWith('.') ? (basePrefix + (index + 1)) : basePrefix;
                    displayInput.value = displayVal;

                    if (valueInput) {
                        if (basePrefix.endsWith('.')) {
                            valueInput.value = basePrefix + (index + 1); 
                        } else {
                            valueInput.value = basePrefix + '-' + (index + 1); 
                        }
                    }
                }
            });
        }
    }

    function getNextIndex() {
        let max = 0;
        document.querySelectorAll('input[name^="pecahan["]').forEach(el => {
            let match = el.name.match(/\[(\d+)\]/);
            if(match) max = Math.max(max, parseInt(match[1]));
        });
        return max + 1;
    }

    tableBody.addEventListener('click', function(e) {
        // Delete
        if (e.target.closest('.remove-dynamic-row')) {
            if(confirm('Padam baris ini?')) {
                const row = e.target.closest('tr');
                const group = row.getAttribute('data-group');
                const kodOl = row.getAttribute('data-kod-ol');
                row.remove();
                
                if(group) updateSequence(group, null);
                else if(kodOl) updateSequence(null, kodOl);
                
                updateGrandTotal();
            }
        }

        // Add
        const btn = e.target.closest('.add-dynamic-btn');
        if (btn) {
            const kodOl = btn.dataset.kodOl;
            const prefix = btn.dataset.prefix;
            const butiran = btn.dataset.butiran;
            const ang = btn.dataset.anggaran || 0;
            const unit = btn.dataset.unit || 1;
            const group = btn.dataset.insertAfter || '';
            
            const idx = getNextIndex();
            const uniqueSub = prefix + '-NEW'; 

            let template = document.getElementById('row-template').innerHTML;
            template = template.replace(/{idx}/g, idx).replace(/{kod}/g, kodOl).replace(/{sub}/g, uniqueSub)
                               .replace(/{butiran}/g, butiran).replace(/{ang}/g, ang).replace(/{unit}/g, unit).replace(/{group}/g, group);

            let targetRow = btn.closest('tr');
            if (group) {
                const groupRows = document.querySelectorAll(`.row-os15-data[data-group="${group}"]`);
                if (groupRows.length > 0) targetRow = groupRows[groupRows.length - 1];
            } else {
                const sameKodRows = document.querySelectorAll(`.row-os15-data[data-kod-ol="${kodOl}"]`);
                if (sameKodRows.length > 0) targetRow = sameKodRows[sameKodRows.length - 1];
            }

            targetRow.insertAdjacentHTML('afterend', template);
            
            if(group) updateSequence(group, null);
            else updateSequence(null, kodOl);

            calculateRow(targetRow.nextElementSibling);
        }
    });

    tableBody.addEventListener('input', e => {
        if(e.target.matches('.input-anggaran, .input-unit')) calculateRow(e.target.closest('tr'));
    });

    // Init
    document.querySelectorAll('.row-os15-data').forEach(calculateRow);
    ['bujang', 'keluarga', 'tpkln'].forEach(g => updateSequence(g, null));
    ['OL15101', 'OL15110', 'OL15111'].forEach(k => updateSequence(null, k));
});
</script>

@endpush

@endsection