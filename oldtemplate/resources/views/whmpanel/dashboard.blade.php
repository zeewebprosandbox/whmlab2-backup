@extends('whmpanel.layout')

@section('content')
    @php
        $diskPercent = $primaryNode && $primaryNode->total_disk_mb ? round(($primaryNode->used_disk_mb / $primaryNode->total_disk_mb) * 100) : 0;
        $bandwidthPercent = $primaryNode && $primaryNode->total_bandwidth_mb ? round(($primaryNode->used_bandwidth_mb / $primaryNode->total_bandwidth_mb) * 100) : 0;
    @endphp

    <section class="hero">
        <div>
            <p class="eyebrow">Primary hosting command layer</p>
            <h2>ZodPanel operations without the old control-panel maze</h2>
            <p>Provisioning, domains, DNS, SSL, mail, runtimes, and service checks are grouped around the work operators do every day: inspect the node, fix the domain, open the right tool, and move on.</p>
        </div>
        <div class="hero-status">
            <div class="status-row"><span>Node</span><strong>{{ $primaryNode->name ?? 'Local node' }}</strong></div>
            <div class="status-row"><span>Status</span><span class="badge online">{{ $primaryNode->status ?? 'online' }}</span></div>
            <div class="status-row"><span>Bridge</span><strong>v1 ready</strong></div>
            <div class="status-row"><span>Modules</span><strong>{{ $serviceSummary['total'] }}</strong></div>
        </div>
    </section>

    <section class="grid stats" style="margin-top:18px">
        <article class="card">
            <div class="metric-head"><span class="metric-label">Nodes</span><span class="metric-icon"><i data-lucide="server"></i></span></div>
            <div class="metric-value">{{ $nodes->count() }}</div>
            <div class="metric-note">Attached ZodPanel endpoints</div>
        </article>
        <article class="card">
            <div class="metric-head"><span class="metric-label">Accounts</span><span class="metric-icon"><i data-lucide="users"></i></span></div>
            <div class="metric-value">{{ $accounts->count() }}</div>
            <div class="metric-note">Provisioned service users</div>
        </article>
        <article class="card">
            <div class="metric-head"><span class="metric-label">CPU</span><span class="metric-icon"><i data-lucide="cpu"></i></span></div>
            <div class="metric-value">{{ $primaryNode->cpu_percent ?? 0 }}%</div>
            <div class="metric-note">Current node load</div>
        </article>
        <article class="card">
            <div class="metric-head"><span class="metric-label">Memory</span><span class="metric-icon"><i data-lucide="memory-stick"></i></span></div>
            <div class="metric-value">{{ $primaryNode->memory_percent ?? 0 }}%</div>
            <div class="metric-note">Runtime usage</div>
        </article>
    </section>

    <section class="grid split" style="margin-top:18px">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Operator path</h3>
                    <p>Common hosting tasks are surfaced as direct routes instead of buried menus.</p>
                </div>
            </div>
            <div class="module-map">
                <a href="{{ route('whmpanel.websites') }}"><span><i data-lucide="globe"></i>Diagnose a domain</span><i data-lucide="arrow-right"></i></a>
                <a href="{{ route('whmpanel.services.show', 'ssl') }}"><span><i data-lucide="shield-check"></i>Repair SSL coverage</span><i data-lucide="arrow-right"></i></a>
                <a href="{{ route('whmpanel.services.show', 'dns') }}"><span><i data-lucide="network"></i>Validate DNS records</span><i data-lucide="arrow-right"></i></a>
                <a href="{{ route('whmpanel.services.show', 'webmail') }}"><span><i data-lucide="mail"></i>Open mailbox controls</span><i data-lucide="arrow-right"></i></a>
            </div>
        </div>
        <aside class="card">
            <h3>Primary node capacity</h3>
            <p>{{ $primaryNode->name ?? 'No node yet' }}</p>
            <div class="node-meter">
                <div>
                    <div class="status-row"><span>Disk usage</span><strong>{{ $diskPercent }}%</strong></div>
                    <div class="bar"><span style="width: {{ min($diskPercent, 100) }}%"></span></div>
                </div>
                <div>
                    <div class="status-row"><span>Bandwidth usage</span><strong>{{ $bandwidthPercent }}%</strong></div>
                    <div class="bar"><span style="width: {{ min($bandwidthPercent, 100) }}%"></span></div>
                </div>
            </div>
        </aside>
    </section>

    <section class="card" style="margin-top:18px">
        <div class="card-head">
            <div>
                <h3>Service map</h3>
                <p>{{ $serviceSummary['live'] }} live, {{ $serviceSummary['planned'] }} planned, {{ $serviceSummary['blocked'] }} package or node-gated modules.</p>
            </div>
            <a class="btn secondary" href="{{ route('whmpanel.api.services') }}"><i data-lucide="external-link"></i>JSON</a>
        </div>
        <div class="grid service-grid">
            @foreach($serviceModules as $module)
                <article class="card service-card">
                    <div class="service-top">
                        <span class="service-icon"><i data-lucide="{{ $module['icon'] }}"></i></span>
                        <span class="badge {{ $module['status'] }}">{{ ucfirst($module['status']) }}</span>
                    </div>
                    <div>
                        <h3>{{ $module['name'] }}</h3>
                        <p>{{ $module['description'] }}</p>
                    </div>
                    <ul class="service-list">
                        @foreach(array_slice($module['capabilities'], 0, 4) as $capability)
                            <li>{{ $capability }}</li>
                        @endforeach
                    </ul>
                    <div style="display:flex;gap:7px;flex-wrap:wrap;margin-top:auto">
                        <span class="badge {{ $module['nodeReady'] ? 'online' : 'blocked' }}">{{ $module['nodeReady'] ? 'Node ready' : 'Node blocked' }}</span>
                        <span class="badge {{ $module['packageReady'] ? 'online' : 'blocked' }}">{{ $module['packageReady'] ? 'Package ready' : 'Package gated' }}</span>
                    </div>
                    <a class="btn secondary" href="{{ route('whmpanel.services.show', $module['key']) }}" style="margin-top:12px">
                        <i data-lucide="arrow-right"></i>{{ $module['primaryAction'] }}
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="card" style="margin-top:18px">
        <div class="card-head">
            <div>
                <h3>Aligned node action</h3>
                <p>Run quick checks against the selected node, account, service, or domain.</p>
            </div>
        </div>
        <form class="grid control-grid" action="{{ route('whmpanel.api.server.stats') }}" method="get">
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

    <section class="grid section" style="margin-top:18px">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Recent accounts</h3>
                    <p>Provisioned locally through ZodHost automation or ZodPanel API calls.</p>
                </div>
                <a class="btn secondary" href="{{ route('whmpanel.api.users') }}"><i data-lucide="external-link"></i>JSON</a>
            </div>
            <div class="table-wrap">
                <table class="table">
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
        </div>
        <aside class="card">
            <h3>Bridge readiness</h3>
            <p>Fast links for API consumers and support tooling.</p>
            <div class="module-map" style="margin-top:16px">
                <a href="{{ route('whmpanel.api.server.info') }}"><span><i data-lucide="activity"></i>Server health JSON</span><i data-lucide="arrow-right"></i></a>
                <a href="{{ route('whmpanel.api.services') }}"><span><i data-lucide="braces"></i>Service catalog JSON</span><i data-lucide="arrow-right"></i></a>
                <a href="{{ route('whmpanel.api.websites') }}"><span><i data-lucide="globe"></i>Website records JSON</span><i data-lucide="arrow-right"></i></a>
            </div>
        </aside>
    </section>
@endsection
