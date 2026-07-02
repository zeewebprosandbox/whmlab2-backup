<form action="" class="form whm-live-domain-form">
    <div class="form-group position-relative mb-0">
        <div class="domain-search-icon"><i class="fas fa-search"></i></div>
        <input class="form-control form--control whm-live-domain-input" type="text" name="domain" required placeholder="@lang('Domain name or keyword')" value="{{ @request()->domain }}" autocomplete="off">
        <div class="domain-search-icon-reset">
            <button class="btn btn--base" type="submit">@lang('Search')</button>
        </div>
    </div>
    <div class="whm-live-domain-results"></div>
</form>

@push('script')
    <script>
        (function($) {
            "use strict";

            if (window.whmLiveDomainSearchReady) {
                return;
            }

            window.whmLiveDomainSearchReady = true;

            var liveSearchTimers = new WeakMap();
            var liveSearchRequests = new WeakMap();

            function normalizeLiveDomain(domain) {
                return $.trim(domain || '')
                    .toLowerCase()
                    .replace(/^https?:\/\//, '')
                    .replace(/^www\./, '')
                    .replace(/\/.*$/, '');
            }

            function renderLiveRows(form, response) {
                var box = form.find('.whm-live-domain-results');

                if (response && response.result && response.result.deferred) {
                    box.html(`
                        <div class="domain-row whm-domain-result-row mt-3">
                            <span>${response.result.message || '@lang('Please pause briefly or press Search for a full availability check.')'}</span>
                            <small class="whm-domain-status whm-domain-status--available">@lang('Live')</small>
                        </div>
                    `);
                    return;
                }

                var data = response && response.result && response.result.data ? response.result.data : [];
                var rows = Array.isArray(data) ? data : Object.keys(data).map(function(domain) {
                    return {
                        domain: domain,
                        available: data[domain] && data[domain].status === 'available',
                        setup: response.result.setup || null,
                        match: domain === response.result.domain ? 999 : 0
                    };
                });

                rows = rows
                    .filter(function(row) {
                        return row.available && row.setup;
                    })
                    .sort(function(a, b) {
                        return (b.match || 0) - (a.match || 0);
                    })
                    .slice(0, 6);

                box.empty();

                if (!rows.length) {
                    box.html(`
                        <div class="domain-row whm-domain-result-row mt-3">
                            <span>@lang('No available configured extensions found yet.')</span>
                            <small class="whm-domain-status whm-domain-status--unavailable">@lang('Try another name')</small>
                        </div>
                    `);
                    return;
                }

                box.append(`<p class="whm-domain-suggestion-title mt-3 mb-2">@lang('Available domain suggestions')</p>`);

                rows.forEach(function(row) {
                    var priceValue = row.setup && row.setup.pricing && row.setup.pricing.firstPrice ? row.setup.pricing.firstPrice.price : 0;
                    var price = parseFloat(priceValue || 0).toFixed(2);
                    var url = "{{ route('register.domain') }}?domain=" + encodeURIComponent(row.domain);
                    var currencySymbol = window.general && general.cur_sym ? general.cur_sym : '';

                    box.append(`
                        <div class="domain-row whm-domain-result-row">
                            <span>${row.domain}</span>
                            <div class="text-end">
                                <small class="whm-domain-status whm-domain-status--available">@lang('Available')</small>
                                <strong class="fw-bold">${currencySymbol}${price}</strong>
                                <a href="${url}" class="btn btn--sm btn--base-outline ms-2">@lang('View')</a>
                            </div>
                        </div>
                    `);
                });
            }

            function runLiveSearch(form, domain) {
                var box = form.find('.whm-live-domain-results');
                var activeRequest = liveSearchRequests.get(form[0]);

                if (activeRequest) {
                    activeRequest.abort();
                }

                box.html(`
                    <div class="domain-row whm-domain-result-row mt-3">
                        <span>@lang('Checking available extensions')...</span>
                        <small class="whm-domain-status whm-domain-status--available">@lang('Live')</small>
                    </div>
                `);

                var request = $.ajax({
                    url: "{{ route('search.domain') }}",
                    data: { domain: domain, live: 1 },
                    success: function(response) {
                        if (!response.success) {
                            box.empty();
                            return;
                        }

                        renderLiveRows(form, response);
                    },
                    error: function(xhr) {
                        if (xhr.statusText === 'abort') {
                            return;
                        }

                        box.empty();
                    },
                    complete: function() {
                        if (liveSearchRequests.get(form[0]) === request) {
                            liveSearchRequests.delete(form[0]);
                        }
                    }
                });

                liveSearchRequests.set(form[0], request);
            }

            $(document).on('input', '.whm-live-domain-input', function() {
                var input = $(this);
                var form = input.closest('.whm-live-domain-form');
                var domain = normalizeLiveDomain(input.val());
                var oldTimer = liveSearchTimers.get(form[0]);

                if (oldTimer) {
                    clearTimeout(oldTimer);
                }

                if (domain.length < 2) {
                    form.find('.whm-live-domain-results').empty();
                    return;
                }

                var timer = setTimeout(function() {
                    runLiveSearch(form, domain);
                }, 450);

                liveSearchTimers.set(form[0], timer);
            });

            $(document).on('submit', '.whm-live-domain-form', function(e) {
                e.preventDefault();
                var domain = normalizeLiveDomain($(this).find('input[name=domain]').val());
                if (!domain) {
                    return;
                }

                window.location.href = "{{ route('register.domain') }}?domain=" + encodeURIComponent(domain);
            });

            $('.whm-live-domain-input').each(function() {
                var input = $(this);
                if (normalizeLiveDomain(input.val()).length >= 2) {
                    input.trigger('input');
                }
            });
        })(jQuery);
    </script>
@endpush
