@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                            <tr>
                                <th>@lang('Client')</th>  
                                <th>@lang('Email & Mobile')</th>
                                <th>@lang('Verifications (EV / SV / KYC)')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Services / Domains')</th>
                                <th>@lang('Balance')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $user->fullname }}</span>
                                    <br>
                                    <span class="small">
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="text--primary fw-semibold">
                                            <span>@</span>{{ $user->username }}
                                        </a>
                                    </span>
                                    <br>
                                    <span class="text-muted text--11" title="{{ @$user->country_name }}">
                                        <i class="las la-globe"></i> {{ $user->country_code ?: 'N/A' }} • {{ diffForHumans($user->created_at) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="text--12">{{ $user->email }}</span>
                                        <span class="text--12 text-muted">{{ $user->mobileNumber ?: 'No Phone' }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        <!-- Email Verification Toggle Badge -->
                                        <button type="button" class="btn btn-xs btn-quick-toggle {{ $user->ev ? 'btn--success' : 'btn--danger' }}" data-id="{{ $user->id }}" data-field="ev" data-value="{{ $user->ev ? 0 : 1 }}" title="@lang('Click to toggle Email Verification')">
                                            <i class="las la-envelope"></i> EV: {{ $user->ev ? __('Yes') : __('No') }}
                                        </button>

                                        <!-- Mobile Verification Toggle Badge -->
                                        <button type="button" class="btn btn-xs btn-quick-toggle {{ $user->sv ? 'btn--success' : 'btn--warning' }}" data-id="{{ $user->id }}" data-field="sv" data-value="{{ $user->sv ? 0 : 1 }}" title="@lang('Click to toggle Mobile Verification')">
                                            <i class="las la-mobile"></i> SV: {{ $user->sv ? __('Yes') : __('No') }}
                                        </button>

                                        <!-- KYC Verification Toggle Badge -->
                                        <button type="button" class="btn btn-xs btn-quick-toggle {{ $user->kv ? 'btn--success' : 'btn--dark' }}" data-id="{{ $user->id }}" data-field="kv" data-value="{{ $user->kv ? 0 : 1 }}" title="@lang('Click to toggle KYC Verification')">
                                            <i class="las la-user-check"></i> KYC: {{ $user->kv ? __('Yes') : __('No') }}
                                        </button>
                                    </div>
                                </td>

                                <td>
                                    <div class="form-check form-switch ps-0">
                                        <input class="form-check-input user-status-switch ms-0" type="checkbox" role="switch" id="userStatus_{{ $user->id }}" data-id="{{ $user->id }}" {{ $user->status ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.25em;">
                                        <label class="form-check-label text--12 fw-semibold ms-1 status-label-{{ $user->id }} {{ $user->status ? 'text--success' : 'text--danger' }}" for="userStatus_{{ $user->id }}">
                                            {{ $user->status ? __('Active') : __('Banned') }}
                                        </label>
                                    </div>
                                </td>

                                <td>
                                    <div class="text--12">
                                        <a href="{{ permit('admin.users.services') ? route('admin.users.services', $user->id) : 'javascript:void(0)' }}" class="badge badge--primary">
                                            <i class="las la-server"></i> {{ $user->hostings->count() }} @lang('Services')
                                        </a>
                                        <a href="{{ permit('admin.users.domains') ? route('admin.users.domains', $user->id) : 'javascript:void(0)' }}" class="badge badge--info">
                                            <i class="las la-globe"></i> {{ $user->domains->count() }} @lang('Domains')
                                        </a>
                                    </div>
                                </td>

                                <td>
                                    <span class="fw-bold font-monospace text--dark">
                                        {{ showAmount($user->balance) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="button--group">
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-outline--primary" title="@lang('Client Settings & Overview')">
                                            <i class="las la-sliders-h"></i> @lang('Manage')
                                        </a>
                                        @permit('admin.users.login')
                                            <a href="{{ route('admin.users.login', $user->id) }}" target="_blank" class="btn btn-sm btn-outline--success" title="@lang('Instant Login as Client')">
                                                <i class="las la-sign-in-alt"></i> @lang('Login')
                                            </a>
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
                @if ($users->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($users) }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <form action="{{ route('admin.users.support.pin.search') }}" method="post" class="d-flex gap-2">
        @csrf
        <input type="text" name="support_pin" class="form-control bg--white h-45" placeholder="@lang('Support PIN')" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>
        <button class="btn btn-outline--primary h-45" type="submit">
            <i class="las la-search"></i>@lang('PIN')
        </button>
    </form>
    <x-search-form placeholder="Username / Email" />
    @permit('admin.users.add.new.form')
        <a href="{{ route('admin.users.add.new.form') }}" class="btn btn-outline--primary h-45">
            <i class="las la-plus"></i>@lang('Add New')
        </a>
    @endpermit
@endpush

@push('script')
<script>
    (function($) {
        "use strict";

        // Quick verification badge toggles (EV, SV, KYC)
        $('.btn-quick-toggle').on('click', function() {
            const btn = $(this);
            const userId = btn.data('id');
            const field = btn.data('field');
            const nextVal = btn.data('value');

            btn.prop('disabled', true).addClass('opacity-50');

            $.ajax({
                url: "{{ route('admin.users.quick.toggle', '') }}/" + userId,
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    field: field,
                    value: nextVal
                },
                success: function(res) {
                    btn.prop('disabled', false).removeClass('opacity-50');
                    if (res.success) {
                        notify('success', res.message);
                        
                        const isVerified = (res.user[field] == 1);
                        btn.data('value', isVerified ? 0 : 1);

                        if (isVerified) {
                            btn.removeClass('btn--danger btn--warning btn--dark').addClass('btn--success');
                            btn.html(`<i class="las la-check"></i> ${field.toUpperCase()}: @lang('Yes')`);
                        } else {
                            btn.removeClass('btn--success').addClass(field === 'sv' ? 'btn--warning' : (field === 'kv' ? 'btn--dark' : 'btn--danger'));
                            btn.html(`<i class="las la-times"></i> ${field.toUpperCase()}: @lang('No')`);
                        }
                    } else {
                        notify('error', res.message || '@lang("Update failed")');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).removeClass('opacity-50');
                    notify('error', xhr.responseJSON ? xhr.responseJSON.message : '@lang("Server error")');
                }
            });
        });

        // Instant user status toggle switch (Active / Banned)
        $('.user-status-switch').on('change', function() {
            const el = $(this);
            const userId = el.data('id');
            const isActive = el.is(':checked') ? 1 : 0;
            const label = $('.status-label-' + userId);

            el.prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.users.quick.toggle', '') }}/" + userId,
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    field: 'status',
                    value: isActive
                },
                success: function(res) {
                    el.prop('disabled', false);
                    if (res.success) {
                        notify('success', res.message);
                        if (isActive) {
                            label.removeClass('text--danger').addClass('text--success').text('@lang("Active")');
                        } else {
                            label.removeClass('text--success').addClass('text--danger').text('@lang("Banned")');
                        }
                    } else {
                        el.prop('checked', !isActive);
                        notify('error', res.message || '@lang("Status update failed")');
                    }
                },
                error: function(xhr) {
                    el.prop('disabled', false);
                    el.prop('checked', !isActive);
                    notify('error', xhr.responseJSON ? xhr.responseJSON.message : '@lang("Server error")');
                }
            });
        });

    })(jQuery);
</script>
@endpush
