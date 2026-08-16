@php
    $pages = App\Models\Page::where('tempname', $activeTemplate)->where('is_default', 0)->get();
@endphp

<header class="whm-desktop-nav d-none d-lg-flex">
    <a href="{{ route('home') }}" class="whm-brand">
        <span class="whm-brand-mark"><i data-lucide="server"></i></span>
        <span>
            <strong>{{ gs('site_name') }}</strong>
            <small>@lang('Hosting & Domains')</small>
        </span>
    </a>

    <nav class="whm-desktop-menu">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">@lang('Home')</a>
        <div class="whm-menu-dropdown">
            <button type="button">@lang('Hosting') <i data-lucide="chevron-down"></i></button>
            <div class="whm-dropdown-panel whm-dropdown-panel--wide">
                <div class="whm-dropdown-intro">
                    <strong>@lang('Explore packages')</strong>
                    <span>@lang('Pick a service class and compare plans clearly.')</span>
                </div>
                <div class="whm-dropdown-grid">
                    @foreach ($serviceCategories as $serviceCategory)
                        <a href="{{ route('service.category', $serviceCategory->slug) }}">
                            <i data-lucide="{{ str_contains($serviceCategory->slug, 'vps') || str_contains($serviceCategory->slug, 'dedicated') ? 'database' : 'server' }}"></i>
                            <span>{{ __($serviceCategory->name) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <a href="{{ route('register.domain') }}" class="{{ request()->routeIs('register.domain') ? 'active' : '' }}">@lang('Domains')</a>
        <a href="{{ route('blogs') }}" class="{{ request()->routeIs('blogs*') ? 'active' : '' }}">@lang('Announcements')</a>
        <div class="whm-menu-dropdown">
            <button type="button">@lang('Company') <i data-lucide="chevron-down"></i></button>
            <div class="whm-dropdown-panel">
                @foreach ($pages as $data)
                    <a href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a>
                @endforeach
                <a href="{{ route('contact') }}">@lang('Contact')</a>
            </div>
        </div>
    </nav>

    <div class="whm-desktop-actions">
        @include($activeTemplate . 'partials.cart_widget')
        <x-language />
        @auth
            <a href="{{ route('user.home') }}" class="whm-login-btn">@lang('Client Area')</a>
        @else
            <a href="{{ route('user.login') }}" class="whm-login-btn">@lang('Login')</a>
        @endauth
    </div>
</header>

<header class="whm-mobile-header d-lg-none">
    <button type="button" class="whm-mobile-toggle" data-whm-toggle>
        <i data-lucide="menu"></i>
    </button>
    <a href="{{ route('home') }}" class="whm-mobile-brand">
        <span class="whm-brand-mark"><i data-lucide="server"></i></span>
        <span>{{ gs('site_name') }}</span>
    </a>
    @include($activeTemplate . 'partials.cart_widget')
</header>

<div class="whm-sidebar-overlay" data-whm-overlay></div>

<aside class="whm-app-sidebar" data-whm-sidebar>
    <div class="whm-sidebar-brand">
        <a href="{{ route('home') }}" class="whm-brand">
            <span class="whm-brand-mark"><i data-lucide="server"></i></span>
            <span>
                <strong>{{ gs('site_name') }}</strong>
                <small>@lang('Billing & Management')</small>
            </span>
        </a>
    </div>
    <nav class="whm-sidebar-nav">
        <div class="whm-nav-group">
            <p>@lang('Overview')</p>
            <a href="{{ route('home') }}" class="whm-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i><span>@lang('Dashboard')</span>
            </a>
            <a href="{{ route('blogs') }}" class="whm-nav-item {{ request()->routeIs('blogs*') ? 'active' : '' }}">
                <i data-lucide="newspaper"></i><span>@lang('Announcements')</span>
            </a>
        </div>

        <div class="whm-nav-group">
            <p>@lang('Services')</p>
            @if (@$serviceCategories->first())
                <a href="{{ route('service.category', [@$serviceCategories->first()->slug, 'all=']) }}" class="whm-nav-item {{ request()->routeIs('service.category') && !request()->slug ? 'active' : '' }}">
                    <i data-lucide="hard-drive"></i><span>@lang('All Packages')</span>
                </a>
            @endif
            @foreach ($serviceCategories as $serviceCategory)
                <a href="{{ route('service.category', $serviceCategory->slug) }}" class="whm-nav-item {{ request()->is('store/'.$serviceCategory->slug.'*') ? 'active' : '' }}">
                    <i data-lucide="server"></i><span>{{ __($serviceCategory->name) }}</span>
                </a>
            @endforeach
        </div>

        <div class="whm-nav-group">
            <p>@lang('Domains & Billing')</p>
            <a href="{{ route('register.domain') }}" class="whm-nav-item {{ request()->routeIs('register.domain') ? 'active' : '' }}">
                <i data-lucide="globe"></i><span>@lang('Register Domain')</span>
            </a>
            <a href="{{ route('shopping.cart') }}" class="whm-nav-item {{ request()->routeIs('shopping.cart*') ? 'active' : '' }}">
                <i data-lucide="shopping-cart"></i><span>@lang('View Cart')</span>
            </a>
        </div>

        <div class="whm-nav-group">
            <p>@lang('Account')</p>
            @auth
                <a href="{{ route('user.home') }}" class="whm-nav-item">
                    <i data-lucide="user-circle"></i><span>@lang('Client Area')</span>
                </a>
                <a href="{{ route('user.logout') }}" class="whm-nav-item">
                    <i data-lucide="log-out"></i><span>@lang('Logout')</span>
                </a>
            @else
                <a href="{{ route('user.login') }}" class="whm-nav-item {{ request()->routeIs('user.login') ? 'active' : '' }}">
                    <i data-lucide="log-in"></i><span>@lang('Login')</span>
                </a>
                <a href="{{ route('user.register') }}" class="whm-nav-item {{ request()->routeIs('user.register') ? 'active' : '' }}">
                    <i data-lucide="user-plus"></i><span>@lang('Register')</span>
                </a>
            @endauth
            @foreach ($pages as $data)
                <a href="{{ route('pages', [$data->slug]) }}" class="whm-nav-item {{ request()->is($data->slug) ? 'active' : '' }}">
                    <i data-lucide="file-text"></i><span>{{ __($data->name) }}</span>
                </a>
            @endforeach
            <a href="{{ route('contact') }}" class="whm-nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                <i data-lucide="messages-square"></i><span>@lang('Contact')</span>
            </a>
        </div>
    </nav>
</aside>
