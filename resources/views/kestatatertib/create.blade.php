@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold text-uppercase">Daftar Kes Tatatertib</h3>

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
        <form method="POST" action="{{ route('kestatatertib.store') }}">
            @csrf

            <div class="mb-3">
                <label for="tarikh_terima" class="form-label">Tarikh Terima</label>
                <input type="date" class="form-control" name="tarikh_terima" required>
            </div>

            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select name="kategori" class="form-select" required>
                    <option value="">-- Sila Pilih --</option>
                    <option value="PRIMA FACIE">PRIMA FACIE</option>
                    <option value="SURCAJ">SURCAJ</option>
                    <option value="PENAMATAN">PENAMATAN</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="fakta_ringkasan" class="form-label">Fakta Ringkasan</label>
                <textarea class="form-control" name="fakta_ringkasan" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label for="isu" class="form-label">Isu</label>
                <textarea class="form-control" name="isu" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label for="ringkasan_pandangan" class="form-label">Ringkasan Pandangan</label>
                <textarea class="form-control" name="ringkasan_pandangan" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" class="form-control" name="status">
            </div>

            <div class="mb-3">
                <label for="tarikh_selesai" class="form-label">Tarikh Selesai</label>
                <input type="date" class="form-control" name="tarikh_selesai">
            </div>

            @if (auth()->user()?->role === 'user')
                <div class="form-check mb-3">
                    <input type="checkbox" name="hantar_kepada_boss" value="1" id="hantar_kepada_boss" class="form-check-input">
                    <label for="hantar_kepada_boss" class="form-check-label">Saya hadir bersama YB Penasihat</label>
                </div>
            @endif

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
