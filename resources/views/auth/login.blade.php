<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            display: flex;
            gap: 40px;
            max-width: 1000px;
            width: 100%;
            align-items: stretch;
        }

        /* Left Side - Login Form */
        .login-card {
            flex: 1;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }

        .login-card h2 {
            text-align: center;
            color: #1a1a2e;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .login-card .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: white;
            border: 2px solid #e0e0e0;
            padding: 14px 24px;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }

        .google-btn:hover {
            background: #f8f9fa;
            border-color: #4285F4;
            box-shadow: 0 4px 15px rgba(66, 133, 244, 0.2);
            transform: translateY(-2px);
        }

        .divider {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            padding: 0 15px;
            color: #888;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* Right Side - Role Cards */
        .role-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 320px;
        }

        .role-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .role-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.15);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .role-card.admin {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.1) 100%);
            border-color: rgba(239, 68, 68, 0.3);
        }

        .role-card.user {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .role-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .role-card.admin .role-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .role-card.user .role-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .role-card h3 {
            color: white;
            font-size: 20px;
            margin-bottom: 8px;
        }

        .role-card p {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .role-features {
            list-style: none;
        }

        .role-features li {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .role-features li::before {
            content: '✓';
            color: #10b981;
            font-weight: bold;
        }

        .role-card.admin .role-features li::before {
            color: #ef4444;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
            }
            .role-cards {
                width: 100%;
                flex-direction: row;
            }
            .role-card {
                flex: 1;
            }
        }

        @media (max-width: 600px) {
            .role-cards {
                flex-direction: column;
            }
            .login-card {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Login Form -->
        <div class="login-card">
            <h2>Welcome Back</h2>
            <p class="subtitle">Sign in to continue to your dashboard</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Google Sign In -->
            <a href="{{ route('google.redirect') }}" class="google-btn">
                <svg width="20" height="20" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Continue with Google
            </a>

            <div class="divider">
                <span>or login with email</span>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-btn">Sign In</button>
            </form>

            <p class="register-link">
                Don't have an account? <a href="{{ route('register') }}">Create Account</a>
            </p>
        </div>

        <!-- Right Side - Role Cards -->
        <div class="role-cards">
            <div class="role-card admin">
                <div class="role-icon">👑</div>
                <h3>Admin Access</h3>
                <p>Full control over the platform with advanced management capabilities.</p>
                <ul class="role-features">
                    <li>Manage all users</li>
                    <li>View analytics & reports</li>
                    <li>System configuration</li>
                    <li>Security settings</li>
                </ul>
            </div>

            <div class="role-card user">
                <div class="role-icon">👤</div>
                <h3>User Access</h3>
                <p>Access your personal dashboard and manage your profile.</p>
                <ul class="role-features">
                    <li>Personal dashboard</li>
                    <li>Profile management</li>
                    <li>Secure authentication</li>
                    <li>Activity history</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
