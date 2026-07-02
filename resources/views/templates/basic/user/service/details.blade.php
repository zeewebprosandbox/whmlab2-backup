@extends($activeTemplate.'layouts.master')

@section('content')
    <div class="pt-60 pb-60 bg--light section-full">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
            
                    <div class="row gy-4">  
                        <div class="col-lg-5">
                            <div class="card custom--card style-two h-100">
                                <div class="card-body">
                                    <div class="row">
                                        @if(!$product->server_group_id) 
                                            <div class="col-md-12">
                                                <div class="new-card">
                                                    <span class="whm-service-detail-icon">
                                                        <i data-lucide="{{ $product->product_type == 3 ? 'server' : ($product->product_type == 4 ? 'archive' : 'hard-drive') }}"></i>
                                                    </span>
                                                    <h3 class="text-center">{{ __(@$service->product->name) }}</h3>
                                                    <h4 class="text-center">{{ __(@$service->product->serviceCategory->name) }}</h4>
                                                    <span class="text-center d-block">
                                                        @php echo $service->showStatus; @endphp
                                                    </span>
                                                </div>
                    
                                                @if($status == 1)
                                                    <button class="btn btn--danger btn--sm w-100 mt-2 {{ $service->cancelRequest ? 'disabled' : 'cancenRequest' }}">  
                                                        @lang('Request Cancellation') 
                                                    </button> 
                                                @endif
                    
                                                @if($service->cancelRequest && $service->cancelRequest->status == 2)
                                                    <small class="text-center w-100 d-block mt-2 text--danger">
                                                        @lang('There is an outstanding cancellation request for this product/service')
                                                    </small>
                                                @endif
                                            </div> 
                                        @else 
                                            @if($status == 1)
                                                @php
                                                    $panelLoginLabel = @$serverGroup->type == 4 ? 'Login ZodPanel' : 'Login to '.(@$serverGroup->getType ?? 'Control Panel');
                                                    $zodFeatures = is_array($product->whmpanel_features) ? $product->whmpanel_features : [];
                                                    $zodWebmailEnabled = (bool) data_get($zodFeatures, 'features.webmail', false);
                                                @endphp
                                                <div class="col-md-12">
                                                    <div class="new-card zod-service-card text-center">
                                                        <span class="whm-service-detail-icon">
                                                            <i data-lucide="server-cog"></i>
                                                        </span>
                                                        <span class="zod-service-eyebrow">{{ __($product->serviceCategory->name) }}</span>
                                                        <h4 class="mb-2">{{ __($product->name) }}</h4>
                                                        <div>
                                                            @if($service->domain)
                                                                <a class="zod-service-domain" href="http://{{ $service->domain }}" target="_blank">www.{{ $service->domain }}</a>
                                                            @endif
                                                            <div class="zod-service-actions">
                                                                <a class="btn btn--success btn--xs" href="http://{{ $service->domain }}" target="_blank">
                                                                    @lang('Visit Website')
                                                                </a>
                                                                @if($hasAccount)
                                                                    <a 
                                                                        class="btn btn--primary btn--xs zod-panel-login" 
                                                                        href="{{ route('user.login.hosting', $service->id) }}"
                                                                        target="_blank"
                                                                        rel="noopener"
                                                                    >
                                                                        <span class="zod-login-spinner" aria-hidden="true"></span>
                                                                        @lang($panelLoginLabel)
                                                                    </a>
                                                                @endif
                                                                @if(@$serverGroup->type == 4 && $zodWebmailEnabled && $service->domain)
                                                                    <a class="btn btn--info btn--xs" href="https://webmail.{{ $service->domain }}/" target="_blank" rel="noopener">
                                                                        @lang('Open Webmail')
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if(@$serverGroup->type == 1 && $diskUsagePercent)
                                                        <div class="new-card mt-4">
                                                            <h4 class="text-center mb-3">@lang('Disk Usage')</h4>
                                                            <div class="row"> 
                                                                <div class="col-lg-12 form-group">
                                                                    <div class="progress custom--progress progress-bg">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                                                            style="width: {{ $diskUsagePercent }};" 
                                                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                                        </div>
                                                                        <div class="progress-text text-white">
                                                                            {{ $diskUsagePercent }}
                                                                        </div>
                                                                        </div>
                                                                    <small>
                                                                        {{ @$accountSummary['diskused'] }} / {{ @$accountSummary['disklimit'] }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                            @elseif($status == 2)
                                                <div class="col-md-12">
                                                    <div class="new-card bg--warning">
                                                        <h3 class="mb-3">@lang('Pending')</h3>
                                                        <small class="d-block">@lang('This hosting package is currently Pending')</small>
                                                        <small>@lang('You cannot begin using this hosting account until it is activated')</small>
                                                    </div>
                                                </div>
                                            @elseif($status == 3)
                                                <div class="col-md-12">
                                                    <div class="new-card bg--warning">
                                                        <h3 class="mb-3">@lang('Suspended')</h3>
                                                        <small class="d-block">@lang('This hosting package is currently Suspended')</small>
                                                        <small>@lang('You cannot continue to use or manage this package until it is reactivated')</small>
                                                    </div>
                                                </div>
                                            @elseif($status == 4)
                                                <div class="col-md-12">
                                                    <div class="new-card bg--warning">
                                                        <h3 class="mb-3">@lang('Terminated')</h3>
                                                        <small>@lang('This hosting package is currently Terminated')</small>
                                                    </div>
                                                </div>
                                            @elseif($status == 5)
                                                <div class="col-md-12">
                                                    <div class="new-card bg--warning">
                                                        <h3 class="mb-3">@lang('Cancelled')</h3>
                                                        <small>@lang('This hosting package is currently Cancelled')</small>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 text-center">
                            <div class="card custom--card style-two h-100">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush text-center">
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('Registration Date')
                                            <strong>{{ @$service->reg_date ? showDateTime(@$service->reg_date, 'd/m/Y') : 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('First Payment Amount')
                                            <strong>{{ showAmount($service->first_payment_amount) }}</strong>
                                        </li>
                                        @if($service->billing != 1)
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                @lang('Recurring Amount')
                                                <strong>{{ showAmount($service->recurring_amount) }}</strong>
                                            </li>
                                        @endif
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('Billing Cycle')
                                            <strong>{{ billingCycle(@$service->billing_cycle, true)['showText'] }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('Next Due Date')
                                            <strong>
                                                @if($service->billing_cycle == 0)
                                                    @lang('N/A')
                                                @else 
                                                    {{ @$service->next_due_date ? showDateTime(@$service->next_due_date, 'd/m/Y') : 'N/A' }}
                                                @endif
                                            </strong>
                                        </li>
                                    </ul>
                                </div>  
                            </div>                              
                        </div> 
                    </div>
    
                    @if(count($product->getConfigs))
                        <h4 class="mt-4 text-center">@lang('Configurable Options')</h4>
                        <div class="card custom--card style-two w-100 mt-4">
                            <div class="card-body">    
                                <ul class="list-group list-group-flush text-center">
                                    @foreach($product->getConfigs as $config)
                                        @forelse($config->group->options as $option)  
                                            <li class="list-group-item d-flex justify-content-between">
                                                {{ __(@$option->name) }}
                                                <strong>
                                                    {{ @$service->hostingConfigs->where('configurable_group_option_id', $option->id)->first()->option->name ?? __('N/A') }}
                                                </strong>
                                            </li>
                                        @empty
                                            {{ __(@$emptyMessage) }}
                                        @endforelse
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if($service->status == 1 && $nameservers->count())
                        <div class="card custom--card style-two w-100 mt-4 whm-dns-card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <p class="mb-1 text--secondary">@lang('Provider nameservers')</p>
                                        <h4 class="mb-0">@lang('Point your domain to these nameservers')</h4>
                                    </div>
                                    <span class="badge badge--primary">@lang('Use at your registrar')</span>
                                </div>

                                <div class="whm-dns-grid">
                                    @foreach($nameservers as $record)
                                        <div class="whm-dns-record">
                                            <span>{{ $record['label'] }}</span>
                                            <strong>{{ $record['host'] }}</strong>
                                            @if($record['ip'])
                                                <small>{{ $record['ip'] }}</small>
                                            @endif
                                            <button type="button" class="whm-copy-record" data-copy="{{ $record['host'] }}" title="@lang('Copy')">
                                                <i data-lucide="copy"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mb-0 mt-3 text-muted">@lang('Update these at your current domain registrar after your service is active.')</p>
                            </div>
                        </div>
                    @endif

                    @if(@$serverGroup->type == 4 && $status == 1 && $hasAccount && $service->domain)
                        @php
                            $zodFeatures = isset($zodFeatures) && is_array($zodFeatures) ? $zodFeatures : (is_array($product->whmpanel_features) ? $product->whmpanel_features : []);
                            $zodWebmailEnabled = isset($zodWebmailEnabled) ? $zodWebmailEnabled : (bool) data_get($zodFeatures, 'features.webmail', false);
                            $zodBlockers = collect(data_get($zodPanelDiagnostics, 'data.blockers', []))->filter()->values();
                            $zodWebmailUrl = data_get($zodPanelDiagnostics, 'data.webmail.url')
                                ?: data_get($zodPanelDiagnostics, 'data.webmail_url')
                                ?: 'https://webmail.'.$service->domain.'/';
                        @endphp
                        <div class="card custom--card style-two w-100 mt-4 zod-health-card">
                            <div class="card-body">
                                <div class="zod-health-head">
                                    <div>
                                        <span>@lang('ZodPanel Health')</span>
                                        <h4>@lang('Website, SSL, and webmail readiness')</h4>
                                    </div>
                                    <a href="{{ route('user.login.hosting', $service->id) }}" target="_blank" rel="noopener" class="btn btn--primary btn--sm zod-panel-login">
                                        <span class="zod-login-spinner" aria-hidden="true"></span>
                                        @lang('Open Panel')
                                    </a>
                                </div>

                                <div class="zod-health-grid">
                                    <div class="zod-health-item">
                                        <small>@lang('Primary domain')</small>
                                        <strong>{{ $service->domain }}</strong>
                                    </div>
                                    <div class="zod-health-item">
                                        <small>@lang('Webmail')</small>
                                        <a href="{{ $zodWebmailUrl }}" target="_blank" rel="noopener">{{ str_replace(['https://', 'http://'], '', rtrim($zodWebmailUrl, '/')) }}</a>
                                    </div>
                                </div>

                                @if($zodBlockers->count())
                                    <div class="zod-health-alert">
                                        <strong>@lang('Needs attention')</strong>
                                        <ul>
                                            @foreach($zodBlockers->take(3) as $blocker)
                                                <li>{{ __($blocker) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="zod-health-ok">
                                        <i data-lucide="shield-check"></i>
                                        <span>@lang('No active DNS, SSL, or webmail blockers reported.')</span>
                                    </div>
                                @endif

                                @if($zodWebmailEnabled)
                                    <form action="{{ route('user.service.zodpanel.webmail.repair', $service->id) }}" method="POST" class="zod-health-actions">
                                        @csrf
                                        <a href="{{ $zodWebmailUrl }}" target="_blank" rel="noopener" class="btn btn--info btn--sm">
                                            @lang('Open Webmail')
                                        </a>
                                        <button type="submit" class="btn btn--base btn--sm">
                                            @lang('Repair Webmail')
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($product->product_type == 3)
                        <h3 class="mt-4 text-center">@lang('Server Information')</h3>
                        <div class="card custom--card style-two w-100 mt-4">
                            <div class="card-body">   
                                <ul class="list-group list-group-flush text-center">
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Hostname')
                                        <strong> 
                                            {{ $service->domain ?? 'N/A' }}  
                                        </strong> 
                                    </li> 
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Primary IP')
                                        <strong>
                                            {{ $service->dedicated_ip ?? 'N/A' }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Nameservers')
                                        <strong>
                                            {{ $service->ns1 ?? 'N/A' }}, {{ $service->ns2 ?? 'N/A' }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Assigned IPs')
                                        <strong>
                                            @php echo nl2br($service->assigned_ips); @endphp 
                                        </strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @if(!$product->server_group_id && $status == 1)
        <div class="modal fade" id="cancenRequest" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Briefly Describe your reason for Cancellation')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('user.service.cancel.request') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $service->id }}">
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12 form-group">
                                    <label for="cancellation_type">@lang('Cancellation Type')</label>
                                    <select name="cancellation_type" class="form-control form--control h-45 form-select" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach($cancelRequestTypes as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="reason">@lang('Reason')</label>
                                    <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('style')
    <style>
        .new-card {
            margin: 0;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 30px;
            line-height: 1.35;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        }

        .zod-service-card {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(37, 99, 235, .08), rgba(20, 184, 166, .05)),
                #ffffff;
        }

        .zod-service-eyebrow {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .zod-service-domain {
            display: inline-flex;
            max-width: 100%;
            margin-top: 4px;
            color: #0f766e;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .zod-service-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 18px;
        }

        .zod-service-actions .btn {
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 7px;
        }

        .zod-panel-login.is-loading {
            pointer-events: none;
            opacity: .88;
        }

        .zod-login-spinner {
            display: none;
            width: 13px;
            height: 13px;
            border: 2px solid rgba(255, 255, 255, .45);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: zod-spin .7s linear infinite;
        }

        .zod-panel-login.is-loading .zod-login-spinner {
            display: inline-block;
        }

        @keyframes zod-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .whm-service-detail-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: #eff6ff;
            color: #2563eb;
        }
        .whm-service-detail-icon svg {
            width: 30px;
            height: 30px;
        }
        .progress-bg{
            background: #c5cace;
        }
        .custom--progress {
            position: relative;
        }
        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            line-height: 0;
            font-size: .75rem;
        }

        .whm-dns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 12px;
        }

        .whm-dns-record {
            position: relative;
            display: grid;
            gap: 4px;
            padding: 14px 46px 14px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            text-align: left;
        }

        .whm-dns-record span {
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .whm-dns-record strong {
            color: #0f172a;
            font-size: 14px;
        }

        .whm-dns-record small {
            color: #64748b;
        }

        .whm-copy-record {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbeafe;
            border-radius: 9px;
            background: #eff6ff;
            color: #2563eb;
        }

        .whm-copy-record.copied {
            background: #dcfce7;
            border-color: #bbf7d0;
            color: #15803d;
        }

        .zod-health-card {
            text-align: left;
        }

        .zod-health-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .zod-health-head span {
            display: block;
            color: #2563eb;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .zod-health-head h4 {
            margin: 5px 0 0;
            color: #0f172a;
            font-size: 18px;
            font-weight: 800;
        }

        .zod-health-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .zod-health-item {
            min-width: 0;
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
        }

        .zod-health-item small {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .zod-health-item strong,
        .zod-health-item a {
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .zod-health-alert,
        .zod-health-ok {
            margin-top: 14px;
            padding: 14px;
            border-radius: 12px;
        }

        .zod-health-alert {
            border: 1px solid #fed7aa;
            background: #fff7ed;
            color: #9a3412;
        }

        .zod-health-alert strong {
            display: block;
            margin-bottom: 6px;
            color: #9a3412;
        }

        .zod-health-alert ul {
            display: grid;
            gap: 5px;
            margin: 0;
            padding-left: 18px;
        }

        .zod-health-ok {
            display: flex;
            align-items: center;
            gap: 9px;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            font-weight: 700;
        }

        .zod-health-ok svg {
            flex: 0 0 auto;
            width: 18px;
            height: 18px;
        }

        .zod-health-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .zod-health-actions .btn,
        .zod-health-head .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            min-height: 38px;
        }

        .iziToast {
            border-radius: 8px !important;
            box-shadow: 0 16px 34px rgba(15, 23, 42, .14) !important;
        }

        @media (max-width: 575px) {
            .new-card {
                padding: 22px 18px;
            }

            .zod-service-actions .btn {
                width: 100%;
            }

            .zod-health-head {
                display: block;
            }

            .zod-health-head .btn {
                width: 100%;
                margin-top: 14px;
            }

            .zod-health-grid {
                grid-template-columns: 1fr;
            }

            .zod-health-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.zod-panel-login').on('click', function() {
                var button = $(this);
                button.addClass('is-loading');
                notify('info', 'Preparing your ZodPanel session...');
            });

            $('.cancenRequest').on('click', function(){
                var modal = $('#cancenRequest');
                modal.modal('show');
            });

            $('.whm-copy-record').on('click', function() {
                var button = $(this);
                var value = button.data('copy');
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(value);
                }
                button.addClass('copied');
                setTimeout(function() {
                    button.removeClass('copied');
                }, 900);
            });

        })(jQuery);
    </script>
@endpush 
