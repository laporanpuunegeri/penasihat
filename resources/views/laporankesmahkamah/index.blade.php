@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mb-4">Senarai Laporan Kes Mahkamah</h3>

    {{-- Tapisan ikut bulan --}}
    <form method="GET" class="row g-3 mb-3 align-items-end">
        <div class="col-md-3">
            <label for="bulan" class="form-label">Tapis Ikut Bulan:</label>
            <select name="bulan" id="bulan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih Bulan --</option>
                @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April', 5 => 'Mei', 6 => 'Jun',
                          7 => 'Julai', 8 => 'Ogos', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember']
                          as $num => $nama)
                    <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <a href="{{ route('laporankesmahkamah.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('laporankesmahkamah.create') }}" class="btn btn-success">+ Daftar Baharu</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-secondary">
                <tr>
                    <th>BIL</th>
                    <th>Tarikh Daftar</th>
                    <th>*Jenis Kes / Pihak-Pihak</th>
                    <th>Tarikh Sebutan / Bicara</th>
                    <th>Fakta Ringkas</th>
                    <th>Isu</th>
                    <th>** Skop Tugas</th>
                    <th>Ringkasan Hujahan</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->jenis_kes }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tarikh_sebutan)->format('d/m/Y') }}</td>
                        <td class="text-start">{{ $item->fakta_ringkas }}</td>
                        <td class="text-start">{{ $item->isu }}</td>
                        <td class="text-start">{{ $item->skop_tugas }}</td>
                        <td class="text-start">{{ $item->ringkasan_hujahan }}</td>
                        <td>{{ $item->status }}</td>
                        <td>
                            @if (auth()->user()->role === 'pa' || auth()->id() === $item->user_id)
                                <a href="{{ route('laporankesmahkamah.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('laporankesmahkamah.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Padam laporan ini?')">Padam</button>
                                </form>
                            @else
                                <span class="text-muted fst-italic">Untuk Semakan Sahaja</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-muted">Tiada laporan direkodkan untuk bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
