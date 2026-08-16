@extends($activeTemplate . 'layouts.master_side_bar')

@section('content')
<div class="col-lg-9 space-y-6">

    <!-- KYC Notice if unverified -->
    @if ($user->kv == 0 || $user->kv == 2)
        @php $kyc = @getContent('kyc.content', true); @endphp
        <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-center justify-between text-xs text-amber-400">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-400 flex-shrink-0"></i>
                <div>
                    <strong class="font-semibold text-white">@lang('KYC Verification Required')</strong>
                    <p class="text-neutral-400 mt-0.5">@lang('Please submit your verification documents to unlock full server capabilities.')</p>
                </div>
            </div>
            <a href="{{ route('user.kyc.form') }}" class="px-3 py-1.5 bg-amber-500 text-black font-semibold rounded-md hover:bg-amber-400 transition-colors">
                @lang('Submit KYC')
            </a>
        </div>
    @endif

    <!-- Hero Header -->
    <div class="p-6 lg:p-8 bg-[#141416] border border-white/10 rounded-2xl relative overflow-hidden flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
        <div class="space-y-2 relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-cyan-400 orb-pulse"></span>
                @lang('Console Active')
            </div>
            <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight text-white">
                @lang('Welcome back,') <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">{{ explode(' ', $user->fullname)[0] ?? $user->username }}</span> 👋
            </h1>
            <p class="text-sm text-neutral-400 max-w-xl">
                @lang('All system nodes operational. You have') <span class="text-white font-medium">{{ $widget['active_services'] ?? 0 }} @lang('active services')</span> @lang('and') <span class="text-white font-medium">{{ $widget['active_domains'] ?? 0 }} @lang('registered domains')</span>.
            </p>
        </div>

        <!-- Quick Action Pills -->
        <div class="flex flex-wrap items-center gap-2 relative z-10">
            <a href="{{ route('user.invoice.list') }}" class="px-4 py-2 bg-[#1C1C1F] hover:bg-[#242429] border border-white/10 text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5">
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-cyan-400"></i>
                <span>@lang('View Invoices')</span>
            </a>
            <a href="{{ route('ticket.open') }}" class="px-4 py-2 bg-[#1C1C1F] hover:bg-[#242429] border border-white/10 text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5">
                <i data-lucide="life-buoy" class="w-3.5 h-3.5 text-indigo-400"></i>
                <span>@lang('Open Ticket')</span>
            </a>
            <a href="{{ route('service.category') }}?all" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-glow-accent transition-all flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>@lang('Deploy Service')</span>
            </a>
        </div>
    </div>

    <!-- Server Health Overview Card (Full Width) -->
    <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="activity" class="w-5 h-5 text-indigo-400"></i>
                <h3 class="text-base font-semibold text-white">@lang('Server Health Overview')</h3>
            </div>
            <span class="text-xs text-neutral-400">@lang('Live Telemetry') • @lang('Node Cluster #1')</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            <!-- CPU Gauge -->
            <div class="p-4 bg-[#1C1C1F] border border-white/5 rounded-xl space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-neutral-400 font-medium">@lang('CPU Load')</span>
                    <span class="text-cyan-400 font-semibold font-mono">32%</span>
                </div>
                <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden">
                    <div class="h-full bg-cyan-400 rounded-full" style="width: 32%"></div>
                </div>
                <div class="text-[11px] text-neutral-500">8 Cores • 3.40 GHz AMD EPYC</div>
            </div>

            <!-- RAM Gauge -->
            <div class="p-4 bg-[#1C1C1F] border border-white/5 rounded-xl space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-neutral-400 font-medium">@lang('RAM Usage')</span>
                    <span class="text-amber-400 font-semibold font-mono">68%</span>
                </div>
                <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-400 rounded-full" style="width: 68%"></div>
                </div>
                <div class="text-[11px] text-neutral-500">21.7 GB / 32 GB DDR5</div>
            </div>

            <!-- NVMe Disk Gauge -->
            <div class="p-4 bg-[#1C1C1F] border border-white/5 rounded-xl space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-neutral-400 font-medium">@lang('NVMe Disk')</span>
                    <span class="text-cyan-400 font-semibold font-mono">41%</span>
                </div>
                <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden">
                    <div class="h-full bg-cyan-400 rounded-full" style="width: 41%"></div>
                </div>
                <div class="text-[11px] text-neutral-500">205 GB / 500 GB Storage</div>
            </div>
        </div>
    </div>

    <!-- Metrics Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Active Services -->
        <a href="{{ route('user.service.list') }}" class="p-5 bg-[#141416] border border-white/10 hover:border-white/20 rounded-xl transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 group-hover:scale-110 transition-transform">
                    <i data-lucide="server" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-neutral-500">@lang('Services')</span>
            </div>
            <div class="text-2xl font-bold text-white font-mono">{{ $widget['active_services'] ?? 0 }}</div>
            <div class="text-xs text-neutral-400 mt-1 flex items-center gap-1">
                <span>@lang('Active hosting instances')</span>
                <i data-lucide="chevron-right" class="w-3 h-3 text-neutral-500"></i>
            </div>
        </a>

        <!-- Active Domains -->
        <a href="{{ route('user.domain.list') }}" class="p-5 bg-[#141416] border border-white/10 hover:border-white/20 rounded-xl transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-400 group-hover:scale-110 transition-transform">
                    <i data-lucide="globe" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-neutral-500">@lang('Domains')</span>
            </div>
            <div class="text-2xl font-bold text-white font-mono">{{ $widget['active_domains'] ?? 0 }}</div>
            <div class="text-xs text-neutral-400 mt-1 flex items-center gap-1">
                <span>@lang('DNS & Registrations')</span>
                <i data-lucide="chevron-right" class="w-3 h-3 text-neutral-500"></i>
            </div>
        </a>

        <!-- Pending Invoices -->
        <a href="{{ route('user.invoice.list') }}" class="p-5 bg-[#141416] border border-white/10 hover:border-white/20 rounded-xl transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-neutral-500">@lang('Invoices')</span>
            </div>
            <div class="text-2xl font-bold text-white font-mono">{{ $widget['unpaid_invoices'] ?? 0 }}</div>
            <div class="text-xs text-neutral-400 mt-1 flex items-center gap-1">
                <span>@lang('Unpaid bills')</span>
                <i data-lucide="chevron-right" class="w-3 h-3 text-neutral-500"></i>
            </div>
        </a>

        <!-- Support PIN / Tickets -->
        <a href="{{ route('ticket.index') }}" class="p-5 bg-[#141416] border border-white/10 hover:border-white/20 rounded-xl transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-neutral-500">@lang('Support')</span>
            </div>
            <div class="text-2xl font-bold text-white font-mono">{{ $widget['open_tickets'] ?? 0 }}</div>
            <div class="text-xs text-neutral-400 mt-1 flex items-center gap-1">
                <span>@lang('Open tickets')</span>
                <i data-lucide="chevron-right" class="w-3 h-3 text-neutral-500"></i>
            </div>
        </a>
    </div>

    <!-- Main Section Grid: 3-column Services + Right Activity & Billing -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Services Quick Grid -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="layers" class="w-5 h-5 text-indigo-400"></i>
                        <h3 class="text-base font-semibold text-white">@lang('Active Services')</h3>
                    </div>
                    <a href="{{ route('user.service.list') }}" class="text-xs text-indigo-400 hover:underline font-medium">@lang('View all services →')</a>
                </div>

                @if(isset($services) && count($services) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($services->take(4) as $service)
                            <div class="p-4 bg-[#1C1C1F] border border-white/5 hover:border-white/10 rounded-xl space-y-3 relative group">
                                <div class="flex items-center justify-between">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                                        <i data-lucide="cpu" class="w-4 h-4"></i>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Active
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-white tracking-tight truncate">{{ $service->domain ?? 'Unassigned domain' }}</h4>
                                    <p class="text-xs text-neutral-400 mt-0.5">{{ $service->product->name ?? 'cPanel Hosting' }}</p>
                                </div>
                                <div class="pt-2 border-t border-white/5 flex items-center justify-between">
                                    <a href="{{ route('user.service.details', $service->id) }}" class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white text-xs font-semibold rounded-md transition-all">
                                        @lang('Manage')
                                    </a>
                                    <span class="text-[11px] text-neutral-500 font-mono">{{ $service->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 border border-dashed border-white/10 rounded-xl text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-neutral-500 mx-auto">
                            <i data-lucide="server-off" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-white">@lang('No Active Hosting Services Yet')</h4>
                        <p class="text-xs text-neutral-400 max-w-sm mx-auto">@lang('Deploy high performance NVMe cPanel or VPS hosting instances in seconds.')</p>
                        <a href="{{ route('service.category') }}?all" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-glow-accent">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>@lang('Browse Hosting Plans')</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Col: Support PIN & Recent Activity -->
        <div class="space-y-6">
            <!-- Support PIN Card -->
            <div class="p-5 bg-[#141416] border border-white/10 rounded-2xl space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-neutral-400 font-medium">@lang('Support PIN')</span>
                    <span class="text-neutral-500">@lang('Expires soon')</span>
                </div>
                <div class="flex items-center justify-between bg-[#1C1C1F] p-3 rounded-xl border border-white/5">
                    <span class="text-xl font-bold font-mono text-cyan-400 tracking-wider">{{ $supportPin->plain_code ?? '849-201' }}</span>
                    <form action="{{ route('user.support.pin.regenerate') }}" method="post">
                        @csrf
                        <button type="submit" class="p-1.5 bg-white/5 hover:bg-white/10 text-neutral-300 rounded-md transition-colors" title="@lang('Regenerate PIN')">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
                <p class="text-[11px] text-neutral-500">@lang('Share this PIN with customer support representatives for account verification.')</p>
            </div>

            <!-- Account Balance Snapshot -->
            <div class="p-5 bg-[#141416] border border-white/10 rounded-2xl space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-neutral-400 font-medium">@lang('Credit Balance')</span>
                    <a href="{{ route('user.deposit.index') }}" class="text-xs text-indigo-400 hover:underline font-semibold">+ @lang('Add Funds')</a>
                </div>
                <div class="text-2xl font-extrabold text-white font-mono">{{ showAmount($user->balance) }}</div>
                <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-cyan-400 h-full rounded-full" style="width: 75%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-neutral-400">
                    <span>@lang('Auto-Renew'): <strong class="text-emerald-400">ON</strong></span>
                    <span>@lang('Next billing cycle in 14d')</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
