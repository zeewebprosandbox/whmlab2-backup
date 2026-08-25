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
                <form action="{{ route('register.domain') }}" method="get" class="whm-topbar-search relative flex items-center">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 pointer-events-none"></i>
                    <input type="text" name="domain" placeholder="@lang('Search domains')" class="pl-8.5 pr-3 py-1.5 bg-slate-100/80 border border-slate-200/80 rounded-full text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all w-52 sm:w-64">
                </form>
                @include($activeTemplate . 'partials.cart_widget')
                <x-language />
                <a href="{{ route('user.profile.setting') }}" class="whm-avatar-link">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->fullname ?? $user->username) }}&background=4f46e5&color=fff" alt="@lang('User')">
                </a>
            </div>
        </header>

        @include($activeTemplate.'partials.breadcrumb')

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
