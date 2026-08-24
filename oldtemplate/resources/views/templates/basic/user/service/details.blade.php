@extends($activeTemplate . 'layouts.master')

@section('content')
@php
    $isActive = ($service->status == 1);
    $isPending = ($service->status == 2);
    $isSuspended = ($service->status == 3);
    $isTerminated = ($service->status == 4);
    $isCancelled = ($service->status == 5);
    $nodeHost = $service->server->ip_address ?? (parse_url($service->server->hostname ?? '', PHP_URL_HOST) ?: '169.58.176.53');
@endphp

<div class="py-8 bg-[#F8FAFC] text-slate-900 min-h-screen font-sans space-y-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Breadcrumb & Actions Bar -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('user.home') }}" class="hover:text-slate-900 transition-colors">@lang('Dashboard')</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <a href="{{ route('user.service.list') }}" class="hover:text-slate-900 transition-colors">@lang('Services')</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="text-slate-900 font-semibold font-mono">{{ $service->domain ?? $product->name }}</span>
            </div>
            
            <div class="flex items-center gap-2">
                @if(!$service->cancelRequest && !$isCancelled && !$isTerminated)
                    <button type="button" data-bs-toggle="modal" data-bs-target="#cancelModal" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-xs font-semibold rounded-lg transition-all text-rose-700 flex items-center gap-1.5 shadow-xs">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                        <span>@lang('Request Cancellation')</span>
                    </button>
                @endif
                <a href="{{ route('user.service.list') }}" class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 text-xs font-semibold rounded-lg transition-all text-slate-700 flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>@lang('Back to Services')</span>
                </a>
            </div>
        </div>

        {{-- ── STATUS ALERTS (PENDING / SUSPENDED) ── --}}
        @if($isPending)
            <div class="p-6 bg-amber-50/90 border border-amber-200 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 border border-amber-200 text-amber-700 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="clock" class="w-5 h-5 animate-pulse"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-amber-900 font-display">@lang('Automated Provisioning In Progress')</h4>
                        <p class="text-xs text-amber-800 leading-relaxed max-w-2xl">
                            @lang('Your service order for') <span class="font-bold font-mono">{{ $service->domain }}</span> @lang('is queued on the node cluster. Control panel access, database tools, and webmail unlock immediately upon completion.')
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-center">
                    <a href="{{ route('user.invoice.list') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors flex items-center gap-1.5">
                        <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                        <span>@lang('View Invoices')</span>
                    </a>
                </div>
            </div>
        @elseif($isSuspended)
            <div class="p-6 bg-rose-50/90 border border-rose-200 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-200 text-rose-700 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-rose-900 font-display">@lang('Hosting Service Suspended')</h4>
                        <p class="text-xs text-rose-800 leading-relaxed max-w-2xl">
                            @lang('This instance is suspended due to an overdue billing cycle. Please settle outstanding invoices to immediately reactivate web traffic and management.')
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-center">
                    <a href="{{ route('user.invoice.list') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors flex items-center gap-1.5">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                        <span>@lang('Pay Overdue Invoice')</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Hero Header & Quick SSO Bar -->
        <div class="p-6 lg:p-8 bg-white border border-slate-200/80 rounded-2xl relative overflow-hidden space-y-6 shadow-sm">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2">
                    <div class="flex items-center gap-3 flex-wrap">
                        @if($isActive)
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 orb-pulse"></span>
                                @lang('Active & Online')
                            </span>
                        @elseif($isPending)
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                @lang('Pending Setup')
                            </span>
                        @elseif($isSuspended)
                            <span class="px-2.5 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                @lang('Suspended')
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold">
                                {{ @App\Models\Hosting::status()[$service->status] ?? 'Unknown' }}
                            </span>
                        @endif

                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-mono">
                            IP: {{ $service->server->ip_address ?? ($isActive ? 'Assigned' : 'Pending') }}
                        </span>

                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <i data-lucide="server" class="w-3.5 h-3.5 text-indigo-600"></i>
                            {{ $service->server->name ?? $service->server->hostname ?? 'Cloud Server' }}
                        </span>
                    </div>

                    <!-- Domain Name Display + Copy -->
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight font-display">
                            {{ $service->domain ?? $product->name }}
                        </h1>
                        @if($service->domain)
                            <button onclick="navigator.clipboard.writeText('{{ $service->domain }}'); alert('@lang('Domain copied!')')" 
                                class="p-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-500 hover:text-slate-900 transition-colors shadow-xs" title="@lang('Copy domain')">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                            </button>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500">{{ $product->name }} • {{ $product->serviceCategory->name ?? 'Hosting' }}</p>
                </div>

                <!-- Primary Real-Time SSO Launch Buttons -->
                <div class="flex flex-wrap items-center gap-2.5">
                    @if($isActive)
                        <a href="{{ $ssoLinks['panel'] ?? route('user.login.hosting', $service->id) }}" target="_blank" rel="noopener"
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-2">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            <span>@lang('Control Panel (SSO)')</span>
                        </a>
                        <a href="{{ $ssoLinks['file_manager'] ?? 'https://'.$nodeHost.':8083/fm/' }}" target="_blank" rel="noopener"
                            class="px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                            <i data-lucide="folder" class="w-4 h-4 text-amber-500"></i>
                            <span>@lang('File Manager')</span>
                        </a>
                        <a href="{{ $ssoLinks['phpmyadmin'] ?? 'https://'.$nodeHost.':8083/open/phpmyadmin/' }}" target="_blank" rel="noopener"
                            class="px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                            <i data-lucide="database" class="w-4 h-4 text-indigo-600"></i>
                            <span>@lang('phpMyAdmin')</span>
                        </a>
                        <a href="{{ $ssoLinks['webmail'] ?? 'https://webmail.'.$service->domain }}" target="_blank" rel="noopener"
                            class="px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                            <i data-lucide="mail" class="w-4 h-4 text-cyan-600"></i>
                            <span>@lang('Webmail')</span>
                        </a>
                    @else
                        <button disabled class="px-4 py-2.5 bg-slate-100 border border-slate-200 text-slate-400 text-xs font-semibold rounded-lg cursor-not-allowed flex items-center gap-2">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            <span>@lang('Tools Locked (Pending)')</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Tabbed Navigation Bar -->
            <div class="border-t border-slate-200/80 pt-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none" id="serviceTabs">
                    <button onclick="switchTab('overview')" id="tab-overview" class="service-tab-btn px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap shadow-xs">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>@lang('Overview')</span>
                    </button>
                    <button onclick="switchTab('databases')" id="tab-databases" class="service-tab-btn px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap border border-slate-200">
                        <i data-lucide="database" class="w-4 h-4 text-slate-500"></i>
                        <span>@lang('Databases')</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 text-slate-700 font-mono">{{ count($databases) }}</span>
                    </button>
                    <button onclick="switchTab('mailboxes')" id="tab-mailboxes" class="service-tab-btn px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap border border-slate-200">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-500"></i>
                        <span>@lang('Mailboxes')</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 text-slate-700 font-mono">{{ count($mailAccounts) }}</span>
                    </button>
                    <button onclick="switchTab('dns')" id="tab-dns" class="service-tab-btn px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap border border-slate-200">
                        <i data-lucide="network" class="w-4 h-4 text-slate-500"></i>
                        <span>@lang('DNS Zone')</span>
                    </button>
                    <button onclick="switchTab('security')" id="tab-security" class="service-tab-btn px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap border border-slate-200">
                        <i data-lucide="shield-check" class="w-4 h-4 text-slate-500"></i>
                        <span>@lang('SSL Security')</span>
                    </button>
                    <button onclick="switchTab('advanced')" id="tab-advanced" class="service-tab-btn px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap border border-slate-200">
                        <i data-lucide="sliders" class="w-4 h-4 text-slate-500"></i>
                        <span>@lang('PHP & Tools')</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── TAB CONTENT PANELS ── --}}

        <!-- 1. OVERVIEW TAB -->
        <div id="tab-content-overview" class="tab-pane space-y-6">
            <!-- Resource Usage Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Disk Usage -->
                <div class="p-5 bg-white border border-slate-200/80 rounded-2xl text-center space-y-3 shadow-sm">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">@lang('Disk Space')</div>
                    <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100 stroke-current" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-indigo-600 stroke-current" stroke-width="3.5" stroke-dasharray="{{ min(100, max(0, $diskUsagePercent)) }}, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-base font-bold font-mono text-slate-900">{{ round($diskUsagePercent) }}%</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono">
                        {{ @$accountSummary['disk_used_text'] ?: ($isActive ? '0 MB' : '0 MB') }} / {{ $product->disk_space ?? '10 GB' }}
                    </div>
                </div>

                <!-- Bandwidth Usage -->
                <div class="p-5 bg-white border border-slate-200/80 rounded-2xl text-center space-y-3 shadow-sm">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">@lang('Bandwidth')</div>
                    <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100 stroke-current" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-cyan-600 stroke-current" stroke-width="3.5" stroke-dasharray="{{ min(100, max(0, $bwUsagePercent)) }}, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-base font-bold font-mono text-slate-900">{{ round($bwUsagePercent) }}%</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono">
                        {{ @$accountSummary['bw_used_text'] ?: '0 MB' }} / {{ $product->bandwidth ?? 'Unlimited' }}
                    </div>
                </div>

                <!-- Databases -->
                <div class="p-5 bg-white border border-slate-200/80 rounded-2xl text-center space-y-3 shadow-sm">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">@lang('Active Databases')</div>
                    <div class="w-20 h-20 mx-auto rounded-full bg-slate-50 border border-slate-200 flex flex-col items-center justify-center">
                        <i data-lucide="database" class="w-5 h-5 text-indigo-600 mb-0.5"></i>
                        <span class="text-base font-extrabold font-mono text-slate-900">{{ count($databases) }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono">
                        {{ count($databases) }} @lang('Live DBs')
                    </div>
                </div>

                <!-- Mailboxes -->
                <div class="p-5 bg-white border border-slate-200/80 rounded-2xl text-center space-y-3 shadow-sm">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">@lang('Mail Accounts')</div>
                    <div class="w-20 h-20 mx-auto rounded-full bg-slate-50 border border-slate-200 flex flex-col items-center justify-center">
                        <i data-lucide="mail" class="w-5 h-5 text-cyan-600 mb-0.5"></i>
                        <span class="text-base font-extrabold font-mono text-slate-900">{{ count($mailAccounts) }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono">
                        {{ count($mailAccounts) }} @lang('Mailboxes')
                    </div>
                </div>
            </div>

            <!-- Credentials & Nameservers 2-Col Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Credentials Card -->
                <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 font-display flex items-center justify-between">
                        <span>@lang('Control Panel Credentials')</span>
                        <span class="text-[11px] font-mono px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                            @lang('Node Active')
                        </span>
                    </h3>

                    <div class="space-y-3 text-xs divide-y divide-slate-100">
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-slate-500">@lang('Control Panel URL')</span>
                            <a href="https://{{ $nodeHost }}:8083/login/" target="_blank" rel="noopener" class="font-mono text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                                https://{{ $nodeHost }}:8083
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-slate-500">@lang('Username')</span>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-slate-900 font-bold text-sm" id="credUsernameVal">{{ $service->username }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $service->username }}'); alert('@lang('Username copied!')')" class="text-slate-400 hover:text-slate-900 p-1" title="@lang('Copy')">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-slate-500">@lang('Password')</span>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-slate-900 font-bold text-sm" id="credPasswordMasked">••••••••••••</span>
                                <span class="font-mono text-slate-900 font-bold text-sm hidden" id="credPasswordPlain">{{ $service->password }}</span>
                                <button type="button" onclick="let m=document.getElementById('credPasswordMasked'), p=document.getElementById('credPasswordPlain'); if(p.classList.contains('hidden')){ p.classList.remove('hidden'); m.classList.add('hidden'); } else { p.classList.add('hidden'); m.classList.remove('hidden'); }" class="text-slate-400 hover:text-slate-900 p-1" title="@lang('Toggle view')">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </button>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $service->password }}'); alert('@lang('Password copied!')')" class="text-slate-400 hover:text-slate-900 p-1" title="@lang('Copy')">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-slate-500">@lang('Server Dedicated IP')</span>
                            <span class="font-mono text-slate-900 font-semibold">{{ $service->server->ip_address ?? '169.58.176.53' }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-slate-500">@lang('Billing Cycle & Fee')</span>
                            <span class="font-medium text-slate-900">{{ @billingCycle($service->billing_cycle, true)['showText'] ?? 'Monthly' }} • {{ gs('cur_sym') }}{{ showAmount($service->price) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Nameservers Card -->
                <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 font-display">@lang('Authoritative Nameservers')</h3>
                    <p class="text-xs text-slate-500">@lang('Point your domain registrar to these nameservers to route DNS records to this instance.')</p>

                    <div class="space-y-2 text-xs">
                        @forelse($nameservers as $ns)
                            <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between font-mono">
                                <div>
                                    <span class="text-indigo-600 font-bold me-2">{{ $ns['label'] }}:</span>
                                    <span class="text-slate-900 font-semibold">{{ $ns['host'] }}</span>
                                    @if(!empty($ns['ip']))
                                        <span class="text-slate-400 text-[11px] ms-1">({{ $ns['ip'] }})</span>
                                    @endif
                                </div>
                                <button onclick="navigator.clipboard.writeText('{{ $ns['host'] }}'); alert('@lang('Nameserver copied!')')" class="text-slate-400 hover:text-slate-900 p-1" title="@lang('Copy')">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        @empty
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 text-center">
                                @lang('Nameservers assigned on node allocation.')
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. DATABASES TAB (REAL-TIME) -->
        <div id="tab-content-databases" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-display">@lang('MySQL Databases (Real-Time)')</h3>
                        <p class="text-xs text-slate-500">@lang('Live relational databases active on the hosting cluster.')</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($isActive)
                            <a href="{{ $ssoLinks['phpmyadmin'] ?? 'https://'.$nodeHost.':8083/open/phpmyadmin/' }}" target="_blank" rel="noopener" class="px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                                <i data-lucide="external-link" class="w-3.5 h-3.5 text-indigo-600"></i>
                                <span>@lang('Open phpMyAdmin (SSO)')</span>
                            </a>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#createDbModal" class="px-3.5 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 shadow-xs flex items-center gap-1.5">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>@lang('Create Database')</span>
                            </button>
                        @endif
                    </div>
                </div>

                @if(count($databases) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 rounded-l-lg">@lang('Database Name')</th>
                                    <th class="px-4 py-3">@lang('DB User')</th>
                                    <th class="px-4 py-3">@lang('Host')</th>
                                    <th class="px-4 py-3">@lang('Charset')</th>
                                    <th class="px-4 py-3 rounded-r-lg text-right">@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                @foreach($databases as $dbKey => $db)
                                    @php
                                        $dbName = is_array($db) ? ($db['DATABASE'] ?? $db['database'] ?? $dbKey) : $dbKey;
                                        $dbUser = is_array($db) ? ($db['DBUSER'] ?? $db['dbuser'] ?? $db['db_user'] ?? $dbName) : $dbName;
                                        $dbHost = is_array($db) ? ($db['HOST'] ?? 'localhost') : 'localhost';
                                        $dbCharset = is_array($db) ? ($db['CHARSET'] ?? 'UTF8MB4') : 'UTF8MB4';
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-slate-900 font-bold flex items-center gap-2">
                                            <i data-lucide="database" class="w-3.5 h-3.5 text-indigo-600"></i>
                                            <span>{{ $dbName }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">{{ $dbUser }}</td>
                                        <td class="px-4 py-3 text-slate-500">{{ $dbHost }}</td>
                                        <td class="px-4 py-3 text-slate-500 uppercase">{{ $dbCharset }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[11px] font-bold">@lang('Active')</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center bg-slate-50 border border-slate-200/80 rounded-xl space-y-2">
                        <i data-lucide="database" class="w-8 h-8 text-slate-400 mx-auto"></i>
                        <p class="text-xs text-slate-600 font-semibold">@lang('No databases created yet for this hosting service.')</p>
                        @if($isActive)
                            <button type="button" data-bs-toggle="modal" data-bs-target="#createDbModal" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>@lang('Create First Database')</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- 3. MAILBOXES TAB (REAL-TIME) -->
        <div id="tab-content-mailboxes" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-display">@lang('Custom Domain Mailboxes (Real-Time)')</h3>
                        <p class="text-xs text-slate-500">@lang('Branded email accounts configured on') {{ $service->domain ?? 'your domain' }}.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($isActive)
                            <a href="{{ $ssoLinks['webmail'] ?? 'https://webmail.'.$service->domain }}" target="_blank" rel="noopener" class="px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                                <i data-lucide="mail" class="w-3.5 h-3.5 text-cyan-600"></i>
                                <span>@lang('Launch Webmail')</span>
                            </a>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#createMailModal" class="px-3.5 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 shadow-xs flex items-center gap-1.5">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>@lang('Create Mailbox')</span>
                            </button>
                        @endif
                    </div>
                </div>

                @if(count($mailAccounts) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 rounded-l-lg">@lang('Email Address')</th>
                                    <th class="px-4 py-3">@lang('Quota Allocation')</th>
                                    <th class="px-4 py-3">@lang('Status')</th>
                                    <th class="px-4 py-3 rounded-r-lg text-right">@lang('Webmail Access')</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                @foreach($mailAccounts as $mKey => $mail)
                                    @php
                                        $accountName = is_array($mail) ? ($mail['account'] ?? $mail['email'] ?? $mKey) : $mKey;
                                        $fullEmail = str_contains($accountName, '@') ? $accountName : ($accountName . '@' . $service->domain);
                                        $quota = is_array($mail) ? ($mail['QUOTA'] ?? $mail['quota'] ?? 'Unlimited') : '1000 MB';
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-slate-900 font-bold flex items-center gap-2">
                                            <i data-lucide="mail" class="w-3.5 h-3.5 text-cyan-600"></i>
                                            <span>{{ $fullEmail }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600">{{ $quota == 'unlimited' ? 'Unlimited' : $quota . (is_numeric($quota) ? ' MB' : '') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[11px] font-bold">@lang('Active')</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($isActive)
                                                <a href="https://webmail.{{ $service->domain }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-sans font-semibold inline-flex items-center gap-1">
                                                    <span>@lang('Login')</span>
                                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                                </a>
                                            @else
                                                <span class="text-slate-400 font-sans">@lang('Pending')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center bg-slate-50 border border-slate-200/80 rounded-xl space-y-2">
                        <i data-lucide="mail" class="w-8 h-8 text-slate-400 mx-auto"></i>
                        <p class="text-xs text-slate-600 font-semibold">@lang('No custom mailboxes created yet.')</p>
                        @if($isActive)
                            <button type="button" data-bs-toggle="modal" data-bs-target="#createMailModal" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>@lang('Create First Mailbox')</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- 4. DNS ZONE TAB (REAL-TIME) -->
        <div id="tab-content-dns" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-display">@lang('Authoritative DNS Zone & Records')</h3>
                        <p class="text-xs text-slate-500">@lang('DNS routing zone for') {{ $service->domain ?? 'your domain' }}.</p>
                    </div>
                    @if($isActive)
                        <form action="{{ route('user.service.dns.repair', $service->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg shadow-xs flex items-center gap-1.5 transition-colors">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-indigo-600"></i>
                                <span>@lang('Sync & Repair DNS Zone')</span>
                            </button>
                        </form>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">@lang('Host / Name')</th>
                                <th class="px-4 py-3">@lang('Type')</th>
                                <th class="px-4 py-3">@lang('Routing Target / Value')</th>
                                <th class="px-4 py-3 rounded-r-lg text-right">@lang('TTL')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono">
                            @if(count($dnsRecords) > 0)
                                @foreach($dnsRecords as $rec)
                                    <tr>
                                        <td class="px-4 py-3 text-slate-900 font-bold">{{ $rec['name'] ?? '@' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[11px]">{{ $rec['type'] ?? 'A' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-800">{{ $rec['value'] ?? $service->server->ip_address }}</td>
                                        <td class="px-4 py-3 text-right text-slate-500">{{ $rec['ttl'] ?? 3600 }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="px-4 py-3 text-slate-900 font-bold">@</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[11px]">A</span></td>
                                    <td class="px-4 py-3">{{ $service->server->ip_address ?? 'Auto-Assigned' }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">3600</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-slate-900 font-bold">www</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-cyan-50 text-cyan-700 font-bold text-[11px]">CNAME</span></td>
                                    <td class="px-4 py-3">{{ $service->domain ?? 'example.com' }}.</td>
                                    <td class="px-4 py-3 text-right text-slate-500">3600</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-slate-900 font-bold">mail</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[11px]">A</span></td>
                                    <td class="px-4 py-3">{{ $service->server->ip_address ?? 'Auto-Assigned' }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">3600</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-slate-900 font-bold">webmail</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[11px]">A</span></td>
                                    <td class="px-4 py-3">{{ $service->server->ip_address ?? 'Auto-Assigned' }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">3600</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. SSL SECURITY TAB (REAL-TIME) -->
        <div id="tab-content-security" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-6 shadow-sm">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-display">@lang('SSL / TLS Encryption & HTTPS')</h3>
                    <p class="text-xs text-slate-500 mt-1">@lang('Automated 2048-bit SAN certificates and Force HTTPS redirection.')</p>
                </div>

                <div class="p-5 {{ $isActive ? 'bg-emerald-50/80 border-emerald-200' : 'bg-slate-50 border-slate-200' }} border rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl {{ $isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center flex-shrink-0">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 font-display">
                                {{ $isActive ? __('AutoSSL Active (Let\'s Encrypt SAN)') : __('SSL Verification Queued') }}
                            </div>
                            <div class="text-xs text-slate-600 mt-0.5">
                                @lang('Securing apex, www, mail, and webmail for') <span class="font-mono font-semibold">{{ $service->domain }}</span>
                            </div>
                        </div>
                    </div>
                    @if($isActive)
                        <form action="{{ route('user.service.ssl.issue', $service->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-white hover:bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-semibold rounded-lg shadow-xs transition-colors flex items-center gap-1.5">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                <span>@lang('Re-Issue / Force AutoSSL')</span>
                            </button>
                        </form>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-mono">
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                        <span class="text-slate-500 text-[11px] font-sans">@lang('Certificate Authority')</span>
                        <div class="font-bold text-slate-900">Let's Encrypt Authority X3</div>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                        <span class="text-slate-500 text-[11px] font-sans">@lang('Key Length')</span>
                        <div class="font-bold text-slate-900">RSA 2048-bit SAN</div>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                        <span class="text-slate-500 text-[11px] font-sans">@lang('Force HTTPS Redirection')</span>
                        <div class="font-bold text-emerald-700 flex items-center gap-1">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>Enabled (HTTP/2 + TLS 1.3)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. PHP & DEVELOPER TOOLS (REAL-TIME) -->
        <div id="tab-content-advanced" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-6 shadow-sm">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-display">@lang('PHP Runtime & Server Maintenance Tools')</h3>
                    <p class="text-xs text-slate-500 mt-1">@lang('Switch active PHP interpreter versions and run server diagnostics in real time.')</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dynamic Real-Time PHP Version Selector -->
                    <div class="p-5 bg-slate-50 border border-slate-200/80 rounded-xl space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-800">@lang('Active PHP Engine')</label>
                            <p class="text-[11px] text-slate-500">@lang('Change PHP interpreter dynamically for this website domain.')</p>
                        </div>
                        @if($isActive)
                            <form action="{{ route('user.service.php.change', $service->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <select name="php_version" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900 font-mono">
                                    @if(count($availablePhp) > 0)
                                        @foreach($availablePhp as $pOption)
                                            @php
                                                $tplVal = is_array($pOption) ? ($pOption['template'] ?? '') : $pOption;
                                                $tplLabel = is_array($pOption) ? ($pOption['label'] ?? $tplVal) : $tplVal;
                                            @endphp
                                            <option value="{{ $tplVal }}" @selected($phpVersion == $tplVal)>
                                                {{ $tplLabel }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="PHP-8_3" @selected(str_contains($phpVersion, '8_3') || $phpVersion == '8.3')>PHP 8.3 (Recommended)</option>
                                        <option value="PHP-8_2" @selected(str_contains($phpVersion, '8_2') || $phpVersion == '8.2')>PHP 8.2 (Stable)</option>
                                        <option value="PHP-8_1" @selected(str_contains($phpVersion, '8_1') || $phpVersion == '8.1')>PHP 8.1</option>
                                        <option value="PHP-8_0" @selected(str_contains($phpVersion, '8_0') || $phpVersion == '8.0')>PHP 8.0</option>
                                        <option value="PHP-7_4" @selected(str_contains($phpVersion, '7_4') || $phpVersion == '7.4')>PHP 7.4 (Legacy)</option>
                                    @endif
                                </select>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                                    @lang('Apply PHP Version')
                                </button>
                            </form>
                        @else
                            <p class="text-xs text-slate-500 italic">@lang('Available once service is active.')</p>
                        @endif
                    </div>

                    <!-- Webmail Router Repair -->
                    <div class="p-5 bg-slate-50 border border-slate-200/80 rounded-xl space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-800">@lang('Webmail Router Repair')</label>
                            <p class="text-[11px] text-slate-500">@lang('Re-sync webmail DNS aliases and roundcube authentication endpoints.')</p>
                        </div>
                        @if($isActive)
                            <form action="{{ route('user.service.zodpanel.webmail.repair', $service->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition-colors flex items-center gap-2">
                                    <i data-lucide="wrench" class="w-3.5 h-3.5 text-indigo-600"></i>
                                    <span>@lang('Repair Webmail Router')</span>
                                </button>
                            </form>
                        @else
                            <p class="text-xs text-slate-500 italic">@lang('Available once service is active.')</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── MODALS ── --}}

<!-- Create Database Modal -->
@if($isActive)
<div class="modal fade" id="createDbModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-2xl">
            <div class="modal-header border-b border-slate-100 p-5">
                <h5 class="modal-title text-sm font-bold text-slate-900 font-display">@lang('Create MySQL Database')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.service.database.create', $service->id) }}" method="POST">
                @csrf
                <div class="modal-body p-5 space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">@lang('Database Name')</label>
                        <div class="flex items-center gap-2 font-mono">
                            <span class="text-slate-500 font-bold">{{ $service->username }}_</span>
                            <input type="text" name="database" required placeholder="app_db" class="flex-1 bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900 font-mono">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">@lang('Database User')</label>
                        <div class="flex items-center gap-2 font-mono">
                            <span class="text-slate-500 font-bold">{{ $service->username }}_</span>
                            <input type="text" name="dbuser" placeholder="db_user" class="flex-1 bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900 font-mono">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">@lang('User Password')</label>
                        <input type="password" name="password" required placeholder="••••••••••••" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900 font-mono">
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 p-4">
                    <button type="button" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs">@lang('Create Database')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Mailbox Modal -->
<div class="modal fade" id="createMailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-2xl">
            <div class="modal-header border-b border-slate-100 p-5">
                <h5 class="modal-title text-sm font-bold text-slate-900 font-display">@lang('Create Custom Mailbox')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.service.email.create', $service->id) }}" method="POST">
                @csrf
                <input type="hidden" name="v_domain" value="{{ $service->domain }}">
                <div class="modal-body p-5 space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">@lang('Email Account Prefix')</label>
                        <div class="flex items-center gap-2 font-mono">
                            <input type="text" name="v_account" required placeholder="contact" class="flex-1 bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900">
                            <span class="text-slate-500 font-bold">@ {{ $service->domain }}</span>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">@lang('Mailbox Password')</label>
                        <input type="password" name="v_password" required placeholder="••••••••••••" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900 font-mono">
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">@lang('Storage Quota (MB)')</label>
                        <input type="number" name="v_quota" value="1000" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900 font-mono">
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 p-4">
                    <button type="button" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs">@lang('Create Mailbox')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Cancellation Modal -->
@if(!$service->cancelRequest)
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-2xl">
            <div class="modal-header border-b border-slate-100 p-5">
                <h5 class="modal-title text-sm font-bold text-slate-900 font-display">@lang('Request Service Cancellation')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.service.cancel.request') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $service->id }}">
                <div class="modal-body p-5 space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">@lang('Cancellation Type')</label>
                        <select name="cancellation_type" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900">
                            @foreach($cancelRequestTypes as $key => $type)
                                <option value="{{ $key }}">{{ trans($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">@lang('Reason for Cancellation')</label>
                        <textarea name="reason" required rows="3" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-xs text-slate-900" placeholder="@lang('Please describe why you wish to cancel this service...')"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 p-4">
                    <button type="button" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg" data-bs-dismiss="modal">@lang('Keep Service')</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-xs">@lang('Confirm Cancellation Request')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        const activeContent = document.getElementById('tab-content-' + tabId);
        if (activeContent) activeContent.classList.remove('hidden');

        document.querySelectorAll('.service-tab-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
            btn.classList.add('bg-slate-50', 'text-slate-700', 'border', 'border-slate-200');
        });

        const activeBtn = document.getElementById('tab-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('bg-slate-50', 'text-slate-700', 'border', 'border-slate-200');
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
        }

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }
</script>
@endsection
