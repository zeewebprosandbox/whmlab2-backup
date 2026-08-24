@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Service / Domain')</th>
                            <th>@lang('Pricing')</th> 
                            <th>@lang('Next Due Date')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr> 
                        </thead> 
                        <tbody>  
                            @forelse($services as $service)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{$service->user->fullname}}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', $service->user_id) : 'javascript:void(0)' }}">
                                                <span>@</span>{{ $service->user->username }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ __(@$service->product->name) }}</span>
                                        @if($service->domain)
                                            <br>
                                            <a href="{{ permit('admin.order.hosting.details') ? route('admin.order.hosting.details', $service->id) : 'javascript:void(0)' }}" class="small text--primary fw-semibold font-mono">
                                                <i class="las la-globe"></i> {{ $service->domain }}
                                            </a>
                                        @endif
                                        <br>
                                        <span class="small text-muted">
                                            {{ __(@$service->product->serviceCategory->name) }}
                                            @if($service->server)
                                                • {{ $service->server->name }}
                                            @endif
                                        </span>
                                    </td>  
                                    <td>
                                        <span class="fw-bold">
                                            {{ gs('cur_sym') }}{{ getAmount($service->recurring_amount) }} {{ __(gs('cur_text')) }}
                                            {{ @billingCycle(@$service->billing_cycle, true)['showText'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($service->billing_cycle != 0)
                                            {{ showDateTime($service->next_due_date, 'd/m/Y') }}
                                        @else 
                                            @lang('N/A')
                                        @endif 
                                    </td>
                                    <td>
                                        @php echo $service->showStatus; @endphp
                                    </td> 
                                    <td>
                                        <div class="button--group">
                                            @permit('admin.order.hosting.details')
                                                <a href="{{ route('admin.order.hosting.details', $service->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop text--shadow"></i> @lang('Details')
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-outline--primary" disabled>
                                                    <i class="las la-desktop text--shadow"></i> @lang('Details')
                                                </button>
                                            @endpermit

                                            @permit('admin.module.command')
                                                @if($service->status == 1)
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline--warning serviceActionBtn"
                                                        data-id="{{ $service->id }}"
                                                        data-type="2"
                                                        data-title="@lang('Suspend Service')"
                                                        data-message="@lang('This will suspend the service on the connected hosting panel in real time.')"
                                                        data-reason="@lang('Suspended by admin')"
                                                        data-reason-required="1"
                                                    >
                                                        <i class="las la-ban"></i> @lang('Suspend')
                                                    </button>
                                                @elseif($service->status == 3)
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline--success serviceActionBtn"
                                                        data-id="{{ $service->id }}"
                                                        data-type="3"
                                                        data-title="@lang('Unsuspend Service')"
                                                        data-message="@lang('This will unsuspend the service on the connected hosting panel in real time.')"
                                                        data-reason-required="0"
                                                    >
                                                        <i class="las la-undo"></i> @lang('Unsuspend')
                                                    </button>
                                                @endif

                                                @if(!in_array($service->status, [4, 5]))
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline--dark serviceActionBtn"
                                                        data-id="{{ $service->id }}"
                                                        data-type="7"
                                                        data-title="@lang('Deactivate Service')"
                                                        data-message="@lang('This sets the service to Cancelled in the database without destroying the remote server account.')"
                                                        data-reason-required="0"
                                                    >
                                                        <i class="las la-power-off"></i> @lang('Deactivate')
                                                    </button>
                                                @endif

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline--info mergeServerBtn"
                                                    data-id="{{ $service->id }}"
                                                    data-domain="{{ $service->domain }}"
                                                    data-current-server="{{ $service->server_id }}"
                                                    data-current-server-name="{{ $service->server ? $service->server->name : __('Unassigned') }}"
                                                >
                                                    <i class="las la-random"></i> @lang('Merge Server')
                                                </button>

                                                @permit('admin.service.delete')
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.service.delete', $service->id) }}"
                                                        data-question="@lang('Are you sure you want to permanently delete this service entirely 100%?')"
                                                    >
                                                        <i class="las la-trash"></i> @lang('Delete')
                                                    </button>
                                                @endpermit
                                            @endpermit
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($services->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($services) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />

{{-- Service Action Modal --}}
<div class="modal fade" id="serviceActionModal" tabindex="-1" role="dialog" aria-labelledby="serviceActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceActionModalTitle">@lang('Manage Service')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.module.command') }}" method="POST" id="serviceActionForm">
                @csrf
                <input type="hidden" name="hosting_id" id="serviceActionHostingId">
                <input type="hidden" name="module_type" id="serviceActionModuleType">

                <div class="modal-body">
                    <p class="service-action-message mb-3"></p>
                    <div class="form-group service-action-reason-wrapper">
                        <label class="fw-bold">@lang('Reason')</label>
                        <textarea class="form-control" name="suspend_reason" id="serviceActionReason" rows="3" placeholder="@lang('Enter reason...')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary" id="serviceActionSubmitBtn">@lang('Confirm')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Merge Service to Server Modal --}}
<div class="modal fade" id="mergeServerModal" tabindex="-1" role="dialog" aria-labelledby="mergeServerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mergeServerModalLabel">@lang('Merge / Reassign Service to Server')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.service.merge.server') }}" method="POST">
                @csrf
                <input type="hidden" name="hosting_id" id="mergeHostingId">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="fw-bold">@lang('Service Domain')</label>
                        <input type="text" class="form-control" id="mergeDomainName" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-bold">@lang('Current Server')</label>
                        <input type="text" class="form-control" id="mergeCurrentServerName" readonly>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">@lang('Select Target Server (to merge into)') <span class="text--danger">*</span></label>
                        <select name="target_server_id" class="form-control" required>
                            <option value="">@lang('Choose Target Server...')</option>
                            @foreach($allServers ?? [] as $srv)
                                <option value="{{ $srv->id }}">
                                    {{ $srv->name }} ({{ $srv->ip_address ?: $srv->host }}) - {{ @$srv->group->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">
                            @lang('This will instantly reassign the hosting service, assign the target node IP & nameservers, and overwrite default DNS records in real time.')
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary"><i class="las la-check-circle"></i> @lang('Confirm & Merge')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    @permit('admin.services.delete.all')
        <button
            type="button"
            class="btn btn-outline--danger confirmationBtn me-2"
            data-action="{{ route('admin.services.delete.all') }}"
            data-question="@lang('Are you sure you want to permanently delete ALL services entirely 100%?')"
        >
            <i class="las la-trash"></i> @lang('Delete All Services')
        </button>
    @endpermit
    <x-search-form placeholder="Username / Email / Domain" />
@endpush

@push('script')
<script>
    (function ($) {
        "use strict";

        $('.serviceActionBtn').on('click', function () {
            let modal = $('#serviceActionModal');
            let id = $(this).data('id');
            let type = $(this).data('type');
            let title = $(this).data('title') || '@lang("Manage Service")';
            let message = $(this).data('message') || '@lang("Are you sure you want to perform this action?")';
            let reason = $(this).data('reason') || '';
            let reasonRequired = $(this).data('reason-required') == 1;

            $('#serviceActionHostingId').val(id);
            $('#serviceActionModuleType').val(type);
            $('#serviceActionModalTitle').text(title);
            $('.service-action-message').text(message);
            $('#serviceActionReason').val(reason);

            if (reasonRequired || type == 2) {
                $('.service-action-reason-wrapper').show();
                $('#serviceActionReason').prop('required', true);
            } else {
                $('.service-action-reason-wrapper').hide();
                $('#serviceActionReason').prop('required', false);
            }

            modal.modal('show');
        });

        $('.mergeServerBtn').on('click', function () {
            let modal = $('#mergeServerModal');
            let id = $(this).data('id');
            let domain = $(this).data('domain');
            let currentServerName = $(this).data('current-server-name');

            $('#mergeHostingId').val(id);
            $('#mergeDomainName').val(domain);
            $('#mergeCurrentServerName').val(currentServerName);

            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
