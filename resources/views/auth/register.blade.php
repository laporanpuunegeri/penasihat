@extends('layouts.app')

@section('title', 'Daftar Akaun Baharu')

@section('content')
<div class="container-fluid">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Daftar Akaun Baharu</h1>
        <a href="{{ route('tetapan.pengguna.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Senarai
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            
            {{-- Kad Borang --}}
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-user-plus me-2"></i> Isi Maklumat Pegawai</h6>
                </div>
                
                <div class="card-body p-4">
                    
                    {{-- Papar Error (Termasuk error jika Role sudah wujud) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- 🔥 PENTING: Tambah enctype="multipart/form-data" untuk upload gambar --}}
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Baris 1: Nama & Emel --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">NAMA PEGAWAI</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">ALAMAT EMEL</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        {{-- Baris 2: Telefon & Negeri --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">NO. TELEFON</label>
                                <input type="text" class="form-control" name="no_telefon" value="{{ old('no_telefon') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">NEGERI</label>
                                <select name="negeri" class="form-select" required>
                                    <option value="">-- Sila Pilih Negeri --</option>
                                    @php
                                        $states = ["Johor","Kedah","Kelantan","Melaka","Negeri Sembilan","Pahang","Perak","Perlis","Pulau Pinang","Sabah","Sarawak","Selangor","Terengganu","Wilayah Persekutuan Kuala Lumpur","Wilayah Persekutuan Labuan","Wilayah Persekutuan Putrajaya"];
                                    @endphp
                                    @foreach ($states as $state)
                                        <option value="{{ $state }}" {{ old('negeri') == $state ? 'selected' : '' }}>{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Baris 3: Bahagian & Jawatan --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">BAHAGIAN</label>
                                <select name="bahagian" class="form-select" required>
                                    <option value="">-- Sila Pilih Bahagian --</option>
                                    @php
                                        $bahagians = ["BAHAGIAN PENASIHAT", "BAHAGIAN PENDAKWAAN", "BAHAGIAN SEMAKAN", "BAHAGIAN PENTADBIRAN & KEWANGAN", "BAHAGIAN GUAMAN", "BAHAGIAN SYARIAH"];
                                    @endphp
                                    @foreach ($bahagians as $bahagian)
                                        <option value="{{ $bahagian }}" {{ old('bahagian') == $bahagian ? 'selected' : '' }}>{{ $bahagian }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">NAMA JAWATAN</label>
                                <input type="text" class="form-control" name="nama_jawatan" value="{{ old('nama_jawatan') }}" required>
                            </div>
                        </div>

                        {{-- Baris 4: Gred & Peranan --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">GRED JAWATAN</label>
                                <input type="text" class="form-control" name="gred_jawatan" value="{{ old('gred_jawatan') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">PERANAN SISTEM</label>
                                <select name="role" class="form-select" required>
                                    <option value="">-- Sila Pilih Peranan --</option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User Biasa</option>
                                    <option value="eo" {{ old('role') == 'eo' ? 'selected' : '' }}>EO (Terhad 1/Negeri)</option>
                                    <option value="cc" {{ old('role') == 'cc' ? 'selected' : '' }}>CC (Terhad 1/Negeri)</option>
                                    <option value="pa" {{ old('role') == 'pa' ? 'selected' : '' }}>PA (Terhad 1/Negeri)</option>
                                    <option value="yb" {{ old('role') == 'yb' ? 'selected' : '' }}>YB Penasihat (Terhad 1/Negeri)</option>
                                </select>
                            </div>
                        </div>

                        {{-- 🔥 Baris 5: Upload Tandatangan --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small">MUAT NAIK TANDATANGAN (.PNG)</label>
                                <input type="file" class="form-control" name="signature_file" accept=".png" required>
                                <div class="form-text">Sila muat naik fail imej tandatangan (latar belakang telus) format PNG sahaja.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Baris 6: Kata Laluan --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">KATA LALUAN</label>
                                <input type="password" class="form-control" name="password" required>
                                <small class="text-muted" style="font-size: 0.8rem;">
                                    * Min 8 aksara, huruf besar, kecil & nombor.
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">SAHKAN KATA LALUAN</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        {{-- Butang Submit --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan & Daftar Pengguna
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection