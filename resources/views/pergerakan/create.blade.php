@extends('layouts.app')

@section('title', 'Daftar Pergerakan Pegawai')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Daftar Pergerakan Pegawai</h1>
                <a href="{{ route('pergerakan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-4 border-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- KAD BORANG --}}
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-file-alt me-2"></i> Borang Permohonan Baru</h6>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('pergerakan.store') }}" method="POST">
                        @csrf
                        
                        {{-- 1. JENIS PERGERAKAN --}}
                        <div class="mb-4">
                            <label for="jenis" class="form-label fw-bold text-muted">
                                <i class="fas fa-tag me-1"></i> Jenis Pergerakan
                            </label>
                            <select name="jenis" id="jenis" class="form-select form-select-lg @error('jenis') is-invalid @enderror" required>
                                <option value="">-- Sila Pilih --</option>
                                <option value="Kursus" {{ old('jenis') == 'Kursus' ? 'selected' : '' }}>Kursus</option>
                                <option value="Seminar" {{ old('jenis') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                                <option value="Bengkel" {{ old('jenis') == 'Bengkel' ? 'selected' : '' }}>Bengkel</option>
                                <option value="Forum" {{ old('jenis') == 'Forum' ? 'selected' : '' }}>Forum</option>
                                <option value="Bertugas Di Luar Pejabat" {{ old('jenis') == 'Bertugas Di Luar Pejabat' ? 'selected' : '' }}>Bertugas Di Luar Pejabat</option>
                                <option value="Mesyuarat Dalaman" {{ old('jenis') == 'Mesyuarat Dalaman' ? 'selected' : '' }}>Mesyuarat Dalaman</option>
                                <option value="Mesyuarat Luar Pejabat" {{ old('jenis') == 'Mesyuarat Luar Pejabat' ? 'selected' : '' }}>Mesyuarat Luar Pejabat</option>
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. TARIKH & TEMPOH (DIKEMASKINI: DIBAGI KEPADA 4 LAJUR) --}}
                        <div class="row mb-4">
                            
                            {{-- Tarikh Mula --}}
                            <div class="col-md-3">
                                <label for="tarikh_mula" class="form-label fw-bold text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> Tarikh Mula
                                </label>
                                <input type="date" name="tarikh_mula" id="tarikh_mula" class="form-control @error('tarikh_mula') is-invalid @enderror" value="{{ old('tarikh_mula') }}" required>
                                @error('tarikh_mula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Masa Mula (BARU) --}}
                            <div class="col-md-3">
                                <label for="masa_mula" class="form-label fw-bold text-muted">
                                    <i class="far fa-clock me-1"></i> Masa Mula
                                </label>
                                <input type="time" name="masa_mula" id="masa_mula" class="form-control @error('masa_mula') is-invalid @enderror" value="{{ old('masa_mula') }}">
                                @error('masa_mula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Tarikh Akhir --}}
                            <div class="col-md-3">
                                <label for="tarikh_akhir" class="form-label fw-bold text-muted">
                                    <i class="far fa-calendar-check me-1"></i> Tarikh Akhir
                                </label>
                                <input type="date" name="tarikh_akhir" id="tarikh_akhir" class="form-control bg-white @error('tarikh_akhir') is-invalid @enderror" value="{{ old('tarikh_akhir') }}" required> 
                                @error('tarikh_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Masa Akhir (BARU) --}}
                            <div class="col-md-3">
                                <label for="masa_akhir" class="form-label fw-bold text-muted">
                                    <i class="far fa-clock me-1"></i> Masa Akhir
                                </label>
                                <input type="time" name="masa_akhir" id="masa_akhir" class="form-control @error('masa_akhir') is-invalid @enderror" value="{{ old('masa_akhir') }}">
                                @error('masa_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        
                        {{-- TEMPOH HARI (Diasingkan dari baris atas) --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="tempoh" class="form-label fw-bold text-muted">
                                    <i class="fas fa-hourglass-half me-1"></i> Tempoh (Hari)
                                </label>
                                <div class="input-group">
                                    <input type="number" id="tempoh" class="form-control" placeholder="0" min="1">
                                    <span class="input-group-text bg-light">Hari</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.8rem;">*Masukkan jumlah hari</small>
                            </div>
                        </div>

                        
                        {{-- 3. JENIS KENDERAAN --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted d-block mb-3">
                                <i class="fas fa-car me-1"></i> Jenis Kenderaan
                            </label>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="kenderaan" id="kenderaan_sendiri" value="Kenderaan Sendiri" required {{ old('kenderaan') == 'Kenderaan Sendiri' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 p-3 d-flex align-items-center text-start h-100" for="kenderaan_sendiri">
                                        <i class="fas fa-car-side fa-2x me-3"></i>
                                        <div>
                                            <div class="fw-bold">Kenderaan Sendiri</div>
                                            <div class="small text-muted">Menggunakan kereta persendirian</div>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="kenderaan" id="kenderaan_pejabat" value="Kenderaan Pejabat" required {{ old('kenderaan') == 'Kenderaan Pejabat' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info w-100 p-3 d-flex align-items-center text-start h-100" for="kenderaan_pejabat">
                                        <i class="fas fa-building fa-2x me-3"></i>
                                        <div>
                                            <div class="fw-bold">Kenderaan Pejabat</div>
                                            <div class="small text-muted">Menggunakan kenderaan jabatan/rasmi</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('kenderaan')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 4. BUTIRAN PERGERAKAN RASMI (STATIK & SENTIASA MUNCUL) --}}
                        <div class="mb-4">
                            <hr class="my-4">
                            <h5 class="fw-bold text-primary mb-3"><i class="fas fa-route me-2"></i> Butiran Pergerakan Rasmi</h5>
                            
                            <div class="row mb-4">
                                {{-- Tujuan Penggunaan --}}
                                <div class="col-md-6">
                                    <label for="tujuan_penggunaan" class="form-label fw-bold text-muted">
                                        <i class="fas fa-bullseye me-1"></i> Tujuan Penggunaan
                                    </label>
                                    <input type="text" name="tujuan_penggunaan" id="tujuan_penggunaan" class="form-control @error('tujuan_penggunaan') is-invalid @enderror" value="{{ old('tujuan_penggunaan') }}" placeholder="Contoh: Bertugas di Mahkamah Tinggi" required>
                                    @error('tujuan_penggunaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Destinasi --}}
                                <div class="col-md-6">
                                    <label for="destinasi" class="form-label fw-bold text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i> Destinasi
                                    </label>
                                    <input type="text" name="destinasi" id="destinasi" class="form-control @error('destinasi') is-invalid @enderror" value="{{ old('destinasi') }}" placeholder="Contoh: Kuala Lumpur / Pejabat Tanah Galian" required>
                                    @error('destinasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- 5. CATATAN --}}
                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-bold text-muted">
                                <i class="fas fa-sticky-note me-1"></i> Catatan
                            </label>
                            <textarea name="catatan" id="catatan" rows="4" class="form-control" placeholder="Masukkan sebarang catatan tambahan jika perlu...">{{ old('catatan') }}</textarea>
                        </div>

                        {{-- BUTANG SUBMIT --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i> Hantar Permohonan
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputMula = document.getElementById('tarikh_mula');
        const inputMasaMula = document.getElementById('masa_mula'); // Baru
        const inputAkhir = document.getElementById('tarikh_akhir');
        const inputMasaAkhir = document.getElementById('masa_akhir'); // Baru
        const inputTempoh = document.getElementById('tempoh');

        // Fungsi Kira Tarikh Akhir
        function kiraTarikhAkhir() {
            if (inputMula.value && inputTempoh.value) {
                let date = new Date(inputMula.value);
                let hari = parseInt(inputTempoh.value);

                if(hari > 0) {
                    // Kira tarikh akhir berdasarkan tempoh hari
                    date.setDate(date.getDate() + (hari - 1));
                    
                    let year = date.getFullYear();
                    let month = String(date.getMonth() + 1).padStart(2, '0');
                    let day = String(date.getDate()).padStart(2, '0');
                    
                    inputAkhir.value = `${year}-${month}-${day}`;
                } else {
                    inputAkhir.value = '';
                }
            }
        }

        // Fungsi Kira Tempoh
        function kiraTempoh() {
            if (inputMula.value && inputAkhir.value) {
                let start = new Date(inputMula.value);
                let end = new Date(inputAkhir.value);
                
                let diffTime = end - start;
                
                if (diffTime >= 0) {
                    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    inputTempoh.value = diffDays;
                } else {
                    inputTempoh.value = '';
                    alert('Tarikh Akhir tidak boleh awal dari Tarikh Mula!');
                    inputAkhir.value = '';
                }
            }
        }

        // Event Listeners Tarikh/Tempoh
        inputTempoh.addEventListener('input', kiraTarikhAkhir);
        inputMula.addEventListener('change', function() {
            if(inputTempoh.value) kiraTarikhAkhir();
            else if(inputAkhir.value) kiraTempoh();
        });
        inputAkhir.addEventListener('change', kiraTempoh);
        
        // Tambahan: Jika tarikh diisi, isikan masa piawai (pilihan)
        // inputMula.addEventListener('change', function() { if (!inputMasaMula.value) inputMasaMula.value = '08:00'; });
        // inputAkhir.addEventListener('change', function() { if (!inputMasaAkhir.value) inputMasaAkhir.value = '17:00'; });
    });
</script>
@endpush