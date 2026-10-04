<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Compare-Go</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #FAFAFA;
            color: #0F172A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
        }

        .auth-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 40px 36px 36px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .auth-header {
            margin-bottom: 26px;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .auth-brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0D9488;
            flex-shrink: 0;
        }

        .auth-brand-name {
            font-size: 24px;
            font-weight: 700;
            color: #0F172A;
            letter-spacing: -0.3px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: #64748B;
            line-height: 1.4;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13.5px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            height: 44px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            color: #0F172A;
            background-color: #FFFFFF;
            border: 1.5px solid #CBD5E1;
            border-radius: 8px;
            outline: none;
            transition: all 0.15s ease-in-out;
        }

        .form-input:focus {
            border-color: #0D9488;
            background-color: #F0FDFA;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.18);
        }

        .form-input.is-invalid {
            border-color: #EF4444;
            background-color: #FEF2F2;
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18);
        }

        .error-feedback {
            display: block;
            margin-top: 6px;
            font-size: 12.5px;
            color: #DC2626;
        }

        .form-checkbox-row {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 14px;
            margin-bottom: 24px;
            user-select: none;
        }

        .form-checkbox {
            width: 17px;
            height: 17px;
            border-radius: 4px;
            border: 1.5px solid #CBD5E1;
            accent-color: #0D9488;
            cursor: pointer;
        }

        .form-checkbox-label {
            font-size: 13.5px;
            color: #334155;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0D9488;
            color: #FFFFFF;
            font-size: 14.5px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out, transform 0.05s ease-in-out;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .btn-submit:hover {
            background-color: #0F766E;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        /* Demo credentials pill */
        .demo-credentials-box {
            margin-top: 24px;
            padding: 12px 14px;
            border-radius: 8px;
            background: #F8FAFC;
            border: 1px dashed #CBD5E1;
            font-size: 12px;
            color: #64748B;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .demo-badge-btn {
            background: #EEF2FF;
            color: #3730A3;
            border: 1px solid #C7D2FE;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
        }

        .demo-badge-btn:hover {
            background: #E0E7FF;
        }

        .alert-error {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        
        <div class="auth-header">
            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                </div>
                <div class="auth-brand-name">Compare-Go</div>
            </div>
            <div class="auth-subtitle">Sistem Analisis Pengadaan — Masuk ke akun Anda</div>
        </div>

        @if(session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" autocomplete="on">
            @csrf

            <div class="form-group">
                <label for="login" class="form-label">Email atau Username</label>
                <input 
                    type="text" 
                    id="login" 
                    name="login" 
                    class="form-input {{ $errors->has('login') ? 'is-invalid' : '' }}" 
                    value="{{ old('login') }}" 
                    placeholder=""
                    required 
                    autofocus
                >
                @if($errors->has('login'))
                    <span class="error-feedback">{{ $errors->first('login') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                    required
                >
                @if($errors->has('password'))
                    <span class="error-feedback">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div class="form-checkbox-row">
                <input type="checkbox" id="remember" name="remember" class="form-checkbox" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="form-checkbox-label">Ingat saya</label>
            </div>

            <button type="submit" class="btn-submit">
                Masuk
            </button>
        </form>

        @if(\App\Models\Setting::get('show_demo_accounts', true))
            {{-- Quick fill shortcuts for convenience --}}
            <div class="demo-credentials-box" style="flex-direction: column; align-items: stretch; gap: 10px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <strong style="color:#1E293B;">Login Admin:</strong><br>
                        <span><code>admin@hsitoperasional.com</code> &bull; Pass: <code>admin</code></span>
                    </div>
                    <button type="button" class="demo-badge-btn" onclick="fillAdmin()">
                        Gunakan
                    </button>
                </div>
                <div style="border-top: 1px dashed #E2E8F0; padding-top: 8px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <strong style="color:#1E293B;">Login User:</strong><br>
                        <span>Username: <code>it</code> &bull; Pass: <code>it</code></span>
                    </div>
                    <button type="button" class="demo-badge-btn" onclick="fillUser()">
                        Gunakan
                    </button>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    function fillAdmin() {
        document.getElementById('login').value = 'admin@hsitoperasional.com';
        document.getElementById('password').value = 'admin';
        document.getElementById('login').focus();
    }
    function fillUser() {
        document.getElementById('login').value = 'it';
        document.getElementById('password').value = 'it';
        document.getElementById('login').focus();
    }
</script>

</body>
</html>
