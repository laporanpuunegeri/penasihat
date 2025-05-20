@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Daftar Laporan Lain-Lain Tugasan</h3>

    <form action="{{ route('lainlaintugasan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="perihal" class="form-label">Perihal Tugasan - Mohon untuk edit semula</label>
            <input list="senarai-perihal" name="tugasan[0][perihal]" id="perihal" class="form-control" required>
            <datalist id="senarai-perihal">
                <option value="Perbincangan dengan (nama pegawai dan jawatan)">
                <option value="Perbincangan melalui Online Meeting bersama (nama pegawai dan jawatan)">
            </datalist>
        </div>

        <div class="mb-3">
            <label for="tarikh" class="form-label">Tarikh</label>
            <input type="date" name="tugasan[0][tarikh]" id="tarikh" class="form-control" required>
        </div>

        <div class="mb-4">
            <label for="tindakan" class="form-label">Tindakan</label>
            <select name="tugasan[0][tindakan]" id="tindakan" class="form-select" required>
                <option value="">-- Sila Pilih --</option>
                <option value="Telah Hadir">Telah Hadir</option>
                <option value="Telah Bincang">Telah Bincang</option>
            </select>
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
