@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold text-uppercase">Kemaskini Kes Tatatertib</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-4 shadow-sm border-0">
        <form method="POST" action="{{ route('kestataterib.update', $laporan->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="tarikh_terima" class="form-label">Tarikh Terima</label>
                <input type="date" class="form-control" name="tarikh_terima" 
                    value="{{ old('tarikh_terima', optional($laporan->tarikh_terima)->format('Y-m-d')) }}" required>
            </div>

            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select name="kategori" class="form-select" required>
                    <option value="">-- Sila Pilih --</option>
                    <option value="PRIMA FACIE" {{ old('kategori', $laporan->kategori) == 'PRIMA FACIE' ? 'selected' : '' }}>PRIMA FACIE</option>
                    <option value="SURCAJ" {{ old('kategori', $laporan->kategori) == 'SURCAJ' ? 'selected' : '' }}>SURCAJ</option>
                    <option value="PENAMATAN" {{ old('kategori', $laporan->kategori) == 'PENAMATAN' ? 'selected' : '' }}>PENAMATAN</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="fakta_ringkasan" class="form-label">Fakta Ringkasan</label>
                <textarea class="form-control" name="fakta_ringkasan" rows="2" required>{{ old('fakta_ringkasan', $laporan->fakta_ringkasan) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="isu" class="form-label">Isu</label>
                <input type="text" class="form-control" name="isu" value="{{ old('isu', $laporan->isu) }}" required>
            </div>

            <div class="mb-3">
                <label for="ringkasan_pandangan" class="form-label">Ringkasan Pandangan</label>
                <textarea class="form-control" name="ringkasan_pandangan" rows="2">{{ old('ringkasan_pandangan', $laporan->ringkasan_pandangan) }}</textarea>
            </div>

            {{-- Asingkan medan status dan tarikh selesai --}}
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" class="form-control" name="status" value="{{ old('status', $laporan->status) }}">
            </div>

            <div class="mb-3">
                <label for="tarikh_selesai" class="form-label">Tarikh Selesai</label>
                <input type="date" class="form-control" name="tarikh_selesai" 
                    value="{{ old('tarikh_selesai', optional($laporan->tarikh_selesai)->format('Y-m-d')) }}">
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Kemaskini</button>
            </div>
        </form>
    </div>
</div>
@endsection
