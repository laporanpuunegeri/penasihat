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
        border-left: 5px solid #f59e0b; /* Warning Accent */
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
            <h3 class="mb-1 fw-bold text-dark">Kemaskini Tugasan</h3>
            <p class="text-muted small mb-0">Kemaskini maklumat tugasan lain-lain.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Butang Padam --}}
            @if(auth()->user()->id === $tugasan->user_id || auth()->user()->role === 'admin')
                <form action="{{ route('lainlaintugasan.destroy', $tugasan->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger shadow-sm" title="Padam Rekod">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif

            <a href="{{ route('lainlaintugasan.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('lainlaintugasan.update', $tugasan->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            
            {{-- KOLUM KIRI: BUTIRAN TUGASAN --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-clipboard-list me-2 text-warning"></i> Butiran Tugasan</h5>

                    <div class="mb-4">
                        <label for="perihal" class="form-label">Perihal Tugasan</label>
                        <input list="senarai-perihal" type="text" name="perihal" id="perihal" class="form-control fs-6" 
                               value="{{ old('perihal', $tugasan->perihal) }}" required placeholder="Taip atau pilih dari senarai...">
                        
                        <datalist id="senarai-perihal">
                            <option value="Perbincangan dengan (nama pegawai dan jawatan)">
                            <option value="Perbincangan melalui Online Meeting bersama (nama pegawai dan jawatan)">
                            <option value="Menyediakan minit ceraian">
                            <option value="Semakan dokumen">
                        </datalist>
                    </div>

                    <div class="mb-3">
                        <label for="tindakan" class="form-label">Tindakan Diambil</label>
                        <select name="tindakan" id="tindakan" class="form-select" required>
                            <option value="">-- Sila Pilih --</option>
                            <option value="Telah Hadir" {{ old('tindakan', $tugasan->tindakan) == 'Telah Hadir' ? 'selected' : '' }}>Telah Hadir</option>
                            <option value="Telah Bincang" {{ old('tindakan', $tugasan->tindakan) == 'Telah Bincang' ? 'selected' : '' }}>Telah Bincang</option>
                            <option value="Telah Disemak" {{ old('tindakan', $tugasan->tindakan) == 'Telah Disemak' ? 'selected' : '' }}>Telah Disemak</option>
                            <option value="Selesai" {{ old('tindakan', $tugasan->tindakan) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: TARIKH --}}
            <div class="col-lg-4">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-calendar-alt me-2 text-warning"></i> Tarikh</h5>

                    <div class="mb-3">
                        <label for="tarikh" class="form-label">Tarikh Tugasan</label>
                        <input type="date" name="tarikh" id="tarikh" class="form-control" 
                               value="{{ old('tarikh', optional($tugasan->tarikh)->format('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('lainlaintugasan.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-5 py-2 shadow fw-bold text-dark">
                <i class="fas fa-save me-2"></i> Simpan Kemaskini
            </button>
        </div>

    </form>
</div>

@endsection