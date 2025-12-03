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
            <h3 class="mb-1 fw-bold text-dark">Kemaskini Tatatertib</h3>
            <p class="text-muted small mb-0">Kemaskini maklumat kes tatatertib.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Butang Padam --}}
            @if(auth()->user()->id === $laporan->user_id || auth()->user()->role === 'admin')
                <form action="{{ route('kestatatertib.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger shadow-sm" title="Padam Rekod">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif

            <a href="{{ route('kestatatertib.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('kestatatertib.update', $laporan->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            
            {{-- KOLUM KIRI: MAKLUMAT KES --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-gavel me-2 text-warning"></i> Butiran Kes</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kategori" class="form-label">Kategori Kes</label>
                            <select name="kategori" id="kategori" class="form-select" required>
                                <option value="">-- Sila Pilih --</option>
                                <option value="(i) MENYEMAK PENENTUAN KES PRIMA FACIE / KERTAS PERTUDUHAN / NOTIS TIDAK HADIR BERTUGAS" 
                                    {{ old('kategori', $laporan->kategori) == '(i) MENYEMAK PENENTUAN KES PRIMA FACIE / KERTAS PERTUDUHAN / NOTIS TIDAK HADIR BERTUGAS' ? 'selected' : '' }}>
                                    (i) Prima Facie / Pertuduhan / Tidak Hadir
                                </option>
                                <option value="(ii) KES SURCAJ / MENELITI LAPORAN JAWATANKUASA SIASATAN" 
                                    {{ old('kategori', $laporan->kategori) == '(ii) KES SURCAJ / MENELITI LAPORAN JAWATANKUASA SIASATAN' ? 'selected' : '' }}>
                                    (ii) Kes Surcaj / Laporan Siasatan
                                </option>
                                <option value="(iii) PENYEDIAAN ULASAN BAGI KES PENAMATAN DEMI KEPENTINGAN AWAM" 
                                    {{ old('kategori', $laporan->kategori) == '(iii) PENYEDIAAN ULASAN BAGI KES PENAMATAN DEMI KEPENTINGAN AWAM' ? 'selected' : '' }}>
                                    (iii) Penamatan Demi Kepentingan Awam
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tarikh_terima" class="form-label">Tarikh Terima</label>
                            <input type="date" name="tarikh_terima" id="tarikh_terima" class="form-control" 
                                   value="{{ optional($laporan->tarikh_terima)->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fakta_ringkasan" class="form-label">Fakta Ringkasan</label>
                        <textarea name="fakta_ringkasan" id="fakta_ringkasan" class="form-control" rows="3" required>{{ old('fakta_ringkasan', $laporan->fakta_ringkasan) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="isu" class="form-label">Isu Berbangkit</label>
                        <textarea name="isu" id="isu" class="form-control" rows="3" required>{{ old('isu', $laporan->isu) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="ringkasan_pandangan" class="form-label">Ringkasan Pandangan</label>
                        <textarea name="ringkasan_pandangan" id="ringkasan_pandangan" class="form-control" rows="3" required>{{ old('ringkasan_pandangan', $laporan->ringkasan_pandangan) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: STATUS --}}
            <div class="col-lg-4">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-tasks me-2 text-warning"></i> Status Semasa</h5>

                    <div class="mb-2">
                        <label for="status_preset" class="form-label text-muted small">Pilih Template Status</label>
                        <select id="status_preset" class="form-select form-select-sm bg-light mb-2">
                            <option value="">-- Pilih Status --</option>
                            <option value="Pandangan telah dikemukakan kepada Urus Setia.">Pandangan dikemukakan</option>
                            <option value="Menunggu maklum balas jabatan.">Menunggu jabatan</option>
                            <option value="Draf pertuduhan telah disediakan.">Draf pertuduhan siap</option>
                            <option value="Selesai.">Selesai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Keterangan Status</label>
                        <textarea name="status" id="status" class="form-control" rows="4" required>{{ old('status', $laporan->status) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tarikh_selesai" class="form-label">Tarikh Selesai</label>
                        <input type="date" name="tarikh_selesai" id="tarikh_selesai" class="form-control" 
                               value="{{ optional($laporan->tarikh_selesai)->format('Y-m-d') }}">
                        <small class="text-muted fst-italic">*Biarkan kosong jika belum selesai.</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('kestatatertib.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-5 py-2 shadow fw-bold text-dark">
                <i class="fas fa-save me-2"></i> Simpan Kemaskini
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