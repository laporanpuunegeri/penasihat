<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Laporan - Log Masuk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* --- SETUP ASAS --- */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #1a1a2e; /* Latar Belakang Gelap */
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; /* Elak scrollbar */
        }

        /* --- KONTENA UTAMA (BUKU) --- */
        .main-container {
            display: flex;
            width: 90%; 
            max-width: 1400px;
            height: 85vh; 
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.9); 
            border-radius: 15px;
            background-color: transparent; 
            position: relative;
            perspective: 1500px; /* Efek 3D */
        }

        .book-wrapper {
            display: flex;
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* GARIS TENGAH (SPINE) */
        .spine-divider {
            width: 4px; 
            height: 90%;
            background: linear-gradient(to bottom, transparent, #00ffff, transparent); /* Garis Neon */
            position: absolute;
            left: 50%;
            top: 5%;
            transform: translateX(-50%);
            z-index: 10; 
            box-shadow: 0 0 15px #00ffff;
        }

        /* --- PANEL KIRI (BRANDING) --- */
        .left-panel {
            flex: 1; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: radial-gradient(circle at center, #1f2a40 0%, #151525 100%);
            border-radius: 15px 0 0 15px;
            z-index: 5;
            text-align: center; /* Centerkan semua text dalam panel */
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-right: none;
            position: relative;
            overflow: hidden;
        }
        
        /* EFEK GLOW LATAR BELAKANG KIRI */
        .left-panel::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: #00ffff;
            filter: blur(150px);
            opacity: 0.1;
            border-radius: 50%;
            top: 20%;
            left: 20%;
            z-index: 0;
        }

        /* --- PANEL KANAN (BORANG) --- */
        .right-panel {
            flex: 1; 
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px;
            background-color: #1a1a2e; 
            border-radius: 0 15px 15px 0;
            z-index: 5;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-left: none;
        }

        /* --- LOGO --- */
        .logo-container { 
            z-index: 2; 
            margin-bottom: 30px;
            position: relative;
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .brand-logo { 
            width: 180px; 
            height: 180px; 
            object-fit: contain; 
            filter: drop-shadow(0 0 20px rgba(0, 255, 255, 0.6)); 
            animation: floatLogo 6s ease-in-out infinite;
        }
        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* --- TEKS TAJUK (DIKEMASKINI UTK CENTER) --- */
        .main-title { 
            font-weight: 900; 
            font-size: 3.5rem; 
            letter-spacing: 2px; 
            margin-bottom: 10px; 
            color: #ffffff; 
            text-shadow: 0 0 20px rgba(0, 255, 255, 0.8); 
            line-height: 1.1;
            text-transform: uppercase;
            z-index: 2;
            
            /* Center Settings */
            text-align: center;
            width: 100%;
            display: block;
        }
        .sub-title { 
            font-size: 1.3rem; 
            font-weight: 300; 
            color: #a0a0a0; 
            letter-spacing: 1px; 
            z-index: 2;
            
            /* Center Settings */
            text-align: center;
            width: 100%;
            display: block;
        }

        .version-badge { 
            background: rgba(0, 255, 255, 0.1); 
            border: 1px solid #00ffff; 
            color: #00ffff; 
            padding: 8px 20px; 
            border-radius: 50px; 
            font-size: 0.9rem; 
            font-weight: 600; 
            margin-top: 40px; 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
            z-index: 2;
        }

        /* --- BORANG LOGIN --- */
        .login-card { width: 100%; max-width: 450px; }
        .greeting-header { font-size: 2.2rem; font-weight: 800; margin-bottom: 10px; color: #ffffff; }
        .instruction-text { font-size: 1rem; color: #8888aa; margin-bottom: 30px; }
        
        .form-label { 
            font-size: 0.85rem; 
            font-weight: 700; 
            color: #aab; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            margin-bottom: 8px;
            display: block;
        }
        
        .input-group { margin-bottom: 25px; position: relative; }
        
        .form-control { 
            width: 100%;
            background-color: #252535; 
            color: #ffffff; 
            padding: 15px 15px 15px 45px; /* Padding kiri untuk ikon */
            border-radius: 8px; 
            font-size: 1rem; 
            border: 1px solid #3a3a50; 
            outline: none;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        
        /* Ikon dalam input */
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #00ffff;
            font-size: 1.1rem;
        }

        .form-control:focus { 
            border-color: #00ffff; 
            background-color: #2a2a3a; 
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3); 
        }
        
        .btn-login { 
            width: 100%;
            background: linear-gradient(90deg, #007bff 0%, #00ffff 100%); 
            padding: 15px; 
            border-radius: 8px; 
            font-size: 1.1rem; 
            font-weight: 800; 
            letter-spacing: 1px; 
            text-transform: uppercase;
            box-shadow: 0 5px 20px rgba(0, 255, 255, 0.3); 
            color: #1a1a2e; 
            border: none; 
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-login:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(0, 255, 255, 0.5);
        }

        .footer-links {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }
        .footer-links a, .footer-links label { color: #8888aa; text-decoration: none; transition: color 0.3s; cursor: pointer; }
        .footer-links a:hover { color: #00ffff; }

        .copyright { margin-top: 50px; text-align: center; color: #444466; font-size: 0.8rem; }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .main-container { flex-direction: column; height: auto; max-width: 95%; margin: 20px auto; }
            .left-panel { border-radius: 15px 15px 0 0; padding: 30px; }
            .right-panel { border-radius: 0 0 15px 15px; padding: 30px; }
            .spine-divider { display: none; }
            .main-title { font-size: 2.5rem; }
            body { overflow-y: auto; }
        }
    </style>
</head>
<body>

    <div class="main-container">
        
        <div class="book-wrapper">
            
            {{-- Divider Tengah Neon --}}
            <div class="spine-divider"></div>

            {{-- PANEL KIRI: BRANDING --}}
            <div class="left-panel">
                
                <div class="logo-container">
                    {{-- Pastikan path gambar betul --}}
                    <img src="{{ asset('images/logo-ai.png') }}" 
                         onerror="this.src='{{ asset('images/logo.png') }}'" 
                         alt="Logo AGC" 
                         class="brand-logo">
                </div>

                <h1 class="main-title">LAPORAN PENASIHAT</h1>
                <p class="sub-title">Jabatan Peguam Negara</p>
                
                <div class="version-badge">
                    <i class="fas fa-robot"></i> Versi 2.0 (AI Integrated)
                </div>
            </div>

            {{-- PANEL KANAN: BORANG LOGIN --}}
            <div class="right-panel">
                <div class="login-card">
                    
                    <div class="mb-4">
                        <h3 class="greeting-header">Selamat Kembali</h3>
                        <p class="instruction-text">Sila masukkan kelayakan anda untuk akses sistem.</p>
                    </div>

                    {{-- Paparan Error --}}
                    @if ($errors->any())
                        <div style="background: rgba(255,0,0,0.1); border: 1px solid red; color: #ff6666; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf 
                        
                        {{-- Input Email --}}
                        <div class="form-group">
                            <label class="form-label">ID PENGGUNA / EMEL</label>
                            <div class="input-group">
                                <i class="fas fa-user input-icon"></i>
                                <input type="email" name="email" class="form-control" placeholder="nama@agc.gov.my" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        {{-- Input Password --}}
                        <div class="form-group">
                            <label class="form-label">KATA LALUAN</label>
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>
                        
                        {{-- Butang Login --}}
                        <button type="submit" class="btn-login">
                            LOG MASUK SISTEM
                        </button>
                        
                        {{-- Pautan Bawah --}}
                        <div class="footer-links">
                            <div>
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Ingat saya</label>
                            </div>
                            <a href="{{ route('password.request') }}">Lupa Kata Laluan?</a>
                        </div>
                    </form>
                    
                    <div class="copyright">
                        &copy; {{ date('Y') }} KuEzyAlpha. Hak Cipta Terpelihara.
                    </div>

                </div>
            </div>
            
        </div>
    </div>

</body>
</html>