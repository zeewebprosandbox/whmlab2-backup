@extends($activeTemplate . 'layouts.master_side_bar')

@section('content')
<div class="col-12 space-y-4">

    <!-- KYC Notice if unverified -->
    @if ($user->kv == 0 || $user->kv == 2)
        @php $kyc = @getContent('kyc.content', true); @endphp
        <div class="p-3.5 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-center justify-between text-xs text-amber-900 shadow-xs">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-amber-500/15 flex items-center justify-center flex-shrink-0 text-amber-600">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <strong class="font-semibold text-slate-900">@lang('KYC Verification Required')</strong>
                    <p class="text-slate-600 text-[11px] mt-0.5">@lang('Please submit your verification documents to unlock full automated server features.')</p>
                </div>
            </div>
            <a href="{{ route('user.kyc.form') }}" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg text-xs transition-colors shadow-xs">
                @lang('Submit KYC')
            </a>
        </div>
    @endif

    <!-- Hero Header Banner (Compact, Sleek & Subtle) -->
    <div class="p-4 sm:p-5 bg-white border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200/80 text-slate-700 text-[10px] font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 orb-pulse"></span>
                @lang('Client Portal Active')
            </div>
            <h1 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 font-display">
                @lang('Welcome back,') <span class="text-slate-900">{{ explode(' ', $user->fullname)[0] ?? $user->username }}</span> 👋
            </h1>
            <p class="text-xs text-slate-500 max-w-xl">
                @lang('Manage your cloud services, domains, databases, and billing from your centralized dashboard.')
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
            <a href="{{ route('user.invoice.list') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>@lang('Invoices')</span>
            </a>
            <a href="{{ route('ticket.index') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="life-buoy" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>@lang('Support')</span>
            </a>
            <a href="{{ route('service.category') }}?all" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition-all flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>@lang('Deploy Service')</span>
            </a>
        </div>
    </div>

    <!-- Clean 4-Metric Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-3.5">
        <!-- Active Services -->
        <a href="{{ route('user.service.list') }}" class="p-3.5 sm:p-4 bg-white hover:bg-slate-50/80 border border-slate-200/70 hover:border-slate-300 rounded-xl transition-all group flex flex-col justify-between space-y-2 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-100/60 flex items-center justify-center text-indigo-600">
                    <i data-lucide="server" class="w-3.5 h-3.5"></i>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">@lang('Live')</span>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-900 font-mono tracking-tight">{{ $widget['active_services'] ?? 0 }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Active Services')</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Active Domains -->
        <a href="{{ route('user.domain.list') }}" class="p-3.5 sm:p-4 bg-white hover:bg-slate-50/80 border border-slate-200/70 hover:border-slate-300 rounded-xl transition-all group flex flex-col justify-between space-y-2 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="w-7 h-7 rounded-lg bg-cyan-50 border border-cyan-100/60 flex items-center justify-center text-cyan-600">
                    <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200/60">@lang('DNS')</span>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-900 font-mono tracking-tight">{{ $widget['active_domains'] ?? 0 }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('My Domains')</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400 group-hover:text-cyan-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Unpaid Invoices -->
        <a href="{{ route('user.invoice.list') }}" class="p-3.5 sm:p-4 bg-white hover:bg-slate-50/80 border border-slate-200/70 hover:border-slate-300 rounded-xl transition-all group flex flex-col justify-between space-y-2 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="w-7 h-7 rounded-lg bg-amber-50 border border-amber-100/60 flex items-center justify-center text-amber-600">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ ($widget['unpaid_invoices'] ?? 0) > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' }}">
                    {{ ($widget['unpaid_invoices'] ?? 0) > 0 ? __('Due') : __('Clear') }}
                </span>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-900 font-mono tracking-tight">{{ $widget['unpaid_invoices'] ?? 0 }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Unpaid Invoices')</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Open Support Tickets -->
        <a href="{{ route('ticket.index') }}" class="p-3.5 sm:p-4 bg-white hover:bg-slate-50/80 border border-slate-200/70 hover:border-slate-300 rounded-xl transition-all group flex flex-col justify-between space-y-2 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-100/60 flex items-center justify-center text-emerald-600">
                    <i data-lucide="messages-square" class="w-3.5 h-3.5"></i>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-700 border border-slate-200/60">@lang('24/7')</span>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-900 font-mono tracking-tight">{{ $widget['open_tickets'] ?? 0 }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Open Tickets')</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Layout (2-Column) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

        <!-- Left 2 Cols: My Active Services List -->
        <div class="lg:col-span-2 space-y-4">
            <div class="p-4 sm:p-5 bg-white border border-slate-200/80 rounded-2xl space-y-3.5 shadow-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-100/60 flex items-center justify-center text-indigo-600">
                            <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 font-display">@lang('Active Services & Instances')</h3>
                            <p class="text-[11px] text-slate-500">@lang('Quick access to manage your hosting nodes and websites.')</p>
                        </div>
                    </div>
                    <a href="{{ route('user.service.list') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 transition-colors">
                        <span>@lang('View all')</span>
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </a>
                </div>

                @if(isset($recentServices) && count($recentServices) > 0)
                    <div class="space-y-2">
                        @foreach($recentServices as $service)
                            <div class="p-3 bg-slate-50/70 hover:bg-slate-50 border border-slate-200/80 hover:border-slate-300 rounded-xl transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-white border border-slate-200/80 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                        <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('user.service.details', $service->id) }}" class="text-xs font-bold text-slate-900 hover:text-indigo-600 transition-colors truncate max-w-[200px] sm:max-w-[280px]">
                                                {{ $service->domain ?? 'Unassigned domain' }}
                                            </a>
                                            @if($service->status == 1)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[9px] font-bold uppercase">
                                                    <span class="w-1 h-1 rounded-full bg-emerald-500 orb-pulse"></span>
                                                    @lang('Active')
                                                </span>
                                            @elseif($service->status == 2)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200/60 text-[9px] font-bold uppercase">
                                                    @lang('Pending')
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono mt-0.5">
                                            <span>{{ $service->product->name ?? 'Cloud Hosting' }}</span>
                                            <span class="text-slate-300">•</span>
                                            <span class="text-slate-400">{{ $service->server->ip_address ?? '169.58.176.53' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <a href="{{ route('user.service.details', $service->id) }}" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200/80 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1 shadow-2xs">
                                        <i data-lucide="settings" class="w-3 h-3 text-slate-500"></i>
                                        <span>@lang('Manage')</span>
                                    </a>
                                    @if($service->status == 1)
                                        <a href="{{ route('user.login.hosting', $service->id) }}" target="_blank" rel="noopener" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-1 shadow-2xs">
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                            <span>@lang('Control Panel')</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 border border-dashed border-slate-200 rounded-xl text-center space-y-2">
                        <i data-lucide="server" class="w-5 h-5 text-slate-400 mx-auto"></i>
                        <p class="text-xs text-slate-600 font-semibold">@lang('No active services yet.')</p>
                        <a href="{{ route('service.category') }}?all" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg">
                            <i data-lucide="plus" class="w-3 h-3"></i>
                            <span>@lang('Deploy First Service')</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Col: Support PIN & Balance Snapshot -->
        <div class="space-y-3.5">
            <!-- Support PIN Card -->
            <div class="p-4 sm:p-4.5 bg-white border border-slate-200/80 rounded-2xl space-y-2 shadow-xs">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-indigo-600"></i>
                        <span class="text-slate-900 font-bold font-display text-xs">@lang('Verified Support PIN')</span>
                    </div>
                    <span class="text-emerald-600 text-[9px] font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60">@lang('Active')</span>
                </div>
                <div class="flex items-center justify-between bg-slate-50/80 px-3 py-1.5 rounded-xl border border-slate-200/80">
                    <span class="text-base font-bold font-mono text-indigo-600 tracking-wider" id="supportPinCode">{{ $supportPin->plain_code ?? '849-201' }}</span>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('supportPinCode').innerText); alert('@lang('Support PIN copied!')')" class="p-1 bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 rounded-md transition-colors shadow-2xs" title="@lang('Copy PIN')">
                            <i data-lucide="copy" class="w-3 h-3"></i>
                        </button>
                        <form action="{{ route('user.support.pin.regenerate') }}" method="post">
                            @csrf
                            <button type="submit" class="p-1 bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 rounded-md transition-colors shadow-2xs" title="@lang('Regenerate PIN')">
                                <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 leading-normal">
                    @lang('Provide this secret PIN when communicating with technical support specialists.')
                </p>
            </div>

            <!-- Account Balance Snapshot -->
            <div class="p-4 sm:p-4.5 bg-white border border-slate-200/80 rounded-2xl space-y-2 shadow-xs">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-900 font-bold font-display">@lang('Credit Balance')</span>
                    <a href="{{ route('user.deposit.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1">
                        <i data-lucide="plus-circle" class="w-3 h-3"></i>
                        <span>@lang('Add Funds')</span>
                    </a>
                </div>
                <div class="text-xl font-bold text-slate-900 font-mono">{{ showAmount($user->balance) }}</div>
                <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center gap-1.5 text-[11px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @lang('Auto-Renew Active')
                    </span>
                    <a href="{{ route('user.transactions') }}" class="text-slate-500 hover:text-slate-900 underline font-medium text-[11px]">@lang('Statements')</a>
                </div>
            </div>

            <!-- Unpaid Invoices Snapshot -->
            @if(isset($recentInvoices) && count($recentInvoices) > 0)
                <div class="p-3.5 bg-amber-50/70 border border-amber-200/80 rounded-2xl space-y-1.5 shadow-xs">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-amber-900 font-display">@lang('Pending Invoices')</span>
                        <span class="text-[9px] font-bold text-rose-600 bg-rose-50 px-1 py-0.5 rounded border border-rose-200/60">{{ count($recentInvoices) }} @lang('due')</span>
                    </div>
                    <div class="space-y-1">
                        @foreach($recentInvoices as $invoice)
                            <div class="p-2 bg-white border border-amber-200/70 rounded-lg flex items-center justify-between text-xs">
                                <div>
                                    <div class="font-mono font-bold text-slate-900">#{{ $invoice->invoice_number }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ showAmount($invoice->amount) }}</div>
                                </div>
                                <a href="{{ route('user.invoice.view', $invoice->id) }}" class="px-2 py-0.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded text-[10px] transition-colors shadow-2xs">
                                    @lang('Pay Now')
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
