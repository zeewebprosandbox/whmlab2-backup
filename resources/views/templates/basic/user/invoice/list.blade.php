@extends($activeTemplate.'layouts.master')

@section('content')
<div class="py-8 bg-[#0A0A0B] text-[#F5F5F7] min-h-screen font-sans space-y-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header & Balance Banner -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-2">
                <h1 class="text-2xl font-extrabold text-white tracking-tight">@lang('Billing & Invoices')</h1>
                <p class="text-xs text-neutral-400">@lang('View active subscriptions, itemized billing history, and pay outstanding invoices.')</p>
            </div>

            <!-- Credit Balance Card -->
            <div class="p-5 bg-[#141416] border border-white/10 rounded-2xl flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-neutral-400">@lang('Credit Balance')</div>
                    <div class="text-2xl font-bold font-mono text-white mt-1">{{ showAmount(auth()->user()->balance) }}</div>
                </div>
                <a href="{{ route('user.deposit.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-glow-accent transition-all flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    <span>@lang('Add Funds')</span>
                </a>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-white/5">
            <button class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg">@lang('All Invoices')</button>
            <button class="px-4 py-2 bg-[#1C1C1F] text-neutral-400 hover:text-white text-xs font-semibold rounded-lg">@lang('Unpaid')</button>
            <button class="px-4 py-2 bg-[#1C1C1F] text-neutral-400 hover:text-white text-xs font-semibold rounded-lg">@lang('Paid')</button>
            <button class="px-4 py-2 bg-[#1C1C1F] text-neutral-400 hover:text-white text-xs font-semibold rounded-lg">@lang('Overdue')</button>
        </div>

        <!-- Invoice Table -->
        <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-[#1C1C1F] text-neutral-400 uppercase font-semibold text-[11px]">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">@lang('Invoice Number')</th>
                            <th class="px-4 py-3">@lang('Invoice Date')</th>
                            <th class="px-4 py-3">@lang('Amount')</th>
                            <th class="px-4 py-3">@lang('Status')</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-sans">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-white/[0.02] transition-colors @if($invoice->status == 0) border-l-4 border-l-amber-500 @elseif($invoice->status == 2) border-l-4 border-l-rose-500 @endif">
                                <td class="px-4 py-4 font-mono font-bold text-white">
                                    <a href="{{ route('user.invoice.view', $invoice->id) }}" class="hover:text-indigo-400">
                                        #{{ $invoice->getInvoiceNumber }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 font-mono text-neutral-400">
                                    {{ showDateTime($invoice->created, 'd/m/Y') }}
                                </td>
                                <td class="px-4 py-4 font-mono text-white font-bold text-sm">
                                    {{ showAmount($invoice->amount) }}
                                </td>
                                <td class="px-4 py-4">
                                    @php echo $invoice->showStatus; @endphp
                                </td>
                                <td class="px-4 py-4 text-right space-x-2">
                                    @if($invoice->status == 0)
                                        <a href="{{ route('user.invoice.view', $invoice->id) }}" class="px-3 py-1.5 bg-amber-500 text-black font-semibold text-xs rounded-md shadow-glow-amber">
                                            @lang('Pay Now')
                                        </a>
                                    @else
                                        <a href="{{ route('user.invoice.view', $invoice->id) }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-neutral-300 font-semibold text-xs rounded-md">
                                            @lang('View Receipt')
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-neutral-500">
                                    <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    <p>@lang('No invoices found.')</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="pt-4 border-t border-white/5">
                    {{ paginateLinks($invoices) }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
