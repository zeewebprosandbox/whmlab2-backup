@extends($activeTemplate . 'layouts.frontend')

@section('content')
<div class="col-12 service-category bg--light section-full">
    <div class="container px-3">  
        <div class="row gy-2"> 
            @yield('data')

        </div>
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
