@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Senarai Laporan Semakan Undang-Undang</h1>
    <a href="#" class="btn btn-primary mb-3">Tambah</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tarikh</th>
                <th>Kategori</th>
                <th>Tajuk</th>
                <th>Isi Ringkas</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporans as $laporan)
                <tr>
                    <td>{{ $laporan->tarikh }}</td>
                    <td>{{ $laporan->kategori }}</td>
                    <td>{{ $laporan->tajuk }}</td>
                    <td>{{ $laporan->isi_ringkas }}</td>
                    <td>
                        <a href="{{ route('laporan.show', $laporan->id) }}" class="btn btn-info btn-sm">Lihat</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tiada rekod dipaparkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
