@extends('layouts.agensi') 
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kemaskini Pelantikan (Seksyen 12)</h1>
        <a href="{{ route('permohonan.seksyen12') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning text-dark">
            <h6 class="m-0 font-weight-bold">Borang Kemaskini Maklumat</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('seksyen12.update', $data->id) }}" method="POST">
                @csrf
                @method('PUT') 
                
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="font-weight-bold text-dark">Nama Pegawai</label>
                        <input type="text" name="nama" value="{{ $data->nama }}" class="form-control text-uppercase shadow-sm" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">No. Kad Pengenalan</label>
                        <input type="text" name="no_kp" value="{{ $data->no_kp }}" class="form-control shadow-sm" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Jawatan Hakiki (BM)</label>
                        <input type="text" name="jawatan" value="{{ $data->jawatan }}" class="form-control shadow-sm" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Official Designation (BI)</label>
                        <input type="text" name="position" value="{{ $data->position }}" class="form-control shadow-sm" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Pelantikan (BM)</label>
                        <input type="text" name="pelantikan_bm" value="{{ $data->pelantikan_bm }}" class="form-control shadow-sm" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Appointment (BI)</label>
                        <input type="text" name="pelantikan_bi" value="{{ $data->pelantikan_bi }}" class="form-control shadow-sm" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Tarikh Lantikan</label>
                        <input type="date" name="tarikh_lantikan" value="{{ $data->tarikh_lantikan }}" class="form-control shadow-sm" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Tarikh Tandatangan (TT)</label>
                        <input type="date" name="tarikh_tt" value="{{ $data->tarikh_tt }}" class="form-control shadow-sm" required>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <label class="font-weight-bold text-dark">No. Fail</label>
                        <input type="text" name="no_fail" value="{{ $data->no_fail }}" class="form-control shadow-sm" required>
                    </div>
                </div>
                
                <div class="border-top pt-3 text-right">
                    <a href="{{ route('permohonan.seksyen12') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 shadow">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection