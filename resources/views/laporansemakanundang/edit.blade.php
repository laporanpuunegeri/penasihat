@extends('layouts.app')

@section('content')

{{-- Style Khas (Tema Edit - Oren) --}}
<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #f59e0b; /* Warning Accent for Edit */
    }

    .form-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .form-section-title {
        color: #1e293b;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .form-control:focus, .form-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Kemaskini Semakan</h3>
            <p class="text-muted small mb-0">Kemaskini maklumat Semakan RUU atau Perundangan Subsidiari.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Butang Padam --}}
            @if(auth()->user()->id === $laporan->user_id || auth()->user()->role === 'admin')
                <form action="{{ route('laporansemakanundang.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger shadow-sm" title="Padam Rekod">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif

            <a href="{{ route('laporansemakanundang.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('laporansemakanundang.update', $laporan->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            
            {{-- KOLUM KIRI: MAKLUMAT SEMAKAN --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-search me-2 text-warning"></i> Butiran Semakan</h5>

                    <div class="mb-4">
                        <label for="tajuk" class="form-label">Tajuk RUU / Perundangan Subsidiari</label>
                        <textarea name="tajuk" id="tajuk" class="form-control fs-6" rows="3" placeholder="Masukkan tajuk penuh..." required>{{ old('tajuk', $laporan->tajuk) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tindakan" class="form-label">Tindakan</label>
                        <textarea class="form-control" id="tindakan" name="tindakan" rows="4" placeholder="Perincian tindakan semakan..." required>{{ old('tindakan', $laporan->tindakan) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: STATUS --}}
            <div class="col-lg-4">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-tasks me-2 text-warning"></i> Status Semasa</h5>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status Terkini</label>
                        <select class="form-select bg-light" id="status" name="status" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(['Dalam Penyediaan', 'Semakan', 'Diluluskan', 'Dibentangkan', 'Berkuat Kuasa'] as $statusOption)
                                <option value="{{ $statusOption }}" {{ old('status', $laporan->status) == $statusOption ? 'selected' : '' }}>
                                    {{ $statusOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="alert alert-light border mt-4">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Sila pastikan status dikemaskini mengikut perkembangan terkini fail.</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('laporansemakanundang.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-5 py-2 shadow fw-bold text-dark">
                <i class="fas fa-save me-2"></i> Simpan Kemaskini
            </button>
        </div>

    </form>
</div>

@endsection