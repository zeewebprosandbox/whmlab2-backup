@php
    $title = __($pageTitle ?? gs('site_name'));
@endphp

<section class="breadcrumb-bg whm-breadcrumb" aria-label="@lang('Page header')">
    <div class="container whm-breadcrumb-inner">
        <div class="whm-breadcrumb-copy">
            <span class="whm-breadcrumb-kicker">@lang('ZodHost Workspace')</span>
            <h2>{{ $title }}</h2>
            <nav class="whm-breadcrumb-trail" aria-label="@lang('Breadcrumb')">
                <a href="{{ route('home') }}">@lang('Home')</a>
                <i data-lucide="chevron-right" aria-hidden="true"></i>
                <span>{{ $title }}</span>
            </nav>
        </div>
        <a href="{{ route('register.domain') }}" class="whm-breadcrumb-action">
            <i data-lucide="search" aria-hidden="true"></i>
            @lang('Find a domain')
        </a>
    </div>
</section>
