@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Borang 16H (Seksyen 263)</h1>
        <a href="{{ route('permohonan.seksyen263') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen263.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">1. Maklumat Lelongan & Perintah</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Fail</label>
                                <input type="text" name="no_fail" class="form-control" placeholder="0402AUC2025000019" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tarikh Perintah</label>
                                <input type="date" name="tarikh_perintah" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Nama Pemegang Gadaian (Chargee)</label>
                            <input type="text" name="nama_pemegang_gadai" class="form-control" placeholder="Public Islamic Bank Berhad" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tarikh Lelongan</label>
                                <input type="date" name="tarikh_lelongan" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold">Hari</label>
                                <input type="text" name="hari_lelongan" class="form-control" placeholder="Khamis" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold">Masa</label>
                                <input type="time" name="masa_lelongan" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Tempat Lelongan</label>
                            <input type="text" name="tempat_lelongan" class="form-control" value="Pejabat Daerah Dan Tanah Jasin, Melaka" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Harga Rizab (RM)</label>
                                <input type="number" step="0.01" name="harga_rizab" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Deposit 10% (RM)</label>
                                <input type="number" step="0.01" name="deposit_sepuluh_peratus" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Amaun Hutang (RM)</label>
                                <input type="number" step="0.01" name="amaun_hutang" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah & Jadual</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="font-weight-bold">Mukim</label><input type="text" name="mukim" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="font-weight-bold">No. Lot</label><input type="text" name="no_lot" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="font-weight-bold">Jenis Hakmilik</label><input type="text" name="jenis_hakmilik" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="font-weight-bold">No. Hakmilik</label><input type="text" name="no_hakmilik" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label class="font-weight-bold">Bahagian Tanah</label><input type="text" name="bahagian_tanah" class="form-control" value="1/1 bhgn"></div>
                        <div class="mb-3"><label class="font-weight-bold">No. Berdaftar Gadaian</label><input type="text" name="no_daftar_gadaian" class="form-control" required></div>
                        <hr>
                        <div class="mb-3"><label class="font-weight-bold">Nama Pentadbir (IC)</label><input type="text" name="nama_pentadbir_tanah" class="form-control" placeholder="SITI NAZURAH BINTI SAHADUN" required></div>
                        <div class="mb-3"><label class="font-weight-bold">IC Pentadbir</label><input type="text" name="ic_pentadbir" class="form-control" placeholder="890408-01-5060" required></div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow mt-3"><i class="fas fa-save"></i> Simpan Borang 16H</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection