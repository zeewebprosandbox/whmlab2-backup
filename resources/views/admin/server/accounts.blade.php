@extends('admin.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card b-radius--10 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h4 class="mb-1 text--primary fw-bold flex items-center gap-2">
                            <i class="las la-server"></i> {{ $server->name }}
                        </h4>
                        <div class="text-muted small d-flex flex-wrap gap-3 font-mono">
                            <span><i class="las la-globe text-primary"></i> {{ $server->hostname }}</span>
                            <span><i class="las la-network-wired text-info"></i> IP: <strong>{{ $server->ip_address }}</strong></span>
                            <span><i class="las la-layer-group text-warning"></i> Group: <strong>{{ @$server->group->name }}</strong></span>
                            <span><i class="las la-users text-success"></i> Accounts: <strong>{{ $accounts->total() }}</strong></span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('admin.server.sync.accounts', $server->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline--success btn-sm">
                                <i class="las la-sync"></i> @lang('Sync All Credentials & DNS')
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline--info btn-sm syncDesignLiveBtn"
                            data-id="{{ $server->id }}"
                            data-name="{{ $server->name }}"
                            data-ip="{{ $server->ip_address ?: $server->host }}">
                            <i class="las la-palette"></i> @lang('Sync Custom Design (Live Stream)')
                        </button>
                        @permit('admin.server.login')
                            <a href="{{ route('admin.server.login', $server->id) }}" class="btn btn-outline--primary btn-sm">
                                <i class="las la-external-link-alt"></i> @lang('Login to Panel')
                            </a>
                        @endpermit
                        <a href="{{ route('admin.servers') }}" class="btn btn-outline--dark btn-sm">
                            <i class="las la-arrow-left"></i> @lang('All Servers')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Domain / Plan')</th>
                            <th>@lang('Username')</th>
                            <th>@lang('Password')</th>
                            <th>@lang('Pricing')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $service)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ @$service->user->fullname }}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', $service->user_id) : 'javascript:void(0)' }}">
                                                <span>@</span>{{ @$service->user->username }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-mono text--primary">{{ $service->domain }}</span>
                                        <br>
                                        <span class="small text-muted">{{ __(@$service->product->name) }} • {{ __(@$service->product->serviceCategory->name) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 font-mono">
                                            <span class="fw-bold text-dark">{{ $service->username }}</span>
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ $service->username }}'); alert('@lang('Username copied!')')" class="btn btn-xs text-muted p-0" title="@lang('Copy Username')">
                                                <i class="las la-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 font-mono">
                                            <span class="fw-bold text-dark cred-masked" id="pwd_masked_{{ $service->id }}">••••••••••••</span>
                                            <span class="fw-bold text-dark cred-plain d-none" id="pwd_plain_{{ $service->id }}">{{ $service->password }}</span>
                                            <button type="button" onclick="togglePasswordView({{ $service->id }})" class="btn btn-xs text-muted p-0" title="@lang('Toggle View')">
                                                <i class="las la-eye" id="pwd_icon_{{ $service->id }}"></i>
                                            </button>
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ $service->password }}'); alert('@lang('Password copied!')')" class="btn btn-xs text-muted p-0" title="@lang('Copy Password')">
                                                <i class="las la-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            {{ gs('cur_sym') }}{{ getAmount($service->recurring_amount) }}
                                            {{ @billingCycle(@$service->billing_cycle, true)['showText'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @php echo $service->showStatus; @endphp
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            @permit('admin.order.hosting.details')
                                                <a href="{{ route('admin.order.hosting.details', $service->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </a>
                                            @endpermit

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline--info mergeServerBtn"
                                                data-id="{{ $service->id }}"
                                                data-domain="{{ $service->domain }}"
                                                data-current-server="{{ $service->server_id }}"
                                                data-current-server-name="{{ $server->name }}"
                                            >
                                                <i class="las la-random"></i> @lang('Reassign')
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        <div class="py-4">
                                            <i class="las la-folder-open text-muted fs-2 mb-2"></i>
                                            <p class="text-muted">@lang('No hosting accounts currently hosted on this server node.')</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($accounts->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($accounts) }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Merge Service to Server Modal --}}
<div class="modal fade" id="mergeServerModal" tabindex="-1" role="dialog" aria-labelledby="mergeServerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mergeServerModalLabel">@lang('Reassign Service to Another Server')</h5>
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
                        <label class="fw-bold">@lang('Select Target Server') <span class="text--danger">*</span></label>
                        <select name="target_server_id" class="form-control" required>
                            <option value="">@lang('Choose Target Server...')</option>
                            @foreach($allServers ?? [] as $srv)
                                <option value="{{ $srv->id }}">
                                    {{ $srv->name }} ({{ $srv->ip_address ?: $srv->host }}) - {{ @$srv->group->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">
                            @lang('This will instantly reassign the hosting service and overwrite authoritative DNS records on the destination node.')
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary"><i class="las la-check-circle"></i> @lang('Confirm & Reassign')</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.servers') }}" class="btn btn-outline--primary btn-sm">
        <i class="las la-server"></i> @lang('All Servers')
    </a>
@endpush

@push('script')
<script>
    function togglePasswordView(id) {
        let masked = document.getElementById('pwd_masked_' + id);
        let plain = document.getElementById('pwd_plain_' + id);
        let icon = document.getElementById('pwd_icon_' + id);

        if (plain.classList.contains('d-none')) {
            plain.classList.remove('d-none');
            masked.classList.add('d-none');
            icon.classList.remove('la-eye');
            icon.classList.add('la-eye-slash');
        } else {
            plain.classList.add('d-none');
            masked.classList.remove('d-none');
            icon.classList.remove('la-eye-slash');
            icon.classList.add('la-eye');
        }
    }

    (function ($) {
        "use strict";

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
