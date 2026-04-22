<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Aura Dating</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #00f5ff;
            --secondary: #a855f7;
            --glass: rgba(15, 23, 42, 0.72);
            --glass-border: rgba(255, 255, 255, 0.15);
        }

        * {
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 50% 30%, rgba(0, 245, 255, 0.08) 0%, transparent 50%),
                        linear-gradient(135deg, #0a0a1f 0%, #1a0b3d 50%, #0f172a 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                linear-gradient(rgba(0, 245, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 245, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridMove 50s linear infinite;
            z-index: -2;
        }

        body::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 30%, rgba(168, 85, 247, 0.18) 0%, transparent 50%),
                        radial-gradient(circle at 80% 70%, rgba(0, 245, 255, 0.18) 0%, transparent 50%);
            animation: orbPulse 30s ease infinite alternate;
            z-index: -1;
        }

        @keyframes gridMove {
            0% { background-position: 0 0; }
            100% { background-position: 60px 60px; }
        }

        @keyframes orbPulse {
            0% { opacity: 0.55; }
            100% { opacity: 0.85; }
        }

        .login-card {
            background: var(--glass);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 
                0 25px 60px -12px rgba(0, 0, 0, 0.55),
                inset 0 2px 0 rgba(255, 255, 255, 0.12);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            position: relative;
            opacity: 0;
            transform: translateY(30px) scale(0.97);
            animation: cardEntrance 1s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        @keyframes cardEntrance {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-header {
            background: linear-gradient(135deg, rgba(0, 245, 255, 0.18), rgba(168, 85, 247, 0.18));
            padding: 35px 40px 28px;
            text-align: center;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary), var(--secondary), transparent);
            animation: neonLine 5s linear infinite;
        }

        @keyframes neonLine {
            0% { transform: translateX(-150%); }
            100% { transform: translateX(300%); }
        }

        .login-header h3 {
            font-size: 1.85rem;
            font-weight: 600;
            letter-spacing: 2.5px;
            margin-bottom: 6px;
            background: linear-gradient(90deg, #00f5ff, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.95rem;
            margin: 0;
        }

        .login-body {
            padding: 35px 45px 40px;
        }

        .form-label {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        /* Password Field with Proper Rounded Corners */
        .password-wrapper {
            position: relative;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: white;
            border-radius: 14px;
            padding: 13px 18px;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(0, 245, 255, 0.18);
            background: rgba(255, 255, 255, 0.13);
        }

        /* Password Toggle Icon */
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.65);
            cursor: pointer;
            font-size: 1.18rem;
            z-index: 10;
            padding: 5px;
            border-radius: 50%;
        }

        .password-toggle:hover {
            color: var(--primary);
            background: rgba(0, 245, 255, 0.12);
        }

        /* Extra padding for password field to prevent text overlap with icon */
        .password-field {
            padding-right: 52px !important;
        }

        .btn-login {
            background: linear-gradient(135deg, #00f5ff, #a855f7);
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 14px;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(0, 245, 255, 0.35);
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 15px 40px rgba(168, 85, 247, 0.4);
        }

        .back-link {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            color: var(--primary);
        }

        @media (min-width: 992px) {
            .login-card {
                max-width: 440px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h3>AURA</h3>
            <p>Admin Control Center</p>
        </div>
        
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif
           
            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}"
                           placeholder="Email Address" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
               
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" 
                               class="form-control password-field @error('password') is-invalid @enderror"
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required>
                        <span class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
               
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label text-light" for="remember" style="font-size: 0.9rem;">
                            Remember me
                        </label>
                    </div>
                </div>
               
                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-arrow-right me-2"></i>
                    SIGN IN TO DASHBOARD
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function () {
            // Toggle password visibility
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle eye / eye-slash icon
            if (type === 'text') {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>