@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kemaskini Borang 10E (#{{ $data->id }})</h1>
        <a href="{{ route('permohonan.seksyen175a') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen175a.update', $data->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-primary">
                        <h6 class="m-0 font-weight-bold text-primary">1. Maklumat Pentadbiran</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Fail</label>
                            <input type="text" name="no_fail" class="form-control" value="{{ $data->no_fail }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Sebab Penyediaan</label>
                            <textarea name="sebab_penyediaan" class="form-control" rows="3" required>{{ $data->sebab_penyediaan }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pentadbir Tanah</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="{{ $data->nama_pentadbir }}">
                        </div>
                        {{-- Dropdown Status dibuang mengikut permintaan --}}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Jenis Hakmilik</label>
                                <input type="text" name="jenis_hakmilik" class="form-control" value="{{ $data->jenis_hakmilik }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Hakmilik</label>
                                <input type="text" name="no_hakmilik" class="form-control" value="{{ $data->no_hakmilik }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Lot</label>
                                <input type="text" name="no_lot" class="form-control" value="{{ $data->no_lot }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Luas</label>
                                <input type="text" name="luas" class="form-control" value="{{ $data->luas }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Mukim</label>
                                <input type="text" name="mukim" class="form-control" value="{{ $data->mukim }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Daerah</label>
                                <input type="text" name="daerah" class="form-control" value="{{ $data->daerah }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-block btn-lg shadow">
                    <i class="fas fa-save"></i> Kemaskini Rekod
                </button>
            </div>
        </div>
    </form>
</div>
@endsection