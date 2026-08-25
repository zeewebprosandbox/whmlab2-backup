@extends($activeTemplate.'layouts.master')

@section('content')
<div class="col-12 space-y-5">
    <!-- Header -->
    <div class="p-5 sm:p-6 bg-white border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">@lang('Helpdesk & Tickets')</span>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 font-display">@lang('Support Center')</h1>
            <p class="text-xs text-slate-500 max-w-xl">@lang('Get help from server engineers 24/7 or open a new support ticket.')</p>
        </div>
        
        <a href="{{ route('ticket.open') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5 flex-shrink-0">
            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
            <span>@lang('Create Ticket')</span>
        </a>
    </div>

    <!-- Ticket Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3.5">@lang('Ticket ID & Subject')</th>
                        <th class="px-4 py-3.5">@lang('Priority')</th>
                        <th class="px-4 py-3.5">@lang('Status')</th>
                        <th class="px-4 py-3.5">@lang('Last Reply')</th>
                        <th class="px-4 py-3.5 text-right">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($supports as $support)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5">
                                    <a href="{{ route('ticket.view', $support->ticket) }}" class="font-mono text-xs font-bold text-indigo-600 hover:underline">
                                        #{{ $support->ticket }}
                                    </a>
                                    <h4 class="text-xs sm:text-sm font-semibold text-slate-900 truncate max-w-md">{{ __($support->subject) }}</h4>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($support->priority == 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Low
                                    </span>
                                @elseif($support->priority == 2)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200/60 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Medium
                                    </span>
                                @elseif($support->priority == 3)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200/60 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> High
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @php echo $support->statusBadge; @endphp
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-500">
                                {{ diffForHumans($support->last_reply) }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('ticket.view', $support->ticket) }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-700 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1 shadow-2xs">
                                    <span>@lang('View Thread')</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                <i data-lucide="message-square" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-xs text-slate-500">@lang('No support tickets found.')</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($supports->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ paginateLinks($supports) }}
            </div>
        @endif
    </div>
</div>
@endsection
