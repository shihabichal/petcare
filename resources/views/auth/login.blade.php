<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PetCare Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #EEF0FF 0%, #FFF4EE 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-wrapper {
            display: flex;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(124,111,247,0.15);
            max-width: 900px;
            width: 100%;
        }
        .login-left {
            background: linear-gradient(160deg, #7C6FF7, #5A52D5);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            color: white;
        }
        .login-left .brand-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin-bottom: 28px;
        }
        .login-left h1 { font-size: 30px; font-weight: 700; margin-bottom: 12px; }
        .login-left p { font-size: 15px; opacity: 0.8; line-height: 1.6; }
        .feature-list { margin-top: 32px; display: flex; flex-direction: column; gap: 12px; }
        .feature-item { display: flex; align-items: center; gap: 10px; font-size: 14px; opacity: 0.9; }
        .feature-item i { font-size: 18px; }

        .login-right {
            background: white;
            padding: 50px 40px;
            width: 380px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-right h2 { font-size: 24px; font-weight: 700; color: #2D3250; margin-bottom: 6px; }
        .login-right p  { font-size: 14px; color: #8A95A5; margin-bottom: 30px; }

        .form-group { margin-bottom: 18px; }
        .form-label { font-size: 13px; font-weight: 600; color: #2D3250; display: block; margin-bottom: 7px; }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #8A95A5;
            font-size: 16px;
        }
        .form-control {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #EAECF0;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: #2D3250;
            outline: none;
            transition: all 0.2s;
        }
        .form-control:focus { border-color: #7C6FF7; box-shadow: 0 0 0 3px rgba(124,111,247,0.12); }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #7C6FF7, #5A52D5);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(124,111,247,0.35);
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(124,111,247,0.5); }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
            background: #FFF0F0;
            color: #F76F6F;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        @media (max-width: 640px) {
            .login-left { display: none; }
            .login-right { width: 100%; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-left">
        <div class="brand-icon"><i class="bi bi-heart-fill"></i></div>
        <h1>Selamat Datang di PetCare</h1>
        <p>Kelola layanan perawatan hewan peliharaan Anda dengan lebih mudah, efisien, dan profesional.</p>
        <div class="feature-list">
            <div class="feature-item"><i class="bi bi-scissors"></i> Layanan Grooming Premium</div>
            <div class="feature-item"><i class="bi bi-house-heart"></i> Penitipan Hewan Peliharaan</div>
            <div class="feature-item"><i class="bi bi-truck"></i> Layanan Antar Jemput</div>
            <div class="feature-item"><i class="bi bi-whatsapp"></i> Notifikasi via WhatsApp</div>
        </div>
    </div>
    <div class="login-right">
        <h2>Masuk ke Akun</h2>
        <p>Silakan masukkan email dan password Anda</p>

        @if($errors->any())
            <div class="alert"><i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@petcare.com" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn-login">Masuk ke Dashboard</button>
        </form>
    </div>
</div>
</body>
</html>
