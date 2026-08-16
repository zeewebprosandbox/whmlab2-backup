@extends($activeTemplate.'layouts.master')

@section('content')
<div class="py-8 bg-[#0A0A0B] text-[#F5F5F7] min-h-screen font-sans space-y-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumb Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-neutral-400 font-medium">
                <a href="{{ route('user.home') }}" class="hover:text-white transition-colors">@lang('Dashboard')</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-neutral-600"></i>
                <a href="{{ route('user.service.list') }}" class="hover:text-white transition-colors">@lang('Services')</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-neutral-600"></i>
                <span class="text-white font-semibold">{{ $service->domain ?? $product->name }}</span>
            </div>
            
            <a href="{{ route('user.service.list') }}" class="px-3 py-1.5 bg-[#1C1C1F] hover:bg-[#242429] border border-white/10 text-xs font-semibold rounded-lg transition-all text-neutral-300 flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>@lang('Back to Services')</span>
            </a>
        </div>

        <!-- Hero Header Card (cPanel Replacement Banner) -->
        <div class="p-6 lg:p-8 bg-[#141416] border border-white/10 rounded-2xl relative overflow-hidden space-y-6">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 orb-pulse"></span>
                            {{ strtoupper($service->showStatusText ?? 'Active') }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/5 border border-white/10 text-neutral-300 text-xs font-mono">
                            IP: {{ $service->server->ip_address ?? '192.168.1.100' }}
                        </span>
                        <span class="text-xs text-neutral-400 flex items-center gap-1">
                            🇺🇸 US-East Datacenter (N. Virginia)
                        </span>
                    </div>

                    <!-- Domain Name Copy to Clipboard -->
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl lg:text-4xl font-extrabold text-white tracking-tight">
                            {{ $service->domain ?? 'example.com' }}
                        </h1>
                        <button onclick="navigator.clipboard.writeText('{{ $service->domain }}'); alert('Domain copied!')" 
                            class="p-2 bg-white/5 hover:bg-white/10 rounded-lg text-neutral-400 hover:text-white transition-colors" title="Copy domain">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p class="text-xs text-neutral-400">{{ $product->name }} • {{ $product->serviceCategory->name ?? 'NVMe Web Hosting' }}</p>
                </div>

                <!-- Primary CTA Bar -->
                <div class="flex flex-wrap items-center gap-3">
                    @if(isset($hasAccount) && $hasAccount)
                        <a href="{{ route('user.login.hosting', $service->id) }}" target="_blank" rel="noopener"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg shadow-glow-accent transition-all flex items-center gap-2">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            <span>@lang('Open cPanel Direct')</span>
                        </a>
                    @endif
                    <a href="https://webmail.{{ $service->domain ?? 'example.com' }}" target="_blank" rel="noopener"
                        class="px-4 py-2.5 bg-[#1C1C1F] hover:bg-[#242429] border border-white/10 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2">
                        <i data-lucide="mail" class="w-4 h-4 text-cyan-400"></i>
                        <span>@lang('Webmail')</span>
                    </a>
                    <button onclick="alert('Launching Web File Manager...')"
                        class="px-4 py-2.5 bg-[#1C1C1F] hover:bg-[#242429] border border-white/10 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2">
                        <i data-lucide="folder" class="w-4 h-4 text-amber-400"></i>
                        <span>@lang('File Manager')</span>
                    </button>
                </div>
            </div>

            <!-- Tabbed Navigation Bar -->
            <div class="border-t border-white/5 pt-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none" id="serviceTabs">
                    <button onclick="switchTab('overview')" id="tab-overview" class="service-tab-btn px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>@lang('Overview')</span>
                    </button>
                    <button onclick="switchTab('files')" id="tab-files" class="service-tab-btn px-4 py-2 bg-[#1C1C1F] hover:bg-[#242429] text-neutral-300 hover:text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap">
                        <i data-lucide="database" class="w-4 h-4"></i>
                        <span>@lang('Files & Databases')</span>
                    </button>
                    <button onclick="switchTab('email')" id="tab-email" class="service-tab-btn px-4 py-2 bg-[#1C1C1F] hover:bg-[#242429] text-neutral-300 hover:text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                        <span>@lang('Email')</span>
                    </button>
                    <button onclick="switchTab('dns')" id="tab-dns" class="service-tab-btn px-4 py-2 bg-[#1C1C1F] hover:bg-[#242429] text-neutral-300 hover:text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap">
                        <i data-lucide="network" class="w-4 h-4"></i>
                        <span>@lang('Domains & DNS')</span>
                    </button>
                    <button onclick="switchTab('security')" id="tab-security" class="service-tab-btn px-4 py-2 bg-[#1C1C1F] hover:bg-[#242429] text-neutral-300 hover:text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>@lang('Security')</span>
                    </button>
                    <button onclick="switchTab('advanced')" id="tab-advanced" class="service-tab-btn px-4 py-2 bg-[#1C1C1F] hover:bg-[#242429] text-neutral-300 hover:text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-2 whitespace-nowrap">
                        <i data-lucide="terminal" class="w-4 h-4"></i>
                        <span>@lang('Advanced')</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT SURFACES -->

        <!-- 1. OVERVIEW TAB -->
        <div id="tab-content-overview" class="tab-pane space-y-6">
            <!-- Resource Usage Circular Progress Gauges Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- CPU -->
                <div class="p-5 bg-[#141416] border border-white/10 rounded-2xl text-center space-y-3">
                    <div class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">@lang('CPU Usage')</div>
                    <div class="relative w-24 h-24 mx-auto flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-white/5 stroke-current" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-cyan-400 stroke-current" stroke-width="3.5" stroke-dasharray="28, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-lg font-bold font-mono text-white">28%</span>
                    </div>
                    <div class="text-[11px] text-neutral-500 font-mono">1 Core / 2.8 GHz</div>
                </div>

                <!-- RAM -->
                <div class="p-5 bg-[#141416] border border-white/10 rounded-2xl text-center space-y-3">
                    <div class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">@lang('RAM Usage')</div>
                    <div class="relative w-24 h-24 mx-auto flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-white/5 stroke-current" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-amber-400 stroke-current" stroke-width="3.5" stroke-dasharray="64, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-lg font-bold font-mono text-white">64%</span>
                    </div>
                    <div class="text-[11px] text-neutral-500 font-mono">1.28 GB / 2.0 GB</div>
                </div>

                <!-- Disk -->
                <div class="p-5 bg-[#141416] border border-white/10 rounded-2xl text-center space-y-3">
                    <div class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">@lang('Disk Space')</div>
                    <div class="relative w-24 h-24 mx-auto flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-white/5 stroke-current" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-cyan-400 stroke-current" stroke-width="3.5" stroke-dasharray="35, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-lg font-bold font-mono text-white">35%</span>
                    </div>
                    <div class="text-[11px] text-neutral-500 font-mono">7.0 GB / 20.0 GB</div>
                </div>

                <!-- Bandwidth -->
                <div class="p-5 bg-[#141416] border border-white/10 rounded-2xl text-center space-y-3">
                    <div class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">@lang('Bandwidth')</div>
                    <div class="relative w-24 h-24 mx-auto flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-white/5 stroke-current" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-indigo-400 stroke-current" stroke-width="3.5" stroke-dasharray="18, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-lg font-bold font-mono text-white">18%</span>
                    </div>
                    <div class="text-[11px] text-neutral-500 font-mono">180 GB / 1000 GB</div>
                </div>
            </div>

            <!-- Quick Links Grid -->
            <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
                <h3 class="text-base font-semibold text-white">@lang('Quick Management Utilities')</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    <button onclick="alert('Launching phpMyAdmin...')" class="p-4 bg-[#1C1C1F] hover:bg-[#242429] border border-white/5 hover:border-white/10 rounded-xl text-left transition-all group space-y-2">
                        <i data-lucide="database" class="w-5 h-5 text-indigo-400 group-hover:scale-110 transition-transform"></i>
                        <div>
                            <div class="text-xs font-semibold text-white">phpMyAdmin</div>
                            <div class="text-[11px] text-neutral-400">Database admin</div>
                        </div>
                    </button>
                    <button onclick="switchTab('email')" class="p-4 bg-[#1C1C1F] hover:bg-[#242429] border border-white/5 hover:border-white/10 rounded-xl text-left transition-all group space-y-2">
                        <i data-lucide="mail" class="w-5 h-5 text-cyan-400 group-hover:scale-110 transition-transform"></i>
                        <div>
                            <div class="text-xs font-semibold text-white">Email Accounts</div>
                            <div class="text-[11px] text-neutral-400">Manage mailboxes</div>
                        </div>
                    </button>
                    <button onclick="switchTab('security')" class="p-4 bg-[#1C1C1F] hover:bg-[#242429] border border-white/5 hover:border-white/10 rounded-xl text-left transition-all group space-y-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400 group-hover:scale-110 transition-transform"></i>
                        <div>
                            <div class="text-xs font-semibold text-white">SSL Status</div>
                            <div class="text-[11px] text-neutral-400">AutoSSL wizard</div>
                        </div>
                    </button>
                    <button onclick="alert('Opening Backup Manager...')" class="p-4 bg-[#1C1C1F] hover:bg-[#242429] border border-white/5 hover:border-white/10 rounded-xl text-left transition-all group space-y-2">
                        <i data-lucide="archive" class="w-5 h-5 text-amber-400 group-hover:scale-110 transition-transform"></i>
                        <div>
                            <div class="text-xs font-semibold text-white">Backup Wizard</div>
                            <div class="text-[11px] text-neutral-400">Download snapshot</div>
                        </div>
                    </button>
                    <button onclick="switchTab('dns')" class="p-4 bg-[#1C1C1F] hover:bg-[#242429] border border-white/5 hover:border-white/10 rounded-xl text-left transition-all group space-y-2">
                        <i data-lucide="globe" class="w-5 h-5 text-rose-400 group-hover:scale-110 transition-transform"></i>
                        <div>
                            <div class="text-xs font-semibold text-white">Subdomains</div>
                            <div class="text-[11px] text-neutral-400">Manage domain aliases</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. FILES & DATABASES TAB -->
        <div id="tab-content-files" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-white">@lang('Databases (MySQL / MariaDB)')</h3>
                    <button onclick="alert('Opening Create Database Modal')" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-500">
                        + @lang('Create Database')
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-neutral-300">
                        <thead class="bg-[#1C1C1F] text-neutral-400 uppercase font-semibold text-[11px]">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">Database Name</th>
                                <th class="px-4 py-3">Size</th>
                                <th class="px-4 py-3">User Count</th>
                                <th class="px-4 py-3 rounded-r-lg text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr>
                                <td class="px-4 py-3 font-mono text-white font-medium">db_wp_app_prod</td>
                                <td class="px-4 py-3">42.8 MB</td>
                                <td class="px-4 py-3">1 User (app_user)</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button class="px-2.5 py-1 bg-white/5 hover:bg-white/10 rounded text-neutral-300">phpMyAdmin</button>
                                    <button class="px-2.5 py-1 bg-white/5 hover:bg-white/10 rounded text-neutral-300">Export</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono text-white font-medium">db_staging_test</td>
                                <td class="px-4 py-3">12.1 MB</td>
                                <td class="px-4 py-3">1 User (stage_user)</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button class="px-2.5 py-1 bg-white/5 hover:bg-white/10 rounded text-neutral-300">phpMyAdmin</button>
                                    <button class="px-2.5 py-1 bg-white/5 hover:bg-white/10 rounded text-neutral-300">Export</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. EMAIL TAB -->
        <div id="tab-content-email" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-white">@lang('Mailbox Accounts')</h3>
                    <button onclick="document.getElementById('createMailModal').classList.remove('hidden')" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-500">
                        + @lang('Create Account')
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-neutral-300">
                        <thead class="bg-[#1C1C1F] text-neutral-400 uppercase font-semibold text-[11px]">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">Mailbox</th>
                                <th class="px-4 py-3">Quota Used</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 rounded-r-lg text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr>
                                <td class="px-4 py-3 font-medium text-white">admin@{{ $service->domain ?? 'example.com' }}</td>
                                <td class="px-4 py-3 font-mono">145 MB / 1000 MB (14.5%)</td>
                                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-semibold">Active</span></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="https://webmail.{{ $service->domain }}" target="_blank" class="px-2.5 py-1 bg-indigo-600/20 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded font-medium">Webmail</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-white">support@{{ $service->domain ?? 'example.com' }}</td>
                                <td class="px-4 py-3 font-mono">420 MB / 1000 MB (42.0%)</td>
                                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-semibold">Active</span></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="https://webmail.{{ $service->domain }}" target="_blank" class="px-2.5 py-1 bg-indigo-600/20 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded font-medium">Webmail</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. DOMAINS & DNS TAB -->
        <div id="tab-content-dns" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-white">@lang('DNS Zone Editor')</h3>
                        <p class="text-xs text-neutral-400">Manage domain DNS records and live cluster resolution.</p>
                    </div>
                    <form action="{{ route('user.service.dns.repair', $service->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-glow-accent transition-all flex items-center gap-1.5">
                            <i data-lucide="network" class="w-3.5 h-3.5"></i>
                            <span>@lang('Repair Live DNS')</span>
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-neutral-300">
                        <thead class="bg-[#1C1C1F] text-neutral-400 uppercase font-semibold text-[11px]">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">Type</th>
                                <th class="px-4 py-3">Host</th>
                                <th class="px-4 py-3">Value</th>
                                <th class="px-4 py-3">TTL</th>
                                <th class="px-4 py-3 rounded-r-lg text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-mono">
                            <tr>
                                <td class="px-4 py-3 font-semibold text-cyan-400">A</td>
                                <td class="px-4 py-3 text-white">@</td>
                                <td class="px-4 py-3">{{ $service->server->ip_address ?? '192.168.1.100' }}</td>
                                <td class="px-4 py-3">14400</td>
                                <td class="px-4 py-3 text-right"><button class="text-indigo-400 hover:underline">Edit</button></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-semibold text-indigo-400">CNAME</td>
                                <td class="px-4 py-3 text-white">www</td>
                                <td class="px-4 py-3">{{ $service->domain ?? 'example.com' }}</td>
                                <td class="px-4 py-3">14400</td>
                                <td class="px-4 py-3 text-right"><button class="text-indigo-400 hover:underline">Edit</button></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-semibold text-amber-400">MX</td>
                                <td class="px-4 py-3 text-white">@</td>
                                <td class="px-4 py-3">mail.{{ $service->domain ?? 'example.com' }} (Priority: 10)</td>
                                <td class="px-4 py-3">14400</td>
                                <td class="px-4 py-3 text-right"><button class="text-indigo-400 hover:underline">Edit</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. SECURITY TAB -->
        <div id="tab-content-security" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-white">@lang('SSL / TLS Certificate')</h3>
                        <p class="text-xs text-neutral-400">Instant SAN SSL certificate active with automatic 0-second HTTPS redirection.</p>
                    </div>
                    <form action="{{ route('user.service.ssl.issue', $service->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg flex items-center gap-1.5 shadow-glow-accent transition-all">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            <span>@lang('Run Auto-SSL & Force HTTPS')</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 6. ADVANCED TAB -->
        <div id="tab-content-advanced" class="tab-pane hidden space-y-6">
            <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
                <h3 class="text-base font-semibold text-white">@lang('PHP Version & INI Settings')</h3>
                <div class="flex items-center gap-4">
                    <label class="text-xs text-neutral-400">Select PHP Version:</label>
                    <select class="px-3 py-2 bg-[#1C1C1F] border border-white/10 rounded-lg text-xs font-mono text-white">
                        <option selected>PHP 8.2 (Recommended)</option>
                        <option>PHP 8.1</option>
                        <option>PHP 8.0</option>
                    </select>
                    <button onclick="alert('PHP version updated!')" class="px-3 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg">Save</button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Create Mail Account -->
<div id="createMailModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <form action="{{ route('user.service.email.create', $service->id) }}" method="POST" class="bg-[#141416] border border-white/10 rounded-2xl p-6 w-full max-w-md space-y-4 shadow-2xl">
        @csrf
        <div class="flex items-center justify-between border-b border-white/5 pb-2">
            <div>
                <h3 class="text-base font-bold text-white">Create Mailbox Account</h3>
                <p class="text-[11px] text-neutral-400">Select any domain or subdomain to provision instant webmail access.</p>
            </div>
            <button type="button" onclick="document.getElementById('createMailModal').classList.add('hidden')" class="text-neutral-400 hover:text-white text-lg">&times;</button>
        </div>
        <div class="space-y-4 text-xs">
            <div>
                <label class="block text-neutral-300 font-medium mb-1.5">Email Address</label>
                <div class="flex items-center gap-1.5">
                    <input type="text" name="v_account" required class="flex-1 bg-[#1C1C1F] border border-white/10 rounded-lg p-2.5 text-white placeholder:text-neutral-600 focus:outline-none focus:border-indigo-500" placeholder="username (e.g. info, support)">
                    <span class="font-bold text-neutral-400 text-sm px-1">@</span>
                    <select name="v_domain" required class="flex-1 bg-[#1C1C1F] border border-white/10 rounded-lg p-2.5 text-white font-mono focus:outline-none focus:border-indigo-500">
                        <option value="{{ $service->domain ?? 'example.com' }}">{{ $service->domain ?? 'example.com' }}</option>
                        <option value="sub.{{ $service->domain ?? 'example.com' }}">sub.{{ $service->domain ?? 'example.com' }}</option>
                        <option value="api.{{ $service->domain ?? 'example.com' }}">api.{{ $service->domain ?? 'example.com' }}</option>
                        <option value="app.{{ $service->domain ?? 'example.com' }}">app.{{ $service->domain ?? 'example.com' }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-neutral-300 font-medium mb-1.5">Storage Quota</label>
                    <select name="v_quota" class="w-full bg-[#1C1C1F] border border-white/10 rounded-lg p-2.5 text-white font-mono focus:outline-none focus:border-indigo-500">
                        <option value="500">500 MB</option>
                        <option value="1000" selected>1000 MB (1 GB)</option>
                        <option value="5000">5000 MB (5 GB)</option>
                        <option value="10000">10000 MB (10 GB)</option>
                        <option value="0">Unlimited</option>
                    </select>
                </div>

                <div>
                    <label class="block text-neutral-300 font-medium mb-1.5">Password</label>
                    <div class="flex gap-1.5">
                        <input type="text" name="v_password" id="genMailPass" required class="w-full bg-[#1C1C1F] border border-white/10 rounded-lg p-2.5 text-white font-mono text-xs focus:outline-none focus:border-indigo-500" value="x9$K#mP2!vL8">
                        <button type="button" onclick="document.getElementById('genMailPass').value = Math.random().toString(36).slice(-10) + '!A9';" class="px-2.5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-lg text-xs" title="Generate password">
                            <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-3 flex justify-end gap-2 border-t border-white/5">
            <button type="button" onclick="document.getElementById('createMailModal').classList.add('hidden')" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-neutral-300 text-xs font-semibold rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-glow-accent transition-all">
                Create Mailbox
            </button>
        </div>
    </form>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.service-tab-btn').forEach(el => {
        el.classList.remove('bg-indigo-600', 'text-white');
        el.classList.add('bg-[#1C1C1F]', 'text-neutral-300');
    });
    
    document.getElementById('tab-content-' + tabName).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tabName);
    activeBtn.classList.remove('bg-[#1C1C1F]', 'text-neutral-300');
    activeBtn.classList.add('bg-indigo-600', 'text-white');
}
</script>
@endsection
