@extends('layouts.app')

@section('content')

{{-- Style Khas (Tema Edit - Oren/Kuning) --}}
<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #f59e0b; /* Warning Accent */
    }

    .form-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .form-section-title {
        color: #1e293b;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.75rem; /* Saiz label bulanan */
        text-transform: uppercase;
    }

    .form-control:focus, .form-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    /* Input Duit */
    .input-group-text {
        font-size: 0.8rem;
        font-weight: bold;
        background-color: #fff7ed;
        border-color: #ffedd5;
        color: #c2410c;
    }

    /* Total Display */
    .total-auto {
        background-color: #fffbeb;
        color: #d97706;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #fcd34d;
    }
    .total-baki-box {
        background-color: #e0f2fe; 
        border-color: #93c5fd;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            {{-- TAJUK DIBETULKAN --}}
            <h3 class="mb-1 fw-bold text-dark">Kemaskini Kewangan</h3> 
            <p class="text-muted small mb-0">Maklumat peruntukan dan perbelanjaan bulanan.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Butang Padam (FIXED VARIABLE: $record) --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pa')
                <form action="{{ route('kewangan.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger shadow-sm" title="Padam Rekod">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif

            <a href="{{ route('kewangan.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    {{-- FORM (ACTION DIBETULKAN KE ROUTE KEWANGAN) --}}
    <form method="POST" action="{{ route('kewangan.update', $record->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- KOLUM KIRI: MAKLUMAT ASAS & TOTAL --}}
            <div class="col-lg-4 mb-4">
                <div class="form-card p-4 h-100">
                    <h5 class="form-section-title"><i class="fas fa-info-circle me-2 text-warning"></i> Maklumat Asas</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Kategori Utama</label>
                        <select name="kod_utama" class="form-select" required>
                            <option value="10000" {{ $record->kod_utama == '10000' ? 'selected' : '' }}>10000 - EMOLUMEN</option>
                            <option value="20000" {{ $record->kod_utama == '20000' ? 'selected' : '' }}>20000 - PERKHIDMATAN & BEKALAN</option>
                            <option value="30000" {{ $record->kod_utama == '30000' ? 'selected' : '' }}>30000 - ASET</option>
                            <option value="40000" {{ $record->kod_utama == '40000' ? 'selected' : '' }}>40000 - PEMBERIAN & KENAAN BAYARAN TETAP</option>
                            <option value="50000" {{ $record->kod_utama == '50000' ? 'selected' : '' }}>50000 - PERBELANJAAN LAIN</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Kod Objek</label>
                        <input type="number" name="kod_objek" class="form-control" required value="{{ $record->kod_objek }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Butiran</label>
                        <textarea name="butiran" class="form-control" rows="3" required>{{ $record->butiran }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-primary">Jumlah Peruntukan (Siling)</label>
                        <div class="input-group">
                            <span class="input-group-text">RM</span>
                            <input type="number" step="0.01" name="peruntukan" id="peruntukan" class="form-control form-control-lg fw-bold text-primary" required value="{{ $record->peruntukan }}">
                        </div>
                    </div>

                    {{-- TOTAL LIVE DISPLAY (Auto) --}}
                    <div class="total-auto text-center mt-4 mb-3">
                        <small class="text-uppercase fw-bold">Jumlah Belanja Terkini</small>
                        <h3 class="fw-bold text-danger mb-0" id="totalDisplay">RM {{ number_format($record->belanja, 2) }}</h3>
                    </div>
                    
                    {{-- BAKI LIVE DISPLAY --}}
                    <div class="total-auto total-baki-box text-center">
                        <small class="text-uppercase fw-bold">Baki Perbelanjaan</small>
                        <h3 class="fw-bold mb-0 {{ ($record->peruntukan - $record->belanja) >= 0 ? 'text-success' : 'text-danger' }}" id="bakiDisplay">
                            RM {{ number_format($record->peruntukan - $record->belanja, 2) }}
                        </h3>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: PRESTASI BULANAN --}}
            <div class="col-lg-8 mb-4">
                <div class="form-card p-4 h-100">
                    <h5 class="form-section-title"><i class="fas fa-calendar-alt me-2 text-warning"></i> Prestasi Perbelanjaan Bulanan</h5>
                    <p class="text-muted small mb-4">Kemaskini nilai belanja mengikut bulan. Jumlah keseluruhan akan dikira secara automatik.</p>

                    @php
                        $months = [
                            'jan' => 'Januari', 'feb' => 'Februari', 'mac' => 'Mac',
                            'apr' => 'April', 'mei' => 'Mei', 'jun' => 'Jun',
                            'jul' => 'Julai', 'ogos' => 'Ogos', 'sep' => 'September',
                            'okt' => 'Oktober', 'nov' => 'November', 'dis' => 'Disember'
                        ];
                    @endphp

                    {{-- SUKU TAHUN 1 (Jan - Mac) --}}
                    <div class="row g-3 mb-4 border-bottom pb-3">
                        <div class="col-12"><span class="badge bg-info text-dark">SUKU PERTAMA</span></div>
                        @foreach(['jan', 'feb', 'mac'] as $key)
                        <div class="col-md-3 col-6">
                            <label class="form-label text-muted">{{ $months[$key] }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_{{ $key }}" 
                                       class="form-control bulan-input" 
                                       placeholder="0.00" 
                                       value="{{ $record->{'belanja_'.$key} ?? 0 }}">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- SUKU TAHUN 2 (Apr - Jun) --}}
                    <div class="row g-3 mb-4 border-bottom pb-3">
                        <div class="col-12"><span class="badge bg-success">SUKU KEDUA</span></div>
                        @foreach(['apr', 'mei', 'jun'] as $key)
                        <div class="col-md-3 col-6">
                            <label class="form-label text-muted">{{ $months[$key] }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_{{ $key }}" 
                                       class="form-control bulan-input" 
                                       placeholder="0.00" 
                                       value="{{ $record->{'belanja_'.$key} ?? 0 }}">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- SUKU TAHUN 3 (Jul - Sep) --}}
                    <div class="row g-3 mb-4 border-bottom pb-3">
                        <div class="col-12"><span class="badge bg-warning text-dark">SUKU KETIGA</span></div>
                        @foreach(['jul', 'ogos', 'sep'] as $key)
                        <div class="col-md-3 col-6">
                            <label class="form-label text-muted">{{ $months[$key] }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_{{ $key }}" 
                                       class="form-control bulan-input" 
                                       placeholder="0.00" 
                                       value="{{ $record->{'belanja_'.$key} ?? 0 }}">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- SUKU TAHUN 4 (Okt - Dis) --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12"><span class="badge bg-danger">SUKU KEEMPAT</span></div>
                        @foreach(['okt', 'nov', 'dis'] as $key)
                        <div class="col-md-3 col-6">
                            <label class="form-label text-muted">{{ $months[$key] }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_{{ $key }}" 
                                       class="form-control bulan-input" 
                                       placeholder="0.00" 
                                       value="{{ $record->{'belanja_'.$key} ?? 0 }}">
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="d-flex justify-content-end pb-5">
            <a href="{{ route('kewangan.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-5 py-2 shadow fw-bold text-dark">
                <i class="fas fa-save me-2"></i> Simpan Kemaskini
            </button>
        </div>

    </form>
</div>

{{-- SCRIPT: Auto-calculate Total (Updated for Baki) --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll('.bulan-input');
        const totalDisplay = document.getElementById('totalDisplay');
        const bakiDisplay = document.getElementById('bakiDisplay');
        const peruntukanInput = document.getElementById('peruntukan');

        function calculateTotal() {
            const peruntukan = parseFloat(peruntukanInput.value) || 0;
            let totalBelanja = 0;
            inputs.forEach(input => {
                totalBelanja += parseFloat(input.value) || 0;
            });
            
            const baki = peruntukan - totalBelanja;

            // Update Total Belanja Display
            totalDisplay.innerText = 'RM ' + totalBelanja.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // Update Baki Display
            bakiDisplay.innerText = 'RM ' + baki.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Change color if baki is negative
            if (baki < 0) {
                bakiDisplay.classList.remove('text-success');
                bakiDisplay.classList.add('text-danger');
            } else {
                bakiDisplay.classList.remove('text-danger');
                bakiDisplay.classList.add('text-success');
            }
        }

        // Run on load to show existing data total
        calculateTotal(); 

        inputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        peruntukanInput.addEventListener('input', calculateTotal); // Update Baki kalau Siling diubah
    });
</script>

@endsection