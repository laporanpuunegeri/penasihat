@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Pecahan Perjalanan & Sara Hidup (OS21000) - Tahun {{ $tahun }}</h3>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun' => $tahun]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
    
    <form action="{{ route('pentadbiran.dbus.update_os21000') }}" method="POST">
        @csrf
        <input type="hidden" name="master_id" value="{{ $dbusData->id }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">
        <input type="hidden" name="kod" value="{{ $kod }}">
        
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle os21-table" style="font-size: 0.9rem;">
                        <thead class="table-dark text-center align-middle">
                            <tr>
                                <th style="width: 5%;">BIL</th>
                                <th style="width: 15%;">PERKARA</th>
                                <th style="width: 65%;">ANGGARAN PERUNTUKAN <br><small class="fw-light">(Butiran & Pengiraan)</small></th>
                                <th style="width: 15%;">JUMLAH (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @php
                                $mainCategories = [
                                    ['id' => '1', 'label' => 'Seminar / Kursus / Persidangan', 'suffix' => '_S'],
                                    ['id' => '2', 'label' => 'Tugas Rasmi', 'suffix' => '_R']
                                ];
                                
                                $gredMakan = [
                                    ['l' => 'Gred Utama/Khas A (RM115)', 'r' => 115, 'k' => 'OL21101_GUG'], ['l' => 'Gred Utama/Khas B/C (RM100)', 'r' => 100, 'k' => 'OL21101_GUB'],
                                    ['l' => 'Gred 14-15 (RM85)', 'r' => 85, 'k' => 'OL21101_G14'], ['l' => 'Gred 11-13 (RM60)', 'r' => 60, 'k' => 'OL21101_G11'],
                                    ['l' => 'Gred 9-10 (RM45)', 'r' => 45, 'k' => 'OL21101_G09'], ['l' => 'Gred 5-8 (RM40)', 'r' => 40, 'k' => 'OL21101_G05'],
                                    ['l' => 'Gred 1-4 (RM40)', 'r' => 40, 'k' => 'OL21101_G01'],
                                ];
                                $gredHotel = [
                                    ['l' => 'Gred Utama/Khas A (RM400)', 'r' => 400, 'k' => 'OL21102_GUG'], ['l' => 'Gred Utama/Khas B/C (RM400)', 'r' => 400, 'k' => 'OL21102_GUB'],
                                    ['l' => 'Gred 14-15 (RM350)', 'r' => 350, 'k' => 'OL21102_G14'], ['l' => 'Gred 11-13 (RM145)', 'r' => 145, 'k' => 'OL21102_G11'],
                                    ['l' => 'Gred 9-10 (RM130)', 'r' => 130, 'k' => 'OL21102_G09'], ['l' => 'Gred 5-8 (RM80)', 'r' => 80, 'k' => 'OL21102_G05'],
                                    ['l' => 'Gred 1-4 (RM65)', 'r' => 65, 'k' => 'OL21102_G01'],
                                ];
                                $gredFlight = [
                                    ['l' => 'Gred Utama/Khas B/C (RM650)', 'r' => 650, 'k' => 'OL21106_GUB'],
                                    ['l' => 'Gred 14-15 (RM650)', 'r' => 650, 'k' => 'OL21106_G14'],
                                    ['l' => 'Gred 11-13 (RM650)', 'r' => 650, 'k' => 'OL21106_G11'],
                                    ['l' => 'Gred 9-10 (RM650)', 'r' => 650, 'k' => 'OL21106_G09'],
                                    ['l' => 'Gred 1-8 (RM650)', 'r' => 650, 'k' => 'OL21106_G01'],
                                ];
                            @endphp

                            @foreach($mainCategories as $cat)
                            <tr class="border-bottom-2 border-dark">
                                <td class="text-center fw-bold align-top py-3">{{ $cat['id'] }}</td>
                                <td class="fw-bold align-top py-3">{{ $cat['label'] }}</td>
                                <td class="p-0">
                                    <div class="p-2">
                                        {{-- MAKAN --}}
                                        <div class="fw-bold text-primary mb-1">1. Elaun Makan dan Minuman (OL21101)</div>
                                        <table class="table table-borderless table-sm mb-2">
                                            @foreach($gredMakan as $g)
                                                @php $key = $g['k'] . $cat['suffix']; $p = $pecahanMap[$key] ?? []; @endphp
                                                <tr>
                                                    <td style="width: 35%;" class="small ps-3">{{ $g['l'] }}</td>
                                                    <td style="width: 65%;">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light">RM{{ $g['r'] }} x</span>
                                                            <input type="number" name="data[{{ $key }}][orang]" value="{{ $p['bil_orang'] ?? 0 }}" class="form-control text-center input-orang" data-kadar="{{ $g['r'] }}" min="0">
                                                            <span class="input-group-text bg-light">org x</span>
                                                            <input type="number" name="data[{{ $key }}][hari]" value="{{ $p['bil_hari'] ?? 0 }}" class="form-control text-center input-hari" min="0">
                                                            <span class="input-group-text bg-light">hari</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>

                                        {{-- HOTEL --}}
                                        <div class="fw-bold text-primary mb-1 mt-3">2. Elaun Penginapan (OL21102)</div>
                                        <table class="table table-borderless table-sm mb-2">
                                            @foreach($gredHotel as $g)
                                                @php $key = $g['k'] . $cat['suffix']; $p = $pecahanMap[$key] ?? []; @endphp
                                                <tr>
                                                    <td style="width: 35%;" class="small ps-3">{{ $g['l'] }}</td>
                                                    <td style="width: 65%;">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light">RM{{ $g['r'] }} x</span>
                                                            <input type="number" name="data[{{ $key }}][orang]" value="{{ $p['bil_orang'] ?? 0 }}" class="form-control text-center input-orang" data-kadar="{{ $g['r'] }}" min="0">
                                                            <span class="input-group-text bg-light">org x</span>
                                                            <input type="number" name="data[{{ $key }}][hari]" value="{{ $p['bil_hari'] ?? 0 }}" class="form-control text-center input-hari" min="0">
                                                            <span class="input-group-text bg-light">hari</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>

                                        {{-- TRANS --}}
                                        <div class="fw-bold text-primary mb-1 mt-3">3. Elaun Pengangkutan (OL21104)</div>
                                        @php $key = 'OL21104_TRANS' . $cat['suffix']; $p = $pecahanMap[$key] ?? []; @endphp
                                        <div class="input-group input-group-sm mb-2 ps-3" style="max-width: 95%;">
                                            <span class="input-group-text bg-white border-0 ps-0" style="width: 35%; text-align: left;">Tambang/Bas/Teksi:</span>
                                            <span class="input-group-text bg-light">RM</span>
                                            <input type="number" name="data[{{ $key }}][orang]" value="{{ $p['anggaran'] ?? 0 }}" class="form-control text-center input-kos-trans" min="0" placeholder="Kos Total">
                                            <span class="input-group-text bg-light">x</span>
                                            <input type="number" name="data[{{ $key }}][hari]" value="{{ $p['bil_hari'] ?? 0 }}" class="form-control text-center input-kekerapan-trans" min="0" placeholder="Kekerapan">
                                            <span class="input-group-text bg-light">Kali</span>
                                        </div>

                                        {{-- FLIGHT --}}
                                        <div class="fw-bold text-primary mb-1 mt-3">4. Bayaran Kapal Terbang (OL21106)</div>
                                        <table class="table table-borderless table-sm mb-2">
                                            @foreach($gredFlight as $g)
                                                @php $key = $g['k'] . $cat['suffix']; $p = $pecahanMap[$key] ?? []; @endphp
                                                <tr>
                                                    <td style="width: 35%;" class="small ps-3">{{ $g['l'] }}</td>
                                                    <td style="width: 65%;">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light">RM</span>
                                                            <input type="number" name="data[{{ $key }}][kos]" value="{{ $p['anggaran'] ?? $g['r'] }}" class="form-control text-center input-kos-flight" min="0">
                                                            <span class="input-group-text bg-light">x</span>
                                                            <input type="number" name="data[{{ $key }}][orang]" value="{{ $p['bil_orang'] ?? 0 }}" class="form-control text-center input-orang-flight" min="0">
                                                            <span class="input-group-text bg-light">org x</span>
                                                            <input type="number" name="data[{{ $key }}][hari]" value="{{ $p['bil_hari'] ?? 0 }}" class="form-control text-center input-hari-flight" min="0">
                                                            <span class="input-group-text bg-light">kali</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </td>

                                <td class="align-top py-3 text-end fw-bold">
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($gredMakan as $g)
                                            @php $key = $g['k'] . $cat['suffix']; $p = $pecahanMap[$key] ?? []; @endphp
                                            <div style="height: 31px; padding-top: 4px;">RM <span class="total-baris" id="total-{{ $key }}">{{ number_format($p['jumlah'] ?? 0, 2) }}</span></div>
                                        @endforeach
                                        <div class="mb-4">&nbsp;</div> 
                                        @foreach($gredHotel as $g)
                                            @php $key = $g['k'] . $cat['suffix']; $p = $pecahanMap[$key] ?? []; @endphp
                                            <div style="height: 31px; padding-top: 4px;">RM <span class="total-baris" id="total-{{ $key }}">{{ number_format($p['jumlah'] ?? 0, 2) }}</span></div>
                                        @endforeach
                                        <div class="mb-4">&nbsp;</div> 
                                        @php $kTrans = 'OL21104_TRANS' . $cat['suffix']; $pT = $pecahanMap[$kTrans] ?? []; @endphp
                                        <div style="height: 31px;">RM <span class="total-baris" id="total-{{ $kTrans }}">{{ number_format($pT['jumlah'] ?? 0, 2) }}</span></div>
                                        <div class="mb-4">&nbsp;</div> 
                                        @foreach($gredFlight as $g)
                                            @php $key = $g['k'] . $cat['suffix']; $p = $pecahanMap[$key] ?? []; @endphp
                                            <div style="height: 31px; padding-top: 4px;">RM <span class="total-baris" id="total-{{ $key }}">{{ number_format($p['jumlah'] ?? 0, 2) }}</span></div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="3" class="text-end fw-bold py-2 fs-5">JUMLAH BESAR KESELURUHAN</td>
                                <td class="text-end fw-bold fs-5 py-2 pe-3 text-success bg-dark" id="grand-total">RM {{ number_format($dbusData->jumlah, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light text-end p-3">
                <button type="submit" class="btn btn-success btn-lg shadow"><i class="fas fa-save me-2"></i> Simpan Maklumat</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('.os21-table');
    function formatNumber(num) { return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","); }

    function calculateTotals() {
        let grandTotal = 0;
        
        // 1. MAKAN & HOTEL
        table.querySelectorAll('input.input-orang').forEach(inputOrang => {
            const row = inputOrang.closest('tr');
            const inputHari = row.querySelector('.input-hari');
            if(inputOrang && inputHari) {
                let orang = parseFloat(inputOrang.value) || 0;
                let hari = parseFloat(inputHari.value) || 0;
                let kadar = parseFloat(inputOrang.dataset.kadar);
                const subtotal = orang * hari * kadar;
                grandTotal += subtotal;
                const key = inputOrang.name.match(/\[(.*?)\]/)[1];
                const totalSpan = document.getElementById('total-' + key);
                if(totalSpan) totalSpan.textContent = formatNumber(subtotal);
            }
        });
        
        // 2. TRANS
        table.querySelectorAll('input.input-kos-trans').forEach(inputKos => {
            const row = inputKos.closest('tr');
            const inputKekerapan = row.querySelector('.input-kekerapan-trans');
            if(inputKos && inputKekerapan) {
                let kos = parseFloat(inputKos.value) || 0;
                let kekerapan = parseFloat(inputKekerapan.value) || 0;
                const subtotal = kos * kekerapan;
                grandTotal += subtotal;
                const key = inputKos.name.match(/\[(.*?)\]/)[1];
                const totalSpan = document.getElementById('total-' + key);
                if(totalSpan) totalSpan.textContent = formatNumber(subtotal);
            }
        });

        // 3. FLIGHT
        table.querySelectorAll('input.input-kos-flight').forEach(inputKos => {
            const row = inputKos.closest('tr');
            const inputOrang = row.querySelector('.input-orang-flight');
            const inputKali = row.querySelector('.input-hari-flight');
            if(inputKos && inputOrang && inputKali) {
                let kos = parseFloat(inputKos.value) || 0;
                let orang = parseFloat(inputOrang.value) || 0;
                let kali = parseFloat(inputKali.value) || 0;
                const subtotal = kos * orang * kali;
                grandTotal += subtotal;
                const key = inputKos.name.match(/\[(.*?)\]/)[1];
                const totalSpan = document.getElementById('total-' + key);
                if(totalSpan) totalSpan.textContent = formatNumber(subtotal);
            }
        });

        document.getElementById('grand-total').textContent = 'RM ' + formatNumber(grandTotal);
    }
    table.addEventListener('input', e => { if(e.target.matches('input')) calculateTotals(); });
    calculateTotals();
});
</script>
@endpush
@endsection