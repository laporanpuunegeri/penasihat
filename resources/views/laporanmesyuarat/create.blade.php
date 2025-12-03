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

    /* Radio Button Style */
    .btn-check:checked + .btn-outline-primary {
        background-color: #eff6ff;
        color: #1d4ed8;
        border-color: #3b82f6;
        font-weight: bold;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Daftar Mesyuarat</h3>
            <p class="text-muted small mb-0">Rekod kehadiran mesyuarat dan pandangan yang diberikan.</p>
        </div>
        <a href="{{ route('laporanmesyuarat.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('laporanmesyuarat.store') }}">
        @csrf

        <div class="row">
            
            {{-- KOLUM KIRI: BUTIRAN MESYUARAT --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-handshake me-2 text-primary"></i> Butiran Mesyuarat</h5>

                    <div class="mb-3">
                        <label for="mesyuarat" class="form-label">Nama Mesyuarat</label>
                        <textarea name="mesyuarat" id="mesyuarat" class="form-control fs-6" rows="2" placeholder="Contoh: Mesyuarat Jawatankuasa Pelesenan Bil. 1/2025" required>{{ old('mesyuarat') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="isu" class="form-label">Isu Berbangkit</label>
                        <textarea name="isu" id="isu" class="form-control" rows="4" placeholder="Ringkasan isu yang dibincangkan..." required>{{ old('isu') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Jenis Pandangan Diberikan</label>
                        <div class="d-flex gap-2 w-100">
                            <input type="radio" class="btn-check" name="pandangan" id="lisan" value="Lisan" {{ old('pandangan') == 'Lisan' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-primary w-50 py-2" for="lisan">
                                <i class="fas fa-microphone-alt d-block mb-1 fs-5"></i> Lisan
                            </label>
                        
                            <input type="radio" class="btn-check" name="pandangan" id="bertulis" value="Bertulis" {{ old('pandangan') == 'Bertulis' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-primary w-50 py-2" for="bertulis">
                                <i class="fas fa-file-signature d-block mb-1 fs-5"></i> Bertulis
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: TARIKH & STATUS --}}
            <div class="col-lg-4">
                
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-calendar-check me-2 text-primary"></i> Status & Tarikh</h5>

                    <div class="mb-3">
                        <label for="tarikh_mesyuarat" class="form-label">Tarikh Mesyuarat</label>
                        <input type="date" name="tarikh_mesyuarat" id="tarikh_mesyuarat" class="form-control" value="{{ old('tarikh_mesyuarat') }}" required>
                    </div>

                    <div class="mb-2">
                        <label for="status_preset" class="form-label text-muted small">Pilih Template Status</label>
                        <select id="status_preset" class="form-select form-select-sm bg-light mb-2">
                            <option value="">-- Pilih Status --</option>
                            <option value="Selesai dihadiri.">Selesai dihadiri</option>
                            <option value="Mesyuarat ditangguhkan.">Mesyuarat ditangguhkan</option>
                            <option value="Pandangan undang-undang telah direkodkan dalam minit.">Pandangan direkodkan dalam minit</option>
                            <option value="Perlu tindakan susulan/semakan lanjut.">Perlu tindakan susulan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Keterangan Status</label>
                        <textarea name="status" id="status" class="form-control" rows="4" placeholder="Status terkini..." required>{{ old('status') }}</textarea>
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
            <a href="{{ route('laporanmesyuarat.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-success px-5 py-2 shadow fw-bold">
                <i class="fas fa-save me-2"></i> Simpan Mesyuarat
            </button>
        </div>

    </form>
</div>

{{-- SCRIPT: Auto-fill Status --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const presetSelect = document.getElementById('status_preset');
        const statusTextarea = document.getElementById('status');
        
        if (presetSelect && statusTextarea) {
            presetSelect.addEventListener('change', function () {
                if(this.value) {
                    statusTextarea.value = this.value;
                    statusTextarea.focus();
                }
            });
        }
    });
</script>

@endsection