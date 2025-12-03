@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Kemaskini Anggaran D'BUS</h3>
        <a href="{{ route('pentadbiran.dbus.index') }}" class="btn btn-secondary shadow-sm">Kembali</a>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-4">
            
            <form action="{{ route('pentadbiran.dbus.store') }}" method="POST">
                @csrf

                {{-- PILIHAN TAHUN --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tahun Anggaran</label>
                        <select name="tahun" class="form-select border-primary fw-bold">
                            <option value="2027" selected>2027</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Kategori Objek Am (OA)</label>
                        <select name="kategori_oa" id="kategori_oa" class="form-select border-primary fw-bold" onchange="toggleForm()">
                            <option value="OA10000" {{ (request('kategori') == 'OA10000') ? 'selected' : '' }}>OA10000 - EMOLUMEN</option>
                            <option value="OA20000" {{ (request('kategori') == 'OA20000') ? 'selected' : '' }}>OA20000 - PERKHIDMATAN & BEKALAN</option>
                        </select>
                    </div>
                </div>

                <hr>

                {{-- FORM: OA10000 (EMOLUMEN) --}}
                <div id="form-OA10000" style="display: block;">
                    <h5 class="text-primary fw-bold mb-3"><i class="fas fa-money-bill-wave me-2"></i> Butiran Emolumen</h5>
                    
                    {{-- OS 11000 --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="fw-bold">OS11000: GAJI DAN UPAHAN</label>
                        <div class="input-group mt-2">
                            <span class="input-group-text">OL11101 Gaji Biasa</span>
                            <input type="number" step="0.01" name="data[0][jumlah]" class="form-control" placeholder="0.00" 
                                   value="{{ $existingData['OL11101'] ?? '' }}">
                            <input type="hidden" name="data[0][kod]" value="OL11101">
                        </div>
                    </div>

                    {{-- OS 12000 --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="fw-bold">OS12000: ELAUN TETAP</label>
                        <div class="row g-2 mt-1">
                            <div class="col-md-6">
                                <label class="small text-muted">OL12101 Elaun Khidmat Awam</label>
                                <input type="number" step="0.01" name="data[1][jumlah]" class="form-control form-control-sm" 
                                       value="{{ $existingData['OL12101'] ?? '' }}">
                                <input type="hidden" name="data[1][kod]" value="OL12101">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">OL12102 Bantuan Sewa Rumah</label>
                                <input type="number" step="0.01" name="data[2][jumlah]" class="form-control form-control-sm"
                                       value="{{ $existingData['OL12102'] ?? '' }}">
                                <input type="hidden" name="data[2][kod]" value="OL12102">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">OL12103 Elaun Keraian</label>
                                <input type="number" step="0.01" name="data[3][jumlah]" class="form-control form-control-sm"
                                       value="{{ $existingData['OL12103'] ?? '' }}">
                                <input type="hidden" name="data[3][kod]" value="OL12103">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">OL12199 Elaun Tetap Lain</label>
                                <input type="number" step="0.01" name="data[4][jumlah]" class="form-control form-control-sm"
                                       value="{{ $existingData['OL12199'] ?? '' }}">
                                <input type="hidden" name="data[4][kod]" value="OL12199">
                            </div>
                        </div>
                    </div>

                    {{-- Tambah lagi field lain ikut PDF Emolumen --}}
                </div>

                {{-- FORM: OA20000 (PERKHIDMATAN) --}}
                <div id="form-OA20000" style="display: none;">
                    <h5 class="text-success fw-bold mb-3"><i class="fas fa-boxes me-2"></i> Butiran Perkhidmatan & Bekalan</h5>
                    
                    {{-- OS 21000 --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="fw-bold">OS21000: PERJALANAN & SARA HIDUP</label>
                        <div class="row g-2 mt-1">
                            <div class="col-md-6">
                                <label class="small text-muted">OL21101 Makanan & Minuman</label>
                                <input type="number" step="0.01" name="data[10][jumlah]" class="form-control form-control-sm"
                                       value="{{ $existingData['OL21101'] ?? '' }}">
                                <input type="hidden" name="data[10][kod]" value="OL21101">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">OL21104 Tambang & Elaun Perjalanan</label>
                                <input type="number" step="0.01" name="data[11][jumlah]" class="form-control form-control-sm"
                                       value="{{ $existingData['OL21104'] ?? '' }}">
                                <input type="hidden" name="data[11][kod]" value="OL21104">
                            </div>
                        </div>
                    </div>

                    {{-- OS 23000 --}}
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="fw-bold">OS23000: PERHUBUNGAN & UTILITI</label>
                        <div class="row g-2 mt-1">
                            <div class="col-md-4">
                                <label class="small text-muted">OL23101 Pos</label>
                                <input type="number" step="0.01" name="data[12][jumlah]" class="form-control form-control-sm"
                                       value="{{ $existingData['OL23101'] ?? '' }}">
                                <input type="hidden" name="data[12][kod]" value="OL23101">
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted">OL23102 Telefon/Internet</label>
                                <input type="number" step="0.01" name="data[13][jumlah]" class="form-control form-control-sm"
                                       value="{{ $existingData['OL23102'] ?? '' }}">
                                <input type="hidden" name="data[13][kod]" value="OL23102">
                            </div>
                        </div>
                    </div>

                    {{-- Tambah lagi field lain ikut PDF Perkhidmatan --}}
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">Simpan Maklumat</button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleForm() {
        var selected = document.getElementById('kategori_oa').value;
        
        // Sembunyikan semua dulu
        document.getElementById('form-OA10000').style.display = 'none';
        document.getElementById('form-OA20000').style.display = 'none';

        // Tunjuk yang dipilih
        if(selected === 'OA10000') {
            document.getElementById('form-OA10000').style.display = 'block';
        } else {
            document.getElementById('form-OA20000').style.display = 'block';
        }
    }

    // Run sekali masa load (untuk Edit mode)
    document.addEventListener('DOMContentLoaded', function() {
        toggleForm();
    });
</script>
@endpush
@endsection