@extends($activeTemplate.'layouts.app')

@section('app')    

    @php
        $user = auth()->user();
    @endphp 

    @include($activeTemplate.'partials.auth_header')
    <main class="whm-main-content whm-client-main">
        <header class="whm-topbar d-none d-lg-flex">
            <div>
                <p class="whm-topbar-kicker">@lang('Client Area')</p>
                <h1>{{ __($pageTitle) }}</h1>
            </div>
            <div class="whm-topbar-actions">
                <form action="{{ route('register.domain') }}" method="get" class="whm-topbar-search">
                    <i data-lucide="search"></i>
                    <input type="text" name="domain" placeholder="@lang('Search domains')">
                </form>
                @include($activeTemplate . 'partials.cart_widget')
                <x-language />
                <a href="{{ route('user.profile.setting') }}" class="whm-avatar-link">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->fullname ?? $user->username) }}&background=2563eb&color=fff" alt="@lang('User')">
                </a>
            </div>
        </header>

        @include($activeTemplate.'partials.breadcrumb')

        <div class="service-category bg--light whm-page-body whm-client-page-body">
            <div class="container px-3">
                <div class="row gy-4 justify-content-center">
                    @yield('content')
                </div>
            </div>
        </div>

        @include($activeTemplate.'partials.footer')
    </main>
@endsection 
 
