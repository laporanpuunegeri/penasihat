@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pendaftaran Borang 16G (Seksyen 261)</h1>
        <a href="{{ route('permohonan.seksyen261') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen261.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Bahagian 1: Maklumat Saman & Siasatan --}}
            <div class="col-lg-6">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">1. Maklumat Saman & Siasatan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Fail</label>
                            <input type="text" name="no_fail" class="form-control" placeholder="PTAG. B1/06/06/2020(2024)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Penggadai (Chargor)</label>
                            <input type="text" name="nama_penggadai" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Alamat Penggadai</label>
                            <textarea name="alamat_penggadai" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pemegang Gadai (Chargee)</label>
                            <input type="text" name="nama_pemegang_gadai" class="form-control" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tempat Siasatan</label>
                            <input type="text" name="tempat_siasatan" class="form-control" value="Bilik Perbicaraan Pejabat Tanah Melaka Tengah" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Tarikh Siasatan</label>
                                <input type="date" name="tarikh_siasatan" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Masa Siasatan</label>
                                <input type="time" name="masa_siasatan" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bahagian 2: Maklumat Tanah (Jadual) --}}
            <div class="col-lg-6">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah & Gadaian</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Bandar/Pekan/Mukim</label>
                                <input type="text" name="mukim" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Lot/Petak/PT</label>
                                <input type="text" name="no_lot" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Jenis Hakmilik</label>
                                <input type="text" name="jenis_hakmilik" class="form-control" placeholder="GMM / GRN / PN" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">No. Hakmilik</label>
                                <input type="text" name="no_hakmilik" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Bahagian Tanah (Jika ada)</label>
                            <input type="text" name="bahagian_tanah" class="form-control" value="1/1 bhgn.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Berdaftar Gadaian</label>
                            <input type="text" name="no_daftar_gadaian" class="form-control" placeholder="Contoh: 0401SC2022004435" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pentadbir (Penandatangan)</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="MOHD HAIRY BIN JAPAH">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow mt-3">
                            <i class="fas fa-save"></i> Simpan Borang 16G
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection