@php
    $user = auth()->user();
    $sidebarServiceGroups = collect();

    if ($user) {
        $serviceRoles = collect(\App\Models\Server::serviceRoles())->except(['any', 'domain']);
        $serviceRoleCounts = array_fill_keys($serviceRoles->keys()->all(), 0);

        \App\Models\Hosting::where('user_id', $user->id)
            ->with('product.serviceCategory', 'server')
            ->get()
            ->each(function ($service) use (&$serviceRoleCounts) {
                $key = @$service->server->service_role ?: \App\Models\Server::roleForProduct($service->product);

                if (array_key_exists($key, $serviceRoleCounts)) {
                    $serviceRoleCounts[$key]++;
                }
            });

        $sidebarServiceGroups = $serviceRoles
            ->map(fn ($label, $key) => ['key' => $key, 'label' => $label, 'count' => $serviceRoleCounts[$key] ?? 0])
            ->filter(fn ($group) => $group['count'] > 0)
            ->values();
    }
@endphp

<header class="tw-nav whm-client-desktop-nav">
    <a href="{{ route('user.home') }}" class="tw-brand">
        <span class="tw-brand-mark"><i data-lucide="server"></i></span>
        <span>
            <strong class="tw-brand-title">{{ gs('site_name') }}</strong>
            <small class="tw-brand-subtitle">@lang('Client Area')</small>
        </span>
    </a>

    <nav class="tw-menu">
        <a href="{{ route('user.home') }}" class="tw-menu-link {{ request()->routeIs('user.home') ? 'active' : '' }}">@lang('Overview')</a>
        <div class="whm-menu-dropdown">
            <button type="button" class="tw-menu-button">@lang('My Services') <i data-lucide="chevron-down"></i></button>
            <div class="whm-dropdown-panel">
                <a href="{{ route('user.service.list') }}">@lang('Services')</a>
                <a href="{{ route('user.domain.list') }}">@lang('Domains')</a>
                <a href="{{ route('service.category') }}?all">@lang('Order New Service')</a>
                <a href="{{ route('register.domain') }}">@lang('Register Domain')</a>
            </div>
        </div>
        <a href="{{ route('user.invoice.list') }}" class="tw-menu-link {{ request()->routeIs('user.invoice.*') ? 'active' : '' }}">@lang('Invoices')</a>
        <a href="{{ route('ticket.index') }}" class="tw-menu-link {{ request()->routeIs('ticket.*') ? 'active' : '' }}">@lang('Support')</a>
    </nav>

    <div class="whm-desktop-actions">
        @include($activeTemplate . 'partials.cart_widget')
        <x-language />
        <a href="{{ route('user.profile.setting') }}" class="whm-user-chip">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->fullname ?? $user->username) }}&background=2563eb&color=fff" alt="@lang('User')">
            <span>{{ __($user->firstname ?? $user->username) }}</span>
        </a>
    </div>
</header>

<header class="tw-mobile-nav">
    <button type="button" class="whm-mobile-toggle" data-whm-toggle>
        <i data-lucide="menu"></i>
    </button>
    <a href="{{ route('user.home') }}" class="tw-brand">
        <span class="tw-brand-mark"><i data-lucide="server"></i></span>
        <span class="font-black text-ink">{{ gs('site_name') }}</span>
    </a>
    @include($activeTemplate . 'partials.cart_widget')
</header>

<div class="whm-sidebar-overlay" data-whm-overlay></div>

<aside class="whm-app-sidebar whm-client-sidebar" data-whm-sidebar>
    <div class="whm-sidebar-brand">
        <a href="{{ route('user.home') }}" class="whm-brand">
            <span class="whm-brand-mark"><i data-lucide="server"></i></span>
            <span>
                <strong>{{ gs('site_name') }}</strong>
                <small>@lang('Client Area')</small>
            </span>
        </a>
    </div>
    <nav class="whm-sidebar-nav">
        <div class="whm-nav-group">
            <p>@lang('Overview')</p>
            <a href="{{ route('user.home') }}" class="whm-nav-item {{ request()->routeIs('user.home') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i><span>@lang('Dashboard')</span>
            </a>
            <a href="{{ route('user.transactions') }}" class="whm-nav-item {{ request()->routeIs('user.transactions') ? 'active' : '' }}">
                <i data-lucide="activity"></i><span>@lang('Transactions')</span>
            </a>
        </div>
        <div class="whm-nav-group">
            <p>@lang('Services')</p>
            <a href="{{ route('user.service.list') }}" class="whm-nav-item {{ request()->routeIs('user.service.*') ? 'active' : '' }}">
                <i data-lucide="hard-drive"></i><span>@lang('My Services')</span>
            </a>
            @foreach($sidebarServiceGroups as $group)
                <a href="{{ route('user.service.list', ['service' => $group['key']]) }}" class="whm-nav-item whm-nav-subitem {{ request('service') === $group['key'] ? 'active' : '' }}">
                    <i data-lucide="{{ $group['key'] === 'vps' ? 'server-cog' : ($group['key'] === 'mail' ? 'mail' : 'box') }}"></i>
                    <span>{{ __($group['label']) }}</span>
                    <b>{{ $group['count'] }}</b>
                </a>
            @endforeach
            <a href="{{ route('service.category') }}?all" class="whm-nav-item">
                <i data-lucide="plus-circle"></i><span>@lang('Order New Service')</span>
            </a>
            <a href="{{ route('user.domain.list') }}" class="whm-nav-item {{ request()->routeIs('user.domain.*') ? 'active' : '' }}">
                <i data-lucide="globe"></i><span>@lang('My Domains')</span>
            </a>
            <a href="{{ route('register.domain') }}" class="whm-nav-item">
                <i data-lucide="search"></i><span>@lang('Domain Search')</span>
            </a>
        </div>
        <div class="whm-nav-group">
            <p>@lang('Billing')</p>
            <a href="{{ route('user.invoice.list') }}" class="whm-nav-item {{ request()->routeIs('user.invoice.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i><span>@lang('Invoices')</span>
            </a>
            @if (gs('deposit_module'))
                <a href="{{ route('user.deposit.index') }}" class="whm-nav-item {{ request()->routeIs('user.deposit.*') ? 'active' : '' }}">
                    <i data-lucide="credit-card"></i><span>@lang('Deposit Money')</span>
                </a>
                <a href="{{ route('user.deposit.history') }}" class="whm-nav-item">
                    <i data-lucide="receipt"></i><span>@lang('Deposit Log')</span>
                </a>
            @endif
        </div>
        <div class="whm-nav-group">
            <p>@lang('Support')</p>
            <a href="{{ route('ticket.open') }}" class="whm-nav-item {{ request()->routeIs('ticket.open') ? 'active' : '' }}">
                <i data-lucide="message-circle-plus"></i><span>@lang('Create Ticket')</span>
            </a>
            <a href="{{ route('ticket.index') }}" class="whm-nav-item {{ request()->routeIs('ticket.index') || request()->routeIs('ticket.view') ? 'active' : '' }}">
                <i data-lucide="messages-square"></i><span>@lang('My Tickets')</span>
            </a>
        </div>
        <div class="whm-nav-group">
            <p>@lang('Account')</p>
            <a href="{{ route('user.profile.setting') }}" class="whm-nav-item {{ request()->routeIs('user.profile.setting') ? 'active' : '' }}">
                <i data-lucide="user-cog"></i><span>@lang('Profile Setting')</span>
            </a>
            <a href="{{ route('user.change.password') }}" class="whm-nav-item {{ request()->routeIs('user.change.password') ? 'active' : '' }}">
                <i data-lucide="key-round"></i><span>@lang('Change Password')</span>
            </a>
            <a href="{{ route('user.twofactor') }}" class="whm-nav-item {{ request()->routeIs('user.twofactor') ? 'active' : '' }}">
                <i data-lucide="shield-check"></i><span>@lang('2FA Security')</span>
            </a>
        </div>
    </nav>
    <div class="whm-sidebar-user">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->fullname ?? $user->username) }}&background=2563eb&color=fff" alt="@lang('User')">
        <div>
            <strong>{{ __($user->fullname ?? $user->username) }}</strong>
            <span>{{ __($user->email) }}</span>
        </div>
        <a href="{{ route('user.logout') }}" title="@lang('Logout')"><i data-lucide="log-out"></i></a>
    </div>
</aside>

<nav class="whm-mobile-bottom-nav d-lg-none" aria-label="@lang('Client navigation')">
    <a href="{{ route('user.home') }}" class="{{ request()->routeIs('user.home') ? 'active' : '' }}">
        <i data-lucide="layout-dashboard"></i>
        <span>@lang('Home')</span>
    </a>
    <a href="{{ route('user.service.list') }}" class="{{ request()->routeIs('user.service.*') ? 'active' : '' }}">
        <i data-lucide="hard-drive"></i>
        <span>@lang('Services')</span>
    </a>
    <a href="{{ route('register.domain') }}">
        <i data-lucide="search"></i>
        <span>@lang('Domains')</span>
    </a>
    <a href="{{ route('user.invoice.list') }}" class="{{ request()->routeIs('user.invoice.*') ? 'active' : '' }}">
        <i data-lucide="file-text"></i>
        <span>@lang('Billing')</span>
    </a>
    <a href="{{ route('ticket.index') }}" class="{{ request()->routeIs('ticket.*') ? 'active' : '' }}">
        <i data-lucide="messages-square"></i>
        <span>@lang('Support')</span>
    </a>
</nav>
