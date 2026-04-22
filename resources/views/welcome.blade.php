<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ESP Dashboard</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:500,600,700,800|space-grotesk:500,600,700&display=swap" rel="stylesheet" />

        <style>
            :root {
                --page-bg: #f4f8fb;
                --ink: #0f1e2c;
                --muted: #5f7386;
                --card: #ffffff;
                --line: #d8e2ec;
                --brand: #0f766e;
                --brand-soft: #d9f6f2;
                --accent: #f59e0b;
                --alert: #e11d48;
                --shadow: 0 24px 56px rgba(15, 30, 44, 0.12);
                --radius-lg: 22px;
                --radius-md: 16px;
                --radius-sm: 12px;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: "Manrope", sans-serif;
                background:
                    radial-gradient(circle at 10% 10%, #c9ece8 0%, transparent 35%),
                    radial-gradient(circle at 90% 5%, #ffe9be 0%, transparent 30%),
                    var(--page-bg);
                color: var(--ink);
                min-height: 100vh;
            }

            .shell {
                width: min(1160px, 94vw);
                margin: 26px auto;
                background: rgba(255, 255, 255, 0.88);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(216, 226, 236, 0.85);
                border-radius: 28px;
                box-shadow: var(--shadow);
                overflow: hidden;
                animation: rise-in 600ms ease-out;
            }

            .topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                padding: 18px 24px;
                border-bottom: 1px solid var(--line);
                background: linear-gradient(140deg, #ffffff, #f9fcff);
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
                color: var(--ink);
            }

            .brand-mark {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                display: grid;
                place-items: center;
                background: linear-gradient(155deg, #0f766e, #14b8a6);
                color: #ffffff;
                font-family: "Space Grotesk", sans-serif;
                font-weight: 700;
                letter-spacing: 0.08em;
                font-size: 12px;
            }

            .brand h1 {
                margin: 0;
                font-size: 1.05rem;
                font-weight: 800;
                line-height: 1.1;
            }

            .brand p {
                margin: 2px 0 0;
                color: var(--muted);
                font-size: 0.76rem;
            }

            .top-links {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .btn {
                text-decoration: none;
                border-radius: 999px;
                padding: 9px 16px;
                font-weight: 700;
                font-size: 0.85rem;
                border: 1px solid transparent;
                transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
            }

            .btn:hover {
                transform: translateY(-1px);
            }

            .btn-primary {
                background: var(--brand);
                color: #ffffff;
                box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
            }

            .btn-primary:hover {
                background: #0b625c;
            }

            .btn-secondary {
                border-color: var(--line);
                color: var(--ink);
                background: #ffffff;
            }

            .btn-secondary:hover {
                background: #f6fafc;
            }

            .content {
                padding: 28px 24px 24px;
            }

            .hero {
                display: grid;
                grid-template-columns: 1.55fr 1fr;
                gap: 18px;
            }

            .panel {
                border: 1px solid var(--line);
                border-radius: var(--radius-lg);
                background: var(--card);
                padding: 22px;
            }

            .hero-main {
                position: relative;
                overflow: hidden;
                background:
                    radial-gradient(circle at 20% 15%, rgba(20, 184, 166, 0.14), transparent 42%),
                    radial-gradient(circle at 90% 100%, rgba(245, 158, 11, 0.16), transparent 38%),
                    #ffffff;
            }

            .hero-main::after {
                content: "";
                position: absolute;
                top: 16px;
                right: 16px;
                width: 92px;
                height: 92px;
                border-radius: 24px;
                border: 1px solid rgba(15, 118, 110, 0.15);
                transform: rotate(14deg);
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: var(--brand-soft);
                color: #0f5e57;
                border-radius: 999px;
                padding: 6px 11px;
                font-size: 0.72rem;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                font-weight: 800;
            }

            .hero h2 {
                margin: 14px 0 9px;
                font-family: "Space Grotesk", sans-serif;
                font-size: clamp(1.35rem, 2.5vw, 2.2rem);
                line-height: 1.12;
                max-width: 17ch;
            }

            .hero p {
                margin: 0;
                color: var(--muted);
                font-size: 0.95rem;
                line-height: 1.65;
                max-width: 56ch;
            }

            .quick-actions {
                margin-top: 18px;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 14px;
                margin-top: 20px;
            }

            .kpi {
                border: 1px solid var(--line);
                border-radius: var(--radius-md);
                padding: 14px;
                background: #fcfeff;
                animation: float-up 560ms ease both;
            }

            .kpi:nth-child(2) {
                animation-delay: 120ms;
            }

            .kpi:nth-child(3) {
                animation-delay: 220ms;
            }

            .kpi span {
                color: var(--muted);
                font-size: 0.74rem;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                font-weight: 700;
            }

            .kpi strong {
                display: block;
                margin-top: 7px;
                font-size: 1.25rem;
                font-weight: 800;
                color: var(--ink);
            }

            .kpi small {
                color: #0f766e;
                font-size: 0.75rem;
                font-weight: 700;
            }

            .focus-card {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .focus-card h3 {
                margin: 0;
                font-size: 1rem;
                font-weight: 800;
            }

            .progress-track {
                width: 100%;
                height: 9px;
                background: #e8f2f9;
                border-radius: 999px;
                overflow: hidden;
            }

            .progress-track i {
                display: block;
                width: 78%;
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, #0f766e, #14b8a6);
            }

            .focus-list {
                list-style: none;
                padding: 0;
                margin: 0;
                display: grid;
                gap: 10px;
            }

            .focus-list li {
                padding: 10px 11px;
                border: 1px solid var(--line);
                border-radius: var(--radius-sm);
                font-size: 0.86rem;
                display: flex;
                justify-content: space-between;
                gap: 8px;
            }

            .focus-list b {
                font-weight: 700;
            }

            .focus-list em {
                font-style: normal;
                color: var(--muted);
                font-size: 0.78rem;
            }

            .lower-grid {
                margin-top: 18px;
                display: grid;
                grid-template-columns: 1.25fr 1fr;
                gap: 18px;
            }

            .section-title {
                margin: 0 0 14px;
                font-size: 0.95rem;
                font-weight: 800;
            }

            .activity-list {
                list-style: none;
                margin: 0;
                padding: 0;
                display: grid;
                gap: 12px;
            }

            .activity-list li {
                border: 1px solid var(--line);
                border-radius: var(--radius-sm);
                padding: 12px;
                display: grid;
                grid-template-columns: auto 1fr auto;
                gap: 12px;
                align-items: center;
            }

            .dot {
                width: 9px;
                height: 9px;
                border-radius: 999px;
            }

            .dot.ok {
                background: #10b981;
            }

            .dot.warn {
                background: var(--accent);
            }

            .dot.alert {
                background: var(--alert);
            }

            .activity-list h4 {
                margin: 0;
                font-size: 0.9rem;
            }

            .activity-list p {
                margin: 4px 0 0;
                color: var(--muted);
                font-size: 0.8rem;
            }

            .pill {
                font-size: 0.72rem;
                border-radius: 999px;
                padding: 5px 9px;
                font-weight: 700;
                background: #eef4fa;
                color: #325169;
            }

            .calendar {
                display: grid;
                gap: 10px;
            }

            .event {
                border: 1px solid var(--line);
                border-radius: var(--radius-sm);
                padding: 11px;
                display: grid;
                gap: 6px;
            }

            .event strong {
                font-size: 0.88rem;
            }

            .event span {
                color: var(--muted);
                font-size: 0.78rem;
            }

            .event.green {
                border-left: 4px solid #0f766e;
            }

            .event.gold {
                border-left: 4px solid var(--accent);
            }

            .event.red {
                border-left: 4px solid var(--alert);
            }

            .footer {
                margin-top: 18px;
                border-top: 1px solid var(--line);
                padding-top: 14px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 10px;
                color: var(--muted);
                font-size: 0.8rem;
            }

            @media (max-width: 980px) {
                .hero,
                .lower-grid {
                    grid-template-columns: 1fr;
                }

                .kpi-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 640px) {
                .shell {
                    width: min(100%, 100vw);
                    margin: 0;
                    border-radius: 0;
                    border-left: 0;
                    border-right: 0;
                    min-height: 100vh;
                }

                .topbar {
                    padding: 16px;
                    flex-direction: column;
                    align-items: stretch;
                }

                .top-links {
                    justify-content: flex-start;
                    flex-wrap: wrap;
                }

                .content {
                    padding: 16px;
                }

                .panel {
                    padding: 16px;
                }

                .hero p {
                    font-size: 0.9rem;
                }
            }

            @keyframes rise-in {
                from {
                    transform: translateY(14px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes float-up {
                from {
                    transform: translateY(8px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <header class="topbar">
                <a href="{{ url('/') }}" class="brand">
                    <span class="brand-mark">ESP</span>
                    <span>
                        <h1>Employee Success Portal</h1>
                        <p>Operations Dashboard</p>
                    </span>
                </a>

                @if (Route::has('login'))
                    <nav class="top-links" aria-label="Authentication links">
                        @auth
                            <a href="{{ url('/home') }}" class="btn btn-primary">Open Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-secondary">Create Account</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <section class="content">
                <div class="hero">
                    <article class="panel hero-main">
                        <span class="eyebrow">Live workspace update</span>
                        <h2>Run your team with clarity, speed, and confidence.</h2>
                        <p>
                            Track attendance, employee focus, and pending tasks in one place. This layout keeps the most important information visible first, so managers can act faster and teams can stay aligned.
                        </p>

                        <div class="quick-actions">
                            <a href="{{ url('/home') }}" class="btn btn-primary">View Attendance</a>
                            <a href="{{ url('/home') }}" class="btn btn-secondary">Open Task Board</a>
                            <a href="{{ url('/home') }}" class="btn btn-secondary">Review Leave Requests</a>
                        </div>

                        <div class="kpi-grid">
                            <div class="kpi">
                                <span>Active Employees</span>
                                <strong>128</strong>
                                <small>+9 this month</small>
                            </div>
                            <div class="kpi">
                                <span>On-Time Check-ins</span>
                                <strong>94.7%</strong>
                                <small>+2.1% weekly</small>
                            </div>
                            <div class="kpi">
                                <span>Open Tasks</span>
                                <strong>36</strong>
                                <small>12 high priority</small>
                            </div>
                        </div>
                    </article>

                    <aside class="panel focus-card">
                        <h3>Weekly Delivery Health</h3>
                        <div>
                            <p style="margin: 0 0 8px; color: var(--muted); font-size: 0.84rem;">Current sprint completion</p>
                            <div class="progress-track" role="img" aria-label="Sprint progress 78 percent">
                                <i></i>
                            </div>
                            <p style="margin: 8px 0 0; font-weight: 800;">78% complete</p>
                        </div>

                        <ul class="focus-list">
                            <li><b>Payroll Audit</b> <em>Due in 2 days</em></li>
                            <li><b>Policy Acknowledgements</b> <em>17 pending</em></li>
                            <li><b>Leave Conflict Review</b> <em>3 blocked</em></li>
                        </ul>
                    </aside>
                </div>

                <div class="lower-grid">
                    <section class="panel">
                        <h3 class="section-title">Recent Operations Activity</h3>
                        <ul class="activity-list">
                            <li>
                                <span class="dot ok"></span>
                                <div>
                                    <h4>Attendance imported for all departments</h4>
                                    <p>Data synchronized from biometric gateway.</p>
                                </div>
                                <span class="pill">Done</span>
                            </li>
                            <li>
                                <span class="dot warn"></span>
                                <div>
                                    <h4>Design review meeting moved to 3:00 PM</h4>
                                    <p>Calendar updated for project members.</p>
                                </div>
                                <span class="pill">Updated</span>
                            </li>
                            <li>
                                <span class="dot alert"></span>
                                <div>
                                    <h4>2 leave requests need manager approval</h4>
                                    <p>Pending longer than 24 hours.</p>
                                </div>
                                <span class="pill">Action</span>
                            </li>
                        </ul>
                    </section>

                    <section class="panel">
                        <h3 class="section-title">Today Schedule</h3>
                        <div class="calendar">
                            <article class="event green">
                                <strong>09:30 AM - Team Standup</strong>
                                <span>Product, Engineering, and QA</span>
                            </article>
                            <article class="event gold">
                                <strong>01:00 PM - Hiring Panel</strong>
                                <span>Interview loop for Backend Engineer</span>
                            </article>
                            <article class="event red">
                                <strong>04:00 PM - Compliance Checkpoint</strong>
                                <span>Finalize monthly employee records</span>
                            </article>
                        </div>
                    </section>
                </div>

                <footer class="footer">
                    <span>Employee Success Portal</span>
                    <span>Laravel v{{ Illuminate\Foundation\Application::VERSION }} | PHP v{{ PHP_VERSION }}</span>
                </footer>
            </section>
        </main>
    </body>
</html>
