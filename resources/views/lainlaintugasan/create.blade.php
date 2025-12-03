@extends('layouts.app')

@section('content')

{{-- CSS Khas --}}
<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #0f172a; /* Dark Navy Accent */
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
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Daftar Tugasan</h3>
            <p class="text-muted small mb-0">Daftar tugasan lain-lain selain daripada kategori utama.</p>
        </div>
        <a href="{{ route('lainlaintugasan.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('lainlaintugasan.store') }}">
        @csrf

        <div class="row">
            
            {{-- KOLUM KIRI: BUTIRAN TUGASAN --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-clipboard-list me-2 text-primary"></i> Maklumat Tugasan</h5>

                    <div class="mb-4">
                        <label for="perihal" class="form-label">Perihal Tugasan</label>
                        <input list="senarai-perihal" type="text" name="perihal" id="perihal" class="form-control fs-6" 
                               placeholder="Taip atau pilih dari senarai..." required value="{{ old('perihal') }}">
                        
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
                            <option value="Telah Hadir" {{ old('tindakan') == 'Telah Hadir' ? 'selected' : '' }}>Telah Hadir</option>
                            <option value="Telah Bincang" {{ old('tindakan') == 'Telah Bincang' ? 'selected' : '' }}>Telah Bincang</option>
                            <option value="Telah Disemak" {{ old('tindakan') == 'Telah Disemak' ? 'selected' : '' }}>Telah Disemak</option>
                            <option value="Selesai" {{ old('tindakan') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: TARIKH & STATUS --}}
            <div class="col-lg-4">
                
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-calendar-alt me-2 text-primary"></i> Tarikh</h5>

                    <div class="mb-3">
                        <label for="tarikh" class="form-label">Tarikh Tugasan</label>
                        <input type="date" name="tarikh" id="tarikh" class="form-control" value="{{ old('tarikh') }}" required>
                    </div>
                </div>

                {{-- KEHADIRAN (ROLE BASED) --}}
                @php $authUser = auth()->user(); @endphp
                @if ($authUser->role === 'user')
                    <div class="form-card p-3 mb-4 border-start border-3 border-primary">
                        <div class="form-check">
                            <input type="checkbox" name="hantar_kepada_boss" value="1" id="hantar_kepada_boss" class="form-check-input">
                            <label for="hantar_kepada_boss" class="form-check-label small fw-bold">Saya hadir bersama YB Penasihat</label>
                        </div>
                    </div>
                @endif
                @if ($authUser->role === 'pa')
                    <input type="hidden" name="hantar_kepada_boss" value="1">
                @endif

            </div>
        </div>

        {{-- FOOTER --}}
        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('lainlaintugasan.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-success px-5 py-2 shadow fw-bold">
                <i class="fas fa-save me-2"></i> Simpan Tugasan
            </button>
        </div>

    </form>
</div>

@endsection