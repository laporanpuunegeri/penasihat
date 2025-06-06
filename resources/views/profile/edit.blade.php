@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold text-uppercase">Kemaskini Profil Pengguna</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ralat:</strong> Sila betulkan kesilapan berikut:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 p-4">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Emel</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="no_telefon" class="form-label">No Telefon</label>
                    <input type="text" class="form-control" name="no_telefon" value="{{ old('no_telefon', $user->no_telefon) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="negeri" class="form-label">Negeri</label>
                    <input type="text" class="form-control" name="negeri" value="{{ old('negeri', $user->negeri) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nama_jawatan" class="form-label">Jawatan</label>
                    <input type="text" class="form-control" name="nama_jawatan" value="{{ old('nama_jawatan', $user->nama_jawatan) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gred_jawatan" class="form-label">Gred Jawatan</label>
                    <input type="text" class="form-control" name="gred_jawatan" value="{{ old('gred_jawatan', $user->gred_jawatan) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="bahagian" class="form-label">Bahagian</label>
                    <input type="text" class="form-control" name="bahagian" value="{{ old('bahagian', $user->bahagian) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Peranan</label>
                    <select name="role" class="form-select" required>
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User Biasa</option>
                        <option value="pa" {{ old('role', $user->role) == 'pa' ? 'selected' : '' }}>PA</option>
                        <option value="yb" {{ old('role', $user->role) == 'yb' ? 'selected' : '' }}>YB Penasihat</option>
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
