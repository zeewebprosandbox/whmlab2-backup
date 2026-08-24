<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - {{ config('app.name') }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --ink: #0f172a;
            --text: #334155;
            --muted: #64748b;
            --line: #e2e8f0;
            --line-strong: #cbd5e1;
            --soft: #f8fafc;
            --soft-2: #f1f5f9;
            --surface: #ffffff;
            --success: #059669;
            --warning: #b45309;
            --danger: #b91c1c;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--soft);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.5;
            letter-spacing: 0;
        }
        a { color: inherit; text-decoration: none; }
        button, input, select { font: inherit; }

        .shell { min-height: 100vh; display: grid; grid-template-columns: 272px minmax(0, 1fr); }
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--line);
            padding: 18px 14px;
            overflow-y: auto;
        }
        .brand { display: flex; align-items: center; gap: 12px; padding: 4px 8px 18px; border-bottom: 1px solid var(--line); }
        .brand-mark { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; background: var(--brand); color: #fff; }
        .brand strong { display: block; font-size: 18px; letter-spacing: -0.01em; }
        .brand span { color: var(--muted); font-size: 12px; }
        .nav { padding: 18px 0; display: grid; gap: 22px; }
        .nav-group p { margin: 0 0 8px; padding: 0 10px; color: var(--muted); font-size: 11px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .nav a { min-height: 42px; display: flex; align-items: center; gap: 10px; padding: 10px 11px; border-radius: 10px; color: var(--text); font-size: 14px; font-weight: 650; }
        .nav a.active { background: #eff6ff; color: var(--brand); box-shadow: inset 3px 0 0 var(--brand); }
        .nav a:hover { background: var(--soft); color: var(--ink); }
        .nav svg { width: 18px; height: 18px; flex: 0 0 auto; }

        .main { min-width: 0; }
        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            min-height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 0 32px;
            background: rgba(255, 255, 255, .96);
            border-bottom: 1px solid var(--line);
        }
        .topbar h1 { margin: 0; font-size: 18px; letter-spacing: -0.01em; }
        .topbar small { color: var(--muted); }
        .top-actions { display: flex; align-items: center; gap: 10px; }
        .content { max-width: 1440px; margin: 0 auto; padding: 32px; }

        .btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--brand);
            border-radius: 10px;
            background: var(--brand);
            color: #fff;
            padding: 10px 16px;
            font-weight: 750;
            white-space: nowrap;
            cursor: pointer;
        }
        .btn:hover { background: var(--brand-dark); border-color: var(--brand-dark); }
        .btn.secondary { background: var(--surface); border-color: var(--line); color: var(--text); }
        .btn.secondary:hover { background: var(--soft); border-color: var(--line-strong); color: var(--ink); }
        .btn svg { width: 17px; height: 17px; }

        .hero {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 26px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 24px;
        }
        .eyebrow { margin: 0 0 8px; color: var(--brand); font-size: 12px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .hero h2 { margin: 0 0 8px; font-size: clamp(27px, 4vw, 42px); line-height: 1.12; letter-spacing: -0.02em; }
        .hero p { margin: 0; color: var(--muted); max-width: 760px; }
        .hero-status {
            width: min(360px, 100%);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            background: var(--soft);
        }
        .status-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 9px 0; border-bottom: 1px solid var(--line); }
        .status-row:last-child { border-bottom: 0; }
        .status-row span:first-child { color: var(--muted); font-size: 13px; }
        .status-row strong { font-size: 14px; }

        .grid { display: grid; gap: 18px; }
        .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 18px; }
        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .card:hover { transform: translateY(-1px); border-color: var(--line-strong); box-shadow: 0 4px 6px -1px rgba(15, 23, 42, .05); }
        .metric-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .metric-icon { width: 38px; height: 38px; display: grid; place-items: center; border-radius: 10px; background: #eff6ff; color: var(--brand); }
        .metric-icon svg { width: 19px; height: 19px; }
        .metric-label { color: var(--muted); font-size: 12px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .metric-value { margin-top: 12px; font-size: 31px; font-weight: 850; letter-spacing: -.03em; }
        .metric-note { margin-top: 2px; color: var(--muted); font-size: 13px; }

        .section { margin-top: 18px; grid-template-columns: minmax(0, 1.45fr) minmax(360px, .85fr); align-items: start; }
        .section h3 { margin: 0 0 4px; font-size: 18px; letter-spacing: -0.01em; }
        .section p { margin: 0; color: var(--muted); }
        .card-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px; }

        .control-grid {
            display: grid;
            grid-template-columns: minmax(160px, 1fr) minmax(160px, 1fr) minmax(190px, 1.2fr) auto;
            gap: 12px;
            align-items: end;
            margin-top: 18px;
        }
        .field { min-width: 0; }
        .field label { display: block; margin: 0 0 7px; color: var(--muted); font-size: 12px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
        .field input,
        .field select {
            width: 100%;
            height: 42px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--ink);
            padding: 0 12px;
            outline: none;
        }
        .field select { appearance: none; background-image: linear-gradient(45deg, transparent 50%, var(--muted) 50%), linear-gradient(135deg, var(--muted) 50%, transparent 50%); background-position: calc(100% - 17px) 17px, calc(100% - 12px) 17px; background-size: 5px 5px, 5px 5px; background-repeat: no-repeat; padding-right: 34px; }
        .field input:focus,
        .field select:focus { border-color: var(--brand); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }

        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { padding: 14px 12px; border-top: 1px solid var(--line); text-align: left; font-size: 14px; }
        th { background: var(--soft); color: var(--muted); font-size: 12px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        tr:hover td { background: var(--soft); }
        .badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 3px 9px; font-size: 12px; font-weight: 750; border: 1px solid var(--line); background: var(--soft-2); color: var(--muted); }
        .badge.online, .badge.active { background: #ecfdf5; color: var(--success); border-color: #a7f3d0; }
        .badge.suspended { background: #fffbeb; color: var(--warning); border-color: #fde68a; }
        .badge.terminated { background: #fef2f2; color: var(--danger); border-color: #fecaca; }
        .bar { height: 9px; background: var(--soft-2); border-radius: 999px; overflow: hidden; margin-top: 10px; }
        .bar span { display: block; height: 100%; background: var(--brand); }
        .node-meter { display: grid; gap: 18px; margin-top: 18px; }

        @media (max-width: 1100px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .topbar { padding: 0 18px; }
            .content { padding: 18px; }
            .hero { grid-template-columns: 1fr; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .section { grid-template-columns: 1fr; }
            .control-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .topbar { align-items: flex-start; flex-direction: column; padding: 14px 16px; }
            .top-actions { width: 100%; }
            .top-actions .btn { flex: 1; }
            .content { padding: 16px; }
            .hero { padding: 20px; }
            .stats { grid-template-columns: 1fr; }
            .control-grid { grid-template-columns: 1fr; }
            .control-grid .btn { width: 100%; }
            table, thead, tbody, tr, td { display: block; }
            thead { display: none; }
            tr { border-top: 1px solid var(--line); padding: 10px 0; }
            td { border: 0; display: flex; justify-content: space-between; gap: 16px; }
            td::before { content: attr(data-label); color: var(--muted); font-size: 12px; font-weight: 800; text-transform: uppercase; }
        }
    </style>
</head>
<body>
    @php
        $diskPercent = $primaryNode && $primaryNode->total_disk_mb ? round(($primaryNode->used_disk_mb / $primaryNode->total_disk_mb) * 100) : 0;
        $bandwidthPercent = $primaryNode && $primaryNode->total_bandwidth_mb ? round(($primaryNode->used_bandwidth_mb / $primaryNode->total_bandwidth_mb) * 100) : 0;
    @endphp
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark"><i data-lucide="server"></i></div>
                <div><strong>WHMPanel</strong><span>ZodHost node console</span></div>
            </div>
            <nav class="nav">
                <div class="nav-group">
                    <p>Overview</p>
                    <a class="active" href="{{ route('whmpanel.dashboard') }}"><i data-lucide="layout-dashboard"></i>Dashboard</a>
                    <a href="{{ route('whmpanel.api.server.info') }}"><i data-lucide="activity"></i>API health</a>
                </div>
                <div class="nav-group">
                    <p>Resources</p>
                    <a href="{{ route('whmpanel.api.users') }}"><i data-lucide="users"></i>Accounts</a>
                    <a href="{{ route('whmpanel.api.websites') }}"><i data-lucide="globe"></i>Websites</a>
                </div>
                <div class="nav-group">
                    <p>Operations</p>
                    <a href="{{ route('whmpanel.api.server.stats') }}"><i data-lucide="bar-chart-3"></i>Node stats</a>
                    <a href="{{ url('/') }}"><i data-lucide="credit-card"></i>Billing portal</a>
                </div>
            </nav>
        </aside>
        <main class="main">
            <header class="topbar">
                <div>
                    <h1>WHMPanel</h1>
                    <small>{{ $primaryNode->hostname ?? 'Local development node' }}</small>
                </div>
                <div class="top-actions">
                    <a class="btn secondary" href="{{ route('whmpanel.api.server.info') }}"><i data-lucide="shield-check"></i>Health</a>
                    <a class="btn" href="{{ url('/') }}"><i data-lucide="arrow-left"></i>Billing portal</a>
                </div>
            </header>
            <div class="content">
                <section class="hero">
                    <div>
                        <p class="eyebrow">Primary hosting control layer</p>
                        <h2>Clean node operations for ZodHost services</h2>
                        <p>Provisioning tests, account summaries, website records, DNS workflows, support lookups, and SSO checks are organized in a compact control surface ready for real WHMPanel node automation.</p>
                    </div>
                    <div class="hero-status">
                        <div class="status-row"><span>Node</span><strong>{{ $primaryNode->name ?? 'Local node' }}</strong></div>
                        <div class="status-row"><span>Status</span><span class="badge online">{{ $primaryNode->status ?? 'online' }}</span></div>
                        <div class="status-row"><span>API</span><strong>v1 ready</strong></div>
                    </div>
                </section>

                <section class="grid stats">
                    <div class="card">
                        <div class="metric-head"><span class="metric-label">Nodes</span><span class="metric-icon"><i data-lucide="server"></i></span></div>
                        <div class="metric-value">{{ $nodes->count() }}</div>
                        <div class="metric-note">Attached WHMPanel endpoints</div>
                    </div>
                    <div class="card">
                        <div class="metric-head"><span class="metric-label">Accounts</span><span class="metric-icon"><i data-lucide="users"></i></span></div>
                        <div class="metric-value">{{ $accounts->count() }}</div>
                        <div class="metric-note">Provisioned service users</div>
                    </div>
                    <div class="card">
                        <div class="metric-head"><span class="metric-label">CPU</span><span class="metric-icon"><i data-lucide="cpu"></i></span></div>
                        <div class="metric-value">{{ $primaryNode->cpu_percent ?? 0 }}%</div>
                        <div class="metric-note">Current node load</div>
                    </div>
                    <div class="card">
                        <div class="metric-head"><span class="metric-label">Memory</span><span class="metric-icon"><i data-lucide="memory-stick"></i></span></div>
                        <div class="metric-value">{{ $primaryNode->memory_percent ?? 0 }}%</div>
                        <div class="metric-note">Runtime usage</div>
                    </div>
                </section>

                <section class="card" style="margin-top:18px">
                    <div class="card-head">
                        <div>
                            <h3>Aligned node action</h3>
                            <p>Inputs and selects stay on one clean row on desktop, then stack predictably on smaller screens.</p>
                        </div>
                    </div>
                    <form class="control-grid" action="{{ route('whmpanel.api.server.stats') }}" method="get">
                        <div class="field">
                            <label for="node">Node</label>
                            <select id="node" name="node">
                                @forelse($nodes as $node)
                                    <option value="{{ $node->id }}">{{ $node->name }}</option>
                                @empty
                                    <option value="local">Local development node</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="field">
                            <label for="action">Action</label>
                            <select id="action" name="action">
                                <option value="stats">View stats</option>
                                <option value="accounts">List accounts</option>
                                <option value="websites">List websites</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="query">Service or domain</label>
                            <input id="query" name="query" type="search" placeholder="example.com or client username">
                        </div>
                        <button class="btn" type="submit"><i data-lucide="search"></i>Run check</button>
                    </form>
                </section>

                <section class="grid section">
                    <div class="card">
                        <div class="card-head">
                            <div>
                                <h3>Recent accounts</h3>
                                <p>Provisioned locally through ZodHost automation or WHMPanel API calls.</p>
                            </div>
                            <a class="btn secondary" href="{{ route('whmpanel.api.users') }}"><i data-lucide="external-link"></i>JSON</a>
                        </div>
                        <table>
                            <thead><tr><th>User</th><th>Domain</th><th>Package</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($accounts as $account)
                                    <tr>
                                        <td data-label="User">{{ $account->username }}</td>
                                        <td data-label="Domain">{{ $account->primary_domain ?: '-' }}</td>
                                        <td data-label="Package">{{ $account->package ?: '-' }}</td>
                                        <td data-label="Status"><span class="badge {{ $account->status }}">{{ ucfirst($account->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4">No accounts provisioned yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card">
                        <h3>Primary node capacity</h3>
                        <p>{{ $primaryNode->name ?? 'No node yet' }}</p>
                        <div class="node-meter">
                            <div>
                                <div class="status-row"><span>Disk usage</span><strong>{{ $diskPercent }}%</strong></div>
                                <div class="bar"><span style="width: {{ $diskPercent }}%"></span></div>
                            </div>
                            <div>
                                <div class="status-row"><span>Bandwidth usage</span><strong>{{ $bandwidthPercent }}%</strong></div>
                                <div class="bar"><span style="width: {{ $bandwidthPercent }}%"></span></div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
