@extends('layouts.agensi')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Borang Permohonan Perizaban (Seksyen 62)</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('permohonan.seksyen62.store') }}" method="POST">
                        @csrf
                        
                        <div class="alert alert-info py-2"><i class="fas fa-language"></i> Maklumat Dwibahasa (BM & BI)</div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tujuan / Maksud Awam (BM)</label>
                                <input type="text" name="tujuan_bm" class="form-control" placeholder="Contoh: Kawasan Rekreasi" required>
                                <div class="form-text">Akan dipaparkan dlm perenggan Bahasa Melayu.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Public Purpose (BI)</label>
                                <input type="text" name="tujuan_bi" class="form-control" placeholder="Contoh: Recreation Area" required>
                                <div class="form-text">Akan dipaparkan dlm perenggan Bahasa Inggeris.</div>
                            </div>
                        </div>

                        <div class="row mb-4 border-top pt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pihak Mengawal (BM)</label>
                                <input type="text" name="kawalan_bm" class="form-control" placeholder="Setiausaha Kerajaan Negeri Melaka" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Control Of (BI)</label>
                                <input type="text" name="kawalan_bi" class="form-control" placeholder="State Secretary of Malacca" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Diselenggara Oleh (BM)</label>
                                <input type="text" name="selenggara_bm" class="form-control" placeholder="Majlis Perbandaran Jasin" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Maintained By (BI)</label>
                                <input type="text" name="selenggara_bi" class="form-control" placeholder="Jasin Municipal Council" required>
                            </div>
                        </div>

                        <div class="alert alert-secondary py-2"><i class="fas fa-th-list"></i> Maklumat Jadual / Schedule</div>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Daerah</label>
                                <input type="text" name="daerah" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Mukim</label>
                                <input type="text" name="mukim" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">No. Lot</label>
                                <input type="text" name="no_lot" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">No. Pelan Akui (PA)</label>
                                <input type="text" name="no_pa" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Keluasan (Meter Persegi)</label>
                                <div class="input-group">
                                    <input type="text" name="luas" class="form-control" placeholder="15,364" required>
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tarikh Dokumen</label>
                                <input type="date" name="tarikh_tt" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">No. Fail / Rujukan</label>
                                <input type="text" name="no_fail" class="form-control" required>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permohonan.seksyen62') }}" class="btn btn-light"><i class="fas fa-arrow-left"></i> Kembali</a>
                            <button type="submit" class="btn btn-primary px-5 shadow"><i class="fas fa-paper-plane"></i> Hantar Permohonan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection