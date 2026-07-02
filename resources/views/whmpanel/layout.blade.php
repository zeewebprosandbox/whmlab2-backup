<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - {{ config('app.name') }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --ink: #111827;
            --text: #374151;
            --muted: #6b7280;
            --line: #d8dee8;
            --line-strong: #b9c3d2;
            --canvas: #f4f7fb;
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --rail: #151923;
            --rail-2: #202633;
            --blue: #276ef1;
            --teal: #0f9f8f;
            --magenta: #c026d3;
            --amber: #d97706;
            --green: #0f8f62;
            --red: #c2410c;
            --shadow: 0 24px 80px rgba(31, 41, 55, .12);
            --shadow-sm: 0 12px 34px rgba(31, 41, 55, .08);
            --radius: 8px;
        }
        * { box-sizing: border-box; }
        html { background: var(--canvas); }
        body {
            margin: 0;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.5;
            letter-spacing: 0;
            background:
                linear-gradient(90deg, rgba(39, 110, 241, .08) 1px, transparent 1px),
                linear-gradient(180deg, rgba(15, 159, 143, .07) 1px, transparent 1px),
                linear-gradient(135deg, #fbfdff 0%, #f4f7fb 44%, #eef3f9 100%);
            background-size: 72px 72px, 72px 72px, auto;
        }
        a { color: inherit; text-decoration: none; }
        button, input, select { font: inherit; }
        button, .btn, a { -webkit-tap-highlight-color: transparent; }
        .shell { min-height: 100vh; display: grid; grid-template-columns: 284px minmax(0, 1fr); }
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 16px;
            overflow-y: auto;
            color: #f8fafc;
            background:
                linear-gradient(180deg, rgba(39, 110, 241, .14), transparent 220px),
                linear-gradient(160deg, var(--rail), var(--rail-2));
            border-right: 1px solid rgba(255,255,255,.08);
            box-shadow: 20px 0 60px rgba(17, 24, 39, .22);
        }
        .brand { display: flex; align-items: center; gap: 12px; min-height: 62px; padding: 8px 8px 18px; border-bottom: 1px solid rgba(255,255,255,.1); }
        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: var(--radius);
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #276ef1, #0f9f8f 54%, #c026d3);
            color: #fff;
            box-shadow: 0 16px 36px rgba(39,110,241,.26);
        }
        .brand strong { display: block; font-size: 18px; line-height: 1.15; color: #fff; }
        .brand span { display: block; margin-top: 3px; color: rgba(255,255,255,.57); font-size: 12px; }
        .nav { padding: 18px 0; display: grid; gap: 18px; }
        .nav-group p {
            margin: 0 0 8px;
            padding: 0 10px;
            color: rgba(255,255,255,.42);
            font-size: 11px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .nav a {
            min-height: 42px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 11px;
            border-radius: var(--radius);
            color: rgba(255,255,255,.72);
            font-size: 14px;
            font-weight: 740;
            transition: background .18s ease, color .18s ease, transform .18s ease;
        }
        .nav a.active {
            background: linear-gradient(90deg, rgba(39,110,241,.32), rgba(15,159,143,.12));
            color: #fff;
            box-shadow: inset 3px 0 0 #67e8f9, 0 14px 28px rgba(0,0,0,.14);
        }
        .nav a:hover { background: rgba(255,255,255,.08); color: #fff; transform: translateX(2px); }
        .nav svg, .brand svg { width: 18px; height: 18px; flex: 0 0 auto; }
        .main { min-width: 0; }
        .topbar {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin: 18px 24px 0;
            padding: 14px 18px 14px 22px;
            border: 1px solid rgba(255,255,255,.78);
            border-radius: var(--radius);
            background: rgba(255,255,255,.82);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(18px);
        }
        .topbar h1 { margin: 0; font-size: 20px; line-height: 1.2; letter-spacing: 0; }
        .topbar small { color: var(--muted); }
        .top-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .mobile-tabs { display: none; gap: 8px; padding: 12px 18px 0; overflow-x: auto; }
        .mobile-tabs a { flex: 0 0 auto; min-height: 38px; display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; color: var(--text); font-size: 13px; font-weight: 780; }
        .mobile-tabs a.active { border-color: rgba(39,110,241,.4); background: #eef5ff; color: var(--blue); }
        .mobile-tabs svg { width: 16px; height: 16px; }
        .content { max-width: 1480px; margin: 0 auto; padding: 30px 32px 38px; }
        .grid { display: grid; gap: 18px; }
        .card {
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(216,222,232,.92);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }
        .card:hover { border-color: var(--line-strong); }
        .card-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 16px; }
        .card h2, .card h3 { margin: 0 0 4px; line-height: 1.2; }
        .card p { margin: 0; color: var(--muted); }
        .btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #1f5ed9;
            border-radius: var(--radius);
            background: linear-gradient(135deg, #276ef1, #1f5ed9);
            color: #fff;
            padding: 10px 15px;
            font-weight: 820;
            white-space: nowrap;
            cursor: pointer;
            box-shadow: 0 14px 28px rgba(39,110,241,.22);
            transition: transform .18s ease, background .18s ease, border-color .18s ease;
        }
        .btn:hover { transform: translateY(-1px); background: linear-gradient(135deg, #1f5ed9, #1747ac); }
        .btn.secondary { background: #fff; border-color: var(--line); color: var(--text); box-shadow: 0 10px 22px rgba(31,41,55,.06); }
        .btn.secondary:hover { background: var(--surface-2); border-color: var(--line-strong); color: var(--ink); }
        .btn svg { width: 17px; height: 17px; }
        .hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(260px, 360px);
            align-items: stretch;
            gap: 22px;
            padding: 28px;
            border: 1px solid rgba(255,255,255,.78);
            border-radius: var(--radius);
            color: #fff;
            background:
                linear-gradient(135deg, rgba(21,25,35,.96), rgba(32,38,51,.95)),
                linear-gradient(90deg, rgba(39,110,241,.92), rgba(15,159,143,.82));
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(39,110,241,.34), transparent 34%),
                repeating-linear-gradient(135deg, rgba(255,255,255,.08) 0 1px, transparent 1px 18px);
            pointer-events: none;
        }
        .hero > * { position: relative; z-index: 1; }
        .eyebrow { margin: 0 0 10px; color: #67e8f9; font-size: 12px; font-weight: 850; letter-spacing: .08em; text-transform: uppercase; }
        .hero h2 { max-width: 980px; margin: 0 0 10px; font-size: clamp(30px, 4vw, 56px); line-height: 1.02; letter-spacing: 0; }
        .hero p { margin: 0; color: rgba(255,255,255,.72); max-width: 820px; font-size: 16px; }
        .hero-status {
            min-width: 0;
            align-self: stretch;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: var(--radius);
            background: rgba(255,255,255,.08);
            padding: 14px 16px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            max-width: 100%;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            line-height: 1.25;
            font-weight: 800;
            border: 1px solid var(--line);
            background: var(--surface-2);
            color: var(--muted);
        }
        .badge.live, .badge.online, .badge.active, .badge.completed, .badge.running { background: #eafaf3; color: var(--green); border-color: #9ee4c6; }
        .badge.planned, .badge.recorded { background: #eef5ff; color: var(--blue); border-color: #b8d2ff; }
        .badge.blocked, .badge.terminated { background: #fff2ed; color: var(--red); border-color: #fed7c7; }
        .badge.suspended { background: #fff8e8; color: var(--amber); border-color: #f7d98a; }
        .hero .badge { background: rgba(255,255,255,.12); color: #fff; border-color: rgba(255,255,255,.16); }
        .table-wrap { overflow-x: auto; }
        .table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 12px; }
        .table th, .table td { padding: 14px 12px; border-top: 1px solid var(--line); text-align: left; font-size: 14px; vertical-align: top; }
        .table th { background: var(--surface-2); color: var(--muted); font-size: 12px; font-weight: 850; letter-spacing: .05em; text-transform: uppercase; }
        .table tbody tr:hover td { background: #fbfdff; }
        .field label { display: block; margin: 0 0 7px; color: var(--muted); font-size: 12px; font-weight: 850; letter-spacing: .05em; text-transform: uppercase; }
        .field input, .field select {
            width: 100%;
            height: 44px;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: #fff;
            color: var(--ink);
            padding: 0 12px;
            outline: none;
            box-shadow: inset 0 1px 0 rgba(17,24,39,.03);
        }
        .field input:focus, .field select:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(39,110,241,.13); }
        .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .metric-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .metric-label { color: var(--muted); font-size: 12px; font-weight: 850; letter-spacing: .05em; text-transform: uppercase; }
        .metric-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: var(--radius); background: #eef5ff; color: var(--blue); }
        .metric-value { margin-top: 12px; font-size: 34px; line-height: 1; font-weight: 880; letter-spacing: 0; }
        .metric-note { margin-top: 7px; color: var(--muted); font-size: 13px; }
        .section { grid-template-columns: minmax(0, 1.4fr) minmax(340px, .85fr); align-items: start; }
        .bar { height: 10px; background: #edf1f6; border-radius: 999px; overflow: hidden; margin-top: 10px; }
        .bar span { display: block; height: 100%; background: linear-gradient(90deg, var(--blue), var(--teal)); }
        .node-meter { display: grid; gap: 18px; margin-top: 18px; }
        .control-grid { grid-template-columns: minmax(160px, 1fr) minmax(160px, 1fr) minmax(190px, 1.2fr) auto; align-items: end; }
        .service-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .service-card { position: relative; min-height: 246px; display: flex; flex-direction: column; gap: 14px; overflow: hidden; transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease; }
        .service-card::before { content: ""; position: absolute; inset: 0 0 auto 0; height: 4px; background: linear-gradient(90deg, var(--blue), var(--teal), var(--magenta)); }
        .service-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
        .service-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
        .service-icon { width: 44px; height: 44px; border-radius: var(--radius); display: grid; place-items: center; background: linear-gradient(135deg, #eef5ff, #e8fbf8); color: var(--blue); box-shadow: inset 0 0 0 1px rgba(39,110,241,.08); }
        .service-list { display: flex; flex-wrap: wrap; gap: 7px; margin: auto 0 0; padding: 0; list-style: none; }
        .service-list li { border: 1px solid var(--line); border-radius: 999px; color: var(--muted); background: #fbfdff; padding: 4px 9px; font-size: 11px; font-weight: 780; }
        .module-section { margin-top: 24px; }
        .module-section__head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; margin: 0 0 12px; }
        .module-section__head h3 { margin: 0; font-size: 18px; color: var(--ink); }
        .module-section__head p { margin: 4px 0 0; color: var(--muted); font-size: 13px; }
        .split { grid-template-columns: minmax(0, 1.1fr) minmax(340px, .9fr); align-items: start; }
        .status-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 0; border-top: 1px solid var(--line); }
        .status-row:first-child { border-top: 0; }
        .module-map { display: grid; gap: 10px; }
        .module-map a { display: flex; align-items: center; justify-content: space-between; gap: 12px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; padding: 12px; color: var(--text); font-weight: 820; }
        .module-map a:hover { border-color: var(--line-strong); background: var(--surface-2); color: var(--ink); }
        .module-map span { display: inline-flex; align-items: center; gap: 8px; }
        .module-map svg { width: 17px; height: 17px; color: var(--blue); }
        .notice { border: 1px solid #f7d98a; background: #fff8e8; color: #8a5400; border-radius: var(--radius); padding: 14px 16px; margin-bottom: 18px; }
        .copy-list { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
        .copy-list li { display: grid; grid-template-columns: 80px 90px minmax(0, 1fr); gap: 10px; border: 1px solid var(--line); border-radius: var(--radius); padding: 11px 12px; background: var(--surface-2); font-size: 13px; }
        .copy-list code { overflow-wrap: anywhere; color: var(--ink); }
        @media (max-width: 1180px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .mobile-tabs { display: flex; }
            .topbar { margin: 0; border-radius: 0; padding: 16px 18px; }
            .content { padding: 18px; }
            .hero { grid-template-columns: 1fr; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .service-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .split, .section { grid-template-columns: 1fr; }
            .control-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 680px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .top-actions { width: 100%; }
            .top-actions .btn { flex: 1 1 150px; }
            .content { padding: 16px; }
            .hero { padding: 22px; }
            .hero h2 { font-size: 32px; }
            .stats, .control-grid { grid-template-columns: 1fr; }
            .service-grid { grid-template-columns: 1fr; }
            .card-head, .module-section__head { align-items: flex-start; flex-direction: column; }
            .copy-list li { grid-template-columns: 1fr; }
            .table, .table thead, .table tbody, .table tr, .table td { display: block; }
            .table thead { display: none; }
            .table tr { border-top: 1px solid var(--line); padding: 10px 0; }
            .table td { border: 0; display: flex; justify-content: space-between; gap: 16px; background: transparent !important; }
            .table td::before { content: attr(data-label); color: var(--muted); font-size: 12px; font-weight: 850; text-transform: uppercase; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark"><i data-lucide="server"></i></div>
                <div><strong>ZodPanel</strong><span>ZodHost node console</span></div>
            </div>
            <nav class="nav">
                <div class="nav-group">
                    <p>Overview</p>
                    <a @class(['active' => request()->routeIs('whmpanel.dashboard')]) href="{{ route('whmpanel.dashboard') }}"><i data-lucide="layout-dashboard"></i>Dashboard</a>
                    <a @class(['active' => request()->routeIs('whmpanel.services*')]) href="{{ route('whmpanel.services') }}"><i data-lucide="blocks"></i>Services</a>
                    <a @class(['active' => request()->routeIs('whmpanel.websites*')]) href="{{ route('whmpanel.websites') }}"><i data-lucide="globe"></i>Websites</a>
                </div>
                <div class="nav-group">
                    <p>Core centers</p>
                    <a @class(['active' => request()->is('whmpanel/services/file_manager')]) href="{{ route('whmpanel.services.show', 'file_manager') }}"><i data-lucide="folder-open"></i>File Manager</a>
                    <a @class(['active' => request()->is('whmpanel/services/webmail')]) href="{{ route('whmpanel.services.show', 'webmail') }}"><i data-lucide="mail"></i>Webmail</a>
                    <a @class(['active' => request()->is('whmpanel/services/dns')]) href="{{ route('whmpanel.services.show', 'dns') }}"><i data-lucide="network"></i>DNS</a>
                    <a @class(['active' => request()->is('whmpanel/services/ssl')]) href="{{ route('whmpanel.services.show', 'ssl') }}"><i data-lucide="shield-check"></i>SSL</a>
                    <a @class(['active' => request()->is('whmpanel/services/databases')]) href="{{ route('whmpanel.services.show', 'databases') }}"><i data-lucide="database"></i>Databases</a>
                    <a @class(['active' => request()->is('whmpanel/services/backups')]) href="{{ route('whmpanel.services.show', 'backups') }}"><i data-lucide="archive-restore"></i>Backups</a>
                </div>
                <div class="nav-group">
                    <p>Runtime & apps</p>
                    <a @class(['active' => request()->is('whmpanel/services/php_selector')]) href="{{ route('whmpanel.services.show', 'php_selector') }}"><i data-lucide="file-code-2"></i>PHP Selector</a>
                    <a @class(['active' => request()->is('whmpanel/services/terminal')]) href="{{ route('whmpanel.services.show', 'terminal') }}"><i data-lucide="square-terminal"></i>Terminal</a>
                    <a @class(['active' => request()->is('whmpanel/services/nodejs')]) href="{{ route('whmpanel.services.show', 'nodejs') }}"><i data-lucide="hexagon"></i>Node.js Apps</a>
                    <a @class(['active' => request()->is('whmpanel/services/python')]) href="{{ route('whmpanel.services.show', 'python') }}"><i data-lucide="blocks"></i>Python Apps</a>
                    <a @class(['active' => request()->is('whmpanel/services/apps')]) href="{{ route('whmpanel.services.show', 'apps') }}"><i data-lucide="rocket"></i>App Installer</a>
                </div>
                <div class="nav-group">
                    <p>Operations</p>
                    <a @class(['active' => request()->is('whmpanel/services/logs')]) href="{{ route('whmpanel.services.show', 'logs') }}"><i data-lucide="scroll-text"></i>Logs</a>
                    <a @class(['active' => request()->is('whmpanel/services/security')]) href="{{ route('whmpanel.services.show', 'security') }}"><i data-lucide="lock-keyhole"></i>Security</a>
                    <a @class(['active' => request()->is('whmpanel/services/fleet')]) href="{{ route('whmpanel.services.show', 'fleet') }}"><i data-lucide="server-cog"></i>Fleet</a>
                    <a @class(['active' => request()->is('whmpanel/services/control_strip')]) href="{{ route('whmpanel.services.show', 'control_strip') }}"><i data-lucide="panel-top"></i>Client Controls</a>
                </div>
                <div class="nav-group">
                    <p>API</p>
                    <a href="{{ route('whmpanel.api.services') }}"><i data-lucide="braces"></i>Services JSON</a>
                    <a href="{{ route('whmpanel.api.server.info') }}"><i data-lucide="activity"></i>Health JSON</a>
                </div>
                <div class="nav-group">
                    <p>Operations</p>
                    <a href="{{ url('/') }}"><i data-lucide="credit-card"></i>Billing portal</a>
                </div>
            </nav>
        </aside>
        <main class="main">
            <header class="topbar">
                <div>
                    <h1>{{ $pageTitle }}</h1>
                    <small>Focused hosting operations for domains, services, and node health</small>
                </div>
                <div class="top-actions">
                    <a class="btn secondary" href="{{ route('whmpanel.dashboard') }}"><i data-lucide="arrow-left"></i>Dashboard</a>
                    <a class="btn" href="{{ route('whmpanel.services') }}"><i data-lucide="blocks"></i>Services</a>
                </div>
            </header>
            <nav class="mobile-tabs" aria-label="WHMPanel sections">
                <a @class(['active' => request()->routeIs('whmpanel.dashboard')]) href="{{ route('whmpanel.dashboard') }}"><i data-lucide="layout-dashboard"></i>Dashboard</a>
                <a @class(['active' => request()->routeIs('whmpanel.services*')]) href="{{ route('whmpanel.services') }}"><i data-lucide="blocks"></i>Services</a>
                <a @class(['active' => request()->routeIs('whmpanel.websites*')]) href="{{ route('whmpanel.websites') }}"><i data-lucide="globe"></i>Websites</a>
                <a href="{{ route('whmpanel.api.server.info') }}"><i data-lucide="activity"></i>Health</a>
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
