@extends('layouts.app') 
{{-- Gantikan 'layouts.app' dengan nama layout induk anda --}}

@section('title', 'Kemaskini Profil Pengguna')

@section('content')
<div class="container-fluid">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Kemaskini Profil</h1>
    </div>

    {{-- Mesej Status --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-4 border-success" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    {{-- Papar Error jika ada --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading small fw-bold">Ralat Validasi</h5>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 rounded-3">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="m-0 fw-bold"><i class="fas fa-edit me-2"></i> Borang Kemaskini Profil Anda</h6>
        </div>
        <div class="card-body p-4">
            
            {{-- 🔥 PENTING: Borang mesti ada method PUT dan enctype --}}
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT') 

                {{-- Bahagian 1: Data Peribadi --}}
                <h5 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i> Maklumat Peribadi & Jawatan</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">NAMA PEGAWAI</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">ALAMAT EMEL</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">NO. TELEFON</label>
                        <input type="text" class="form-control" name="no_telefon" value="{{ old('no_telefon', $user->no_telefon) }}" required>
                        @error('no_telefon')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">NEGERI</label>
                        <select name="negeri" class="form-select" required>
                            <option value="">-- Sila Pilih Negeri --</option>
                            @php
                                $states = ["Johor","Kedah","Kelantan","Melaka","Negeri Sembilan","Pahang","Perak","Perlis","Pulau Pinang","Sabah","Sarawak","Selangor","Terengganu","Wilayah Persekutuan Kuala Lumpur","Wilayah Persekutuan Labuan","Wilayah Persekutuan Putrajaya"];
                            @endphp
                            @foreach ($states as $state)
                                <option value="{{ $state }}" {{ old('negeri', $user->negeri) == $state ? 'selected' : '' }}>{{ $state }}</option>
                            @endforeach
                        </select>
                        @error('negeri')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">BAHAGIAN</label>
                        <select name="bahagian" class="form-select" required>
                            <option value="">-- Sila Pilih Bahagian --</option>
                            @php
                                $bahagians = ["BAHAGIAN PENASIHAT", "BAHAGIAN SEMAKAN", "BAHAGIAN PENTADBIRAN & KEWANGAN", "BAHAGIAN GUAMAN", "BAHAGIAN SYARIAH"];
                            @endphp
                            @foreach ($bahagians as $bahagian)
                                <option value="{{ $bahagian }}" {{ old('bahagian', $user->bahagian) == $bahagian ? 'selected' : '' }}>{{ $bahagian }}</option>
                            @endforeach
                        </select>
                        @error('bahagian')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">NAMA JAWATAN</label>
                        <input type="text" class="form-control" name="nama_jawatan" value="{{ old('nama_jawatan', $user->nama_jawatan) }}" required>
                        @error('nama_jawatan')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">GRED JAWATAN</label>
                        <input type="text" class="form-control" name="gred_jawatan" value="{{ old('gred_jawatan', $user->gred_jawatan) }}" required>
                        @error('gred_jawatan')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Bahagian 2: Tandatangan Digital --}}
                <h5 class="mb-3 text-primary"><i class="fas fa-signature me-2"></i> Pengurusan Tandatangan Digital</h5>
                
                <div class="p-3 border rounded mb-4 bg-light">
                    <label class="form-label fw-bold text-dark small d-block mb-2">TANDATANGAN SEMASA</label>
                    <div class="mb-3">
                        @if($user->signature_file)
                            {{-- PENTING: Pastikan anda sudah jalankan 'php artisan storage:link' --}}
                            <img src="{{ asset('storage/' . $user->signature_file) }}" alt="Tandatangan Digital" style="max-height: 80px; border: 1px solid #ddd; padding: 5px; background-color: #fff;">
                        @else
                            <span class="text-danger small d-block">Tiada fail tandatangan dimuat naik.</span>
                        @endif
                    </div>
                    
                    <label class="form-label fw-bold text-dark small mt-3">MUAT NAIK TANDATANGAN BAHARU (.PNG)</label>
                    <input type="file" class="form-control" name="signature_file" accept=".png">
                    <div class="form-text">Muat naik fail PNG sahaja (Max 2MB). Akan menggantikan tandatangan sedia ada. Biarkan kosong jika tidak mahu tukar.</div>
                    @error('signature_file')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                
                <hr class="my-4">
                
                {{-- Bahagian 3: Tukar Kata Laluan --}}
                <h5 class="mb-3 text-primary"><i class="fas fa-lock me-2"></i> Tukar Kata Laluan (Pilihan)</h5>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">KATA LALUAN SEMASA</label>
                        <input type="password" class="form-control" name="current_password" autocomplete="off">
                        @error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">KATA LALUAN BAHARU</label>
                        <input type="password" class="form-control" name="new_password" autocomplete="new-password">
                        <small class="text-muted" style="font-size: 0.8rem;">
                            * Min 8-12 aksara, besar, kecil & nombor.
                        </small>
                        @error('new_password')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">SAHKAN KATA LALUAN BAHARU</label>
                        <input type="password" class="form-control" name="new_password_confirmation" autocomplete="new-password">
                    </div>
                </div>

                {{-- Butang Submit --}}
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-sync-alt me-2"></i> Kemaskini Profil
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection