<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Employee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #7a9fb5 100%);
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
            box-shadow: 0 20px 60px rgba(44, 62, 80, 0.25);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
            color: #f5f1e8;
            padding: 30px 25px;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
            color: #f5f1e8;
        }

        .login-header p {
            margin: 8px 0 0;
            opacity: 0.95;
            font-size: 0.95rem;
            color: #f5f1e8;
        }

        .login-body {
            padding: 40px 35px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #d4c4a8;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            color: #2c3e50;
        }

        .form-control:focus {
            border-color: #7a9fb5;
            box-shadow: 0 0 0 3px rgba(122, 159, 181, 0.1);
            outline: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
            border: none;
            color: #f5f1e8;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(44, 62, 80, 0.4);
            color: #f5f1e8;
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
            border: 2px solid #d4c4a8;
        }

        .form-check-input:checked {
            background-color: #7a9fb5;
            border-color: #7a9fb5;
        }

        .form-check-label {
            margin-left: 8px;
            cursor: pointer;
            color: #2c3e50;
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
            background-color: #e8d9d9;
            color: #7a4a4a;
        }

        .login-footer {
            text-align: center;
            padding: 20px 35px;
            background-color: #f5f1e8;
            border-top: 1px solid #d4c4a8;
            font-size: 0.9rem;
            color: #7a6a5a;
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
                <h2><i class="bi bi-buildings me-2"></i>ESP Portal</h2>
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

                <form method="POST" action="{{ route('login.store', [], false) }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Email Address or Username</label>
                        <input
                            type="text"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter your email or username (e.g., Karma Wangdi)"
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
