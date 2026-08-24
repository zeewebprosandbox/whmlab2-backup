@extends($activeTemplate . 'layouts.app')

@section('app')
    <section class="whm-auth-shell">
        <div class="whm-auth-brand-panel">
            <a href="{{ route('home') }}" class="whm-brand">
                <span class="whm-brand-mark"><i data-lucide="server"></i></span>
                <span>
                    <strong>{{ gs('site_name') }}</strong>
                    <small>@lang('Billing & Management')</small>
                </span>
            </a>
            <div>
                <p class="whm-auth-kicker">@lang('Hosting client portal')</p>
                <h1>@lang('Manage hosting, domains, invoices, and support from one clean workspace.')</h1>
            </div>
        </div>
        <div class="whm-auth-content">
            <div class="@if (request()->routeIs('user.register')) whm-auth-card whm-auth-card-wide @else whm-auth-card @endif">
                @yield('auth')
            </div>
        </div>
    </section>
@endsection
