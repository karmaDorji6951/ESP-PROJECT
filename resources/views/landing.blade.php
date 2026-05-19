<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | ESP Portal</title>
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
            color: #f5f1e8;
        }

        .landing-container {
            width: 100%;
            max-width: 820px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(44, 62, 80, 0.25);
        }

        .card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
            color: #f5f1e8;
            padding: 28px 26px;
            border: none;
        }

        .card-body {
            padding: 28px 26px;
            color: #2c3e50;
            background: #ffffff;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 18px;
            font-weight: 600;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(44, 62, 80, 0.3);
        }

        .muted {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-buildings fs-3"></i>
                    <div>
                        <h1 class="h4 mb-1">ESP Portal</h1>
                        <div class="muted">Employee Management System</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-4">
                    Welcome. Please log in to continue to your role-based dashboard.
                </p>

                <a href="{{ route('login', [], false) }}" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </a>
            </div>
        </div>

        <div class="text-center mt-3 muted" style="font-size: 0.9rem;">
            © {{ date('Y') }} ESP
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
