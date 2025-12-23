@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Senarai Seksyen 175D (Borang 10H)</h1>
        <a href="{{ route('permohonan.seksyen175d.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Pendaftaran Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Bil</th>
                            <th>No. Fail</th>
                            <th>Maklumat Tanah</th>
                            <th>Tuan Punya Berdaftar</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($senarai as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $data->no_fail }}</strong></td>
                                <td>
                                    {{ $data->jenis_hakmilik }} {{ $data->no_hakmilik }} (Lot {{ $data->no_lot }})<br>
                                    <small>{{ $data->mukim }}, {{ $data->daerah }}</small>
                                </td>
                                <td>{{ $data->nama_pemilik }}<br><small>{{ $data->no_kp_pemilik }}</small></td>
                                <td class="text-center font-weight-bold">
                                    {{-- Menggunakan text color supaya tulisan berwarna dan nampak jelas atas putih --}}
                                    @if($data->status == 'Baru')
                                        <span class="text-warning">BARU</span>
                                    @elseif($data->status == 'Proses')
                                        <span class="text-info">DALAM PROSES</span>
                                    @else
                                        <span class="text-success">{{ strtoupper($data->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        {{-- Butang Lihat/Cetak --}}
                                        <a href="{{ route('permohonan.seksyen175d.show', $data->id) }}" class="btn btn-outline-info btn-sm" title="Lihat/Cetak">
                                            <i class="fas fa-file-alt"></i> Lihat
                                        </a>
                                        {{-- Butang Edit --}}
                                        <a href="{{ route('permohonan.seksyen175d.edit', $data->id) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        {{-- Butang Delete Telah Dibuang --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">Tiada rekod ditemui.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection