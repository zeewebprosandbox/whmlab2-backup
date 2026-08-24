@extends('whmpanel.layout')

@section('content')
    <section class="hero">
        <div>
            <p class="eyebrow">Domain operations</p>
            <h2>Every domain gets a clean operational cockpit</h2>
            <p>Open a domain workspace to verify DNS records, switch PHP, repair SSL, check webmail, and inspect the mirrored panel state without jumping between legacy screens.</p>
        </div>
        <div class="hero-status">
            <div class="status-row"><span>Visible records</span><strong>{{ $websites->count() }}</strong></div>
            <div class="status-row"><span>Total records</span><strong>{{ $websites->total() }}</strong></div>
            <div class="status-row"><span>API</span><a class="badge" href="{{ route('whmpanel.api.websites') }}">websites.json</a></div>
        </div>
    </section>

    <section class="card" style="margin-top:18px">
        <div class="card-head">
            <div>
                <h3>Websites</h3>
                <p>Local WHMPanel simulator records and mirrored panel state.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>User</th>
                        <th>PHP</th>
                        <th>SSL</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($websites as $website)
                        <tr>
                            <td data-label="Domain"><strong>{{ $website->domain }}</strong></td>
                            <td data-label="User">{{ $website->account->username ?? '-' }}</td>
                            <td data-label="PHP">PHP {{ $website->php_version }}</td>
                            <td data-label="SSL"><span class="badge {{ $website->ssl_enabled ? 'online' : 'blocked' }}">{{ $website->ssl_enabled ? 'Enabled' : 'Off' }}</span></td>
                            <td data-label="Status"><span class="badge {{ $website->status }}">{{ ucfirst($website->status) }}</span></td>
                            <td data-label="Action">
                                <a class="btn secondary" href="{{ route('whmpanel.websites.show', $website->domain) }}"><i data-lucide="settings-2"></i>Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No websites have been mirrored into WHMPanel yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px">{{ $websites->links() }}</div>
    </section>
@endsection
