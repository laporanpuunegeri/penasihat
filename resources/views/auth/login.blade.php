<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4 w-100" style="max-width: 420px;">
        <h4 class="mb-4 text-center">Log Masuk</h4>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Alamat Emel</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus value="{{ old('email') }}">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Katalaluan</label>
                <input type="password" class="form-control" id="password" name="password" required>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me">Ingat saya</label>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">Log Masuk</button>
            </div>

            <div class="text-center">
                <a href="{{ route('register') }}">Belum ada akaun? Daftar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
