@extends($activeTemplate . 'layouts.app')

@section('app')
    <section class="whm-auth-shell">
        <div class="whm-auth-simple">
            <a href="{{ route('home') }}" class="whm-brand">
                <span class="whm-brand-mark"><i data-lucide="server"></i></span>
                <span>
                    <strong>{{ gs('site_name') }}</strong>
                    <small>@lang('Client Area')</small>
                </span>
            </a>
            <div class="whm-auth-content">
                <div class="@if (request()->routeIs('user.register')) whm-auth-card whm-auth-card-wide @else whm-auth-card @endif">
                    @yield('auth')
                </div>
            </div>
        </div>
    </section>
@endsection
