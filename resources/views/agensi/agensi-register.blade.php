<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Portal Warta</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* --- 1. LATAR BELAKANG (SAMA MACAM LOGIN) --- */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #1a1a2e; /* Biru Gelap */
            background-image: radial-gradient(circle at center, #1f2a40 0%, #151525 100%);
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Kita bagi scroll kalau screen kecil */
            min-height: 100vh;
        }

        /* --- 2. KAD UTAMA (LEBAR & GLOW) --- */
        .register-container {
            width: 100%;
            max-width: 900px; /* 🔥 LEBAR (WIDE) */
            background: rgba(26, 26, 46, 0.95); /* Gelap lutsinar sikit */
            border: 1px solid rgba(0, 255, 255, 0.1); /* Border neon halus */
            border-radius: 15px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.5); /* Shadow tebal */
            padding: 40px;
            position: relative;
            margin: 20px;
            
            /* Efek Glow Belakang Kad */
            box-shadow: 0 0 50px rgba(0, 255, 255, 0.05);
        }

        /* --- 3. HEADER & LOGO --- */
        .header-section {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
        }
        
        .brand-logo { 
            width: 100px; 
            height: 100px; 
            object-fit: contain; 
            filter: drop-shadow(0 0 15px rgba(0, 255, 255, 0.5)); 
            margin-bottom: 15px;
            animation: floatLogo 6s ease-in-out infinite;
        }
        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .main-title { 
            font-weight: 800; font-size: 2rem; color: #ffffff; margin: 0;
            text-shadow: 0 0 15px rgba(0, 255, 255, 0.6); text-transform: uppercase; letter-spacing: 1px;
        }
        .sub-title { color: #a0a0a0; font-size: 1rem; margin-top: 5px; font-weight: 300; }

        /* --- 4. SUSUNAN GRID (2 COLUMN) --- */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* 🔥 KIRI - KANAN */
            gap: 25px; /* Jarak antara input */
        }
        .full-width { grid-column: span 2; } /* Input panjang (macam email) */

        /* --- 5. STYLE INPUT (DARK MODE) --- */
        .form-group { margin-bottom: 5px; }
        
        .form-label { 
            font-size: 0.8rem; font-weight: 700; color: #00ffff; /* Label Neon */
            text-transform: uppercase; margin-bottom: 8px; display: block; letter-spacing: 0.5px;
        }
        
        .input-wrapper { position: relative; }
        
        .form-control { 
            width: 100%; background-color: #252535; color: #ffffff; 
            padding: 12px 15px 12px 45px; /* Padding kiri untuk ikon */
            border-radius: 8px; font-size: 1rem; border: 1px solid #3a3a50; 
            outline: none; transition: all 0.3s; box-sizing: border-box;
        }
        
        .input-icon { 
            position: absolute; left: 15px; top: 50%; transform: translateY(-50%); 
            color: #8888aa; font-size: 1rem; transition: color 0.3s;
        }

        /* Focus Effect */
        .form-control:focus { border-color: #00ffff; background-color: #2a2a3a; box-shadow: 0 0 15px rgba(0, 255, 255, 0.2); }
        .form-control:focus + .input-icon { color: #00ffff; } /* Ikon jadi neon bila focus */

        /* Dropdown Style */
        select.form-control option { background-color: #1a1a2e; color: white; padding: 10px; }

        /* --- 6. BUTANG & ERROR --- */
        .btn-register { 
            width: 100%; background: linear-gradient(90deg, #007bff 0%, #00ffff 100%); 
            padding: 15px; border-radius: 8px; font-size: 1.1rem; font-weight: 800; 
            text-transform: uppercase; box-shadow: 0 5px 20px rgba(0, 255, 255, 0.3); 
            color: #1a1a2e; border: none; cursor: pointer; transition: all 0.2s; margin-top: 20px;
        }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0, 255, 255, 0.5); }

        .login-link { text-align: center; margin-top: 25px; font-size: 0.95rem; color: #8888aa; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; }
        .login-link a { color: #00ffff; text-decoration: none; font-weight: 700; transition: all 0.3s; }
        .login-link a:hover { text-shadow: 0 0 10px #00ffff; }

        .text-danger { color: #ff4d4d; font-size: 0.8rem; margin-top: 5px; display: block; font-weight: 600; }

        /* RESPONSIVE (HP JADI 1 COLUMN) */
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; gap: 15px; }
            .full-width { grid-column: span 1; }
            .register-container { padding: 20px; margin: 10px; }
        }
    </style>
</head>
<body>

    <div class="register-container">
        
        <div class="header-section">
            <img src="{{ asset('images/logo-ai.png') }}" onerror="this.src='{{ asset('images/logo.png') }}'" alt="Logo" class="brand-logo">
            <h1 class="main-title">Pendaftaran Portal Warta</h1>
            <p class="sub-title">Sila isi maklumat agensi di bawah untuk pendaftaran akaun.</p>
        </div>

        <form method="POST" action="{{ route('agensi.register.store') }}">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Nama Pegawai</label>
                    <div class="input-wrapper">
                        <input type="text" name="nama_pegawai" class="form-control" placeholder="Nama Penuh" value="{{ old('nama_pegawai') }}" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    @error('nama_pegawai') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">No. Telefon</label>
                    <div class="input-wrapper">
                        <input type="text" name="no_telefon" class="form-control" placeholder="012-3456789" value="{{ old('no_telefon') }}" required>
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                    @error('no_telefon') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Alamat Emel Rasmi</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" class="form-control" placeholder="nama@agensi.gov.my" value="{{ old('email') }}" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Agensi / Jabatan</label>
                    <div class="input-wrapper">
                        <input type="text" name="nama_agensi" class="form-control" placeholder="Cth: Pejabat Tanah" value="{{ old('nama_agensi') }}" required>
                        <i class="fas fa-building input-icon"></i>
                    </div>
                    @error('nama_agensi') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Negeri</label>
                    <div class="input-wrapper">
                        <select name="negeri" class="form-control" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Wilayah Persekutuan'] as $ng)
                                <option value="{{ $ng }}" {{ old('negeri') == $ng ? 'selected' : '' }}>{{ $ng }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-map-marker-alt input-icon"></i>
                    </div>
                    @error('negeri') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kata Laluan</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" class="form-control" placeholder="Minima 8 aksara" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Sahkan Kata Laluan</label>
                    <div class="input-wrapper">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulang kata laluan" required>
                        <i class="fas fa-check-circle input-icon"></i>
                    </div>
                </div>

            </div> <button type="submit" class="btn-register">
                DAFTAR AKAUN SEKARANG <i class="fas fa-arrow-right ms-2"></i>
            </button>

            <div class="login-link">
                Sudah mempunyai akaun? <a href="{{ route('login') }}">Log Masuk Di Sini</a>
            </div>

        </form>
    </div>

</body>
</html>