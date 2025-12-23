@extends('layouts.agensi')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kemaskini Borang 10D (ID: #{{ $data->id }})</h1>
        <a href="{{ route('permohonan.seksyen168') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen168.update', $data->id) }}" method="POST">
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
                    <div class="card-header py-3 border-left-warning">
                        <h6 class="m-0 font-weight-bold text-warning">Perihal Kehilangan (Penting)</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Sebab Permohonan</label>
                            <select name="sebab_permohonan" class="form-control">
                                <option value="Dokumen hakmilik yang dikeluarkan telah hilang." {{ $data->sebab_permohonan == 'Dokumen hakmilik yang dikeluarkan telah hilang.' ? 'selected' : '' }}>Dokumen hakmilik yang dikeluarkan telah hilang.</option>
                                <option value="Dokumen hakmilik yang dikeluarkan telah rosak sebahagian/sepenuhnya." {{ $data->sebab_permohonan == 'Dokumen hakmilik yang dikeluarkan telah rosak sebahagian/sepenuhnya.' ? 'selected' : '' }}>Dokumen hakmilik yang dikeluarkan telah rosak.</option>
                                <option value="Dokumen hakmilik sebahagiannya telah musnah." {{ $data->sebab_permohonan == 'Dokumen hakmilik sebahagiannya telah musnah.' ? 'selected' : '' }}>Dokumen hakmilik sebahagiannya telah musnah.</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-info">
                        <h6 class="m-0 font-weight-bold text-info">Pentadbiran</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pentadbir Tanah</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="{{ $data->nama_pentadbir }}">
                        </div>
                        {{-- Dropdown Status telah dibuang atas permintaan user --}}
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
                                    <option value="Pajakan Negeri" {{ $data->jenis_hakmilik == 'Pajakan Negeri' ? 'selected' : '' }}>Pajakan Negeri</option>
                                    <option value="Geran Mukim" {{ $data->jenis_hakmilik == 'Geran Mukim' ? 'selected' : '' }}>Geran Mukim</option>
                                    <option value="Geran" {{ $data->jenis_hakmilik == 'Geran' ? 'selected' : '' }}>Geran</option>
                                    <option value="H.S.(D)" {{ $data->jenis_hakmilik == 'H.S.(D)' ? 'selected' : '' }}>H.S.(D)</option>
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
                                <label class="form-label font-weight-bold">Luas (Area) <span class="text-danger">*</span></label>
                                <input type="text" name="luas" class="form-control" value="{{ $data->luas }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Bandar / Pekan / Mukim <span class="text-danger">*</span></label>
                                <input type="text" name="mukim" class="form-control" value="{{ $data->mukim }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Daerah <span class="text-danger">*</span></label>
                                <input type="text" name="daerah" class="form-control" value="{{ $data->daerah }}" placeholder="Contoh: MELAKA TENGAH" required>
                            </div>
                        </div>
                        
                         <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Fail (Jadual) <span class="text-danger">*</span></label>
                            <input type="text" name="no_fail" class="form-control" value="{{ $data->no_fail }}" required>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body text-right">
                        <button type="submit" class="btn btn-success btn-lg shadow">
                            <i class="fas fa-save"></i> Kemaskini Rekod
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection