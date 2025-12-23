@extends('layouts.agensi')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow border-0">
                {{-- Header warna kuning melambangkan mod Kemaskini --}}
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Kemaskini Permohonan Pembatalan (Seksyen 64)</h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('seksyen64.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info py-2 mb-4">
                            <i class="fas fa-history"></i> Maklumat Warta Asal (Data Sedia Ada)
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. Pemberitahuan Warta Asal</label>
                                <input type="text" name="no_warta_asal" class="form-control" value="{{ $data->no_warta_asal }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tarikh Warta Asal Disiarkan</label>
                                <input type="date" name="tarikh_warta_asal" class="form-control" value="{{ $data->tarikh_warta_asal }}" required>
                            </div>
                        </div>

                        <div class="alert alert-secondary py-2 mb-4">
                            <i class="fas fa-language"></i> Tujuan & Pihak Kawalan (Asal)
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Maksud Awam (BM)</label>
                                <input type="text" name="tujuan_bm" class="form-control" value="{{ $data->tujuan_bm }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Public Purpose (BI)</label>
                                <input type="text" name="tujuan_bi" class="form-control" value="{{ $data->tujuan_bi }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pihak Mengawal (BM)</label>
                                <input type="text" name="kawalan_bm" class="form-control" value="{{ $data->kawalan_bm }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Control Of (BI)</label>
                                <input type="text" name="kawalan_bi" class="form-control" value="{{ $data->kawalan_bi }}" required>
                            </div>
                        </div>

                        <div class="bg-light p-2 border rounded mb-3 fw-bold">
                            <i class="fas fa-th-list me-2"></i> Maklumat Jadual / Schedule
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Daerah</label>
                                <input type="text" name="daerah" class="form-control" value="{{ $data->daerah }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Mukim</label>
                                <input type="text" name="mukim" class="form-control" value="{{ $data->mukim }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">No. Lot</label>
                                <input type="text" name="no_lot" class="form-control" value="{{ $data->no_lot }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">No. Pelan Akui (PA)</label>
                                <input type="text" name="no_pa" class="form-control" value="{{ $data->no_pa }}" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Keluasan (Meter Persegi)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="luas" class="form-control" value="{{ $data->luas }}" required>
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tarikh Dokumen (Baru)</label>
                                <input type="date" name="tarikh_tt" class="form-control" value="{{ $data->tarikh_tt }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">No. Fail / Rujukan</label>
                                <input type="text" name="no_fail" class="form-control" value="{{ $data->no_fail }}" required>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permohonan.seksyen64') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning px-5 shadow fw-bold">
                                <i class="fas fa-save me-1"></i> Simpan Kemaskini
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection