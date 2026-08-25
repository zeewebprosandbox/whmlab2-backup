@extends($activeTemplate.'layouts.master')

@section('content')
<div class="col-12 space-y-5">
    <!-- Header Card -->
    <div class="p-5 sm:p-6 bg-white border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">@lang('DNS & Domains')</span>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 font-display">@lang('Domain Portfolio')</h1>
            <p class="text-xs text-slate-500 max-w-xl">@lang('Manage your registered domains, DNS records, and WHOIS privacy.')</p>
        </div>
        <a href="{{ route('register.domain') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5 flex-shrink-0">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>@lang('Register New Domain')</span>
        </a>
    </div>

    <!-- Domains Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3.5">@lang('Domain Name')</th>
                        <th class="px-4 py-3.5">@lang('Registration Date')</th>
                        <th class="px-4 py-3.5">@lang('Expiry / Next Due')</th>
                        <th class="px-4 py-3.5">@lang('Auto-Renew')</th>
                        <th class="px-4 py-3.5">@lang('Status')</th>
                        <th class="px-4 py-3.5 text-right">@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($domains as $domain)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3.5">
                                <a href="{{ route('user.domain.details', $domain->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors text-xs sm:text-sm">
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
                                <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-[11px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>ON</span>
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                @php echo @$domain->showStatus; @endphp
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('user.domain.details', $domain->id) }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-700 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1 shadow-2xs">
                                    <i data-lucide="settings" class="w-3.5 h-3.5 text-slate-500"></i>
                                    <span>@lang('Manage')</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <i data-lucide="globe" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-xs text-slate-500">@lang('No domains registered yet.')</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($domains->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ paginateLinks($domains) }}
            </div>
        @endif
    </div>
</div>
@endsection
