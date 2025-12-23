@extends('layouts.agensi')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Senarai Seksyen 168 (Ganti Hakmilik Hilang)</h1>
        <a href="{{ route('permohonan.seksyen168.create') }}" class="btn btn-primary shadow-sm">
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
                            <th>No. Fail / Tarikh</th>
                            <th>Maklumat Pemilik</th>
                            <th>Maklumat Tanah</th>
                            <th>Sebab Permohonan</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($senarai as $permohonan)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="font-weight-bold text-dark">{{ $permohonan->no_fail }}</span><br>
                                    <small class="text-muted"><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($permohonan->created_at)->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-primary text-uppercase">{{ $permohonan->nama_pemilik }}</div>
                                    <small class="text-muted">IC: {{ $permohonan->no_kp_pemilik ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="small"><strong>Hakmilik:</strong> {{ $permohonan->jenis_hakmilik }} {{ $permohonan->no_hakmilik }}</div>
                                    <div class="small"><strong>Lot:</strong> {{ $permohonan->no_lot }}</div>
                                    <small class="text-muted">{{ $permohonan->mukim }}</small>
                                </td>
                                <td>
                                    <small class="text-danger font-italic">{{ $permohonan->sebab_permohonan }}</small>
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{-- Penyelarasan Status Mengikut Warna Teks (Standard Seksyen 175D) --}}
                                    @if($permohonan->status == 'Baru' || $permohonan->status == '')
                                        <span class="text-warning">BARU</span>
                                    @elseif($permohonan->status == 'Semakan')
                                        <span class="text-info">SEMAKAN</span>
                                    @elseif($permohonan->status == 'Selesai')
                                        <span class="text-success">SELESAI</span>
                                    @else
                                        <span class="text-primary">{{ strtoupper($permohonan->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        {{-- Butang Lihat --}}
                                        <a href="{{ route('permohonan.seksyen168.show', $permohonan->id) }}" class="btn btn-outline-info btn-sm" title="Lihat Borang 10D">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        {{-- Butang Edit --}}
                                        <a href="{{ route('permohonan.seksyen168.edit', $permohonan->id) }}" class="btn btn-outline-warning btn-sm" title="Kemaskini">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tiada rekod Borang 10D ditemui.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection