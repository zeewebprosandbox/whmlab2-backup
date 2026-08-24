@extends($activeTemplate . 'layouts.master_side_bar')

@section('content')
<div class="col-12 space-y-6">

    <!-- KYC Notice if unverified -->
    @if ($user->kv == 0 || $user->kv == 2)
        @php $kyc = @getContent('kyc.content', true); @endphp
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between text-xs text-amber-800">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>
                <div>
                    <strong class="font-semibold text-slate-900">@lang('KYC Verification Required')</strong>
                    <p class="text-slate-600 mt-0.5">@lang('Please submit your verification documents to unlock full server capabilities.')</p>
                </div>
            </div>
            <a href="{{ route('user.kyc.form') }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-md transition-colors shadow-sm">
                @lang('Submit KYC')
            </a>
        </div>
    @endif

    <!-- Hero Header (Sleek Off-White Luxury Banner) -->
    <div class="p-6 lg:p-8 bg-white border border-slate-200/80 rounded-2xl relative overflow-hidden flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 shadow-sm">
        <div class="space-y-2 relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-indigo-600 orb-pulse"></span>
                @lang('Console Active')
            </div>
            <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight text-slate-900 font-display">
                @lang('Welcome back,') <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-cyan-600">{{ explode(' ', $user->fullname)[0] ?? $user->username }}</span> 👋
            </h1>
            <p class="text-sm text-slate-600 max-w-xl">
                @lang('All system nodes operational. You have') <span class="text-slate-900 font-semibold">{{ $widget['active_services'] ?? 0 }} @lang('active services')</span> @lang('and') <span class="text-slate-900 font-semibold">{{ $widget['active_domains'] ?? 0 }} @lang('registered domains')</span>.
            </p>
        </div>

        <!-- Quick Action Pills -->
        <div class="flex flex-wrap items-center gap-2 relative z-10">
            <a href="{{ route('user.invoice.list') }}" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-cyan-600"></i>
                <span>@lang('View Invoices')</span>
            </a>
            <a href="{{ route('ticket.open') }}" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                <i data-lucide="life-buoy" class="w-3.5 h-3.5 text-indigo-600"></i>
                <span>@lang('Open Ticket')</span>
            </a>
            <a href="{{ route('service.category') }}?all" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>@lang('Deploy Service')</span>
            </a>
        </div>
    </div>

    <!-- Server Health Overview Card (Full Width) -->
    <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="activity" class="w-5 h-5 text-indigo-600"></i>
                <h3 class="text-base font-bold text-slate-900 font-display">@lang('Server Health Overview')</h3>
            </div>
            <span class="text-xs text-slate-500 font-medium">@lang('Live Telemetry') • @lang('Node Cluster #1')</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            <!-- CPU Gauge -->
            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-semibold">@lang('CPU Load')</span>
                    <span class="text-indigo-600 font-bold font-mono">32%</span>
                </div>
                <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full" style="width: 32%"></div>
                </div>
                <div class="text-[11px] text-slate-500">8 Cores • 3.40 GHz AMD EPYC</div>
            </div>

            <!-- RAM Gauge -->
            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-semibold">@lang('RAM Usage')</span>
                    <span class="text-amber-600 font-bold font-mono">68%</span>
                </div>
                <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full" style="width: 68%"></div>
                </div>
                <div class="text-[11px] text-slate-500">21.7 GB / 32 GB DDR5</div>
            </div>

            <!-- NVMe Disk Gauge -->
            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-semibold">@lang('NVMe Disk')</span>
                    <span class="text-cyan-600 font-bold font-mono">41%</span>
                </div>
                <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-full" style="width: 41%"></div>
                </div>
                <div class="text-[11px] text-slate-500">205 GB / 500 GB Storage</div>
            </div>
        </div>
    </div>

    <!-- Metrics Strip (Compact Luxury Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Active Services -->
        <a href="{{ route('user.service.list') }}" class="p-4 bg-white hover:bg-slate-50 border border-slate-200/80 hover:border-indigo-300 rounded-xl transition-all group flex flex-col justify-between space-y-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-105 transition-transform">
                    <i data-lucide="server" class="w-4 h-4"></i>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">@lang('Live')</span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $widget['active_services'] ?? 0 }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Active Services')</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Active Domains -->
        <a href="{{ route('user.domain.list') }}" class="p-4 bg-white hover:bg-slate-50 border border-slate-200/80 hover:border-cyan-300 rounded-xl transition-all group flex flex-col justify-between space-y-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-lg bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600 group-hover:scale-105 transition-transform">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-100">@lang('DNS')</span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $widget['active_domains'] ?? 0 }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('My Domains')</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-cyan-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Pending Invoices -->
        <a href="{{ route('user.invoice.list') }}" class="p-4 bg-white hover:bg-slate-50 border border-slate-200/80 hover:border-amber-300 rounded-xl transition-all group flex flex-col justify-between space-y-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-105 transition-transform">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">@lang('Billing')</span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $widget['unpaid_invoices'] ?? 0 }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Unpaid Invoices')</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Support Tickets -->
        <a href="{{ route('ticket.index') }}" class="p-4 bg-white hover:bg-slate-50 border border-slate-200/80 hover:border-emerald-300 rounded-xl transition-all group flex flex-col justify-between space-y-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition-transform">
                    <i data-lucide="messages-square" class="w-4 h-4"></i>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">@lang('Support')</span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $widget['open_tickets'] ?? 0 }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Open Tickets')</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Section Grid: 2-column Services + Right Activity & Billing -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Services Quick Grid -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="layers" class="w-5 h-5 text-indigo-600"></i>
                        <h3 class="text-base font-bold text-slate-900 font-display">@lang('Active Services')</h3>
                    </div>
                    <a href="{{ route('user.service.list') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">@lang('View all services →')</a>
                </div>

                @if(isset($services) && count($services) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($services->take(4) as $service)
                            <div class="p-4 bg-slate-50 border border-slate-200/80 hover:border-slate-300 rounded-xl space-y-3 relative group transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                        <i data-lucide="cpu" class="w-4 h-4"></i>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 tracking-tight truncate">{{ $service->domain ?? 'Unassigned domain' }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $service->product->name ?? 'cPanel Hosting' }}</p>
                                </div>
                                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                                    <a href="{{ route('user.service.details', $service->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white text-xs font-semibold rounded-md transition-all">
                                        @lang('Manage')
                                    </a>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $service->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 border border-dashed border-slate-200 rounded-xl text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mx-auto">
                            <i data-lucide="server-off" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-900">@lang('No Active Hosting Services Yet')</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">@lang('Deploy high performance NVMe cPanel or VPS hosting instances in seconds.')</p>
                        <a href="{{ route('service.category') }}?all" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>@lang('Browse Hosting Plans')</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Col: Support PIN & Credit Balance -->
        <div class="space-y-6">
            <!-- Support PIN Card -->
            <div class="p-5 bg-white border border-slate-200/80 rounded-2xl space-y-3 shadow-sm">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-bold">@lang('Support PIN')</span>
                    <span class="text-slate-400">@lang('Expires soon')</span>
                </div>
                <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <span class="text-xl font-bold font-mono text-indigo-600 tracking-wider">{{ $supportPin->plain_code ?? '849-201' }}</span>
                    <form action="{{ route('user.support.pin.regenerate') }}" method="post">
                        @csrf
                        <button type="submit" class="p-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 rounded-md transition-colors shadow-xs" title="@lang('Regenerate PIN')">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
                <p class="text-[11px] text-slate-500">@lang('Share this PIN with customer support representatives for account verification.')</p>
            </div>

            <!-- Account Balance Snapshot -->
            <div class="p-5 bg-white border border-slate-200/80 rounded-2xl space-y-3 shadow-sm">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-bold">@lang('Credit Balance')</span>
                    <a href="{{ route('user.deposit.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">+ @lang('Add Funds')</a>
                </div>
                <div class="text-2xl font-extrabold text-slate-900 font-mono">{{ showAmount($user->balance) }}</div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-cyan-500 h-full rounded-full" style="width: 75%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500">
                    <span>@lang('Auto-Renew'): <strong class="text-emerald-600">ON</strong></span>
                    <span>@lang('Next cycle in 14d')</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
