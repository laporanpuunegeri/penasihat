@extends('layouts.app')

@section('content')
<div class="container">
    <h5 class="fw-bold text-center text-uppercase mb-4">
        Daftar Laporan Penyemakan Rang Undang-Undang / Perundangan Subsidiari Substantif / Pemberitahuan Awam (G.N)<br>
        <small class="text-uppercase">(Termasuk Cetakan Semula dan Pembaharuan Undang-Undang)</small>
    </h5>

    <div class="card shadow-sm p-4 border-0">
        <form method="POST" action="{{ url('/laporansemakanundang') }}">
            @csrf

            <div class="mb-3">
                <label for="tajuk" class="form-label">Tajuk</label>
<select name="tajuk" id="tajuk" class="form-select" required>
    <option value="">-- Sila Pilih --</option>
    <option value="Rang Undang-Undang"
        {{ old('tajuk') == 'Rang Undang-Undang' ? 'selected' : '' }}>
        Rang Undang-Undang
    </option>
    <option value="Perundangan Subsidiari Substantif"
        {{ old('tajuk') == 'Perundangan Subsidiari Substantif' ? 'selected' : '' }}>
        Perundangan Subsidiari Substantif
    </option>
        <option value="Pemberitahuan Awam (G.N)"
        {{ old('tajuk') == 'Pemberitahuan Awam (G.N)' ? 'selected' : '' }}>
        Pemberitahuan Awam (G.N)
    </option>
</select>

            <div class="mb-3">
                <label for="tindakan" class="form-label">Tindakan</label>
                <textarea class="form-control" id="tindakan" name="tindakan" rows="3" required>{{ old('tindakan') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="">-- Sila Pilih --</option>
                    @foreach(['Dalam Penyediaan', 'Semakan', 'Diluluskan', 'Dibentangkan', 'Berkuat Kuasa', 'Selesai'] as $statusOption)
                        <option value="{{ $statusOption }}" {{ old('status') == $statusOption ? 'selected' : '' }}>
                            {{ $statusOption }}
                        </option>
                    @endforeach
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
