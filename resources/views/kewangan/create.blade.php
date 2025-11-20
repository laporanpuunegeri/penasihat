@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Rekod Kewangan</h1>
        <a href="{{ route('kewangan.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Isi Maklumat Kewangan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('kewangan.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kod_utama" class="form-label font-weight-bold">Kategori Utama</label>
                        <select name="kod_utama" class="form-control" required>
                            <option value="">-- Sila Pilih --</option>
                            <option value="10000">10000 - EMOLUMEN</option>
                            <option value="20000">20000 - PERKHIDMATAN & BEKALAN</option>
                            <option value="30000">30000 - ASET</option>
                            <option value="40000">40000 - PEMBERIAN & KENAAN BAYARAN TETAP</option>
                            <option value="50000">50000 - PERBELANJAAN LAIN</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="kod_objek" class="form-label font-weight-bold">Kod Objek (Contoh: 21000)</label>
                        <input type="number" name="kod_objek" class="form-control" required placeholder="Masukkan Kod Objek">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="butiran" class="form-label font-weight-bold">Butiran / Jenis Perbelanjaan</label>
                    <input type="text" name="butiran" class="form-control" required placeholder="Contoh: Gaji Kakitangan">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="peruntukan" class="form-label font-weight-bold text-primary">Jumlah Peruntukan (Siling) RM</label>
                        <input type="number" step="0.01" name="peruntukan" class="form-control" required placeholder="0.00">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="belanja" class="form-label font-weight-bold text-danger">Jumlah Belanja (Semasa) RM</label>
                        <input type="number" step="0.01" name="belanja" class="form-control" value="0" placeholder="0.00">
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary btn-block">Simpan Rekod</button>
            </form>
        </div>
    </div>

</div>
@endsection