@extends('layouts.app')

@section('content')

<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #0f172a;
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
    .input-group-text {
        font-size: 0.8rem;
        font-weight: bold;
        background-color: #f8fafc;
    }
    .month-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Daftar Kewangan Baru</h3>
            <p class="text-muted small mb-0">Isi maklumat peruntukan dan perbelanjaan bulanan.</p>
        </div>
        <a href="{{ route('kewangan.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('kewangan.store') }}" method="POST">
        @csrf

        <div class="row">
            {{-- KIRI: MAKLUMAT ASAS --}}
            <div class="col-lg-4 mb-4">
                <div class="form-card p-4 h-100">
                    <h5 class="form-section-title"><i class="fas fa-info-circle me-2 text-primary"></i> Maklumat Asas</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Kategori Utama</label>
                        <select name="kod_utama" class="form-select" required>
                            <option value="">-- Sila Pilih --</option>
                            <option value="10000">10000 - EMOLUMEN</option>
                            <option value="20000">20000 - PERKHIDMATAN & BEKALAN</option>
                            <option value="30000">30000 - ASET</option>
                            <option value="40000">40000 - PEMBERIAN & KENAAN BAYARAN TETAP</option>
                            <option value="50000">50000 - PERBELANJAAN LAIN</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Kod Objek</label>
                        <input type="number" name="kod_objek" class="form-control" required placeholder="Contoh: 21000">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Butiran</label>
                        <textarea name="butiran" class="form-control" rows="3" required placeholder="Contoh: Gaji Kakitangan"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-primary">Jumlah Peruntukan (Siling)</label>
                        <div class="input-group">
                            <span class="input-group-text">RM</span>
                            <input type="number" step="0.01" name="peruntukan" class="form-control form-control-lg fw-bold text-primary" required placeholder="0.00">
                        </div>
                    </div>
                    
                    {{-- TOTAL LIVE DISPLAY --}}
                    <div class="alert alert-light border text-center mt-4">
                        <small class="text-muted text-uppercase fw-bold">Jumlah Belanja (Auto)</small>
                        <h3 class="fw-bold text-danger mb-0" id="totalDisplay">RM 0.00</h3>
                    </div>
                </div>
            </div>

            {{-- KANAN: INPUT BULANAN --}}
            <div class="col-lg-8 mb-4">
                <div class="form-card p-4 h-100">
                    <h5 class="form-section-title"><i class="fas fa-calendar-alt me-2 text-primary"></i> Perbelanjaan Bulanan</h5>
                    <p class="text-muted small mb-4">Masukkan nilai belanja mengikut bulan. Biarkan 0 jika tiada belanja.</p>

                    {{-- SUKU TAHUN 1 --}}
                    <div class="row g-3 mb-4 border-bottom pb-3">
                        <div class="col-12"><span class="badge bg-info text-dark">SUKU PERTAMA</span></div>
                        <div class="col-md-4">
                            <label class="month-label">Januari</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_jan" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="month-label">Februari</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_feb" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="month-label">Mac</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_mac" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    {{-- SUKU TAHUN 2 --}}
                    <div class="row g-3 mb-4 border-bottom pb-3">
                        <div class="col-12"><span class="badge bg-success">SUKU KEDUA</span></div>
                        <div class="col-md-4">
                            <label class="month-label">April</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_apr" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="month-label">Mei</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_mei" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="month-label">Jun</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_jun" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    {{-- SUKU TAHUN 3 --}}
                    <div class="row g-3 mb-4 border-bottom pb-3">
                        <div class="col-12"><span class="badge bg-warning text-dark">SUKU KETIGA</span></div>
                        <div class="col-md-4">
                            <label class="month-label">Julai</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_jul" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="month-label">Ogos</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_ogos" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="month-label">September</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_sep" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    {{-- SUKU TAHUN 4 --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12"><span class="badge bg-danger">SUKU KEEMPAT</span></div>
                        <div class="col-md-4">
                            <label class="month-label">Oktober</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_okt" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="month-label">November</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_nov" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="month-label">Disember</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="belanja_dis" class="form-control bulan-input" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="d-flex justify-content-end pb-5">
            <button type="submit" class="btn btn-success px-5 py-2 shadow fw-bold">
                <i class="fas fa-save me-2"></i> Simpan Rekod
            </button>
        </div>

    </form>
</div>

{{-- SCRIPT: Auto-Calculate Total --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll('.bulan-input');
        const totalDisplay = document.getElementById('totalDisplay');

        function calculateTotal() {
            let total = 0;
            inputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            totalDisplay.innerText = 'RM ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
        });
    });
</script>

@endsection