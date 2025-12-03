{{-- resources/views/guaman/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">

    @php
        // Tentukan sama ada mod Edit atau Create
        $isEdit = isset($guaman_case);
        $route = $isEdit ? route('guaman.update', $guaman_case) : route('guaman.store');
        $method = $isEdit ? 'PUT' : 'POST';
        $title = $isEdit ? 'Sunting Kes Guaman' : 'Daftar Kes Guaman Baharu';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">{{ $title }}</h3>
        <a href="{{ route('guaman.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Senarai Kes
        </a>
    </div>

    <form action="{{ $route }}" method="POST">
        @csrf
        @method($method) {{-- Guna method PUT untuk update --}}

        {{-- KAD 1: PEGAWAI KENDALIAN --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white fw-bold">Pegawai Kendalian</div>
            <div class="card-body">
                <div class="row g-3"> 
                    {{-- KENDALIAN OLEH (Dropdown) --}}
                    <div class="col-md-6">
                        <label for="kendalian_oleh" class="form-label fw-bold">Kendalian Oleh <span class="text-danger">*</span></label>
                        <select name="kendalian_oleh" id="kendalian_oleh" class="form-select @error('kendalian_oleh') is-invalid @enderror" required>
                            <option value="">-- Sila Pilih Pegawai --</option>
                            
                            @isset($kendalianList)
                                @foreach ($kendalianList as $pegawai)
                                    {{-- MUAT DATA LAMA: Menggunakan $guaman_case->kendalian_oleh sebagai fallback untuk 'old()' --}}
                                    <option value="{{ $pegawai }}" {{ old('kendalian_oleh', $guaman_case->kendalian_oleh ?? '') == $pegawai ? 'selected' : '' }}>
                                        {{ $pegawai }}
                                    </option>
                                @endforeach
                            @endisset

                        </select>
                        @error('kendalian_oleh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- KAD 2: MAKLUMAT UTAMA KES --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white fw-bold">Maklumat Utama Kes</div>
            <div class="card-body">
                <div class="row g-3">
                    
                    {{-- KOD PERKARA & TAJUK --}}
                    <div class="col-md-6">
                        <label for="kod_perkara" class="form-label">Kod Perkara & Tajuk <span class="text-danger">*</span></label>
                        <select name="kod_perkara" id="kod_perkara" class="form-select @error('kod_perkara') is-invalid @enderror" required>
                            <option value="">-- Pilih Kod --</option>
                            
                            @isset($categories)
                                @foreach ($categories as $kod => $cat)
                                    {{-- MUAT DATA LAMA --}}
                                    <option value="{{ $kod }}" {{ old('kod_perkara', $guaman_case->kod_perkara ?? '') == $kod ? 'selected' : '' }}>
                                        KOD {{ $kod }} - {{ $cat['title'] }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('kod_perkara')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- RUJUKAN FAIL --}}
                    <div class="col-md-6">
                        <label for="rujukan_fail" class="form-label">Rujukan Fail</label>
                        {{-- MUAT DATA LAMA --}}
                        <input type="text" name="rujukan_fail" id="rujukan_fail" class="form-control @error('rujukan_fail') is-invalid @enderror" value="{{ old('rujukan_fail', $guaman_case->rujukan_fail ?? '') }}" placeholder="Cth: PN/MK/PP/01/02/01/2023 (2022)">
                        @error('rujukan_fail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TARIKH BUKA KES --}}
                    <div class="col-md-4">
                        <label for="tarikh_buka" class="form-label">Tarikh Buka Kes</label>
                        {{-- MUAT DATA LAMA: Note the format change if data is stored as Carbon/Date object --}}
                        <input type="date" name="tarikh_buka" id="tarikh_buka" class="form-control @error('tarikh_buka') is-invalid @enderror" value="{{ old('tarikh_buka', $guaman_case->tarikh_buka ?? '') }}">
                        @error('tarikh_buka')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    {{-- MAHKAMAH --}}
                    <div class="col-md-4">
                        <label for="mahkamah" class="form-label">Mahkamah</label>
                        {{-- MUAT DATA LAMA --}}
                        <input type="text" name="mahkamah" id="mahkamah" class="form-control @error('mahkamah') is-invalid @enderror" value="{{ old('mahkamah', $guaman_case->mahkamah ?? '') }}" placeholder="Cth: Mahkamah Tinggi Melaka">
                        @error('mahkamah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- KATEGORI KES --}}
                    <div class="col-md-4">
                        <label for="kategori_kes" class="form-label">Kategori Kes <span class="text-danger">*</span></label>
                        {{-- MUAT DATA LAMA --}}
                        <input type="text" name="kategori_kes" id="kategori_kes" class="form-control @error('kategori_kes') is-invalid @enderror" value="{{ old('kategori_kes', $guaman_case->kategori_kes ?? '') }}" placeholder="Cth: Kewarganegaraan" required>
                        @error('kategori_kes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- RUJUKAN MAHKAMAH --}}
                    <div class="col-12">
                        <label for="rujukan_mahkamah" class="form-label">Rujukan Mahkamah</label>
                        {{-- MUAT DATA LAMA --}}
                        <input type="text" name="rujukan_mahkamah" id="rujukan_mahkamah" class="form-control @error('rujukan_mahkamah') is-invalid @enderror" value="{{ old('rujukan_mahkamah', $guaman_case->rujukan_mahkamah ?? '') }}" placeholder="Cth: Rayuan Sivil No: M-01(A)-206-04/2023">
                        @error('rujukan_mahkamah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- KAD 3: BUTIRAN PIHAK BERLAWANAN & STATUS --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white fw-bold">Butiran Pihak Berlawanan & Status</div>
    <div class="card-body">
        <div class="row g-3">
            
            {{-- PIHAK BERLAWANAN (Kekal sama) --}}
            <div class="col-md-8">
                <label for="pihak_berlawanan" class="form-label">Pihak Berlawanan (Plaintif/Pemohon & Defendan/Responden) <span class="text-danger">*</span></label>
                <textarea name="pihak_berlawanan" id="pihak_berlawanan" class="form-control @error('pihak_berlawanan') is-invalid @enderror" rows="4" required placeholder="Cth: Lim Yong Kim V. Ketua Setiausaha Kementerian Dalam Negeri">{{ old('pihak_berlawanan', $guaman_case->pihak_berlawanan ?? '') }}</textarea>
                @error('pihak_berlawanan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- STATUS KES (DIUBAH DARI SELECT KEPADA TEXT INPUT) --}}
            <div class="col-md-4">
                <label for="status_kes" class="form-label">Status Kes</label>
                {{-- KOD BARU: Menggunakan INPUT TEXT --}}
                <input type="text" name="status_kes" id="status_kes" class="form-control @error('status_kes') is-invalid @enderror" 
                       value="{{ old('status_kes', $guaman_case->status_kes ?? 'Kendalian PGN') }}" 
                       placeholder="Cth: Kendalian PGN">
                {{-- Nota: Nilai default 'Kendalian PGN' akan dipaparkan --}}
                
                @error('status_kes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

        {{-- BUTTON SIMPAN/KEMASKINI --}}
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success btn-lg shadow-sm">
                <i class="fas fa-save me-2"></i> {{ $isEdit ? 'Kemaskini Kes Guaman' : 'Simpan Kes Guaman' }}
            </button>
        </div>
        
    </form>
</div>
@endsection