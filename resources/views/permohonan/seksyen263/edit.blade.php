@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kemaskini Borang 16H (Seksyen 263)</h1>
        <a href="{{ route('permohonan.seksyen263') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen263.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- BAHAGIAN 1: MAKLUMAT LELONGAN --}}
            <div class="col-lg-7">
                <div class="card shadow mb-4 border-left-warning">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">1. Maklumat Lelongan & Perintah</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Fail</label>
                                <input type="text" name="no_fail" class="form-control" value="{{ $data->no_fail }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tarikh Perintah</label>
                                <input type="date" name="tarikh_perintah" class="form-control" value="{{ $data->tarikh_perintah->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="font-weight-bold">Pemegang Gadaian</label>
                            <input type="text" name="nama_pemegang_gadai" class="form-control" value="{{ $data->nama_pemegang_gadai }}" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tarikh Lelongan</label>
                                <input type="date" name="tarikh_lelongan" class="form-control" value="{{ $data->tarikh_lelongan->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold">Hari</label>
                                <input type="text" name="hari_lelongan" class="form-control" value="{{ $data->hari_lelongan }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold">Masa</label>
                                <input type="time" name="masa_lelongan" class="form-control" value="{{ $data->masa_lelongan }}" required>
                            </div>
                        </div>

                        {{-- INI YANG TERTINGGAL TADI (TEMPAT LELONGAN) --}}
                        <div class="mb-3">
                            <label class="font-weight-bold">Tempat Lelongan</label>
                            <textarea name="tempat_lelongan" class="form-control" rows="2" required>{{ $data->tempat_lelongan }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Harga Rizab</label>
                                <input type="number" step="0.01" name="harga_rizab" class="form-control" value="{{ $data->harga_rizab }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Deposit 10%</label>
                                <input type="number" step="0.01" name="deposit_sepuluh_peratus" class="form-control" value="{{ $data->deposit_sepuluh_peratus }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Amaun Hutang</label>
                                <input type="number" step="0.01" name="amaun_hutang" class="form-control" value="{{ $data->amaun_hutang }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAHAGIAN 2: MAKLUMAT TANAH & PENTADBIR --}}
            <div class="col-lg-5">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah & Jadual</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Mukim</label>
                                <input type="text" name="mukim" class="form-control" value="{{ $data->mukim }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Lot</label>
                                <input type="text" name="no_lot" class="form-control" value="{{ $data->no_lot }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Hakmilik</label>
                                <input type="text" name="jenis_hakmilik" class="form-control" value="{{ $data->jenis_hakmilik }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Hakmilik</label>
                                <input type="text" name="no_hakmilik" class="form-control" value="{{ $data->no_hakmilik }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="font-weight-bold">Bahagian Tanah</label>
                            <input type="text" name="bahagian_tanah" class="form-control" value="{{ $data->bahagian_tanah }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="font-weight-bold">No. Daftar Gadaian</label>
                            <input type="text" name="no_daftar_gadaian" class="form-control" value="{{ $data->no_daftar_gadaian }}" required>
                        </div>
                        
                        <hr>
                        {{-- MAKLUMAT PENTADBIR (Dah Betul) --}}
                        <div class="mb-3">
                            <label class="font-weight-bold">Nama Pentadbir Tanah</label>
                            <input type="text" name="nama_pentadbir_tanah" class="form-control" value="{{ $data->nama_pentadbir_tanah }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">No. KP Pentadbir</label>
                            <input type="text" name="ic_pentadbir" class="form-control" value="{{ $data->ic_pentadbir }}" required>
                        </div>

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