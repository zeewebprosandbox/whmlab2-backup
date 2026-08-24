@extends('whmpanel.layout')

@section('content')
    <section class="hero">
        <div>
            <p class="eyebrow">Domain workspace</p>
            <h2>{{ $website->domain }}</h2>
            <p>Manage runtime, SSL readiness, webmail access, and DNS records from one domain-focused screen built for fast support and client operations.</p>
        </div>
        <div class="hero-status">
            <div class="status-row"><span>Account</span><strong>{{ $website->account->username ?? 'unassigned' }}</strong></div>
            <div class="status-row"><span>Runtime</span><strong>PHP {{ $website->php_version }}</strong></div>
            <div class="status-row"><span>SSL</span><span class="badge {{ $website->ssl_enabled ? 'online' : 'blocked' }}">{{ $website->ssl_enabled ? 'Enabled' : 'Off' }}</span></div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px">
            <a class="btn secondary" href="http://{{ $website->domain }}/" target="_blank" rel="noopener"><i data-lucide="external-link"></i>Visit</a>
            <a class="btn" href="{{ $diagnostics['mail']['webmail_url'] }}" target="_blank" rel="noopener"><i data-lucide="mail"></i>Webmail</a>
            </div>
        </div>
    </section>

    @if(!empty($diagnostics['blockers']))
        <div class="notice" style="margin-top:18px">
            <strong>Blockers found:</strong>
            {{ implode(' ', $diagnostics['blockers']) }}
        </div>
    @endif

    <section class="grid split" style="margin-top:18px">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>PHP Selector</h3>
                    <p>Switch the local simulator runtime for this domain. Live nodes use the bridge PHP backend endpoint.</p>
                </div>
                <span class="badge live">Current {{ $website->php_version }}</span>
            </div>
            <form method="post" action="{{ route('whmpanel.websites.php.update', $website->domain) }}" class="grid" style="grid-template-columns:minmax(0,1fr) auto;align-items:end">
                @csrf
                <div class="field">
                    <label for="php_version">PHP version</label>
                    <select id="php_version" name="php_version">
                        @foreach($phpVersions as $version)
                            <option value="{{ $version }}" @selected($website->php_version === $version)>PHP {{ $version }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn" type="submit"><i data-lucide="save"></i>Apply</button>
            </form>
            @error('php_version')
                <p style="margin-top:12px;color:var(--danger)">{{ $message }}</p>
            @enderror
        </div>

        <div class="card">
            <h3>Readiness</h3>
            <div class="status-row"><span>Web domain</span><span class="badge {{ $diagnostics['web']['exists'] ? 'online' : 'blocked' }}">{{ $diagnostics['web']['exists'] ? 'Ready' : 'Missing' }}</span></div>
            <div class="status-row"><span>SSL</span><span class="badge {{ $diagnostics['web']['ssl'] ? 'online' : 'blocked' }}">{{ $diagnostics['web']['ssl'] ? 'Enabled' : 'Off' }}</span></div>
            <div class="status-row"><span>Force HTTPS</span><span class="badge {{ $diagnostics['web']['force_https'] ? 'online' : 'blocked' }}">{{ $diagnostics['web']['force_https'] ? 'On' : 'Off' }}</span></div>
            <div class="status-row"><span>Webmail</span><span class="badge planned">{{ $diagnostics['mail']['status'] }}</span></div>
            <div class="status-row"><span>Node IP</span><strong>{{ $diagnostics['node_ip'] }}</strong></div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px">
                <form method="post" action="{{ route('whmpanel.websites.ssl.enable', $website->domain) }}">
                    @csrf
                    <button class="btn secondary" type="submit"><i data-lucide="shield-check"></i>Enable SSL</button>
                </form>
                <form method="post" action="{{ route('whmpanel.websites.dns.repair', $website->domain) }}">
                    @csrf
                    <button class="btn secondary" type="submit"><i data-lucide="wrench"></i>Repair DNS</button>
                </form>
            </div>
        </div>
    </section>

    <section class="grid split" style="margin-top:18px">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Required DNS records</h3>
                    <p>Use these records when the domain is not delegated to ZodPanel nameservers.</p>
                </div>
            </div>
            <ul class="copy-list">
                @foreach($diagnostics['dns']['required_records'] as $record)
                    <li>
                        <strong>{{ $record['name'] }}</strong>
                        <span>{{ $record['type'] }}{{ isset($record['priority']) ? ' / '.$record['priority'] : '' }}</span>
                        <code>{{ $record['value'] }}</code>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Managed records</h3>
                    <p>Records currently stored in the local WHMPanel simulator.</p>
                </div>
                <a class="btn secondary" href="{{ route('whmpanel.api.dns.records', $website->domain) }}"><i data-lucide="braces"></i>JSON</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Name</th><th>Type</th><th>Value</th></tr></thead>
                    <tbody>
                        @forelse($website->dnsRecords as $record)
                            <tr>
                                <td data-label="Name">{{ $record->name }}</td>
                                <td data-label="Type">{{ $record->type }}</td>
                                <td data-label="Value">{{ $record->value }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No local DNS records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
