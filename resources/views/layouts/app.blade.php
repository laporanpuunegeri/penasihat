<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Sistem Laporan | AGC</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        aside {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 4px 0 15px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo-area {
            padding: 40px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
            background: rgba(0,0,0,0.15);
        }

        .sidebar-logo-img {
            width: 140px;
            height: 140px;
            object-fit: contain;
            border-radius: 50%;
            margin-bottom: 20px;
            box-shadow: 0 0 30px rgba(6, 182, 212, 0.3); 
            background: #fff;
            padding: 5px;
            transition: transform 0.3s ease;
        }
        .sidebar-logo-img:hover { transform: scale(1.05); }

        .brand-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #fff;
            text-transform: uppercase;
            line-height: 1.2;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .user-profile-compact {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 12px;
            margin: 0 15px 15px 15px; 
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar-circle {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
            font-weight: bold;
        }
        .user-info {
            flex-grow: 1; 
            min-width: 0; 
        }
        .user-info h6 { 
            font-size: 0.9rem; 
            margin: 0; 
            color: #fff; 
            font-weight: 700; 
            white-space: normal; 
            line-height: 1.2;
            max-height: 2.4em; 
            overflow: hidden;
            text-overflow: ellipsis; 
        }
        .user-info span { 
            font-size: 0.75rem; 
            color: #cbd5e1; 
            display: block; 
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .menu-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #64748b;
            padding: 0 25px;
            margin-bottom: 10px;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-link {
            color: #cbd5e1 !important;
            padding: 12px 25px;
            font-size: 0.95rem;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            text-decoration: none; 
        }

        .nav-link i { width: 25px; text-align: center; margin-right: 10px; font-size: 1.1rem; }
        .nav-link:hover { background: rgba(255, 255, 255, 0.05); color: #fff !important; }
        
        .nav-link.active {
            background: linear-gradient(90deg, rgba(6, 182, 212, 0.15) 0%, rgba(6, 182, 212, 0) 100%);
            color: #22d3ee !important;
            border-left: 4px solid #06b6d4;
        }

        .collapse-inner {
            background: rgba(0, 0, 0, 0.3);
            margin: 5px 15px;
            border-radius: 8px;
            padding: 10px 0;
        }
        .collapse-item {
            color: #94a3b8 !important;
            padding: 8px 20px;
            display: block;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
            border-radius: 5px;
            margin: 0 10px;
        }
        .collapse-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff !important;
            transform: translateX(5px);
        }
        .collapse-item.active {
            color: #22d3ee !important;
            font-weight: 700;
            background: rgba(6, 182, 212, 0.1);
        }

        aside::-webkit-scrollbar { width: 5px; }
        aside::-webkit-scrollbar-thumb { background: #475569; border-radius: 5px; }
        aside::-webkit-scrollbar-track { background: transparent; }
    </style>
</head>
<body> 
    <div class="container-fluid">
        <div class="row">
            
            <aside class="col-md-2 d-none d-md-flex p-0">
                
                <div class="sidebar-logo-area">
                    <img src="{{ asset('images/logo-ai.png') }}" 
                        onerror="this.src='{{ asset('images/logo.png') }}'" 
                        alt="Logo" class="sidebar-logo-img">
                    <div class="brand-title">SISTEM LAPORAN</div>
                    <div class="brand-subtitle">JABATAN PEGUAM NEGARA</div>
                </div>

                @php
                    $user = Auth::user();
                    $role = strtolower(trim($user->role ?? '')); 
                    $bahagian = strtoupper(trim($user->bahagian ?? '')); 

                    $targetRoute = 'dashboard'; 

                    switch ($bahagian) {
                        case 'BAHAGIAN PENTADBIRAN & KEWANGAN':
                        case 'BAHAGIAN PENTADBIRAN DAN KEWANGAN':
                            $targetRoute = 'dashboard.pentadbirandankewangan';
                            break;
                        case 'BAHAGIAN GUAMAN':
                            $targetRoute = 'dashboard.guaman'; 
                            break;
                        case 'BAHAGIAN PENASIHAT':
                            $targetRoute = 'dashboard.penasihat';
                            break;
                        case 'BAHAGIAN PENDAKWAAN':
                            $targetRoute = 'dashboard.pendakwaan';
                            break;
                        case 'BAHAGIAN SEMAKAN':
                            $targetRoute = 'dashboard.semakan';
                            break;
                        case 'BAHAGIAN SYARIAH':
                            $targetRoute = 'dashboard.syariah';
                            break;
                        default:
                            if (in_array($role, ['super_admin', 'admin', 'administrator'])) {
                                $targetRoute = 'dashboard.admin';
                            }
                            break;
                    }

                    $dashboardRoute = Route::has($targetRoute) ? $targetRoute : 'dashboard';


                    $isSuperAdmin = in_array($role, ['super_admin', 'admin', 'administrator']);
                    $isBoss = ($role === 'boss');
                    $isCC = ($role === 'cc'); 
                    $isEO = ($role === 'eo'); 
                    $isYB = ($role === 'yb');
                    $isPA = ($role === 'pa');
                    $isMidAdmin = ($isPA || $isBoss); 

                    $isStaffKewPen = str_contains($bahagian, 'PENTADBIRAN') || str_contains($bahagian, 'KEWANGAN');

                    $isStaffGuaman = ($bahagian === 'BAHAGIAN GUAMAN');
                    
                    $isStaffLaporanPenasihat = in_array($bahagian, ['BAHAGIAN PENASIHAT', 'BAHAGIAN SEMAKAN', 'BAHAGIAN SYARIAH', 'BAHAGIAN PENDAKWAAN']); 
                    
                    $showKewangan = ($isSuperAdmin || $isStaffKewPen || $isMidAdmin || $isYB || $isCC || $isEO);
                    $showPentadbiran = ($isSuperAdmin || $isStaffKewPen || $isMidAdmin || $isYB || $isCC || $isEO);
                    
                    $showModulGuaman = ($isSuperAdmin || $isYB || $isBoss || $isStaffGuaman);

                    $showModulLaporanPenasihat = ($isSuperAdmin || $isYB || $isBoss || $isStaffLaporanPenasihat || ($isPA && $isStaffLaporanPenasihat)); 

                    $showDbus = ($isSuperAdmin || (in_array($role, ['cc', 'eo']) && $isStaffKewPen));

                    $showUrusAgensi = ($isSuperAdmin || $isMidAdmin); 
                    $showTetapanPengguna = ($isSuperAdmin || $isMidAdmin || $isCC || $isEO);
                    $showMenuTetapan = ($showTetapanPengguna || $showUrusAgensi);
                    
                    $showLaporanPenuh = ($isSuperAdmin || $isYB || $isBoss || $isPA) || $isStaffLaporanPenasihat;
                @endphp

                @if(Auth::check())
                <div class="user-profile-compact">
                    <div class="avatar-circle">{{ substr($user->name, 0, 1) }}</div>
                    <div class="user-info text-start">
                        <h6 title="{{ $user->name }}">{{ $user->name }}</h6>
                        <span>{{ Str::limit($user->bahagian ?? 'Umum', 20) }}</span>
                    </div>
                </div>

                <div class="px-3 mb-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-danger w-100 fw-bold d-flex align-items-center justify-content-center gap-2" style="border-radius: 8px;">
                            <i class="fas fa-power-off"></i> LOG KELUAR
                        </button>
                    </form>
                </div>
                @endif 

                <div style="flex-grow: 1; padding-bottom: 20px;">
                    
                    <div class="menu-label">Menu Utama</div>
                    <ul class="nav flex-column mb-3">
                        
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is($dashboardRoute) || Route::is('dashboard.*') ? 'active' : '' }}" 
                               href="{{ route($dashboardRoute) }}">
                                <i class="fas fa-chart-pie"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('profile.*') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                                <i class="fas fa-id-card"></i> Profil Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('pergerakan.index') && !Route::is('pergerakan.borang') ? 'active' : '' }}" 
                               href="{{ route('pergerakan.index') }}">
                                <i class="fas fa-walking"></i> Kalendar Pergerakan
                            </a>
                        </li>
                    </ul>

                    @if($showPentadbiran)
                        <div class="menu-label">Pentadbiran</div>
                        <ul class="nav flex-column mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('pentadbiran.laporan_prestasi.*') ? 'active' : '' }}" href="{{ route('pentadbiran.laporan_prestasi.index') }}">
                                    <i class="fas fa-chart-line"></i> Laporan PPUUN
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('pentadbiran.waran.*') ? 'active' : '' }}" href="{{ route('pentadbiran.waran.index') }}">
                                    <i class="fas fa-file-invoice"></i> Waran Perjawatan
                                </a>
                            </li>
                            @if($showDbus)
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('pentadbiran.dbus.*') ? 'active' : '' }}" href="{{ route('pentadbiran.dbus.index') }}">
                                    <i class="fas fa-money-check-alt"></i> D'BUS (OBB)
                                </a>
                            </li>
                            @endif
                        </ul>
                    @endif

                    {{-- BAHAGIAN KEWANGAN DITUKAR KEPADA PAPARAN STATIK --}}
                    @if($showKewangan)
                        <div class="menu-label">Kewangan</div>
                        <ul class="nav flex-column mb-3">
                            {{-- Modul Kewangan Utama (Senarai Rekod) --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('kewangan.index') ? 'active' : '' }}" 
                                   href="{{ route('kewangan.index') }}">
                                    <i class="fas fa-coins"></i> Senarai Rekod
                                </a>
                            </li>
                            {{-- Sub-modul 1 --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('kewangan.suku_tahun') ? 'active' : '' }}" 
                                   href="{{ route('kewangan.suku_tahun') }}">
                                    <i class="far fa-calendar-alt"></i> Prestasi Suku Tahun
                                </a>
                            </li>
                            {{-- Sub-modul 2 --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('kewangan.perbandingan') ? 'active' : '' }}" 
                                   href="{{ route('kewangan.perbandingan') }}">
                                    <i class="fas fa-chart-bar"></i> Perbandingan Tahunan
                                </a>
                            </li>
                        </ul>
                    @endif

                    @if($showModulGuaman)
                        <div class="menu-label">Modul Guaman</div>
                        <ul class="nav flex-column mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('guaman.*') ? 'active' : '' }}" href="{{ route('guaman.index') }}">
                                    <i class="fas fa-landmark"></i> Kes Guaman
                                </a>
                            </li>
                        </ul>
                    @endif

                    @if($showModulLaporanPenasihat)
                        <div class="menu-label">Modul Laporan</div>
                        <ul class="nav flex-column mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('laporanpandanganundang.*') ? 'active' : '' }}" href="{{ route('laporanpandanganundang.index') }}">
                                    <i class="fas fa-gavel"></i> Pandangan Undang
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Route::is('laporankesmahkamah.*') ? 'active' : '' }}" href="{{ route('laporankesmahkamah.index') }}"><i class="fas fa-balance-scale"></i> Kes Mahkamah</a></li>
                            
                            @if($role === 'pa')
                            <li class="nav-item"><a class="nav-link {{ Route::is('lampiran.*') ? 'active' : '' }}" href="{{ route('lampiran.index') }}"><i class="fas fa-file-contract"></i> Lampiran II</a></li>
                            @endif
                            
                            <li class="nav-item"><a class="nav-link {{ Route::is('laporangubalanundang.*') ? 'active' : '' }}" href="{{ route('laporangubalanundang.index') }}"><i class="fas fa-pen-nib"></i> Gubalan</a></li>
                            <li class="nav-item"><a class="nav-link {{ Route::is('laporanpindaanundang.*') ? 'active' : '' }}" href="{{ route('laporanpindaanundang.index') }}"><i class="fas fa-file-pen"></i> Pindaan</a></li>
                            <li class="nav-item"><a class="nav-link {{ Route::is('laporansemakanundang.*') ? 'active' : '' }}" href="{{ route('laporansemakanundang.index') }}"><i class="fas fa-magnifying-glass"></i> Semakan</a></li>
                            <li class="nav-item"><a class="nav-link {{ Route::is('laporanmesyuarat.*') ? 'active' : '' }}" href="{{ route('laporanmesyuarat.index') }}"><i class="fas fa-handshake"></i> Mesyuarat</a></li>
                            <li class="nav-item"><a class="nav-link {{ Route::is('kestatatertib.*') ? 'active' : '' }}" href="{{ route('kestatatertib.index') }}"><i class="fas fa-triangle-exclamation"></i> Tatatertib</a></li>
                            <li class="nav-item"><a class="nav-link {{ Route::is('lainlaintugasan.*') ? 'active' : '' }}" href="{{ route('lainlaintugasan.index') }}"><i class="fas fa-list-check"></i> Lain-lain</a></li>
                            
                            @if($showLaporanPenuh)
                                <li class="nav-item mt-2">
                                    <a class="nav-link btn btn-primary text-white text-center mx-2" href="{{ route('laporan.index') }}" style="justify-content: center;">
                                        <i class="fas fa-book-open me-2"></i> Laporan Penuh
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif

                    @if($showMenuTetapan)
                        <div class="menu-label text-warning">Tetapan</div>
                        <ul class="nav flex-column mb-5">
                            @if($showUrusAgensi)
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('agensi.*') ? 'active' : '' }}" href="{{ route('agensi.index') }}">
                                    <i class="fas fa-building"></i> Urus Agensi
                                </a>
                            </li>
                            @endif

                            @if($showTetapanPengguna)
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('tetapan.pengguna.*') ? 'active' : '' }}" href="{{ route('tetapan.pengguna.index') }}">
                                    <i class="fas fa-users-cog"></i> Urus Pengguna
                                </a>
                            </li>
                            @endif
                        </ul>
                    @endif

                </div>
                <div class="sidebar-footer"></div>
            </aside>
            
            <main class="col-md-10 ms-auto py-4 px-4 bg-light" style="min-height: 100vh;">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-4 border-success mb-4">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-4 border-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
                
                <div class="text-center mt-5 text-muted small">
                    &copy; {{ date('Y') }} Sistem Laporan Pejabat Penasihat Undang-Undang.
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>