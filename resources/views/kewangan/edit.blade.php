@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kemaskini Rekod Kewangan</h1>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4 border-left-warning">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Maklumat Rekod: {{ $record->kod_objek }} - {{ $record->butiran }}</h6>
        </div>
        <div class="card-body">
            
            <form action="{{ route('kewangan.update', $record->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-4">
                    <h6 class="text-gray-600 font-weight-bold mb-3 text-uppercase small border-bottom pb-2">Maklumat Asas</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Kategori Utama</label>
                            <select name="kod_utama" class="form-control" required>
                                <option value="10000" {{ $record->kod_utama == '10000' ? 'selected' : '' }}>10000 - EMOLUMEN</option>
                                <option value="20000" {{ $record->kod_utama == '20000' ? 'selected' : '' }}>20000 - PERKHIDMATAN & BEKALAN</option>
                                <option value="30000" {{ $record->kod_utama == '30000' ? 'selected' : '' }}>30000 - ASET</option>
                                <option value="40000" {{ $record->kod_utama == '40000' ? 'selected' : '' }}>40000 - PEMBERIAN & KENAAN BAYARAN TETAP</option>
                                <option value="50000" {{ $record->kod_utama == '50000' ? 'selected' : '' }}>50000 - PERBELANJAAN LAIN</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Kod Objek</label>
                            <input type="text" name="kod_objek" class="form-control" value="{{ $record->kod_objek }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold">Butiran / Jenis Perbelanjaan</label>
                        <input type="text" name="butiran" class="form-control" value="{{ $record->butiran }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold text-primary">Jumlah Peruntukan (Siling) RM</label>
                        <input type="number" step="0.01" name="peruntukan" class="form-control form-control-lg border-primary" value="{{ $record->peruntukan }}" required>
                    </div>
                </div>

                <div class="form-group mb-4 bg-light p-3 rounded">
                    <h6 class="text-gray-600 font-weight-bold mb-3 text-uppercase small border-bottom pb-2">Prestasi Perbelanjaan (Mengikut Suku Tahun)</h6>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold small text-muted">SUKU 1 (Jan - Mac)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">RM</span>
                                </div>
                                <input type="number" step="0.01" name="belanja_s1" class="form-control suku-input" value="{{ $record->belanja_s1 }}">
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold small text-muted">SUKU 2 (Apr - Jun)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">RM</span>
                                </div>
                                <input type="number" step="0.01" name="belanja_s2" class="form-control suku-input" value="{{ $record->belanja_s2 }}">
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold small text-muted">SUKU 3 (Jul - Sep)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">RM</span>
                                </div>
                                <input type="number" step="0.01" name="belanja_s3" class="form-control suku-input" value="{{ $record->belanja_s3 }}">
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold small text-muted">SUKU 4 (Okt - Dis)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">RM</span>
                                </div>
                                <input type="number" step="0.01" name="belanja_s4" class="form-control suku-input" value="{{ $record->belanja_s4 }}">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-secondary mt-2 d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold">Jumlah Keseluruhan Belanja (Auto-Kira):</span>
                        <span class="h5 mb-0 font-weight-bold text-danger" id="totalDisplay">RM {{ number_format($record->belanja, 2) }}</span>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Bila mana-mana input suku tahun berubah
        $('.suku-input').on('input', function() {
            let total = 0;
            // Campur semua nilai input
            $('.suku-input').each(function() {
                let val = parseFloat($(this).val()) || 0;
                total += val;
            });
            // Update paparan total
            $('#totalDisplay').text('RM ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        });
    });
</script>
@endpush

@endsection