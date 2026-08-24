@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 bg--transparent shadow-none">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two bg-white">
                        <thead>
                        <tr>
                            
                            <th>@lang('Name')</th>
                            <th>@lang('Group')</th>
                            <th>@lang('Role')</th>
                            <th>@lang('URL')</th>
                            <th>@lang('Capacity')</th>
                            <th>@lang('Health')</th>
                            <th>@lang('Username')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr> 
                        </thead>  
                        <tbody>
                            @forelse($servers as $server)
                                @php 
                                    $serverGroup = @$server->group;
                                    $serverType = @$serverGroup->getType
                                @endphp
                                <tr>  
                                    <td>
                                        <span class="fw-bold">{{ __($server->name) }}</span>
                                        @if($server->location)
                                            <br><small class="text-muted">{{ __($server->location) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap justify-content-xl-center justify-content-end align-items-center">
                                            <span class="me-1">{{ __(@$serverGroup->name) }}</span>
                                            (@php echo getProductModuleLogo(@$server->group->type); @endphp)
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge--primary">{{ __($server->serviceRoleLabel()) }}</span>
                                    </td>
                                    <td>
                                        {{ $server->hostname }} 
                                    </td>
                                    <td>
                                        @php $capacity = $server->capacityPercent(); @endphp
                                        <div class="text-start min-width-180">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('admin.server.accounts', $server->id) }}" class="fw-bold text--primary">
                                                    <i class="las la-users"></i> {{ $server->current_accounts ?? 0 }} @lang('Accounts')
                                                </a>
                                                <small class="text-muted">{{ $server->max_accounts ? $capacity.'%' : __('Open') }}</small>
                                            </div>
                                            <div class="progress mt-1" style="height: 6px;">
                                                <div class="progress-bar bg--base" style="width: {{ $server->max_accounts ? $capacity : 8 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php echo $server->healthBadge; @endphp
                                        @if($server->health_checked_at)
                                            <br><small class="text-muted">{{ showDateTime($server->health_checked_at, 'M d, H:i') }}</small>
                                        @endif
                                    </td>

                                    <td>
                                        {{ __($server->username) }}
                                    </td>

                                    <td>
                                        @php echo $server->showStatus; @endphp
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline--primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="las la-ellipsis-v"></i>@lang('Action')
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('admin.server.accounts', $server->id) }}" class="dropdown-item text--primary fw-bold">
                                                <i class="las la-users"></i> @lang('Hosted Accounts & Credentials')
                                            </a>
                                            @permit('admin.server.login')
                                                <a href="{{ route('admin.server.login', $server->id) }}" class="dropdown-item" 
                                                    data-modal_title="@lang("Login to $serverType")"
                                                >
                                                    <i class="lab la-whmcs"></i> @lang("Login to $serverType")
                                                </a>
                                            @endpermit
                                            @permit('admin.server.edit.page')
                                                <a href="{{ route('admin.server.edit.page', $server->id) }}" class="dropdown-item"
                                                    data-modal_title="@lang('Edit')"
                                                >
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                                <a href="{{ route('admin.server.add.page') }}?vps_ip={{ $server->ip_address ?: $server->host }}" class="dropdown-item text-warning">
                                                    <i class="la la-terminal"></i> @lang('Reinstall VPS & Live Terminal')
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item text--success syncDesignLiveBtn"
                                                   data-id="{{ $server->id }}"
                                                   data-name="{{ $server->name }}"
                                                   data-ip="{{ $server->ip_address ?: $server->host }}">
                                                    <i class="la la-paint-brush"></i> @lang('Sync Custom Hestia Design (Live Stream)')
                                                </a>
                                            @endpermit
                                            @permit('admin.server.health')
                                                <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                   data-action="{{ route('admin.server.health', $server->id) }}"
                                                   data-question="@lang('Run a health check for this server now?')">
                                                    <i class="la la-heartbeat"></i> @lang('Health Check')
                                                </a>
                                            @endpermit
                                            @permit('admin.server.status')
                                                @if($server->status == 0)
                                                    <a href="javascript:void(0)"
                                                            class="dropdown-item confirmationBtn"
                                                            data-action="{{ route('admin.server.status', $server->id) }}"
                                                            data-question="@lang('Are you sure to enable this server?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                    data-action="{{ route('admin.server.status', $server->id) }}"
                                                    data-question="@lang('Are you sure to disable this server?')">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </a>
                                                @endif
                                            @else 
                                                @if($server->status == 0)
                                                    <a href="javascript:void(0)" class="dropdown-item">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </a>
                                                @endif
                                            @endpermit

                                            <button type="button" class="dropdown-item text--danger confirmationBtn"
                                                data-action="{{ route('admin.server.delete', $server->id) }}"
                                                data-question="@lang('Are you sure you want to delete this server entirely 100%? All associated data and linked hosting references will be cleanly unlinked.')"
                                            >
                                                <i class="la la-trash"></i> @lang('Delete Server')
                                            </button>
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
            @if ($servers->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($servers) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />

{{-- Live Custom Hestia Design Sync Terminal Modal --}}
<div class="modal fade" id="syncDesignTerminalModal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content bg-dark text-white border-0 shadow-lg">
            <div class="modal-header border-secondary py-2 bg-black bg-opacity-50">
                <div class="d-flex align-items-center gap-2">
                    <span class="spinner-border spinner-border-sm text-info" id="syncTerminalSpinner" role="status"></span>
                    <h6 class="modal-title text-white font-mono mb-0" id="syncDesignModalTitle">
                        <i class="las la-terminal text-info"></i> @lang('Custom Hestia Design Live Terminal')
                    </h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary font-mono" id="syncStatusBadge">@lang('Connecting...')</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="syncModalCloseBtn"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-black">
                <div class="p-2 border-bottom border-secondary bg-dark text-muted small font-mono d-flex justify-content-between">
                    <span id="syncServerInfo">Node: ...</span>
                    <span class="text-info">Real-time Stream</span>
                </div>
                <pre id="syncDesignTerminal" class="p-3 mb-0 font-mono text-success" style="height: 380px; overflow-y: auto; background-color: #0d1117; font-size: 13px; line-height: 1.5; white-space: pre-wrap; word-break: break-all;"></pre>
            </div>
            <div class="modal-footer border-secondary py-2 bg-black bg-opacity-50 justify-content-between">
                <small class="text-muted font-mono" id="syncTerminalFooterNote">
                    @lang('Streaming live SSH output from VPS node...')
                </small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-warning d-none" id="syncRetryBtn">
                        <i class="las la-redo"></i> @lang('Retry Sync')
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-dismiss="modal">
                        @lang('Close')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="" class="loginUrl d-none" target="_blank">#</a>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.server.add.page') }}" class="btn btn-sm btn-outline--primary"><i class="las la-plus"></i> @lang('Add New')</a>
@endpush

@push('style')
<style>
.table-responsive {
    background: transparent;
    min-height: 350px;
}
.dropdown-toggle::after {
    display: inline-block;
    margin-left: 0.255em;
    vertical-align: 0.255em;
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
}
</style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            var loginUrl = @json(session()->get('loginUrl'));

            if(loginUrl){
                document.querySelector('.loginUrl').click();
            }

            let currentSyncEventSource = null;

            function startCustomDesignSync(serverId, serverName, serverIp) {
                let modal = $('#syncDesignTerminalModal');
                let terminal = $('#syncDesignTerminal');
                let spinner = $('#syncTerminalSpinner');
                let statusBadge = $('#syncStatusBadge');
                let retryBtn = $('#syncRetryBtn');
                let serverInfo = $('#syncServerInfo');
                let footerNote = $('#syncTerminalFooterNote');

                if (currentSyncEventSource) {
                    currentSyncEventSource.close();
                    currentSyncEventSource = null;
                }

                terminal.text('');
                spinner.removeClass('d-none');
                statusBadge.removeClass('bg-success bg-danger bg-secondary').addClass('bg-info').text('Syncing...');
                retryBtn.addClass('d-none');
                serverInfo.text('Node: ' + serverName + ' (' + serverIp + ')');
                footerNote.text('Deploying custom Hestia theme, templates, CSS, JS, and modules...');

                modal.modal('show');

                function appendLog(line, isError = false) {
                    let timestamp = new Date().toLocaleTimeString();
                    let prefix = isError ? '❌ ' : '';
                    let formattedLine = '[' + timestamp + '] ' + prefix + line + '\n';
                    terminal.append(formattedLine);
                    terminal.scrollTop(terminal[0].scrollHeight);
                }

                appendLog('Connecting to ' + serverName + ' (' + serverIp + ')...');

                let streamUrl = '{{ route("admin.server.sync.design.stream", ":id") }}'.replace(':id', serverId);
                currentSyncEventSource = new EventSource(streamUrl);

                currentSyncEventSource.addEventListener('log', function(e) {
                    try {
                        let data = JSON.parse(e.data);
                        if (data.line) {
                            appendLog(data.line);
                        }
                    } catch(err) {
                        appendLog(e.data);
                    }
                });

                currentSyncEventSource.addEventListener('complete', function(e) {
                    try {
                        let data = JSON.parse(e.data);
                        appendLog('----------------------------------------------------');
                        appendLog('✓ ' + (data.message || 'Custom Hestia Design Synced 100% Successfully!'));
                    } catch(err) {
                        appendLog('✓ Custom Hestia Design Synced 100% Successfully!');
                    }
                    spinner.addClass('d-none');
                    statusBadge.removeClass('bg-info bg-danger').addClass('bg-success').text('100% Synced');
                    footerNote.text('✓ Custom Hestia design is 100% active and running on port 8083.');
                    if (currentSyncEventSource) {
                        currentSyncEventSource.close();
                        currentSyncEventSource = null;
                    }
                });

                currentSyncEventSource.addEventListener('error', function(e) {
                    let errorMsg = 'Sync failed or connection interrupted.';
                    try {
                        if (e.data) {
                            let data = JSON.parse(e.data);
                            if (data.message) errorMsg = data.message;
                        }
                    } catch(err) {}

                    appendLog('----------------------------------------------------');
                    appendLog('ERROR: ' + errorMsg, true);
                    spinner.addClass('d-none');
                    statusBadge.removeClass('bg-info bg-success').addClass('bg-danger').text('Failed');
                    footerNote.text('Sync encountered errors. Progress is logged above.');
                    retryBtn.removeClass('d-none').off('click').on('click', function() {
                        startCustomDesignSync(serverId, serverName, serverIp);
                    });

                    if (currentSyncEventSource) {
                        currentSyncEventSource.close();
                        currentSyncEventSource = null;
                    }
                });

                currentSyncEventSource.onerror = function() {
                    if (statusBadge.text() !== '100% Synced') {
                        appendLog('Stream disconnected before 100% confirmation.', true);
                        spinner.addClass('d-none');
                        statusBadge.removeClass('bg-info bg-success').addClass('bg-danger').text('Disconnected');
                        retryBtn.removeClass('d-none').off('click').on('click', function() {
                            startCustomDesignSync(serverId, serverName, serverIp);
                        });
                        if (currentSyncEventSource) {
                            currentSyncEventSource.close();
                            currentSyncEventSource = null;
                        }
                    }
                };
            }

            $('.syncDesignLiveBtn').on('click', function(e) {
                e.preventDefault();
                let serverId = $(this).data('id');
                let serverName = $(this).data('name');
                let serverIp = $(this).data('ip');
                startCustomDesignSync(serverId, serverName, serverIp);
            });

            $('#syncDesignTerminalModal').on('hidden.bs.modal', function() {
                if (currentSyncEventSource) {
                    currentSyncEventSource.close();
                    currentSyncEventSource = null;
                }
            });

        })(jQuery);
    </script>
@endpush
