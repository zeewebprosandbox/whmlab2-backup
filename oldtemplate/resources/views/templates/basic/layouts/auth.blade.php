@extends($activeTemplate . 'layouts.app')

@section('app')
<div class="min-h-screen bg-white text-neutral-900 flex flex-col justify-between relative selection:bg-neutral-900 selection:text-white">
    <!-- Subtle top ambient light -->
    <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-neutral-100/60 to-transparent pointer-events-none"></div>

    <!-- Header / Branding Navigation -->
    <header class="relative z-10 w-full max-w-6xl mx-auto px-6 py-8 flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 transition-opacity hover:opacity-85">
            <img src="{{ siteLogo() }}" alt="{{ gs('site_name') }}" class="whm-brand-img" style="max-height: 40px; max-width: 190px; object-fit: contain;">
        </a>

        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-medium text-neutral-500 hover:text-neutral-900 transition-colors">
            <i class="las la-arrow-left text-sm"></i>
            <span>@lang('Back to home')</span>
        </a>
    </header>

    <!-- Main Auth Center Wrapper -->
    <main class="relative z-10 flex-1 flex flex-col justify-center items-center px-6 py-8 sm:py-12">
        <div class="w-full max-w-[480px]">
            @yield('auth')
        </div>
    </main>

    <!-- Clean Footer -->
    <footer class="relative z-10 w-full max-w-6xl mx-auto px-6 py-6 border-t border-neutral-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-neutral-400">
        <div>&copy; {{ date('Y') }} {{ gs('site_name') }}. @lang('All rights reserved.')</div>
        <div class="flex items-center gap-6">
            <div class="inline-flex items-center gap-1.5 text-neutral-500">
                <i class="las la-shield-alt text-sm text-emerald-600"></i>
                <span>@lang('256-bit SSL Encrypted')</span>
            </div>
            <a href="{{ route('ticket.index') }}" class="hover:text-neutral-900 transition-colors">@lang('Support')</a>
        </div>
    </footer>
</div>
@endsection
