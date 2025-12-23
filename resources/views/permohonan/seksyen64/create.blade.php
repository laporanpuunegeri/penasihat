@extends('layouts.agensi')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Borang Permohonan Pembatalan Perizaban (Seksyen 64)</h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('permohonan.seksyen64.store') }}" method="POST">
                        @csrf

                        <div class="alert alert-info py-2 mb-4">
                            <i class="fas fa-history"></i> Maklumat Warta Asal (Yang Ingin Dibatalkan)
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. Pemberitahuan Warta Asal</label>
                                <input type="text" name="no_warta_asal" class="form-control" placeholder="Contoh: 496" required>
                                <div class="form-text">Rujuk nombor warta asal yang disiarkan sebelum ini.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tarikh Warta Asal Disiarkan</label>
                                <input type="date" name="tarikh_warta_asal" class="form-control" required>
                            </div>
                        </div>

                        <div class="alert alert-secondary py-2 mb-4">
                            <i class="fas fa-language"></i> Tujuan & Pihak Kawalan (Asal)
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Maksud Awam (BM)</label>
                                <input type="text" name="tujuan_bm" class="form-control" placeholder="Contoh: Tapak Tanah Perkuburan Islam" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Public Purpose (BI)</label>
                                <input type="text" name="tujuan_bi" class="form-control" placeholder="Contoh: Islamic Cemetery Site" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pihak Mengawal (BM)</label>
                                <input type="text" name="kawalan_bm" class="form-control" placeholder="Contoh: Pengarah Jabatan Agama Islam Melaka" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Control Of (BI)</label>
                                <input type="text" name="kawalan_bi" class="form-control" placeholder="Contoh: Director of the Melaka Islamic Religious Department" required>
                            </div>
                        </div>

                        <div class="bg-light p-2 border rounded mb-3 fw-bold">
                            <i class="fas fa-th-list me-2"></i> Maklumat Jadual / Schedule
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Daerah</label>
                                <input type="text" name="daerah" class="form-control" placeholder="Jasin" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Mukim</label>
                                <input type="text" name="mukim" class="form-control" placeholder="Kesang" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">No. Lot</label>
                                <input type="text" name="no_lot" class="form-control" placeholder="6799" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">No. Pelan Akui (PA)</label>
                                <input type="text" name="no_pa" class="form-control" placeholder="P.A. 100018" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Keluasan (Meter Persegi)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="luas" class="form-control" placeholder="4176" required>
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tarikh Dokumen (TT)</label>
                                <input type="date" name="tarikh_tt" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">No. Fail / Rujukan</label>
                                <input type="text" name="no_fail" class="form-control" placeholder="PTG(M)R/..." required>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permohonan.seksyen64') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-5 shadow">
                                <i class="fas fa-paper-plane me-1"></i> Hantar Permohonan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection