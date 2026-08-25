@php
    $footer = @getContent('footer.content', true);
    $policyPages = @getContent('policy_pages.element', orderById:true);
    $footerCategories = App\Models\ServiceCategory::active()->take(5)->get(['name', 'slug']);
@endphp

<footer class="footer whm-footer">
    <div class="whm-footer-grid">
        <div class="whm-footer-brand">
            <a href="{{ route('home') }}" class="whm-brand">
                <img src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}" class="whm-brand-img" style="max-height: 38px; max-width: 170px; object-fit: contain;">
            </a>
            <p>{{ __(@$footer->data_values->description) ?: __('Clean hosting billing, domains, invoices, and client services in one workspace.') }}</p>
        </div>

        <div>
            <h6>@lang('Hosting')</h6>
            @foreach($footerCategories as $category)
                <a href="{{ route('service.category', $category->slug) }}">{{ __($category->name) }}</a>
            @endforeach
        </div>

        <div>
            <h6>@lang('Domains')</h6>
            <a href="{{ route('register.domain') }}">@lang('Register Domain')</a>
            <a href="{{ route('register.domain') }}">@lang('Domain Search')</a>
            <a href="{{ route('shopping.cart') }}">@lang('View Cart')</a>
        </div>

        <div>
            <h6>@lang('Client')</h6>
            @auth
                <a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                <a href="{{ route('user.service.list') }}">@lang('My Services')</a>
                <a href="{{ route('user.invoice.list') }}">@lang('Invoices')</a>
                <a href="{{ route('ticket.index') }}">@lang('Support Tickets')</a>
            @else
                <a href="{{ route('user.login') }}">@lang('Login')</a>
                <a href="{{ route('user.register') }}">@lang('Register')</a>
            @endauth
        </div>

        <div>
            <h6>@lang('Company')</h6>
            <a href="{{ route('home') }}">@lang('Home')</a>
            <a href="{{ route('blogs') }}">@lang('Announcements')</a>
            <a href="{{ route('contact') }}">@lang('Contact')</a>
            @foreach($policyPages->take(3) as $policyPage)
                <a href="{{ route('policy.pages', ['slug'=>slug($policyPage->data_values->title)]) }}">
                    {{ __(@$policyPage->data_values->title) }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="whm-footer-bottom">
        <span>{{ gs('site_name') }} &copy; {{ date('Y') }}. @lang('All Rights Reserved')</span>
        <span>@lang('Built for simple hosting management')</span>
    </div>
</footer>
