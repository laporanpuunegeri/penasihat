@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Pecahan Elaun Pemberian Perpindahan (OS22000) - Tahun {{ $tahun }}</h3>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun' => $tahun]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
    
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-3">
        <i class="fas fa-info-circle fa-2x me-3"></i>
        <div>
            <b>Panduan:</b> Kadar elaun telah ditetapkan mengikut gred dan lokasi (Semenanjung vs Sabah/Sarawak). Sila masukkan <b>Bilangan Orang</b> sahaja.
        </div>
    </div>

    <form action="{{ route('pentadbiran.dbus.update_os22000') }}" method="POST">
        @csrf
        <input type="hidden" name="master_id" value="{{ $dbusData->id }}">
        
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle os22-table" style="font-size: 0.9rem;">
                        <thead class="table-dark text-center align-middle">
                            <tr>
                                <th rowspan="2" style="width: 5%;">BIL</th>
                                <th rowspan="2" style="width: 30%;">PERKARA (GRED)</th>
                                <th colspan="2" style="width: 25%;">BUJANG</th>
                                <th colspan="2" style="width: 25%;">BERKELUARGA</th>
                                <th rowspan="2" style="width: 15%;">JUMLAH (RM)</th>
                            </tr>
                            <tr>
                                <th>Anggaran (RM)</th>
                                <th>Bil. Org</th>
                                <th>Anggaran (RM)</th>
                                <th>Bil. Org</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @php
                                $grades = [
                                    ['l' => 'Gred Utama / Khas A', 'kb' => 'OL22155_GUA_B', 'rb' => 600, 'kr' => 'OL22155_GUA_R', 'rr' => 1200, 'kbs' => 'OL22155_GUA_BS', 'rbs' => 800, 'krs' => 'OL22155_GUA_RS', 'rrs' => 1600],
                                    ['l' => 'Gred Utama / Khas B/C', 'kb' => 'OL22155_GUB_B', 'rb' => 500, 'kr' => 'OL22155_GUB_R', 'rr' => 1000, 'kbs' => 'OL22155_GUB_BS', 'rbs' => 700, 'krs' => 'OL22155_GUB_RS', 'rrs' => 1600],
                                    ['l' => 'Gred 53 dan 54', 'kb' => 'OL22155_G53_B', 'rb' => 500, 'kr' => 'OL22155_G53_R', 'rr' => 1000, 'kbs' => 'OL22155_G53_BS', 'rbs' => 700, 'krs' => 'OL22155_G53_RS', 'rrs' => 1600],
                                    ['l' => 'Gred 45 hingga 52', 'kb' => 'OL22155_G45_B', 'rb' => 450, 'kr' => 'OL22155_G45_R', 'rr' => 900, 'kbs' => 'OL22155_G45_BS', 'rbs' => 650, 'krs' => 'OL22155_G45_RS', 'rrs' => 1300],
                                    ['l' => 'Gred 43 dan 44', 'kb' => 'OL22155_G43_B', 'rb' => 400, 'kr' => 'OL22155_G43_R', 'rr' => 800, 'kbs' => 'OL22155_G43_BS', 'rbs' => 600, 'krs' => 'OL22155_G43_RS', 'rrs' => 1200],
                                    ['l' => 'Gred 41 dan 42', 'kb' => 'OL22155_G41_B', 'rb' => 350, 'kr' => 'OL22155_G41_R', 'rr' => 700, 'kbs' => 'OL22155_G41_BS', 'rbs' => 550, 'krs' => 'OL22155_G41_RS', 'rrs' => 1100],
                                    ['l' => 'Gred 31 hingga 40', 'kb' => 'OL22155_G31_B', 'rb' => 300, 'kr' => 'OL22155_G31_R', 'rr' => 600, 'kbs' => 'OL22155_G31_BS', 'rbs' => 400, 'krs' => 'OL22155_G31_RS', 'rrs' => 800],
                                    ['l' => 'Gred 27 hingga 30', 'kb' => 'OL22155_G27_B', 'rb' => 250, 'kr' => 'OL22155_G27_R', 'rr' => 500, 'kbs' => 'OL22155_G27_BS', 'rbs' => 350, 'krs' => 'OL22155_G27_RS', 'rrs' => 700],
                                    ['l' => 'Gred 21 hingga 26', 'kb' => 'OL22155_G21_B', 'rb' => 200, 'kr' => 'OL22155_G21_R', 'rr' => 400, 'kbs' => 'OL22155_G21_BS', 'rbs' => 300, 'krs' => 'OL22155_G21_RS', 'rrs' => 600],
                                    ['l' => 'Gred 17 hingga 20', 'kb' => 'OL22155_G17_B', 'rb' => 180, 'kr' => 'OL22155_G17_R', 'rr' => 360, 'kbs' => 'OL22155_G17_BS', 'rbs' => 250, 'krs' => 'OL22155_G17_RS', 'rrs' => 500],
                                    ['l' => 'Gred 13 hingga 16', 'kb' => 'OL22155_G13_B', 'rb' => 150, 'kr' => 'OL22155_G13_R', 'rr' => 300, 'kbs' => 'OL22155_G13_BS', 'rbs' => 200, 'krs' => 'OL22155_G13_RS', 'rrs' => 400],
                                    ['l' => 'Gred 1 hingga 12', 'kb' => 'OL22155_G01_B', 'rb' => 100, 'kr' => 'OL22155_G01_R', 'rr' => 200, 'kbs' => 'OL22155_G01_BS', 'rbs' => 150, 'krs' => 'OL22155_G01_RS', 'rrs' => 300],
                                ];
                            @endphp

                            @foreach($grades as $index => $g)
                                
                                {{-- Row 1: Semenanjung (Main Row) --}}
                                @php 
                                    $pb = $pecahanMap[$g['kb']] ?? ['bil_unit' => 0];
                                    $pr = $pecahanMap[$g['kr']] ?? ['bil_unit' => 0];
                                    $hasSabahSarawak = $g['kbs'] != null;
                                @endphp
                                <tr data-row-key="{{ $index }}" data-rate-b="{{ $g['rb'] }}" data-rate-r="{{ $g['rr'] }}">
                                    <td class="text-center align-middle" rowspan="{{ $hasSabahSarawak ? 2 : 1 }}">{{ $index + 1 }}</td>
                                    <td class="align-middle fw-bold ps-3" rowspan="{{ $hasSabahSarawak ? 2 : 1 }}">
                                        {{ $g['l'] }}
                                    </td>
                                    
                                    {{-- BUJANG (SEMENANJUNG) --}}
                                    <td class="align-middle text-end small">RM{{ number_format($g['rb'], 2) }} <div class="text-muted small fw-normal">Semenanjung</div></td>
                                    <td class="align-middle p-1">
                                        {{-- Note: Added ']' to selector logic in JS, so name structure is important --}}
                                        <input type="number" name="data[{{ $g['kb'] }}][orang]" value="{{ $pb['bil_unit'] }}" class="form-control form-control-sm text-center input-bujang" min="0">
                                    </td>
                                    
                                    {{-- BERKELUARGA (SEMENANJUNG) --}}
                                    <td class="align-middle text-end small">RM{{ number_format($g['rr'], 2) }} <div class="text-muted small fw-normal">Semenanjung</div></td>
                                    <td class="align-middle p-1">
                                        <input type="number" name="data[{{ $g['kr'] }}][orang]" value="{{ $pr['bil_unit'] }}" class="form-control form-control-sm text-center input-berkeluarga" min="0">
                                    </td>
                                    
                                    {{-- JUMLAH ROW --}}
                                    <td class="text-end align-middle fw-bold pe-3 bg-light" id="total-row-{{ $index }}" rowspan="{{ $hasSabahSarawak ? 2 : 1 }}">
                                        RM <span class="total-row-display">{{ number_format(($pb['bil_unit'] * $g['rb']) + ($pr['bil_unit'] * $g['rr']), 2) }}</span>
                                    </td>
                                </tr>
                                
                                {{-- Row 2: Sabah/Sarawak (Jika ada data) --}}
                                @if($hasSabahSarawak)
                                    @php 
                                        $pbs = $pecahanMap[$g['kbs']] ?? ['bil_unit' => 0];
                                        $prs = $pecahanMap[$g['krs']] ?? ['bil_unit' => 0];
                                    @endphp
                                    {{-- Class table-light penting untuk JS skip row ini dalam main loop --}}
                                    <tr class="table-light border-top border-muted" data-row-key="{{ $index }}_s" data-rate-b="{{ $g['rbs'] }}" data-rate-r="{{ $g['rrs'] }}">
                                        
                                        {{-- BUJANG (SABAH/SARAWAK) --}}
                                        <td class="align-middle text-end small">RM{{ number_format($g['rbs'], 2) }} <div class="text-muted small fw-normal">Sabah/Sarawak</div></td>
                                        <td class="align-middle p-1">
                                            <input type="number" name="data[{{ $g['kbs'] }}][orang]" value="{{ $pbs['bil_unit'] }}" class="form-control form-control-sm text-center input-bujang" min="0">
                                        </td>
                                        
                                        {{-- BERKELUARGA (SABAH/SARAWAK) --}}
                                        <td class="align-middle text-end small">RM{{ number_format($g['rrs'], 2) }} <div class="text-muted small fw-normal">Sabah/Sarawak</div></td>
                                        <td class="align-middle p-1">
                                            <input type="number" name="data[{{ $g['krs'] }}][orang]" value="{{ $prs['bil_unit'] }}" class="form-control form-control-sm text-center input-berkeluarga" min="0">
                                        </td>
                                        {{-- Jumlah dan Bil di row 1 (Merged) --}}
                                    </tr>
                                @endif
                            @endforeach

                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="6" class="text-end fw-bold py-2 fs-5">JUMLAH KESELURUHAN (OL22155)</td>
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
    const table = document.querySelector('.os22-table');
    
    // Fungsi format duit (ada koma dan 2 tempat perpuluhan)
    function formatNumber(num) { 
        return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
    }

    // Fungsi mengira total untuk SATU blok gred (Semenanjung + Sabah/Sarawak)
    function calculateRow(row) {
        let rowTotal = 0;

        // 1. KIRA ROW UTAMA (SEMENANJUNG)
        const rateB = parseFloat(row.getAttribute('data-rate-b')) || 0;
        const rateR = parseFloat(row.getAttribute('data-rate-r')) || 0;
        
        // Cari input dalam row ini sahaja. 
        // Menggunakan selector yang lebih spesifik (input name berakhir dengan _B] atau _R])
        const inputB_sem = row.querySelector('input[name*="_B]"][name*="orang"]');
        const inputR_sem = row.querySelector('input[name*="_R]"][name*="orang"]');
        
        const orgB_sem = inputB_sem ? (parseInt(inputB_sem.value) || 0) : 0;
        const orgR_sem = inputR_sem ? (parseInt(inputR_sem.value) || 0) : 0;
        
        rowTotal += (orgB_sem * rateB) + (orgR_sem * rateR);

        // 2. KIRA ROW ANAK (SABAH/SARAWAK) - Jika wujud
        const nextRow = row.nextElementSibling;
        
        // Pastikan nextRow wujud DAN ia adalah row Sabah/Sarawak (ada class table-light)
        if (nextRow && nextRow.classList.contains('table-light')) {
            const rateBs = parseFloat(nextRow.getAttribute('data-rate-b')) || 0;
            const rateRs = parseFloat(nextRow.getAttribute('data-rate-r')) || 0;
            
            // Selector berakhir dengan _BS] atau _RS]
            const inputBs = nextRow.querySelector('input[name*="_BS]"][name*="orang"]');
            const inputRs = nextRow.querySelector('input[name*="_RS]"][name*="orang"]');
            
            const orgBs = inputBs ? (parseInt(inputBs.value) || 0) : 0;
            const orgRs = inputRs ? (parseInt(inputRs.value) || 0) : 0;

            rowTotal += (orgBs * rateBs) + (orgRs * rateRs);
        }

        // Update paparan Jumlah Row (hanya wujud di Row Utama)
        const displayTotal = row.querySelector('.total-row-display');
        if (displayTotal) {
            displayTotal.textContent = formatNumber(rowTotal);
        }
        
        return rowTotal;
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        
        // *** PEMBAIKAN UTAMA ***
        // Select hanya row yang mempunyai data-row-key TAPI BUKAN row child (.table-light)
        // Ini mengelakkan loop cuba proses row Sabah/Sarawak secara berasingan (yang tiada column total)
        const mainRows = table.querySelectorAll('tbody tr[data-row-key]:not(.table-light)');
        
        mainRows.forEach(mainRow => {
            grandTotal += calculateRow(mainRow);
        });
        
        document.getElementById('grand-total').textContent = 'RM ' + formatNumber(grandTotal);
    }

    // Event Listener untuk input
    table.addEventListener('input', function(e) {
        // Hanya trigger jika input number berubah
        if(e.target.matches('input[type="number"]')) {
            updateGrandTotal();
        } 
    });
    
    // Kira sekali masa page load
    updateGrandTotal();
});
</script>
@endpush
@endsection