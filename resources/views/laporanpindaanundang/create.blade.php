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
            <h3 class="mb-1 fw-bold text-dark">Daftar Pindaan</h3>
            <p class="text-muted small mb-0">Daftar pindaan Rang Undang-Undang atau Perundangan Subsidiari.</p>
        </div>
        <a href="{{ route('laporanpindaanundang.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('laporanpindaanundang.store') }}">
        @csrf

        <div class="row">
            
            {{-- KOLUM KIRI: MAKLUMAT UTAMA --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-file-signature me-2 text-primary"></i> Maklumat Pindaan</h5>

                    <div class="mb-4">
                        <label for="tajuk" class="form-label">Tajuk Pindaan</label>
                        <textarea name="tajuk" id="tajuk" class="form-control fs-6" rows="3" placeholder="Masukkan tajuk penuh pindaan..." required>{{ old('tajuk') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tindakan" class="form-label">Jenis Tindakan</label>
                        <select class="form-select" id="tindakan" name="tindakan" required>
                            <option value="">-- Sila Pilih Tindakan --</option>
                            <option value="Menggubal dan menyemak perundangan utama" {{ old('tindakan') == 'Menggubal dan menyemak perundangan utama' ? 'selected' : '' }}>
                                1. Menggubal dan menyemak perundangan utama
                            </option>
                            <option value="Menggubal dan menyemak perundangan subsidiari" {{ old('tindakan') == 'Menggubal dan menyemak perundangan subsidiari' ? 'selected' : '' }}>
                                2. Menggubal dan menyemak perundangan subsidiari
                            </option>
                            <option value="Semakan draf warta" {{ old('tindakan') == 'Semakan draf warta' ? 'selected' : '' }}>
                                3. Semakan draf warta
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: STATUS --}}
            <div class="col-lg-4">
                
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-tasks me-2 text-primary"></i> Status Semasa</h5>

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
                        <textarea name="status" id="status" class="form-control" rows="6" placeholder="Status terkini..." required>{{ old('status') }}</textarea>
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
            <a href="{{ route('laporanpindaanundang.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-success px-5 py-2 shadow fw-bold">
                <i class="fas fa-save me-2"></i> Simpan Pindaan
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