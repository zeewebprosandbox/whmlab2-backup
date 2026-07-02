@extends('whmpanel.layout')

@section('content')
    @php
        $moduleKey = $serviceModule['key'];
        $isCreateModule = !in_array($moduleKey, ['file_manager', 'dns', 'ssl', 'php_selector', 'fleet', 'control_strip']);
        $requiresDomain = in_array($moduleKey, ['webmail', 'databases', 'backups']);
        $defaultType = match ($moduleKey) {
            'webmail' => 'mailbox',
            'databases' => 'database',
            'backups' => 'snapshot',
            'nodejs', 'python', 'apps' => 'app',
            'security' => 'rule',
            'logs' => 'event',
            'terminal' => 'audit',
            default => 'item',
        };
        $nameLabel = match ($moduleKey) {
            'webmail' => 'Mailbox address',
            'databases' => 'Database name',
            'backups' => 'Backup label',
            'nodejs', 'python', 'apps' => 'Application name',
            'security' => 'Rule name',
            'logs' => 'Event label',
            'terminal' => 'Terminal request label',
            default => 'Name',
        };
        $placeholder = match ($moduleKey) {
            'webmail' => 'hello@example.com',
            'databases' => 'app_production',
            'backups' => 'Pre-update backup',
            'nodejs', 'python', 'apps' => 'Customer portal',
            'security' => 'Block suspicious IP',
            'logs' => 'Manual investigation',
            'terminal' => 'Open domain shell',
            default => 'New service item',
        };
    @endphp

    <section class="hero">
        <div>
            <p class="eyebrow">Service center</p>
            <h2>{{ $serviceModule['name'] }}</h2>
            <p>{{ $serviceModule['description'] }}</p>
        </div>
        <div class="hero-status">
            <div class="status-row"><span>Module status</span><span class="badge {{ $serviceModule['status'] }}">{{ ucfirst($serviceModule['status']) }}</span></div>
            <div class="status-row"><span>Node</span><span class="badge {{ $serviceModule['nodeReady'] ? 'online' : 'blocked' }}">{{ $serviceModule['nodeReady'] ? 'Ready' : 'Blocked' }}</span></div>
            <div class="status-row"><span>Package</span><span class="badge {{ $serviceModule['packageReady'] ? 'online' : 'blocked' }}">{{ $serviceModule['packageReady'] ? 'Enabled' : 'Gated' }}</span></div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px">
                <a class="btn secondary" href="{{ route('whmpanel.api.services.show', $moduleKey) }}"><i data-lucide="braces"></i>JSON</a>
                <a class="btn" href="{{ route('whmpanel.services') }}"><i data-lucide="blocks"></i>All services</a>
            </div>
        </div>
    </section>

    <section class="grid stats" style="margin-top:18px">
        @foreach($payload['stats'] as $stat)
            <article class="card">
                <div class="metric-head">
                    <span class="metric-label">{{ $stat['label'] }}</span>
                    <span class="badge {{ $stat['tone'] }}">{{ ucfirst($stat['tone']) }}</span>
                </div>
                <div class="metric-value" style="font-size:26px">{{ $stat['value'] }}</div>
            </article>
        @endforeach
    </section>

    <section class="grid split" style="margin-top:18px">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Domain operations</h3>
                    <p>Run module-aware actions against mirrored domains through live bridge routes where available.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Status</th>
                            <th>Runtime</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payload['websites'] as $website)
                            <tr>
                                <td data-label="Domain">
                                    <strong>{{ $website->domain }}</strong>
                                    <br><small style="color:var(--muted)">{{ $website->account->username ?? 'unassigned' }}</small>
                                </td>
                                <td data-label="Status">
                                    <span class="badge {{ $website->ssl_enabled ? 'online' : 'blocked' }}">{{ $website->ssl_enabled ? 'SSL on' : 'SSL off' }}</span>
                                </td>
                                <td data-label="Runtime">PHP {{ $website->php_version }}</td>
                                <td data-label="Action">
                                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                                        @if($moduleKey === 'dns')
                                            <form method="post" action="{{ route('whmpanel.websites.dns.repair', $website->domain) }}">
                                                @csrf
                                                <button class="btn secondary" type="submit"><i data-lucide="wrench"></i>Repair DNS</button>
                                            </form>
                                        @elseif($moduleKey === 'ssl')
                                            <form method="post" action="{{ route('whmpanel.websites.ssl.enable', $website->domain) }}">
                                                @csrf
                                                <button class="btn secondary" type="submit"><i data-lucide="shield-check"></i>Enable SSL</button>
                                            </form>
                                        @elseif($moduleKey === 'php_selector')
                                            <a class="btn secondary" href="{{ route('whmpanel.websites.show', $website->domain) }}"><i data-lucide="file-code-2"></i>Switch PHP</a>
                                        @elseif($moduleKey === 'webmail')
                                            <a class="btn secondary" href="https://webmail.{{ $website->domain }}/" target="_blank" rel="noopener"><i data-lucide="mail"></i>Open webmail</a>
                                        @elseif($moduleKey === 'file_manager')
                                            <a class="btn secondary" href="{{ route('whmpanel.websites.show', $website->domain) }}"><i data-lucide="folder-open"></i>Open root</a>
                                        @elseif($moduleKey === 'terminal')
                                            <span class="badge planned">Live terminal policy pending</span>
                                        @else
                                            <a class="btn secondary" href="{{ route('whmpanel.websites.show', $website->domain) }}"><i data-lucide="settings-2"></i>Open</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No mirrored websites yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <aside class="card">
            @if($isCreateModule)
                <div class="card-head">
                    <div>
                        <h3>{{ $serviceModule['primaryAction'] }}</h3>
                        <p>
                            @if($requiresDomain)
                                This runs the live ZodPanel/Hestia bridge command and records the response as an audit item.
                            @else
                                This records a guarded implementation spec until the module-specific live worker is enabled on the node.
                            @endif
                        </p>
                    </div>
                </div>
                <form method="post" action="{{ route('whmpanel.services.items.store', $moduleKey) }}" class="grid">
                    @csrf
                    <input type="hidden" name="type" value="{{ $defaultType }}">
                    <div class="field">
                        <label for="website_id">Domain</label>
                        <select id="website_id" name="website_id" @if($requiresDomain) required @endif>
                            <option value="">{{ $requiresDomain ? 'Select domain' : 'Account level / optional domain' }}</option>
                            @foreach($websites as $website)
                                <option value="{{ $website->id }}">{{ $website->domain }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="name">{{ $nameLabel }}</label>
                        <input id="name" name="name" value="{{ old('name') }}" placeholder="{{ $placeholder }}" required>
                    </div>
                    @if($moduleKey === 'webmail')
                        <div class="field">
                            <label for="quota_mb">Mailbox quota MB</label>
                            <input id="quota_mb" name="quota_mb" type="number" min="128" value="1024">
                        </div>
                    @elseif($moduleKey === 'databases')
                        <div class="field">
                            <label for="db_user">Database user</label>
                            <input id="db_user" name="db_user" placeholder="app_user">
                        </div>
                    @elseif(in_array($moduleKey, ['nodejs', 'python', 'apps']))
                        <div class="field">
                            <label for="startup">Startup command</label>
                            <input id="startup" name="startup" placeholder="{{ $moduleKey === 'python' ? 'gunicorn app:app' : 'npm start' }}">
                        </div>
                    @elseif($moduleKey === 'terminal')
                        <div class="field">
                            <label for="path">Safe path</label>
                            <input id="path" name="path" placeholder="/home/user/web/example.com/public_html">
                        </div>
                    @elseif($moduleKey === 'security')
                        <div class="field">
                            <label for="target">Target</label>
                            <input id="target" name="target" placeholder="IP, path, or policy">
                        </div>
                    @endif
                    <button class="btn" type="submit"><i data-lucide="plus"></i>Create</button>
                </form>
                @error('name')
                    <p style="margin-top:12px;color:var(--danger)">{{ $message }}</p>
                @enderror
            @else
                <h3>Capabilities</h3>
                <p style="margin-top:4px">Visible controls stay package-aware and node-aware before a user can execute an action.</p>
                <ul class="service-list" style="margin-top:12px">
                    @foreach($serviceModule['capabilities'] as $capability)
                        <li>{{ $capability }}</li>
                    @endforeach
                </ul>
                <div class="module-map" style="margin-top:16px">
                    @if($moduleKey === 'file_manager')
                        <a href="{{ route('whmpanel.websites') }}"><span><i data-lucide="folder-open"></i>Open domain roots</span><i data-lucide="arrow-right"></i></a>
                    @elseif($moduleKey === 'dns')
                        <a href="{{ route('whmpanel.websites') }}"><span><i data-lucide="wrench"></i>Repair DNS per domain</span><i data-lucide="arrow-right"></i></a>
                    @elseif($moduleKey === 'ssl')
                        <a href="{{ route('whmpanel.websites') }}"><span><i data-lucide="shield-check"></i>Enable SSL per domain</span><i data-lucide="arrow-right"></i></a>
                    @elseif($moduleKey === 'php_selector')
                        <a href="{{ route('whmpanel.websites') }}"><span><i data-lucide="file-code-2"></i>Switch PHP per domain</span><i data-lucide="arrow-right"></i></a>
                    @elseif($moduleKey === 'fleet')
                        <a href="{{ route('whmpanel.api.server.info') }}"><span><i data-lucide="activity"></i>Node health JSON</span><i data-lucide="arrow-right"></i></a>
                    @elseif($moduleKey === 'control_strip')
                        <a href="{{ route('whmpanel.websites') }}"><span><i data-lucide="panel-top"></i>Preview service controls</span><i data-lucide="arrow-right"></i></a>
                    @endif
                </div>
            @endif
        </aside>
    </section>

    <section class="card" style="margin-top:18px">
        <div class="card-head">
            <div>
                <h3>Live {{ $serviceModule['name'] }} state</h3>
                <p>Current data read directly from the ZodPanel/Hestia node where a live service is attached.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Name</th><th>Domain</th><th>Type</th><th>Status</th><th>Details</th></tr>
                </thead>
                <tbody>
                    @forelse($payload['live'] as $row)
                        <tr>
                            <td data-label="Name">{{ $row['name'] }}</td>
                            <td data-label="Domain">{{ $row['domain'] }}</td>
                            <td data-label="Type">{{ $row['type'] }}</td>
                            <td data-label="Status"><span class="badge {{ $row['status'] === 'no' || $row['status'] === 'local' || $row['status'] === 'active' ? 'online' : 'planned' }}">{{ $row['status'] }}</span></td>
                            <td data-label="Details">{{ $row['meta'] ?: 'Ready' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No live node records returned for this module yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card" style="margin-top:18px">
        <div class="card-head">
            <div>
                <h3>Recent {{ $serviceModule['name'] }} actions</h3>
                <p>Audit records for completed live bridge actions.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Name</th><th>Domain</th><th>Type</th><th>Status</th><th>Checked</th></tr>
                </thead>
                <tbody>
                    @forelse($payload['items'] as $item)
                        <tr>
                            <td data-label="Name">{{ $item->name }}</td>
                            <td data-label="Domain">{{ $item->website->domain ?? 'Account level' }}</td>
                            <td data-label="Type">{{ $item->type ?: 'item' }}</td>
                            <td data-label="Status"><span class="badge {{ $item->status === 'completed' || $item->status === 'active' || $item->status === 'running' ? 'online' : 'planned' }}">{{ $item->status }}</span></td>
                            <td data-label="Checked">{{ optional($item->last_checked_at)->diffForHumans() ?: 'Not checked' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No {{ strtolower($serviceModule['name']) }} activity recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
