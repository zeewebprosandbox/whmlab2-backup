@extends($activeTemplate . 'layouts.master_side_bar')

@section('content')
<div class="col-12 space-y-6">

    <!-- KYC Notice if unverified -->
    @if ($user->kv == 0 || $user->kv == 2)
        @php $kyc = @getContent('kyc.content', true); @endphp
        <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-center justify-between text-xs text-amber-900 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 flex items-center justify-center flex-shrink-0 text-amber-600">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
                <div>
                    <strong class="font-semibold text-slate-900">@lang('KYC Verification Required')</strong>
                    <p class="text-slate-600 mt-0.5">@lang('Please submit your verification documents to unlock full automated server features.')</p>
                </div>
            </div>
            <a href="{{ route('user.kyc.form') }}" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition-colors shadow-xs">
                @lang('Submit KYC')
            </a>
        </div>
    @endif

    <!-- Hero Header Banner (Compact, Subtle & Tasteful) -->
    <div class="p-6 sm:p-7 bg-white border border-slate-200/80 rounded-2xl relative overflow-hidden flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5 shadow-xs">
        <div class="space-y-1.5 relative z-10">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-[11px] font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 orb-pulse"></span>
                @lang('Client Portal Active')
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 font-display">
                @lang('Welcome back,') <span class="text-slate-900">{{ explode(' ', $user->fullname)[0] ?? $user->username }}</span> 👋
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-xl">
                @lang('Manage your cloud services, domains, databases, and billing from your centralized dashboard.')
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 relative z-10">
            <a href="{{ route('user.invoice.list') }}" class="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>@lang('Invoices')</span>
            </a>
            <a href="{{ route('ticket.index') }}" class="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 shadow-xs">
                <i data-lucide="life-buoy" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>@lang('Support')</span>
            </a>
            <a href="{{ route('service.category') }}?all" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition-all flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>@lang('Deploy Service')</span>
            </a>
        </div>
    </div>

    <!-- Clean 4-Metric Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <!-- Active Services -->
        <a href="{{ route('user.service.list') }}" class="p-4 sm:p-5 bg-white hover:bg-slate-50/80 border border-slate-200/80 hover:border-slate-300 rounded-xl transition-all group flex flex-col justify-between space-y-3 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-100/80 flex items-center justify-center text-indigo-600">
                    <i data-lucide="server" class="w-4 h-4"></i>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">@lang('Live')</span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 font-mono tracking-tight">{{ $widget['active_services'] ?? 0 }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Active Services')</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Active Domains -->
        <a href="{{ route('user.domain.list') }}" class="p-4 sm:p-5 bg-white hover:bg-slate-50/80 border border-slate-200/80 hover:border-slate-300 rounded-xl transition-all group flex flex-col justify-between space-y-3 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-lg bg-cyan-50 border border-cyan-100/80 flex items-center justify-center text-cyan-600">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200/60">@lang('DNS')</span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 font-mono tracking-tight">{{ $widget['active_domains'] ?? 0 }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('My Domains')</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400 group-hover:text-cyan-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Unpaid Invoices -->
        <a href="{{ route('user.invoice.list') }}" class="p-4 sm:p-5 bg-white hover:bg-slate-50/80 border border-slate-200/80 hover:border-slate-300 rounded-xl transition-all group flex flex-col justify-between space-y-3 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-lg bg-amber-50 border border-amber-100/80 flex items-center justify-center text-amber-600">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ ($widget['unpaid_invoices'] ?? 0) > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' }}">
                    {{ ($widget['unpaid_invoices'] ?? 0) > 0 ? __('Due') : __('Clear') }}
                </span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 font-mono tracking-tight">{{ $widget['unpaid_invoices'] ?? 0 }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Unpaid Invoices')</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>

        <!-- Open Support Tickets -->
        <a href="{{ route('ticket.index') }}" class="p-4 sm:p-5 bg-white hover:bg-slate-50/80 border border-slate-200/80 hover:border-slate-300 rounded-xl transition-all group flex flex-col justify-between space-y-3 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-100/80 flex items-center justify-center text-emerald-600">
                    <i data-lucide="messages-square" class="w-4 h-4"></i>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200/60">@lang('24/7')</span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900 font-mono tracking-tight">{{ $widget['open_tickets'] ?? 0 }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-between">
                    <span>@lang('Open Tickets')</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Layout (2-Column) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: My Active Services List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-100/80 flex items-center justify-center text-indigo-600">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @foreach($recentServices as $service)
                            <div class="p-4 bg-slate-50/60 border border-slate-200/70 hover:border-slate-300 rounded-xl space-y-3 group transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-indigo-600">
                                            <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-900 tracking-tight truncate max-w-[150px] sm:max-w-[180px]">
                                                {{ $service->domain ?? 'Unassigned domain' }}
                                            </h4>
                                            <span class="text-[10px] text-slate-500 font-mono">{{ $service->server->ip_address ?? 'Cloud Node' }}</span>
                                        </div>
                                    </div>
                                    @if($service->status == 1)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-[10px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 orb-pulse"></span>
                                            @lang('Active')
                                        </span>
                                    @elseif($service->status == 2)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200/80 text-amber-700 text-[10px] font-semibold">
                                            @lang('Pending')
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-600 text-[10px] font-semibold">
                                            {{ @App\Models\Hosting::status()[$service->status] ?? 'Unknown' }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-[11px] text-slate-600 flex items-center justify-between">
                                    <span class="font-medium text-slate-500">{{ $service->product->name ?? 'Cloud Hosting' }}</span>
                                    <span class="font-mono text-slate-400 text-[10px]">{{ $service->created_at->format('M d, Y') }}</span>
                                </div>

                                <div class="pt-2.5 border-t border-slate-200/60 flex items-center justify-between gap-2">
                                    <a href="{{ route('user.service.details', $service->id) }}" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white text-[11px] font-semibold rounded-lg transition-all flex items-center gap-1">
                                        <i data-lucide="settings" class="w-3 h-3"></i>
                                        <span>@lang('Manage')</span>
                                    </a>
                                    @if($service->status == 1)
                                        <a href="{{ route('user.login.hosting', $service->id) }}" target="_blank" rel="noopener" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-[11px] font-semibold rounded-lg transition-all flex items-center gap-1 shadow-xs">
                                            <i data-lucide="external-link" class="w-3 h-3 text-indigo-600"></i>
                                            <span>@lang('Control Panel')</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-7 border border-dashed border-slate-200 rounded-xl text-center space-y-2.5">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mx-auto">
                            <i data-lucide="server" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900">@lang('No Active Services Yet')</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">@lang('Deploy high-performance Cloud VPS, NVMe shared hosting, or server instances instantly.')</p>
                        <a href="{{ route('service.category') }}?all" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>@lang('Browse Hosting Plans')</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Col: Support PIN, Balance & Billing -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Support PIN Card -->
            <div class="p-5 sm:p-6 bg-white border border-slate-200/80 rounded-2xl space-y-3 shadow-xs">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-indigo-600"></i>
                        <span class="text-slate-900 font-bold font-display text-xs">@lang('Verified Support PIN')</span>
                    </div>
                    <span class="text-emerald-600 text-[10px] font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/60">@lang('Active')</span>
                </div>
                <div class="flex items-center justify-between bg-slate-50/80 px-3.5 py-2.5 rounded-xl border border-slate-200/80">
                    <span class="text-lg font-bold font-mono text-indigo-600 tracking-wider" id="supportPinCode">{{ $supportPin->plain_code ?? '849-201' }}</span>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('supportPinCode').innerText); alert('@lang('Support PIN copied!')')" class="p-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 rounded-md transition-colors shadow-xs" title="@lang('Copy PIN')">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('user.support.pin.regenerate') }}" method="post">
                            @csrf
                            <button type="submit" class="p-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 rounded-md transition-colors shadow-xs" title="@lang('Regenerate PIN')">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    @lang('Provide this secret PIN when communicating with technical support specialists.')
                </p>
            </div>

            <!-- Account Balance Snapshot -->
            <div class="p-5 sm:p-6 bg-white border border-slate-200/80 rounded-2xl space-y-3 shadow-xs">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-900 font-bold font-display">@lang('Credit Balance')</span>
                    <a href="{{ route('user.deposit.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1">
                        <i data-lucide="plus-circle" class="w-3 h-3"></i>
                        <span>@lang('Add Funds')</span>
                    </a>
                </div>
                <div class="text-2xl font-bold text-slate-900 font-mono">{{ showAmount($user->balance) }}</div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center gap-1.5 text-[11px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @lang('Auto-Renew Enabled')
                    </span>
                    <a href="{{ route('user.transactions') }}" class="text-slate-500 hover:text-slate-900 underline font-medium text-[11px]">@lang('Statements')</a>
                </div>
            </div>

            <!-- Unpaid Invoices Snapshot -->
            @if(isset($recentInvoices) && count($recentInvoices) > 0)
                <div class="p-4 sm:p-5 bg-amber-50/70 border border-amber-200/80 rounded-2xl space-y-2.5 shadow-xs">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-amber-900 font-display">@lang('Pending Invoices')</span>
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200/60">{{ count($recentInvoices) }} @lang('due')</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($recentInvoices as $invoice)
                            <div class="p-2.5 bg-white border border-amber-200/70 rounded-xl flex items-center justify-between text-xs">
                                <div>
                                    <div class="font-mono font-bold text-slate-900">#{{ $invoice->invoice_number }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ showAmount($invoice->amount) }}</div>
                                </div>
                                <a href="{{ route('user.invoice.view', $invoice->id) }}" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-md text-[10px] transition-colors shadow-xs">
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
