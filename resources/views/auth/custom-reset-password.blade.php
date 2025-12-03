<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Tetapan Semula Kata Laluan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* BASE & LAYOUT */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #1a1a2e; 
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* BINGKAI LUAR PUTIH (KULIT BUKU) */
        .outer-frame {
            width: 90%; 
            max-width: 650px; 
            height: auto; 
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.7); 
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff; 
            padding: 10px; 
        }
        
        /* KONTENA UTAMA (HALAMAN GELAP) */
        .main-container {
            width: 100%;
            height: 100%;
            border-radius: 6px; 
            background-color: #24243e; 
            padding: 60px; 
            color: #ffffff;
        }

        /* TAJUK & TEKS ARAHAN */
        .greeting-header {
            font-size: 2.5rem; 
            font-weight: 900;
            margin-bottom: 15px; 
            color: #ffffff;
            text-align: center;
        }
        .instruction-text {
            font-size: 1rem;
            color: #bbbbbb;
            margin-bottom: 30px; 
            line-height: 1.5;
            text-align: center;
        }
        
        /* LOGO KECIL Aksen */
        .logo-small {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-small i {
            font-size: 40px; 
            color: #00ffff;
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }

        /* INPUT FIELD */
        .form-label {
            font-size: 1rem !important; 
            margin-bottom: 12px !important; 
            font-weight: 600;
            color: #dddddd !important;
            display: block;
        }

        .form-input {
            background-color: #33334d !important; 
            color: #ffffff !important; 
            padding: 16px !important; 
            border-radius: 8px !important; 
            font-size: 1.1rem !important; 
            border: 1px solid #444466 !important;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.3s;
        }
        .form-input:focus {
             border-color: #00ffff !important;
             box-shadow: 0 0 0 3px rgba(0, 255, 255, 0.2) !important;
             background-color: #33334d !important;
             outline: none;
        }

        /* BUTANG - Gaya Gempak Dikuatkan */
        .btn-submit {
            background: linear-gradient(45deg, #007bff 0%, #00ffff 100%); 
            padding: 18px 25px;
            border-radius: 8px; 
            font-size: 1.2rem; 
            font-weight: 900; 
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.4); 
            color: #1a1a2e !important; 
            border: none;
            width: 100%; 
            margin-top: 30px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            box-shadow: 0 7px 20px rgba(0, 255, 255, 0.6);
        }
        
        /* MESEJ STATUS & RALAT */
        .status-message {
            background-color: #cc0000; 
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 0.9rem;
            color: #ffcccc;
            margin-top: 5px;
        }

        /* Pautan Kembali */
        .back-link {
            font-size: 1rem !important;
            color: #00ffff !important;
            text-decoration: none !important;
            margin-top: 20px;
            display: block;
        }
    </style>

<div class="min-h-screen">
    <div class="outer-frame">
        <div class="main-container">

            <div class="logo-small">
                 <i class="fas fa-key"></i> 
            </div>
            
            <h3 class="greeting-header">Tetapkan Kata Laluan Baru</h3>
            
            <p class="instruction-text">
                Masukkan dan sahkan kata laluan baharu anda di bawah.
            </p>

            @if (session('status'))
                <div class="status-message" style="background-color:#28a745;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 status-message" style="background-color:#cc0000; color:white;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('custom.password.update') }}" novalidate>
                @csrf
                
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-6">
                    <label for="password" class="form-label">{{ __('Kata Laluan Baharu') }}</label>
                    <input type="password" name="password" required id="password"
                        class="form-input w-full" autocomplete="new-password">
                    
                    @error('password')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="form-label">{{ __('Sahkan Kata Laluan') }}</label>
                    <input type="password" name="password_confirmation" required id="password_confirmation"
                        class="form-input w-full" autocomplete="new-password">
                        
                    @error('password_confirmation')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    {{ __('SIMPAN KATA LALUAN') }}
                </button>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="back-link">
                        &larr; Kembali ke Log Masuk
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>