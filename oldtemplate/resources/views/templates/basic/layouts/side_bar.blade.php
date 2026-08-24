@extends($activeTemplate . 'layouts.frontend')

@section('content')
<div class="w-full py-8">
    <div class="w-full max-w-[1560px] mx-auto px-4 sm:px-6 lg:px-8">  
        @yield('data')
    </div>
</div>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {
            $('#categoryMenu a[data-slug="{{ @$serviceCategory->slug }}"]').addClass('bg--base text-white');
            $('#actionMenu a[href="{{ url()->current() }}"]').addClass('bg--base text-white');
        })(jQuery);
    </script>
@endpush
