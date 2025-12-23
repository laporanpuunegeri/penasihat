@extends('layouts.agensi') 

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Senarai Permohonan Seksyen 62</h1>
        <a href="{{ route('permohonan.seksyen62.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Permohonan Baru
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
                            <th>Tujuan (BM/BI)</th>
                            <th width="15%" class="text-center">Tarikh Hantar</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekod as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ $item->tujuan_bm }}</div>
                                <small class="text-muted"><em>{{ $item->tujuan_bi }}</em></small>
                            </td>
                            <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                            <td class="text-center font-weight-bold">
                                {{-- Tulisan berwarna & bold ikut standard Seksyen 175D --}}
                                @if($item->status == 'Baru' || $item->status == '')
                                    <span class="text-warning">BARU</span>
                                @elseif($item->status == 'Semakan')
                                    <span class="text-info">SEMAKAN</span>
                                @elseif($item->status == 'Selesai')
                                    <span class="text-success">SELESAI</span>
                                @else
                                    <span class="text-primary">{{ strtoupper($item->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    {{-- Butang Lihat --}}
                                    <a href="{{ route('permohonan.seksyen62.show', $item->id) }}" class="btn btn-outline-info btn-sm" title="Lihat">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                    {{-- Butang Edit --}}
                                    <a href="{{ route('permohonan.seksyen62.edit', $item->id) }}" class="btn btn-outline-warning btn-sm" title="Kemaskini">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Tiada rekod dijumpai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection