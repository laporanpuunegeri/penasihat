@extends('layouts.agensi') 

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pelantikan Baru (Seksyen 12)</h1>
        <a href="{{ route('permohonan.seksyen12') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Senarai
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Borang Maklumat Pelantikan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('permohonan.seksyen12.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="font-weight-bold text-dark">Nama Pegawai (Seperti dalam MyKad)</label>
                        <input type="text" name="nama" class="form-control text-uppercase shadow-sm" placeholder="CONTOH: SITI SHAHNIZA BINTI HASMADI" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">No. Kad Pengenalan (IC)</label>
                        <input type="text" name="no_kp" class="form-control shadow-sm" placeholder="920603-04-XXXX" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Jawatan Hakiki (Bahasa Melayu)</label>
                        <input type="text" name="jawatan" class="form-control shadow-sm" placeholder="Contoh: Pegawai Tadbir Dan Diplomatik Gred M10 (M)" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Official Designation (English)</label>
                        <input type="text" name="position" class="form-control shadow-sm" placeholder="Contoh: Administrative And Diplomatic Officer Grade M10 (M)" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Pelantikan / Appointment (Bahasa Melayu)</label>
                        <input type="text" name="pelantikan_bm" class="form-control shadow-sm" placeholder="Contoh: Penolong Pentadbir Tanah Daerah Jasin" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Appointment / Designation (English)</label>
                        <input type="text" name="pelantikan_bi" class="form-control shadow-sm" placeholder="Contoh: Assistant Land Administrator of Jasin District" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Tarikh Lantikan (Berkuatkuasa)</label>
                        <input type="date" name="tarikh_lantikan" class="form-control shadow-sm" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Tarikh Tandatangan (TT)</label>
                        <input type="date" name="tarikh_tt" class="form-control shadow-sm" required>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <label class="font-weight-bold text-dark">No. Fail</label>
                        <input type="text" name="no_fail" class="form-control shadow-sm" placeholder="PTG(M)R/146/275; MMKN. P.15 21A/28/2025; PUNM.700-02/1/229" required>
                    </div>
                </div>
                
                <div class="border-top pt-3 text-right">
                    <button type="reset" class="btn btn-light border">Set Semula</button>
                    <button type="submit" class="btn btn-success px-4 shadow">
                        <i class="fas fa-save mr-1"></i> Simpan & Hantar Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection