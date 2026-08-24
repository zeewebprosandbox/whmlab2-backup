@extends($activeTemplate.'layouts.app')

@section('app')

    @stack('fbComment')
    
    @include($activeTemplate.'partials.header')

    <main class="whm-main-content">
        <header class="whm-topbar d-none d-lg-flex">
            <div>
                <p class="whm-topbar-kicker">@lang('Hosting Billing & Management')</p>
                <h1>{{ __($pageTitle) }}</h1>
            </div>
            <div class="whm-topbar-actions">
                <form action="{{ route('register.domain') }}" method="get" class="whm-topbar-search">
                    <i data-lucide="search"></i>
                    <input type="text" name="domain" placeholder="@lang('Search domains or services')">
                </form>
                @include($activeTemplate . 'partials.cart_widget')
                <x-language />
                @auth
                    <a href="{{ route('user.home') }}" class="whm-avatar-link">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->fullname ?? auth()->user()->username) }}&background=2563eb&color=fff" alt="@lang('User')">
                    </a>
                @else
                    <a href="{{ route('user.login') }}" class="whm-login-btn">@lang('Login')</a>
                @endauth
            </div>
        </header>

        @if (!request()->routeIs('home'))
            @include($activeTemplate.'partials.breadcrumb')
        @endif

        @yield('content')

        @include($activeTemplate.'partials.footer')

        @include($activeTemplate.'partials.subscribe')
    
        <x-cookie-policy />
    </main>
@endsection 
 
