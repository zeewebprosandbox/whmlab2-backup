@extends($activeTemplate . 'layouts.master_side_bar')

@section('content')
    <div class="col-lg-9 whm-client-dashboard">

        <div class="notice"></div>

        @if ($user->kv == 0 || $user->kv == 2)
            @php
                $kyc = @getContent('kyc.content', true);
            @endphp

            <div class="row">
                <div class="col-md-12">
                    <div class="card card custom--card style-two mb-4 mb-4 bg--navajowhite">
                        <div class="card-body">
                            @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
                                <div class="justify-content-between d-flex flex-wrap gap-4">
                                    <div class="d-flex justify-content-between flex-wrap align-items-center gap-2">
                                        <h6>@lang('KYC Documents Rejected')</h6>
                                        <button class="btn btn-outline-secondary btn--sm" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">
                                            @lang('Show Reason')
                                        </button>
                                    </div>
                                    <a href="{{ route('user.kyc.form') }}" class="text-bold text--primary">@lang('Click Here to Re-submit Documents')</a>
                                </div>
                                <hr>
                                <p>{{ __(@$kyc->data_values->kyc_reject) }}</p>
                            @elseif($user->kv == Status::KYC_UNVERIFIED)
                                <div class="justify-content-between d-flex flex-wrap">
                                    <h6>@lang('KYC Verification Required')</h6>
                                    <a href="{{ route('user.kyc.form') }}" class="text-bold text--primary">@lang('Click Here to Submit Documents')</a>
                                </div>
                                <hr>
                                <p>{{ __(@$kyc->data_values->kyc_required) }}</p>
                            @elseif($user->kv == Status::KYC_PENDING)
                                <div class="justify-content-between d-flex flex-wrap">
                                    <h6>@lang('KYC Verification Pending')</h6>
                                    <a href="{{ route('user.kyc.data') }}" class="text-bold text--primary">@lang('See KYC Data')</a>
                                </div>
                                <hr>
                                <p>{{ __(@$kyc->data_values->kyc_pending) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <section class="tw-client-hero">
            <div>
                <span class="tw-kicker">@lang('Client workspace')</span>
                <h2 class="tw-heading-lg mt-4 mb-3">@lang('Everything about your hosting account, in one place.')</h2>
                <p class="tw-copy-lg">@lang('Track services, domains, invoices, support, and account balance without digging through old billing screens.')</p>
            </div>
            <div class="flex flex-wrap justify-start gap-2 lg:justify-end">
                <a href="{{ route('service.category') }}?all" class="tw-button tw-button-primary">
                    <i data-lucide="plus-circle"></i> @lang('Order Service')
                </a>
                <a href="{{ route('ticket.open') }}" class="tw-button tw-button-secondary">
                    <i data-lucide="message-circle-plus"></i> @lang('Open Ticket')
                </a>
            </div>
        </section>

        <div class="card custom--card whm-support-pin-card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="mb-1 text--secondary">@lang('Support PIN')</p>
                        <h3 class="mb-1 whm-support-pin-code">{{ $supportPin->plain_code }}</h3>
                        <small class="text-muted">
                            @lang('Share this only with support. Expires')
                            {{ diffForHumans($supportPin->expires_at) }}.
                        </small>
                    </div>
                    <form action="{{ route('user.support.pin.regenerate') }}" method="post">
                        @csrf
                        <button class="btn btn--base" type="submit">
                            <i class="fas fa-sync-alt"></i> @lang('Regenerate')
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tw-metric-grid">
            <a href="{{ route('user.transactions') }}" class="tw-metric-card">
                <span class="tw-metric-top">
                    <span class="tw-metric-icon"><i data-lucide="wallet"></i></span>
                    <small class="tw-metric-label">@lang('Balance')</small>
                </span>
                <strong class="tw-metric-value">{{ showAmount($user->balance) }}</strong>
                <span class="tw-metric-action">@lang('View transactions') <i data-lucide="arrow-up-right"></i></span>
            </a>
            <a href="{{ route('user.deposit.history') }}" class="tw-metric-card">
                <span class="tw-metric-top">
                    <span class="tw-metric-icon"><i data-lucide="receipt"></i></span>
                    <small class="tw-metric-label">@lang('Deposits')</small>
                </span>
                <strong class="tw-metric-value">{{ @$user->deposits->count() }}</strong>
                <span class="tw-metric-action">@lang('Deposit history') <i data-lucide="arrow-up-right"></i></span>
            </a>
            <a href="{{ route('user.service.list') }}" class="tw-metric-card">
                <span class="tw-metric-top">
                    <span class="tw-metric-icon"><i data-lucide="hard-drive"></i></span>
                    <small class="tw-metric-label">@lang('Services')</small>
                </span>
                <strong class="tw-metric-value">{{ $totalService }}</strong>
                <span class="tw-metric-action">@lang('Manage services') <i data-lucide="arrow-up-right"></i></span>
            </a>
            <a href="{{ route('user.domain.list') }}" class="tw-metric-card">
                <span class="tw-metric-top">
                    <span class="tw-metric-icon"><i data-lucide="globe"></i></span>
                    <small class="tw-metric-label">@lang('Domains')</small>
                </span>
                <strong class="tw-metric-value">{{ $totalDomain }}</strong>
                <span class="tw-metric-action">@lang('Manage domains') <i data-lucide="arrow-up-right"></i></span>
            </a>
            <a href="{{ route('ticket.index') }}" class="tw-metric-card">
                <span class="tw-metric-top">
                    <span class="tw-metric-icon"><i data-lucide="messages-square"></i></span>
                    <small class="tw-metric-label">@lang('Tickets')</small>
                </span>
                <strong class="tw-metric-value">{{ $totalTicket }}</strong>
                <span class="tw-metric-action">@lang('Open support') <i data-lucide="arrow-up-right"></i></span>
            </a>
            <a href="{{ route('user.invoice.list') }}" class="tw-metric-card">
                <span class="tw-metric-top">
                    <span class="tw-metric-icon"><i data-lucide="file-text"></i></span>
                    <small class="tw-metric-label">@lang('Invoices')</small>
                </span>
                <strong class="tw-metric-value">{{ $totalInvoice }}</strong>
                <span class="tw-metric-action">@lang('View billing') <i data-lucide="arrow-up-right"></i></span>
            </a>
        </div>

        <div class="row g-3 whm-client-action-grid">
            <div class="col-xl-6 col-lg-6">
                <div class="card custom-border-top-dark h-100 whm-command-card whm-command-card--alert">
                    <div class="card-body">
                        <div class="whm-command-card__head">
                            <span class="whm-command-card__icon"><i data-lucide="receipt-text"></i></span>
                            <div>
                                <h5>@lang('Overdue Invoices')</h5>
                                <p>@lang('Billing health')</p>
                            </div>
                            <a class="btn btn--xs btn--base ms-auto" href="{{ route('user.invoice.list') }}">
                                <i class="fas fa-list"></i> @lang('View All')
                            </a>
                        </div>
                        <p class="whm-command-card__copy">
                            @lang('You have') <strong>{{ $totalOverDueInvoice->total }}</strong>
                            @lang('overdue invoice(s) with a total balance due of')
                            <strong>{{ showAmount($totalOverDueInvoice->totalDue) }}</strong>.
                            @lang('Pay them now to avoid any interruptions in service').
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="card custom-border-top-dark h-100 whm-command-card">
                    <div class="card-body">
                        <div class="whm-command-card__head">
                            <span class="whm-command-card__icon"><i data-lucide="boxes"></i></span>
                            <div>
                                <h5>@lang('Products/Services')</h5>
                                <p>@lang('Hosting workspace')</p>
                            </div>
                            <a class="btn btn--xs btn--base ms-auto" href="{{ route('user.service.list') }}"> <i class="fas fa-list"></i> @lang('View All')</a>
                        </div>
                        <p class="whm-command-card__copy">
                            @lang('It appears you do not have any products/services with us yet').
                            <a href="{{ route('service.category') }}?all">@lang('Place an order to get started')</a>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="card custom-border-top-dark h-100 whm-command-card">
                    <div class="card-body">
                        <div class="whm-command-card__head">
                            <span class="whm-command-card__icon"><i data-lucide="life-buoy"></i></span>
                            <div>
                                <h5>@lang('Support Tickets')</h5>
                                <p>@lang('Help desk')</p>
                            </div>
                            <a class="btn btn--xs btn--base ms-auto" href="{{ route('ticket.index') }}"> <i class="fas fa-list"></i> @lang('View All')</a>
                        </div>
                        <p class="whm-command-card__copy">
                            @lang('No Recent Tickets Found. If you need any help'), <a href="{{ route('ticket.open') }}">@lang('please open a ticket')</a>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="card custom-border-top-dark h-100 whm-command-card whm-command-card--search">
                    <div class="card-body">
                        <div class="whm-command-card__head">
                            <span class="whm-command-card__icon"><i data-lucide="globe-2"></i></span>
                            <div>
                                <h5>@lang('Register New Domain')</h5>
                                <p>@lang('Find your next address')</p>
                            </div>
                        </div>
                        <form action="" class="form mt-3">
                            <div class="form-group position-relative mb-0">
                                <div class="domain-search-icon"><i class="fas fa-search"></i></div>
                                <input class="form-control form--control h-45" type="text" name="domain" placeholder="@lang('Domain name or keyword')" required>
                                <div class="domain-search-icon-reset">
                                    <button class="btn btn--base btn--sm" type="submit">@lang('Search')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $user->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.form').on('submit', function(e) {
                e.preventDefault();
                var domain = $(this).find('input[name=domain]').val();
                window.location.href = "{{ route('register.domain') }}?domain=" + domain;
            })
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .whm-support-pin-card {
            border-left: 4px solid #2563eb !important;
        }

        .whm-support-pin-code {
            color: #0f172a;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: clamp(28px, 5vw, 42px);
            font-weight: 900;
            letter-spacing: .16em;
        }
    </style>
@endpush
