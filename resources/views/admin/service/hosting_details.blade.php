@extends('admin.layouts.app')

@section('panel')
    <form action="{{ route('admin.order.hosting.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ @$hosting->id }}">
        <div class="row mb-none-30 mb-1">

            @if (session()->has('response'))
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            @php echo @session()->get('response')->metadata->output->raw; @endphp
                        </div>
                    </div>
                </div>
            @endif

            @if ($product->server_group_id)
                <div class="col-lg-12">
                    <div class="row mb-none-30 mb-3">
                        <div class="col-md-12 form-group">
                            <h6 class="text-center mb-3">@lang('Module Commands')</h6>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="1" data-type="1" type="button">
                                                <i class="las la-plus"></i>@lang('Create')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="2" data-type="2" type="button">
                                                <i class="las la-ban"></i>@lang('Suspend')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="3" data-type="3" type="button">
                                                <i class="las la-undo"></i>@lang('Unsuspend')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="4" data-type="4" type="button">
                                                <i class="las la-trash"></i>@lang('Terminate')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="5" data-type="5" type="button">
                                                <i class="las la-exchange-alt"></i>@lang('Change Package')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="6" data-type="6" type="button">
                                                <i class="las la-key"></i>@lang('Change Password')
                                            </button>
                                        </div>
                                        @if ($hosting->suspend_reason)
                                            <div class="col-md-12 mt-3">
                                                <div class="alert alert-warning p-3 d-block" role="alert">
                                                    <div class="border-bottom pb-2 text-center">
                                                        <h6 class="alert-heading d-xl-inline">@lang('Account Suspended')</h6>
                                                        <small>({{ showDateTime($hosting->suspend_date) }})</small>
                                                    </div>
                                                    <p class="pt-2">{{ $hosting->suspend_reason }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (@$serverGroup->type == 4 && $hasAccount)
                @php
                    $zodBlockers = collect(data_get($zodPanelDiagnostics, 'data.blockers', []))->filter()->values();
                    $zodWebmailUrl = data_get($zodPanelDiagnostics, 'data.webmail.url')
                        ?: data_get($zodPanelDiagnostics, 'data.webmail_url')
                        ?: ($hosting->domain ? 'https://webmail.'.$hosting->domain.'/' : null);
                    $zodPhpCurrent = data_get($zodPanelPhp, 'data.current');
                    $zodPhpBackends = collect(data_get($zodPanelPhp, 'data.backends', []))->map(function ($backend) {
                        if (is_array($backend)) {
                            return [
                                'template' => $backend['template'] ?? $backend['name'] ?? $backend['value'] ?? null,
                                'label' => $backend['label'] ?? $backend['name'] ?? $backend['template'] ?? $backend['value'] ?? null,
                                'switchable' => $backend['switchable'] ?? true,
                            ];
                        }

                        return ['template' => $backend, 'label' => $backend, 'switchable' => true];
                    })->filter(fn ($backend) => !empty($backend['template']) && $backend['switchable'])->values();
                @endphp
                <div class="col-lg-12">
                    <div class="zod-ops-panel">
                        <div class="zod-ops-panel__head">
                            <div>
                                <span>@lang('ZodPanel Operations')</span>
                                <h5>@lang('Live service health and repair controls')</h5>
                            </div>
                            <a href="{{ route('admin.order.hosting.zodpanel.diagnostics', $hosting->id) }}" class="btn btn-sm btn-outline--primary">
                                <i class="las la-sync"></i>@lang('Refresh')
                            </a>
                        </div>

                        <div class="zod-ops-grid">
                            <div class="zod-ops-card zod-ops-card--wide">
                                <div class="zod-ops-card__label">@lang('Domain Health')</div>
                                <strong>{{ $hosting->domain ?: __('No domain') }}</strong>
                                @if($zodBlockers->count())
                                    <ul class="zod-ops-list">
                                        @foreach($zodBlockers->take(4) as $blocker)
                                            <li><i class="las la-exclamation-triangle"></i>{{ __($blocker) }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="zod-ops-ok"><i class="las la-check-circle"></i>@lang('No active DNS, SSL, or webmail blockers reported.')</p>
                                @endif
                            </div>

                            <div class="zod-ops-card">
                                <div class="zod-ops-card__label">@lang('Webmail')</div>
                                <strong>@lang('Roundcube readiness')</strong>
                                <div class="zod-ops-actions">
                                    @if($zodWebmailUrl)
                                        <a href="{{ $zodWebmailUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline--info">
                                            <i class="las la-envelope-open"></i>@lang('Open')
                                        </a>
                                    @endif
                                    <button type="submit" form="zod-webmail-repair-form" class="btn btn-sm btn--primary">
                                        <i class="las la-tools"></i>@lang('Repair')
                                    </button>
                                </div>
                            </div>

                            <div class="zod-ops-card">
                                <div class="zod-ops-card__label">@lang('PHP Runtime')</div>
                                <strong>{{ $zodPhpCurrent ?: __('Unknown') }}</strong>
                                <div class="zod-ops-actions zod-ops-actions--stack">
                                    <select name="template" form="zod-php-form" class="form-control">
                                        @forelse($zodPhpBackends as $backend)
                                            <option value="{{ $backend['template'] }}" @selected($backend['template'] == $zodPhpCurrent)>
                                                {{ __($backend['label']) }}
                                            </option>
                                        @empty
                                            <option value="">@lang('No PHP backends found')</option>
                                        @endforelse
                                    </select>
                                    <button type="submit" form="zod-php-form" class="btn btn-sm btn--info" @disabled(!$zodPhpBackends->count())>
                                        <i class="las la-code-branch"></i>@lang('Apply')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div
                class="col-xl-{{ $product->server_group_id ? '8' : '12' }} col-md-{{ $product->server_group_id ? '8' : '12' }} mb-30">
                <div class="card overflow-hidden box--shadow1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Registration Date')</label>
                                    <input type="text" class="datePicker form-control reg_time flex-grow-1"
                                        data-language='en' data-position='bottom left'
                                        value="{{ showDateTime($hosting->reg_date, 'd-m-Y') }}" name="reg_date"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Next Due Date')</label>
                                    <input type="text" class="datePicker form-control" data-language='en'
                                        data-position='bottom left'
                                        value="{{ showDateTime(@$hosting->next_due_date, 'd-m-Y') }}" name="next_due_date"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Next Invoice Date')</label>
                                    <input type="text" class="datePicker form-control" data-language='en'
                                        data-position='bottom left'
                                        value="{{ showDateTime(@$hosting->next_invoice_date, 'd-m-Y') }}"
                                        name="next_invoice_date" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Termination Date')</label>
                                    <input type="text" class="datePicker form-control" data-language='en'
                                        data-position='bottom left'
                                        value="{{ showDateTime(@$hosting->termination_date, 'd-m-Y') }}"
                                        name="termination_date" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('First Payment Amount')</label>
                                    <div class="input-group">
                                        <input type="text" name="first_payment_amount"
                                            value="{{ getAmount(@$hosting->first_payment_amount) }}"
                                            class="form-control">
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Recurring Amount')</label>
                                    <div class="input-group">
                                        <input type="text" name="recurring_amount"
                                            value="{{ getAmount(@$hosting->recurring_amount) }}" class="form-control">
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Product/Service')</label>
                                    <select name="change_product_id" class="change_product_id form-control">
                                        @php echo $productDropdown; @endphp
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Server')</label>
                                    <select name="server_id" class="server_id form-control">
                                        @if (@$product->serverGroup)
                                            <option value="">@lang('Select One')</option>
                                            @foreach (@$product->serverGroup->servers as $index => $server)
                                                <option value="{{ $server->id }}"
                                                    {{ $server->id == $hosting->server_id ? 'selected' : null }}>
                                                    {{ $server->hostname }} - {{ $server->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="">@lang('N/A')</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>
                                        @if ($product->product_type == 3)
                                            @lang('Hostname')
                                        @else
                                            @lang('Domain')
                                        @endif
                                    </label>
                                    <input class="form-control" type="text" name="domain"
                                        value="{{ @$hosting->domain }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Dedicated IP')</label>
                                    <input class="form-control" type="text" name="dedicated_ip"
                                        value="{{ @$hosting->dedicated_ip }}">
                                </div>
                            </div>


                            @if ($product->product_type == 3)
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="form-group ">
                                        <label> @lang('Assigned IPs')</label>
                                        <textarea name="assigned_ips" class="form-control" rows="2">{{ @$hosting->assigned_ips }}</textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('Nameserver 1') </label>
                                        <input class="form-control" type="text" name="ns1"
                                            value="{{ @$hosting->ns1 }}">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('Nameserver 2') </label>
                                        <input class="form-control" type="text" name="ns2"
                                            value="{{ @$hosting->ns2 }}">
                                    </div>
                                </div>
                            @endif

                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Username') </label>
                                    <input class="form-control" type="text" name="username"
                                        value="{{ @$hosting->username }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <div class="justify-content-between d-flex flex-wrap">
                                        <label>@lang('Password')</label>
                                        <a href="javascript:void(0)" class="generatePassword">@lang('Generate Strong Password')</a>
                                    </div>
                                    <input class="form-control" type="text" name="password"
                                        value="{{ @$hosting->password }}" id="password" required>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Status') </label>
                                    <select name="status" class="form-control">
                                        @foreach (@$hosting::status() as $index => $data)
                                            <option value="{{ $index }}"
                                                {{ @$hosting->status == $index ? 'selected' : null }}>{{ $data }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Billing Cycle') </label>
                                    <select name="billing_cycle" class="form-control">
                                        @foreach (billingCycle() as $index => $data)
                                            <option value="{{ $index }}"
                                                {{ $hosting->billing_cycle == $index ? 'selected' : null }}
                                                data-data='{{ $data['billing_cycle'] }}'>
                                                {{ __($data['showText']) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if ($product->getConfigs->count())
                                <div class="col-12">
                                    <div class="service-config-panel">
                                        <div class="service-config-panel__head">
                                            <div>
                                                <h6>@lang('Configurable Options')</h6>
                                                <p>@lang('Update service limits, add-ons, and panel-linked options from one place. Supported panels sync after submit.')</p>
                                            </div>
                                            <span>@lang('Live sync')</span>
                                        </div>
                                        <div class="row">
                                            @foreach ($product->getConfigs as $index => $config)
                                                @foreach ($config->group->activeOptions as $option)
                                                    <div class="col-xl-4 col-lg-6 col-md-6">
                                                        <div class="form-group service-config-field">
                                                            <label>{{ __($option->name) }}</label>
                                                            <select name="config_options[{{ $option->id }}]"
                                                                class="form-control options">
                                                                <option value="">@lang('Select One')</option>
                                                                @forelse($option->activeSubOptions as $subOption)
                                                                    <option value="{{ $subOption->id }}"
                                                                        data-price='{{ $subOption->getOnlyPrice }}'
                                                                        data-text='{{ $subOption->name }}'>
                                                                        {{ __($subOption->name) }}
                                                                    </option>
                                                                @empty
                                                                    <option value="">@lang('N/A')</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-xl-4 col-lg-12 col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Admin Notes') </label>
                                    <textarea name="admin_notes" class="form-control h-45" rows="2">@php echo nl22br($hosting->admin_notes); @endphp</textarea>
                                </div>
                            </div>

                            @php $cancelRequest = $hosting->cancelRequest; @endphp

                            @if ($cancelRequest)
                                <div class="col-xl-12 col-lg-12 col-md-12 border-top pt-3 ps-3 mt-3">
                                    <div class="form-group">
                                        <div class="justify-content-between d-flex flex-wrap">
                                            <label>
                                                <a
                                                    href="
                                                    {{ permit('admin.cancel.requests') ? route('admin.cancel.requests', ['id' => $cancelRequest->id]) : 'javascript:void(0)' }}">
                                                    @lang('Reason for Cancellation Request')</a>
                                                <small>({{ @$cancelRequest::type()[$cancelRequest->type] }})</small>
                                            </label>
                                            <div>
                                                <input type="checkbox" id="delete_cancel_request"
                                                    name="delete_cancel_request">
                                                <label>@lang('Delete Cancellation Request') </label>
                                            </div>
                                        </div>
                                        <p class="text--danger">@php echo nl22br($cancelRequest->reason); @endphp</p>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            @if ($product->server_group_id)
                <div class="col-xl-4 col-md-4 mb-30">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md  table-responsive metric-table">
                                <table class="table table--light style--two">
                                    <thead>
                                        <tr>
                                            <th>@lang('Metric')</th>
                                            <th>@lang('Info')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($accountSummary ?? [] as $key => $value)
                                            <tr>
                                                <td>
                                                    {{ $key }}
                                                </td>
                                                <td>
                                                    <span class="{{ $value ? 'fw-bold' : null }}">
                                                        {{ $value ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @permit('admin.order.hosting.update')
            <div class="row mb-none-30">
                <div class="col-lg-12 col-md-12 mb-30">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </div>
        @endpermit
    </form>

    @if (@$serverGroup->type == 4 && $hasAccount)
        <form id="zod-webmail-repair-form" action="{{ route('admin.order.hosting.zodpanel.webmail.repair', $hosting->id) }}" method="POST">
            @csrf
            <input type="hidden" name="create_mail_domain" value="1">
        </form>

        <form id="zod-php-form" action="{{ route('admin.order.hosting.zodpanel.php', $hosting->id) }}" method="POST">
            @csrf
        </form>
    @endif

    {{-- Module Modal --}}
    <div id="moduleModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="createModalLabel">@lang('Confirm Module Command')</h6>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="form-horizontal" method="post" action="{{ route('admin.module.command') }}">
                    @csrf
                    <input type="hidden" name="hosting_id" value="{{ $hosting->id }}" required>
                    <input type="hidden" name="module_type" required>
                    <div class="modal-body">
                        <div class="form-group">
                            @lang('Are you sure to want run the') <span class="moduleName text--danger"></span> @lang('function')?

                            <div class="form-group mt-4 suspendArea">
                                <label class="form-control-label fw-bold">@lang('Reason')*</label>
                                <input type="text" class="form-control" name="suspend_reason" autocomplete="off"
                                    placeholder="@lang('Reason')">
                            </div>
                            <div class="form-group suspendArea">
                                <input type="checkbox" name="suspend_email" id="suspend"> <label
                                    for="suspend">@lang('Send Suspension Email')</label>
                            </div>

                            <div class="form-group mt-4 unSuspendArea">
                                <input type="checkbox" name="unSuspend_email" id="unSuspend"> <label
                                    for="unSuspend">@lang('Send Unsuspension Email')</label>
                            </div>

                        </div>
                    </div>
                    @permit('admin.module.command')
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        </div>
                    @endpermit
                </form>
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <div class="d-flex justify-content-end flex-wrap gap-2 breadcrumb-button">
        @permit('admin.orders.details')
            <a href="{{ route('admin.orders.details', @$hosting->order_id) }}"
                class="btn btn-sm btn-outline--dark me-1 breadcrumb-button__one">
                <i class="la la-undo"></i> @lang('Go to Order')
            </a>
        @endpermit

        @permit('admin.invoices.hosting.all')
            <a href="{{ route('admin.invoices.hosting.all', $hosting->id) }}"
                class="btn btn-sm btn-outline--primary me-1 breadcrumb-button__two">
                <i class="las la-file-alt"></i> @lang('Invoices')
            </a>
        @endpermit

        @permit('admin.module.login.hosting')
            @if ($product->server_group_id)
                @php
                    $panelLoginLabel = @$serverGroup->type == 4 ? 'Login ZodPanel' : 'Login to '.(@$serverGroup->getType ?? 'Control Panel');
                @endphp
                <form class="d-init breadcrumb-button__form" action="{{ route('admin.module.login.hosting') }}"
                    method="post" target="_blank" rel="noopener">
                    @csrf
                    <input type="hidden" name="hosting_id" value="{{ $hosting->id }}" required>
                    <button 
                        type="submit" 
                        class="btn btn-sm btn-outline--info breadcrumb-button__three"
                        @disabled(!$hasAccount)
                    >
                        <i class="las la-sign-in-alt"></i>@lang($panelLoginLabel)
                    </button>
                </form>
            @endif
        @endpermit
    </div>
@endpush

@push('style')
    <style>
        .metric-table {
            max-height: 580px;
        }

        .metric-table::-webkit-scrollbar {
            width: 8px;
        }

        .metric-table::-webkit-scrollbar-thumb {
            background-color: #8b8b8b;
            border-radius: 12px;
        }

        .metric-table table thead {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .metric-table table :is(td, th) {
            white-space: wrap !important;
        }

        .d-init {
            display: initial;
        }

        .service-config-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin: 8px 0 22px;
            padding: 18px 18px 2px;
        }

        .service-config-panel__head {
            align-items: flex-start;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 14px;
        }

        .service-config-panel__head h6 {
            color: #0f172a;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .service-config-panel__head p {
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            max-width: 640px;
        }

        .service-config-panel__head span {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 999px;
            color: #047857;
            flex: 0 0 auto;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 10px;
        }

        .service-config-field label {
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .service-config-field .form-control {
            background-color: #fff;
            border-color: #dbe3ef;
            min-height: 43px;
        }

        .service-config-field .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .zod-ops-panel {
            margin: 0 0 24px;
            padding: 18px;
            border: 1px solid #dbe7f5;
            border-radius: 16px;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .10), transparent 34%),
                linear-gradient(135deg, #ffffff, #f8fafc);
            box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
        }

        .zod-ops-panel__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .zod-ops-panel__head span,
        .zod-ops-card__label {
            display: block;
            color: #2563eb;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .zod-ops-panel__head h5 {
            margin: 4px 0 0;
            color: #0f172a;
            font-size: 18px;
            font-weight: 800;
        }

        .zod-ops-grid {
            display: grid;
            grid-template-columns: minmax(260px, 1.35fr) repeat(2, minmax(220px, 1fr));
            gap: 14px;
        }

        .zod-ops-card {
            min-height: 148px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: rgba(255, 255, 255, .82);
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .zod-ops-card strong {
            display: block;
            margin-top: 6px;
            color: #0f172a;
            font-size: 16px;
            overflow-wrap: anywhere;
        }

        .zod-ops-list {
            display: grid;
            gap: 8px;
            margin: 12px 0 0;
            padding: 0;
            list-style: none;
        }

        .zod-ops-list li,
        .zod-ops-ok {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.45;
        }

        .zod-ops-list i {
            color: #d97706;
            font-size: 16px;
            margin-top: 1px;
        }

        .zod-ops-ok i {
            color: #059669;
            font-size: 16px;
            margin-top: 1px;
        }

        .zod-ops-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .zod-ops-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 36px;
            border-radius: 9px;
        }

        .zod-ops-actions--stack {
            align-items: stretch;
        }

        .zod-ops-actions--stack .form-control {
            flex: 1 1 150px;
            min-height: 38px;
            border-color: #dbe3ef;
            border-radius: 9px;
        }

        @media (max-width: 991px) {
            .table-responsive--md tbody tr:nth-child(odd) {
                background-color: #1208080d;
            }

            .service-config-panel {
                padding: 16px 14px 0;
            }

            .service-config-panel__head {
                display: block;
            }

            .service-config-panel__head span {
                display: inline-flex;
                margin-top: 12px;
            }

            .zod-ops-grid {
                grid-template-columns: 1fr;
            }

            .zod-ops-panel__head {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // ========================================= daterangepicker =========================================
            // ========================================= daterangepicker =========================================
            $('.datePicker').daterangepicker({
                autoApply: true,
                autoUpdateInput: false,
                singleDatePicker: true,
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });

            $('.datePicker').on('apply.daterangepicker', (event, picker) => {
                $(event.target).val(picker.startDate.format('DD-MM-YYYY'));
            });
            // ========================================= daterangepicker =========================================
            // ========================================= daterangepicker =========================================

            $('.moduleModal').on('click', function() {
                var modal = $('#moduleModal');

                var moduleName = $(this).text();
                var moduleType = $(this).data('type');

                if (moduleType == 2) {
                    $('.suspendArea').removeClass('d-none');
                } else {
                    $('.suspendArea').addClass('d-none');
                }

                if (moduleType == 3) {
                    $('.unSuspendArea').removeClass('d-none');
                } else {
                    $('.unSuspendArea').addClass('d-none');
                }

                modal.find('.moduleName').text(moduleName);
                modal.find('input[name=module_type]').val(moduleType);

                modal.modal('show');
            });

            $('.generatePassword').on('click', function() {
                var password = generatePassword(15);
                $('#password').val(password);
            });

            function generatePassword(passwordLength) {
                var numberChars = "0123456789";
                var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                var lowerChars = "abcdefghijklmnopqrstuvwxyz";
                var specialChars = "!$%";
                var allChars = numberChars + upperChars + lowerChars + specialChars;
                var randPasswordArray = Array(passwordLength);

                randPasswordArray[0] = numberChars;
                randPasswordArray[1] = upperChars;
                randPasswordArray[2] = lowerChars;
                randPasswordArray[3] = specialChars;
                randPasswordArray = randPasswordArray.fill(allChars, 4);

            return shuffleArray(randPasswordArray.map(function(x) {
                return x[Math.floor(Math.random() * x.length)]
            })).join('');
        }

        function shuffleArray(array) {
            for (var i = array.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
            return array;
        }

        $('.change_product_id').on('change', function() {
            var productId = $(this).val();
            var hostingId = @json($hosting->id);

            if (!productId) {
                return false;
            }

            @permit('admin.change.order.hosting.product')
            window.location.href = '{{ route('admin.change.order.hosting.product', ['', '']) }}/' +
                hostingId + '/' + productId;
            @endpermit
        });

        $('.change_product_id option[value=@json($product->id)]').prop('selected', true);

        var product = @json($product);
        var hosting = @json($hosting);

        $('select[name=billing_cycle]').on('change', function() {
            var value = $('select[name=billing_cycle] option:selected').data('data');

            if ($(this).val() == 0) {
                value = 'monthly';
            }

            showSelect(value, product, $(this).val());
        }).change();

        function showSelect(value, product, cycle = null) {
            try {

                var getColumn = value;
                var getFeeColumn = value + '_setup_fee';

                $('.options').each(function(index, data) {
                    var options = $(data).find('option');
                    var general = @json(gs());
                    var finalText = null;

                    options.each(function(iteration, dropdown) {
                        var dropdown = $(dropdown);
                        var dropdownOptions = null;
                        var optionSetupFee = '';

                        if (dropdown.data('price')) {
                            var priceForThisItem = dropdown.data('price');
                            var mainText = dropdown.data('text');

                            var display = cycle == 0 ? 'One Time' : pricing(0, null, getColumn,
                                cycle);

                            if (cycle == 0) {
                                getColumn = 'monthly'
                            }

                            if (priceForThisItem[getFeeColumn] > 0) {
                                optionSetupFee =
                                    ` + ${general.cur_sym}${getAmount(priceForThisItem[getFeeColumn])} ${general.cur_text} Setup Fee`
                            }

                            dropdownOptions =
                                `${general.cur_sym}${getAmount(priceForThisItem[getColumn])} ${general.cur_text} ${display} ${optionSetupFee}`;

                            finalText = mainText + ' ' + dropdownOptions;
                            dropdown.text(finalText);
                        }

                    });
                });

            } catch (message) {
                console.log(message);
            }
        }

        function pricing(price, type, column, cycle = null) {
            try {

                if (!price) {
                    column = column.replaceAll('_', ' ');

                    if (cycle == 0) {
                        column = 'One Time';
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

        var hostingConfigs = @json(@$hosting->hostingConfigs);

        for (var i = 0; i < hostingConfigs.length; i++) {

            var selectName = hostingConfigs[i]['configurable_group_option_id'];
            var selectOption = hostingConfigs[i]['configurable_group_sub_option_id'];

            $(`select[name='config_options[${selectName}]'] option[value=${selectOption}]`).prop('selected', true);
            }

        })(jQuery);
    </script>
@endpush
