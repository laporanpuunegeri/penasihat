@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pendaftaran Borang 10H</h1>
    <form action="{{ route('permohonan.seksyen175d.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">1. Maklumat Asas & Pemilik</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">No. Fail</label>
                            <input type="text" name="no_fail" class="form-control" placeholder="PTAG. BI/06/06/2020 (2024)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Tuan Punya Berdaftar</label>
                            <input type="text" name="nama_pemilik" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. KP Pemilik</label>
                            <input type="text" name="no_kp_pemilik" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Butir Kepentingan (Jika ada)</label>
                            <input type="text" name="bahagian_tanah" class="form-control" value="1/1 bhgn.">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3"><label>Jenis Hakmilik</label><input type="text" name="jenis_hakmilik" class="form-control" required></div>
                            <div class="col-6 mb-3"><label>No. Hakmilik</label><input type="text" name="no_hakmilik" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3"><label>No. Lot</label><input type="text" name="no_lot" class="form-control" required></div>
                            <div class="col-6 mb-3"><label>Luas</label><input type="text" name="luas" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3"><label>Mukim</label><input type="text" name="mukim" class="form-control" required></div>
                            <div class="col-6 mb-3"><label>Daerah</label><input type="text" name="daerah" class="form-control" required></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan Borang 10H</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection