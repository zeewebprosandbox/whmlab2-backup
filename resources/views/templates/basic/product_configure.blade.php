@extends($activeTemplate . 'layouts.side_bar')

@section('data')
<div class="col-12 max-w-6xl mx-auto py-6 font-sans">

    @if ($product->stock_control && $product->stock_quantity <= 0)
        <div class="p-8 bg-amber-50 border border-amber-200 rounded-2xl text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 mx-auto">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
            <h4 class="text-base font-bold text-slate-900 font-display">@lang('Product Currently Out of Stock')</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">@lang('We are scaling capacity for this node. Please check back shortly or browse other available plans.')</p>
            <a href="{{ route('service.category', $product->serviceCategory->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm">
                @lang('Browse Other Plans')
            </a>
        </div>
    @else

        {{-- ── STEP 1: DOMAIN SEARCH & OWNERSHIP SELECTION ─────────── --}}
        @if ($product->domain_register && !@$cart)
            <div class="domainArea space-y-6">

                <!-- Header Card & Search Console -->
                <div class="p-6 bg-white border border-slate-200/80 rounded-2xl shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold">
                                <span class="w-2 h-2 rounded-full bg-indigo-600 orb-pulse"></span>
                                @lang('Step 1 of 2 • Choose a Domain')
                            </div>
                            <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight font-display">
                                @lang('Connect a domain to') <span class="text-indigo-600">{{ $product->name }}</span>
                            </h1>
                            <p class="text-xs text-slate-500 max-w-xl">
                                @lang('Search for a new domain to register or connect a domain name you already own.')
                            </p>
                        </div>
                    </div>

                    <!-- Flow Tabs Switcher -->
                    <div class="flex items-center gap-2 border-b border-slate-200 pb-1 overflow-x-auto text-xs font-semibold">
                        <button type="button" class="domain-tab-btn px-4 py-2.5 rounded-lg bg-indigo-600 text-white shadow-xs transition-all flex items-center gap-2 whitespace-nowrap" data-target="tab-register">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            <span>@lang('Register New Domain')</span>
                        </button>
                        <button type="button" class="domain-tab-btn px-4 py-2.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 transition-all flex items-center gap-2 whitespace-nowrap" data-target="tab-owned">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                            <span>@lang('I Already Own a Domain')</span>
                        </button>
                        <button type="button" class="domain-tab-btn px-4 py-2.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 transition-all flex items-center gap-2 whitespace-nowrap" data-target="tab-transfer">
                            <i data-lucide="arrow-left-right" class="w-4 h-4 text-cyan-600"></i>
                            <span>@lang('Transfer Domain')</span>
                        </button>
                    </div>

                    <!-- Panel 1: Register New Domain Search -->
                    <div id="tab-register" class="domain-tab-pane space-y-5">
                        <form action="" class="domain_lookup_form form exclude">
                            <div class="flex flex-col sm:flex-row items-stretch gap-2">
                                <div class="relative flex-1">
                                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                                    <input type="text" name="domain" class="domain_lookup_input w-full bg-white border border-slate-300 rounded-xl pl-11 pr-4 py-3 text-slate-900 font-mono text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition-all" required placeholder="@lang('Type domain or keyword (e.g. shpayco.com, mybrand.store, brand.cloud)')" autocomplete="off">
                                </div>
                                <button type="submit" class="exclude px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                    <span>@lang('Search Domain')</span>
                                </button>
                            </div>
                        </form>

                        <!-- Search Result Dynamic Container -->
                        <div class="availability space-y-4"></div>
                        <div class="showAvailability space-y-4"></div>
                    </div>

                    <!-- Panel 2: I Already Own a Domain -->
                    <div id="tab-owned" class="domain-tab-pane hidden space-y-5">
                        <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-xl space-y-3">
                            <div class="flex items-start gap-3">
                                <i data-lucide="info" class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5"></i>
                                <div class="text-xs text-slate-600 space-y-1">
                                    <strong class="text-slate-900 font-semibold">@lang('Nameserver Instructions:')</strong>
                                    <p>@lang('Keep your domain at your current registrar. After completing this order, simply point its nameservers to:')</p>
                                    <div class="flex flex-wrap gap-2 pt-1 font-mono text-[11px] text-indigo-700">
                                        <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-md font-bold">{{ @$nameservers[0]['host'] ?: 'ns1.zodserver.cloud' }}</span>
                                        <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-md font-bold">{{ @$nameservers[1]['host'] ?: 'ns2.zodserver.cloud' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-xs font-bold text-slate-700">@lang('Enter your existing domain name')</label>
                            <div class="relative">
                                <i data-lucide="globe-2" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                                <input type="text" id="owned_domain_input" class="w-full bg-white border border-slate-300 rounded-xl pl-11 pr-4 py-3 text-slate-900 font-mono text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition-all" placeholder="example.com">
                            </div>

                            <label class="flex items-center gap-2 cursor-pointer pt-1">
                                <input type="checkbox" id="owned_domain_check" class="rounded border-slate-300 text-indigo-600 focus:ring-0">
                                <span class="text-xs text-slate-600">@lang('I confirm I own this domain and will update its nameservers after checkout.')</span>
                            </label>

                            <button type="button" id="btn_confirm_owned_domain" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>@lang('Use This Domain & Continue to Options →')</span>
                            </button>
                        </div>
                    </div>

                    <!-- Panel 3: Transfer Domain -->
                    <div id="tab-transfer" class="domain-tab-pane hidden space-y-4">
                        <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-600">
                            <p>@lang('Transfer your domain management to ZodHost to manage your hosting and domain under one single dashboard.')</p>
                        </div>

                        <div class="space-y-3">
                            <label class="text-xs font-bold text-slate-700">@lang('Domain to transfer')</label>
                            <input type="text" id="transfer_domain_input" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-900 font-mono text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-600" placeholder="mydomain.com">

                            <label class="text-xs font-bold text-slate-700">@lang('EPP / Authorization Code (Optional)')</label>
                            <input type="text" id="transfer_epp_input" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-900 font-mono text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-600" placeholder="Auth code from current registrar">

                            <button type="button" id="btn_confirm_transfer_domain" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                <span>@lang('Transfer & Continue to Options →')</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        {{-- ── STEP 2: CONFIGURE SERVICE OPTIONS & ORDER SUMMARY ─────── --}}
        <div class="col-12 {{ $product->domain_register ? 'd-none hideElement' : null }} space-y-6">
            @if (!@$isUpdate)
                <form action="{{ route('shopping.cart.add.service') }}" method="post" id="serviceOrderForm">
            @else
                <form action="{{ route('shopping.cart.config.service.update') }}" method="post" id="serviceOrderForm">
                    <input type="hidden" name="cart_id" value="{{ $cart->id }}">
            @endif
            @csrf

            <input type="hidden" name="domain" class="domain">
            <input type="hidden" name="domain_id" value="0" class="domain_id" required>
            <input type="hidden" name="owned_domain_confirmed" value="0" class="owned_domain_confirmed">
            <input type="hidden" name="product_id" value="{{ $product->id }}" required>

            <!-- Selected Domain Status Indicator Bar -->
            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center">
                        <i data-lucide="globe" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 selectedDomainType">@lang('Selected Domain')</div>
                        <div class="text-base font-extrabold font-mono text-slate-900 selectedDomainDisplay">example.com</div>
                    </div>
                </div>
                <button type="button" id="btn_change_domain" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-indigo-600"></i>
                    <span>@lang('Change Domain')</span>
                </button>
            </div>

            <!-- Main 2-Column Grid: Config Options (Left 2 cols) + Order Summary (Right 1 col) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left 2 Columns -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Plan Overview Box -->
                    <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-slate-900 font-display">{{ __($product->name) }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100">
                                {{ $product->serviceCategory->name ?? 'Hosting' }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-600 leading-relaxed">
                            @php echo nl2br($product->description); @endphp
                        </div>
                    </div>

                    <!-- Billing Duration Selector -->
                    @php $price = $product->price; @endphp
                    <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm {{ $product->payment_type == 1 ? 'd-none' : '' }}">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 font-display">@lang('Choose Billing Duration')</h4>
                            <p class="text-xs text-slate-500">@lang('Save more with longer billing terms with instant auto-provisioning.')</p>
                        </div>

                        <select name="billing_cycle" class="d-none whm-billing-select">
                            @php echo pricing($product->payment_type, $price); @endphp
                        </select>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3" data-billing-card-grid>
                            @php $firstDurationCard = true; @endphp

                            @foreach (billingCycle() as $cycle)
                                @continue($cycle['index'] === 0)

                                @php
                                    $column = $cycle['billing_cycle'];
                                    $setupColumn = "{$column}_setup_fee";
                                    $cyclePrice = $price->{$column} ?? -1;
                                @endphp

                                @if ($cyclePrice >= 0)
                                    @php
                                        $setupFee = $price->{$setupColumn} ?? 0;
                                        $isSelected = $firstDurationCard;
                                        $firstDurationCard = false;
                                        $isLongTerm = in_array($column, ['annually', 'biennially', 'triennially']);
                                    @endphp

                                    <button type="button" class="duration-pill p-4 rounded-xl border text-left transition-all relative flex flex-col justify-between space-y-2 {{ $isSelected ? 'border-indigo-600 bg-indigo-50/50 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50' }}" data-cycle="{{ $column }}">
                                        @if ($isLongTerm)
                                            <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-600 text-white">@lang('Best Value')</span>
                                        @endif
                                        <div class="text-xs font-semibold text-slate-600">{{ __($cycle['showText']) }}</div>
                                        <div class="text-xl font-extrabold font-mono text-slate-900">{{ gs('cur_sym') }}{{ getAmount($cyclePrice) }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            @if ($setupFee > 0)
                                                + {{ gs('cur_sym') }}{{ getAmount($setupFee) }} @lang('setup')
                                            @else
                                                @lang('Free Setup')
                                            @endif
                                        </div>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Configurable Options / Addons -->
                    @php $configs = $product->getConfigs; @endphp
                    @if($configs && count($configs))
                        <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm">
                            <h4 class="text-sm font-bold text-slate-900 font-display">@lang('Configurable Addons & Specifications')</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($configs as $config)
                                    @php
                                        $group = $config->activeGroup;
                                        $options = $group ? $group->activeOptions : collect();
                                    @endphp

                                    @foreach ($options->sortBy('order') as $option)
                                        @php $subOptions = $option->activeSubOptions; @endphp
                                        @if (count($subOptions))
                                            <div class="space-y-1.5">
                                                <label class="text-xs font-bold text-slate-700">{{ __($option->name) }}</label>
                                                <select name="config_options[{{ $option->id }}]" class="form-select w-full bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900 options" data-type='' data-name="{{ __($option->name) }}">
                                                    @foreach ($subOptions->sortBy('order') as $subOption)
                                                        <option value="{{ $subOption->id }}" data-price='{{ $subOption->getOnlyPrice }}' data-text='{{ __($subOption->name) }}'>
                                                            {{ __($subOption->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- VPS / Server Specific Credentials -->
                    @if ($product->product_type == 3)
                        <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm">
                            <h4 class="text-sm font-bold text-slate-900 font-display">@lang('Server Instance Settings')</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-700">@lang('Hostname')</label>
                                    <input type="text" name="hostname" class="form-control hostname" placeholder="server1.example.com" required>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-700">@lang('Root Password')</label>
                                    <input type="password" name="password" class="form-control root_password" placeholder="••••••••" required>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-700">@lang('NS1 Prefix')</label>
                                    <input type="text" name="ns1" class="form-control ns1_prefix" placeholder="ns1" required>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-700">@lang('NS2 Prefix')</label>
                                    <input type="text" name="ns2" class="form-control ns2_prefix" placeholder="ns2" required>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right 1 Column: Sticky Order Summary -->
                <div class="space-y-6">
                    <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-5 shadow-sm sticky top-24">
                        <h4 class="text-sm font-bold text-slate-900 font-display border-b border-slate-200 pb-3">@lang('Order Summary')</h4>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600">{{ __($product->name) }}</span>
                                <span class="font-bold font-mono text-slate-900">
                                    {{ gs('cur_sym') }}<span class="basicPrice">{{ pricing($product->payment_type, $price, $type = 'price') }}</span>
                                </span>
                            </div>

                            <div class="configurablePrice space-y-1.5 text-slate-600"></div>

                            <div class="domainOrderLine hidden flex items-center justify-between text-indigo-700 font-semibold py-1 border-t border-dashed border-indigo-100">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                                    <span class="domainOrderLabel">@lang('Domain Reg')</span>
                                </span>
                                <span class="font-mono font-bold text-indigo-700">{{ gs('cur_sym') }}<span class="domainOrderPrice">0.00</span></span>
                            </div>

                            <div class="pt-3 border-t border-slate-200 space-y-2">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>@lang('Setup Fees'):</span>
                                    <span class="font-mono text-slate-900">{{ gs('cur_sym') }}<span class="setupFee">{{ pricing($product->payment_type, $price, $type = 'setupFee') }}</span></span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="billingType">{{ pricing($product->payment_type, $price, $type = 'price', $showText = true) }}:</span>
                                    <span class="font-mono text-slate-900">{{ gs('cur_sym') }}<span class="billingPrice">{{ pricing($product->payment_type, $price, $type = 'price') }}</span></span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                                <span class="text-base font-extrabold text-slate-900 font-display">@lang('Total Due Today')</span>
                                <span class="text-2xl font-extrabold font-mono text-indigo-600">
                                    {{ gs('cur_sym') }}<span class="finalAmount">{{ pricing($product->payment_type, $price, $type = 'total') }}</span>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                            <span>@lang('Continue to Checkout →')</span>
                        </button>
                    </div>
                </div>

            </div>
            </form>
        </div>

    @endif
</div>
@endsection

@push('script')
<script>
(function($) {
    "use strict";

    let selectedDomainName = '';
    let selectedDomainPrice = 0;
    let selectedDomainId = 0;
    let isDomainOwned = 0;

    // ── Tab Switching Logic ─────────────────────────────────
    $('.domain-tab-btn').on('click', function() {
        $('.domain-tab-btn').removeClass('bg-indigo-600 text-white shadow-xs').addClass('bg-slate-50 text-slate-700 border border-slate-200');
        $(this).removeClass('bg-slate-50 text-slate-700 border border-slate-200').addClass('bg-indigo-600 text-white shadow-xs');

        const target = $(this).data('target');
        $('.domain-tab-pane').addClass('hidden');
        $('#' + target).removeClass('hidden');

        if (window.lucide) window.lucide.createIcons();
    });

    // ── Confirm "I Already Own This Domain" ──────────────────
    $('#btn_confirm_owned_domain').on('click', function() {
        const domain = $.trim($('#owned_domain_input').val()).toLowerCase().replace(/^https?:\/\//, '').replace(/^www\./, '').replace(/\/.*$/, '');
        if (!domain) {
            alert('@lang("Please enter a valid domain name.")');
            $('#owned_domain_input').focus();
            return;
        }

        if (!$('#owned_domain_check').is(':checked')) {
            alert('@lang("Please confirm that you own this domain and will update nameservers.")');
            $('#owned_domain_check').focus();
            return;
        }

        selectedDomainName = domain;
        selectedDomainId = 0;
        selectedDomainPrice = 0;
        isDomainOwned = 1;

        proceedToConfig(domain, 0, 1, 0);
    });

    // ── Confirm "Transfer Domain" ────────────────────────────
    $('#btn_confirm_transfer_domain').on('click', function() {
        const domain = $.trim($('#transfer_domain_input').val()).toLowerCase().replace(/^https?:\/\//, '').replace(/^www\./, '').replace(/\/.*$/, '');
        if (!domain) {
            alert('@lang("Please enter a valid domain name to transfer.")');
            $('#transfer_domain_input').focus();
            return;
        }

        selectedDomainName = domain;
        selectedDomainId = 0;
        selectedDomainPrice = 0;
        isDomainOwned = 0;

        proceedToConfig(domain, 0, 0, 0);
    });

    // ── Change Domain Button (Back to Step 1) ─────────────────
    $('#btn_change_domain').on('click', function() {
        $('.hideElement').addClass('d-none');
        $('.domainArea').removeClass('d-none');
    });

    function proceedToConfig(domain, domainId, isOwned, domPrice = 0) {
        $('.domain').val(domain);
        $('.domain_id').val(domainId);
        $('.owned_domain_confirmed').val(isOwned);
        $('.selectedDomainDisplay').text(domain);

        if (isOwned == 1) {
            $('.selectedDomainType').text('@lang("Existing Domain (Owned)")');
            $('.domainOrderLine').addClass('hidden');
        } else if (domPrice > 0) {
            $('.selectedDomainType').text('@lang("New Domain Registration") (+{{ gs("cur_sym") }}' + parseFloat(domPrice).toFixed(2) + ')');
            $('.domainOrderLabel').text('@lang("Domain Reg") (' + domain + ')');
            $('.domainOrderPrice').text(parseFloat(domPrice).toFixed(2));
            $('.domainOrderLine').removeClass('hidden');
        } else {
            $('.selectedDomainType').text('@lang("Selected Domain")');
            $('.domainOrderLine').addClass('hidden');
        }

        $('.domainArea').addClass('d-none');
        $('.hideElement').removeClass('d-none');

        const curCycle = $('select[name=billing_cycle]').val() || 'monthly';
        recalcPrices(curCycle);

        if (window.lucide) window.lucide.createIcons();
    }

    // ── Live Domain Search Handler ───────────────────────────
    $('.domain_lookup_form').on('submit', function(e) {
        e.preventDefault();
        const domain = $.trim($(this).find('.domain_lookup_input').val()).toLowerCase().replace(/^https?:\/\//, '').replace(/^www\./, '').replace(/\/.*$/, '');
        if (!domain) return;

        $('.availability').html(`
            <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-3 text-xs text-slate-600">
                <span class="w-3 h-3 rounded-full bg-indigo-600 animate-ping"></span>
                <span>@lang("Checking domain availability across registries...")</span>
            </div>
        `);
        $('.showAvailability').empty();

        $.ajax({
            url: "{{ route('search.domain') }}",
            data: { domain: domain, live: 0 },
            success: function(res) {
                if (!res.success || !res.result) {
                    $('.availability').html(`
                        <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs">
                            ${res.message || '@lang("Domain search failed. Please try again.")'}
                        </div>
                    `);
                    return;
                }

                const result = res.result;
                const isAvailable = (result.available === true);
                const primaryDomain = result.domain;
                const pricing = result.pricing || {};
                const setupId = pricing.setup ? pricing.setup.id : 1;
                const price = parseFloat(pricing.price || 12.99).toFixed(2);
                const renewPrice = parseFloat(pricing.renew || (price * 1.5)).toFixed(2);
                const suggestions = result.suggestions || [];

                if (isAvailable) {
                    $('.availability').html(`
                        <div class="domain-card-box p-6 bg-white border border-indigo-200 rounded-2xl shadow-sm transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h2 class="text-2xl font-extrabold font-sans text-slate-900 tracking-tight">${primaryDomain}</h2>
                                    <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-semibold tracking-wide">
                                        @lang('Save 60%')
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px] font-medium flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 orb-pulse"></span>
                                        @lang('Available')
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 self-end sm:self-center">
                                    <div class="text-right">
                                        <div class="text-xs text-slate-400 line-through font-mono">${general.cur_sym}${renewPrice}</div>
                                        <div class="text-xl font-extrabold text-slate-900 font-sans">
                                            ${general.cur_sym}${price}<span class="text-xs font-normal text-slate-500">/1st yr</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn_register_found px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-full transition-all shadow-sm flex items-center gap-1.5" data-domain="${primaryDomain}" data-id="${setupId}" data-price="${price}">
                                        <i data-lucide="plus"></i>
                                        <span>@lang('Select & Continue →')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    $('.availability').html(`
                        <div class="domain-card-box p-6 bg-white border border-amber-200 rounded-2xl shadow-sm transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h2 class="text-2xl font-extrabold font-sans text-slate-900 tracking-tight">${primaryDomain}</h2>
                                    <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-100 text-xs font-semibold">
                                        @lang('Already Taken')
                                    </span>
                                </div>
                                <button type="button" class="btn_use_taken_domain px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-full transition-all shadow-sm flex items-center gap-1.5" data-domain="${primaryDomain}">
                                    <i data-lucide="check"></i>
                                    <span>@lang('I Own This Domain - Continue →')</span>
                                </button>
                            </div>
                        </div>
                    `);
                }

                // Render Suggestions List
                if (suggestions.length > 0) {
                    const discountPills = [89, 57, 18, 97, 50, 75];
                    let sugHtml = `
                        <div class="space-y-4 pt-2">
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight font-display">@lang('More options')</h3>
                            <div class="domain-card-box bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-6 shadow-sm">
                    `;

                    suggestions.slice(0, 6).forEach(function(item, idx) {
                        const discount = discountPills[idx % discountPills.length];
                        const itemPricing = item.pricing || {};
                        const itemPrice = parseFloat(itemPricing.price || 12.99).toFixed(2);
                        const origPrice = parseFloat(itemPricing.renew || (itemPrice * 1.5)).toFixed(2);
                        const itemSetupId = itemPricing.setup ? itemPricing.setup.id : 1;

                        sugHtml += `
                            <div class="tld-row-item py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 last:border-0">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="text-base font-bold font-sans text-slate-900 tracking-tight">${item.domain}</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-[11px] font-semibold">
                                        @lang('Save') ${discount}%
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 self-end sm:self-center">
                                    <div class="text-right">
                                        <span class="text-xs text-slate-400 line-through font-mono me-1.5">${general.cur_sym}${origPrice}</span>
                                        <span class="text-base font-bold text-slate-900 font-sans">${general.cur_sym}${itemPrice}<span class="text-xs font-normal text-slate-500">/1st yr</span></span>
                                    </div>
                                    <button type="button" class="btn_register_found btn-add-cart-pill px-5 py-2 bg-slate-50 hover:bg-indigo-600 hover:text-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-full transition-all" data-domain="${item.domain}" data-id="${itemSetupId}" data-price="${itemPrice}">
                                        @lang('Select')
                                    </button>
                                </div>
                            </div>
                        `;
                    });

                    sugHtml += `</div></div>`;
                    $('.showAvailability').html(sugHtml);
                }

                if (window.lucide) window.lucide.createIcons();
            }
        });
    });

    $(document).on('click', '.btn_register_found', function() {
        const dom = $(this).data('domain');
        const id = $(this).data('id') || 1;
        const pr = parseFloat($(this).data('price') || 12.99);

        selectedDomainName = dom;
        selectedDomainId = id;
        selectedDomainPrice = pr;
        isDomainOwned = 0;

        proceedToConfig(dom, id, 0, pr);
    });

    $(document).on('click', '.btn_use_taken_domain', function() {
        const dom = $(this).data('domain');
        selectedDomainName = dom;
        selectedDomainId = 0;
        selectedDomainPrice = 0;
        isDomainOwned = 1;

        proceedToConfig(dom, 0, 1, 0);
    });

    // ── Billing Duration Selector Card Clicks ────────────────
    $('.duration-pill').on('click', function() {
        $('.duration-pill').removeClass('border-indigo-600 bg-indigo-50/50 shadow-xs').addClass('border-slate-200 bg-white');
        $(this).removeClass('border-slate-200 bg-white').addClass('border-indigo-600 bg-indigo-50/50 shadow-xs');

        const cycle = $(this).data('cycle');
        $('select[name=billing_cycle]').val(cycle).trigger('change');
    });

    // ── Price Recalculation Engine ───────────────────────────
    const productPrice = @json(@$product->price);
    const general = @json(gs());
    const allOptions = $('.options');

    function getAmount(num, decimals = 2) {
        return parseFloat(num || 0).toFixed(decimals);
    }

    $('select[name=billing_cycle]').on('change', function() {
        const cycle = $(this).val();
        recalcPrices(cycle);
    });

    $('.options').on('change', function() {
        const cycle = $('select[name=billing_cycle]').val() || 'monthly';
        recalcPrices(cycle);
    });

    function recalcPrices(cycle) {
        if (!productPrice) return;
        const cyclePrice = parseFloat(productPrice[cycle] || 0);
        const setupFee = parseFloat(productPrice[cycle + '_setup_fee'] || 0);

        $('.basicPrice').text(getAmount(cyclePrice));
        $('.billingPrice').text(getAmount(cyclePrice));
        $('.setupFee').text(getAmount(setupFee));

        let addlPrice = 0;
        let addlSetup = 0;
        $('.configurablePrice').empty();

        $('.options').each(function() {
            const selected = $(this).find('option:selected');
            const dataPrice = selected.data('price');
            if (dataPrice) {
                const optPrice = parseFloat(dataPrice[cycle] || 0);
                const optSetup = parseFloat(dataPrice[cycle + '_setup_fee'] || 0);
                addlPrice += optPrice;
                addlSetup += optSetup;

                $('.configurablePrice').append(`
                    <div class="flex items-center justify-between text-slate-500 text-[11px]">
                        <span>${$(this).data('name')}: ${selected.data('text')}</span>
                        <span class="font-mono">${general.cur_sym}${getAmount(optPrice)}</span>
                    </div>
                `);
            }
        });

        const domainCost = (isDomainOwned === 0 && selectedDomainPrice > 0) ? selectedDomainPrice : 0;
        const totalDue = cyclePrice + setupFee + addlPrice + addlSetup + domainCost;
        $('.finalAmount').text(getAmount(totalDue));
    }

    if (window.lucide) window.lucide.createIcons();

})(jQuery);
</script>
@endpush
