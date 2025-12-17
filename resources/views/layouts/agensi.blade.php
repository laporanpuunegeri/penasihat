<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Portal Warta | Agensi Luar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; overflow-x: hidden; }

        /* --- SIDEBAR STYLE --- */
        aside {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 280px; height: 100vh; overflow-y: auto;
            box-shadow: 4px 0 15px rgba(0,0,0,0.2); z-index: 1000;
            display: flex; flex-direction: column; transition: all 0.3s;
        }

        /* HEADER LOGO GAMBAR SEBENAR */
        .sidebar-logo-area {
            padding: 30px 20px; text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px; background: rgba(0,0,0,0.15);
        }
        
        /* Style untuk container logo supaya ada border neon */
        .sidebar-logo-img {
            width: 110px; height: 110px; 
            margin: 0 auto 15px auto;
            
            /* Border Bulat & Neon */
            border-radius: 50%;
            border: 2px solid #00d2d3;
            box-shadow: 0 0 15px rgba(0, 210, 211, 0.3); 
            background: rgba(0,0,0,0.2); /* Gelap sikit dalam bulatan */
            
            display: flex; align-items: center; justify-content: center;
            padding: 10px; /* Padding supaya logo tak rapat sangat ke tepi */
            transition: transform 0.3s ease;
        }
        .sidebar-logo-img:hover { transform: scale(1.05); }
        
        /* Gambar Logo */
        .logo-image {
            width: 100%; height: 100%; 
            object-fit: contain; /* Pastikan gambar tak terpenyek */
        }

        .brand-title { font-size: 1.2rem; font-weight: 800; letter-spacing: 1px; color: #fff; text-transform: uppercase; line-height: 1.2; margin-bottom: 5px; }
        .brand-subtitle { font-size: 0.75rem; color: #94a3b8; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; }

        /* ... CSS Lain Kekal Sama ... */
        .user-profile-compact {
            background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 12px;
            margin: 0 15px 20px 15px; border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex; align-items: center; gap: 15px;
        }
        .avatar-circle {
            width: 45px; height: 45px; background: linear-gradient(135deg, #06b6d4, #3b82f6);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: white; flex-shrink: 0; font-weight: bold;
        }
        .user-info { flex-grow: 1; min-width: 0; }
        .user-info h6 { font-size: 0.9rem; margin: 0; color: #fff; font-weight: 700; white-space: normal; line-height: 1.2; max-height: 2.4em; overflow: hidden; text-overflow: ellipsis; }
        .user-info span { font-size: 0.75rem; color: #cbd5e1; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .menu-label { font-size: 0.75rem; font-weight: 800; color: #64748b; padding: 0 25px; margin: 15px 0 10px 0; text-transform: uppercase; letter-spacing: 1px; }
        .nav-link {
            color: #cbd5e1 !important; padding: 12px 25px; font-size: 0.95rem; font-weight: 500;
            border-left: 4px solid transparent; transition: all 0.2s; display: flex; align-items: center; text-decoration: none; 
        }
        .nav-link i { width: 25px; text-align: center; margin-right: 10px; font-size: 1.1rem; }
        .nav-link:hover { background: rgba(255, 255, 255, 0.05); color: #fff !important; }
        .nav-link.active {
            background: linear-gradient(90deg, rgba(6, 182, 212, 0.15) 0%, rgba(6, 182, 212, 0) 100%);
            color: #22d3ee !important; border-left: 4px solid #06b6d4;
        }

        .main-content-wrapper { margin-left: 280px; padding: 30px; width: calc(100% - 280px); min-height: 100vh; }
        aside::-webkit-scrollbar { width: 5px; }
        aside::-webkit-scrollbar-thumb { background: #475569; border-radius: 5px; }
        aside::-webkit-scrollbar-track { background: transparent; }

        @media (max-width: 992px) {
            aside { transform: translateX(-100%); z-index: 1050; }
            .main-content-wrapper { margin-left: 0; width: 100%; padding: 15px; }
        }
        
        .mt-auto { margin-top: auto !important; }
        .btn-logout-sidebar {
            background: rgba(220, 38, 38, 0.1); color: #f87171 !important;
            margin: 0 15px 20px 15px; border-radius: 8px; justify-content: center;
            border: 1px solid rgba(220, 38, 38, 0.3); font-weight: 700;
        }
        .btn-logout-sidebar:hover { background: #dc2626; color: white !important; }
    </style>
</head>
<body>

    <div class="d-flex">
        
        <aside>
            <div class="sidebar-logo-area">
                    <img src="{{ asset('images/logo-ai.png') }}" 
                        onerror="this.src='{{ asset('images/logo.png') }}'" 
                        alt="Logo" class="sidebar-logo-img">
                    <div class="brand-title">SISTEM LAPORAN WARTA</div>
                    <div class="brand-subtitle">JABATAN PEGUAM NEGARA</div>
                </div>

            <div class="user-profile-compact">
                <div class="avatar-circle">
                    {{ substr(auth()->guard('agensi')->user()->nama_pegawai ?? 'U', 0, 1) }}
                </div>
                <div class="user-info">
                    <h6>{{ auth()->guard('agensi')->user()->nama_pegawai ?? 'Pengguna' }}</h6>
                    <span>{{ auth()->guard('agensi')->user()->nama_agensi ?? 'Agensi Luar' }}</span>
                </div>
            </div>

            <div class="menu-label">Menu Utama</div>
            
            <a href="{{ route('dashboard.warta') }}" class="nav-link {{ request()->routeIs('dashboard.warta') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            
            <a class="nav-link collapsed" href="#menuWarta" data-bs-toggle="collapse" role="button" aria-expanded="false">
    <div class="sb-nav-link-icon"><i class="fas fa-file-contract"></i></div>
    Permohonan Baru
    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-chevron-down ms-auto"></i></div>
</a>

<div class="collapse" id="menuWarta">
   <div class="bg-dark bg-opacity-25 py-2 rounded mb-2 ms-3 border-start border-info border-3">
    
    <a href="{{ route('permohonan.seksyen12') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 12
    </a>

    <a href="{{ route('permohonan.seksyen62') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 62
    </a>

    <a href="{{ route('permohonan.seksyen64') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 64
    </a>

    <a href="{{ route('permohonan.seksyen9798') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 97 & 98
    </a>

    <a href="{{ route('permohonan.seksyen130') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 130
    </a>

    <a href="{{ route('permohonan.seksyen168') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 168
    </a>

    <a href="{{ route('permohonan.seksyen175A') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 175A
    </a>

    <a href="{{ route('permohonan.seksyen175D') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 175D
    </a>

    <a href="{{ route('permohonan.seksyen261') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 261
    </a>

    <a href="{{ route('permohonan.seksyen263') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 263
    </a>

    <a href="{{ route('permohonan.seksyen326') }}" class="nav-link text-light py-2" style="font-size: 0.85rem;">
        <i class="fas fa-angle-right me-2 text-info"></i> Seksyen 326
    </a>

</div>
</div>
            
            <a href="#" class="nav-link">
                <i class="fas fa-history"></i> Sejarah Permohonan
            </a>
            
            <div class="mt-auto">
                <form action="{{ route('agensi.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link btn-logout-sidebar w-100">
                        <i class="fas fa-sign-out-alt"></i> LOG KELUAR
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content-wrapper">
            @yield('content')
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>