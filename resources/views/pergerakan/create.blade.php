@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="fw-bold text-uppercase mb-4">Daftar Pergerakan Pegawai</h3>

    <div class="card shadow-sm border-0 p-4">
        <form method="POST" action="{{ route('pergerakan.store') }}">
            @csrf

            <div class="mb-3">
                <label for="tarikh" class="form-label">Tarikh Pergerakan</label>
                <input type="date" name="tarikh" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="jenis" class="form-label">Jenis Pergerakan</label>
                <select name="jenis" class="form-select" required>
                    <option value="">-- Sila Pilih --</option>
                    <option value="Kursus">Kursus</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Bicara Kes Jenayah">Bicara Kes Jenayah</option>
                    <option value="Bicara Kes Sivil">Bicara Kes Sivil</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan (Jika Ada)</label>
                <textarea name="catatan" class="form-control" rows="3"></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
