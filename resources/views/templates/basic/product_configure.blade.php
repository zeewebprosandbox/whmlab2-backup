@extends($activeTemplate . 'layouts.side_bar')

@section('data')
    <div class="col-lg-9">
        <div class="row gy-4">

            @if ($product->stock_control && $product->stock_quantity <= 0)
                <div class="col-md-12 text-center">
                    <div class="alert alert-warning" role="alert">
                        <span class="text-muted">@lang('Sorry, Out of Stock')</span>
                    </div>
                </div>
            @else
                @if ($product->domain_register && !@$cart)
                    <div class="col-md-10 domainArea">
                        <div class="whm-domain-card">
                            <div class="whm-domain-card__header">
                                <span>@lang('Choose a Domain')</span>
                                <h3>@lang('Start with your domain name')</h3>
                                <p>@lang('Enter the full domain. If it is free, you can register it. If it is already registered, confirm ownership and update the nameservers after checkout.')</p>
                            </div>

                            <form action="" class="domain_lookup_form form exclude">
                                <div class="whm-domain-search">
                                    <i data-lucide="globe-2"></i>
                                    <input type="text" name="domain" class="domain_lookup_input" required placeholder="@lang('example.com')" autocomplete="off">
                                    <button type="submit" class="exclude">@lang('Check')</button>
                                </div>
                            </form>

                            <div class="availability"></div>
                            <div class="showAvailability"></div>
                        </div>
                    </div>
                @endif

                <div class="col-md-12 {{ $product->domain_register ? 'd-none hideElement' : null }}">
                    @if (!@$isUpdate)
                        <form action="{{ route('shopping.cart.add.service') }}" method="post">
                        @else
                            <form action="{{ route('shopping.cart.config.service.update') }}" method="post">
                                <input type="hidden" name="cart_id" value="{{ $cart->id }}">
                    @endif
                    @csrf
                    <input type="hidden" name="domain" class="domain">
                    <input type="hidden" name="domain_id" value="0" class="domain_id" required>
                    <input type="hidden" name="owned_domain_confirmed" value="0" class="owned_domain_confirmed">
                    <input type="hidden" name="product_id" value="{{ $product->id }}" required>

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="col-md-12 form-group">
                                <h3>@lang('Product Configure')</h3>
                                <p class="mt-1">@lang('Configure your desired options and continue to checkout')</p>
                            </div>
                            <div class="row gy-3 mt-2">

                                @php $price = $product->price; @endphp
                                <div class="col-md-12 form-group">
                                    <div class="card">
                                        <div class="card-header bg-dark-two">
                                            <h5 class="text--white">{{ __($product->name) }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                <div class="fs-12">@php echo nl2br($product->description); @endphp</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 {{ $product->payment_type == 1 ? 'd-none' : '' }}">
                                    <div class="form-group whm-duration-picker">
                                        <div class="whm-duration-picker__head">
                                            <span>@lang('Billing duration')</span>
                                            <p>@lang('Choose how long you want this service billed for.')</p>
                                        </div>
                                        <select name="billing_cycle" class="form-control form--control h-45 form-select whm-billing-select whm-native-select-hidden" tabindex="-1" aria-hidden="true">
                                            @php echo pricing($product->payment_type, $price); @endphp
                                        </select>
                                        <div class="whm-billing-card-grid" data-billing-card-grid>
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

                                                    <button type="button" class="whm-billing-card {{ $isSelected ? 'active' : '' }}" data-billing-card="{{ $column }}" aria-pressed="{{ $isSelected ? 'true' : 'false' }}">
                                                        <span>{{ __($cycle['showText']) }}</span>
                                                        <strong>{{ gs('cur_sym') }}{{ getAmount($cyclePrice) }}</strong>
                                                        <small>
                                                            @if ($setupFee > 0)
                                                                {{ gs('cur_sym') }}{{ getAmount($setupFee) }} {{ __(gs('cur_text')) }} @lang('setup')
                                                            @else
                                                                @lang('No setup fee')
                                                            @endif
                                                        </small>
                                                        @if ($isLongTerm)
                                                            <span class="whm-billing-badge">@lang('Premium value')</span>
                                                        @endif
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                @php $configs = $product->getConfigs; @endphp

                                @foreach ($configs as $config)
                                    @php
                                        $group = $config->activeGroup;
                                        $options = $group->activeOptions;
                                    @endphp

                                    @foreach ($options->sortBy('order') as $option)
                                        @php $subOptions = $option->activeSubOptions; @endphp

                                        @if (count($subOptions))
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __($option->name) }}</label>
                                                    <select name="config_options[{{ $option->id }}]" class="form-control form--control h-45 options form-select" data-type='' data-name="{{ __($option->name) }}">
                                                        @foreach ($subOptions->sortBy('order') as $subOption)
                                                            <option value="{{ $subOption->id }}" data-price='{{ $subOption->getOnlyPrice }}' data-text='{{ __($subOption->name) }}'>
                                                                {{ __($subOption->name) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="whm-config-card-grid" data-config-card-grid></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach

                                @if ($product->product_type == 3)
                                    <div class="col-md-12 mt-5">
                                        <h5 class="text-center mb-3">@lang('Configure Server')</h5>
                                        <div class="row gy-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('Hostname')</label>
                                                    <input type="text" name="hostname" class="form-control form--control h-45 hostname" placeholder="servername.example.com" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('Root Password')</label>
                                                    <input type="password" name="password" class="form-control form--control h-45 root_password" placeholder="*******" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('NS1 Prefix')</label>
                                                    <input type="text" name="ns1" class="form-control form--control h-45 ns1_prefix" placeholder="ns1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('NS2 Prefix')</label>
                                                    <input type="text" name="ns2" class="form-control form--control h-45 ns2_prefix" placeholder="ns2" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card p-3">
                                <span class="card-header bg-dark-two text-center fw-bold rounded-0">@lang('Order Summary')</span>
                                <div class="card-body pb-0 px-0">
                                    <div>
                                        <b>{{ __($product->name) }}</b>
                                        <span class="d-block fst-italic">{{ $product->serviceCategory->name }}</span>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <span>{{ __($product->name) }}</span>
                                            <span>
                                                {{ gs('cur_sym') }}<span class="basicPrice">{{ pricing($product->payment_type, $price, $type = 'price') }}</span>
                                                {{ __(gs('cur_text')) }}
                                            </span>
                                        </div>
                                        <div class="configurablePrice"></div>
                                    </div>
                                    <div class="calculatePrice border-top mt-3">
                                        <div class="d-flex justify-content-between">
                                            <span>@lang('Setup Fees'):</span>
                                            <span>
                                                {{ gs('cur_sym') }}<span class="setupFee">{{ pricing($product->payment_type, $price, $type = 'setupFee') }}</span>
                                                {{ __(gs('cur_text')) }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="billingType">{{ pricing($product->payment_type, $price, $type = 'price', $showText = true) }}:</span>
                                            <span>
                                                {{ gs('cur_sym') }}<span class="billingPrice">{{ pricing($product->payment_type, $price, $type = 'price') }}</span>
                                                {{ __(gs('cur_text')) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-between border-top mt-2 pt-4">
                                        <h5 class="text-center fw-bold">@lang('Total')</h5>
                                        <h5 class="justify-content-end d-flex">
                                            {{ gs('cur_sym') }} <span class="finalAmount">
                                                {{ pricing($product->payment_type, $price, $type = 'price') + pricing($product->payment_type, $price, $type = 'setupFee') }}
                                            </span>
                                            {{ __(gs('cur_text')) }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" class="btn bg--base btn-lg text-white w-100">
                                        @lang('Continue') <i data-lucide="arrow-right-circle"></i>
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>
                    </form>
                </div>

            @endif
        </div>
    </div>
@endsection

@push('style')
<style>
    .whm-domain-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 28px;
    }

    .whm-domain-card__header {
        margin-bottom: 22px;
    }

    .whm-domain-card__header span {
        color: #2563eb;
        display: block;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .whm-domain-card__header h3 {
        color: #0f172a;
        font-size: clamp(24px, 4vw, 36px);
        letter-spacing: 0;
        line-height: 1.15;
        margin-bottom: 8px;
    }

    .whm-domain-card__header p {
        color: #64748b;
        font-size: 15px;
        line-height: 1.65;
        margin: 0;
        max-width: 760px;
    }

    .whm-domain-search {
        align-items: center;
        background: #f8fafc;
        border: 1px solid #dbe3ef;
        border-radius: 14px;
        display: grid;
        gap: 12px;
        grid-template-columns: auto minmax(0, 1fr) auto;
        padding: 8px;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .whm-domain-search:focus-within {
        background: #fff;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .1);
    }

    .whm-domain-search svg {
        color: #64748b;
        height: 22px;
        margin-left: 10px;
        width: 22px;
    }

    .whm-domain-search input {
        background: transparent;
        border: 0;
        color: #0f172a;
        font-size: 18px;
        font-weight: 600;
        min-height: 48px;
        outline: 0;
        width: 100%;
    }

    .whm-domain-search input::placeholder {
        color: #94a3b8;
    }

    .whm-domain-search button,
    .whm-domain-action {
        align-items: center;
        background: #2563eb;
        border: 0;
        border-radius: 10px;
        color: #fff;
        display: inline-flex;
        font-size: 14px;
        font-weight: 700;
        gap: 8px;
        justify-content: center;
        min-height: 46px;
        padding: 0 18px;
        transition: background-color .2s ease, transform .2s ease;
    }

    .whm-domain-search button:hover,
    .whm-domain-action:hover {
        background: #1d4ed8;
        color: #fff;
        transform: translateY(-1px);
    }

    .whm-domain-search button:disabled {
        cursor: wait;
        opacity: .82;
        transform: none;
    }

    .whm-domain-btn-spinner {
        border: 2px solid rgba(255, 255, 255, .45);
        border-radius: 999px;
        border-top-color: #fff;
        display: inline-block;
        height: 15px;
        margin-right: 8px;
        vertical-align: -2px;
        width: 15px;
        animation: whmDomainSpin .65s linear infinite;
    }

    @keyframes whmDomainSpin {
        to {
            transform: rotate(360deg);
        }
    }

    .whm-domain-action--secondary {
        background: #0f172a;
    }

    .whm-domain-action--secondary:hover {
        background: #1e293b;
    }

    .availability {
        margin-top: 18px;
    }

    .showAvailability {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 14px;
    }

    .domain-row {
        align-items: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        order: 2;
        padding: 16px;
    }

    .domain-row.domain-match {
        order: 1;
    }

    .whm-domain-result {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
    }

    .whm-domain-result--available {
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .whm-domain-result--owned {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .whm-domain-result h5 {
        color: #0f172a;
        font-size: 17px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .whm-domain-result p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.55;
        margin: 0;
    }

    .whm-domain-result__top {
        align-items: flex-start;
        display: flex;
        gap: 14px;
        justify-content: space-between;
    }

    .whm-domain-result__domain {
        color: #2563eb;
        font-weight: 800;
    }

    .whm-domain-primary-price {
        color: #0f172a;
        display: block;
        font-size: 20px;
        font-weight: 900;
        margin-top: 10px;
    }

    .whm-domain-suggestion-title {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .04em;
        margin: 4px 0 0;
        text-transform: uppercase;
    }

    .whm-domain-ns-list,
    .whm-domain-ns-guide div {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .whm-domain-ns-list code,
    .whm-domain-ns-guide code {
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        color: #0f172a;
        font-size: 13px;
        padding: 7px 10px;
    }

    .whm-domain-empty-ns {
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        color: #64748b;
        display: inline-flex;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.45;
        padding: 10px 12px;
    }

    .whm-domain-owned-check {
        align-items: flex-start;
        color: #334155;
        display: flex;
        font-size: 13px;
        font-weight: 600;
        gap: 10px;
        line-height: 1.45;
        margin-top: 16px;
    }

    .whm-domain-owned-check input {
        flex: 0 0 auto;
        margin-top: 3px;
    }

    .whm-domain-ns-guide {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        margin-top: 18px;
        padding: 16px;
    }

    .whm-domain-ns-guide span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .whm-duration-picker {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
    }

    .whm-duration-picker__head {
        align-items: flex-start;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .whm-duration-picker__head span {
        color: #0f172a;
        display: block;
        font-size: 16px;
        font-weight: 800;
        line-height: 1.25;
    }

    .whm-duration-picker__head p {
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
        margin: 4px 0 0;
    }

    .whm-native-select-hidden {
        height: 1px !important;
        opacity: 0 !important;
        pointer-events: none !important;
        position: absolute !important;
        width: 1px !important;
    }

    .whm-billing-card-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(auto-fit, minmax(172px, 1fr));
    }

    .whm-billing-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        color: #0f172a;
        min-height: 128px;
        overflow: hidden;
        padding: 16px;
        position: relative;
        text-align: left;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease, background-color .2s ease;
        width: 100%;
    }

    .whm-billing-card::before {
        background: #2563eb;
        content: "";
        height: 3px;
        left: 0;
        opacity: 0;
        position: absolute;
        right: 0;
        top: 0;
        transition: opacity .2s ease;
    }

    .whm-billing-card:hover,
    .whm-billing-card.active {
        background: #f8fbff;
        border-color: #2563eb;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
        transform: translateY(-1px);
    }

    .whm-billing-card.active::before {
        opacity: 1;
    }

    .whm-billing-card.active::after {
        align-items: center;
        background: #2563eb;
        border-radius: 50%;
        color: #fff;
        content: "✓";
        display: flex;
        font-size: 11px;
        font-weight: 900;
        height: 22px;
        justify-content: center;
        position: absolute;
        right: 12px;
        top: 12px;
        width: 22px;
    }

    .whm-billing-card span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .03em;
        padding-right: 28px;
        text-transform: uppercase;
    }

    .whm-billing-card strong {
        color: #0f172a;
        display: block;
        font-size: 26px;
        font-weight: 800;
        line-height: 1.15;
        margin-top: 12px;
    }

    .whm-billing-card small {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.45;
        margin-top: 6px;
    }

    .whm-billing-badge {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 8px;
        color: #047857 !important;
        display: inline-flex !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        letter-spacing: 0 !important;
        margin-top: 10px;
        padding: 4px 7px;
        text-transform: none !important;
        width: max-content;
    }

    .whm-billing-card:focus-visible,
    .whm-config-card:focus-visible {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .14);
        outline: none;
    }

    .whm-config-card-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(164px, 1fr));
        margin-top: 12px;
    }

    .whm-config-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        color: #0f172a;
        min-height: 112px;
        overflow: hidden;
        padding: 15px;
        position: relative;
        text-align: left;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease, background-color .2s ease;
        width: 100%;
    }

    .whm-config-card::before {
        background: #2563eb;
        bottom: 0;
        content: "";
        opacity: 0;
        position: absolute;
        right: 0;
        top: 0;
        transition: opacity .2s ease;
        width: 3px;
    }

    .whm-config-card:hover,
    .whm-config-card.active {
        background: #f8fbff;
        border-color: #2563eb;
        box-shadow: 0 12px 26px rgba(15, 23, 42, .07);
        transform: translateY(-1px);
    }

    .whm-config-card.active::before {
        opacity: 1;
    }

    .whm-config-card.active::after {
        align-items: center;
        background: #2563eb;
        border-radius: 50%;
        color: #fff;
        content: "✓";
        display: flex;
        font-size: 11px;
        font-weight: 900;
        height: 22px;
        justify-content: center;
        position: absolute;
        right: 12px;
        top: 12px;
        width: 22px;
    }

    .whm-config-card span {
        color: #0f172a;
        display: block;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
        padding-right: 26px;
    }

    .whm-config-card strong {
        color: #2563eb;
        display: block;
        font-size: 18px;
        font-weight: 800;
        margin-top: 12px;
    }

    .whm-config-card small {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.45;
        margin-top: 4px;
    }

    @media (max-width: 767px) {
        .whm-domain-card {
            padding: 20px;
        }

        .whm-domain-search {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .whm-domain-search button {
            grid-column: 1 / -1;
            width: 100%;
        }

        .whm-domain-search input {
            font-size: 16px;
        }

        .domain-row,
        .whm-domain-result__top {
            display: block;
        }

        .domain-row .text-end,
        .whm-domain-result__top .whm-domain-action {
            margin-top: 14px;
            text-align: left !important;
            width: 100%;
        }

        .whm-config-card-grid {
            grid-template-columns: 1fr;
        }

        .whm-duration-picker {
            border-radius: 16px;
            padding: 16px;
        }

        .whm-duration-picker__head {
            display: block;
        }

        .whm-billing-card-grid {
            grid-template-columns: 1fr;
        }

        .whm-billing-card,
        .whm-config-card {
            min-height: auto;
            padding: 15px;
        }
    }
</style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var general = @json(gs(['cur_sym', 'cur_text']));
            var product = @json($product);

            var productPrice = @json($product->price);
            var allOptions = $('.options');

            var globalSetup = "{{ pricing($product->payment_type, @$price, $type = 'setupFee') }}";
            var addingSetupFee = 0;

            var globalPrice = "{{ pricing($product->payment_type, @$price, $type = 'price') }}";
            var addingPrice = 0;

            var basicPrice = $('.basicPrice');

            var billingType = $('.billingType');
            var setupFee = $('.setupFee');
            var billingPrice = $('.billingPrice');

            var finalAmount = $('.finalAmount');

            var info = '';
            var billingSelect = $('select[name=billing_cycle]');
            var setupText = @json(__('setup'));
            var noSetupText = @json(__('No setup fee'));

            function syncBillingCards(value) {
                $('[data-billing-card]').removeClass('active').attr('aria-pressed', 'false');
                $('[data-billing-card="' + value + '"]').addClass('active').attr('aria-pressed', 'true');
            }

            function buildBillingCards() {
                var grid = $('[data-billing-card-grid]');
                if (!grid.length || !billingSelect.length || billingSelect.closest('.d-none').length) {
                    return;
                }

                billingSelect.addClass('whm-native-select-hidden').attr({
                    'tabindex': '-1',
                    'aria-hidden': 'true'
                });

                if (grid.children('[data-billing-card]').length) {
                    syncBillingCards(billingSelect.val());
                    return;
                }

                grid.empty();

                billingSelect.find('option').each(function(index) {
                    var option = $(this);
                    var value = option.val();
                    var label = $.trim(option.text());
                    var price = pricing(productPrice, 'price', value);
                    var setup = pricing(productPrice, 'setupFee', value);
                    var isAnnual = value === 'annually' || value === 'biennially' || value === 'triennially';
                    var badge = isAnnual ? `<span class="whm-billing-badge">@lang('Premium value')</span>` : '';
                    var active = option.is(':selected') ? 'active' : '';
                    var pressed = option.is(':selected') ? 'true' : 'false';

                    grid.append(`
                        <button type="button" class="whm-billing-card ${active}" data-billing-card="${value}" aria-pressed="${pressed}">
                            <span>${label}</span>
                            <strong>${general.cur_sym}${price}</strong>
                            <small>${parseFloat(setup) > 0 ? general.cur_sym + setup + ' ' + setupText : noSetupText}</small>
                            ${badge}
                        </button>
                    `);
                });

            }

            buildBillingCards();
            buildConfigCards();

            $(document).on('click', '[data-billing-card]', function() {
                var value = $(this).data('billing-card');

                syncBillingCards(value);
                billingSelect.val(value).trigger('change');
            });

            function buildConfigCards() {
                allOptions.each(function(index, element) {
                    var select = $(element);
                    var grid = select.siblings('[data-config-card-grid]');

                    if (!grid.length) {
                        return;
                    }

                    grid.empty();
                    select.addClass('whm-native-select-hidden');

                    select.find('option').each(function(optionIndex, optionElement) {
                        var option = $(optionElement);
                        var priceData = option.data('price');
                        var value = option.val();
                        var label = option.data('text') || $.trim(option.text());
                        var active = option.is(':selected') ? 'active' : '';
                        var priceLine = '';
                        var setupLine = noSetupText;

                        if (priceData) {
                            var column = currentBillingColumn();
                            var setupColumn = column + '_setup_fee';
                            var price = getAmount(priceData[column] || 0);
                            var setup = getAmount(priceData[setupColumn] || 0);

                            priceLine = `${general.cur_sym}${price} ${general.cur_text}`;
                            setupLine = parseFloat(setup) > 0 ? `${general.cur_sym}${setup} ${general.cur_text} ${setupText}` : noSetupText;
                        }

                        grid.append(`
                            <button type="button" class="whm-config-card ${active}" data-config-select="${select.attr('name')}" data-config-value="${value}">
                                <span>${label}</span>
                                <strong>${priceLine}</strong>
                                <small>${setupLine}</small>
                            </button>
                        `);
                    });
                });
            }

            function currentBillingColumn() {
                if (product.payment_type == 1) {
                    return 'monthly';
                }

                return billingSelect.val() || 'monthly';
            }

            function syncConfigCardActive(select) {
                var name = select.attr('name');
                var value = select.val();
                var cards = $(`[data-config-select="${name}"]`);

                cards.removeClass('active');
                cards.filter(`[data-config-value="${value}"]`).addClass('active');
            }

            $(document).on('click', '[data-config-select]', function() {
                var card = $(this);
                var select = $(`select[name="${card.data('config-select')}"]`);

                select.val(card.data('config-value')).trigger('change');
                syncConfigCardActive(select);
            });

            if (product.domain_register) {

                var domains = @json($domains);
                var hideElement = $('.hideElement');
                var domainArea = $('.domainArea');
                var domainSearchInProgress = false;
                var domainSearchTimer = null;
                var domainSearchRequest = null;
                var domainSearchSequence = 0;

                $('.domain_lookup_form').on('submit', function(e) {
                    e.preventDefault();

                    var domain = normalizeDomain($(this).find('.domain_lookup_input').val());

                    if (!domain) {
                        return false;
                    }

                    $('.showAvailability').empty();
                    $('.availability').empty();

                    checkDomain(domain, $(this), true);
                });

                $('.domain_lookup_input').on('input', function() {
                    var input = $(this);
                    var form = input.closest('.domain_lookup_form');
                    var domain = normalizeDomain(input.val());

                    clearTimeout(domainSearchTimer);

                    if (domain.length < 2) {
                        $('.showAvailability').empty();
                        $('.availability').empty();
                        if (domainSearchRequest) {
                            domainSearchRequest.abort();
                            domainSearchRequest = null;
                        }
                        return;
                    }

                    domainSearchTimer = setTimeout(function() {
                        checkDomain(domain, form, false);
                    }, 450);
                });

                function checkDomain(domain, form, force) {
                    var button = form.find('button[type=submit]');
                    var originalButtonHtml = button.data('original-html') || button.html();
                    var sequence = ++domainSearchSequence;
                    button.data('original-html', originalButtonHtml);

                    if (domainSearchRequest) {
                        domainSearchRequest.abort();
                    }

                    domainSearchRequest = $.ajax({
                        url: "{{ route('search.domain') }}",
                        data: {
                            domain: domain,
                            live: force ? 0 : 1
                        },

                        beforeSend: function(){
                            domainSearchInProgress = true;
                            $('.showAvailability').empty();
                            if (force) {
                                button.prop('disabled', true).html(`<span class="whm-domain-btn-spinner"></span>@lang('Checking')`);
                            }
                            $('.availability').html(`
                                <div class="whm-domain-result">
                                    <h5>@lang('Checking domain')...</h5>
                                    <p>@lang('We are checking live registrar API availability and matching useful extensions.')</p>
                                </div>
	                            `);
                        },
                        
                        success: function(getResponse) {
                            if (sequence !== domainSearchSequence) {
                                return;
                            }
                 
                            if (!getResponse['success']) {
                                $('.availability').html(``);

                                var errors = getResponse['message'];
                                if(typeof(errors) != 'object'){
                                    errors = [errors];
                                }

                                $.each(errors, function(index, message) {
                                    return $('.availability').append(`
                                        <div class="whm-domain-result">
                                            <h5 class="text--danger">${message}</h5>
                                            <p>@lang('Please check the spelling and try again.')</p>
                                        </div>
                                    `);
                                });
                                return false;
                            }

                            var response = getResponse.result;
                            var available = false;

                            var suggestions = [];

                            $.each(response.data.sort(function(a, b) {
                                return b.match - a.match;
                            }), function(key, data) {

                                var domain = data.domain;
                                var setup = data.setup;
                                var match = data.match;

                                if(response.domain == domain && data.available && data.setup){ 
                                    available = true;
                                }

                                if(data.available && data.setup && !match){  
                                    suggestions.push(data);
                                }
                            });

                            if(available){
                                var primarySetupId = matchedSetupId(response);
                                var primaryMatch = (response.data || []).find(function(item) {
                                    return item.domain === response.domain;
                                });
                                var primaryPriceValue = primaryMatch && primaryMatch.setup && primaryMatch.setup.pricing && primaryMatch.setup.pricing.firstPrice ? primaryMatch.setup.pricing.firstPrice.price : 0;
                                var primaryPrice = parseFloat(primaryPriceValue || 0).toFixed(2);

                                $('.availability').html(`
                                    <div class="whm-domain-result whm-domain-result--available">
                                        <div class="whm-domain-result__top">
                                            <div>
                                                <h5>@lang('Domain available')</h5>
                                                <p><span class="whm-domain-result__domain">${response.domain}</span> @lang('is ready to register with this order.')</p>
                                                <strong class="whm-domain-primary-price">${general.cur_sym}${primaryPrice} ${general.cur_text}</strong>
                                            </div>
                                            <button type="button" class="whm-domain-action registerDomainBtn" data-domain="${response.domain}" data-id="${primarySetupId}">
                                                <i data-lucide="shopping-cart"></i> @lang('Register domain')
                                            </button>
                                        </div>
                                    </div>
                                `);
                            }else{
                                $('.availability').html(ownedDomainResult(response.domain));
                            }

                            renderDomainSuggestions(suggestions);

                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        },
                        error: function(error) {
                            if (error.statusText === 'abort') {
                                return;
                            }

                            var errorMessage = error.responseJSON && error.responseJSON.messages ? error.responseJSON.messages : '@lang('Please try again.')';

                            $('.availability').html(`
                                <div class="whm-domain-result">
                                    <h5 class="text--danger">@lang('Unable to check domain')</h5>
                                    <p>${errorMessage}</p>
                                </div>
                            `);
                        },
                        complete: function() {
                            if (sequence === domainSearchSequence) {
                                domainSearchInProgress = false;
                                button.prop('disabled', false).html(originalButtonHtml);
                                domainSearchRequest = null;
                            }
                        }
                    });

                }

                function normalizeDomain(domain) {
                    return $.trim(domain || '')
                        .toLowerCase()
                        .replace(/^https?:\/\//, '')
                        .replace(/^www\./, '')
                        .replace(/\/.*$/, '');
                }

                function matchedSetupId(response) {
                    var matched = (response.data || []).find(function(item) {
                        return item.domain === response.domain;
                    });

                    return matched && matched.setup ? matched.setup.id : '';
                }

                function renderDomainSuggestions(suggestions) {
                    $('.showAvailability').empty();

                    if (!suggestions.length) {
                        return;
                    }

                    $('.showAvailability').append(`<p class="whm-domain-suggestion-title">@lang('Available domain suggestions')</p>`);

                    suggestions.slice(0, 5).forEach(function(data) {
                        var priceValue = data.setup && data.setup.pricing && data.setup.pricing.firstPrice ? data.setup.pricing.firstPrice.price : 0;
                        var price = parseFloat(priceValue || 0).toFixed(2);
                        var setupId = data.setup ? data.setup.id : '';

                        $('.showAvailability').append(`
                            <div class="domain-row">
                                <span>${data.domain}</span>
                                <div class='text-end'>
                                    <span class='fw-bold'>${general.cur_sym}${price}</span>
                                    <button
                                        class="whm-domain-action registerDomainBtn ms-2"
                                        data-domain="${data.domain}"
                                        data-id="${setupId}"
                                    >
                                        <i data-lucide="shopping-cart"></i> @lang('Register')
                                    </button>
                                </div>
                            </div>
                        `);
                    });
                }

                function ownedDomainResult(domain) {
                    return `
                        <div class="whm-domain-result whm-domain-result--owned">
                            <h5>@lang('Domain already registered')</h5>
                            <p><span class="whm-domain-result__domain">${domain}</span> @lang('is not available to register. If this is your domain, you can attach it to this hosting order and point it to ZodHost after payment.')</p>

                            <label class="whm-domain-owned-check">
                                <input type="checkbox" class="ownedDomainCheck">
                                <span>@lang('I own this domain and will point it to ZodHost after payment.')</span>
                            </label>

                            <button type="button" class="whm-domain-action whm-domain-action--secondary useOwnedDomainBtn mt-3" data-domain="${domain}">
                                <i data-lucide="check-circle"></i> @lang('Use my domain')
                            </button>
                        </div>
                    `;
                }

                $(document).on('click', '.registerDomainBtn', function() {
                    $('.domain').val($(this).data('domain'));
                    $('.domain_id').val($(this).data('id'));
                    $('.owned_domain_confirmed').val(0);
                    hideElement.removeClass('d-none');
                    domainArea.addClass('d-none');
                });

                $(document).on('click', '.useOwnedDomainBtn', function() {
                    if (!$('.ownedDomainCheck').is(':checked')) {
                        notify('error', '@lang('Please confirm you own this domain and will update its nameservers.')');
                        return false;
                    }

                    $('.domain').val($(this).data('domain'));
                    $('.domain_id').val(0);
                    $('.owned_domain_confirmed').val(1);
                    hideElement.removeClass('d-none');
                    domainArea.addClass('d-none');
                });
            }

            $('select[name=billing_cycle]').on('change', function() {
                var value = $(this).val();

                var price = pricing(productPrice, 'price', value);
                var setup = pricing(productPrice, 'setupFee', value);
                var type = pricing(0, null, value);

                var totalPriceForSelectedItem = pricing(productPrice, null, value);

                billingType.text(type);
                basicPrice.text(price);
                billingPrice.text(price);
                setupFee.text(setup);

                finalAmount.text(totalPriceForSelectedItem);
                allOptions.attr('data-type', value);

                globalSetup = setup;
                globalPrice = price;

                showSelect(value);
                buildConfigCards();
                syncBillingCards(value);

            }).change();

            allOptions.on('change', function() {

                var column = $(this).attr('data-type');
                var getPrice = $(this).find(":selected").data('price');

                if (!getPrice) {
                    syncConfigCardActive($(this));
                    return;
                }

                showSelect(column, false);
                syncConfigCardActive($(this));
            });

            function pricing(price, type, column) {
                try {

                    if (!price) {
                        column = column.replaceAll('_', ' ');

                        if (product.payment_type == 1) {
                            column = 'One Time:';
                        }

                        return column.replaceAll(/(?:^|\s)\S/g, function(word) {
                            return word.toUpperCase();
                        });
                    }

                    if (!type) {
                        var price = productPrice[column];
                        var fee = productPrice[column + '_setup_fee'];
                        var sum = (parseFloat(fee) + parseFloat(price));

                        return getAmount(sum);
                    }

                    var amount = 0;

                    if (type == 'price') {
                        amount = productPrice[column];
                    } else {
                        column = column + '_setup_fee';
                        amount = productPrice[column];
                    }

                    return getAmount(amount);

                } catch (message) {
                    console.log(message);
                }
            }

            function getAmount(getAmount, length = 2) {
                var amount = parseFloat(getAmount).toFixed(length);
                return amount;
            }

            function showSelect(value, showDropdown = true) {

                try {

                    addingSetupFee = 0;
                    addingPrice = 0;

                    var getColumn = value;
                    var getFeeColumn = value + '_setup_fee';

                    allOptions.each(function(index, data) {
                        var options = $(data).find('option');
                        var finalText = null;

                        options.each(function(iteration, dropdown) {
                            var dropdown = $(dropdown);
                            var dropdownOptions = null;
                            var optionSetupFee = '';

                            if (dropdown.data('price')) {
                                var priceForThisItem = dropdown.data('price');
                                var mainText = dropdown.data('text');
                                var display = product.payment_type == 1 ? 'One Time' : pricing(0, null, getColumn);

                                if (product.payment_type == 1) {
                                    getColumn = 'monthly'
                                }

                                if (priceForThisItem[getFeeColumn] > 0) {
                                    optionSetupFee = ` + ${general.cur_sym}${getAmount(priceForThisItem[getFeeColumn])} ${general.cur_text} Setup Fee`
                                }

                                dropdownOptions = `${general.cur_sym}${getAmount(priceForThisItem[getColumn])} ${general.cur_text} ${display} ${optionSetupFee}`;

                                finalText = mainText + ' ' + dropdownOptions;

                                if (showDropdown) {
                                    dropdown.text(finalText);
                                }

                            }

                            if (dropdown.filter(':selected').attr('data-price')) {

                                var configurableOption = $('.configurablePrice')
                                configurableOption.empty();

                                info += `<div class='d-flex justify-content-between fs-12 mt-2 flex-wrap'>
                                        <span><i class='fa fa-angle-double-right'></i> ${$(data).data('name')}:</span>
                                        <span>${finalText}</span>
                                    </div>`

                                configurableOption.append(info);

                                addingSetupFee = sum(addingSetupFee, priceForThisItem[getFeeColumn]);
                                addingPrice = sum(addingPrice, priceForThisItem[getColumn]);

                                setupFee.text(sum(addingSetupFee, globalSetup));
                                billingPrice.text(sum(addingPrice, globalPrice));

                                finalAmount.text(sum(sum(addingSetupFee, globalSetup), sum(addingPrice, globalPrice)));
                            }

                        });

                    });

                    info = '';

                } catch (message) {
                    console.log(message);
                }

            }

            function sum(param1, param2) {
                var amount = parseFloat(param1) + parseFloat(param2);
                return getAmount(amount);
            }

            //For update operation
            @if (@$cart)

                var cart = @json(@$cart);
                var billingCycle = '{{ $billingCycle }}';

                var column = billingCycle;
                $(`select[name=billing_cycle] option[value=${column}]`).prop('selected', true).change();
                $('select[name=billing_cycle]').parent().hide();

                $('.hideElement').removeClass('d-none');
                $('.domainArea').addClass('d-none');

                $('.domain').val(cart.domain);
                $('.hostname').val(cart.hostname);
                $('.root_password').val(cart.password);
                $('.ns1_prefix').val(cart.ns1);
                $('.ns2_prefix').val(cart.ns2);

                var configOptionKeys = Object.keys(cart.config_options);
                var configOptionValues = Object.values(cart.config_options);
                var length = configOptionKeys.length;

                for (var i = 0; i < length; i++) {
                    var selectName = configOptionKeys[i];
                    var selectOption = configOptionValues[i];

                    $(`select[name='config_options[${selectName}]'] option[value=${selectOption}]`).prop('selected', true);

                    var price = pricing(productPrice, 'price', column);
                    var setup = pricing(productPrice, 'setupFee', column);
                    var type = pricing(0, null, column);

                    var totalPriceForSelectedItem = pricing(productPrice, null, column);

                    billingType.text(type);
                    basicPrice.text(price);
                    billingPrice.text(price);
                    setupFee.text(setup);

                    finalAmount.text(totalPriceForSelectedItem);
                    allOptions.attr('data-type', column);

                    globalSetup = setup;
                    globalPrice = price;

                    showSelect(column, false);
                }

                buildConfigCards();
            @endif

        })(jQuery);
    </script>
@endpush
