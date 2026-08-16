<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* ============================================================
           WHMPanel — Premium Hosting Control Panel
           Hostinger/Namecheap-grade design system
           ============================================================ */
        :root {
            /* Brand */
            --p-brand:       #6366F1;
            --p-brand-dark:  #4F46E5;
            --p-brand-light: #818CF8;
            --p-brand-soft:  rgba(99, 102, 241, 0.12);

            /* Ink & Text */
            --p-ink:    #F5F5F7;
            --p-text:   #F5F5F7;
            --p-muted:  #8E8E93;
            --p-subtle: #48484F;

            /* Surfaces */
            --p-canvas:   #0A0A0B;
            --p-surface:  #141416;
            --p-surface-2:#1C1C1F;
            --p-surface-3:#242429;

            /* Borders */
            --p-line:      rgba(255, 255, 255, 0.06);
            --p-line-str:  rgba(255, 255, 255, 0.10);

            /* Sidebar */
            --p-rail:  #0A0A0B;
            --p-rail-2:#141416;

            /* Semantic */
            --p-success:        #22D3EE;
            --p-success-soft:   rgba(34, 211, 238, 0.12);
            --p-success-border: rgba(34, 211, 238, 0.20);
            --p-warning:        #FBBF24;
            --p-warning-soft:   rgba(251, 191, 36, 0.12);
            --p-warning-border: rgba(251, 191, 36, 0.20);
            --p-danger:         #FB7185;
            --p-danger-soft:    rgba(251, 113, 133, 0.12);
            --p-danger-border:  rgba(251, 113, 133, 0.20);
            --p-info:           #6366F1;
            --p-info-soft:      rgba(99, 102, 241, 0.12);

            /* Shadows */
            --p-shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.30);
            --p-shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.40);
            --p-shadow-md: 0 12px 40px rgba(0, 0, 0, 0.50);
            --p-shadow-lg: 0 24px 64px rgba(0, 0, 0, 0.60);
            --p-shadow-brand: 0 0 20px rgba(99, 102, 241, 0.20);

            /* Geometry */
            --p-radius-sm: 6px;
            --p-radius-md: 10px;
            --p-radius-lg: 16px;
            --p-radius-pill: 9999px;
            --p-dur: 200ms;
            --p-ease: cubic-bezier(0.4, 0, 0.2, 1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            background: var(--p-canvas);
            color-scheme: dark;
            -webkit-font-smoothing: antialiased;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
            line-height: 1.6;
            letter-spacing: 0;
            color: var(--p-text);
            background-color: var(--p-canvas);
            background-image:
                radial-gradient(ellipse 60% 40% at 75% -5%, rgba(99, 102, 241, 0.12), transparent),
                radial-gradient(ellipse 40% 30% at 0% 50%, rgba(34, 211, 238, 0.06), transparent);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select { font: inherit; }
        button, .btn, a { -webkit-tap-highlight-color: transparent; }

        /* ── Shell ── */
        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 272px minmax(0, 1fr);
        }

        /* ── Sidebar ── */
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            background:
                linear-gradient(180deg, rgba(103,61,230,.16) 0%, transparent 240px),
                linear-gradient(160deg, var(--p-rail), var(--p-rail-2));
            border-right: 1px solid rgba(255,255,255,.07);
            box-shadow: 16px 0 50px rgba(11,17,32,.30);
            display: flex;
            flex-direction: column;
            padding: 0;

            /* Custom scrollbar */
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.12) transparent;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.14); border-radius: 99px; }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 68px;
            padding: 14px 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex: 0 0 auto;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: var(--p-radius-sm);
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--p-brand), var(--p-brand-dark));
            color: #fff;
            flex: 0 0 auto;
            box-shadow: var(--p-shadow-brand);
        }

        .brand strong {
            display: block;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.2;
            color: #fff;
            letter-spacing: -0.01em;
        }

        .brand span {
            display: block;
            margin-top: 2px;
            color: rgba(255,255,255,.48);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.04em;
        }

        /* Nav groups */
        .nav {
            padding: 14px 12px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex: 1;
        }

        .nav-group p {
            margin: 0 0 6px;
            padding: 0 10px;
            color: rgba(255,255,255,.36);
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .nav a {
            min-height: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--p-radius-sm);
            color: rgba(255,255,255,.68);
            font-size: 13.5px;
            font-weight: 500;
            transition:
                background var(--p-dur) var(--p-ease),
                color var(--p-dur) var(--p-ease),
                transform var(--p-dur) var(--p-ease),
                box-shadow var(--p-dur) var(--p-ease);
        }

        .nav a:hover {
            background: rgba(255,255,255,.07);
            color: #fff;
            transform: translateX(2px);
        }

        .nav a.active {
            background: linear-gradient(90deg, rgba(103,61,230,.30), rgba(103,61,230,.10));
            color: #fff;
            box-shadow: inset 3px 0 0 var(--p-brand);
            font-weight: 600;
        }

        .nav svg, .brand svg {
            width: 17px;
            height: 17px;
            flex: 0 0 auto;
        }

        /* Sidebar footer */
        .sidebar-footer {
            flex: 0 0 auto;
            padding: 14px 18px;
            border-top: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 40px;
            padding: 9px 12px;
            border-radius: var(--p-radius-sm);
            color: rgba(255,255,255,.52);
            font-size: 13px;
            font-weight: 500;
            transition: background var(--p-dur) var(--p-ease), color var(--p-dur) var(--p-ease);
        }

        .sidebar-footer a:hover {
            background: rgba(255,255,255,.07);
            color: #fff;
        }

        /* ── Main Content ── */
        .main { min-width: 0; display: flex; flex-direction: column; }

        /* ── Topbar ── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin: 18px 24px 0;
            padding: 16px 20px;
            border: 1px solid rgba(255,255,255,.82);
            border-radius: var(--p-radius-md);
            background: rgba(255,255,255,.92);
            box-shadow: var(--p-shadow-sm);
            backdrop-filter: blur(20px);
            flex: 0 0 auto;
        }

        .topbar h1 {
            font-size: clamp(17px, 2.2vw, 22px);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.015em;
            color: var(--p-ink);
            margin: 0 0 3px;
        }

        .topbar small {
            display: block;
            font-size: 12.5px;
            color: var(--p-muted);
            font-weight: 500;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Mobile nav tabs */
        .mobile-tabs {
            display: none;
            gap: 8px;
            padding: 14px 18px 0;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .mobile-tabs::-webkit-scrollbar { display: none; }

        .mobile-tabs a {
            flex: 0 0 auto;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 13px;
            border: 1px solid var(--p-line);
            border-radius: var(--p-radius-pill);
            background: #fff;
            color: var(--p-text);
            font-size: 13px;
            font-weight: 600;
            transition: border-color var(--p-dur) var(--p-ease), background var(--p-dur) var(--p-ease), color var(--p-dur) var(--p-ease);
        }

        .mobile-tabs a:hover {
            border-color: rgba(103,61,230,.24);
            color: var(--p-brand);
        }

        .mobile-tabs a.active {
            border-color: rgba(103,61,230,.32);
            background: var(--p-brand-soft);
            color: var(--p-brand);
            font-weight: 700;
        }

        .mobile-tabs svg { width: 15px; height: 15px; }

        /* ── Content Area ── */
        .content {
            max-width: 1480px;
            margin: 0 auto;
            padding: 28px 28px 48px;
            width: 100%;
        }

        /* ── Grid utilities ── */
        .grid { display: grid; gap: 16px; }

        /* ── Cards ── */
        .card {
            background: rgba(255,255,255,.96);
            border: 1px solid var(--p-line);
            border-radius: var(--p-radius-md);
            padding: 22px;
            box-shadow: var(--p-shadow-xs);
            transition: border-color var(--p-dur) var(--p-ease), box-shadow var(--p-dur) var(--p-ease);
        }

        .card:hover { border-color: rgba(103,61,230,.18); box-shadow: var(--p-shadow-sm); }

        .card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .card h2 {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: var(--p-ink);
            margin-bottom: 4px;
        }

        .card h3 {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.005em;
            color: var(--p-ink);
            margin-bottom: 4px;
        }

        .card p { margin: 0; color: var(--p-muted); font-size: 13.5px; }

        /* ── Buttons ── */
        .btn {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid var(--p-brand-dark);
            border-radius: var(--p-radius-sm);
            background: linear-gradient(135deg, var(--p-brand), var(--p-brand-dark));
            color: #fff;
            padding: 9px 16px;
            font-size: 13.5px;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            box-shadow: var(--p-shadow-brand);
            transition:
                transform var(--p-dur) var(--p-ease),
                background var(--p-dur) var(--p-ease),
                box-shadow var(--p-dur) var(--p-ease);
        }

        .btn:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, var(--p-brand-light), var(--p-brand));
            box-shadow: 0 16px 38px rgba(103,61,230,.38);
        }

        .btn.secondary {
            background: var(--p-surface);
            border-color: var(--p-line);
            color: var(--p-text);
            box-shadow: var(--p-shadow-xs);
        }

        .btn.secondary:hover {
            background: var(--p-surface-2);
            border-color: rgba(103,61,230,.22);
            color: var(--p-brand);
            box-shadow: var(--p-shadow-sm);
        }

        .btn.danger {
            background: linear-gradient(135deg, var(--p-danger), #b91c1c);
            border-color: var(--p-danger);
            box-shadow: 0 10px 24px rgba(220,38,38,.24);
        }

        .btn.success {
            background: linear-gradient(135deg, var(--p-success), #047857);
            border-color: var(--p-success);
            box-shadow: 0 10px 24px rgba(5,150,105,.24);
        }

        .btn svg { width: 16px; height: 16px; }

        /* ── Hero Banner ── */
        .hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(260px, 360px);
            align-items: stretch;
            gap: 24px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,.16);
            border-radius: var(--p-radius-md);
            color: #fff;
            background:
                linear-gradient(135deg, rgba(10,14,30,.96), rgba(20,26,45,.95)),
                linear-gradient(90deg, rgba(103,61,230,.92), rgba(14,165,160,.72));
            box-shadow: var(--p-shadow-lg);
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(103,61,230,.30), transparent 36%),
                repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 1px, transparent 1px 20px);
            pointer-events: none;
        }

        .hero > * { position: relative; z-index: 1; }

        .eyebrow {
            margin: 0 0 10px;
            color: #c4b5fd;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .hero h2 {
            max-width: 980px;
            margin: 0 0 10px;
            font-size: clamp(22px, 3.6vw, 44px);
            line-height: 1.06;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .hero p {
            margin: 0;
            color: rgba(255,255,255,.68);
            font-size: 15px;
            font-weight: 500;
            line-height: 1.66;
        }

        .hero-status {
            min-width: 0;
            align-self: stretch;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: var(--p-radius-sm);
            background: rgba(255,255,255,.07);
            padding: 16px 18px;
        }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: var(--p-radius-pill);
            padding: 4px 10px;
            font-size: 11.5px;
            line-height: 1.3;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .badge.live, .badge.online, .badge.active, .badge.completed, .badge.running {
            background: var(--p-success-soft);
            color: var(--p-success);
            border-color: var(--p-success-border);
        }

        .badge.planned, .badge.recorded {
            background: var(--p-info-soft);
            color: var(--p-info);
            border-color: rgba(37,99,235,.18);
        }

        .badge.blocked, .badge.terminated {
            background: var(--p-danger-soft);
            color: var(--p-danger);
            border-color: var(--p-danger-border);
        }

        .badge.suspended {
            background: var(--p-warning-soft);
            color: var(--p-warning);
            border-color: var(--p-warning-border);
        }

        .hero .badge {
            background: rgba(255,255,255,.12);
            color: #fff;
            border-color: rgba(255,255,255,.16);
        }

        /* ── Tables ── */
        .table-wrap { overflow-x: auto; border-radius: 0 0 var(--p-radius-md) var(--p-radius-md); }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 0;
        }

        .table th, .table td {
            padding: 13px 16px;
            border-top: 1px solid var(--p-line);
            text-align: left;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .table th {
            background: var(--p-surface-2);
            color: var(--p-muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: background var(--p-dur) var(--p-ease);
        }

        .table tbody tr:hover td {
            background: rgba(103,61,230,.025);
        }

        /* ── Form Fields ── */
        .field label {
            display: block;
            margin: 0 0 7px;
            color: var(--p-ink);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .field input, .field select, .field textarea {
            width: 100%;
            height: 44px;
            border: 1px solid var(--p-line);
            border-radius: var(--p-radius-sm);
            background: #fff;
            color: var(--p-ink);
            padding: 0 14px;
            font-size: 14px;
            font-weight: 500;
            outline: none;
            transition: border-color var(--p-dur) var(--p-ease), box-shadow var(--p-dur) var(--p-ease);
        }

        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: var(--p-brand);
            box-shadow: 0 0 0 4px rgba(103,61,230,.12);
        }

        .field textarea { height: auto; padding: 12px 14px; resize: vertical; }

        /* ── Stat Cards ── */
        .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .metric-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .metric-label {
            color: var(--p-muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .metric-icon {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: var(--p-radius-sm);
            background: var(--p-brand-soft);
            color: var(--p-brand);
            flex: 0 0 auto;
        }

        .metric-value {
            margin-top: 16px;
            font-size: 32px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: var(--p-ink);
        }

        .metric-note {
            margin-top: 7px;
            color: var(--p-muted);
            font-size: 13px;
            font-weight: 500;
        }

        /* ── Progress Bar ── */
        .bar {
            height: 8px;
            background: var(--p-surface-3);
            border-radius: var(--p-radius-pill);
            overflow: hidden;
            margin-top: 10px;
        }

        .bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, var(--p-brand), var(--p-brand-light));
            border-radius: var(--p-radius-pill);
        }

        /* ── Section layouts ── */
        .section  { grid-template-columns: minmax(0, 1.4fr) minmax(340px, .85fr); align-items: start; }
        .split    { grid-template-columns: minmax(0, 1.1fr) minmax(340px, .9fr); align-items: start; }
        .node-meter { display: grid; gap: 18px; margin-top: 18px; }
        .control-grid { grid-template-columns: minmax(160px,1fr) minmax(160px,1fr) minmax(190px,1.2fr) auto; align-items: end; }

        /* ── Service Grid ── */
        .service-grid { grid-template-columns: repeat(4, minmax(0,1fr)); }

        .service-card {
            position: relative;
            min-height: 240px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            overflow: hidden;
            transition:
                transform var(--p-dur) var(--p-ease),
                border-color var(--p-dur) var(--p-ease),
                box-shadow var(--p-dur) var(--p-ease);
        }

        .service-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
            background: linear-gradient(90deg, var(--p-brand), var(--p-brand-light), #67e8f9);
            border-radius: var(--p-radius-md) var(--p-radius-md) 0 0;
        }

        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--p-shadow-md);
            border-color: rgba(103,61,230,.20);
        }

        .service-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .service-icon {
            width: 46px;
            height: 46px;
            border-radius: var(--p-radius-sm);
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--p-brand-soft), #e8fbf8);
            color: var(--p-brand);
            box-shadow: inset 0 0 0 1px rgba(103,61,230,.10);
        }

        .service-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: auto 0 0;
            padding: 0;
            list-style: none;
        }

        .service-list li {
            border: 1px solid var(--p-line);
            border-radius: var(--p-radius-pill);
            color: var(--p-muted);
            background: var(--p-surface-2);
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 700;
        }

        /* ── Module Section ── */
        .module-section { margin-top: 24px; }

        .module-section__head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            margin: 0 0 14px;
        }

        .module-section__head h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
            color: var(--p-ink);
            letter-spacing: -0.01em;
        }

        .module-section__head p {
            margin: 4px 0 0;
            color: var(--p-muted);
            font-size: 13px;
        }

        /* ── Status rows ── */
        .status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 0;
            border-top: 1px solid var(--p-line);
        }

        .status-row:first-child { border-top: 0; }

        /* ── Module map ── */
        .module-map { display: grid; gap: 8px; }

        .module-map a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid var(--p-line);
            border-radius: var(--p-radius-sm);
            background: #fff;
            padding: 12px 14px;
            color: var(--p-text);
            font-size: 13.5px;
            font-weight: 600;
            transition: border-color var(--p-dur) var(--p-ease), background var(--p-dur) var(--p-ease), color var(--p-dur) var(--p-ease);
        }

        .module-map a:hover {
            border-color: rgba(103,61,230,.24);
            background: var(--p-brand-soft);
            color: var(--p-brand);
        }

        .module-map span { display: inline-flex; align-items: center; gap: 8px; }
        .module-map svg { width: 16px; height: 16px; color: var(--p-brand); }

        /* ── Notice ── */
        .notice {
            border: 1px solid rgba(217,119,6,.22);
            background: var(--p-warning-soft);
            color: #7C3F00;
            border-radius: var(--p-radius-sm);
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 600;
        }

        /* ── Copy list ── */
        .copy-list { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }

        .copy-list li {
            display: grid;
            grid-template-columns: 80px 90px minmax(0, 1fr);
            gap: 12px;
            border: 1px solid var(--p-line);
            border-radius: var(--p-radius-sm);
            padding: 11px 14px;
            background: var(--p-surface-2);
            font-size: 13px;
            align-items: center;
        }

        .copy-list code {
            overflow-wrap: anywhere;
            color: var(--p-ink);
            font-family: "JetBrains Mono", ui-monospace, monospace;
            font-size: 12.5px;
            background: rgba(103,61,230,.06);
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* ── Responsive ── */
        @media (max-width: 1180px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .mobile-tabs { display: flex; }
            .topbar { margin: 0; border-radius: 0; padding: 14px 18px; }
            .content { padding: 18px; }
            .hero { grid-template-columns: 1fr; }
            .stats { grid-template-columns: repeat(2, minmax(0,1fr)); }
            .service-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
            .split, .section { grid-template-columns: 1fr; }
            .control-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
        }

        @media (max-width: 680px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .top-actions { width: 100%; }
            .top-actions .btn { flex: 1 1 150px; }
            .content { padding: 14px; }
            .hero { padding: 20px; }
            .hero h2 { font-size: 26px; }
            .stats, .control-grid { grid-template-columns: 1fr; }
            .service-grid { grid-template-columns: 1fr; }
            .card-head, .module-section__head { align-items: flex-start; flex-direction: column; }
            .copy-list li { grid-template-columns: 1fr; }
            .table, .table thead, .table tbody, .table tr, .table td { display: block; }
            .table thead { display: none; }
            .table tr { border-top: 1px solid var(--p-line); padding: 10px 0; }
            .table td { border: 0; display: flex; justify-content: space-between; gap: 16px; background: transparent !important; }
            .table td::before { content: attr(data-label); color: var(--p-muted); font-size: 11px; font-weight: 800; text-transform: uppercase; }
        }
    </style>
</head>
<body>
    <div class="shell">
        {{-- ─── Sidebar ─── --}}
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark"><i data-lucide="server"></i></div>
                <div>
                    <strong>ZodPanel</strong>
                    <span>Node console</span>
                </div>
            </div>

            <nav class="nav" aria-label="WHMPanel navigation">
                <div class="nav-group">
                    <p>Overview</p>
                    <a @class(['active' => request()->routeIs('whmpanel.dashboard')]) href="{{ route('whmpanel.dashboard') }}">
                        <i data-lucide="layout-dashboard"></i>Dashboard
                    </a>
                    <a @class(['active' => request()->routeIs('whmpanel.services*')]) href="{{ route('whmpanel.services') }}">
                        <i data-lucide="blocks"></i>Services
                    </a>
                    <a @class(['active' => request()->routeIs('whmpanel.websites*')]) href="{{ route('whmpanel.websites') }}">
                        <i data-lucide="globe"></i>Websites
                    </a>
                </div>

                <div class="nav-group">
                    <p>Core Centers</p>
                    <a @class(['active' => request()->is('whmpanel/services/file_manager')]) href="{{ route('whmpanel.services.show', 'file_manager') }}">
                        <i data-lucide="folder-open"></i>File Manager
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/webmail')]) href="{{ route('whmpanel.services.show', 'webmail') }}">
                        <i data-lucide="mail"></i>Webmail
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/dns')]) href="{{ route('whmpanel.services.show', 'dns') }}">
                        <i data-lucide="network"></i>DNS
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/ssl')]) href="{{ route('whmpanel.services.show', 'ssl') }}">
                        <i data-lucide="shield-check"></i>SSL
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/databases')]) href="{{ route('whmpanel.services.show', 'databases') }}">
                        <i data-lucide="database"></i>Databases
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/backups')]) href="{{ route('whmpanel.services.show', 'backups') }}">
                        <i data-lucide="archive-restore"></i>Backups
                    </a>
                </div>

                <div class="nav-group">
                    <p>Runtime &amp; Apps</p>
                    <a @class(['active' => request()->is('whmpanel/services/php_selector')]) href="{{ route('whmpanel.services.show', 'php_selector') }}">
                        <i data-lucide="file-code-2"></i>PHP Selector
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/terminal')]) href="{{ route('whmpanel.services.show', 'terminal') }}">
                        <i data-lucide="square-terminal"></i>Terminal
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/nodejs')]) href="{{ route('whmpanel.services.show', 'nodejs') }}">
                        <i data-lucide="hexagon"></i>Node.js Apps
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/python')]) href="{{ route('whmpanel.services.show', 'python') }}">
                        <i data-lucide="blocks"></i>Python Apps
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/apps')]) href="{{ route('whmpanel.services.show', 'apps') }}">
                        <i data-lucide="rocket"></i>App Installer
                    </a>
                </div>

                <div class="nav-group">
                    <p>Operations</p>
                    <a @class(['active' => request()->is('whmpanel/services/logs')]) href="{{ route('whmpanel.services.show', 'logs') }}">
                        <i data-lucide="scroll-text"></i>Logs
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/security')]) href="{{ route('whmpanel.services.show', 'security') }}">
                        <i data-lucide="lock-keyhole"></i>Security
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/fleet')]) href="{{ route('whmpanel.services.show', 'fleet') }}">
                        <i data-lucide="server-cog"></i>Fleet
                    </a>
                    <a @class(['active' => request()->is('whmpanel/services/control_strip')]) href="{{ route('whmpanel.services.show', 'control_strip') }}">
                        <i data-lucide="panel-top"></i>Client Controls
                    </a>
                </div>

                <div class="nav-group">
                    <p>API</p>
                    <a href="{{ route('whmpanel.api.services') }}">
                        <i data-lucide="braces"></i>Services JSON
                    </a>
                    <a href="{{ route('whmpanel.api.server.info') }}">
                        <i data-lucide="activity"></i>Health JSON
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ url('/') }}">
                    <i data-lucide="credit-card"></i>Billing Portal
                </a>
            </div>
        </aside>

        {{-- ─── Main ─── --}}
        <main class="main">
            <header class="topbar">
                <div>
                    <h1>{{ $pageTitle }}</h1>
                    <small>Focused hosting operations for domains, services &amp; node health</small>
                </div>
                <div class="top-actions">
                    <a class="btn secondary" href="{{ route('whmpanel.dashboard') }}">
                        <i data-lucide="arrow-left"></i>Dashboard
                    </a>
                    <a class="btn" href="{{ route('whmpanel.services') }}">
                        <i data-lucide="blocks"></i>Services
                    </a>
                </div>
            </header>

            <nav class="mobile-tabs" aria-label="WHMPanel sections">
                <a @class(['active' => request()->routeIs('whmpanel.dashboard')]) href="{{ route('whmpanel.dashboard') }}">
                    <i data-lucide="layout-dashboard"></i>Dashboard
                </a>
                <a @class(['active' => request()->routeIs('whmpanel.services*')]) href="{{ route('whmpanel.services') }}">
                    <i data-lucide="blocks"></i>Services
                </a>
                <a @class(['active' => request()->routeIs('whmpanel.websites*')]) href="{{ route('whmpanel.websites') }}">
                    <i data-lucide="globe"></i>Websites
                </a>
                <a href="{{ route('whmpanel.api.server.info') }}">
                    <i data-lucide="activity"></i>Health
                </a>
            </nav>

            <div class="content">
                @if(session('status'))
                    <div class="notice">{{ session('status') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
