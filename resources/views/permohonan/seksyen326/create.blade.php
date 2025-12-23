@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Borang 19C (Seksyen 326)</h1>
        <a href="{{ route('permohonan.seksyen326') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen326.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- BAHAGIAN 1: MAKLUMAT NOTIS --}}
            <div class="col-lg-7">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">1. Maklumat Notis & Kaveat</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Fail</label>
                                <input type="text" name="no_fail" class="form-control" placeholder="PTMT. B1/138/B(16/2025)" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tarikh Notis</label>
                                <input type="date" name="tarikh_notis" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Kepada (Nama Pengkaveat)</label>
                            <input type="text" name="nama_penerima" class="form-control" placeholder="Yew Kok Hong" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. KP Pengkaveat</label>
                                <input type="text" name="ic_penerima" class="form-control" placeholder="720802-04-5047">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Perserahan Kaveat</label>
                                <input type="text" name="no_perserahan_kaveat" class="form-control" placeholder="0401B2022003460" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Alamat Pengkaveat</label>
                            <textarea name="alamat_penerima" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Nama Pemohon (Yang Minta Potong)</label>
                            <input type="text" name="nama_pemohon" class="form-control" placeholder="I Kitch Sdn Bhd" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAHAGIAN 2: MAKLUMAT TANAH (KEMAS & SELARI) --}}
            <div class="col-lg-5">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success">2. Maklumat Tanah</h6></div>
                    <div class="card-body">
                        
                        {{-- KAWASAN --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Kawasan (*Pilih satu)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select name="jenis_kawasan" class="custom-select bg-light font-weight-bold" style="width: 110px;">
                                        <option value="Bandar">Bandar</option>
                                        <option value="Pekan">Pekan</option>
                                        <option value="Mukim" selected>Mukim</option>
                                    </select>
                                </div>
                                <input type="text" name="nama_kawasan" class="form-control" placeholder="Nama Tempat (cth: Cheng)" required>
                            </div>
                        </div>

                        {{-- JENIS & NO LOT --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Jenis & No. Lot (*Pilih satu)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select name="jenis_lot" class="custom-select bg-light font-weight-bold" style="width: 110px;">
                                        <option value="Lot" selected>Lot</option>
                                        <option value="Petak">Petak</option>
                                        <option value="PT">PT</option>
                                        <option value="Plot">Plot</option>
                                    </select>
                                </div>
                                <input type="text" name="no_lot" class="form-control" placeholder="No. Lot (cth: 3292)" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Jenis Hakmilik</label>
                                <input type="text" name="jenis_hakmilik" class="form-control" placeholder="GM" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">No. Hakmilik</label>
                                <input type="text" name="no_hakmilik" class="form-control" placeholder="1079" required>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="mb-3">
                            <label class="font-weight-bold">Nama Pentadbir</label>
                            <input type="text" name="nama_pentadbir" class="form-control" value="MOHD HAIRY BIN JAPAH" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow mt-3"><i class="fas fa-save"></i> Simpan Borang 19C</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection