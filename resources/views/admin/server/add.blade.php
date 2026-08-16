@extends('admin.layouts.app')

@section('panel')
<form class="form-horizontal server-form" method="post" action="{{ route('admin.server.add') }}">
    @csrf 

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border--primary shadow-sm">
                <div class="card-header bg--primary d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="text-white mb-0"><i class="las la-bolt text-warning"></i> @lang('1-Click Quick VPS Auto-Merge')</h5>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="quickAutoMergeToggle" name="quick_vps_automerge" value="1" @checked(old('quick_vps_automerge', '1') == '1')>
                        <label class="form-check-label text-white font-weight-bold" for="quickAutoMergeToggle">@lang('Enable 1-Click Auto-Merge Mode')</label>
                    </div>
                </div>
                <div class="card-body bg--light" id="quickAutoMergeSection">
                    <p class="text-muted mb-3"><i class="las la-info-circle text--info"></i> @lang('Enter the IP Address and Root Password of any fresh Ubuntu 24.04 VPS. WHMLab will automatically install HestiaCP 1.10.2, sync the ZodPanel template layer, configure nameservers ns1/ns2.zodserver.cloud, setup Auto-SSL, and merge it into your cluster automatically.')</p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">@lang('VPS IP Address') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" name="vps_ip" placeholder="e.g. 169.58.176.53" value="{{ old('vps_ip') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">@lang('Root SSH Password') <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-lg" name="password" placeholder="@lang('VPS Root Password')">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 form-group">
            <div class="card">
                <div class="card-header w-100 bg--dark">
                    <h5 class="text--white">@lang('Name and Hostname')</h5>
                </div>
                <div class="card-body">
                    <div class="row"> 
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Select Group')</label>
                                <select name="server_group_id" class="form-control" required>
                                    <option value="" hidden>@lang('Select One')</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" data-type="{{ $group->getType }}">
                                            {{ __($group->name) }} ({{ $group->getType }})
                                        </option>
                                    @endforeach
                                </select> 
                            </div> 
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Name')</label>
                                <input type="text" class="form-control" name="name" required value="{{old('name')}}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Service Role')</label>
                                <select name="service_role" class="form-control" required>
                                    @foreach($serviceRoles as $key => $label)
                                        <option value="{{ $key }}" @selected(old('service_role', 'any') == $key)>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">@lang('Used for automatic node selection across multiple servers.')</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Location')</label>
                                <input type="text" class="form-control" name="location" value="{{ old('location') }}" placeholder="@lang('New York, London, Lagos')">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-2 col-lg-12 col-xxl-3">
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <div>
                                        <label>@lang('Protocol')</label>
                                    </div>
                                </div>
                                <select name="protocol" class="form-control">
                                    <option value="https://" {{ old('protocol') == 'https://' ? 'selected' : null }}>@lang('https')</option>
                                    <option value="http://" {{ old('protocol') == 'http://' ? 'selected' : null }}>@lang('http')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-8 col-xxl-6">
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <div>
                                        <label>@lang('Hostname')</label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="host" required value="{{old('host')}}" placeholder="abc.server.com">
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-4 col-xxl-3"> 
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <div>
                                        <label>@lang('Port')</label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="port" required value="{{old('port', 8083)}}" placeholder="8083">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>@lang('Max Accounts')</label>
                                <input type="number" min="0" class="form-control" name="max_accounts" value="{{ old('max_accounts', 0) }}" placeholder="@lang('0 means unlimited')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <div class="card">
                    <div class="card-header bg--dark">
                        <h5 class="text--white">@lang('Server Details')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input type="text" class="form-control" name="username" required value="{{old('username')}}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            <div class="col-lg-6 cpanel-input">
                                <div class="form-group">
                                    <label>@lang('API Token')</label>
                                    <input type="text" class="form-control" name="api_token" value="{{ old('api_token') }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6 cpanel-input">
                                <div class="form-group">
                                    <label>@lang('Security Token')</label>
                                    <input type="text" class="form-control" name="security_token" value="{{ old('security_token') }}" disabled placeholder="123456789">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('SSH Port')</label>
                                    <input type="number" min="1" max="65535" class="form-control" name="ssh_port" value="{{ old('ssh_port', 22) }}">
                                    <small class="text-muted">@lang('Used only for automated ZodPanel bootstrap/sync.')</small>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="justify-content-between d-flex">
                                        <label>@lang('Test')</label>
                                        <div class="connection d-none">
                                            <i>
                                                <i class="icon"></i>
                                                <small class="response">@lang('Attempting to connect to server')...</small>
                                            </i>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn--success h-45 w-100 testConnection">@lang('Test Connection') <i class="las la-angle-double-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 zodpanel-bootstrap-box d-none">
                <div class="card border--primary">
                    <div class="card-header bg--primary">
                        <h5 class="text--white">@lang('Automated ZodPanel Bootstrap')</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">@lang('For a new ZodPanel node, enter only the VPS SSH login above, then let WHMLab install Hestia, sync the custom ZodPanel layer, generate the bridge token, and save the node.')</p>
                        <div class="form-group">
                            <label class="d-flex align-items-center gap-2">
                                <input type="checkbox" name="bootstrap_zodpanel" value="1" @checked(old('bootstrap_zodpanel'))>
                                <span>@lang('Install/sync customized ZodPanel on this VPS before saving')</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="d-flex align-items-center gap-2">
                                <input type="checkbox" name="clean_server_confirmed" value="1" @checked(old('clean_server_confirmed'))>
                                <span>@lang('If this VPS is not fresh, I authorize WHMLab to clean hosting stack packages first')</span>
                            </label>
                            <small class="text-danger d-block">@lang('This is destructive. Use it only on a VPS dedicated to becoming a ZodPanel node.')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Version Description')</label>
                            <input type="text" class="form-control" name="deployment_note" value="{{ old('deployment_note') }}" placeholder="@lang('Initial EU node bootstrap, package sync update, etc.')">
                        </div>
                        <button type="button" class="btn btn-outline--primary w-100 h-45 previewZodPanel">
                            @lang('Preview VPS Readiness')
                        </button>
                        <pre class="zodpanel-preview-log mt-3 d-none"></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 form-group">
            <div class="card h-100">
                <div class="card-header bg--dark">
                    <h5 class="text--white">@lang('Nameservers')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Primary Nameserver')</label>
                                <input type="text" class="form-control" name="ns1" value="{{ old('ns1') }}" required disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns1_ip" value="{{ old('ns1_ip') }}" required disabled>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Secondary Nameserver')</label>
                                <input type="text" class="form-control" name="ns2" value="{{ old('ns2') }}" required disabled> 
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns2_ip" value="{{ old('ns2_ip') }}" required disabled>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Third Nameserver')</label>
                                <input type="text" class="form-control" name="ns3" value="{{ old('ns3') }}" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns3_ip" value="{{ old('ns3_ip') }}" disabled>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Fourth Nameserver')</label>
                                <input type="text" class="form-control" name="ns4" value="{{ old('ns4') }}" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns4_ip" value="{{ old('ns4_ip') }}" disabled>
                            </div>
                        </div>

                    </div> 
                </div>
            </div>
        </div>
        
        @permit('admin.server.add')
            <div class="col-lg-12 mt-3">
                <button type="submit" class="btn btn--primary h-45 w-100" disabled>@lang('Submit')</button>
            </div>
        @endpermit
    </div>
</form> 
@if(session('zodpanel_bootstrap_log'))
    <div class="card mt-3">
        <div class="card-header bg--dark"><h5 class="text--white">@lang('ZodPanel Bootstrap Log')</h5></div>
        <div class="card-body">
            <pre class="mb-0">{{ implode("\n", session('zodpanel_bootstrap_log')) }}</pre>
        </div>
    </div>
@endif
@endsection

@permit('admin.servers')
    @push('breadcrumb-plugins')
        <a href="{{ route('admin.servers') }}" class="btn btn-sm btn-outline--primary">
            <i class="la la-undo"></i> @lang('Go to Servers')
        </a>
    @endpush
@endpermit

@push('script')
    <script>
        (function($){
            "use strict"; 

            var oldGroup = '{{ old("server_group_id") }}'; 
          
            if(oldGroup){
                $('select[name=server_group_id]').val(oldGroup);
            }

            $('select[name=server_group_id]').on('change', function() {
                var type = $(this).find('option:selected').data('type');
                
                if(type == 'Cpanel' || type == 'Whmpanel'){
                    $('.cpanel-input').removeClass('d-none');
                    $('.cpanel-input input').removeAttr('disabled');
                }else{
                    $('.cpanel-input').addClass('d-none');
                    $('.cpanel-input input').attr('disabled', true);
                }

                if(type == 'Whmpanel'){
                    $('.zodpanel-bootstrap-box').removeClass('d-none');
                }else{
                    $('.zodpanel-bootstrap-box').addClass('d-none');
                }
            }).change();

            $('.testConnection').on('click', function(){
                @permit('admin.server.test.connection')
                    $.ajax({ 
                        type:'POST',
                        url:'{{ route("admin.server.test.connection") }}',
                        data: $('.server-form').serialize(),
                        beforeSend: function() {
                            $('.connection').removeClass('d-none');
                            $('.connection .icon').html('<i class="fas fa-spinner fa-spin"></i>');
                            $('.connection .respone').text('Attempting to connect to server...');
                        },

                        success:function(response){
                            setTimeout(function() {
                                if(response.success){
                                    $('.connection').addClass('d-none');
                                    $("*[disabled]").not(true).removeAttr("disabled");
                                }
                                else if(response.error){
                                    $.each(response.error, function(key, value) {
                                        notify('error', value);
                                    });
                                    $('.connection').addClass('d-none');
                                }
                                else{
                                    notify('error', response.message);
                                    $('.connection .icon').html('<i class="fas fa-times"></i>');
                                }
                            }, 200);
                        }
                    });
                @endpermit
            });

            $('input[name=bootstrap_zodpanel], input[name=host]').on('change keyup', function(){
                var enabled = $('input[name=bootstrap_zodpanel]').is(':checked');
                if(!enabled){
                    return;
                }

                var host = $('input[name=host]').val();
                var nsBase = host || 'zodpanel.local';
                $('select[name=protocol]').val('https://');
                $('input[name=port]').val($('input[name=port]').val() || '8083');

                if(!$('input[name=ns1]').val()){
                    $('input[name=ns1]').val('ns1.' + nsBase);
                }
                if(!$('input[name=ns2]').val()){
                    $('input[name=ns2]').val('ns2.' + nsBase);
                }
                if(!$('input[name=ns1_ip]').val()){
                    $('input[name=ns1_ip]').val(host);
                }
                if(!$('input[name=ns2_ip]').val()){
                    $('input[name=ns2_ip]').val(host);
                }

                $('input[name^=ns]').removeAttr('disabled');
                $('button[type=submit]').removeAttr('disabled');
            }).change();

            $('.previewZodPanel').on('click', function(){
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.server.zodpanel.bootstrap.preview") }}',
                    data: $('.server-form').serialize(),
                    beforeSend: function() {
                        $('.zodpanel-preview-log').removeClass('d-none').text('Checking VPS readiness...');
                    },
                    success:function(response){
                        var lines = [];
                        lines.push(response.message || 'Preview finished');
                        if(response.data){
                            lines.push('');
                            lines.push('Hostname: ' + (response.data.hostname || 'unknown'));
                            lines.push('IP: ' + (response.data.ip_address || 'unknown'));
                            lines.push('Hestia installed: ' + (response.data.hestia_installed ? 'yes' : 'no'));
                            lines.push('ZodPanel bridge: ' + (response.data.zodpanel_bridge_installed ? 'yes' : 'no'));
                            lines.push('Fresh VPS: ' + (response.data.fresh ? 'yes' : 'no'));
                            lines.push('Detected stack: ' + ((response.data.detected_stack || []).join(', ') || 'none'));
                        }
                        if(response.log && response.log.length){
                            lines.push('');
                            lines = lines.concat(response.log);
                        }
                        $('.zodpanel-preview-log').text(lines.join("\n"));
                    }
                });
            });
        })(jQuery);
    </script> 
@endpush
