<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistem Laporan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Stackable styles for additional CSS (e.g. FullCalendar) -->
    @stack('styles')

    <style>
        canvas {
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light min-vh-100">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-2 bg-white shadow-sm p-3">
                <h5 class="fw-bold">MENU</h5>
                <hr>
                <div class="text-center mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid w-50">
                    <p class="mt-2 mb-0 small fw-bold">JABATAN PEGUAM NEGARA</p>
                </div>

                @if(Auth::check())
                <div class="text-muted small mb-3">
                    Selamat Datang, <strong>{{ Auth::user()->name }}</strong>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger mt-2 w-100">Log Keluar</button>
                    </form>
                </div>
                @endif
<ul class="nav flex-column">
    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">🏠 Utama</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('profile.show') }}">👤 Profil</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('pergerakan.index') }}">🚶‍♂️ Pergerakan Pegawai</a></li>
    
    <li class="nav-item"><hr></li>
    <li class="nav-item"><strong class="text-dark">📁 Laporan</strong></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('laporanpandanganundang.index') }}">Pandangan Undang-Undang</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('laporankesmahkamah.index') }}">Kes Mahkamah</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('laporangubalanundang.index') }}">Gubalan Undang-Undang</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('laporanpindaanundang.index') }}">Pindaan Undang-Undang</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('laporansemakanundang.index') }}">Semakan Undang-Undang</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('laporanmesyuarat.index') }}">Mesyuarat</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('kestatatertib.index') }}">Kes Tatatertib</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('lainlaintugasan.index') }}">Lain-lain Tugasan</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.index') }}">📄 Laporan</a></li>
</ul>


            </aside>

            <!-- Main Content -->
            <main class="col-md-10 p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS (required for dropdown/modal etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Stackable scripts (e.g. FullCalendar) -->
    @stack('scripts')
</body>
</html>
