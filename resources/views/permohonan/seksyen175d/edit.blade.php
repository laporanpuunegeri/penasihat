@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Kemaskini Borang 10H</h1>
    <form action="{{ route('permohonan.seksyen175d.update', $data->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">No. Fail</label>
                            <input type="text" name="no_fail" class="form-control" value="{{ $data->no_fail }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Pemilik</label>
                            <input type="text" name="nama_pemilik" class="form-control" value="{{ $data->nama_pemilik }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. KP Pemilik</label>
                            <input type="text" name="no_kp_pemilik" class="form-control" value="{{ $data->no_kp_pemilik }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kepentingan</label>
                            <input type="text" name="bahagian_tanah" class="form-control" value="{{ $data->bahagian_tanah }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pentadbir Tanah</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="{{ $data->nama_pentadbir }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3"><label>Jenis Hakmilik</label><input type="text" name="jenis_hakmilik" class="form-control" value="{{ $data->jenis_hakmilik }}" required></div>
                            <div class="col-6 mb-3"><label>No. Hakmilik</label><input type="text" name="no_hakmilik" class="form-control" value="{{ $data->no_hakmilik }}" required></div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3"><label>No. Lot</label><input type="text" name="no_lot" class="form-control" value="{{ $data->no_lot }}" required></div>
                            <div class="col-6 mb-3"><label>Luas</label><input type="text" name="luas" class="form-control" value="{{ $data->luas }}" required></div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3"><label>Mukim</label><input type="text" name="mukim" class="form-control" value="{{ $data->mukim }}" required></div>
                            <div class="col-6 mb-3"><label>Daerah</label><input type="text" name="daerah" class="form-control" value="{{ $data->daerah }}" required></div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Kemaskini Rekod</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection