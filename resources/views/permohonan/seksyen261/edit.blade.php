@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kemaskini Borang 16G</h1>
        <a href="{{ route('permohonan.seksyen261') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen261.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4 border-left-warning">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">1. Maklumat Saman & Siasatan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Fail</label>
                            <input type="text" name="no_fail" class="form-control" value="{{ $data->no_fail }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Penggadai</label>
                            <input type="text" name="nama_penggadai" class="form-control" value="{{ $data->nama_penggadai }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Alamat Penggadai</label>
                            <textarea name="alamat_penggadai" class="form-control" rows="3" required>{{ $data->alamat_penggadai }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pemegang Gadai</label>
                            <input type="text" name="nama_pemegang_gadai" class="form-control" value="{{ $data->nama_pemegang_gadai }}" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tempat Siasatan</label>
                            <input type="text" name="tempat_siasatan" class="form-control" value="{{ $data->tempat_siasatan }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Tarikh Siasatan</label>
                                <input type="date" name="tarikh_siasatan" class="form-control" value="{{ $data->tarikh_siasatan }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Masa Siasatan</label>
                                <input type="time" name="masa_siasatan" class="form-control" value="{{ $data->masa_siasatan }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah & Gadaian</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Bandar/Pekan/Mukim</label>
                                <input type="text" name="mukim" class="form-control" value="{{ $data->mukim }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Lot/Petak/PT</label>
                                <input type="text" name="no_lot" class="form-control" value="{{ $data->no_lot }}" required>
                            </div>
                        </div>
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
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Bahagian Tanah</label>
                            <input type="text" name="bahagian_tanah" class="form-control" value="{{ $data->bahagian_tanah }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Berdaftar Gadaian</label>
                            <input type="text" name="no_daftar_gadaian" class="form-control" value="{{ $data->no_daftar_gadaian }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pentadbir</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="{{ $data->nama_pentadbir }}">
                        </div>
                        {{-- Tiada Pilihan Status di sini --}}
                        <button type="submit" class="btn btn-warning btn-block btn-lg shadow mt-3 font-weight-bold text-dark">
                            <i class="fas fa-edit"></i> Kemaskini Rekod
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection