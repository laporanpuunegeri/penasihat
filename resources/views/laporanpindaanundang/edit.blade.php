@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">Kemaskini Laporan Pindaan Undang-Undang</h3>

    {{-- Papar mesej ralat validasi --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm p-4 border-0">
        <form method="POST" action="{{ route('laporanpindaanundang.update', $laporan->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="tajuk" class="form-label">Tajuk</label>
                <input type="text" class="form-control" id="tajuk" name="tajuk"
                       value="{{ old('tajuk', $laporan->tajuk) }}" required>
            </div>

            <div class="mb-3">
            <label for="tindakan" class="form-label">Tindakan</label>
            <select class="form-select" id="tindakan" name="tindakan" required>
                <option value="">-- Sila Pilih Tindakan --</option>
                <option value="Menggubal dan menyemak perundangan utama" {{ old('tindakan', $laporan->tindakan) == 'Menggubal dan menyemak perundangan utama' ? 'selected' : '' }}>
                    1. Menggubal dan menyemak perundangan utama
                </option>
                <option value="Menggubal dan menyemak perundangan subsidiari" {{ old('tindakan', $laporan->tindakan) == 'Menggubal dan menyemak perundangan subsidiari' ? 'selected' : '' }}>
                    2. Menggubal dan menyemak perundangan subsidiari
                </option>
                <option value="Semakan draf warta" {{ old('tindakan', $laporan->tindakan) == 'Semakan draf warta' ? 'selected' : '' }}>
                    3. Semakan draf warta
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <textarea name="status" id="status" class="form-control" rows="4" required>{{ old('status', $laporan->status) }}</textarea>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-send-check"></i> Hantar
            </button>
        </div>
    </form>
</div>
@endsection