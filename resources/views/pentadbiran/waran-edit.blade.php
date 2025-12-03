@extends('layouts.app')

@section('content')

<style>
/* Style khas untuk jadual waran */
.waran-table thead th {
    background-color: #e9ecef;
    border: 1px solid #6c757d;
    vertical-align: middle;
    text-align: center;
    font-size: 0.85rem;
    font-weight: bold;
    text-transform: uppercase;
}
.waran-table tbody td {
    border: 1px solid #dee2e6;
    vertical-align: middle;
    font-size: 0.9rem;
}
.waran-table input[readonly] {
    background-color: #f8f9fa;
    color: #495057;
    cursor: not-allowed;
}
/* Style untuk input kosong */
.input-kosong.text-danger {
    color: #dc3545 !important;
}
.input-kosong.text-success {
    color: #198754 !important;
}
.input-kosong.text-warning {
    color: #ffc107 !important;
}
</style>

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Kemaskini Waran Perjawatan</h3>
            <p class="text-muted small">Kemaskini bilangan pengisian dan catatan untuk setiap jawatan.</p>
        </div>
        <a href="{{ route('dashboard.pentadbiran') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- PENAPIS (FILTER) LANJUTAN --}}
    <div class="card shadow border-0 mb-3">
        <div class="card-body p-3">
            <h6 class="mb-2 fw-bold">Pilihan Paparan (Filter)</h6>
            <div class="btn-group" role="group" aria-label="Waran Filter">
                <button type="button" class="btn btn-primary btn-sm" data-filter="all" id="filter-all">Keseluruhan</button>
                <button type="button" class="btn btn-outline-primary btn-sm" data-filter="persekutuan" id="filter-persekutuan">Lantikan Persekutuan</button>
                <button type="button" class="btn btn-outline-primary btn-sm" data-filter="negeri" id="filter-negeri">Lantikan Negeri</button>
            </div>
        </div>
    </div>

    {{-- BORANG KEMASKINI --}}
    <div class="card shadow border-0">
        <form action="{{ route('pentadbiran.waran.update') }}" method="POST">
            @csrf
            
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm waran-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;">BIL.</th>
                                <th style="width: 35%;">JAWATAN / GRED</th>
                                <th style="width: 10%;">BIL (Waran)</th>
                                {{-- KOLUM BARU UNTUK ISI --}}
                                <th style="width: 10%;">PERSEKUTUAN</th>
                                <th style="width: 10%;">NEGERI</th>
                                {{-- END KOLUM BARU --}}
                                <th style="width: 10%;">KOSONG</th>
                                <th style="width: 20%;">CATATAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($waranData as $index => $waran)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    
                                    {{-- Nama Jawatan --}}
                                    <td class="text-start ps-3 fw-bold text-dark">
                                        {{ $waran->jawatan }}
                                    </td>
                                    
                                    {{-- Kolum BIL (Waran) - Readonly --}}
                                    <td class="text-center">
                                        {{-- Gunakan hidden field untuk ID waran --}}
                                        <input type="hidden" name="waran[{{ $waran->id }}][id]" value="{{ $waran->id }}">
                                        <input type="text" name="bil_waran[]" value="{{ $waran->bil }}" class="form-control form-control-sm text-center fw-bold input-waran" readonly>
                                    </td>
                                    
                                    {{-- KOLUM BARU UNTUK INPUT PERSEKUTUAN --}}
                                    <td class="text-center">
                                        <input type="number" name="waran[{{ $waran->id }}][persekutuan]" value="{{ old("waran.{$waran->id}.persekutuan", $waran->persekutuan) }}" class="form-control form-control-sm text-center input-persekutuan" required min="0">
                                    </td>
                                    
                                    {{-- KOLUM BARU UNTUK INPUT NEGERI --}}
                                    <td class="text-center">
                                        <input type="number" name="waran[{{ $waran->id }}][negeri]" value="{{ old("waran.{$waran->id}.negeri", $waran->negeri) }}" class="form-control form-control-sm text-center input-negeri" required min="0">
                                    </td>
                                    
                                    {{-- Kolum KOSONG (Dikira automatik via JS) --}}
                                    <td class="text-center">
                                        <input type="text" name="waran[{{ $waran->id }}][kosong]" value="{{ $waran->kosong }}" class="form-control form-control-sm text-center fw-bold input-kosong" readonly>
                                    </td>

                                    {{-- Kolum NOTA (Boleh diubah) --}}
                                    <td>
                                        <input type="text" name="waran[{{ $waran->id }}][nota]" value="{{ old("waran.{$waran->id}.nota", $waran->nota) }}" class="form-control form-control-sm" placeholder="Catatan tambahan...">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Tiada data waran dijumpai. Sila jalankan Seeder.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="2" class="text-end fw-bold">JUMLAH KESELURUHAN</td>
                                <td class="text-center fw-bold" id="total-waran">{{ $waranData->sum('bil') }}</td>
                                {{-- UPDATE FOOTER BARU --}}
                                <td class="text-center fw-bold" id="total-persekutuan">{{ $waranData->sum('persekutuan') }}</td>
                                <td class="text-center fw-bold" id="total-negeri">{{ $waranData->sum('negeri') }}</td>
                                {{-- END UPDATE FOOTER BARU --}}
                                <td class="text-center fw-bold" id="total-kosong">{{ $waranData->sum('kosong') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card-footer text-end bg-light">
                <button type="submit" class="btn btn-success shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Kemaskini
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('.waran-table tbody');

    // --- LOGIK KIRAAN ---

    // Fungsi kemaskini nilai Kosong bagi satu baris
    function updateRow(row) {
        const inputWaran = row.querySelector('.input-waran');
        const inputPersekutuan = row.querySelector('.input-persekutuan');
        const inputNegeri = row.querySelector('.input-negeri');
        const inputKosong = row.querySelector('.input-kosong');
        
        const valWaran = parseInt(inputWaran.value) || 0;
        const valPersekutuan = parseInt(inputPersekutuan.value) || 0;
        const valNegeri = parseInt(inputNegeri.value) || 0;
        
        // Logik: Kosong = Waran - (Persekutuan + Negeri)
        const valIsi = valPersekutuan + valNegeri;
        let valKosong = valWaran - valIsi;
        
        // Update input Kosong
        inputKosong.value = valKosong;

        // Tukar warna teks berdasarkan status
        inputKosong.classList.remove('text-danger', 'text-warning', 'text-success');
        if (valKosong > 0) {
            inputKosong.classList.add('text-danger'); // Ada kekosongan
        } else if (valKosong < 0) {
            inputKosong.classList.add('text-warning'); // Terlebih isi
        } else {
            inputKosong.classList.add('text-success'); // Tepat-tepat isi
        }
    }

    // Fungsi kemaskini Jumlah Besar di Footer
    function updateFooter() {
        let sumWaran = 0;
        let sumPersekutuan = 0;
        let sumNegeri = 0;
        let sumKosong = 0;

        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            // Hanya kira baris yang sedang dipaparkan (filter)
            if (row.style.display !== 'none') {
                const w = row.querySelector('.input-waran');
                const p = row.querySelector('.input-persekutuan');
                const n = row.querySelector('.input-negeri');
                const k = row.querySelector('.input-kosong');
                
                if(w && p && n && k) {
                    sumWaran += parseInt(w.value) || 0;
                    sumPersekutuan += parseInt(p.value) || 0;
                    sumNegeri += parseInt(n.value) || 0;
                    sumKosong += parseInt(k.value) || 0;
                }
            }
        });

        document.getElementById('total-waran').innerText = sumWaran;
        document.getElementById('total-persekutuan').innerText = sumPersekutuan;
        document.getElementById('total-negeri').innerText = sumNegeri;
        document.getElementById('total-kosong').innerText = sumKosong;
    }

    // Pasang Event Listener pada setiap input 'Persekutuan' dan 'Negeri'
    const allCountInputs = tableBody.querySelectorAll('.input-persekutuan, .input-negeri');
    
    allCountInputs.forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            updateRow(row);
            updateFooter();
        });
        // Init (masa page load) untuk set Kosong dan warna awal
        updateRow(input.closest('tr'));
    });
    
    // Init footer sekali pada masa page load
    updateFooter(); 

    // --- LOGIK FILTERING ---

    const filterButtons = document.querySelectorAll('.btn-group button');
    const allRows = tableBody.querySelectorAll('tr');

    function applyFilter(filterType) {
        allRows.forEach(row => {
            const inputPersekutuan = row.querySelector('.input-persekutuan');
            const inputNegeri = row.querySelector('.input-negeri');
            
            if (!inputPersekutuan || !inputNegeri) {
                row.style.display = 'none';
                return;
            }

            const persekutuanValue = parseInt(inputPersekutuan.value) || 0;
            const negeriValue = parseInt(inputNegeri.value) || 0;
            let showRow = false;

            if (filterType === 'all') {
                showRow = true;
            } else if (filterType === 'persekutuan') {
                // Tunjukkan baris jika ada lantikan Persekutuan > 0
                showRow = persekutuanValue > 0;
            } else if (filterType === 'negeri') {
                // Tunjukkan baris jika ada lantikan Negeri > 0
                showRow = negeriValue > 0;
            }

            row.style.display = showRow ? '' : 'none';
        });
        
        updateFooter(); // Sentiasa kemaskini footer selepas filtering
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Kemaskini style butang aktif
            filterButtons.forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');

            const filterType = this.getAttribute('data-filter');
            applyFilter(filterType);
        });
    });
    
    // Panggil filter 'all' pada load page
    applyFilter('all');
});

</script>

@endpush