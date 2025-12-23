@extends('layouts.agensi')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Kemaskini Permohonan Perizaban (Seksyen 62)</h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('seksyen62.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- PENTING: Untuk proses update --}}

                        <div class="alert alert-info py-2 mb-4">
                            <i class="fas fa-language"></i> Maklumat Dwibahasa (BM & BI)
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tujuan / Maksud Awam (BM)</label>
                                <input type="text" name="tujuan_bm" class="form-control" value="{{ $data->tujuan_bm }}" required>
                                <div class="form-text">Akan dipaparkan dlm perenggan Bahasa Melayu.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Public Purpose (BI)</label>
                                <input type="text" name="tujuan_bi" class="form-control" value="{{ $data->tujuan_bi }}" required>
                                <div class="form-text">Akan dipaparkan dlm perenggan Bahasa Inggeris.</div>
                            </div>
                        </div>

                        <div class="row mb-4 border-top pt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pihak Mengawal (BM)</label>
                                <input type="text" name="kawalan_bm" class="form-control" value="{{ $data->kawalan_bm }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Control Of (BI)</label>
                                <input type="text" name="kawalan_bi" class="form-control" value="{{ $data->kawalan_bi }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Diselenggara Oleh (BM)</label>
                                <input type="text" name="selenggara_bm" class="form-control" value="{{ $data->selenggara_bm }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Maintained By (BI)</label>
                                <input type="text" name="selenggara_bi" class="form-control" value="{{ $data->selenggara_bi }}" required>
                            </div>
                        </div>

                        <div class="bg-light p-2 border rounded mb-3">
                            <i class="fas fa-th-list"></i> Maklumat Jadual / Schedule
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
                                    <input type="text" name="luas" class="form-control" value="{{ $data->luas }}" required>
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tarikh Dokumen</label>
                                <input type="date" name="tarikh_tt" class="form-control" value="{{ $data->tarikh_tt }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">No. Fail / Rujukan</label>
                                <input type="text" name="no_fail" class="form-control" value="{{ $data->no_fail }}" required>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permohonan.seksyen62') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-warning px-5 shadow fw-bold">
                                <i class="fas fa-save"></i> Simpan Kemaskini
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection