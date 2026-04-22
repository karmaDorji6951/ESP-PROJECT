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
                --page-bg: #f8fafc;
                --sidebar-bg: #15262a;
                --sidebar-border: #1f3337;
                --ink: #0f1e2c;
                --ink-light: #1e293b;
                --muted: #64748b;
                --muted-light: #94a3b8;
                --card: #ffffff;
                --card-hover: #f8fafc;
                --line: #e2e8f0;
                --line-light: #cbd5e1;
                --brand: #0f766e;
                --brand-light: #14b8a6;
                --brand-soft: #ccf7f3;
                --brand-bg: #f0fdfc;
                --accent: #f59e0b;
                --accent-soft: #fef3c7;
                --alert: #e11d48;
                --alert-soft: #ffe4e6;
                --success: #10b981;
                --success-soft: #d1fae5;
                --shadow: 0 20px 50px rgba(15, 30, 44, 0.08);
                --shadow-sm: 0 8px 16px rgba(15, 30, 44, 0.06);
                --shadow-lg: 0 40px 80px rgba(15, 30, 44, 0.12);
                --radius-lg: 24px;
                --radius-md: 16px;
                --radius-sm: 12px;
                --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
                --transition-normal: 300ms cubic-bezier(0.4, 0, 0.2, 1);
            }

            * {
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                margin: 0;
                font-family: "Manrope", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                background: var(--page-bg);
                color: var(--ink);
                min-height: 100vh;
                display: grid;
                grid-template-columns: 280px 1fr;
                grid-template-rows: 1fr;
            }

            /* ====== SIDEBAR ====== */
            .sidebar {
                background: var(--sidebar-bg);
                border-right: 1px solid var(--sidebar-border);
                display: flex;
                flex-direction: column;
                padding: 0;
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
                animation: slideInLeft 400ms var(--transition-normal);
            }

            .sidebar-header {
                padding: 20px 16px;
                border-bottom: 1px solid var(--sidebar-border);
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
                color: #ffffff;
                transition: all var(--transition-fast);
            }

            .sidebar-header:hover {
                background: rgba(20, 184, 166, 0.08);
            }

            .sidebar-mark {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                display: grid;
                place-items: center;
                background: linear-gradient(135deg, var(--brand) 0%, var(--brand-light) 100%);
                color: #ffffff;
                font-family: "Space Grotesk", monospace;
                font-weight: 700;
                letter-spacing: 0.1em;
                font-size: 12px;
                flex-shrink: 0;
            }

            .sidebar-title {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .sidebar-nav {
                flex: 1;
                padding: 12px 8px;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .sidebar-nav::-webkit-scrollbar {
                width: 6px;
            }

            .sidebar-nav::-webkit-scrollbar-track {
                background: transparent;
            }

            .sidebar-nav::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.15);
                border-radius: 3px;
            }

            .nav-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 16px;
                color: rgba(255, 255, 255, 0.7);
                text-decoration: none;
                border-radius: var(--radius-sm);
                transition: all var(--transition-fast);
                font-weight: 600;
                font-size: 0.9rem;
                cursor: pointer;
                position: relative;
            }

            .nav-item:hover {
                background: rgba(20, 184, 166, 0.12);
                color: #ffffff;
            }

            .nav-item.active {
                background: linear-gradient(135deg, rgba(15, 118, 110, 0.2) 0%, rgba(20, 184, 166, 0.1) 100%);
                color: var(--brand-light);
                border-left: 3px solid var(--brand-light);
                padding-left: 13px;
            }

            .nav-icon {
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                flex-shrink: 0;
            }

            .nav-label {
                flex: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .nav-badge {
                background: var(--alert);
                color: #ffffff;
                font-size: 0.7rem;
                padding: 2px 6px;
                border-radius: 999px;
                font-weight: 700;
                flex-shrink: 0;
            }

            .sidebar-divider {
                height: 1px;
                background: var(--sidebar-border);
                margin: 8px 0;
            }

            .sidebar-footer {
                padding: 12px 16px;
                border-top: 1px solid var(--sidebar-border);
                display: grid;
                gap: 8px;
            }

            .user-info {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px;
                border-radius: var(--radius-sm);
                background: rgba(20, 184, 166, 0.1);
                text-decoration: none;
                color: #ffffff;
                transition: all var(--transition-fast);
            }

            .user-info:hover {
                background: rgba(20, 184, 166, 0.15);
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--brand), var(--brand-light));
                display: flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                font-weight: 700;
                font-size: 0.85rem;
                flex-shrink: 0;
            }

            .user-details {
                flex: 1;
                min-width: 0;
            }

            .user-name {
                margin: 0;
                font-size: 0.85rem;
                font-weight: 700;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .user-role {
                margin: 2px 0 0;
                font-size: 0.7rem;
                color: rgba(255, 255, 255, 0.6);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* ====== MAIN CONTENT ====== */
            .main-container {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                padding: 16px 28px;
                background: #ffffff;
                border-bottom: 1px solid var(--line);
                box-shadow: var(--shadow-sm);
            }

            .topbar-left {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .menu-toggle {
                display: none;
                background: none;
                border: none;
                color: var(--ink);
                font-size: 24px;
                cursor: pointer;
                padding: 8px;
                border-radius: var(--radius-sm);
                transition: all var(--transition-fast);
            }

            .menu-toggle:hover {
                background: var(--page-bg);
            }

            .breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.9rem;
                color: var(--muted);
            }

            .breadcrumb strong {
                color: var(--brand);
                font-weight: 700;
            }

            .topbar-right {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .search-box {
                display: flex;
                align-items: center;
                gap: 10px;
                background: var(--page-bg);
                border: 1px solid var(--line);
                border-radius: 999px;
                padding: 8px 14px;
                transition: all var(--transition-fast);
            }

            .search-box:focus-within {
                border-color: var(--brand);
                box-shadow: 0 0 0 3px var(--brand-bg);
            }

            .search-box input {
                border: none;
                background: transparent;
                outline: none;
                font-family: inherit;
                font-size: 0.9rem;
                width: 200px;
                color: var(--ink);
            }

            .search-box input::placeholder {
                color: var(--muted);
            }

            .icon-btn {
                width: 40px;
                height: 40px;
                border-radius: 999px;
                border: none;
                background: var(--page-bg);
                color: var(--ink);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all var(--transition-fast);
                font-size: 18px;
            }

            .icon-btn:hover {
                background: var(--line);
            }

            .content {
                flex: 1;
                padding: 32px 28px;
                overflow-y: auto;
            }

            .shell {
                display: none;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 14px;
                text-decoration: none;
                color: var(--ink);
                transition: transform var(--transition-fast);
                flex-shrink: 0;
            }

            .brand:hover {
                transform: translateY(-2px);
            }

            .brand-mark {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                display: grid;
                place-items: center;
                background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
                color: #ffffff;
                font-family: "Space Grotesk", monospace;
                font-weight: 700;
                letter-spacing: 0.1em;
                font-size: 13px;
                box-shadow: 0 8px 16px rgba(15, 118, 110, 0.2);
                flex-shrink: 0;
            }

            .brand h1 {
                margin: 0;
                font-size: 1.1rem;
                font-weight: 800;
                line-height: 1.2;
                letter-spacing: -0.01em;
            }

            .brand p {
                margin: 3px 0 0;
                color: var(--muted);
                font-size: 0.8rem;
                font-weight: 500;
            }

            .top-links {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .btn {
                text-decoration: none;
                border-radius: 999px;
                padding: 10px 20px;
                font-weight: 700;
                font-size: 0.9rem;
                border: 1px solid transparent;
                transition: all var(--transition-fast);
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                white-space: nowrap;
            }

            .btn:active {
                transform: scale(0.98);
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--brand) 0%, var(--brand-light) 100%);
                color: #ffffff;
                box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #0b625c 0%, #0d9b93 100%);
                box-shadow: 0 12px 28px rgba(15, 118, 110, 0.4);
                transform: translateY(-2px);
            }

            .btn-secondary {
                border-color: var(--line-light);
                color: var(--ink);
                background: var(--card-hover);
            }

            .btn-secondary:hover {
                background: #f1f5f9;
                border-color: var(--line);
                transform: translateY(-1px);
            }

            .content {
                padding: 32px 28px;
            }

            .hero {
                display: grid;
                grid-template-columns: 1.6fr 1fr;
                gap: 20px;
                margin-bottom: 20px;
            }

            .panel {
                border: 1px solid var(--line);
                border-radius: var(--radius-lg);
                background: var(--card);
                padding: 24px;
                transition: all var(--transition-normal);
                box-shadow: var(--shadow-sm);
            }

            .panel:hover {
                border-color: var(--line-light);
                box-shadow: var(--shadow);
            }

            .hero-main {
                position: relative;
                overflow: hidden;
                background:
                    radial-gradient(circle at 25% 20%, rgba(20, 184, 166, 0.12) 0%, transparent 45%),
                    radial-gradient(circle at 92% 95%, rgba(245, 158, 11, 0.1) 0%, transparent 40%),
                    linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            }

            .hero-main::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: 
                    radial-gradient(circle at 20% 30%, rgba(20, 184, 166, 0.08), transparent 50%);
                pointer-events: none;
            }

            .hero-main::after {
                content: "";
                position: absolute;
                top: 12px;
                right: 12px;
                width: 110px;
                height: 110px;
                border-radius: 28px;
                border: 2px solid rgba(15, 118, 110, 0.08);
                transform: rotate(18deg);
                pointer-events: none;
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: var(--brand-bg);
                color: var(--brand);
                border-radius: 999px;
                padding: 7px 13px;
                font-size: 0.75rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 800;
                border: 1px solid rgba(15, 118, 110, 0.15);
                animation: slideDown 600ms var(--transition-normal);
            }

            .eyebrow::before {
                content: "✦";
                font-size: 0.6rem;
            }

            .hero h2 {
                margin: 16px 0 12px;
                font-family: "Space Grotesk", sans-serif;
                font-size: clamp(1.5rem, 3vw, 2.4rem);
                line-height: 1.15;
                max-width: 18ch;
                font-weight: 800;
                letter-spacing: -0.02em;
                animation: slideDown 700ms var(--transition-normal) 100ms both;
            }

            .hero p {
                margin: 0;
                color: var(--muted);
                font-size: 1rem;
                line-height: 1.7;
                max-width: 60ch;
                animation: slideDown 800ms var(--transition-normal) 200ms both;
            }

            .quick-actions {
                margin-top: 20px;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                animation: slideDown 900ms var(--transition-normal) 300ms both;
            }

            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 16px;
                margin-top: 22px;
            }

            .kpi {
                border: 1px solid var(--line);
                border-radius: var(--radius-md);
                padding: 18px;
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                animation: float-up 600ms ease-out both;
                transition: all var(--transition-normal);
                position: relative;
                overflow: hidden;
            }

            .kpi::before {
                content: "";
                position: absolute;
                top: 0;
                right: 0;
                width: 100px;
                height: 100px;
                background: radial-gradient(circle, rgba(20, 184, 166, 0.08), transparent 70%);
                border-radius: 50%;
                pointer-events: none;
            }

            .kpi:hover {
                border-color: var(--brand-light);
                box-shadow: 0 12px 24px rgba(20, 184, 166, 0.12);
                transform: translateY(-4px);
            }

            .kpi:nth-child(1) {
                animation-delay: 100ms;
            }

            .kpi:nth-child(2) {
                animation-delay: 200ms;
            }

            .kpi:nth-child(3) {
                animation-delay: 300ms;
            }

            .kpi span {
                color: var(--muted);
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                font-weight: 700;
                position: relative;
                z-index: 1;
            }

            .kpi strong {
                display: block;
                margin-top: 8px;
                font-size: 1.65rem;
                font-weight: 800;
                color: var(--ink);
                position: relative;
                z-index: 1;
            }

            .kpi small {
                color: var(--brand);
                font-size: 0.8rem;
                font-weight: 700;
                margin-top: 6px;
                display: block;
                position: relative;
                z-index: 1;
            }

            .focus-card {
                display: flex;
                flex-direction: column;
                gap: 18px;
                position: relative;
            }

            .focus-card h3 {
                margin: 0;
                font-size: 1.05rem;
                font-weight: 800;
                letter-spacing: -0.01em;
            }

            .progress-track {
                width: 100%;
                height: 10px;
                background: var(--brand-bg);
                border-radius: 999px;
                overflow: hidden;
                border: 1px solid rgba(15, 118, 110, 0.15);
            }

            .progress-track i {
                display: block;
                width: 78%;
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, var(--brand), var(--brand-light));
                animation: growWidth 1200ms var(--transition-normal) 400ms both;
                box-shadow: 0 0 12px rgba(20, 184, 166, 0.3);
            }

            .focus-list {
                list-style: none;
                padding: 0;
                margin: 0;
                display: grid;
                gap: 10px;
            }

            .focus-list li {
                padding: 12px 14px;
                border: 1px solid var(--line);
                border-radius: var(--radius-sm);
                font-size: 0.9rem;
                display: flex;
                justify-content: space-between;
                gap: 10px;
                align-items: center;
                transition: all var(--transition-fast);
                background: #f9fafb;
            }

            .focus-list li:hover {
                background: var(--card);
                border-color: var(--brand-light);
                box-shadow: 0 4px 8px rgba(15, 118, 110, 0.1);
            }

            .focus-list b {
                font-weight: 700;
                color: var(--ink);
            }

            .focus-list em {
                font-style: normal;
                color: var(--muted);
                font-size: 0.82rem;
                font-weight: 500;
            }

            .lower-grid {
                display: grid;
                grid-template-columns: 1.3fr 1fr;
                gap: 20px;
            }

            .section-title {
                margin: 0 0 16px;
                font-size: 1rem;
                font-weight: 800;
                letter-spacing: -0.01em;
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
                padding: 14px;
                display: grid;
                grid-template-columns: auto 1fr auto;
                gap: 12px;
                align-items: flex-start;
                background: #f9fafb;
                transition: all var(--transition-fast);
            }

            .activity-list li:hover {
                background: var(--card);
                border-color: var(--line-light);
                box-shadow: 0 4px 12px rgba(15, 30, 44, 0.08);
                transform: translateX(4px);
            }

            .dot {
                width: 11px;
                height: 11px;
                border-radius: 999px;
                flex-shrink: 0;
                margin-top: 3px;
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
            }

            .dot.ok {
                background: var(--success);
                box-shadow: 0 0 8px rgba(16, 185, 129, 0.4);
            }

            .dot.warn {
                background: var(--accent);
                box-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
            }

            .dot.alert {
                background: var(--alert);
                box-shadow: 0 0 8px rgba(225, 29, 72, 0.4);
            }

            .activity-list h4 {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 700;
                color: var(--ink);
            }

            .activity-list p {
                margin: 4px 0 0;
                color: var(--muted);
                font-size: 0.82rem;
                line-height: 1.5;
            }

            .pill {
                font-size: 0.75rem;
                border-radius: 999px;
                padding: 6px 11px;
                font-weight: 700;
                background: #eef2f7;
                color: #475569;
                flex-shrink: 0;
                white-space: nowrap;
            }

            .calendar {
                display: grid;
                gap: 12px;
            }

            .event {
                border: 1px solid var(--line);
                border-radius: var(--radius-sm);
                padding: 13px;
                display: grid;
                gap: 6px;
                background: #f9fafb;
                transition: all var(--transition-fast);
                border-left: 4px solid transparent;
                position: relative;
            }

            .event::before {
                content: "";
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                border-radius: 4px 0 0 4px;
            }

            .event:hover {
                background: var(--card);
                box-shadow: 0 4px 12px rgba(15, 30, 44, 0.08);
                transform: translateX(4px);
            }

            .event strong {
                font-size: 0.92rem;
                font-weight: 700;
                color: var(--ink);
            }

            .event span {
                color: var(--muted);
                font-size: 0.8rem;
                font-weight: 500;
            }

            .event.green {
                border-left-color: var(--success);
            }

            .event.green::before {
                background: var(--success);
            }

            .event.gold {
                border-left-color: var(--accent);
            }

            .event.gold::before {
                background: var(--accent);
            }

            .event.red {
                border-left-color: var(--alert);
            }

            .event.red::before {
                background: var(--alert);
            }

            .footer {
                margin-top: 20px;
                border-top: 1px solid var(--line);
                padding-top: 16px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 12px;
                color: var(--muted-light);
                font-size: 0.8rem;
                font-weight: 500;
            }

            /* ====== ANIMATIONS ====== */
            @keyframes slideInLeft {
                from {
                    transform: translateX(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes rise-in {
                from {
                    transform: translateY(20px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes float-up {
                from {
                    transform: translateY(12px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes slideDown {
                from {
                    transform: translateY(-12px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes growWidth {
                from {
                    width: 0;
                    opacity: 0.5;
                }
                to {
                    width: 78%;
                    opacity: 1;
                }
            }

            /* ====== RESPONSIVE ====== */
            @media (max-width: 1200px) {
                .content {
                    padding: 24px 20px;
                }

                .panel {
                    padding: 20px;
                }

                .hero h2 {
                    font-size: clamp(1.3rem, 2.5vw, 2rem);
                }
            }

            @media (max-width: 1024px) {
                .hero {
                    grid-template-columns: 1fr;
                }

                .lower-grid {
                    grid-template-columns: 1fr;
                }

                .kpi-grid {
                    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                }

                .topbar {
                    padding: 14px 20px;
                }
            }

            @media (max-width: 768px) {
                body {
                    grid-template-columns: 1fr;
                }

                .sidebar {
                    position: fixed;
                    left: 0;
                    top: 0;
                    bottom: 0;
                    width: 280px;
                    z-index: 999;
                    transform: translateX(-100%);
                    transition: transform var(--transition-normal);
                }

                .sidebar.open {
                    transform: translateX(0);
                }

                .sidebar-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 998;
                }

                .sidebar.open ~ .sidebar-overlay {
                    display: block;
                }

                .menu-toggle {
                    display: flex;
                }

                .search-box {
                    display: none;
                }

                .topbar {
                    padding: 12px 16px;
                }

                .content {
                    padding: 20px 16px;
                }

                .panel {
                    padding: 16px;
                }

                .hero h2 {
                    font-size: clamp(1.2rem, 4vw, 1.8rem);
                }
            }

            @media (max-width: 640px) {
                .hero {
                    gap: 16px;
                }

                .kpi-grid {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .quick-actions {
                    flex-direction: column;
                }

                .quick-actions .btn {
                    width: 100%;
                }

                .content {
                    padding: 16px 14px;
                }

                .hero p {
                    font-size: 0.95rem;
                }

                .section-title {
                    font-size: 0.95rem;
                }

                .activity-list li {
                    padding: 12px;
                    gap: 10px;
                    grid-template-columns: auto 1fr;
                }

                .activity-list h4 {
                    font-size: 0.85rem;
                }

                .activity-list p {
                    font-size: 0.75rem;
                }

                .pill {
                    font-size: 0.7rem;
                    padding: 4px 8px;
                }

                .topbar-left {
                    gap: 10px;
                }

                .breadcrumb {
                    display: none;
                }
            }

            @media (max-width: 480px) {
                .topbar {
                    flex-direction: column;
                    gap: 12px;
                    padding: 12px;
                }

                .topbar-left {
                    width: 100%;
                }

                .topbar-right {
                    width: 100%;
                    justify-content: flex-end;
                }

                .hero h2 {
                    font-size: 1.1rem;
                }

                .content {
                    padding: 12px 10px;
                }

                .kpi strong {
                    font-size: 1.4rem;
                }

                .footer {
                    flex-direction: column;
                    gap: 8px;
                    font-size: 0.75rem;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                * {
                    animation-duration: 1ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 1ms !important;
                }
            }

            /* ====== NOTIFICATIONS ====== */
            .notifications-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 12px;
                max-width: 420px;
                pointer-events: none;
            }

            .notification {
                background: var(--card);
                border: 1px solid var(--line);
                border-radius: var(--radius-md);
                padding: 16px;
                box-shadow: var(--shadow-lg);
                display: flex;
                align-items: flex-start;
                gap: 12px;
                animation: slideInRight 300ms var(--transition-normal) forwards;
                pointer-events: auto;
                cursor: pointer;
                transition: all var(--transition-normal);
                position: relative;
                overflow: hidden;
            }

            .notification::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background: var(--brand);
            }

            .notification.success {
                border-color: var(--success-soft);
                background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
            }

            .notification.success::before {
                background: var(--success);
            }

            .notification.info {
                border-color: var(--brand-soft);
                background: linear-gradient(135deg, #ffffff 0%, #f0fdfc 100%);
            }

            .notification.info::before {
                background: var(--brand);
            }

            .notification.warning {
                border-color: var(--accent-soft);
                background: linear-gradient(135deg, #ffffff 0%, #fefce8 100%);
            }

            .notification.warning::before {
                background: var(--accent);
            }

            .notification.error {
                border-color: var(--alert-soft);
                background: linear-gradient(135deg, #ffffff 0%, #ffe4e6 100%);
            }

            .notification.error::before {
                background: var(--alert);
            }

            .notification-icon {
                font-size: 20px;
                flex-shrink: 0;
                margin-top: 2px;
            }

            .notification-content {
                flex: 1;
                overflow: hidden;
            }

            .notification-title {
                font-weight: 700;
                font-size: 0.95rem;
                color: var(--ink);
                margin: 0 0 4px;
                line-height: 1.2;
            }

            .notification-message {
                font-size: 0.85rem;
                color: var(--muted);
                margin: 0;
                line-height: 1.4;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .notification-close {
                background: none;
                border: none;
                cursor: pointer;
                color: var(--muted-light);
                font-size: 18px;
                padding: 0;
                flex-shrink: 0;
                transition: color var(--transition-fast);
                line-height: 1;
                margin-top: 2px;
            }

            .notification-close:hover {
                color: var(--ink);
            }

            .notification:hover {
                box-shadow: var(--shadow);
                border-color: var(--line-light);
                transform: translateY(-2px);
            }

            .notification.fade-out {
                animation: slideOutRight 300ms var(--transition-normal) forwards;
            }

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                    pointer-events: none;
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideOutRight {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(100%);
                    pointer-events: none;
                }
            }

            @media (max-width: 768px) {
                .notifications-container {
                    max-width: 100%;
                    width: calc(100% - 20px);
                    top: 10px;
                    right: 10px;
                    left: 10px;
                }

                .notification {
                    padding: 14px;
                    gap: 10px;
                }

                .notification-icon {
                    font-size: 18px;
                }

                .notification-title {
                    font-size: 0.9rem;
                }

                .notification-message {
                    font-size: 0.8rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- SIDEBAR -->
        <nav class="sidebar" id="sidebar">
            <a href="{{ url('/') }}" class="sidebar-header">
                <span class="sidebar-mark">ESP</span>
                <h2 class="sidebar-title">Employee Success</h2>
            </a>

            <div class="sidebar-nav">
                <a href="#" class="nav-item active">
                    <span class="nav-icon">📊</span>
                    <span class="nav-label">Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-label">Employees</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📝</span>
                    <span class="nav-label">Attendance</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">✓</span>
                    <span class="nav-label">Tasks</span>
                    <span class="nav-badge">12</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📅</span>
                    <span class="nav-label">Leave Requests</span>
                    <span class="nav-badge">3</span>
                </a>

                <div class="sidebar-divider"></div>

                <a href="#" class="nav-item">
                    <span class="nav-icon">📈</span>
                    <span class="nav-label">Reports</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-label">Settings</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">❓</span>
                    <span class="nav-label">Help</span>
                </a>
            </div>

            <div class="sidebar-footer">
                @auth
                    <a href="{{ url('/home') }}" class="user-info">
                        <span class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        <div class="user-details">
                            <p class="user-name">{{ Auth::user()->name }}</p>
                            <p class="user-role">Supervisor</p>
                        </div>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        Sign In
                    </a>
                @endauth
            </div>
        </nav>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- NOTIFICATIONS CONTAINER -->
        <div id="notificationsContainer" class="notifications-container"></div>

        <!-- MAIN CONTENT -->
        <div class="main-container">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle sidebar">☰</button>
                    <nav class="breadcrumb">
                        <span>Welcome back,</span>
                        @auth
                            <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong>
                        @endauth
                    </nav>
                </div>

                <div class="topbar-right">
                    <div class="search-box">
                        <span style="font-size: 16px;">🔍</span>
                        <input type="text" placeholder="Search employees, tasks...">
                    </div>
                    <button class="icon-btn" title="Notifications">🔔</button>
                    <button class="icon-btn" title="More options">⋯</button>
                </div>
            </header>

            <main class="content">
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
            </main>
        </div>

        <script>
            // ====== SIDEBAR TOGGLE ======
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            const overlay = document.getElementById('sidebarOverlay');

            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });

            // Close sidebar when clicking nav items on mobile
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('open');
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                }
            });

            // ====== NOTIFICATION SYSTEM ======
            class NotificationManager {
                constructor() {
                    this.container = document.getElementById('notificationsContainer');
                    this.notifications = [];
                    this.initializeEcho();
                }

                initializeEcho() {
                    // Check if Laravel Echo is available
                    if (typeof window.Echo === 'undefined') {
                        console.warn('Laravel Echo not initialized. Install it with: npm install laravel-echo');
                        return;
                    }

                    // Alternative: Use database polling for development
                    // Listen for task assignments
                    this.listenForTaskAssignments();
                    // Listen for task submissions
                    this.listenForTaskSubmissions();
                }

                listenForTaskAssignments() {
                    // Listen on private channel for current logged-in user
                    @auth
                        const userId = {{ Auth::user()->id }};
                        if (window.Echo) {
                            try {
                                window.Echo.private(`user.${userId}`)
                                    .listen('TaskAssigned', (data) => {
                                        this.showNotification(
                                            'New Task Assigned! ✓',
                                            data.message || `Task: ${data.title}`,
                                            'success',
                                            '📋'
                                        );
                                    });
                            } catch (e) {
                                console.log('Echo listener setup:', e);
                            }
                        }
                    @endauth
                }

                listenForTaskSubmissions() {
                    // Listen on private channel for supervisor/manager
                    @auth
                        const userId = {{ Auth::user()->id }};
                        if (window.Echo) {
                            try {
                                window.Echo.private(`user.${userId}`)
                                    .listen('TaskSubmitted', (data) => {
                                        this.showNotification(
                                            'Work Submission Received! 📝',
                                            data.message || `${data.submitted_by} submitted work`,
                                            'info',
                                            '📊'
                                        );
                                    });
                            } catch (e) {
                                console.log('Echo listener setup:', e);
                            }
                        }
                    @endauth
                }

                showNotification(title, message, type = 'info', icon = 'ℹ️') {
                    const id = Date.now();
                    const notification = document.createElement('div');
                    notification.className = `notification ${type}`;
                    notification.id = `notification-${id}`;
                    notification.innerHTML = `
                        <div class="notification-icon">${icon}</div>
                        <div class="notification-content">
                            <p class="notification-title">${title}</p>
                            <p class="notification-message">${message}</p>
                        </div>
                        <button class="notification-close" aria-label="Close notification">&times;</button>
                    `;

                    this.container.appendChild(notification);
                    this.notifications.push(id);

                    // Close button listener
                    notification.querySelector('.notification-close').addEventListener('click', () => {
                        this.removeNotification(id);
                    });

                    // Auto-close after 8 seconds
                    let timeout = setTimeout(() => {
                        this.removeNotification(id);
                    }, 8000);

                    notification.addEventListener('mouseenter', () => {
                        clearTimeout(timeout);
                    });

                    notification.addEventListener('mouseleave', () => {
                        timeout = setTimeout(() => {
                            this.removeNotification(id);
                        }, 3000);
                    });
                }

                removeNotification(id) {
                    const element = document.getElementById(`notification-${id}`);
                    if (element) {
                        element.classList.add('fade-out');
                        setTimeout(() => {
                            if (element.parentNode) {
                                element.remove();
                            }
                            this.notifications = this.notifications.filter(n => n !== id);
                        }, 300);
                    }
                }
            }

            // Initialize notification manager when DOM is ready
            document.addEventListener('DOMContentLoaded', () => {
                window.notificationManager = new NotificationManager();

                // Example: Show a welcome notification
                @auth
                    setTimeout(() => {
                        window.notificationManager.showNotification(
                            'Welcome back! 👋',
                            'Real-time notifications will appear here when tasks are assigned or submitted.',
                            'success',
                            '🎉'
                        );
                    }, 500);
                @endauth
            });
        </script>
    </body>
</html>
