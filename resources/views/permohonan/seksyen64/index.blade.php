@extends('layouts.agensi') 

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Seksyen 64 - Pembatalan Perizaban Tanah</h1>
        <a href="{{ route('permohonan.seksyen64.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Daftar Pembatalan Baru
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
                            <th>Maklumat Warta Asal & Tanah</th>
                            <th>Tujuan / Maksud Awam</th>
                            <th>No. Fail</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekod as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-weight-bold text-primary text-uppercase">Warta: {{ $item->no_warta_asal }}</div>
                                <small class="text-muted">Tarikh: {{ \Carbon\Carbon::parse($item->tarikh_warta_asal)->format('d/m/Y') }}</small>
                                <hr class="my-1">
                                <small><strong>Lot:</strong> {{ $item->no_lot }}, {{ $item->mukim }}</small><br>
                                <small><strong>Daerah:</strong> {{ $item->daerah }}</small>
                            </td>
                            <td>
                                <div class="small font-weight-bold">{{ $item->tujuan_bm }}</div>
                                <div class="small text-muted italic"><em>{{ $item->tujuan_bi }}</em></div>
                            </td>
                            <td><small>{{ $item->no_fail }}</small></td>
                            <td class="text-center font-weight-bold">
                                {{-- Penyelarasan Status Mengikut Standard Seksyen 175D --}}
                                @if($item->status == 'Telah Disemak' || $item->status == 'Selesai')
                                    <span class="text-success">TELAH DISEMAK</span>
                                @elseif($item->status == 'Ditolak')
                                    <span class="text-danger">DITOLAK</span>
                                @else
                                    <span class="text-warning">BELUM DISEMAK</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    {{-- Butang Lihat --}}
                                    <a href="{{ route('permohonan.seksyen64.show', $item->id) }}" class="btn btn-outline-info btn-sm" title="Lihat Preview">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                    {{-- Butang Edit --}}
                                    <a href="{{ route('permohonan.seksyen64.edit', $item->id) }}" class="btn btn-outline-warning btn-sm" title="Edit Rekod">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tiada rekod permohonan pembatalan dijumpai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection