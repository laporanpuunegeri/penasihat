@extends('layouts.app')

@section('content')

{{-- Style Khas --}}
<style>
    .page-header {
        background: #fff; padding: 20px 25px; border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03); margin-bottom: 25px;
        border-left: 5px solid #f59e0b;
    }
    .form-card {
        background: #fff; border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden;
    }
    .form-section-title {
        color: #1e293b; font-weight: 700; font-size: 1rem; margin-bottom: 15px;
        padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; text-transform: uppercase;
    }
    /* Input Readonly nampak macam disabled */
    .form-control[readonly], .form-check-input:disabled { 
        background-color: #f1f5f9; color: #64748b; cursor: not-allowed; opacity: 1;
    }
</style>

<div class="container-fluid px-4 py-4">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Kemaskini Laporan</h3>
            <p class="text-muted small mb-0">Kemaskini status tindakan. Maklumat asas dikunci.</p>
        </div>
        <a href="{{ route('laporanpandanganundang.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('laporanpandanganundang.update', $laporan->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- KOLUM KIRI: MAKLUMAT ASAS (LOCKED) --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-lock me-2 text-secondary"></i> Maklumat Asas (Dikunci)</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Kategori</label>
                            <input type="text" class="form-control" value="{{ $laporan->kategori }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tarikh Terima Asal</label>
                            <input type="text" class="form-control" value="{{ $laporan->tarikh_terima->format('d/m/Y') }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Agensi Terlibat</label>
                        <input type="text" class="form-control fw-bold" value="{{ $laporan->agensi }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Tajuk Isu</label>
                        <input type="text" class="form-control" value="{{ $laporan->isu }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Fakta Ringkasan</label>
                        <textarea class="form-control" rows="4" readonly>{{ $laporan->fakta_ringkasan }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Perincian Isu</label>
                        <textarea class="form-control" rows="3" readonly>{{ $laporan->isu_detail }}</textarea>
                    </div>
                </div>
                
                {{-- RUANG RINGKASAN PANDANGAN (BOLEH EDIT) --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-edit me-2 text-warning"></i> Ringkasan Pandangan (Boleh Edit)</h5>
                    <textarea name="ringkasan_pandangan" id="ringkasan_pandangan" class="form-control border-warning" rows="4">{{ $laporan->ringkasan_pandangan }}</textarea>
                </div>
            </div>

            {{-- KOLUM KANAN: STATUS & INPUT BARU --}}
            <div class="col-lg-4">
                
                {{-- JENIS PANDANGAN (LOCKED) --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-lock me-2 text-secondary"></i> Jenis Pandangan (Dikunci)</h5>
                    <div class="d-flex gap-2 w-100">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="lisan" {{ $laporan->jenis_pandangan == 'Lisan' ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="lisan">Lisan</label>
                        </div>
                        <div class="form-check ms-3">
                            <input class="form-check-input" type="radio" id="bertulis" {{ $laporan->jenis_pandangan == 'Bertulis' ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="bertulis">Bertulis</label>
                        </div>
                        {{-- Hidden input untuk hantar nilai ke controller --}}
                        <input type="hidden" name="jenis_pandangan" value="{{ $laporan->jenis_pandangan }}">
                    </div>
                </div>

                {{-- STATUS TINDAKAN (LOGIC UTAMA) --}}
                <div class="form-card p-4 mb-4 border-warning">
                    <h5 class="form-section-title text-warning"><i class="fas fa-tasks me-2"></i> Status Tindakan</h5>

                    {{-- 1. INPUT TARIKH BARU (Muncul untuk Status 2-7) --}}
                    <div class="mb-3 p-3 bg-warning bg-opacity-10 rounded border border-warning" id="date-container" style="display: none;">
                        <label for="tarikh_status_baru" class="form-label fw-bold text-dark">
                            <i class="fas fa-calendar-plus me-1"></i> Tarikh Status Baharu
                        </label>
                        <input type="date" name="tarikh_status_baru" id="tarikh_status_baru" class="form-control border-warning bg-white text-dark">
                        <small class="text-muted" style="font-size: 0.75rem;">Tarikh ini akan menjadi tarikh tindakan.</small>
                    </div>

                    {{-- 2. INPUT TARIKH SELESAI (Hanya Muncul untuk Status 8) --}}
                    <div class="mb-3 p-3 bg-success bg-opacity-10 rounded border border-success" id="selesai-container" style="display: none;">
                        <label for="tarikh_selesai" class="form-label fw-bold text-success">
                            <i class="fas fa-check-circle me-1"></i> Tarikh Selesai
                        </label>
                        <input type="date" name="tarikh_selesai" id="tarikh_selesai" class="form-control border-success bg-white">
                        <small class="text-muted" style="font-size: 0.75rem;">Sila masukkan tarikh penyelesaian.</small>
                    </div>

                    <div class="mb-3">
                        <label for="status_preset" class="form-label small text-muted">Pilih Status</label>
                        <select id="status_preset" class="form-select form-select-sm mb-2" onchange="toggleDateInput(this)">
                            <option value="">-- Pilih Template Status --</option>
                            @php
                                $senarai_status = [
                                    '1. Pandangan undang-undang/Maklum Balas telah dikemukakan...',
                                    '2. Draf pandangan undang-undang telah dikemukakan...',
                                    '3. Dalam tindakan untuk menyediakan pandangan...',
                                    '4. Dalam tindakan untuk mendapatkan dokumen...',
                                    '5. Dalam tindakan untuk menyediakan pandangan...',
                                    '6. Cadangan untuk digugurkan daripada Laporan...',
                                    '7. Draf Perjanjian telah dikemukakan kepada...',
                                    '8. Pandangan undang-undang telah dikemukakan...',
                                ];
                            @endphp
                            @foreach ($senarai_status as $status_item)
                                <option value="{{ $status_item }}">{{ $status_item }}</option>
                            @endforeach
                        </select>
                        
                        <label class="form-label">Ringkasan Status</label>
                        <textarea name="status" id="status" class="form-control" rows="4" required>{{ $laporan->status }}</textarea>
                    </div>
                </div>

                {{-- DOKUMEN TAMBAHAN --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-paperclip me-2"></i> Dokumen</h5>
                    @if($laporan->dokumen_path)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $laporan->dokumen_path) }}" target="_blank" class="btn btn-sm btn-info text-white w-100 mb-2">Lihat Dokumen Asal</a>
                        </div>
                    @endif
                    <label class="form-label small">Muat Naik Dokumen Baru (Jika ada)</label>
                    <input type="file" name="dokumen" class="form-control form-control-sm">
                </div>

            </div>
        </div>

        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('laporanpandanganundang.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-5 py-2 shadow fw-bold text-dark">
                <i class="fas fa-save me-2"></i> Simpan Kemaskini
            </button>
        </div>

    </form>
</div>

{{-- JAVASCRIPT: LOGIK PAPARAN TARIKH --}}
<script>
    function toggleDateInput(selectObject) {
        var value = selectObject.value;
        var dateContainer = document.getElementById('date-container');     // Tarikh Baru (2-7)
        var dateInput = document.getElementById('tarikh_status_baru');
        
        var selesaiContainer = document.getElementById('selesai-container'); // Tarikh Selesai (8)
        var selesaiInput = document.getElementById('tarikh_selesai');
        
        var statusTextarea = document.getElementById('status');

        // Auto-fill textarea
        if(value) {
            statusTextarea.value = value;
        }

        // Ambil nombor status
        var firstChar = value.charAt(0);
        var statusNumber = parseInt(firstChar);

        // Reset semua dulu
        dateContainer.style.display = 'none';
        dateInput.removeAttribute('required');
        selesaiContainer.style.display = 'none';
        selesaiInput.removeAttribute('required');

        if (!isNaN(statusNumber)) {
            // LOGIK 1: Status 2 hingga 7
            if (statusNumber >= 2 && statusNumber <= 7) {
                dateContainer.style.display = 'block';
                dateInput.setAttribute('required', 'required');
                
                // Pastikan selesai kosong/hidden
                selesaiInput.value = ''; 
            }
            // LOGIK 2: Status 8
            else if (statusNumber === 8) {
                selesaiContainer.style.display = 'block';
                selesaiInput.setAttribute('required', 'required');
                
                // Pastikan tarikh status baru kosong/hidden
                dateInput.value = '';
            }
        }
    }
</script>

@endsection