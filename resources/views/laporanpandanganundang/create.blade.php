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

    /* Radio Button Style jadi Card */
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
            <h3 class="mb-1 fw-bold text-dark">Daftar Pandangan Baharu</h3>
            <p class="text-muted small mb-0">Isi maklumat di bawah untuk merekodkan pandangan undang-undang.</p>
        </div>
        <a href="{{ route('laporanpandanganundang.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- FORM UTAMA (PASTIKAN ADA enctype="multipart/form-data") --}}
    <form method="POST" action="{{ route('laporanpandanganundang.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            {{-- KOLUM KIRI: MAKLUMAT ASAS --}}
            <div class="col-lg-8">
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-info-circle me-2 text-primary"></i> Maklumat Asas & Perincian</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select" required>
                                <option value="">-- Sila Pilih Kategori --</option>
                                @foreach(['Perlembagaan', 'Tanah / PBT', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Perjanjian / MOU', 'Penswastaan', 'Lain-lain'] as $kategori)
                                    <option value="{{ $kategori }}" {{ old('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tarikh_terima" class="form-label">Tarikh Terima</label>
                            <input type="date" name="tarikh_terima" id="tarikh_terima" class="form-control" value="{{ old('tarikh_terima') }}" required>
                        </div>
                    </div>

                    {{-- 🔥 TAMBAHAN INPUT DOKUMEN 🔥 --}}
                    <div class="mb-4 bg-light p-3 rounded border">
                        <label for="dokumen" class="form-label fw-bold text-dark">
                            <i class="fas fa-paperclip me-1"></i> Dokumen Sokongan / Lampiran
                        </label>
                        <div class="input-group">
                            <input type="file" class="form-control" name="dokumen" id="dokumen" accept=".pdf,.doc,.docx,.jpg,.png">
                            <label class="input-group-text" for="dokumen"><i class="fas fa-upload text-muted"></i></label>
                        </div>
                        <div class="form-text text-muted small mt-1">
                            <i class="fas fa-info-circle me-1"></i> Format: PDF, Word, Imej. Saiz maks: 10MB.
                        </div>
                        @error('dokumen')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="agensi" class="form-label">Agensi Terlibat</label>
                        <select name="agensi" id="agensi" class="form-select" required>
                            <option value="">-- Sila Pilih / Taip Agensi --</option>
                            @foreach($agensiList ?? [] as $agensi)
                                <option value="{{ $agensi }}" {{ old('agensi') == $agensi ? 'selected' : '' }}>{{ $agensi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="isu" class="form-label">Tajuk Isu</label>
                        <input type="text" name="isu" id="isu" class="form-control" placeholder="Ringkasan tajuk isu..." value="{{ old('isu') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="fakta_ringkasan" class="form-label">Fakta Ringkasan</label>
                        <textarea name="fakta_ringkasan" id="fakta_ringkasan" class="form-control" rows="4" placeholder="Masukkan fakta kes..." required>{{ old('fakta_ringkasan') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="isu_detail" class="form-label">Perincian Isu</label>
                        <textarea name="isu_detail" id="isu_detail" class="form-control" rows="3" placeholder="Huraian lanjut isu..." required>{{ old('isu_detail') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- KOLUM KANAN: STATUS & PANDANGAN --}}
            <div class="col-lg-4">
                
                {{-- CARD 1: JENIS PANDANGAN --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-comment-dots me-2 text-primary"></i> Jenis Pandangan</h5>
                    
                    <div class="d-flex gap-2 w-100">
                        <input type="radio" class="btn-check" name="jenis_pandangan" value="Lisan" id="lisan" {{ old('jenis_pandangan') === 'Lisan' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary w-50 py-2" for="lisan">
                            <i class="fas fa-microphone-alt d-block mb-1 fs-5"></i> Lisan
                        </label>
                    
                        <input type="radio" class="btn-check" name="jenis_pandangan" value="Bertulis" id="bertulis" {{ old('jenis_pandangan') === 'Bertulis' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary w-50 py-2" for="bertulis">
                            <i class="fas fa-file-signature d-block mb-1 fs-5"></i> Bertulis
                        </label>
                    </div>
                </div>

                {{-- CARD 2: STATUS & TARIKH --}}
                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title"><i class="fas fa-tasks me-2 text-primary"></i> Status Tindakan</h5>

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
                        <textarea name="status" id="status" class="form-control" rows="4" required>{{ old('status') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tarikh_selesai" class="form-label">Tarikh Selesai</label>
                        <input type="date" name="tarikh_selesai" id="tarikh_selesai" class="form-control" value="{{ old('tarikh_selesai') }}">
                        
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="belum_selesai" value="1" id="belum_selesai" {{ old('belum_selesai') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="belum_selesai">Tandakan jika Belum Selesai</label>
                        </div>
                    </div>

                    <div class="form-check mt-2 p-3 bg-light rounded border">
                        <input class="form-check-input" type="checkbox" name="dirujuk_jpn" value="1" id="dirujuk_jpn" {{ old('dirujuk_jpn') ? 'checked' : '' }}>
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
            <button type="submit" class="btn btn-success px-5 py-2 shadow fw-bold">
                <i class="fas fa-save me-2"></i> Simpan Laporan
            </button>
        </div>

        {{-- Error Display --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                    statusTextarea.focus(); // Focus ke textarea supaya user boleh terus edit
                }
            });
        }

        // 2. Toggle Date Field based on 'Belum Selesai' Checkbox
        const tarikhField = document.getElementById("tarikh_selesai");
        const checkbox = document.getElementById("belum_selesai");

        if (tarikhField && checkbox) {
            function toggleFields() {
                // Jika checkbox 'Belum Selesai' ditanda, disable date input
                tarikhField.disabled = checkbox.checked;
                
                if (checkbox.checked) {
                    tarikhField.value = ""; // Kosongkan tarikh
                    tarikhField.classList.add('bg-secondary', 'bg-opacity-10'); // Visual cue
                } else {
                    tarikhField.classList.remove('bg-secondary', 'bg-opacity-10');
                }
            }

            // Logic tambahan: Kalau user pilih tarikh, auto uncheck 'Belum Selesai'
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