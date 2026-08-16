<!doctype html>
<html lang="{{ config('app.locale') }}" class="dark" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}" />

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" />
    @vite('resources/css/app.css')

    <style>
        body, button, input, select, textarea, h1, h2, h3, h4, h5, h6, .nav-link, .btn, .card, .menu-title {
            font-family: 'Barlow Condensed', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }
        code, pre, .font-mono, .ip-address, .domain-name {
            font-family: 'JetBrains Mono', monospace !important;
        }
    </style>

    @stack('style-lib')
    @stack('style')

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/color.php') }}?color={{ gs('base_color') }}">
</head>

@php echo loadExtension('google-analytics') @endphp
<body class="tw-page-shell">

    <!-- Overlay -->
    <div class="overlay"></div>

    <div class="preloader" aria-label="@lang('Loading')" role="status">
        <div class="whm-preloader-card">
            <span class="whm-preloader-mark">
                <i data-lucide="server"></i>
            </span>
            <div>
                <strong>{{ gs('site_name') }}</strong>
                <small>@lang('Preparing hosting workspace')</small>
            </div>
            <span class="whm-preloader-line"></span>
        </div>
    </div>

    @yield('app')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.validate.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')

    @if(!auth()->check() || gs('show_livechat_user_panel'))
        @php echo loadExtension('tawk-chat') @endphp
    @endif
    
    @include('partials.notify')

    @if(gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

    <script>
        (function($) {
            "use strict";

            var currentUrl = '{{ url()->full() }}';

            $('.menu a[href="' + currentUrl + '"]').addClass('active');
            $('.menu .sub-menu a[href="' + currentUrl + '"]').closest('a').addClass('active');
            $('.menu .sub-menu a[href="' + currentUrl + '"]').parents('.has-sub-menu').find('a').eq(0).addClass('active')

            if ($('.navbar-nav .dropdown-menu a[href="' + currentUrl + '"]').length || "{{ @request()->routeIs('service.category') }}") {
                $('#navbarDropdown').addClass('active');
            }

            var inputElements = $('[type=text],select,textarea');
            $.each(inputElements, function (index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for',element.attr('name'));
                element.attr('id',element.attr('name'))
            });

            $.each($('input, select, textarea'), function (i, element) {
                var elementType = $(element);
                if(elementType.attr('type') != 'checkbox'){
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }

            });

            let disableSubmission = false;
            $('.disableSubmission').on('submit',function(e){
                if (disableSubmission) {
                e.preventDefault()
                }else{
                disableSubmission = true;
                }
            });

            $(document).on('submit', 'form:not(.form):not(.exclude)', function () {
                var submitButton = $(this).find('button[type=submit], input[type=submit]').first();
                if (!submitButton.length || submitButton.hasClass('whm-btn-loading')) {
                    return;
                }

                submitButton.addClass('whm-btn-loading');
                if (submitButton.is('button')) {
                    submitButton.data('label', submitButton.html());
                    submitButton.html('<span class="whm-btn-spinner"></span><span>' + '@lang('Please wait')' + '</span>');
                }
            });

            if (window.lucide) {
                window.lucide.createIcons();
            }

            $('[data-whm-toggle]').on('click', function () {
                $('[data-whm-sidebar], [data-whm-overlay]').toggleClass('is-open');
            });

            $('[data-whm-overlay]').on('click', function () {
                $('[data-whm-sidebar], [data-whm-overlay]').removeClass('is-open');
            });

            function enhanceFormFields() {
                $('.form-group').each(function () {
                    var group = $(this);
                    var field = group.find('input:not([type=hidden]):not([type=checkbox]):not([type=radio]), textarea, select').first();
                    var label = group.children('label').first();

                    if (!field.length || !label.length || label.attr('role') === 'button') {
                        return;
                    }

                    var labelText = $.trim(label.clone().children().remove().end().text()).replace(/\s*\*$/, '');
                    if (!labelText) {
                        return;
                    }

                    field.attr('aria-label', labelText);

                    if (!field.attr('placeholder') && !field.is('select')) {
                        field.attr('placeholder', labelText);
                    }

                    label.addClass('whm-field-label-hidden');
                    group.addClass('whm-field-modern');
                });
            }

            enhanceFormFields();
        })(jQuery);
    </script>

</body>

</html>
