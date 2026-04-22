<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Management System - ESP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .landing-container {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
        }

        .header-section {
            text-align: center;
            color: white;
            margin-bottom: 50px;
            animation: fadeInDown 0.8s ease;
        }

        .header-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header-section p {
            font-size: 1.3rem;
            opacity: 0.95;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
            animation: fadeInUp 0.8s ease;
        }

        .role-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.25);
        }

        .role-card .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .admin-card .icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .supervisor-card .icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .staff-card .icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .role-card .icon {
            color: white;
        }

        .role-card h2 {
            font-size: 1.8rem;
            color: #2d3748;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .role-card p {
            color: #718096;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .features {
            text-align: left;
            margin-bottom: 30px;
        }

        .features li {
            list-style: none;
            padding: 8px 0;
            color: #4a5568;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .features li:before {
            content: "✓";
            display: inline-block;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #e6fffa;
            color: #0d9488;
            text-align: center;
            line-height: 25px;
            margin-right: 12px;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 12px 35px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn-login:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .admin-card:hover .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .supervisor-card:hover .btn-login {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .staff-card:hover .btn-login {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            opacity: 0.9;
            animation: fadeIn 1s ease;
        }

        .footer p {
            margin: 5px 0;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 2.5rem;
            }

            .header-section p {
                font-size: 1.1rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .role-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="header-section">
            <h1>🏢 Employee System Portal</h1>
            <p>Manage your team and track productivity efficiently</p>
        </div>

        <div class="cards-grid">
            <!-- Admin Card -->
            <div class="role-card admin-card">
                <div class="icon">👨‍💼</div>
                <h2>Admin Panel</h2>
                <p>Complete system control and management</p>
                <ul class="features">
                    <li>Manage Users & Roles</li>
                    <li>Create Employees</li>
                    <li>View All Reports</li>
                    <li>System Monitoring</li>
                </ul>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-login">Admin Login</a>
                @endif
            </div>

            <!-- Supervisor Card -->
            <div class="role-card supervisor-card">
                <div class="icon">👨‍⚔️</div>
                <h2>Supervisor</h2>
                <p>Manage team and operations</p>
                <ul class="features">
                    <li>Manage Staff</li>
                    <li>Assign Tasks</li>
                    <li>Track Attendance</li>
                    <li>Approve Leaves</li>
                </ul>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-login">Supervisor Login</a>
                @endif
            </div>

            <!-- Staff Card -->
            <div class="role-card staff-card">
                <div class="icon">👨‍💻</div>
                <h2>Staff</h2>
                <p>View your tasks and requests</p>
                <ul class="features">
                    <li>Track My Tasks</li>
                    <li>View Attendance</li>
                    <li>Request Leave</li>
                    <li>View Dashboard</li>
                </ul>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-login">Staff Login</a>
                @endif
            </div>
        </div>

        <div class="footer">
            <p><strong>Employee System Portal</strong></p>
            <p>Streamline Your Team Management Process</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
