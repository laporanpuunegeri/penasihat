@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Senarai Laporan Lain-Lain Tugasan</h3>

    {{-- Tapis Ikut Bulan --}}
    <form method="GET" action="{{ route('lainlaintugasan.index') }}" class="row g-3 mb-3 align-items-end">
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
            <a href="{{ route('lainlaintugasan.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
        <div class="col text-end">
            <a href="{{ route('lainlaintugasan.create') }}" class="btn btn-success">+ Daftar Baharu</a>
        </div>
    </form>

    {{-- Jadual Paparan --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light text-center">
            <tr>
                <th>BIL</th>
                <th>TARIKH DAFTAR</th>
                <th>PERIHAL TUGASAN</th>
                <th>TARIKH</th>
                <th>TINDAKAN</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            @php $user = auth()->user(); @endphp
            @forelse ($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                    <td class="text-start">{{ $item->perihal }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tarikh)->format('d/m/Y') }}</td>
                    <td class="text-start">{{ $item->tindakan }}</td>
                    <td class="text-center">
                        @if ($user->role === 'pa' && $user->negeri === $item->negeri || $user->id === $item->user_id)
                            <a href="{{ route('lainlaintugasan.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('lainlaintugasan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda pasti untuk padam?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Padam</button>
                            </form>
                        @else
                            <span class="text-muted fst-italic">Semakan Sahaja</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Tiada rekod ditemui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
