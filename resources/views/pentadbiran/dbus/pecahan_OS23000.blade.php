@extends('layouts.app')

@section('content')

{{-- STYLE UNTUK TAB --}}
<style>
    .nav-tabs .nav-link {
        background-color: #f1f3f5; 
        color: #6c757d;
        border: 1px solid #dee2e6;
        margin-right: 5px;
        font-weight: bold;
    }
    .nav-tabs .nav-link:hover {
        background-color: #e9ecef;
        color: #000;
    }
    .nav-tabs .nav-link.active {
        background-color: #0d6efd !important; /* Warna Biru */
        color: white !important;
        border-color: #0d6efd !important;
        box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
    }
    .nav-tabs .nav-link.active i {
        color: white !important;
    }
    
    /* Warna Input Readonly supaya nampak beza */
    input[readonly] {
        background-color: #e9ecef !important;
        color: #495057;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Pecahan Perhubungan & Utiliti (OS23000) - Tahun {{ $tahun }}</h3>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun' => $tahun]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
    
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-3">
        <i class="fas fa-info-circle fa-2x me-3"></i>
        <div>
            <b>Panduan:</b> Sila isi maklumat mengikut tab kategori di bawah. Jumlah keseluruhan akan dikira secara automatik.
        </div>
    </div>

    {{-- FORM UTAMA (ID: form-kira) --}}
    <form id="form-kira" action="{{ route('pentadbiran.dbus.update_os23000') }}" method="POST">
        @csrf
        <input type="hidden" name="master_id" value="{{ $dbusData->id }}">
        
        {{-- TAB NAVIGATION --}}
        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="pos-tab" data-bs-toggle="tab" data-bs-target="#pos" type="button" role="tab">
                    <i class="fas fa-envelope me-2"></i> POS (OL23101)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="kom-tab" data-bs-toggle="tab" data-bs-target="#kom" type="button" role="tab">
                    <i class="fas fa-phone me-2"></i> KOMUNIKASI (OL23102/3)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="util-tab" data-bs-toggle="tab" data-bs-target="#util" type="button" role="tab">
                    <i class="fas fa-bolt me-2"></i> UTILITI (OL23200)
                </button>
            </li>
        </ul>

        {{-- TAB CONTENT --}}
        <div class="tab-content" id="myTabContent">
            
            {{-- TAB 1: POS --}}
            <div class="tab-pane fade show active" id="pos" role="tabpanel">
                <div class="card shadow border-0 mb-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0 align-middle os23-table">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th style="width: 5%;">BIL</th>
                                        <th style="width: 40%;">PERKARA</th>
                                        <th style="width: 10%;">KUANTITI</th>
                                        <th style="width: 10%;">UNIT</th>
                                        <th style="width: 10%;">BULAN</th>
                                        <th style="width: 15%;">KOS SEUNIT (RM)</th>
                                        <th style="width: 10%;">JUMLAH (RM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $countPos = 1; @endphp
                                    @foreach($itemsPos as $group => $subs)
                                        <tr class="bg-secondary text-white fw-bold"><td colspan="7" class="ps-3">{{ $group }}</td></tr>
                                        @foreach($subs as $item)
                                            @php 
                                                $data = $pecahanMap[$item['sub']] ?? null;
                                                $qty = $data ? $data['kuantiti'] : 0;
                                                $bulan = $data ? $data['bil_bulan'] : 12;
                                                $kos = $data ? $data['anggaran_sebulan'] : 0;
                                                $jumlah = $qty * $bulan * $kos;
                                            @endphp
                                            <tr class="item-row">
                                                <td class="text-center">{{ $countPos++ }}</td>
                                                <td>{{ $item['butiran'] }}
                                                    <input type="hidden" name="data[{{ $item['sub'] }}][butiran]" value="{{ $item['butiran'] }}">
                                                    <input type="hidden" name="data[{{ $item['sub'] }}][kod_ol]" value="{{ $item['kod_ol'] }}">
                                                </td>
                                                <td><input type="number" name="data[{{ $item['sub'] }}][kuantiti]" value="{{ $qty }}" class="form-control form-control-sm text-center input-qty" min="0"></td>
                                                <td><input type="text" name="data[{{ $item['sub'] }}][unit]" value="{{ $item['unit'] }}" class="form-control form-control-sm text-center" readonly></td>
                                                <td><input type="number" name="data[{{ $item['sub'] }}][bulan]" value="{{ $bulan }}" class="form-control form-control-sm text-center input-bulan" min="0"></td>
                                                <td><input type="number" step="0.01" name="data[{{ $item['sub'] }}][anggaran]" value="{{ $kos }}" class="form-control form-control-sm text-end input-kos" min="0"></td>
                                                <td class="text-end fw-bold row-total">RM {{ number_format($jumlah, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: KOMUNIKASI --}}
            <div class="tab-pane fade" id="kom" role="tabpanel">
                <div class="card shadow border-0 mb-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0 align-middle os23-table">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th style="width: 5%;">BIL</th>
                                        <th style="width: 40%;">PERKARA</th>
                                        <th style="width: 10%;">KUANTITI</th>
                                        <th style="width: 10%;">UNIT</th>
                                        <th style="width: 10%;">BULAN</th>
                                        <th style="width: 15%;">KADAR (RM)</th>
                                        <th style="width: 10%;">JUMLAH (RM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $countKom = 1; @endphp
                                    @foreach($itemsKom as $group => $subs)
                                        <tr class="bg-secondary text-white fw-bold"><td colspan="7" class="ps-3">{{ $group }}</td></tr>
                                        @foreach($subs as $item)
                                            @php 
                                                $data = $pecahanMap[$item['sub']] ?? null;
                                                $qty = $data ? $data['kuantiti'] : 0;
                                                $bulan = $data ? $data['bil_bulan'] : 12;
                                                $kos = $data ? $data['anggaran_sebulan'] : 0;
                                                $jumlah = $qty * $bulan * $kos;
                                            @endphp
                                            <tr class="item-row">
                                                <td class="text-center">{{ $countKom++ }}</td>
                                                <td>{{ $item['butiran'] }}
                                                    <input type="hidden" name="data[{{ $item['sub'] }}][butiran]" value="{{ $item['butiran'] }}">
                                                    <input type="hidden" name="data[{{ $item['sub'] }}][kod_ol]" value="{{ $item['kod_ol'] }}">
                                                </td>
                                                <td><input type="number" name="data[{{ $item['sub'] }}][kuantiti]" value="{{ $qty }}" class="form-control form-control-sm text-center input-qty" min="0"></td>
                                                <td><input type="text" name="data[{{ $item['sub'] }}][unit]" value="{{ $item['unit'] }}" class="form-control form-control-sm text-center" readonly></td>
                                                <td><input type="number" name="data[{{ $item['sub'] }}][bulan]" value="{{ $bulan }}" class="form-control form-control-sm text-center input-bulan" min="0"></td>
                                                <td><input type="number" step="0.01" name="data[{{ $item['sub'] }}][anggaran]" value="{{ $kos }}" class="form-control form-control-sm text-end input-kos" min="0"></td>
                                                <td class="text-end fw-bold row-total">RM {{ number_format($jumlah, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: UTILITI --}}
            <div class="tab-pane fade" id="util" role="tabpanel">
                <div class="card shadow border-0 mb-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0 align-middle os23-table">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th style="width: 5%;">BIL</th>
                                        <th style="width: 45%;">PERKARA</th>
                                        <th style="width: 15%;">BULAN PENGGUNAAN</th>
                                        <th style="width: 20%;">KADAR SEBULAN (RM)</th>
                                        <th style="width: 15%;">JUMLAH (RM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $countUtil = 1; @endphp
                                    @foreach($itemsUtil as $groupCode => $subs)
                                        <tr class="bg-secondary text-white fw-bold"><td colspan="5" class="ps-3">{{ $groupCode }}</td></tr>
                                        @foreach($subs as $item)
                                            @php 
                                                $data = $pecahanMap[$item['sub']] ?? null;
                                                $bulan = $data ? $data['bil_bulan'] : 12;
                                                $kos = $data ? $data['anggaran_sebulan'] : 0;
                                                // Utiliti: Kuantiti sentiasa 1
                                                $jumlah = 1 * $bulan * $kos; 
                                            @endphp
                                            <tr class="item-row">
                                                <td class="text-center">{{ $countUtil++ }}</td>
                                                <td>{{ $item['butiran'] }}
                                                    <input type="hidden" name="data[{{ $item['sub'] }}][butiran]" value="{{ $item['butiran'] }}">
                                                    <input type="hidden" name="data[{{ $item['sub'] }}][kod_ol]" value="{{ $item['kod_ol'] }}">
                                                    
                                                    {{-- PENTING: Hidden Kuantiti = 1 untuk Utiliti supaya pengiraan JS tak error --}}
                                                    <input type="hidden" name="data[{{ $item['sub'] }}][kuantiti]" value="1" class="input-qty">
                                                </td>
                                                <td><input type="number" name="data[{{ $item['sub'] }}][bulan]" value="{{ $bulan }}" class="form-control form-control-sm text-center input-bulan" min="0"></td>
                                                <td><input type="number" step="0.01" name="data[{{ $item['sub'] }}][anggaran]" value="{{ $kos }}" class="form-control form-control-sm text-end input-kos" min="0"></td>
                                                <td class="text-end fw-bold row-total">RM {{ number_format($jumlah, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- GRAND TOTAL FOOTER --}}
        <div class="card bg-dark text-white border-0 shadow mt-3 sticky-bottom" style="z-index: 1020;">
            <div class="card-body d-flex justify-content-between align-items-center p-3">
                <h4 class="mb-0 fw-bold">JUMLAH KESELURUHAN (OS23000)</h4>
                <h3 class="mb-0 text-success fw-bold" id="grand-total">RM {{ number_format($dbusData->jumlah, 2) }}</h3>
            </div>
            <div class="card-footer bg-secondary text-end p-3">
                <button type="submit" class="btn btn-success btn-lg shadow fw-bold"><i class="fas fa-save me-2"></i> SIMPAN SEMUA TAB</button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Format Nombor (Koma & 2 Perpuluhan)
    function formatNumber(num) { 
        return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
    }

    // 2. Kira Setiap Baris
    function calculateRow(row) {
        // Cari input dalam baris tersebut
        const qtyInput = row.querySelector('.input-qty');
        const bulanInput = row.querySelector('.input-bulan');
        const kosInput = row.querySelector('.input-kos');
        const totalDisplay = row.querySelector('.row-total');

        // Pastikan nilai default adalah 0 jika kosong
        let qty = qtyInput ? parseFloat(qtyInput.value) : 1; // Default 1 jika input tak wujud (failsafe)
        if (isNaN(qty)) qty = 0;

        let bulan = bulanInput ? parseFloat(bulanInput.value) : 0;
        if (isNaN(bulan)) bulan = 0;

        let kos = kosInput ? parseFloat(kosInput.value) : 0;
        if (isNaN(kos)) kos = 0;

        // FORMULA: Kuantiti x Bulan x Kos
        const total = qty * bulan * kos;

        // Update Text Baris
        if (totalDisplay) {
            totalDisplay.textContent = 'RM ' + formatNumber(total);
        }

        return total;
    }

    // 3. Kira Jumlah Keseluruhan (Grand Total)
    function updateGrandTotal() {
        let grandTotal = 0;
        
        // Loop SEMUA baris dalam SEMUA tab
        // Kita guna document.querySelectorAll supaya ia ambil semua walaupun hidden
        const allRows = document.querySelectorAll('.item-row');
        
        allRows.forEach(row => {
            grandTotal += calculateRow(row);
        });

        // Update Paparan Bawah
        const grandTotalElement = document.getElementById('grand-total');
        if (grandTotalElement) {
            grandTotalElement.textContent = 'RM ' + formatNumber(grandTotal);
        }
    }

    // 4. Pasang Event Listener
    const form = document.getElementById('form-kira');
    if (form) {
        form.addEventListener('input', function(e) {
            // Check jika yang diubah adalah salah satu input kiraan
            if (e.target.classList.contains('input-qty') || 
                e.target.classList.contains('input-bulan') || 
                e.target.classList.contains('input-kos')) {
                
                // Kira baris yang disentuh dahulu
                const row = e.target.closest('.item-row');
                calculateRow(row);

                // Kemudian kira jumlah besar
                updateGrandTotal();
            }
        });
        
        // Debugging
        console.log("Sistem Pengiraan Auto Aktif");
    }

    // 5. Jalankan Pengiraan Awal (Untuk data dari Database)
    // Gunakan timeout sedikit untuk pastikan DOM sudah stabil
    setTimeout(updateGrandTotal, 100);
});
</script>
@endpush
@endsection