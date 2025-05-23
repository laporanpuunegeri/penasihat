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
        <div class="col text-end">
            <a href="{{ url('/kestatatertib/create') }}" class="btn btn-success">+ Daftar Baharu</a>
        </div>
    </form>

    {{-- Jadual --}}
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-secondary">
                <tr>
                    <th>BIL</th>
                    <th>Tarikh Daftar</th>
                    <th>Tarikh Terima</th>
                    <th>Kategori</th>
                    <th>Fakta Ringkasan</th>
                    <th>Isu</th>
                    <th>Ringkasan Pandangan</th>
                    <th>Status / Tarikh Selesai</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @php $user = auth()->user(); @endphp
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
                            @if ($user->role === 'pa' || $laporan->user_id === $user->id)
                                <a href="{{ url('/kestatatertib/' . $laporan->id . '/edit') }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                                <form action="{{ url('/kestatatertib/' . $laporan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda pasti untuk padam?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Padam</button>
                                </form>
                            @else
                                <span class="text-muted fst-italic">Semakan Sahaja</span>
                            @endif
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
