@extends('layouts.app')

@section('content')
<div class="container">
    <h5 class="fw-bold text-center text-uppercase mb-4">
        Kemaskini Laporan Penyemakan Rang Undang-Undang / Perundangan Subsidiari Substantif<br>
        <small class="text-uppercase">(Termasuk Cetakan Semula dan Pembaharuan Undang-Undang)</small>
    </h5>

    <div class="card shadow-sm p-4 border-0">
        <form method="POST" action="{{ route('laporansemakanundang.update', $laporan->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="tajuk" class="form-label">Tajuk Rang Undang-Undang / Perundangan Subsidiari Substantif</label>
                <input type="text" class="form-control" id="tajuk" name="tajuk" value="{{ old('tajuk', $laporan->tajuk) }}" required>
            </div>

            <div class="mb-3">
                <label for="tindakan" class="form-label">Tindakan</label>
                <textarea class="form-control" id="tindakan" name="tindakan" rows="3" required>{{ old('tindakan', $laporan->tindakan) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="">-- Sila Pilih --</option>
                    @foreach(['Dalam Penyediaan', 'Semakan', 'Diluluskan', 'Dibentangkan', 'Berkuat Kuasa'] as $statusOption)
                        <option value="{{ $statusOption }}" {{ old('status', $laporan->status) == $statusOption ? 'selected' : '' }}>
                            {{ $statusOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="text-end">
            <button type="submit" class="btn btn-primary">Kemaskini</button>
            </div>
        </form>
    </div>
</div>
@endsection
