@extends($activeTemplate.'layouts.master')

@section('content')
<div class="py-8 bg-[#0A0A0B] text-[#F5F5F7] min-h-screen font-sans space-y-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Page Title & Toolbar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">@lang('Domain Portfolio')</h1>
                <p class="text-xs text-neutral-400">@lang('Manage your registered domains, DNS records, and WHOIS privacy.')</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('register.domain') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-glow-accent transition-all flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>@lang('Register New Domain')</span>
                </a>
            </div>
        </div>

        <!-- Bulk Actions Toolbar & Filter Strip -->
        <div class="p-4 bg-[#141416] border border-white/10 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <button onclick="alert('Bulk renew selected domains')" class="px-3 py-1.5 bg-[#1C1C1F] hover:bg-[#242429] border border-white/10 text-xs font-semibold text-neutral-300 rounded-lg flex items-center gap-1.5">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>@lang('Bulk Renew')</span>
                </button>
                <button onclick="alert('Updating DNS for selected domains')" class="px-3 py-1.5 bg-[#1C1C1F] hover:bg-[#242429] border border-white/10 text-xs font-semibold text-neutral-300 rounded-lg flex items-center gap-1.5">
                    <i data-lucide="network" class="w-3.5 h-3.5 text-indigo-400"></i>
                    <span>@lang('Update Nameservers')</span>
                </button>
            </div>

            <div class="text-xs text-neutral-400">
                @lang('Showing') <strong class="text-white font-mono">{{ $domains->count() }}</strong> @lang('domains')
            </div>
        </div>

        <!-- Domains Table -->
        <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl overflow-hidden space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-[#1C1C1F] text-neutral-400 uppercase font-semibold text-[11px]">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg w-10">
                                <input type="checkbox" class="rounded bg-[#0A0A0B] border-white/20 text-indigo-600 focus:ring-0">
                            </th>
                            <th class="px-4 py-3">@lang('Domain Name')</th>
                            <th class="px-4 py-3">@lang('Registration Date')</th>
                            <th class="px-4 py-3">@lang('Expiry / Next Due')</th>
                            <th class="px-4 py-3">@lang('Auto-Renew')</th>
                            <th class="px-4 py-3">@lang('Status')</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-sans">
                        @forelse($domains as $domain)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-3.5">
                                    <input type="checkbox" class="rounded bg-[#0A0A0B] border-white/20 text-indigo-600 focus:ring-0">
                                </td>
                                <td class="px-4 py-3.5">
                                    <a href="{{ route('user.domain.details', $domain->id) }}" class="font-bold text-white hover:text-indigo-400 transition-colors text-sm">
                                        {{ $domain->domain }}
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-neutral-400">
                                    {{ showDateTime($domain->reg_date, 'd/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5 font-mono text-neutral-300">
                                    {{ showDateTime($domain->next_due_date, 'd/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1 text-emerald-400 font-semibold">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        <span>ON</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @php echo @$domain->showStatus; @endphp
                                </td>
                                <td class="px-4 py-3.5 text-right space-x-2">
                                    <a href="{{ route('user.domain.details', $domain->id) }}" class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-md text-xs font-semibold transition-all inline-flex items-center gap-1">
                                        <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                        <span>@lang('Manage')</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-neutral-500">
                                    <i data-lucide="globe" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    <p>@lang('No domains registered yet.')</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($domains->hasPages())
                <div class="pt-4 border-t border-white/5">
                    {{ paginateLinks($domains) }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
