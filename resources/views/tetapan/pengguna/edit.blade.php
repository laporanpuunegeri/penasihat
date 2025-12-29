@extends('layouts.app')

@section('title', 'Kemaskini Pengguna')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Kemaskini Pengguna</h1>
        <a href="{{ route('tetapan.pengguna.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

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

    <div class="card shadow border-0 rounded-3">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="m-0 fw-bold"><i class="fas fa-user-edit me-2"></i> Kemaskini Maklumat: {{ $userToEdit->name }}</h6>
        </div>
        
        <div class="card-body p-4">
            
            {{-- Form Update --}}
            <form method="POST" action="{{ route('tetapan.pengguna.update', $userToEdit->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Wajib untuk Update --}}

                {{-- Baris 1: Nama & Emel --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">NAMA PEGAWAI</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $userToEdit->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">ALAMAT EMEL</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $userToEdit->email) }}" required>
                    </div>
                </div>

                {{-- Baris 2: Telefon & Negeri --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">NO. TELEFON</label>
                        <input type="text" class="form-control" name="no_telefon" value="{{ old('no_telefon', $userToEdit->no_telefon) }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">NEGERI</label>
                        
                        @if(Auth::user()->role == 'super_admin')
                            {{-- Super Admin tak boleh tukar negeri pengguna ke negeri lain --}}
                            <input type="text" class="form-control bg-light" value="{{ $userToEdit->negeri }}" readonly>
                            <input type="hidden" name="negeri" value="{{ $userToEdit->negeri }}">
                            <div class="form-text text-muted small"><i class="fas fa-lock"></i> Dikunci (Negeri sama dengan anda)</div>
                        @else
                            {{-- Admin HQ boleh tukar negeri --}}
                            <select name="negeri" class="form-select" required>
                                <option value="">-- Pilih Negeri --</option>
                                @php $states = ["Johor","Kedah","Kelantan","Melaka","Negeri Sembilan","Pahang","Perak","Perlis","Pulau Pinang","Sabah","Sarawak","Selangor","Terengganu","Wilayah Persekutuan Kuala Lumpur","Wilayah Persekutuan Labuan","Wilayah Persekutuan Putrajaya"]; @endphp
                                @foreach ($states as $state)
                                    <option value="{{ $state }}" {{ old('negeri', $userToEdit->negeri) == $state ? 'selected' : '' }}>{{ $state }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                {{-- Baris 3: Bahagian & Jawatan --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">BAHAGIAN</label>
                        <select name="bahagian" class="form-select" required>
                            <option value="">-- Pilih Bahagian --</option>
                            @php $bahagians = ["BAHAGIAN PENASIHAT","BAHAGIAN PENDAKWAAN", "BAHAGIAN SEMAKAN", "BAHAGIAN PENTADBIRAN & KEWANGAN", "BAHAGIAN GUAMAN", "BAHAGIAN SYARIAH"]; @endphp
                            @foreach ($bahagians as $bahagian)
                                <option value="{{ $bahagian }}" {{ old('bahagian', $userToEdit->bahagian) == $bahagian ? 'selected' : '' }}>{{ $bahagian }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">NAMA JAWATAN</label>
                        <input type="text" class="form-control" name="nama_jawatan" value="{{ old('nama_jawatan', $userToEdit->nama_jawatan) }}" required>
                    </div>
                </div>

                {{-- Baris 4: Gred & Peranan --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">GRED JAWATAN</label>
                        <input type="text" class="form-control" name="gred_jawatan" value="{{ old('gred_jawatan', $userToEdit->gred_jawatan) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">PERANAN SISTEM</label>
                        <select name="role" class="form-select" required>
                            <option value="user" {{ old('role', $userToEdit->role) == 'user' ? 'selected' : '' }}>User Biasa</option>
                            <option value="eo" {{ old('role', $userToEdit->role) == 'eo' ? 'selected' : '' }}>EO</option>
                            <option value="cc" {{ old('role', $userToEdit->role) == 'cc' ? 'selected' : '' }}>CC</option>
                            <option value="pa" {{ old('role', $userToEdit->role) == 'pa' ? 'selected' : '' }}>PA</option>
                            <option value="yb" {{ old('role', $userToEdit->role) == 'yb' ? 'selected' : '' }}>YB Penasihat</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Tandatangan --}}
                <h5 class="mb-3 text-primary"><i class="fas fa-signature me-2"></i> Tandatangan Digital</h5>
                <div class="row mb-4">
<div class="col-md-2 text-center">
    @if($userToEdit->signature_file)
        {{-- KOD BARU: Terus panggil variable, tak payah asset() atau storage/ --}}
        <img src="{{ $userToEdit->signature_file }}" class="img-thumbnail mb-2" style="max-height: 100px;">
        <div class="small text-muted">Semasa</div>
    @else
        <div class="text-muted small py-4 border bg-light rounded">Tiada Fail</div>
    @endif
</div>
                    <div class="col-md-10">
                        <label class="form-label fw-bold text-dark small">TUKAR TANDATANGAN (Pilihan)</label>
                        <input type="file" class="form-control" name="signature_file" accept=".png">
                        <div class="form-text">Biarkan kosong jika tidak mahu menukar tandatangan. (Format PNG sahaja)</div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Password (Optional) --}}
                <h5 class="mb-3 text-primary"><i class="fas fa-lock me-2"></i> Tetapan Semula Kata Laluan (Pilihan)</h5>
                <div class="alert alert-info py-2 small">
                    <i class="fas fa-info-circle me-1"></i> Hanya isi ruangan di bawah jika anda ingin menukar kata laluan pengguna ini.
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">KATA LALUAN BARU</label>
                        <input type="password" class="form-control" name="password" autocomplete="new-password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">SAHKAN KATA LALUAN</label>
                        <input type="password" class="form-control" name="password_confirmation">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-save me-2"></i> Simpan Kemaskini
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection