@extends($activeTemplate.'layouts.master_side_bar')

@section('content')
<div class="col-12 space-y-5">
    <!-- Header Card -->
    <div class="p-5 sm:p-6 bg-white border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">@lang('Service Groups')</span>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 font-display">@lang('My Services')</h1>
            <p class="text-xs text-slate-500 max-w-xl">@lang('Manage your hosting instances, Cloud VPS, mail, and server nodes.')</p>
        </div>
        <a href="{{ route('service.category') }}?all" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5 flex-shrink-0">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>@lang('Order New Service')</span>
        </a>
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('user.service.list') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ !$selectedServiceGroup ? 'bg-slate-900 text-white shadow-xs' : 'bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
            <i data-lucide="layers" class="w-3.5 h-3.5"></i>
            <span>@lang('All Services')</span>
            <span class="px-1.5 py-0.2 rounded-md text-[10px] font-mono {{ !$selectedServiceGroup ? 'bg-slate-800 text-slate-300' : 'bg-slate-100 text-slate-600' }}">{{ $services->total() }}</span>
        </a>
        @foreach($serviceGroups as $group)
            <a href="{{ route('user.service.list', ['service' => $group['key']]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $selectedServiceGroup === $group['key'] ? 'bg-slate-900 text-white shadow-xs' : 'bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                <i data-lucide="{{ $group['key'] === 'vps' ? 'server' : ($group['key'] === 'mail' ? 'mail' : 'hard-drive') }}" class="w-3.5 h-3.5"></i>
                <span>{{ __($group['label']) }}</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] font-mono {{ $selectedServiceGroup === $group['key'] ? 'bg-slate-800 text-slate-300' : 'bg-slate-100 text-slate-600' }}">{{ $group['count'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Service Cards List -->
    <div class="space-y-3">
        @forelse($services as $service)
            @php
                $server = $service->server;
                $serverGroup = @$server->group;
                $fallbackRoleKey = \App\Models\Server::roleForProduct($service->product);
                $allRoles = \App\Models\Server::serviceRoles();
                $role = $server ? $server->serviceRoleLabel() : ($allRoles[$fallbackRoleKey] ?? ucfirst($fallbackRoleKey));
            @endphp
            <div class="p-4 sm:p-5 bg-white border border-slate-200/80 hover:border-slate-300 rounded-2xl transition-all shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Main Info -->
                <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100/60 flex items-center justify-center text-indigo-600 flex-shrink-0">
                        <i data-lucide="{{ str_contains(strtolower($role), 'vps') ? 'server' : (str_contains(strtolower($role), 'mail') ? 'mail' : 'globe') }}" class="w-5 h-5"></i>
                    </div>
                    <div class="space-y-1 min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h4 class="text-sm font-bold text-slate-900 tracking-tight">
                                {{ __(@$service->product->name ?: @$service->product->serviceCategory->name) }}
                            </h4>
                            @if($service->status == 1)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 orb-pulse"></span>
                                    @lang('Active')
                                </span>
                            @elseif($service->status == 2)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200/60 text-[10px] font-bold">
                                    @lang('Pending')
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold">
                                    {{ @App\Models\Hosting::status()[$service->status] ?? 'Unknown' }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-mono flex-wrap">
                            <a href="{{ route('user.service.details', $service->id) }}" class="font-bold text-slate-700 hover:text-indigo-600 transition-colors">
                                {{ __(@$service->domain ?: 'Unassigned domain') }}
                            </a>
                            @if($server)
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-400">{{ $server->ip_address ?? $server->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Billing & Action Group -->
                <div class="flex items-center justify-between lg:justify-end gap-6 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                    <div class="text-right">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">@lang('Recurring')</span>
                        <div class="text-sm font-bold font-mono text-slate-900">{{ showAmount($service->recurring_amount) }}</div>
                        <span class="text-[11px] text-slate-500">{{ @billingCycle($service->billing_cycle, true)['showText'] }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('user.service.details', $service->id) }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5">
                            <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                            <span>@lang('Manage')</span>
                        </a>
                        @if($service->status == 1)
                            <a href="{{ route('user.login.hosting', $service->id) }}" target="_blank" rel="noopener" class="p-2 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-700 rounded-xl transition-all shadow-2xs" title="@lang('Control Panel')">
                                <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-500"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 bg-white border border-slate-200/80 rounded-2xl text-center space-y-3 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100/60 flex items-center justify-center text-indigo-600 mx-auto">
                    <i data-lucide="box" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-900">@lang('No services found')</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">@lang('Order a cloud hosting, VPS, mail, or server plan to manage it here.')</p>
                <a href="{{ route('service.category') }}?all" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>@lang('Browse Services')</span>
                </a>
            </div>
        @endforelse
    </div>

    @if($services->hasPages())
        <div class="mt-4">
            {{ paginateLinks($services) }}
        </div>
    @endif
</div>
@endsection
