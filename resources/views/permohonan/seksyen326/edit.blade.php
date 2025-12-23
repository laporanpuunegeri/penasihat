@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kemaskini Borang 19C (Seksyen 326)</h1>
        <a href="{{ route('permohonan.seksyen326') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen326.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- BAHAGIAN 1 --}}
            <div class="col-lg-7">
                <div class="card shadow mb-4 border-left-warning">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">1. Maklumat Notis & Kaveat</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Fail</label>
                                <input type="text" name="no_fail" class="form-control" value="{{ $data->no_fail }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tarikh Notis</label>
                                <input type="date" name="tarikh_notis" class="form-control" value="{{ $data->tarikh_notis ? $data->tarikh_notis->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Kepada (Nama Pengkaveat)</label>
                            <input type="text" name="nama_penerima" class="form-control" value="{{ $data->nama_penerima }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. KP Pengkaveat</label>
                                <input type="text" name="ic_penerima" class="form-control" value="{{ $data->ic_penerima }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Perserahan Kaveat</label>
                                <input type="text" name="no_perserahan_kaveat" class="form-control" value="{{ $data->no_perserahan_kaveat }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Alamat Pengkaveat</label>
                            <textarea name="alamat_penerima" class="form-control" rows="2" required>{{ $data->alamat_penerima }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Nama Pemohon (Yang Minta Potong)</label>
                            <input type="text" name="nama_pemohon" class="form-control" value="{{ $data->nama_pemohon }}" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAHAGIAN 2 (KEMAS & SELARI) --}}
            <div class="col-lg-5">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah & Pentadbir</h6>
                    </div>
                    <div class="card-body">
                        
                        {{-- KAWASAN --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Kawasan</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select name="jenis_kawasan" class="custom-select bg-light font-weight-bold" style="width: 110px;">
                                        <option value="Bandar" {{ $data->jenis_kawasan == 'Bandar' ? 'selected' : '' }}>Bandar</option>
                                        <option value="Pekan" {{ $data->jenis_kawasan == 'Pekan' ? 'selected' : '' }}>Pekan</option>
                                        <option value="Mukim" {{ $data->jenis_kawasan == 'Mukim' ? 'selected' : '' }}>Mukim</option>
                                    </select>
                                </div>
                                <input type="text" name="nama_kawasan" class="form-control" value="{{ $data->nama_kawasan }}" required>
                            </div>
                        </div>

                        {{-- JENIS & NO LOT --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Jenis & No. Lot</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select name="jenis_lot" class="custom-select bg-light font-weight-bold" style="width: 110px;">
                                        <option value="Lot" {{ $data->jenis_lot == 'Lot' ? 'selected' : '' }}>Lot</option>
                                        <option value="Petak" {{ $data->jenis_lot == 'Petak' ? 'selected' : '' }}>Petak</option>
                                        <option value="PT" {{ $data->jenis_lot == 'PT' ? 'selected' : '' }}>PT</option>
                                        <option value="Plot" {{ $data->jenis_lot == 'Plot' ? 'selected' : '' }}>Plot</option>
                                    </select>
                                </div>
                                <input type="text" name="no_lot" class="form-control" value="{{ $data->no_lot }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Jenis Hakmilik</label>
                                <input type="text" name="jenis_hakmilik" class="form-control" value="{{ $data->jenis_hakmilik }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Hakmilik</label>
                                <input type="text" name="no_hakmilik" class="form-control" value="{{ $data->no_hakmilik }}" required>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="mb-3">
                            <label class="font-weight-bold">Nama Pentadbir</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="{{ $data->nama_pentadbir }}" required>
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