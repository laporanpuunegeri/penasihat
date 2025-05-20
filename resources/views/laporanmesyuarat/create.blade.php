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

        <form method="POST" action="{{ route('laporanmesyuarat.store') }}">
            @csrf

            <div class="mb-3">
                <label for="mesyuarat" class="form-label">Mesyuarat</label>
                <input type="text" name="mesyuarat" id="mesyuarat" class="form-control" value="{{ old('mesyuarat') }}" required>
            </div>

            <div class="mb-3">
                <label for="isu" class="form-label">Isu</label>
                <input type="text" name="isu" id="isu" class="form-control" value="{{ old('isu') }}" required>
            </div>

            <div class="mb-3">
                <label for="tarikh_mesyuarat" class="form-label">Tarikh Mesyuarat</label>
                <input type="date" name="tarikh_mesyuarat" id="tarikh_mesyuarat" class="form-control" value="{{ old('tarikh_mesyuarat') }}" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" name="status" id="status" class="form-control" value="{{ old('status') }}" required>
            </div>

            <div class="mb-3">
    <label class="form-label d-block">Pandangan</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="pandangan" id="lisan" value="Lisan" required>
        <label class="form-check-label" for="lisan">Lisan</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="pandangan" id="bertulis" value="Bertulis" required>
        <label class="form-check-label" for="bertulis">Bertulis</label>
    </div>
</div>
            @php $authUser = auth()->user(); @endphp

            @if ($authUser->role === 'user')
                <div class="form-check mb-3">
                    <input type="checkbox" name="hantar_kepada_boss" value="1" id="hantar_kepada_boss" class="form-check-input">
                    <label for="hantar_kepada_boss" class="form-check-label">Saya hadir bersama YB Penasihat</label>
                </div>
            @endif

            @if ($authUser->role === 'pa')
                <input type="hidden" name="hantar_kepada_boss" value="1">
            @endif

            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-send-check"></i> Hantar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
