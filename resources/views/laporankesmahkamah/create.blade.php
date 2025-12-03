@extends('layouts.app')

@section('content')

{{-- CSS Khas (Konsisten dengan modul lain) --}}
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
            <h3 class="mb-1 fw-bold text-dark">Daftar Kes Mahkamah</h3>
            <p class="text-muted small mb-0">Masukkan butiran kes baharu untuk rekod jabatan.</p>
        </div>
        <a href="{{ route('laporankesmahkamah.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('laporankesmahkamah.store') }}">
        @csrf

        <div class="row">
            
            {{-- KOLUM KIRI: BUTIRAN KES --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-gavel me-2 text-primary"></i> Butiran Utama Kes</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jenis_kes" class="form-label">Jenis Kes / Pihak-Pihak</label>
                            <input type="text" name="jenis_kes" id="jenis_kes" class="form-control" placeholder="Contoh: Sivil / Jenayah (Ali vs Abu)" required>
                        </div>
                        <div class="col-md-6">
                            <label for="perkara" class="form-label">Perkara / No. Kes</label>
                            <input type="text" name="perkara" id="perkara" class="form-control" placeholder="Contoh: Saman Pemula No..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fakta_ringkas" class="form-label">Fakta Ringkas</label>
                        <textarea name="fakta_ringkas" id="fakta_ringkas" class="form-control" rows="3" placeholder="Ringkasan fakta kes..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="isu" class="form-label">Isu Undang-Undang</label>
                        <textarea name="isu" id="isu" class="form-control" rows="3" placeholder="Isu berbangkit..."></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="skop_tugas" class="form-label">Skop Tugas</label>
                            <textarea name="skop_tugas" id="skop_tugas" class="form-control" rows="3" placeholder="Tugas pegawai..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="ringkasan_hujahan" class="form-label">Ringkasan Hujahan</label>
                            <textarea name="ringkasan_hujahan" id="ringkasan_hujahan" class="form-control" rows="3" placeholder="Intipati hujah..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: TARIKH & STATUS --}}
            <div class="col-lg-4">
                
                {{-- CARD 1: TARIKH PENTING --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-calendar-alt me-2 text-primary"></i> Tarikh Penting</h5>
                    
                    <div class="mb-3">
                        <label for="tarikh_daftar" class="form-label">Tarikh Daftar Kes</label>
                        <input type="date" name="tarikh_daftar" id="tarikh_daftar" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label for="tarikh_sebutan" class="form-label">Tarikh Sebutan / Bicara</label>
                        <input type="date" name="tarikh_sebutan" id="tarikh_sebutan" class="form-control">
                        <small class="text-muted fst-italic">*Biarkan kosong jika belum ada tarikh.</small>
                    </div>
                </div>

                {{-- CARD 2: STATUS KES --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-tasks me-2 text-primary"></i> Status Semasa</h5>

                    <div class="mb-2">
                        <label for="status_preset" class="form-label text-muted small">Pilih Template Status</label>
                        <select id="status_preset" class="form-select form-select-sm bg-light mb-2">
                            <option value="">-- Pilih Status --</option>
                            <option value="Kes selesai sepenuhnya.">Kes Selesai</option>
                            <option value="Dalam proses perbicaraan.">Dalam Perbicaraan</option>
                            <option value="Menunggu tarikh sebutan baru.">Menunggu Sebutan</option>
                            <option value="Dalam proses penyediaan hujahan bertulis.">Penyediaan Hujahan</option>
                            <option value="Fail ditutup.">Fail Ditutup</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Keterangan Status</label>
                        <textarea name="status" id="status" class="form-control" rows="4" placeholder="Status terkini kes..." required></textarea>
                    </div>
                </div>

                {{-- CARD 3: KEHADIRAN (ROLE BASED) --}}
                @php $authUser = auth()->user(); @endphp
                @if ($authUser->role === 'user')
                    <div class="form-card p-3 mb-4 border-warning border-start border-3">
                        <div class="form-check">
                            <input type="checkbox" name="hantar_kepada_boss" value="1" id="hantar_kepada_boss" class="form-check-input">
                            <label for="hantar_kepada_boss" class="form-check-label small fw-bold text-dark">Saya hadir bersama YB Penasihat</label>
                        </div>
                    </div>
                @endif
                @if ($authUser->role === 'pa')
                    <input type="hidden" name="hantar_kepada_boss" value="1">
                @endif

            </div>
        </div>

        {{-- FOOTER / SUBMIT BUTTON --}}
        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('laporankesmahkamah.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-success px-5 py-2 shadow fw-bold">
                <i class="fas fa-save me-2"></i> Simpan Kes
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