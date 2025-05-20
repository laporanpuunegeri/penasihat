
@extends('layouts.app')

@section('content')
<div class="container">
    <h5 class="fw-bold text-center text-uppercase mb-4">
        Laporan Mesyuarat Yang Dihadiri<br>
        <small class="text-muted fw-normal">
            (*Sila nyatakan dan rujukan butiran berkenaan sekiranya pandangan undang-undang telah diberikan dalam <strong>LAMPIRAN I</strong>)<br>
            (**Bagi Kes Tatatertib, sila nyatakan bilangan kes yang telah didengar)
        </small>
    </h5>

    <div class="card shadow-sm p-4 border-0">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('laporanmesyuarat.update', $laporan->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="mesyuarat" class="form-label">Mesyuarat</label>
                <input type="text" name="mesyuarat" id="mesyuarat" class="form-control" value="{{ old('mesyuarat', $laporan->mesyuarat) }}" required>
            </div>

            <div class="mb-3">
                <label for="isu" class="form-label">Isu</label>
                <input type="text" name="isu" id="isu" class="form-control" value="{{ old('isu', $laporan->isu) }}" required>
            </div>

            <div class="mb-3">
                <label for="tarikh_mesyuarat" class="form-label">Tarikh Mesyuarat</label>
                <input type="date" name="tarikh_mesyuarat" id="tarikh_mesyuarat" class="form-control" value="{{ old('tarikh_mesyuarat', $laporan->tarikh_mesyuarat) }}" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" name="status" id="status" class="form-control" value="{{ old('status', $laporan->status) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Pandangan</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="pandangan" id="lisan" value="Lisan"
                        {{ old('pandangan', $laporan->pandangan) == 'Lisan' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="lisan">Lisan</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="pandangan" id="bertulis" value="Bertulis"
                        {{ old('pandangan', $laporan->pandangan) == 'Bertulis' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="bertulis">Bertulis</label>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Kemaskini
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
