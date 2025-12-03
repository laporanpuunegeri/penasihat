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
        Sila masukkan anggaran bagi setiap item. Jumlah keseluruhan akan mengemas kini rekod **OS15000** secara automatik.
    </div>
    
    {{-- Action borang ke route update yang betul --}}
    <form action="{{ route('pentadbiran.dbus.update_os15000') }}" method="POST">
        @csrf
        <input type="hidden" name="master_id" value="{{ $dbusData->id }}"> 
        
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle" style="font-size: 0.85rem;">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 45%;">Butiran Faedah (Kod OL)</th>
                                <th style="width: 15%;">Anggaran Unit (RM)</th>
                                <th style="width: 10%;">Bil. Unit</th>
                                <th style="width: 20%;">Jumlah Pecahan (RM)</th>
                                <th style="width: 10%;">Catatan</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-os15">
                            @php
                                $itemsStructure = [
                                    // ... (Item OL15101 hingga OL15113 dikekalkan) ...
                                    'OL15101' => [
                                        'tajuk' => 'Bayaran dan Bayaran Balik Utiliti',
                                        'dynamic' => true, 
                                        'template' => ['sub' => '', 'butiran' => 'Nama Pegawai/Jenis Utiliti (Sebulan)', 'anggaran_default' => 0.00, 'unit_default' => 12, 'catatan_default' => 'Cth: Bil Elektrik'],
                                        'initial_items' => [], 
                                    ],
                                    'OL15102' => [
                                        'tajuk' => 'Bayaran Balik Lain (Pasport, Lesen, Yuran Profesional)',
                                        'dynamic' => false, 
                                        'items' => [
                                            ['sub' => '2.1', 'butiran' => 'Yuran Tahunan Badan Profesional', 'anggaran_default' => 350.00, 'unit_default' => 0, 'catatan_default' => 'Sila Nyatakan Nama Yuran'],
                                            ['sub' => '2.2', 'butiran' => 'Bayaran Balik Lesen Memandu', 'anggaran_default' => 50.00, 'unit_default' => 1, 'catatan_default' => 'Pemandu Kenderaan H1'],
                                            ['sub' => '2.3', 'butiran' => 'Bayaran Balik Pasport', 'anggaran_default' => 300.00, 'unit_default' => 1, 'catatan_default' => ''],
                                        ]
                                    ],
                                    'OL15110' => [
                                        'tajuk' => 'Pemberian Alat Telekomunikasi Mudah Alih',
                                        'dynamic' => true,
                                        'template' => ['sub' => '', 'butiran' => 'Nama Pegawai & Gred', 'anggaran_default' => 2000.00, 'unit_default' => 0, 'catatan_default' => 'Tempoh 2 Tahun'],
                                        'initial_items' => [],
                                    ],
                                    'OL15111' => [
                                        'tajuk' => 'Bayaran Kemudahan Perubatan',
                                        'dynamic' => true,
                                        'template' => ['sub' => '', 'butiran' => 'Nama Pesakit', 'anggaran_default' => 0.00, 'unit_default' => 1, 'catatan_default' => ''],
                                        'initial_items' => [],
                                    ],
                                    'OL15112' => [
                                        'tajuk' => 'Pelbagai Elaun Pakaian',
                                        'dynamic' => false,
                                        'items' => [
                                            ['sub' => '5.1', 'butiran' => 'Elaun Pakaian Panas', 'anggaran_default' => 1500.00, 'unit_default' => 1, 'catatan_default' => ''],
                                            ['sub' => '5.2', 'butiran' => 'Elaun Jubah (Gown)', 'anggaran_default' => 750.00, 'unit_default' => 6, 'catatan_default' => ''],
                                            ['sub' => '5.3', 'butiran' => 'Elaun Baju Kot', 'anggaran_default' => 500.00, 'unit_default' => 8, 'catatan_default' => ''],
                                            ['sub' => '5.4', 'butiran' => 'Elaun Pakaian Istiadat', 'anggaran_default' => 1500.00, 'unit_default' => 2, 'catatan_default' => ''],
                                            ['sub' => '5.5', 'butiran' => 'Elaun Pakaian Istiadat Pengurniaan', 'anggaran_default' => 1500.00, 'unit_default' => 1, 'catatan_default' => ''],
                                        ]
                                    ],
                                    'OL15113' => [
                                        'tajuk' => 'Pemberian Anugerah Perkhidmatan Cemerlang',
                                        'dynamic' => false,
                                        'items' => [
                                            ['sub' => '6.1', 'butiran' => 'Penerima APC (Anggaran per orang)', 'anggaran_default' => 0.00, 'unit_default' => 0, 'catatan_default' => ''],
                                        ]
                                    ],
                                    // 🔥 STRUKTUR OL15114 DIBETULKAN UNTUK PEMAPARAN STATIK SEPENUHNYA 🔥
                                    'OL15114' => [
                                        'tajuk' => 'Pelbagai Kemudahan Tambang Pengangkutan',
                                        'dynamic' => false, // Set kepada False untuk memaparkan item statik
                                        'items' => [
                                            // 7.1 Tambang Mengunjungi Wilayah Asal
                                            ['sub' => '7.1a', 'butiran' => 'Tambang Mengunjungi Wilayah Asal (Bujang) MAB (T&C)', 'anggaran_default' => 2684.60, 'unit_default' => 0, 'catatan_default' => ''],
                                            ['sub' => '7.1b', 'butiran' => 'Tambang Mengunjungi Wilayah Asal (Keluarga/KK)', 'anggaran_default' => 11678.40, 'unit_default' => 1, 'catatan_default' => 'Kota Kinabalu: Fazilah binti Abidin'],
                                            ['sub' => '7.1c', 'butiran' => 'Tambang Mengunjungi Wilayah Asal (Keluarga/Tw)', 'anggaran_default' => 5839.20, 'unit_default' => 1, 'catatan_default' => 'Tawau: Rahmatia'],
                                            ['sub' => '7.1d', 'butiran' => 'Tambang Mengunjungi Wilayah Asal (Keluarga/Kch)', 'anggaran_default' => 7509.12, 'unit_default' => 1, 'catatan_default' => 'Kuching: Muhammad Adam'],
                                            // 7.2 Tambang Percuma Ke Luar Negara (TPKLN)
                                            ['sub' => '7.2a', 'butiran' => 'Tambang Percuma Ke Luar Negara (TPKLN) Pegawai A', 'anggaran_default' => 0.00, 'unit_default' => 0, 'catatan_default' => ''],
                                            ['sub' => '7.2b', 'butiran' => 'Tambang Percuma Ke Luar Negara (TPKLN) Pegawai B', 'anggaran_default' => 0.00, 'unit_default' => 0, 'catatan_default' => ''],
                                        ]
                                    ],
                                    'OL15119' => [
                                        'tajuk' => 'Faedah Kewangan Kakitangan Awam Termasuk Elaun Perkakasan',
                                        'dynamic' => false,
                                        'items' => [
                                            ['sub' => '8.1', 'butiran' => 'Insuran Perlindungan Persendirian (Anggaran per orang)', 'anggaran_default' => 100.00, 'unit_default' => 2, 'catatan_default' => ''],
                                        ]
                                    ],
                                ];
                                
                                $dbMap = $pecahanData->keyBy(function($item) {
                                    return $item->kod_ol . '_' . $item->sub_kod;
                                });
                                $globalIndex = 0;
                            @endphp

                            @foreach($itemsStructure as $kod_ol => $group)
                                {{-- HEADER KOD OL --}}
                                <tr class="table-active header-row" data-kod-ol="{{ $kod_ol }}">
                                    <td colspan="5" class="text-start fw-bold text-dark ps-3">{{ $kod_ol }} - {{ $group['tajuk'] }}</td>
                                    <td class="text-center">
                                        @if ($group['dynamic'])
                                            {{-- BUTANG TAMBAH BARIS HANYA PADA ITEM DYNAMIC --}}
                                            <button type="button" class="btn btn-sm btn-info add-dynamic-btn" data-kod-ol="{{ $kod_ol }}" data-template-butiran="{{ $group['template']['butiran'] }}" title="Tambah Baris">
                                                <i class="fas fa-plus text-white"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                
                                @php
                                    // Item yang akan dipaparkan (Statik atau Item dari DB)
                                    $displayItems = $group['dynamic'] ? ($dbDataMap[$kod_ol] ?? collect()) : collect($group['items']);
                                @endphp
                                
                                @foreach($displayItems as $item)
                                    @php
                                        // Pengekstrak data untuk item ini
                                        if ($group['dynamic']) {
                                            $sub_kod = $item->sub_kod;
                                            $butiran = $item->butiran;
                                            $anggaran = $item->anggaran;
                                            $bil_unit = $item->bil_unit;
                                            $catatan = $item->catatan;
                                        } else {
                                            $sub_kod = $item['sub'];
                                            $butiran = $item['butiran'];
                                            $anggaran = $dbMap->get($kod_ol . '_' . $sub_kod)->anggaran ?? $item['anggaran_default'];
                                            $bil_unit = $dbMap->get($kod_ol . '_' . $sub_kod)->bil_unit ?? $item['unit_default'];
                                            $catatan = $dbMap->get($kod_ol . '_' . $sub_kod)->catatan ?? $item['catatan_default'];
                                        }

                                        $jumlah = $anggaran * $bil_unit;
                                    @endphp
                                    <tr class="row-os15-data {{ $group['dynamic'] ? 'dynamic-item' : 'static-item' }}" data-kod-ol="{{ $kod_ol }}" data-sub-kod="{{ $sub_kod }}">
                                        <td class="text-start ps-4">{{ $sub_kod }} - {{ $butiran }}</td>
                                        
                                        <td>
                                            <input type="number" step="0.01" name="pecahan[{{ $globalIndex }}][anggaran]" 
                                                   class="form-control form-control-sm text-end input-anggaran" 
                                                   value="{{ old("pecahan.{$globalIndex}.anggaran", $anggaran) }}" min="0">
                                        </td>
                                        
                                        <td>
                                            <input type="number" name="pecahan[{{ $globalIndex }}][bil_unit]" 
                                                   class="form-control form-control-sm text-center input-unit" 
                                                   value="{{ old("pecahan.{$globalIndex}.bil_unit", $bil_unit) }}" min="0">
                                        </td>
                                        
                                        <td class="text-end fw-bold text-success jumlah-row">
                                            RM <span class="jumlah-nilai">{{ number_format($jumlah, 2) }}</span>
                                            <input type="hidden" name="pecahan[{{ $globalIndex }}][jumlah]" class="jumlah-input" value="{{ $jumlah }}">
                                        </td>
                                        
                                        <td>
                                            <input type="text" name="pecahan[{{ $globalIndex }}][catatan]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ old("pecahan.{$globalIndex}.catatan", $catatan) }}" 
                                                   placeholder="Catatan per item...">
                                        </td>
                                        
                                        <td class="text-center">
                                            @if($group['dynamic'])
                                                <button type="button" class="btn btn-danger btn-sm remove-dynamic-row" title="Padam Baris"><i class="fas fa-trash"></i></button>
                                            @endif
                                        </td>
                                        
                                        {{-- Hidden fields untuk Controller --}}
                                        <input type="hidden" name="pecahan[{{ $globalIndex }}][kod_ol]" value="{{ $kod_ol }}">
                                        <input type="hidden" name="pecahan[{{ $globalIndex }}][sub_kod]" value="{{ $sub_kod }}">
                                        <input type="hidden" name="pecahan[{{ $globalIndex }}][butiran]" value="{{ $butiran }}">
                                    </tr>
                                    @php $globalIndex++; @endphp
                                @endforeach
                            @endforeach
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
                <input type="hidden" name="master_grand_total" id="master-grand-total" value="{{ $dbusData->jumlah }}">
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('tbody-os15');
    const grandTotalElement = document.getElementById('grand-total');
    const masterTotalInput = document.getElementById('master-grand-total');

    function formatNumber(num) { 
        return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
    }
    function parseFormattedNumber(str) { 
        const cleaned = str.toString().replace(/[^0-9.-]+/g,"");
        return parseFloat(cleaned) || 0; 
    }

    function calculateRowTotal(row) {
        const anggaran = parseFormattedNumber(row.querySelector('.input-anggaran').value);
        const unit = parseFormattedNumber(row.querySelector('.input-unit').value);
        
        const total = anggaran * unit;
        
        row.querySelector('.jumlah-nilai').innerText = formatNumber(total);
        row.querySelector('.jumlah-input').value = total.toFixed(2);
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        
        tableBody.querySelectorAll('.jumlah-input').forEach(input => {
            if (input.closest('.row-os15-data')) {
                 grandTotal += parseFormattedNumber(input.value);
            }
        });

        grandTotalElement.innerText = "RM " + formatNumber(grandTotal);
        masterTotalInput.value = grandTotal.toFixed(2);
    }

    // Pasang Event Listeners pada baris sedia ada
    function attachEventsToRow(row) {
        row.querySelectorAll('.input-anggaran, .input-unit').forEach(input => {
            input.addEventListener('input', function() {
                const r = this.closest('tr');
                calculateRowTotal(r);
                calculateGrandTotal();
            });
        });
        calculateRowTotal(row); // Kiraan awal
    }
    
    // Cari index global tertinggi untuk penamaan input yang unik
    function findMaxIndex() {
        let maxIndex = -1;
        tableBody.querySelectorAll('input[name*="pecahan"]').forEach(input => {
            const name = input.name;
            const match = name.match(/\[(\d+)\]/);
            if (match) {
                maxIndex = Math.max(maxIndex, parseInt(match[1]));
            }
        });
        return maxIndex;
    }


    // Initialize existing static/dynamic rows
    tableBody.querySelectorAll('.row-os15-data').forEach(row => {
        attachEventsToRow(row);
    });
    calculateGrandTotal();

    // ----------------------------------------------------
    // LOGIK TAMBAH BARIS DINAMIK (Add Row Logic)
    // ----------------------------------------------------

    tableBody.addEventListener('click', function(e) {
        // Handle Remove Row button
        if (e.target.closest('.remove-dynamic-row')) {
            if (confirm("Adakah anda pasti mahu memadam baris ini?")) {
                const row = e.target.closest('.row-os15-data');
                row.remove();
                calculateGrandTotal();
            }
        }
    });

    tableBody.addEventListener('click', function(e) {
        // Handle Add Row button
        const addButton = e.target.closest('.add-dynamic-btn');
        if (addButton) {
            const kodOl = addButton.getAttribute('data-kod-ol');
            const butiranDefault = addButton.getAttribute('data-template-butiran');
            
            const newIndex = findMaxIndex() + 1;
            const subKod = 'DYN_' + newIndex; // Sub kod unik untuk baris baru

            const newRowHTML = `
                <tr class="row-os15-data dynamic-item" data-kod-ol="${kodOl}" data-sub-kod="${subKod}">
                    <td class="text-start ps-4">${subKod} - ${butiranDefault}</td>
                    <td><input type="number" step="0.01" name="pecahan[${newIndex}][anggaran]" class="form-control form-control-sm text-end input-anggaran" value="0.00" min="0"></td>
                    <td><input type="number" name="pecahan[${newIndex}][bil_unit]" class="form-control form-control-sm text-center input-unit" value="1" min="0"></td>
                    <td class="text-end fw-bold text-success jumlah-row">RM <span class="jumlah-nilai">0.00</span><input type="hidden" name="pecahan[${newIndex}][jumlah]" class="jumlah-input" value="0.00"></td>
                    <td><input type="text" name="pecahan[${newIndex}][catatan]" class="form-control form-control-sm" placeholder="Catatan per item..."></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-dynamic-row" title="Padam Baris"><i class="fas fa-trash"></i></button></td>
                    <input type="hidden" name="pecahan[${newIndex}][kod_ol]" value="${kodOl}">
                    <input type="hidden" name="pecahan[${newIndex}][sub_kod]" value="${subKod}">
                    <input type="hidden" name="pecahan[${newIndex}][butiran]" value="${butiranDefault}">
                </tr>
            `;

            // Cari baris header untuk kod OL yang sama dan masukkan baris baru selepasnya
            const headerRow = tableBody.querySelector(`.header-row[data-kod-ol="${kodOl}"]`);
            if (headerRow) {
                // Cari baris terakhir di bawah header ini
                let lastItemUnderHeader = headerRow;
                let nextElement = headerRow.nextElementSibling;
                while (nextElement && nextElement.classList.contains('row-os15-data') && nextElement.getAttribute('data-kod-ol') === kodOl) {
                    lastItemUnderHeader = nextElement;
                    nextElement = lastItemUnderHeader.nextElementSibling;
                }
                lastItemUnderHeader.insertAdjacentHTML('afterend', newRowHTML);
            } else {
                 tableBody.insertAdjacentHTML('beforeend', newRowHTML);
            }

            // Dapatkan baris yang baru dimasukkan dan pasang events
            const newRow = tableBody.querySelector(`tr[data-sub-kod="${subKod}"]`);
            if (newRow) attachEventsToRow(newRow);

            calculateGrandTotal();
        }
    });

});
</script>
@endpush
@endsection