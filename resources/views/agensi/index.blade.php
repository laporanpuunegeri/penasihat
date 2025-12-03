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
            <p class="text-muted small mb-0">Tambah atau padam senarai agensi untuk negeri {{ Auth::user()->negeri }}.</p>
        </div>
    </div>

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
                        <button type="submit" class="btn btn-success w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- SENARAI AGENSI --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-list me-2 text-primary"></i> Senarai Agensi ({{ $agensis->count() }})
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4">Nama Agensi</th>
                                    <th class="text-end pe-4">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($agensis as $agensi)
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">{{ $agensi->nama_agensi }}</td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('agensi.destroy', $agensi->id) }}" method="POST" onsubmit="return confirm('Yakin nak padam agensi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection