@extends($activeTemplate.'layouts.master')

@section('content')
<div class="py-8 bg-[#F8FAFC] text-slate-900 min-h-screen font-sans space-y-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Page Title & Toolbar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">@lang('Domain Portfolio')</h1>
                <p class="text-xs text-slate-500">@lang('Manage your registered domains, DNS records, and WHOIS privacy.')</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('register.domain') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>@lang('Register New Domain')</span>
                </a>
            </div>
        </div>

        <!-- Bulk Actions Toolbar & Filter Strip -->
        <div class="p-4 bg-white border border-slate-200/80 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-2">
                <button onclick="alert('Bulk renew selected domains')" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 rounded-lg flex items-center gap-1.5 transition-colors">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-cyan-600"></i>
                    <span>@lang('Bulk Renew')</span>
                </button>
                <button onclick="alert('Updating DNS for selected domains')" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 rounded-lg flex items-center gap-1.5 transition-colors">
                    <i data-lucide="network" class="w-3.5 h-3.5 text-indigo-600"></i>
                    <span>@lang('Update Nameservers')</span>
                </button>
            </div>

            <div class="text-xs text-slate-500">
                @lang('Showing') <strong class="text-slate-900 font-mono">{{ $domains->count() }}</strong> @lang('domains')
            </div>
        </div>

        <!-- Domains Table -->
        <div class="p-6 bg-white border border-slate-200/80 rounded-2xl overflow-hidden space-y-4 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg w-10">
                                <input type="checkbox" class="rounded bg-white border-slate-300 text-indigo-600 focus:ring-0">
                            </th>
                            <th class="px-4 py-3">@lang('Domain Name')</th>
                            <th class="px-4 py-3">@lang('Registration Date')</th>
                            <th class="px-4 py-3">@lang('Expiry / Next Due')</th>
                            <th class="px-4 py-3">@lang('Auto-Renew')</th>
                            <th class="px-4 py-3">@lang('Status')</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($domains as $domain)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5">
                                    <input type="checkbox" class="rounded bg-white border-slate-300 text-indigo-600 focus:ring-0">
                                </td>
                                <td class="px-4 py-3.5">
                                    <a href="{{ route('user.domain.details', $domain->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors text-sm">
                                        {{ $domain->domain }}
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-500">
                                    {{ showDateTime($domain->reg_date, 'd/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-700">
                                    {{ showDateTime($domain->next_due_date, 'd/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        <span>ON</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @php echo @$domain->showStatus; @endphp
                                </td>
                                <td class="px-4 py-3.5 text-right space-x-2">
                                    <a href="{{ route('user.domain.details', $domain->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white rounded-md text-xs font-semibold transition-all inline-flex items-center gap-1">
                                        <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                        <span>@lang('Manage')</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                    <i data-lucide="globe" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    <p>@lang('No domains registered yet.')</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($domains->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ paginateLinks($domains) }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
