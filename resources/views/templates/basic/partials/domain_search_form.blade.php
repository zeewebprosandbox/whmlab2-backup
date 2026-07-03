<form action="" class="form whm-domain-search-form">
    <div class="form-group position-relative mb-0">
        <div class="domain-search-icon"><i class="fas fa-search"></i></div>
        <input class="form-control form--control" type="text" name="domain" required placeholder="@lang('Domain name or keyword')" value="{{ @request()->domain }}" autocomplete="off">
        <div class="domain-search-icon-reset">
            <button class="btn btn--base" type="submit">@lang('Search')</button>
        </div>
    </div>
</form>

@push('script')
    <script>
        (function($) {
            "use strict";

            function normalizeSearchDomain(domain) {
                return $.trim(domain || '')
                    .toLowerCase()
                    .replace(/^https?:\/\//, '')
                    .replace(/^www\./, '')
                    .replace(/\/.*$/, '');
            }

            $(document).on('submit', '.whm-domain-search-form', function(e) {
                e.preventDefault();
                var domain = normalizeSearchDomain($(this).find('input[name=domain]').val());
                if (!domain) {
                    return;
                }

                window.location.href = "{{ route('register.domain') }}?domain=" + encodeURIComponent(domain);
            });
        })(jQuery);
    </script>
@endpush
