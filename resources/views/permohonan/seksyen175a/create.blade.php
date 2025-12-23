@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pendaftaran Borang 10E (Seksyen 175A)</h1>
        <a href="{{ route('permohonan.seksyen175a') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen175a.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">1. Maklumat Notis & Pentadbiran</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Fail <span class="text-danger">*</span></label>
                            <input type="text" name="no_fail" class="form-control" placeholder="Contoh: PTAG. B1/06/06/2020(2024)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Sebab Penyediaan <span class="text-danger">*</span></label>
                            <textarea name="sebab_penyediaan" class="form-control" rows="3" required>Dokumen hakmilik daftar (Buku Daftar) yang asal telah rosak.</textarea>
                            <small class="text-muted">Rujuk Seksyen 175A Kanun Tanah Negara[cite: 72].</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pentadbir Tanah</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="BARI'AH BINTI DZULKIFLI">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah (Jadual)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Jenis Hakmilik <span class="text-danger">*</span></label>
                                <input type="text" name="jenis_hakmilik" class="form-control" placeholder="Contoh: E.M.R." required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Hakmilik <span class="text-danger">*</span></label>
                                <input type="text" name="no_hakmilik" class="form-control" placeholder="Contoh: 1451" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Lot <span class="text-danger">*</span></label>
                                <input type="text" name="no_lot" class="form-control" placeholder="Contoh: 1451" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Luas <span class="text-danger">*</span></label>
                                <input type="text" name="luas" class="form-control" placeholder="Contoh: 1a. 1r. 12p." required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Mukim <span class="text-danger">*</span></label>
                                <input type="text" name="mukim" class="form-control" placeholder="Contoh: Ayer Paabas" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Daerah <span class="text-danger">*</span></label>
                                <input type="text" name="daerah" class="form-control" placeholder="Contoh: Alor Gajah" required>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg shadow">
                    <i class="fas fa-save"></i> Simpan Borang 10E
                </button>
            </div>
        </div>
    </form>
</div>
@endsection