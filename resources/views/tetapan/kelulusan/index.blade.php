@extends('layouts.app') 

@section('content')
<div class="container-fluid px-4">
    
    {{-- Header Khas --}}
    <h1 class="mt-4 text-warning">Kelulusan Pendaftaran</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">Tetapan</li>
        <li class="breadcrumb-item active">Permohonan Baru (Pending)</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success shadow-sm"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div>
    @endif

    <div class="card mb-4 border-warning shadow-lg">
        <div class="card-header bg-warning text-dark fw-bold">
            <i class="fas fa-user-clock me-1"></i> Senarai Menunggu Tindakan
        </div>
        <div class="card-body">
            @if($senaraiPending->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Tarikh Daftar</th>
                                <th>Nama Pegawai</th>
                                <th>Agensi / Jabatan</th>
                                <th>Kontak (Email/Tel)</th>
                                <th class="text-center">Keputusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($senaraiPending as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y h:i A') }}</td>
                                <td class="fw-bold">{{ $item->nama_pegawai }}</td>
                                <td>
                                    {{ $item->nama_agensi }} <br>
                                    <span class="badge bg-secondary">{{ $item->negeri }}</span>
                                </td>
                                <td>
                                    {{ $item->email }} <br>
                                    <small class="text-muted">{{ $item->no_telefon }}</small>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('kelulusan.approve', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm fw-bold" onclick="return confirm('Sahkan kelulusan?')">
                                            <i class="fas fa-check"></i> LULUS
                                        </button>
                                    </form>
                                    <form action="{{ route('kelulusan.reject', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Tolak permohonan?')">
                                            <i class="fas fa-times"></i> TOLAK
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-check fa-4x text-success mb-3"></i>
                    <h4 class="text-muted">Tiada Permohonan Baru</h4>
                    <p>Semua pendaftaran telah disemak.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection