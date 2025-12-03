{{-- resources/views/pentadbiran/dbus/pecahan_OS14000.blade.php --}}

@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Pecahan Bayaran Lebih Masa (OS14000) - Tahun {{ $dbusData->tahun }}</h3>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun' => $dbusData->tahun]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="alert alert-info border-0 shadow-sm">
        <i class="fas fa-info-circle me-2"></i> 
        Sila masukkan anggaran peruntukan, bilangan pegawai, dan bilangan bulan bagi setiap Gred. Jumlah keseluruhan akan mengemas kini rekod **OL14101** secara automatik.
    </div>
    
    {{-- Action borang ke route update yang betul --}}
    <form action="{{ route('pentadbiran.dbus.update_ol14101') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $dbusData->id }}">
        
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle text-center" style="font-size: 0.9rem;">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 25%">Gred</th>
                                <th style="width: 15%">Anggaran (RM)</th>
                                <th style="width: 15%">Bil. Pegawai (Orang)</th>
                                <th style="width: 15%">Bil. Bulan</th>
                                <th style="width: 20%">JUMLAH PERUNTUKAN (RM)</th>
                                <th style="width: 10%">Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-ot">
                            @php
                                // Data default berdasarkan OS14.pdf (digunakan jika DB kosong)
                                $defaultGrades = [
                                    ['gred' => 'Gred 7 hingga 8', 'anggaran' => 0.00, 'orang' => 0, 'bulan' => 0, 'catatan' => 'Gred 7 hingga 8'],
                                    ['gred' => 'Gred 5 hingga 6', 'anggaran' => 110.00, 'orang' => 5, 'bulan' => 5, 'catatan' => 'Gred 5 hingga 6'],
                                    ['gred' => 'Gred 4', 'anggaran' => 0.00, 'orang' => 0, 'bulan' => 0, 'catatan' => 'Gred 4'],
                                    ['gred' => 'Gred 3', 'anggaran' => 0.00, 'orang' => 1, 'bulan' => 12, 'catatan' => 'Gred 3'],
                                    ['gred' => 'Gred 2', 'anggaran' => 110.00, 'orang' => 10, 'bulan' => 12, 'catatan' => 'Gred 2'],
                                    ['gred' => 'Gred 1', 'anggaran' => 180.00, 'orang' => 5, 'bulan' => 12, 'catatan' => 'Gred 1'],
                                ];
                                
                                // Jika $pecahanData kosong, guna default. Jika ada, Model akan diutamakan.
                                $gradesToUse = $pecahanData->keyBy('gred')->toArray();
                            @endphp

                            @foreach($defaultGrades as $index => $default)
                                @php
                                    $gredKey = $default['gred'];
                                    
                                    // Ambil data dari DB jika wujud, jika tidak, guna default
                                    $data = $gradesToUse[$gredKey] ?? $default;

                                    $anggaran = $data['anggaran'] ?? $data['anggaran'];
                                    $orang = $data['bil_orang'] ?? $data['orang'];
                                    $bulan = $data['bil_bulan'] ?? $data['bulan'];
                                    $catatan = $data['catatan'] ?? $data['catatan'];
                                    
                                    // Kira jumlah: Anggaran * Orang * Bulan
                                    $jumlah = $anggaran * $orang * $bulan;
                                @endphp
                                <tr class="row-ot-data">
                                    <td class="text-start fw-bold">{{ $gredKey }}</td>
                                    <td>
                                        <input type="number" step="0.01" name="pecahan[{{ $index }}][anggaran]" 
                                               class="form-control form-control-sm text-end input-anggaran" 
                                               value="{{ old("pecahan.{$index}.anggaran", $anggaran) }}" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="pecahan[{{ $index }}][orang]" 
                                               class="form-control form-control-sm text-center input-orang" 
                                               value="{{ old("pecahan.{$index}.orang", $orang) }}" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="pecahan[{{ $index }}][bulan]" 
                                               class="form-control form-control-sm text-center input-bulan" 
                                               value="{{ old("pecahan.{$index}.bulan", $bulan) }}" min="0">
                                    </td>
                                    <td class="text-end fw-bold text-success jumlah-row">
                                        RM <span class="jumlah-nilai">{{ number_format($jumlah, 2) }}</span>
                                        <input type="hidden" name="pecahan[{{ $index }}][jumlah]" class="jumlah-input" value="{{ $jumlah }}">
                                    </td>
                                    <td>
                                        <input type="text" name="pecahan[{{ $index }}][catatan]" 
                                               class="form-control form-control-sm" 
                                               value="{{ old("pecahan.{$index}.catatan", $catatan) }}" 
                                               placeholder="Catatan Gred...">
                                    </td>
                                    <input type="hidden" name="pecahan[{{ $index }}][gred]" value="{{ $gredKey }}">
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">JUMLAH KESELURUHAN OS14000</td>
                                <td class="text-end fw-bold fs-5" id="grand-total">RM {{ number_format($dbusData->jumlah, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card-footer text-end bg-light">
                <button type="submit" class="btn btn-success shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Pecahan & Kemaskini
                </button>
                {{-- Hidden field untuk Grand Total dari JS --}}
                <input type="hidden" name="master_grand_total" id="master-grand-total" value="{{ $dbusData->jumlah }}">
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('tbody-ot');
    const grandTotalElement = document.getElementById('grand-total');
    const masterTotalInput = document.getElementById('master-grand-total');

    function formatNumber(num) { 
        return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
    }
    function parseFormattedNumber(str) { 
        // Menggunakan regex yang lebih robust untuk mengendalikan nilai yang mungkin datang dari DB
        const cleaned = str.toString().replace(/[^0-9.-]+/g,"");
        return parseFloat(cleaned) || 0; 
    }

    function calculateRowTotal(row) {
        const anggaran = parseFloat(row.querySelector('.input-anggaran').value) || 0;
        const orang = parseInt(row.querySelector('.input-orang').value) || 0;
        const bulan = parseInt(row.querySelector('.input-bulan').value) || 0;
        
        const total = anggaran * orang * bulan;
        
        row.querySelector('.jumlah-nilai').innerText = formatNumber(total);
        row.querySelector('.jumlah-input').value = total.toFixed(2);
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        
        tableBody.querySelectorAll('.row-ot-data').forEach(row => {
            const totalInput = row.querySelector('.jumlah-input');
            if (totalInput) {
                // Parse nilai dari hidden input
                grandTotal += parseFormattedNumber(totalInput.value);
            }
        });

        // Update paparan Grand Total (RM X,XXX.XX)
        grandTotalElement.innerText = "RM " + formatNumber(grandTotal);
        
        // Update hidden field yang dihantar ke controller
        masterTotalInput.value = grandTotal.toFixed(2);
    }

    // Pasang Event Listeners pada setiap input yang mempengaruhi kiraan
    tableBody.querySelectorAll('input').forEach(input => {
        if (input.name.includes('[anggaran]') || input.name.includes('[orang]') || input.name.includes('[bulan]')) {
            input.addEventListener('input', function() {
                const row = this.closest('tr');
                calculateRowTotal(row);
                calculateGrandTotal();
            });
        }
    });

    // Jalankan kiraan awal apabila page dimuatkan
    tableBody.querySelectorAll('.row-ot-data').forEach(row => {
        calculateRowTotal(row);
    });
    calculateGrandTotal();
    
    // Pastikan nilai dihantar sebagai float
    document.querySelector('form').addEventListener('submit', function() {
        calculateGrandTotal(); 
    });
});
</script>
@endpush
@endsection