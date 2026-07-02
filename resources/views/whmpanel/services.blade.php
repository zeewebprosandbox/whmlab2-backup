@extends('whmpanel.layout')

@section('content')
    @php
        $moduleGroups = [
            'Core hosting' => [
                'description' => 'Daily controls for sites, files, mail, DNS, SSL, databases, and backups.',
                'keys' => ['file_manager', 'webmail', 'dns', 'ssl', 'databases', 'backups'],
            ],
            'Runtime and deploy' => [
                'description' => 'Developer-facing runtimes and app deployment flows.',
                'keys' => ['php_selector', 'terminal', 'nodejs', 'python', 'apps'],
            ],
            'Operations' => [
                'description' => 'Diagnostics, security, server fleet, and client-side control surfaces.',
                'keys' => ['logs', 'security', 'fleet', 'control_strip'],
            ],
        ];
    @endphp

    <section class="hero">
        <div>
            <p class="eyebrow">Service architecture</p>
            <h2>Every hosting capability in one operating map</h2>
            <p>These modules define the WHMPanel roadmap and the package-aware controls that will replace old cPanel-style clutter with focused hosting operations.</p>
        </div>
        <div class="hero-status">
            <div class="status-row"><span>Total modules</span><strong>{{ $serviceSummary['total'] }}</strong></div>
            <div class="status-row"><span>Live</span><span class="badge live">{{ $serviceSummary['live'] }}</span></div>
            <div class="status-row"><span>Planned</span><span class="badge planned">{{ $serviceSummary['planned'] }}</span></div>
            <div class="status-row"><span>Blocked</span><span class="badge blocked">{{ $serviceSummary['blocked'] }}</span></div>
        </div>
    </section>

    @foreach($moduleGroups as $groupName => $group)
        @php
            $modules = collect($serviceModules)->whereIn('key', $group['keys'])->sortBy(fn($module) => array_search($module['key'], $group['keys'], true));
        @endphp
        <section class="module-section">
            <div class="module-section__head">
                <div>
                    <h3>{{ $groupName }}</h3>
                    <p>{{ $group['description'] }}</p>
                </div>
                <span class="badge">{{ $modules->count() }} modules</span>
            </div>
            <div class="grid service-grid">
                @foreach($modules as $module)
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
                            @foreach(array_slice($module['capabilities'], 0, 6) as $capability)
                                <li>{{ $capability }}</li>
                            @endforeach
                        </ul>
                        <div style="display:flex;gap:7px;flex-wrap:wrap;margin-top:12px">
                            <span class="badge {{ $module['nodeReady'] ? 'online' : 'blocked' }}">{{ $module['nodeReady'] ? 'Node ready' : 'Node blocked' }}</span>
                            <span class="badge {{ $module['packageReady'] ? 'online' : 'blocked' }}">{{ $module['packageReady'] ? 'Package ready' : 'Package gated' }}</span>
                        </div>
                        <a class="btn secondary" href="{{ route('whmpanel.services.show', $module['key']) }}" style="margin-top:14px">
                            <i data-lucide="arrow-right"></i>{{ $module['primaryAction'] }}
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach
@endsection
