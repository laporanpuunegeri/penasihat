@extends('layouts.agensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Senarai Seksyen 263 (Borang 16H)</h1>
        <a href="{{ route('permohonan.seksyen263.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Pendaftaran Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">Bil</th>
                            <th>No. Fail / Pemegang Gadai</th>
                            <th>Maklumat Lelongan</th>
                            <th>Harga Rizab</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($senarai as $data)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $data->no_fail }}</strong><br>
                                    <small class="text-primary font-weight-bold text-uppercase">{{ $data->nama_pemegang_gadai }}</small>
                                </td>
                                <td>
                                    <small><strong>Tarikh:</strong> {{ \Carbon\Carbon::parse($data->tarikh_lelongan)->format('d/m/Y') }}</small><br>
                                    <small><strong>Tempat:</strong> {{ $data->tempat_lelongan }}</small>
                                </td>
                                <td class="font-weight-bold text-danger">RM {{ number_format($data->harga_rizab, 2) }}</td>
                                <td class="text-center font-weight-bold">
                                    @if($data->status == 'Baru' || $data->status == '')
                                        <span class="text-warning">BARU</span>
                                    @elseif($data->status == 'Semakan')
                                        <span class="text-info">SEMAKAN</span>
                                    @else
                                        <span class="text-success">{{ strtoupper($data->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('permohonan.seksyen263.show', $data->id) }}" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i> Lihat</a>
                                        <a href="{{ route('permohonan.seksyen263.edit', $data->id) }}" class="btn btn-outline-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">Tiada rekod Borang 16H ditemui.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection