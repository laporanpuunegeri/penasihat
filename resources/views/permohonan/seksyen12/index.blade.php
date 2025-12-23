@extends('layouts.agensi') 

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Seksyen 12 - Pelantikan Di Bawah Perenggan 12(1)(b)</h1>
        <a href="{{ route('permohonan.seksyen12.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Daftar Pelantikan Baru
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
                            <th width="5%">Bil</th>
                            <th>No. Fail</th>
                            <th>Maklumat Calon & Jawatan</th>
                            <th>Tarikh Lantikan</th>
                            <th width="15%">Status</th>
                            <th width="15%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekod as $r)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td><strong>{{ $r->no_fail }}</strong></td>
                            <td>
                                <div class="font-weight-bold text-uppercase text-primary">{{ $r->nama }}</div>
                                <small class="text-muted">No. KP: {{ $r->no_kp ?: 'N/A' }}</small><br>
                                <small><strong>Lantikan:</strong> {{ $r->pelantikan_bm }}</small>
                            </td>
                            <td class="text-center">
                                {{ $r->tarikh_lantikan ? \Carbon\Carbon::parse($r->tarikh_lantikan)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="text-center font-weight-bold">
                                {{-- Penyelarasan Status Mengikut Warna Teks (Standard 175D) --}}
                                @if($r->status == 'Telah Disemak')
                                    <span class="text-success">TELAH DISEMAK</span>
                                @elseif($r->status == 'Ditolak')
                                    <span class="text-danger">DITOLAK</span>
                                @else
                                    <span class="text-warning">BELUM DISEMAK</span>
                                @endif
                            </td>       
                            <td class="text-center">
                                <div class="btn-group">
                                    {{-- Butang Lihat --}}
                                    <a href="{{ route('permohonan.seksyen12.show', $r->id) }}" class="btn btn-outline-info btn-sm" title="Lihat Preview">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                    {{-- Butang Edit --}}
                                    <a href="{{ route('permohonan.seksyen12.edit', $r->id) }}" class="btn btn-outline-warning btn-sm" title="Edit Rekod">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tiada rekod pelantikan dijumpai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection