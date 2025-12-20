<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        .register-container {
            display: flex;
            gap: 40px;
            max-width: 1000px;
            width: 100%;
            align-items: stretch;
        }

        /* Left Side - Register Form */
        .register-card {
            flex: 1;
            background: white;
            border-radius: 8px;
            padding: 35px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }

        .register-card h2 {
            text-align: center;
            color: #1a1a2e;
            font-size: 26px;
            margin-bottom: 8px;
        }

        .register-card .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: white;
            border: 1px solid #e0e0e0;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .google-btn:hover {
            background: #f8f9fa;
            border-color: #4285F4;
            box-shadow: 0 2px 8px rgba(66, 133, 244, 0.2);
        }

        .divider {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
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
            font-size: 12px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
            color: #333;
            font-weight: 500;
            font-size: 13px;
        }

        .form-group label i {
            color: #667eea;
            font-size: 12px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .register-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Right Side - Info Cards */
        .info-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 320px;
        }

        .info-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .info-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .info-card.security .icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .info-card.features .icon {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }

        .info-card.support .icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .info-card h3 {
            color: white;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .info-card p {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            line-height: 1.5;
        }

        .features-list {
            list-style: none;
            margin-top: 12px;
        }

        .features-list li {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .features-list li i {
            color: #10b981;
            font-size: 10px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .register-container {
                flex-direction: column;
            }
            .info-cards {
                width: 100%;
                flex-direction: row;
            }
            .info-card {
                flex: 1;
            }
        }

        @media (max-width: 600px) {
            .info-cards {
                flex-direction: column;
            }
            .register-card {
                padding: 25px;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Left Side - Register Form -->
        <div class="register-card">
            <h2>Create Account</h2>
            <p class="subtitle">Join us and start your journey</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Google Sign Up -->
            <a href="{{ route('google.redirect') }}" class="google-btn">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Continue with Google
            </a>

            <div class="divider">
                <span>or register with email</span>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" placeholder="Min 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm password" required>
                    </div>
                </div>
                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <p class="login-link">
                Already have an account? <a href="{{ route('login') }}">Sign In</a>
            </p>
        </div>

        <!-- Right Side - Info Cards -->
        <div class="info-cards">
            <div class="info-card security">
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Platform</h3>
                <p>Your data is protected with industry-standard encryption and 2FA authentication.</p>
            </div>

            <div class="info-card features">
                <div class="icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3>What You Get</h3>
                <ul class="features-list">
                    <li><i class="fas fa-check"></i> Personal Dashboard</li>
                    <li><i class="fas fa-check"></i> Profile Management</li>
                    <li><i class="fas fa-check"></i> Two-Factor Auth</li>
                    <li><i class="fas fa-check"></i> Secure Login</li>
                </ul>
            </div>

            <div class="info-card support">
                <div class="icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Our team is here to help you anytime you need assistance.</p>
            </div>
        </div>
    </div>
</body>
</html>
