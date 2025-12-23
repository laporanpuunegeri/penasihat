@extends('layouts.agensi')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kemaskini Borang 8A (ID: #{{ $data->id }})</h1>
        <a href="{{ route('permohonan.seksyen130') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen130.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-primary">
                        <h6 class="m-0 font-weight-bold text-primary">1. Maklumat Pemilik</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pemilik <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemilik" class="form-control" value="{{ $data->nama_pemilik }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Kad Pengenalan</label>
                            <input type="text" name="no_kp_pemilik" class="form-control" value="{{ $data->no_kp_pemilik }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Alamat Surat-Menyurat</label>
                            <textarea name="alamat_pemilik" class="form-control" rows="3">{{ $data->alamat_pemilik }}</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-info">
                        <h6 class="m-0 font-weight-bold text-info">Maklumat Notis</h6>
                    </div>
                    <div class="card-body">
                         <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Fail</label>
                            <input type="text" class="form-control bg-light" value="{{ $data->no_fail }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pentadbir Tanah</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="{{ $data->nama_pentadbir }}">
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
                                    <option value="P.M." {{ $data->jenis_hakmilik == 'P.M.' ? 'selected' : '' }}>P.M.</option>
                                    <option value="P.N." {{ $data->jenis_hakmilik == 'P.N.' ? 'selected' : '' }}>P.N.</option>
                                    <option value="G.M." {{ $data->jenis_hakmilik == 'G.M.' ? 'selected' : '' }}>G.M.</option>
                                    <option value="H.S.(D)" {{ $data->jenis_hakmilik == 'H.S.(D)' ? 'selected' : '' }}>H.S.(D)</option>
                                    <option value="H.S.(M)" {{ $data->jenis_hakmilik == 'H.S.(M)' ? 'selected' : '' }}>H.S.(M)</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label font-weight-bold">No. Hakmilik <span class="text-danger">*</span></label>
                                <input type="text" name="no_hakmilik" class="form-control" value="{{ $data->no_hakmilik }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Lot / P.T. <span class="text-danger">*</span></label>
                                <input type="text" name="no_lot" class="form-control" value="{{ $data->no_lot }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Luas / Area <span class="text-danger">*</span></label>
                                <input type="text" name="luas" class="form-control" value="{{ $data->luas }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Bandar / Pekan / Mukim</label>
                                <input type="text" name="mukim" class="form-control" value="{{ $data->mukim }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Daerah</label>
                                <input type="text" name="daerah" class="form-control" value="{{ $data->daerah }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Status Permohonan</label>
                            <select name="status" class="form-control">
                                <option value="Baru" {{ $data->status == 'Baru' ? 'selected' : '' }}>Baru</option>
                                <option value="Semakan" {{ $data->status == 'Semakan' ? 'selected' : '' }}>Dalam Semakan</option>
                                <option value="Selesai" {{ $data->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4 text-right">
                    <button type="submit" class="btn btn-success btn-lg shadow">
                        <i class="fas fa-save"></i> Kemaskini Rekod
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection