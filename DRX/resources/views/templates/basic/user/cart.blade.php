@extends($activeTemplate . 'layouts.side_bar')

@section('data')
    @php
        $cartTotal = $carts->sum('after_discount');
        $taxRate = gs('tax') ?? 0;
        $taxAmount = $taxRate > 0 ? ($cartTotal * $taxRate / 100) : 0;
        $checkoutPayable = $cartTotal + $taxAmount;
        $canPayWithBalance = auth()->check() && gs('deposit_module') && auth()->user()->balance >= $checkoutPayable;
    @endphp
    <div class="col-lg-9">
        <div class="row g-3">
            <div class="col-lg-8">
                <h4>@lang('Cart')</h4>
                @forelse($carts as $cart)
                    <div class="card fs-12 cart_child m-1 mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-11 d-flex justify-content-between flex-wrap align-items-center">
                                    @if ($cart->product_id && !$cart->domain_setup_id && !$cart->domain_id)
                                        <div>
                                            <h6 class="d-inline">{{ __($cart->product->name) }}</h6>
                                            <a href="{{ route('shopping.cart.config.service', $cart->id) }}">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </a>
                                            <span class="d-block">{{ __($cart->product->serviceCategory->name) }}</span>
                                            <span class="d-block fw-bold">{{ @$cart->domain }}</span>
                                        </div>
                                        <div class="mt-1 mt-lg-0">
                                            <h6 class="d-inline">{{ showAmount(@$cart->price) }}</h6>
                                            <span class="d-block">{{ @billingCycle($cart->billing_cycle, true)['showText'] }}</span>
                                            <span class="d-block small">{{ gs('cur_sym') }}{{ showAmount(@$cart->setup_fee) }} @lang('Setup Fee')</span>
                                            <span class="fst-italic fw-bold small">
                                                @lang('Total') {{ showAmount(@$cart->total) }}
                                            </span>
                                        </div>
                                    @else
                                        <div>
                                            @if ($cart->type == 4)
                                                <h6 class="d-inline">@lang('Domain Renew')</h6>
                                                <a href="{{ route('user.domain.details', $cart->domain_id) }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                            @else
                                                <h6 class="d-inline">@lang('Domain Registration')</h6>
                                                <a href="{{ route('shopping.cart.config.domain', $cart->id) }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                            @endif
                                            <span class="d-block fw-bold">
                                                {{ @$cart->domain }} - {{ @$cart->reg_period }} @lang('Year')
                                                {{ @$cart->id_protection ? __('with ID Protection') : null }}
                                            </span>
                                        </div>
                                        <div class="mt-1 mt-lg-0">
                                            <h6 class="d-block">{{ showAmount(@$cart->price) }}</h6>
                                            @if (@$cart->id_protection)
                                                <span class="d-block small">{{ showAmount(@$cart->setup_fee) }} @lang('ID Protection')</span>
                                            @endif
                                            <span class="fst-italic fw-bold small">
                                                @lang('Total') {{ showAmount(@$cart->total) }} 
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-1 form-group">
                                    <a class="remove_cart d-none" href="{{ route('shopping.cart.remove', $cart->id) }}">
                                        <i class="la la-trash">&nbsp;@lang('Remove')</i>
                                    </a>
                                    <a href="{{ route('shopping.cart.remove', $cart->id) }}" class="remove_icon">
                                        <i class="la la-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="row">
                        <div class="col-md-12 text-center mt-3">
                            <div class="card p-4">
                                <x-empty-message div={{ true }} message="Empty carts" />
                            </div>
                        </div>
                    </div>
                @endforelse

                @if (@$cart)
                    <div class="row">
                        <div class="col-lg-12 text-end mb-4">
                            <span class="bg--dark text-white p-2 fs-12 me-1 btn--xs btn">
                                <a href="{{ route('shopping.cart.empty') }}" class="text-white"><i class="la la-trash"></i> @lang('Empty Cart')</a>
                            </span>
                        </div>
                        <div class="col-lg-12">
                            <div class="card p-3">
                                @if (@$appliedCoupon)
                                    <form action="{{ route('shopping.cart.coupon.remove') }}" method="post">
                                        @csrf
                                        <p class="border p-2 text-center">
                                            {{ $appliedCoupon->coupon->code }} - {{ $appliedCoupon->coupon_type == 0 ? showAmount($appliedCoupon->coupon_discount, currencyFormat:false) . '%' : showAmount($carts->sum('discount')) }} @lang('Discount')
                                        </p>
                                        <div class="form-group mt-2">
                                            <button type="submit" class="btn btn-warning w-100 text-white">@lang('Remove Coupon Code')</button>
                                        </div>
                                    </form>
                                @else
                                    <form action="{{ route('shopping.cart.coupon') }}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text" class="form-control form--control h-45" name="coupon_code" placeholder="@lang('Enter coupon code if you have one')" required>
                                        </div>
                                        <div class="form-group mt-2">
                                            <button type="submit" class="btn btn--base btn--sm w-100">@lang('Validate Code')</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card p-3">
                    <span class="border-bottom p-2 bg-dark-two text-center fw-bold w-100">@lang('Order Summary')</span>
                    <div class="card-body pb-0 px-0">
                        <div>
                            <div class="d-flex justify-content-between mt-3">
                                <span>@lang('Subtotal')</span>
                                <span class="basicPrices">{{ showAmount($carts->sum('total')) }}</span>
                            </div>
                        </div>
                        @if ($appliedCoupon)
                            <div class="border-top mt-3">
                                <div class="d-flex justify-content-between small mt-1">
                                    <span class="discounts">
                                        @lang('Get') - {{ $appliedCoupon->coupon_type == 0 ? showAmount($appliedCoupon->coupon_discount, currencyFormat:false) . '%' : showAmount($carts->sum('discount')) }} @lang('Discount')
                                    </span>
                                    <span>
                                        <span class="discountAmounts">{{ showAmount($carts->sum('discount')) }}</span>
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if ($taxAmount > 0)
                            <div class="border-top mt-3">
                                <div class="d-flex justify-content-between small mt-1">
                                    <span>@lang('Tax') {{ showAmount($taxRate, currencyFormat:false) }}%</span>
                                    <span>{{ showAmount($taxAmount) }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="d-flex flex-wrap justify-content-between border-top mt-2 pt-4">
                            <h5 class="text-center fw-bold">@lang('Total')</h5>
                            <h5 class="justify-content-end d-flex">
                                <span class="finalAmounts">{{ showAmount($checkoutPayable) }}</span>
                            </h5>
                        </div>
                    </div>

                    @if (count($carts))
                        @auth
                            <div class="text-center mt-3">
                                <form action="{{ route('user.invoice.create') }}" method="post" class="checkoutPaymentForm">
                                    @csrf
                                    <input type="hidden" name="method_code">
                                    <input type="hidden" name="currency">
                                    <div class="form-group mb-2 text-start">
                                        <select name="payment" class="form-select form--control h-45 checkoutGateway whm-payment-select-hidden" required>
                                            <option value="">@lang('Select payment method')</option>
                                            @if (gs('deposit_module'))
                                                <option value="wallet" @selected($canPayWithBalance)>
                                                    @lang('Wallet Balance') - {{ showAmount(auth()->user()->balance) }}
                                                </option>
                                            @endif
                                            @foreach ($gatewayCurrency as $data)
                                                <option value="{{ $data->method_code }}" data-gateway="{{ $data }}">
                                                    {{ __($data->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="whm-payment-options" data-payment-options>
                                            @if (gs('deposit_module'))
                                                <button type="button" class="whm-payment-card {{ $canPayWithBalance ? 'active' : 'disabled' }}" data-payment-option="wallet" @disabled(!$canPayWithBalance)>
                                                    <span>@lang('Account balance')</span>
                                                    <strong>{{ showAmount(auth()->user()->balance) }}</strong>
                                                    <small>
                                                        @if ($canPayWithBalance)
                                                            @lang('Pay instantly from your available balance.')
                                                        @else
                                                            @lang('Balance is lower than this order total.')
                                                        @endif
                                                    </small>
                                                </button>
                                            @endif
                                            @foreach ($gatewayCurrency as $data)
                                                <button type="button" class="whm-payment-card" data-payment-option="{{ $data->method_code }}">
                                                    <span>{{ __($data->name) }}</span>
                                                    <strong>@lang('Gateway')</strong>
                                                    <small>@lang('Continue securely with this payment method.')</small>
                                                </button>
                                            @endforeach
                                        </div>
                                        @if (gs('deposit_module') && !$canPayWithBalance)
                                            <small class="text-muted d-block mt-1">
                                                @lang('Your wallet balance is lower than this order total. Choose a gateway to continue.')
                                            </small>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn bg--base btn-lg text-white w-100">
                                        {{ $canPayWithBalance ? __('Pay Now') : __('Checkout') }} <i class="la la-arrow-circle-right"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="whm-checkout-auth mt-4">
                                <div class="whm-checkout-auth__head">
                                    <span>@lang('Secure checkout')</span>
                                    <strong>@lang('Continue with your account')</strong>
                                </div>
                                <ul class="nav nav-pills whm-checkout-tabs" id="checkoutAuthTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="checkout-register-tab" data-bs-toggle="pill" data-bs-target="#checkout-register" type="button" role="tab">
                                            @lang('Create account')
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="checkout-login-tab" data-bs-toggle="pill" data-bs-target="#checkout-login" type="button" role="tab">
                                            @lang('Sign in')
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content mt-3">
                                    <div class="tab-pane fade show active" id="checkout-register" role="tabpanel">
                                        <form action="{{ route('shopping.cart.checkout.account') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="checkout_mode" value="register">
                                            <input type="hidden" name="method_code">
                                            <input type="hidden" name="currency">
                                            <div class="form-group mb-2">
                                                <input type="text" class="form-control form--control h-45" name="full_name" value="{{ old('full_name') }}" placeholder="@lang('Full name')" required>
                                            </div>
                                            <div class="form-group mb-2">
                                                <input type="email" class="form-control form--control h-45" name="email" value="{{ old('email') }}" placeholder="@lang('Email address')" required>
                                            </div>
                                            <div class="form-group mb-2">
                                                <input type="tel" class="form-control form--control h-45" name="mobile" value="{{ old('mobile') }}" placeholder="@lang('Phone number')" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="password" class="form-control form--control h-45 @if (gs('secure_password')) secure-password @endif" name="password" placeholder="@lang('Password')" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <select name="payment" class="form-select form--control h-45 checkoutGateway whm-payment-select-hidden" required>
                                                    <option value="">@lang('Select payment method')</option>
                                                    @foreach ($gatewayCurrency as $data)
                                                        <option value="{{ $data->method_code }}" data-gateway="{{ $data }}">
                                                            {{ __($data->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="whm-payment-options" data-payment-options>
                                                    @foreach ($gatewayCurrency as $data)
                                                        <button type="button" class="whm-payment-card" data-payment-option="{{ $data->method_code }}">
                                                            <span>{{ __($data->name) }}</span>
                                                            <strong>@lang('Gateway')</strong>
                                                            <small>@lang('Create your account and continue securely.')</small>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn--base btn-lg w-100">
                                                @lang('Create account & checkout')
                                            </button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="checkout-login" role="tabpanel">
                                        <form action="{{ route('shopping.cart.checkout.account') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="checkout_mode" value="login">
                                            <input type="hidden" name="method_code">
                                            <input type="hidden" name="currency">
                                            <div class="form-group mb-2">
                                                <input type="text" class="form-control form--control h-45" name="username" value="{{ old('username') }}" placeholder="@lang('Email or username')" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="password" class="form-control form--control h-45" name="password" placeholder="@lang('Password')" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <select name="payment" class="form-select form--control h-45 checkoutGateway whm-payment-select-hidden" required>
                                                    <option value="">@lang('Select payment method')</option>
                                                    @if (gs('deposit_module'))
                                                        <option value="wallet">@lang('Wallet Balance')</option>
                                                    @endif
                                                    @foreach ($gatewayCurrency as $data)
                                                        <option value="{{ $data->method_code }}" data-gateway="{{ $data }}">
                                                            {{ __($data->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="whm-payment-options" data-payment-options>
                                                    @if (gs('deposit_module'))
                                                        <button type="button" class="whm-payment-card" data-payment-option="wallet">
                                                            <span>@lang('Account balance')</span>
                                                            <strong>@lang('Auto-check')</strong>
                                                            <small>@lang('After sign in, we will use your balance if it is enough.')</small>
                                                        </button>
                                                    @endif
                                                    @foreach ($gatewayCurrency as $data)
                                                        <button type="button" class="whm-payment-card" data-payment-option="{{ $data->method_code }}">
                                                            <span>{{ __($data->name) }}</span>
                                                            <strong>@lang('Gateway')</strong>
                                                            <small>@lang('Use this method after signing in.')</small>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn--base btn-lg w-100">
                                                @lang('Sign in & checkout')
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .capitalize {
            text-transform: capitalize;
        }

        .cart_child:nth-child(odd) .card-body {
            background-color: #00000008;
        }

        .whm-checkout-auth {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px;
            background: #fff;
        }

        .whm-checkout-auth__head {
            display: grid;
            gap: 2px;
            margin-bottom: 12px;
        }

        .whm-checkout-auth__head span {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .whm-checkout-auth__head strong {
            color: #0f172a;
            font-size: 16px;
        }

        .whm-checkout-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            padding: 4px;
            border-radius: 12px;
            background: #f8fafc;
        }

        .whm-checkout-tabs .nav-item,
        .whm-checkout-tabs .nav-link {
            width: 100%;
        }

        .whm-checkout-tabs .nav-link {
            border-radius: 10px;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            padding: 9px 8px;
        }

        .whm-checkout-tabs .nav-link.active {
            background: #2563eb;
            color: #fff;
        }

        .whm-payment-select-hidden {
            height: 1px !important;
            opacity: 0 !important;
            pointer-events: none !important;
            position: absolute !important;
            width: 1px !important;
        }

        .whm-payment-options {
            display: grid;
            gap: 10px;
            grid-template-columns: 1fr;
        }

        .whm-payment-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            color: #0f172a;
            display: block;
            min-height: 96px;
            padding: 14px;
            position: relative;
            text-align: left;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease, background-color .2s ease;
            width: 100%;
        }

        .whm-payment-card:hover,
        .whm-payment-card.active {
            background: #f8fbff;
            border-color: #2563eb;
            box-shadow: 0 12px 26px rgba(15, 23, 42, .07);
            transform: translateY(-1px);
        }

        .whm-payment-card.active::after {
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

        .whm-payment-card.disabled,
        .whm-payment-card:disabled {
            background: #f8fafc;
            border-color: #e2e8f0;
            box-shadow: none;
            color: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }

        .whm-payment-card span {
            color: #64748b;
            display: block;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .03em;
            padding-right: 28px;
            text-transform: uppercase;
        }

        .whm-payment-card strong {
            color: #0f172a;
            display: block;
            font-size: 17px;
            font-weight: 800;
            line-height: 1.25;
            margin-top: 8px;
        }

        .whm-payment-card small {
            color: #64748b;
            display: block;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.45;
            margin-top: 4px;
        }

        .whm-payment-card:focus-visible {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .14);
            outline: none;
        }

        @media only screen and (max-width: 767px) {
            .remove_cart {
                display: inline-block !important;
            }

            .remove_icon {
                display: none;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            function syncPaymentCards(form, value) {
                form.find('[data-payment-option]').removeClass('active').attr('aria-pressed', 'false');

                if (value) {
                    form.find('[data-payment-option="' + value + '"]').addClass('active').attr('aria-pressed', 'true');
                }
            }

            $(document).on('click', '[data-payment-option]', function() {
                var button = $(this);

                if (button.is(':disabled') || button.hasClass('disabled')) {
                    return;
                }

                var form = button.closest('form');
                var value = button.data('payment-option');

                form.find('.checkoutGateway').val(value).trigger('change');
            });

            $('.checkoutGateway').on('change', function() {
                var form = $(this).closest('form');
                var gateway = $(this).val();
                var resource = $(this).find('option:selected').data('gateway');

                form.find('input[name=method_code]').val('');
                form.find('input[name=currency]').val('');

                if (gateway && gateway != 'wallet' && resource) {
                    form.find('input[name=method_code]').val(resource.method_code);
                    form.find('input[name=currency]').val(resource.currency);
                }

                syncPaymentCards(form, gateway);
            }).trigger('change');
        })(jQuery);
    </script>
@endpush

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
