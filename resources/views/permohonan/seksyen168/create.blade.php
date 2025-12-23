@extends('layouts.agensi')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pendaftaran Borang 10D (Seksyen 168)</h1>
        <a href="{{ route('permohonan.seksyen168') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen168.store') }}" method="POST">
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
                            <input type="text" name="nama_pemilik" class="form-control" placeholder="Contoh: ALI BIN ABU" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Kad Pengenalan</label>
                            <input type="text" name="no_kp_pemilik" class="form-control" placeholder="Contoh: 880101-01-5555">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Alamat Surat-Menyurat</label>
                            <textarea name="alamat_pemilik" class="form-control" rows="3" placeholder="Alamat lengkap..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-warning">
                        <h6 class="m-0 font-weight-bold text-warning">Perihal Kehilangan (Penting untuk Borang 10D)</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Sebab Permohonan</label>
                            <select name="sebab_permohonan" class="form-control">
                                <option value="Dokumen hakmilik yang dikeluarkan telah hilang.">Dokumen hakmilik yang dikeluarkan telah hilang.</option>
                                <option value="Dokumen hakmilik yang dikeluarkan telah rosak sebahagian/sepenuhnya.">Dokumen hakmilik yang dikeluarkan telah rosak.</option>
                                <option value="Dokumen hakmilik sebahagiannya telah musnah.">Dokumen hakmilik sebahagiannya telah musnah.</option>
                            </select>
                            <small class="text-muted">Ayat ini akan dipaparkan dalam teks notis rasmi.</small>
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
                                    <option value="Pajakan Negeri">Pajakan Negeri</option>
                                    <option value="Geran Mukim">Geran Mukim</option>
                                    <option value="Geran">Geran</option>
                                    <option value="H.S.(D)">H.S.(D)</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label font-weight-bold">No. Hakmilik <span class="text-danger">*</span></label>
                                <input type="text" name="no_hakmilik" class="form-control" placeholder="Contoh: 39519" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Lot / P.T. <span class="text-danger">*</span></label>
                                <input type="text" name="no_lot" class="form-control" placeholder="Contoh: 4481" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Luas (Area) <span class="text-danger">*</span></label>
                                <input type="text" name="luas" class="form-control" placeholder="Contoh: 143 Meter Persegi" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Bandar / Pekan / Mukim <span class="text-danger">*</span></label>
                                <input type="text" name="mukim" class="form-control" placeholder="Contoh: Mukim Paya Rumput" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Daerah <span class="text-danger">*</span></label>
                                <input type="text" name="daerah" class="form-control" placeholder="Contoh: MELAKA TENGAH" required>
                            </div>
                        </div>
                        
                         <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Fail (Rujukan Jadual) <span class="text-danger">*</span></label>
                            <input type="text" name="no_fail" class="form-control" placeholder="Contoh: PTHM.PH.657/10 Jld.4 (1)" required>
                            <small class="text-muted">Penting untuk dipaparkan dalam jadual Borang 10D.</small>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow">
                            <i class="fas fa-save"></i> Simpan Permohonan Borang 10D
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection