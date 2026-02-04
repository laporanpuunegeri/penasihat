@extends('layouts.app')

@section('content')

<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #0f172a;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Pengurusan Agensi</h3>
            <p class="text-muted small mb-0">Tambah, kemaskini atau padam senarai agensi untuk negeri {{ Auth::user()->negeri }}.</p>
        </div>
    </div>

    {{-- PAPAR MESEJ SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        
        {{-- BORANG TAMBAH --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-plus-circle me-2 text-success"></i> Tambah Agensi Baru
                </div>
                <div class="card-body">
                    <form action="{{ route('agensi.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted text-uppercase fw-bold">Nama Agensi</label>
                            <input type="text" name="nama_agensi" class="form-control" placeholder="Contoh: Majlis Daerah..." required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan Agensi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- SENARAI AGENSI --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list me-2 text-primary"></i> Senarai Agensi ({{ $agensis->count() }})</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4" style="width: 5%">No.</th>
                                    <th style="width: 75%">Nama Agensi</th>
                                    <th class="text-center" style="width: 20%">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($agensis as $index => $agensi)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $index + 1 }}.</td>
                                        <td class="fw-bold text-secondary">{{ $agensi->nama_agensi }}</td>
                                        <td class="text-center">
                                            
                                            {{-- BUTANG EDIT (Buka Modal) --}}
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal{{ $agensi->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- BUTANG PADAM --}}
                                            <form action="{{ route('agensi.destroy', $agensi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin nak padam agensi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>

                                    {{-- MODAL EDIT (Setiap row ada modal sendiri) --}}
                                    <div class="modal fade" id="editModal{{ $agensi->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Kemaskini Agensi</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('agensi.update', $agensi->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nama Agensi</label>
                                                            <input type="text" name="nama_agensi" class="form-control" value="{{ $agensi->nama_agensi }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- TAMAT MODAL --}}

                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Tiada agensi didaftarkan lagi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection