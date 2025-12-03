@extends('layouts.app')

@section('content')
<style>
    /* Style untuk kecantikan borang */
    .form-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden; }
    .form-section-title { color: #1e293b; font-weight: 700; font-size: 1rem; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-label { font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; }
    .suku-input-group { background-color: #f8f9fa; border-radius: 8px; padding: 15px; }
    .suku-input-group label { font-size: 0.75rem; }
</style>

<div class="container-fluid py-4">

    {{-- HEADER & BACK BUTTON --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Daftar Prestasi PPUUN Tahun {{ $tahun }}</h3>
        <a href="{{ route('pentadbiran.laporan_prestasi.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Laporan
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- Borang Utama --}}
            <form action="{{ route('pentadbiran.laporan_prestasi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                {{-- CARD UTAMA --}}
                <div class="form-card p-5 mb-4">
                    <h5 class="form-section-title text-primary"><i class="fas fa-bullseye me-2"></i> Maklumat Outcome & KPI</h5>

                    {{-- 1. PILIH OUTCOME --}}
                    <div class="mb-4">
                        <label for="outcome_id" class="form-label">Pilih Outcome</label>
                        <select name="outcome_id" id="outcome_id" class="form-select form-select-lg @error('outcome_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Outcome Program --</option>
                            
                            {{-- OUTCOME 1: KHIDMAT NASIHAT --}}
                            <option value="OUTCOME 1" 
                                data-labels="Nasihat Undang-Undang;Nasihat Syariah;Perjanjian" 
                                {{ old('outcome_id') == 'OUTCOME 1' ? 'selected' : '' }}>
                                OUTCOME 1: Khidmat Nasihat Perundangan Yang Cekap Dan Teratur Kepada Kerajaan Negeri
                            </option>

                            {{-- OUTCOME 2: KES SIVIL --}}
                            <option value="OUTCOME 2" 
                                data-labels="Kes Sivil;Kes Pengambilan Tanah;Kes Rayuan Sivil;Kes Rayuan Pengambilan Tanah" 
                                {{ old('outcome_id') == 'OUTCOME 2' ? 'selected' : '' }}>
                                OUTCOME 2: Pengendalian Kes Sivil Kerajaan Negeri Yang Cekap Dan Teratur
                            </option>
                            
                            {{-- OUTCOME 3: PENGGUBALAN --}}
                            <option value="OUTCOME 3" 
                                data-labels="Penggubalan Perundangan utama dan subsidiari;Semakan dan cetakan semula undang-undang" 
                                {{ old('outcome_id') == 'OUTCOME 3' ? 'selected' : '' }}>
                                OUTCOME 3: Penggubalan Semakan Dan Pencetakan Semula Enakmen Dan Rang Undang-Undang Subsidiari Yang Cekap Dan Teratur
                            </option>
                            
                        </select>
                        @error('outcome_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- 2. KPI & SASARAN --}}
                    <div class="row mb-4 g-3">
                        <div class="col-md-9">
                            <label for="kpi_desc" class="form-label">Penerangan KPI</label>
                            <textarea name="kpi_desc" id="kpi_desc" class="form-control @error('kpi_desc') is-invalid @enderror" rows="2" required>{{ old('kpi_desc', 'Peratusan nasihat perundangan diselesaikan dalam tempoh yang ditetapkan') }}</textarea>
                            @error('kpi_desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="sasaran" class="form-label">Sasaran Tahunan (%)</label>
                            <input type="number" name="sasaran" id="sasaran" class="form-control @error('sasaran') is-invalid @enderror" value="{{ old('sasaran', 90) }}" required>
                            @error('sasaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- 3. PENCAPAIAN SUKU TAHUN --}}
                    <h5 class="form-section-title mt-5 text-success"><i class="fas fa-tasks me-2"></i> Pencapaian Suku Tahun (Nilai Sebenar)</h5>
                    
                    <div class="suku-input-group row mb-4 g-3">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="col-md-3">
                                <label for="suku{{ $i }}" class="form-label">Suku {{ $i }} ({{ $i * 25 }}%)</label>
                                <input type="number" name="suku[]" id="suku{{ $i }}" class="form-control" value="{{ old('suku.' . ($i-1), 0) }}" required>
                            </div>
                        @endfor
                    </div>

                    {{-- 4. BAHAGIAN CATATAN (DYNAMIC) --}}
                    <h5 class="form-section-title mt-5 text-info"><i class="fas fa-edit me-2"></i> Data Catatan (Penerangan Rujukan)</h5>
                    <div id="catatan_container" class="suku-input-group">
                        <p class="text-muted mb-0">Sila pilih Outcome di atas untuk memaparkan bidang Catatan (Contoh: Nasihat UU - 55).</p>
                    </div>

                </div>

                {{-- FOOTER / SUBMIT BUTTON --}}
                <div class="d-flex justify-content-end mt-3 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg shadow fw-bold px-5">
                        <i class="fas fa-save me-2"></i> Simpan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const outcomeSelect = document.getElementById('outcome_id');
    const catatanContainer = document.getElementById('catatan_container');
    
    // Data dummy untuk 'old' input fields jika ada error validasi
    const oldInputValues = @json(old('catatan_val', [])); 

    // Fungsi untuk render input Catatan berdasarkan Outcome yang dipilih
    function renderCatatanFields(selectedOption) {
        const labelsData = selectedOption.getAttribute('data-labels');
        if (!labelsData) {
            catatanContainer.innerHTML = `<p class="text-muted mb-0">Sila pilih Outcome di atas untuk memaparkan bidang Catatan.</p>`;
            return;
        }

        const labels = labelsData.split(';');
        let html = '';

        labels.forEach((label, index) => {
            const fieldId = `catatan_${index}`;
            const oldValue = oldInputValues[index] || ''; 

            html += `
            <div class="row mb-3 align-items-center">
                <div class="col-md-7">
                    <label for="${fieldId}" class="form-label">${label}</label>
                </div>
                <div class="col-md-5">
                    <input type="text" name="catatan_val[${index}]" id="${fieldId}" 
                        class="form-control" placeholder="Nilai Sebenar" value="${oldValue}">
                    <div class="form-text text-muted small">Cth: 55, atau 20 (G) + 3 (S) = 23</div>
                </div>
            </div>`;
        });

        catatanContainer.innerHTML = html;
    }

    // Event Listener apabila OUTCOME berubah
    if (outcomeSelect) {
        outcomeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            renderCatatanFields(selectedOption);
        });

        // Load fields semasa page load (jika ada validation error atau old data)
        const selectedId = outcomeSelect.value;
        if (selectedId) {
            const selectedOption = outcomeSelect.querySelector(`option[value="${selectedId}"]`);
            if (selectedOption) {
                renderCatatanFields(selectedOption);
            }
        }
    }
});
</script>
@endpush