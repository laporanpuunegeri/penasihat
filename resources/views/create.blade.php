
@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold text-uppercase">Daftar Profil Pengguna</h3>

    <div class="card shadow-sm border-0 p-4">
        <form method="POST" action="{{ route('profile.store') }}">
            @csrf
            

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Emel</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="no_telefon" class="form-label">No Telefon</label>
                    <input type="text" class="form-control" name="no_telefon" value="{{ old('no_telefon', $user->no_telefon) }}">
                </div>
                <div class="col-md-6">
                    <label for="negeri" class="form-label">Negeri</label>
                    <select name="negeri" class="form-select" required>
                        <option value="">-- Sila Pilih Negeri --</option>
                        @php
                            $senaraiNegeri = [
                                'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan',
                                'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah',
                                'Sarawak', 'Selangor', 'Terengganu',
                                'Wilayah Persekutuan Kuala Lumpur',
                                'Wilayah Persekutuan Labuan',
                                'Wilayah Persekutuan Putrajaya'
                            ];
                        @endphp
                        @foreach ($senaraiNegeri as $negeri)
                            <option value="{{ $negeri }}" {{ old('negeri', $user->negeri) == $negeri ? 'selected' : '' }}>
                                {{ $negeri }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="bahagian" class="form-label">Bahagian</label>
                    <input type="text" class="form-control" name="bahagian" value="{{ old('bahagian', $user->bahagian) }}">
                </div>
                <div class="col-md-6">
                    <label for="nama_jawatan" class="form-label">Nama Jawatan</label>
                    <input type="text" class="form-control" name="nama_jawatan" value="{{ old('nama_jawatan', $user->nama_jawatan) }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="gred_jawatan" class="form-label">Gred Jawatan</label>
                <input type="text" class="form-control" name="gred_jawatan" value="{{ old('gred_jawatan', $user->gred_jawatan) }}">
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Kemaskini</button>
            </div>
        </form>
    </div>
</div>
@endsection
