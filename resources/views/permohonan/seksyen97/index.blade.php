@extends('layouts.agensi')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Senarai Seksyen 97 (Notis Tuntutan)</h1>
        <a href="{{ route('permohonan.seksyen97.create') }}" class="btn btn-primary shadow-sm">
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
                            <th class="text-center" style="width: 5%">Bil</th>
                            <th style="width: 15%">No. Fail / Tarikh</th>
                            <th style="width: 25%">Maklumat Pemilik</th>
                            <th style="width: 20%">Maklumat Tanah</th>
                            <th style="width: 15%">Jumlah Tuntutan</th>
                            <th class="text-center" style="width: 10%">Status</th>
                            <th class="text-center" style="width: 10%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($senarai as $data)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            
                            <td>
                                <span class="font-weight-bold text-dark">{{ $data->no_fail }}</span><br>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}
                                </small>
                            </td>

                            <td>
                                <div class="font-weight-bold text-primary text-uppercase">{{ $data->nama_pemilik }}</div>
                                <small class="text-muted">IC: {{ $data->no_kp_pemilik }}</small>
                            </td>

                            <td>
                                <div class="small"><strong>Hakmilik:</strong> {{ $data->no_hakmilik }}</div>
                                <div class="small"><strong>Lot:</strong> {{ $data->no_lot }}</div>
                                <small class="text-muted">{{ $data->mukim }}, {{ $data->daerah }}</small>
                            </td>

                            <td class="text-right font-weight-bold text-danger">
                                RM {{ number_format($data->jumlah_besar, 2) }}
                            </td>

                            <td class="text-center font-weight-bold">
                                {{-- Penyelarasan Status mengikut warna tulisan (Standard Seksyen 175D) --}}
                                @if($data->status == 'Baru')
                                    <span class="text-warning">BARU</span>
                                @elseif($data->status == 'Semakan' || $data->status == 'Dalam Semakan')
                                    <span class="text-info">SEMAKAN</span>
                                @elseif($data->status == 'Lulus' || $data->status == 'Selesai')
                                    <span class="text-success">LULUS</span>
                                @else
                                    <span class="text-primary">{{ strtoupper($data->status ?? 'TIADA STATUS') }}</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('permohonan.seksyen97.show', $data->id) }}" class="btn btn-outline-info btn-sm" title="Papar Surat">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                    <a href="{{ route('permohonan.seksyen97.edit', $data->id) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tiada rekod permohonan dijumpai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection