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
                <form action="{{ route('register.domain') }}" method="get" class="whm-global-search">
                    <i data-lucide="search"></i>
                    <input type="text" name="domain" placeholder="@lang('Search domains...')">
                </form>
                @include($activeTemplate . 'partials.cart_widget')
                <x-language />
                <a href="{{ route('user.profile.setting') }}" class="whm-avatar-link">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->fullname ?? $user->username) }}&background=4f46e5&color=fff" alt="@lang('User')">
                </a>
            </div>
        </header>

        <div class="service-category whm-page-body whm-client-page-body py-4">
            <div class="container-fluid px-3 px-xl-4" style="max-width: 1440px; margin: 0 auto;">
                <div class="row gy-4">
                    @yield('content')
                </div>
            </div>
        </div>

        @include($activeTemplate.'partials.footer')
    </main>
@endsection
