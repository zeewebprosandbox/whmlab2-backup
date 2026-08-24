@extends($activeTemplate.'layouts.master')

@section('content')
<div class="py-8 bg-[#0A0A0B] text-[#F5F5F7] min-h-screen font-sans space-y-6 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">@lang('Support Center')</h1>
                <p class="text-xs text-neutral-400">@lang('Get help from server engineers 24/7 or read technical guides.')</p>
            </div>
            
            <a href="{{ route('ticket.open') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-glow-accent transition-all flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>@lang('Create Ticket')</span>
            </a>
        </div>

        <!-- Ticket Status Filter Tabs -->
        <div class="flex items-center gap-2 border-b border-white/5 pb-2">
            <button class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg">@lang('All Tickets')</button>
            <button class="px-4 py-2 bg-[#1C1C1F] text-neutral-400 hover:text-white text-xs font-semibold rounded-lg">@lang('Open')</button>
            <button class="px-4 py-2 bg-[#1C1C1F] text-neutral-400 hover:text-white text-xs font-semibold rounded-lg">@lang('Answered')</button>
            <button class="px-4 py-2 bg-[#1C1C1F] text-neutral-400 hover:text-white text-xs font-semibold rounded-lg">@lang('Closed')</button>
        </div>

        <!-- Ticket Table -->
        <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-[#1C1C1F] text-neutral-400 uppercase font-semibold text-[11px]">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">@lang('Ticket ID & Subject')</th>
                            <th class="px-4 py-3">@lang('Priority')</th>
                            <th class="px-4 py-3">@lang('Status')</th>
                            <th class="px-4 py-3">@lang('Last Reply')</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-sans">
                        @forelse($supports as $support)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-4">
                                    <div class="space-y-0.5">
                                        <a href="{{ route('ticket.view', $support->ticket) }}" class="font-mono text-xs font-bold text-indigo-400 hover:underline">
                                            #{{ $support->ticket }}
                                        </a>
                                        <h4 class="text-sm font-semibold text-white truncate max-w-md">{{ __($support->subject) }}</h4>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($support->priority == 1)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/5 text-neutral-400 text-[11px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span> Low
                                        </span>
                                    @elseif($support->priority == 2)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[11px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Medium
                                        </span>
                                    @elseif($support->priority == 3)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[11px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> High
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php echo $support->statusBadge; @endphp
                                </td>
                                <td class="px-4 py-4 font-mono text-neutral-400">
                                    {{ diffForHumans($support->last_reply) }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('ticket.view', $support->ticket) }}" class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-md text-xs font-semibold transition-all">
                                        @lang('View Thread')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-neutral-500">
                                    <i data-lucide="message-square" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    <p>@lang('No support tickets found.')</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($supports->hasPages())
                <div class="pt-4 border-t border-white/5">
                    {{ paginateLinks($supports) }}
                </div>
            @endif
        </div>

    </div>

    <!-- Floating Action Button (FAB) -->
    <a href="{{ route('ticket.open') }}" class="fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full shadow-glow-accent flex items-center justify-center transition-all hover:scale-110 z-40" title="New Support Ticket">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </a>
</div>
@endsection
