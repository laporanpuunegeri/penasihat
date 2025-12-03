@extends('layouts.app')

@section('content')

{{-- Style Khas untuk Form --}}
<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #f59e0b; /* Warning Color Accent untuk Edit */
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

    /* Radio Button Style jadi Card */
    .btn-check:checked + .btn-outline-warning {
        background-color: #fffbeb;
        color: #b45309;
        border-color: #f59e0b;
        font-weight: bold;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Kemaskini Laporan</h3>
            <p class="text-muted small mb-0">Kemaskini maklumat pandangan undang-undang sedia ada.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Butang Padam (Optional, kalau nak letak sini) --}}
            <form action="{{ route('laporanpandanganundang.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger shadow-sm">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
            <a href="{{ route('laporanpandanganundang.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('laporanpandanganundang.update', $laporan->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- KOLUM KIRI: MAKLUMAT ASAS --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-edit me-2 text-warning"></i> Maklumat Asas & Perincian</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Kategori (Dikunci)</label>
                            <input type="text" class="form-control bg-light" value="{{ $laporan->kategori }}" readonly>
                            <input type="hidden" name="kategori" value="{{ $laporan->kategori }}">
                        </div>
                        <div class="col-md-6">
                            <label for="tarikh_terima" class="form-label">Tarikh Terima</label>
                            <input type="date" name="tarikh_terima" id="tarikh_terima" class="form-control" value="{{ $laporan->tarikh_terima->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Agensi Terlibat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 fw-bold text-dark" value="{{ $laporan->agensi }}" readonly>
                            <input type="hidden" name="agensi" value="{{ $laporan->agensi }}">
                        </div>
                        <small class="text-muted fst-italic">*Agensi tidak boleh diubah selepas didaftarkan.</small>
                    </div>

                    <div class="mb-3">
                        <label for="isu" class="form-label">Tajuk Isu</label>
                        <input type="text" name="isu" id="isu" class="form-control" value="{{ $laporan->isu }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="fakta_ringkasan" class="form-label">Fakta Ringkasan</label>
                        <textarea name="fakta_ringkasan" id="fakta_ringkasan" class="form-control" rows="4" required>{{ $laporan->fakta_ringkasan }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="isu_detail" class="form-label">Perincian Isu</label>
                        <textarea name="isu_detail" id="isu_detail" class="form-control" rows="3" required>{{ $laporan->isu_detail }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="ringkasan_pandangan" class="form-label">Ringkasan Pandangan</label>
                        <textarea name="ringkasan_pandangan" id="ringkasan_pandangan" class="form-control" rows="3" required>{{ $laporan->ringkasan_pandangan }}</textarea>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: STATUS & PANDANGAN --}}
            <div class="col-lg-4">
                
                {{-- CARD 1: JENIS PANDANGAN --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-comment-dots me-2 text-warning"></i> Jenis Pandangan</h5>
                    
                    <div class="d-flex gap-2 w-100">
                        <input type="radio" class="btn-check" name="jenis_pandangan" value="Lisan" id="lisan" 
                            {{ (old('jenis_pandangan', $laporan->jenis_pandangan ?? '') === 'Lisan') ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning w-50 py-2 text-dark" for="lisan">
                            <i class="fas fa-microphone-alt d-block mb-1 fs-5"></i> Lisan
                        </label>
                    
                        <input type="radio" class="btn-check" name="jenis_pandangan" value="Bertulis" id="bertulis" 
                            {{ (old('jenis_pandangan', $laporan->jenis_pandangan ?? '') === 'Bertulis') ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning w-50 py-2 text-dark" for="bertulis">
                            <i class="fas fa-file-signature d-block mb-1 fs-5"></i> Bertulis
                        </label>
                    </div>
                </div>

                {{-- CARD 2: STATUS & TARIKH --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-tasks me-2 text-warning"></i> Status Tindakan</h5>

                    <div class="mb-3">
                        <label for="status_preset" class="form-label text-muted small">Pilih Ayat Status (Template)</label>
                        <select id="status_preset" class="form-select form-select-sm mb-2 bg-light">
                            <option value="">-- Pilih Template Status --</option>
                            @php
                                $senarai_status = [
                                    'Pandangan undang-undang/Maklum Balas telah dikemukakan /melalui e-mel kepada ………. pada …… Julai 20XX',
                                    'Draf pandangan undang-undang telah dikemukakan kepada Penasihat Undang-Undang pada ….. Mei 20XX',
                                    'Dalam tindakan untuk menyediakan pandangan undang-undang selepas perbincangan dengan YB PUU/Agensi pada … Mei 20XX',
                                    'Dalam tindakan untuk mendapatkan dokumen atau maklum balas daripada Agensi ………. melalui surat/email/percakapan/Whatsapps/telefon bertarikh……. Mei 20XX',
                                    'Dalam tindakan untuk menyediakan pandangan undang-undang atau maklum balas selepas menerima dokumen daripada Agensi ……… bertarikh ……. Mei 20XX',
                                    'Cadangan untuk digugurkan daripada Laporan sehingga mendapat tarikh daripada agensi untuk perbincangan',
                                    'Draf Perjanjian telah dikemukakan kepada …………………….. pada …………… September 20XX',
                                    'Pandangan undang-undang telah dikemukakan dalam mesyuarat pada …………………………',
                                ];
                            @endphp
                            @foreach ($senarai_status as $status_item)
                                <option value="{{ $status_item }}">{{ $loop->iteration }}. {{ Str::limit($status_item, 40) }}</option>
                            @endforeach
                        </select>
                        <label for="status" class="form-label">Ringkasan Status</label>
                        <textarea name="status" id="status" class="form-control" rows="4" required>{{ $laporan->status }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tarikh_selesai" class="form-label">Tarikh Selesai</label>
                        <input type="date" name="tarikh_selesai" id="tarikh_selesai" class="form-control" value="{{ $laporan->tarikh_selesai ? $laporan->tarikh_selesai->format('Y-m-d') : '' }}">
                        
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="belum_selesai" value="1" id="belum_selesai" 
                                {{ $laporan->belum_selesai ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="belum_selesai">Tandakan jika Belum Selesai</label>
                        </div>
                    </div>

                    <div class="form-check mt-2 p-3 bg-light rounded border">
                        <input class="form-check-input" type="checkbox" name="dirujuk_jpn" value="1" id="dirujuk_jpn" 
                            {{ $laporan->dirujuk_jpn ? 'checked' : '' }}>
                        <label class="form-check-label" for="dirujuk_jpn">Dirujuk ke AGC (HQ)</label>
                    </div>
                </div>

                {{-- CARD 3: KEHADIRAN (ROLE BASED) --}}
                @php $authUser = auth()->user(); @endphp
                @if ($authUser->role === 'user')
                    <div class="form-card p-3 mb-4">
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

        {{-- FOOTER / SUBMIT BUTTON --}}
        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('laporanpandanganundang.index') }}" class="btn btn-light border me-3 px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-5 py-2 shadow fw-bold text-dark">
                <i class="fas fa-save me-2"></i> Simpan Kemaskini
            </button>
        </div>

    </form>
</div>

{{-- SCRIPT --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Auto-fill Textarea from Dropdown
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

        // 2. Toggle Date Field based on 'Belum Selesai' Checkbox
        const tarikhField = document.getElementById("tarikh_selesai");
        const checkbox = document.getElementById("belum_selesai");

        if (tarikhField && checkbox) {
            function toggleFields() {
                tarikhField.disabled = checkbox.checked;
                
                if (checkbox.checked) {
                    tarikhField.value = "";
                    tarikhField.classList.add('bg-secondary', 'bg-opacity-10');
                } else {
                    tarikhField.classList.remove('bg-secondary', 'bg-opacity-10');
                }
            }

            tarikhField.addEventListener("change", function() {
                if (this.value !== "") {
                    checkbox.checked = false;
                    toggleFields();
                }
            });

            checkbox.addEventListener("change", toggleFields);
            
            // Run on load
            toggleFields();
        }
    });
</script>

@endsection