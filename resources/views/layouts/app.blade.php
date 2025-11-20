<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistem Laporan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    @stack('styles')

    <style>
        canvas { background-color: #fff; padding: 10px; border-radius: 8px; }
        .nav-link.active { font-weight: bold; color: #0d6efd !important; background-color: rgba(13, 110, 253, 0.1); border-radius: 5px; }
        .collapse-inner { background: #f8f9fa; border-left: 3px solid #0d6efd; margin-left: 1rem; padding: 0.5rem 0; border-radius: 0 5px 5px 0; }
        .collapse-item { display: block; padding: 0.5rem 1rem; color: #555; text-decoration: none; font-size: 0.85rem; transition: all 0.2s; }
        .collapse-item:hover { background-color: #e9ecef; color: #0d6efd; text-decoration: none; }
        .collapse-item.active { color: #0d6efd; font-weight: bold; background-color: #e2e6ea; }
        .collapse-header { font-size: 0.7rem; text-transform: uppercase; color: #888; padding: 0.5rem 1rem; font-weight: bold; letter-spacing: 0.5px; }
        .nav-link[aria-expanded="true"] .fa-caret-down { transform: rotate(180deg); transition: transform 0.3s; }
    </style>
</head>
<body class="bg-light min-vh-100">
    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-2 bg-white shadow-sm p-3 d-none d-md-block" style="min-height: 100vh;">
                <h5 class="fw-bold text-primary"><i class="fas fa-bars mr-2"></i> MENU</h5>
                <hr>
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid w-50 mb-2">
                    <p class="mb-0 small fw-bold text-uppercase text-dark" style="line-height: 1.2;">JABATAN PEGUAM NEGARA</p>
                </div>

                @if(Auth::check())
                <div class="alert alert-light border text-center p-2 mb-3">
                    <small class="text-muted d-block">Selamat Datang,</small>
                    <strong>{{ Auth::user()->name }}</strong>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-sign-out-alt mr-1"></i> Log Keluar</button>
                    </form>
                </div>
                @endif

                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fas fa-home mr-2"></i> Utama</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('profile.*') ? 'active' : '' }}" href="{{ route('profile.show') }}"><i class="fas fa-user mr-2"></i> Profil</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('pergerakan.*') ? 'active' : '' }}" href="{{ route('pergerakan.index') }}"><i class="fas fa-walking mr-2"></i> Pergerakan Pegawai</a></li>
                    
                    <li class="nav-item my-2"><hr class="m-0"></li>
                    <li class="nav-item mb-2"><small class="text-uppercase text-muted fw-bold pl-3">Modul Laporan</small></li>
                    
                    @if(Auth::check() && 
                        (strtolower(Auth::user()->role) === 'pa' || 
                         strtolower(Auth::user()->role) === 'yb' || 
                         strtolower(Auth::user()->role) === 'eo' || 
                         Auth::user()->bahagian === 'Bahagian Pentadbiran'))
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('pentadbiran.*') ? 'active' : '' }}" href="{{ route('pentadbiran.index') }}">
                                <i class="fas fa-cogs mr-2"></i> Pentadbiran
                            </a>
                        </li>
                    @endif
                    
                    @if(Auth::check() && 
                        (strtolower(Auth::user()->role) === 'pa' || 
                         strtolower(Auth::user()->role) === 'yb' || 
                         strtolower(Auth::user()->role) === 'eo' || 
                         Auth::user()->bahagian === 'Bahagian Kewangan'))

                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('kewangan.*') ? '' : 'collapsed' }} d-flex justify-content-between align-items-center" 
                               href="#collapseKewangan" 
                               data-bs-toggle="collapse" 
                               role="button" 
                               aria-expanded="{{ Route::is('kewangan.*') ? 'true' : 'false' }}" 
                               aria-controls="collapseKewangan">
                                <span><i class="fas fa-coins mr-2"></i> Kewangan</span>
                                <i class="fas fa-caret-down text-muted small"></i>
                            </a>

                            <div id="collapseKewangan" class="collapse {{ Route::is('kewangan.*') ? 'show' : '' }}">
                                <div class="collapse-inner mt-1">
                                    <h6 class="collapse-header">Laporan Prestasi:</h6>
                                    <a class="collapse-item {{ Route::is('kewangan.index') ? 'active' : '' }}" href="{{ route('kewangan.index') }}">Prestasi Keseluruhan</a>
                                    <a class="collapse-item {{ Route::is('kewangan.suku_tahun') ? 'active' : '' }}" href="{{ route('kewangan.suku_tahun') }}">Prestasi Suku Tahun</a>
                                    <a class="collapse-item {{ Route::is('kewangan.perbandingan') ? 'active' : '' }}" href="{{ route('kewangan.perbandingan') }}">Perbandingan Tahunan</a>
                            </div>
                        </li>
                    @endif
                    
                    @if(Auth::check() && strtolower(Auth::user()->role) !== 'eo')
                        
                        <li class="nav-item"><a class="nav-link {{ Route::is('laporanpandanganundang.*') ? 'active' : '' }}" href="{{ route('laporanpandanganundang.index') }}"><i class="fas fa-gavel mr-2"></i> Pandangan Undang-Undang</a></li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('laporankesmahkamah.*') ? 'active' : '' }}" href="{{ route('laporankesmahkamah.index') }}"><i class="fas fa-balance-scale mr-2"></i> Kes Mahkamah</a>
                        </li>
                        
                        @if(strtolower(Auth::user()->role) === 'pa')
                            <li class="nav-item ml-3">
                                <a class="nav-link text-secondary small {{ Route::is('lampiran.*') ? 'active' : '' }}" href="{{ route('lampiran.index') }}">
                                    <i class="fas fa-angle-right mr-1"></i> Lampiran II - Kes Mahkamah
                                </a>
                            </li>
                        @endif

                        <li class="nav-item"><a class="nav-link {{ Route::is('laporangubalanundang.*') ? 'active' : '' }}" href="{{ route('laporangubalanundang.index') }}"><i class="fas fa-pen-fancy mr-2"></i> Gubalan Undang-Undang</a></li>
                        <li class="nav-item"><a class="nav-link {{ Route::is('laporanpindaanundang.*') ? 'active' : '' }}" href="{{ route('laporanpindaanundang.index') }}"><i class="fas fa-edit mr-2"></i> Pindaan Undang-Undang</a></li>
                        <li class="nav-item"><a class="nav-link {{ Route::is('laporansemakanundang.*') ? 'active' : '' }}" href="{{ route('laporansemakanundang.index') }}"><i class="fas fa-search mr-2"></i> Semakan Undang-Undang</a></li>
                        <li class="nav-item"><a class="nav-link {{ Route::is('laporanmesyuarat.*') ? 'active' : '' }}" href="{{ route('laporanmesyuarat.index') }}"><i class="fas fa-users mr-2"></i> Mesyuarat</a></li>
                        <li class="nav-item"><a class="nav-link {{ Route::is('kestatatertib.*') ? 'active' : '' }}" href="{{ route('kestatatertib.index') }}"><i class="fas fa-exclamation-circle mr-2"></i> Kes Tatatertib</a></li>
                        <li class="nav-item"><a class="nav-link {{ Route::is('lainlaintugasan.*') ? 'active' : '' }}" href="{{ route('lainlaintugasan.index') }}"><i class="fas fa-tasks mr-2"></i> Lain-lain Tugasan</a></li>
                        
                        @if(strtolower(Auth::user()->role) === 'pa' || strtolower(Auth::user()->role) === 'yb')
                            <li class="nav-item mt-2"><a class="nav-link btn btn-outline-primary text-left {{ Route::is('laporan.index') ? 'active text-white bg-primary' : '' }}" href="{{ route('laporan.index') }}"><i class="fas fa-file-alt mr-2"></i> Laporan Penuh</a></li>
                        @endif

                    @endif 
                    </ul>
            </aside>

            <main class="col-md-10 p-4 ml-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>