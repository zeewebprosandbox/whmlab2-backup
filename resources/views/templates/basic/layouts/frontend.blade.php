@extends($activeTemplate.'layouts.app')

@section('app')

    @stack('fbComment')
    
    @include($activeTemplate.'partials.header')

    <main class="whm-frontend-main">
        @if (!request()->routeIs('home'))
            @include($activeTemplate.'partials.breadcrumb')
        @endif

        @yield('content')

        @include($activeTemplate.'partials.footer')

        @include($activeTemplate.'partials.subscribe')
    
        <x-cookie-policy />
    </main>
@endsection
