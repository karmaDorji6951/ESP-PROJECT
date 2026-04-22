<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Employee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 25px;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .login-header p {
            margin: 8px 0 0;
            opacity: 0.95;
            font-size: 0.95rem;
        }

        .login-body {
            padding: 40px 35px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .form-check {
            margin-bottom: 25px;
        }

        .form-check-input {
            border-radius: 4px;
            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
            border: 2px solid #cbd5e1;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            margin-left: 8px;
            cursor: pointer;
            color: #4a5568;
            font-size: 0.95rem;
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
            padding: 12px 15px;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #fee;
            color: #c33;
        }

        .login-footer {
            text-align: center;
            padding: 20px 35px;
            background-color: #f7fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 0.9rem;
            color: #718096;
        }

        .credentials-info {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 0.85rem;
        }

        .credentials-info strong {
            display: block;
            color: #0c4a6e;
            margin-bottom: 8px;
        }

        .credentials-info p {
            margin: 5px 0;
            color: #0c4a6e;
        }

        @media (max-width: 576px) {
            .login-body {
                padding: 25px 20px;
            }

            .login-header {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>🏢 ESP Portal</h2>
                <p>Employee Management System</p>
            </div>

            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="credentials-info">
                    <strong>📋 Demo Credentials:</strong>
                    <p><strong>Admin:</strong> admin@example.com / password</p>
                    <p><strong>Supervisor:</strong> supervisor@example.com / password</p>
                    <p><strong>Staff:</strong> staff@example.com / password</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter your email"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter your password"
                            required
                        >
                    </div>

                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                        >
                        <label class="form-check-label" for="remember">
                            Remember me for 24 hours
                        </label>
                    </div>

                    <button type="submit" class="btn-login">Login</button>
                </form>
            </div>

            <div class="login-footer">
                <p>All users will be redirected to their role-specific dashboard upon successful login.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
