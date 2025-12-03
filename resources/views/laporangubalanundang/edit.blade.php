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
            <h3 class="mb-1 fw-bold text-dark">Kemaskini Gubalan</h3>
            <p class="text-muted small mb-0">Kemaskini maklumat RUU atau Perundangan Subsidiari.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Butang Padam --}}
            @if(auth()->user()->id === $laporan->user_id || auth()->user()->role === 'admin')
                <form action="{{ route('laporangubalanundang.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger shadow-sm" title="Padam Rekod">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif

            <a href="{{ route('laporangubalanundang.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('laporangubalanundang.update', $laporan->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            
            {{-- KOLUM KIRI: MAKLUMAT GUBALAN --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-pen-nib me-2 text-warning"></i> Butiran Gubalan</h5>

                    <div class="mb-4">
                        <label for="tajuk" class="form-label">Tajuk RUU / Perundangan Subsidiari</label>
                        <textarea name="tajuk" id="tajuk" class="form-control fs-6" rows="3" placeholder="Masukkan tajuk penuh..." required>{{ old('tajuk', $laporan->tajuk) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tindakan" class="form-label">Jenis Tindakan</label>
                        <select class="form-select" id="tindakan" name="tindakan" required>
                            <option value="">-- Sila Pilih Tindakan --</option>
                            <option value="Menggubal dan menyemak perundangan utama" {{ old('tindakan', $laporan->tindakan) == 'Menggubal dan menyemak perundangan utama' ? 'selected' : '' }}>
                                1. Menggubal dan menyemak perundangan utama
                            </option>
                            <option value="Menggubal dan menyemak perundangan subsidiari" {{ old('tindakan', $laporan->tindakan) == 'Menggubal dan menyemak perundangan subsidiari' ? 'selected' : '' }}>
                                2. Menggubal dan menyemak perundangan subsidiari
                            </option>
                            <option value="Semakan draf warta" {{ old('tindakan', $laporan->tindakan) == 'Semakan draf warta' ? 'selected' : '' }}>
                                3. Semakan draf warta
                            </option>
                        </select>
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
                            <option value="Telah diwartakan pada ...">Telah diwartakan</option>
                            <option value="Draf akhir telah dihantar untuk semakan PUU.">Draf dihantar ke PUU</option>
                            <option value="Dalam tindakan semakan akhir.">Semakan Akhir</option>
                            <option value="Menunggu maklum balas agensi.">Menunggu Agensi</option>
                            <option value="Selesai disemak dan diluluskan.">Selesai & Lulus</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Keterangan Status</label>
                        <textarea name="status" id="status" class="form-control" rows="6" required>{{ old('status', $laporan->status) }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('laporangubalanundang.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
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