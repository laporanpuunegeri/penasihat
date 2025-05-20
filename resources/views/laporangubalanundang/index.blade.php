@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h5 class="fw-bold text-center text-uppercase mb-4">
        Laporan Penggubalan RUU / Perundangan Subsidiari Substantif<br>
        <small class="text-uppercase">(Termasuk Cetakan Semula dan Pembaharuan Undang-Undang)</small>
    </h5>

    {{-- Tapisan ikut bulan daftar --}}
    <form method="GET" action="{{ url('/laporangubalanundang') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-auto">
            <label for="bulan" class="form-label mb-0">Tapis Ikut Bulan Daftar:</label>
        </div>
        <div class="col-auto">
            <select name="bulan" id="bulan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Bulan --</option>
                @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April', 5 => 'Mei', 6 => 'Jun',
                          7 => 'Julai', 8 => 'Ogos', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember']
                          as $num => $nama)
                    <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <a href="{{ url('/laporangubalanundang') }}" class="btn btn-outline-secondary">Reset</a>
        </div>

        <div class="col text-end">
            <a href="{{ url('/laporangubalanundang/create') }}" class="btn btn-success">+ Daftar Baharu</a>
        </div>
    </form>

    {{-- Jadual laporan --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-secondary">
                <tr>
                    <th>BIL</th>
                    <th>Tarikh Daftar</th>
                    <th class="text-start">Tajuk RUU / Perundangan Subsidiari</th>
                    <th class="text-start">Tindakan</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $laporan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $laporan->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-start">{{ $laporan->tajuk }}</td>
                        <td class="text-start">{{ $laporan->tindakan }}</td>
                        <td>{{ $laporan->status }}</td>
                        <td>
                            <a href="{{ url('/laporangubalanundang/' . $laporan->id . '/edit') }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                            <form action="{{ url('/laporangubalanundang/' . $laporan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda pasti untuk padam?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Padam</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Tiada data direkodkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
