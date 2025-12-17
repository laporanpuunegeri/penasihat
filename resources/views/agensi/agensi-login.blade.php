<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk Portal Warta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e9ecef; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-login { width: 100%; max-width: 450px; border: none; shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="card card-login">
    <div class="card-header bg-dark text-white text-center py-4">
        <h4 class="mb-0">PORTAL WARTA</h4>
        <small>Log Masuk Agensi</small>
    </div>
    <div class="card-body p-4">

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agensi.login.store') }}">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Alamat Emel</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Kata Laluan</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-dark btn-lg">LOG MASUK</button>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('agensi.register') }}" class="text-decoration-none">Daftar Akaun Baru</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>