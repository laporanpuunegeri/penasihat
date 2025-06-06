@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold text-uppercase">Senarai Kes Tatatertib</h3>

    {{-- Tapisan ikut bulan --}}
    <form method="GET" action="{{ url('/kestatatertib') }}" class="row g-3 mb-3 align-items-end">
        <div class="col-auto">
            <label for="bulan" class="form-label">Tapis Ikut Bulan:</label>
        </div>
        <div class="col-auto">
            <select name="bulan" id="bulan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Bulan --</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-auto">
            <a href="{{ url('/kestatatertib') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    {{-- Butang daftar baharu --}}
    <a href="{{ url('/kestatatertib/create') }}" class="btn btn-success mb-3">+ Daftar Baharu</a>

    {{-- Jadual laporan --}}
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-secondary">
                <tr>
                    <th>BIL</th>
                    <th>TARIKH DAFTAR</th>
                    <th>TARIKH TERIMA</th>
                    <th>KATEGORI</th>
                    <th>FAKTA RINGKASAN</th>
                    <th>ISU</th>
                    <th>RINGKASAN PANDANGAN</th>
                    <th>STATUS / TARIKH SELESAI</th>
                    <th>TINDAKAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $laporan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ optional($laporan->tarikh_daftar)->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ optional($laporan->tarikh_terima)->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $laporan->kategori }}</td>
                        <td class="text-start">{{ $laporan->fakta_ringkasan }}</td>
                        <td class="text-start">{{ $laporan->isu }}</td>
                        <td class="text-start">{{ $laporan->ringkasan_pandangan }}</td>
                        <td class="text-start">
                            {{ $laporan->status ?? '-' }}
                            @if ($laporan->tarikh_selesai)
                                <br><small class="text-muted">Selesai: {{ \Carbon\Carbon::parse($laporan->tarikh_selesai)->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ url('/kestatatertib/' . $laporan->id . '/edit') }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                            <form action="{{ url('/kestatatertib/' . $laporan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda pasti untuk padam?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Padam</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-muted fst-italic">Tiada data direkodkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
