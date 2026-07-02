@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Service/Product')</th>
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
                                        <br>
                                        <span class="small">
                                            {{ __(@$service->product->serviceCategory->name) }}
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
                                                        data-message="@lang('This will suspend the remote account when a panel is connected, then deactivate the WHMLab service record.')"
                                                        data-reason="@lang('Service deactivated by admin')"
                                                        data-reason-required="0"
                                                    >
                                                        <i class="las la-pause-circle"></i> @lang('Deactivate')
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline--danger serviceActionBtn"
                                                        data-id="{{ $service->id }}"
                                                        data-type="4"
                                                        data-title="@lang('Delete Service')"
                                                        data-message="@lang('This will terminate/delete the remote hosting account on the connected panel in real time.')"
                                                        data-reason-required="0"
                                                    >
                                                        <i class="las la-trash"></i> @lang('Delete')
                                                    </button>
                                                @endif
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

@permit('admin.module.command')
    <div id="serviceActionModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.module.command') }}">
                    @csrf
                    <input type="hidden" name="hosting_id">
                    <input type="hidden" name="module_type">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Confirm Action')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="action-message text-muted mb-3"></p>
                        <div class="form-group reason-wrapper">
                            <label>@lang('Reason')</label>
                            <input type="text" class="form-control" name="suspend_reason" placeholder="@lang('Reason')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--primary">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpermit
@endsection

@if(request()->routeIs('admin.services'))
    @push('breadcrumb-plugins')
        <x-search-form placeholder="Username / Email" />
    @endpush
@endif

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.serviceActionBtn').on('click', function() {
                const modal = $('#serviceActionModal');
                const requiresReason = Number($(this).data('reason-required')) === 1;

                modal.find('.modal-title').text($(this).data('title'));
                modal.find('.action-message').text($(this).data('message'));
                modal.find('input[name=hosting_id]').val($(this).data('id'));
                modal.find('input[name=module_type]').val($(this).data('type'));
                modal.find('input[name=suspend_reason]').val($(this).data('reason') || '');
                modal.find('input[name=suspend_reason]').prop('required', requiresReason);
                modal.find('.reason-wrapper').toggleClass('d-none', !requiresReason && !$(this).data('reason'));

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
