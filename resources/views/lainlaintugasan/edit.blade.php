
@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Kemaskini Laporan Lain-Lain Tugasan</h3>

    <form action="{{ route('lainlaintugasan.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="perihal" class="form-label">Perihal Tugasan</label>
            <input list="senarai-perihal" name="perihal" id="perihal" class="form-control" required value="{{ $data->perihal }}">
            <datalist id="senarai-perihal">
                <option value="Perbincangan dengan (nama pegawai dan jawatan)">
                <option value="Perbincangan melalui Online Meeting bersama (nama pegawai dan jawatan)">
            </datalist>
        </div>

        <div class="mb-3">
            <label for="tarikh" class="form-label">Tarikh</label>
            <input type="date" name="tarikh" id="tarikh" class="form-control" required value="{{ $data->tarikh }}">
        </div>

        <div class="mb-4">
            <label for="tindakan" class="form-label">Tindakan</label>
            <select name="tindakan" id="tindakan" class="form-select" required>
                <option value="">-- Sila Pilih --</option>
                <option value="Telah Hadir" {{ $data->tindakan == 'Telah Hadir' ? 'selected' : '' }}>Telah Hadir</option>
                <option value="Telah Bincang" {{ $data->tindakan == 'Telah Bincang' ? 'selected' : '' }}>Telah Bincang</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Kemaskini</button>
    </form>
</div>
@endsection
