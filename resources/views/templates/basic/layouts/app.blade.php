<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}" />

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" />
    @vite('resources/css/app.css')

    <style>
        :root {
            --font-family-base: 'Barlow Condensed', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        /* 1. Global Barlow Condensed Uniform Font */
        *, html, body, button, input, select, textarea, .nav-link, .btn, .card, .menu-title, p, span, div, a, li, label, table, th, td, h1, h2, h3, h4, h5, h6, .whm-brand strong, .tw-brand-title, .tw-heading-xl, .tw-heading-lg, .whm-client-sidebar, .whm-app-sidebar, .whm-sidebar-nav, .whm-nav-item, .whm-nav-subitem, .whm-nav-group p {
            font-family: var(--font-family-base) !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }

        /* 2. Balanced 50% Reduced Font Weights (Light, Clean, Subtle Aesthetic) */
        h1, h2, h3, h4, h5, h6, .font-display, .font-bold, .font-extrabold {
            font-weight: 500 !important;
            letter-spacing: 0.01em !important;
        }

        .whm-topbar h1,
        .whm-service-page-head h3,
        .font-semibold {
            font-weight: 500 !important;
        }

        body, p, span, div, a, li, label, td, input, select, textarea {
            font-weight: 400 !important;
            letter-spacing: 0.015em !important;
        }

        .whm-nav-item,
        .whm-nav-subitem {
            font-weight: 400 !important;
            letter-spacing: 0.02em !important;
        }

        .whm-nav-group p {
            font-weight: 500 !important;
            letter-spacing: 0.08em !important;
        }

        code, pre, .font-mono, .ip-address, .domain-name, [data-mono] {
            font-family: var(--font-mono) !important;
            font-weight: 400 !important;
        }

        /* 3. Improved Button Heights & Proportions Across All Pages */
        .btn,
        button:not([data-bs-dismiss]):not(.modal-close),
        a.btn,
        .whm-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            letter-spacing: 0.02em !important;
            transition: all 0.15s ease !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        }

        /* Primary/Regular Buttons */
        .btn-primary,
        .btn--base,
        .btn-base,
        a.bg-indigo-600,
        button.bg-indigo-600,
        a.bg-slate-900,
        button.bg-slate-900 {
            height: 34px !important;
            min-height: 34px !important;
            padding: 0 14px !important;
            font-size: 13px !important;
            line-height: 34px !important;
        }

        /* Small Action Buttons (Manage, Control Panel, View, Pay Now, Statements) */
        .btn-sm,
        .btn--sm,
        a.px-2\.5,
        a.px-3,
        button.px-2\.5,
        button.px-3 {
            height: 28px !important;
            min-height: 28px !important;
            padding: 0 10px !important;
            font-size: 11.5px !important;
            line-height: 28px !important;
            border-radius: 6px !important;
        }

        .btn svg,
        .btn i,
        a.btn svg,
        button svg {
            width: 13px !important;
            height: 13px !important;
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
        <div class="whm-preloader-wrapper">
            <div class="whm-preloader-spinner-container">
                <svg class="whm-preloader-spinner" viewBox="0 0 50 50">
                    <circle class="whm-spinner-track" cx="25" cy="25" r="20" fill="none" stroke-width="2.5"></circle>
                    <circle class="whm-spinner-circle" cx="25" cy="25" r="20" fill="none" stroke-width="2.5"></circle>
                </svg>
                <div class="whm-preloader-favicon-wrap">
                    <img src="{{ siteFavicon() }}" alt="{{ gs('site_name') }}" class="whm-preloader-favicon" onerror="this.src='{{ siteLogo() }}'">
                </div>
            </div>
            <div class="whm-preloader-caption">
                <span>{{ gs('site_name') }}</span>
            </div>
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
