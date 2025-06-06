<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akaun Baharu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4 w-100" style="max-width: 600px;">
        <h2 class="text-center mb-4 fw-bold text-primary">Daftar Akaun Baharu</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Alamat Emel</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label for="no_telefon" class="form-label">No Telefon</label>
                <input type="text" class="form-control" id="no_telefon" name="no_telefon" value="{{ old('no_telefon') }}" required>
            </div>

            <div class="mb-3">
                <label for="negeri" class="form-label">Negeri</label>
                <select name="negeri" id="negeri" class="form-select" required>
                    <option value="">-- Sila Pilih Negeri --</option>
                    @php
                        $states = [
                            "Johor","Kedah","Kelantan","Melaka","Negeri Sembilan","Pahang",
                            "Perak","Perlis","Pulau Pinang","Sabah","Sarawak","Selangor",
                            "Terengganu","Wilayah Persekutuan Kuala Lumpur",
                            "Wilayah Persekutuan Labuan","Wilayah Persekutuan Putrajaya"
                        ];
                    @endphp
                    @foreach ($states as $state)
                        <option value="{{ $state }}" {{ old('negeri') == $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="bahagian" class="form-label">Bahagian</label>
                <input type="text" class="form-control" id="bahagian" name="bahagian" value="{{ old('bahagian') }}" required>
            </div>

            <div class="mb-3">
                <label for="nama_jawatan" class="form-label">Nama Jawatan</label>
                <input type="text" class="form-control" id="nama_jawatan" name="nama_jawatan" value="{{ old('nama_jawatan') }}" required>
            </div>

            <div class="mb-3">
                <label for="gred_jawatan" class="form-label">Gred Jawatan</label>
                <input type="text" class="form-control" id="gred_jawatan" name="gred_jawatan" value="{{ old('gred_jawatan') }}" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Peranan</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">-- Sila Pilih Peranan --</option>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User Biasa</option>
                    <option value="pa" {{ old('role') == 'pa' ? 'selected' : '' }}>PA</option>
                    <option value="yb" {{ old('role') == 'yb' ? 'selected' : '' }}>YB Penasihat</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata Laluan</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Sahkan Kata Laluan</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Daftar Akaun</button>
            </div>

            <div class="mt-3 text-center">
                <a href="{{ route('login') }}">Sudah ada akaun? Log Masuk</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
