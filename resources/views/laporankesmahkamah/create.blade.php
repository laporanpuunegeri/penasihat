@extends('layouts.app')

@section('content')
<div class="container-fluid px-5">
    <h3 class="mb-4">Daftar Laporan Kes Mahkamah</h3>

    <div class="card w-100 shadow-sm p-4">
        <form method="POST" action="{{ route('laporankesmahkamah.store') }}">
            @csrf

            <div class="mb-3">
                <label for="perkara" class="form-label">Perkara</label>
                <input type="text" name="perkara" id="perkara" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="jenis_kes" class="form-label">Jenis Kes</label>
                <input type="text" name="jenis_kes" id="jenis_kes" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tarikh_sebutan" class="form-label">Tarikh Sebutan</label>
                <input type="date" name="tarikh_sebutan" id="tarikh_sebutan" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="fakta_ringkas" class="form-label">Fakta Ringkas</label>
                <textarea name="fakta_ringkas" id="fakta_ringkas" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="isu" class="form-label">Isu</label>
                <textarea name="isu" id="isu" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="skop_tugas" class="form-label">Skop Tugas</label>
                <textarea name="skop_tugas" id="skop_tugas" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="ringkasan_hujahan" class="form-label">Ringkasan Hujahan</label>
                <textarea name="ringkasan_hujahan" id="ringkasan_hujahan" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" name="status" id="status" class="form-control">
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
