@extends('layouts.agensi')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pendaftaran Borang 8A (Seksyen 130)</h1>
        <a href="{{ route('permohonan.seksyen130') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Senarai
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen130.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-primary">
                        <h6 class="m-0 font-weight-bold text-primary">1. Maklumat Pemilik</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pemilik <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemilik" class="form-control" placeholder="Contoh: SATIYASEELAN A/L SINNASAMY" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Kad Pengenalan</label>
                            <input type="text" name="no_kp_pemilik" class="form-control" placeholder="Contoh: 410720-08-5105">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Alamat Surat-Menyurat</label>
                            <textarea name="alamat_pemilik" class="form-control" rows="3" placeholder="Alamat lengkap pemilik..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-success">
                        <h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah (Jadual)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Jenis Hakmilik</label>
                                <select name="jenis_hakmilik" class="form-control">
                                    <option value="P.M.">P.M.</option>
                                    <option value="P.N.">P.N.</option>
                                    <option value="G.M.">G.M.</option>
                                    <option value="H.S.(D)">H.S.(D)</option>
                                    <option value="H.S.(M)">H.S.(M)</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label font-weight-bold">No. Hakmilik <span class="text-danger">*</span></label>
                                <input type="text" name="no_hakmilik" class="form-control" placeholder="Contoh: 586" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Lot / P.T. <span class="text-danger">*</span></label>
                                <input type="text" name="no_lot" class="form-control" placeholder="Contoh: LOT 1599" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Luas / Area <span class="text-danger">*</span></label>
                                <input type="text" name="luas" class="form-control" placeholder="Contoh: 1168.0000 Meter Persegi" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Bandar / Pekan / Mukim</label>
                                <input type="text" name="mukim" class="form-control" placeholder="Contoh: MUKIM TELOK MAS">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Daerah</label>
                                <input type="text" name="daerah" class="form-control" placeholder="Contoh: MELAKA TENGAH">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <p class="small text-muted">Pastikan semua maklumat yang bertanda (*) telah diisi dengan betul sebelum disimpan. Data ini akan digunakan untuk menjana Borang 8A rasmi.</p>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow">
                            <i class="fas fa-save"></i> Simpan Permohonan Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection